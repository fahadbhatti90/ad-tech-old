

var t;
var visibleColumnIndex;
var columnCount;
var columnName;
var alaisLimit, addAliasContainer;
var tempAlias;
var addAliasObject = {
  fkId: null,
  overrideLabel: null,
  type: null,
  _token:null,
};
var labelOverridePreloader = '<div class="align-items-center labelOverridePreloader h-100 justify-content-center position-absolute text-center w-100">Please Wait<div class="dot-typing position-relative"></div></div>';
$(function () {
  
  alaisLimit = $(".counterLimit").length > 0 ? $(".counterLimit").text() : 100;
  addAliasContainer = $(".card-body").children(".addAliasContainer");
  columnCount = visibleColumnIndex = "all";

  initDataTable(); //end datatable

  //event handling for closing add Alias Popup
  $(".addAliasContainer").on("click", ".aliasDetails > span", function () {
      hideAliasBox(addAliasContainer);
  });

  //event handling for Opening and initializing add Alias Popup
  $("#dataTable").on("click","tbody tr td", function () {
    if ($(this).find(".attributeData").length <= 0)
      return;
    
    $("td.selected").removeClass("selected");
    $(this).addClass("selected");
    
    addAliasObject.fkId = $(this).children(".attributeData").attr("override-id");
    tempAlias = $(this).children(".attributeData").attr("alias");
    orignalAttribute = $(this).children(".attributeData").attr("orignalattribute");
    $(addAliasContainer).find(".alias").text(tempAlias);
    if (tempAlias == "Not Available") {
      $(".aliasDetails .labelOverride-title").addClass("primaryLabel").removeClass("secondaryLabel");
      $(".aliasDetails .labelOverride-alias").addClass("secondaryLabel").removeClass("primaryLabel");
      $(".addAliasContainer .addAliasBox").val("");
      $(".dynamicCounter").text("0")
      addAliasObject.overrideLabel = null;
      $(".information").hide();
    } else {
      $(".aliasDetails .labelOverride-title").addClass("secondaryLabel").removeClass("primaryLabel");
      $(".aliasDetails .labelOverride-alias").addClass("primaryLabel").removeClass("secondaryLabel");
      $(".addAliasContainer .addAliasBox").val(tempAlias);
      $(".dynamicCounter").text(tempAlias.length)
      addAliasObject.overrideLabel = tempAlias;
      $(".information").show();
    }
    if (!$(addAliasContainer).hasClass("shown")) {
      showAliasBox(addAliasContainer);
    }
    $(".aliasDetails .labelOverride-title .orignal").text(orignalAttribute);
    $(".aliasDetails .labelOverride-title .orignal").parent().children("b").text("Original "+getColumnName($(this).children(".attributeData").attr("attribute"))+" :");
    $(".addAliasContainer .addAliasBox").attr("placeholder","Please Write "+getColumnName($(this).children(".attributeData").attr("attribute"))+" Alias And Press Enter");
  });//end click event
 
  //event handling for Filtering Columns in Database
  $('.filter').on('click', function (e) {
    visibleColumnIndex = $(this).attr("data-column");
    columnName = $(this).attr("data-name");
    columnCount = visibleColumnIndex != "all" ? "one": visibleColumnIndex;
    t.clearPipeline().draw();
  });//end click funtion
});//end documtnet ready funciton
function initDataTable() {
  $.fn.dataTable.ext.errMode = "none";

  $.fn.dataTable.pipeline = function ( opts ) {
      // Configuration options
      var conf = $.extend( {
          pages: 5,     // number of pages to cache
          url: '',      // script url
          data: null,   // function or object with parameters to send to the server
                        // matching how `ajax.data` works in DataTables
          method: 'GET' // Ajax HTTP method
      }, opts );
  
      // Private variables for storing the cache
      var cacheLower = -1;
      var cacheUpper = null;
      var cacheLastRequest = null;
      var cacheLastJson = null;
  
      return function ( request, drawCallback, settings ) {
          var ajax          = false;
          var requestStart  = request.start;
          var drawStart     = request.start;
          var requestLength = request.length;
          var requestEnd    = requestStart + requestLength;
          
          if ( settings.clearCache ) {
              // API requested that the cache be cleared
              ajax = true;
              settings.clearCache = false;
          }
          else if ( cacheLower < 0 || requestStart < cacheLower || requestEnd > cacheUpper ) {
              // outside cached data - need to make a request
              ajax = true;
          }
          else if ( JSON.stringify( request.order )   !== JSON.stringify( cacheLastRequest.order ) ||
                    JSON.stringify( request.columns ) !== JSON.stringify( cacheLastRequest.columns ) ||
                    JSON.stringify( request.search )  !== JSON.stringify( cacheLastRequest.search )
          ) {
              // properties changed (ordering, columns, searching)
              ajax = true;
          }
          
          // Store the request for checking next time around
          cacheLastRequest = $.extend( true, {}, request );
  
          if ( ajax ) {
              // Need data from the server
              if ( requestStart < cacheLower ) {
                  requestStart = requestStart - (requestLength*(conf.pages-1));
  
                  if ( requestStart < 0 ) {
                      requestStart = 0;
                  }
              }
              
              cacheLower = requestStart;
              cacheUpper = requestStart + (requestLength * conf.pages);
  
              request.start = requestStart;
              request.length = requestLength*conf.pages;
  
              // Provide the same `data` options as DataTables.
              if ( typeof conf.data === 'function' ) {
                  // As a function it is executed with the data object as an arg
                  // for manipulation. If an object is returned, it is used as the
                  // data object to submit
                  var d = conf.data( request );
                  if ( d ) {
                      $.extend( request, d );
                  }
              }
              else if ( $.isPlainObject( conf.data ) ) {
                  // As an object, the data given extends the default
                  $.extend( request, conf.data );
              }
  
              settings.jqXHR = $.ajax( {
                  "type":     conf.method,
                  "url":      conf.url,
                  "data":     request,
                  "dataType": "json",
                  "cache":    false,
                  "success":  function ( json ) {
                      cacheLastJson = $.extend(true, {}, json);
  
                      if ( cacheLower != drawStart ) {
                          json.data.splice( 0, drawStart-cacheLower );
                      }
                      if ( requestLength >= -1 ) {
                          json.data.splice( requestLength, json.data.length );
                      }
                      
                      drawCallback( json );
                  },
                  error: function (xhr, error, thrown) {
                    if (xhr.responseText.toLowerCase().includes("server error")) {
                      // console.log("Internal Server Error refresh")
                    }
                  }
              } );
          }
          else {
              json = $.extend( true, {}, cacheLastJson );
              json.draw = request.draw; // Update the echo for each response
              json.data.splice( 0, requestStart-cacheLower );
              json.data.splice( requestLength, json.data.length );
  
              drawCallback(json);
          }
      }
  };
  
  // Register an API method that will empty the pipelined data, forcing an Ajax
  // fetch on the next draw (i.e. `table.clearPipeline().draw()`)
  $.fn.dataTable.Api.register( 'clearPipeline()', function () {
      return this.iterator( 'table', function ( settings ) {
          settings.clearCache = true;
      } );
  } );
 
  t = $("#dataTable").DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": false,
        "scrollX": true,
        "deferRender": true,
        "ajax": $.fn.dataTable.pipeline( {
            url: $("#dataTable").attr("datatable-url"),
            pages: 5, // number of pages to cache
            "data": function ( d ) {
              d.columsCustom = columnCount;
              d.columnName = columnName;
          }
        }),
        "columns":[
          {
            "data": "ASIN",
            'title' : 'ASIN',
          },
          {
              "data": "product_title",
              'title' : 'Product Title',
          },
          {
            "data": "subcategory_name",
            'title' : 'Subcategory Name',
          },  
          {
            "data": "category_name",
            'title' : 'Category Name',
          },
          {
            "data": "accountName",
            'title' : 'Brand Name',
          }
        ],
      "drawCallback": function( settings ) {
          // Tool Tip Tipster
        $("#dataTable tbody td").removeAttr("tabindex")
        for (let index = 0; index < 5; index++) {
          if (index == visibleColumnIndex) {
            t.column( index ).visible(true);
            $(".aliasOrignal").addClass("adjustSingle");
            continue;
          }//end if
          if (visibleColumnIndex != "all") {
            t.column(index).visible(false);
          }//end if
          else {
            t.column( index ).visible(true);
          }//end else
          if (visibleColumnIndex == 1) {
            t.column( 0 ).visible(true);  
          }
        }//end for
      }//end function
  });//end datatable init.
}

function showAliasBox(addAliasContainer) {
  $(addAliasContainer).css({"transform": "translateY(0)"}).addClass("shown");
  $(addAliasContainer).clearQueue().show(100);
  $(".characterCounter").removeClass("d-none");
  $(addAliasContainer).find(".addAliasBox").focus();
  $(addAliasContainer).addClass("shadow");
}//function ends


function hideAliasBox(addAliasContainer) {
  $(addAliasContainer).find(".addAliasBox").val("");
  $(addAliasContainer).find(".alias").text("Not Available");
  $(addAliasContainer).removeClass("shown").removeAttr("style");
  $(".characterCounter").addClass("d-none");
  $(".LabelOverrdieCardBody").removeAttr("style")
  $("td.selected").removeClass("selected");
  $(addAliasContainer).removeClass("shadow");
  addAliasObject = {
    fkId: null,
    overrideLabel: null,
    type: null,
    _token:null,
  };
}
function getColumnName(columnIndex){
  switch (columnIndex) {
    case "1":
      addAliasObject.type = 1;
      return "Brand";
      break;
    case "2":
      addAliasObject.type = 2;
      return "Product Title";
      break;
    case "3":
      addAliasObject.type = 3;
      return "Category";
      break;
    case "4":
      addAliasObject.type = 4;
      return "Sub Category";
      break;
    default:
      addAliasObject.type = 1;
      return "Brand";
      break;
  }
}