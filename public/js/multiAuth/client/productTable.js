var t = null;
$(document).ready(function() {
  var selectedAsin = [];
  var selectedObject = {};
  var dotsContainerWidth = $(".itemCounts").width();
  jQuery.fn.visible = function() {
      return this.css('visibility', 'visible');
  };

  jQuery.fn.invisible = function() {
      return this.css('visibility', 'hidden');
  };

  jQuery.fn.visibilityToggle = function() {
      return this.css('visibility', function(i, visibility) {
          return (visibility == 'visible') ? 'hidden' : 'visible';
      });
  };
  var dotsLimit = Math.floor(dotsContainerWidth / 12);
  function setTagPopupHeight() {
    $(".tags-container").css(
      "top",
      "-" +
        ($(".tags-container").height() > 3
          ? $(".tags-container").height() + 3
          : $(".tags-container").height() + 1) +
        "px"
    );
  }//end function
  setTagPopupHeight()
  //for disabling userselect on controls
  $(".tagGroupManager .row .section3 .control")
    .attr("unselectable", "on")
    .css({
      "user-select": "none",
      MozUserSelect: "none"
    })
    .on("selectstart", false)
    .on("mousedown", false);

  $(".tooltip").tooltipster({
    interactive: true,
    maxWidth: 300
  }); //end tooltipster

  $.fn.dataTable.ext.errMode = "none";
  t = $("#dataTable").DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "ajax": $("body").attr("base_url") + "/client/productList",
        "columns": [
          {
            "data": "id",
          },
          {
            "data": "ASIN",
            'title' : 'ASIN',
          },
          {
            "data": "product_title",
            'title' : 'Product Title',
          },
          {
            "data": "fullfillment_channel",
            'title' : 'Fullfillment channel',
          },
          {
            "data": "shipped_units",
            'title' : 'Shipped Units',
          },
          {
            "data": "tag",
            'title' : 'Tags',
            'name':"tag.tag"
          },
      ],
      'columnDefs': [ {
        'targets': [0], // column index (start from 0)
        'orderable': false, // set orderable false for selected columns
      }],
      drawCallback: function(settings) {
        // productTableTitle
        $("#dataTable thead tr th:nth-child(1)").removeAttr("class").addClass("sorting_disabled");
        $(".productTableTitle").tooltipster({
          interactive: true,
          maxWidth: 300
        }); //end tooltipster
        
        $(".tagTootltip").tooltipster({
          interactive: true,
          contentAsHTML: true,
          maxWidth: 300,
          // trigger:"click",
          functionReady: function(instance, helper){
            if ($(".tooltipster-arrow").position().top == 0) {
              $(".progress").removeAttr("style");
              $(".progress").css({
                "bottom" : "0",
                "top" : "unset",
              });
            }
            else {
              $(".progress").removeAttr("style");
              $(".progress").css({
                "top": "0",
                "bottom" : "unset",
              });
            }
          }
        }); //end tooltipster
  
        var tds = $("tbody tr td:nth-child(2)");
        $.each(tds, function(indexInArray, valueOfElement) {
          if (selectedAsin.includes($(valueOfElement).text())) {
            $(valueOfElement)
              .parent()
              .addClass("activeTr");
          }
        });
        if ($("tbody tr.activeTr").length >= $("tbody tr").length) {
          $("th .selectContainer").addClass("active");
        } else {
          $("th .selectContainer").removeClass("active");
        }
      }
  });
  Array.prototype.remove = function(v) {
    this.splice(this.indexOf(v) == -1 ? this.length : this.indexOf(v), 1);
  };
  Array.prototype.removeOnKey = function(v) {
    this.splice(this.indexOf(v), 1);
  };
  $("#dataTable th .selectContainer .checkboxMiniContainer span").click(
    function(e) {
      if ( $(this).parents(".selectContainer").hasClass("active") ) {
        $(this).parents(".selectContainer").removeClass("active");
        $("#dataTable tbody tr").click();
      } else {
        $(this).parents(".selectContainer").addClass("active");
        $("#dataTable tbody tr").not(".activeTr").click();
      }
    }
  ); //end function
  $("tbody").on("click", "tr", function(e) {
    e.preventDefault();

    if ($(this).find(".dataTables_empty").length > 0) return;
    $(".tagGroupManager .control.active").click();

    var dot = '<span class="itemAdded"></span>';
    var extradotCounter = '<span class="extraDotCounter"></span>';
    $(this).toggleClass("activeTr");
    if ( $(this).parent().find(".activeTr").length >= $(this).parent().find("tr").length ) {
      $("th .selectContainer").addClass("active");
    } else {
      $("th .selectContainer").removeClass("active");
    }
    //if their is no row selected
    if (!$(this).hasClass("activeTr")) {
      selectedAsin.remove(
        $(this)
          .find("td:nth-child(2)")
          .text()
      );
      //for removing unselected asin
      if (
        selectedObject.hasOwnProperty(
          $(this)
            .find("td:nth-child(2)")
            .text()
            .trim()
        )
      )
        delete selectedObject[
          $(this)
            .find("td:nth-child(2)")
            .text()
            .trim()
        ];
    }
    if (!$("tbody tr").hasClass("activeTr") && selectedAsin.length <= 0) {
      $(".tagGroupManager").removeClass("active");
    } else {
      //if row or rows selected
      totalSelected = $("tbody tr.activeTr").length;
      selectedTr = $("tbody tr.activeTr td:nth-child(2)");

      for (let index = 0; index < totalSelected; index++) {
        if (!selectedAsin.includes($(selectedTr[index]).text())) {
          asin = $(selectedTr[index]).text();
          accountId = $(selectedTr[index])
            .find("span")
            .attr("accountId");
          fullFilmentMethod = $(selectedTr[index])
            .parent()
            .find("td:nth-child(4)")
            .text()
            .trim();

          selectedObject[asin] = {
            ffm: fullFilmentMethod,
            accountId: accountId
          };
          selectedAsin.push(asin);
        }
      }
      $(".tagGroupManager .counter").text(selectedAsin.length);
      if (selectedAsin.length < dotsLimit) {
        $(".itemCounts .itemAdded").remove();
        $(".itemCounts .extraDotCounter").remove();

        for (let index = 0; index < selectedAsin.length; index++) {
          $(".itemCounts").append(dot);
        }
      } else {
        if ($(".extraDotCounter").length <= 0) {
          $(".itemCounts").append(extradotCounter);
        }
        $(".extraDotCounter").text(
          "+ " + (selectedAsin.length - (dotsLimit - 1))
        );
      }
      if(selectedAsin.length > 1){
        $(".itemLabel").text("Items")
      }else{
        $(".itemLabel").text("Item")
      }
      $(".tagGroupManager").addClass("active");
    }
  }); //end click funciton
  $(".closeButton").click(function(e) {
    e.preventDefault();
    selectedAsin = [];
    selectedObject = {};
    $(".tagGroupManager").removeClass("active");
    $("tbody tr.activeTr").click();
    $(".control.active").click();
  });

  $(".tagGroupManager .row .section3 .control::before").click(function(e) {
    e.stopPropagation();
    e.preventDefault();
  });

  /******************************************Tag Assignment Manager ********************************/
  //assigns Tag
  $(".control.tagControl").click(function(e) {
    e.preventDefault();
    if ($(".tagGroupManager .control").hasClass("stopWorking")) return;
    thisObj = $(this);
    if (
      $(".tagGroupManager .tags-container").is(":visible")
    ) {
      $(".tagGroupManager > .row .section3 .tags-container").toggle();
      $(".tagGroupManager .control").removeClass("active");
      return;
    }
    if ($(".tagGroupManager > .row > .progress").is(":visible")) return;

    $(".statsSection, .inputSection").removeClass("move");
    $(
      ".tagGroupManager > .row > .progress, .tagGroupManager .tags-container .coverContainer"
    ).show();
    $(".tagGroupManager .control").addClass("stopWorking");
    $(".assignTagButton").invisible();
    $.ajax({
      type: "GET",
      url: $("body").attr("base_url") + "/client/tags",
      success: function(response) {
        $(".tagGroupManager .control").removeClass("stopWorking");

        if (response.status) {
          if (
            !$(".tagGroupManager > .row .section3 .tags-container").is(
              ":visible"
            )
          )
            $(".tagGroupManager > .row .section3 .tags-container").toggle();
          $(".tags-container > .tags").html(response.data);
          $(".tags-container > .tags").append(
            '<div class="coverContainer"></div>'
          );
          $(".tagGroupManager .control.active").removeClass("active");
          $(thisObj).toggleClass("active");
        } else {
          Swal.fire({
            title: "<strong>Error</strong>",
            type: "error",
            text: "Error loading tags refresh and try again "
          });
          $(thisObj).removeClass("active");
        }
        $(
          ".tagGroupManager > .row > .progress, .tagGroupManager .tags-container .coverContainer"
        ).hide();
      }, //end success
      error: function(error) {
        error.responseText;
        Swal.fire({
          title: "<strong>Error</strong>",
          type: "error",
          text: "Sorry! fail to add tag refresh and try again "
        });
        $(".tagGroupManager .control").removeClass("stopWorking");
        $(thisObj).removeClass("active");
        $(
          ".tagGroupManager > .row > .progress, .tagGroupManager .tags-container .coverContainer"
        ).hide();
      }
    });
  });
  $(".tags-container").on("click", ".tags .tag .tag-left span", function() {
    $(this)
      .parents(".editBox")
      .toggleClass("active");
      if ($(".tags-container .editBox.active").length > 0) {
        $(".assignTagButton").visible();
      }
      else {
        $(".assignTagButton").invisible();
      }
  }); //end function
  //Assign Tag Button click
  $(".tags-container .assignTagButton span").on("click", function() {
    tags = $(this)
      .parents(".tags-container")
      .find(".editBox.active");
    if (selectedAsin.length <= 0 || tags.length <= 0) return;
    tagsObj = {};
    $.each(tags, function(indexInArray, valueOfElement) {
      tagsObj[$(valueOfElement).attr("id")] = $(valueOfElement)
        .find(".tag .tag-left span")
        .text()
        .trim();
    });
    preloader =
      ".tagGroupManager > .row .tags-container > .progress, .tagGroupManager > .row .tags-container > .tags > .coverContainer";
    ajaxData = {
      asins: selectedObject,
      tagsObj: tagsObj,
      _token: $("body").attr("csrf")
    };
    ajaxForAsigningTag(ajaxData, preloader);
  }); //Assign Tag Button click
  /******************************************Tag Assignment Manager ********************************/

  $(".control.groupControl").click(function(e) {
    e.preventDefault();
    return;
    if ($(".tagGroupManager .control").hasClass("stopWorking")) return;
    if (selectedAsin.length <= 0) return;
    preloader = ".tagGroupManager > .row > .progress";
    ajaxData = {
      asins: selectedObject,
      tagId: 1,
      tagName: "#unassigned",
      _token: $("body").attr("csrf")
    };
    ajaxForAsigningTag(ajaxData, preloader);
  }); //assigns Tag

  /******************************************Tag un-assignment Manager ********************************/
  $(".control.deleteControl").on("click", function(e) {
    e.preventDefault();
    if ($(".tagGroupManager .control").hasClass("stopWorking")) return;
    thisObj = $(this);
    if ($(thisObj).hasClass("active")) {
      $(".control").removeClass("active");
      return;
    }

    $(".tagGroupManager .control.active").click();
    $(".tagGroupManager > .row > .progress").show();
    $(".tagGroupManager .control").addClass("stopWorking");
    $.ajax({
      type: "GET",
      url: $("body").attr("base_url") + "/client/tags/getAllTagsToDelete",
      data: {
        asins: selectedObject
      },
      success: function(response) {
        if (response.status) {
          $activeTrs = $("tbody tr.activeTr td:last-child");
          $.each($activeTrs, function(indexInArray, valueOfElement) {
            $(valueOfElement).html("<span>None</span>");
          });
          $(".control").removeClass("active");
          $("tbody tr.activeTr").click();
        } else {
          Swal.fire({
            title: "<strong>Error</strong>",
            type: "error",
            text: "Error loading tags refresh and try again "
          });
          $(thisObj).removeClass("active");
        }
        $(".tagGroupManager .control").removeClass("stopWorking");
        $(".tagGroupManager > .row > .progress").hide();
      }, //end success
      error: function(error) {
        Swal.fire({
          title: "<strong>Error</strong>",
          type: "error",
          text: "Sorry! fail to add tag refresh and try again "
        });
        $(".tagGroupManager .control").removeClass("stopWorking");
        $(thisObj).removeClass("active");
        $(".tagGroupManager > .row > .progress").hide();
      }
    });
  });
  /******************************************Tag un-assignment Manager ********************************/

  /******************************************Deleting Tag Manager ********************************/
  $(".tagGroupManager > .row .section3 .tags-container .tags").on(
    "click",
    ".editBox i.fa-trash-alt ",
    function(e) {
      e.preventDefault();
      if ($(this).hasClass("stopWorking")) return;
      thisObj = $(this);
      tagId = $(thisObj)
        .parent()
        .parent()
        .parent()
        .attr("id");

      if (typeof tagId == "undefined") return;
      preloader =
        ".tagGroupManager > .row .tags-container > .progress, .tagGroupManager > .row .tags-container > .tags > .coverContainer";
      $(preloader).show();
      $(thisObj).addClass("stopWorking");
      $.ajax({
        type: "GET",
        url: $("body").attr("base_url") + "/client/tags/" + tagId + "/delete",
        success: function(response) {
          if (response.status) {
            $(thisObj)
              .parent()
              .parent()
              .parent()
              .remove();
            t.ajax.reload();
          } else {
            Swal.fire({
              title: "<strong>Error</strong>",
              type: "error",
              text: "Error loading tags refresh and try again "
            });
          }
          $(thisObj).removeClass("stopWorking");
          $(preloader).hide();
        }, //end success
        error: function(error) {
          Swal.fire({
            title: "<strong>Error</strong>",
            type: "error",
            text: "Sorry! fail to add tag refresh and try again "
          });
          $(thisObj).removeClass("stopWorking");
          $(preloader).hide();
        }
      });
    }
  ); //delete tag area
  /******************************************Deleting Tag Manager ********************************/

  /******************************************On Mobile view Settings Button Manager********************************/
  //On Mobile view Settings Button Manager
  $(".control.settingControl").on("click", function() {
    var settingControlThisObj = $(this);
    $(".controlsContainer").toggle();
    $(settingControlThisObj).toggleClass("active");
  });
  //On Mobile view Settings Button Manager
  /******************************************On Mobile view Settings Button Manager********************************/

  /******************************************Add Tag Manager********************************/
  $(".control.addControl").click(function(e) {
    e.preventDefault();
    if ($(".tagGroupManager .control").hasClass("stopWorking")) return;
    var thisObj = $(this);
    $(".tagGroupManager .control.active").not(".addControl").click();
    $(".tagGroupManager .control").addClass("stopWorking");

    setTimeout(function() {
      if ($(".inputSection").hasClass("move")) {
        $("#tag").focus();
      } else {
        $("#tag").blur();
      }
      $(".tagGroupManager .control").removeClass("stopWorking");
      $(".statsSection, .inputSection").toggleClass("move");
      $(thisObj).toggleClass("active");
    }, 300);
    // $("tbody tr").removeClass("activeTr")
  });
  $("#tag").keypress(function(e) {
    var letters = /^[0-9a-zA-Z]/gi;
    if (!isAlphaNumaric(e.key) || $(this).val().length > 18) e.preventDefault();
  });
  $("#tag").bind("paste", function(e) {
    validatePastEvent(e, $(this));
  });
  $(".tags-container .tags").on("paste", ".editBox .tag-left input", function(
    e
  ) {
    validatePastEvent(e, $(this));
  });
  $("#tag").keyup(function(e) {
    if ($(".tagGroupManager > .row > .progress").is(":visible")) return;
    currentVal = $(this).val();
    if (!currentVal.includes("#") || currentVal[0] != "#") {
      currentVal = currentVal.replace("#", "");
      $(this).val("#" + currentVal);
    }

    if (e.keyCode == 13) {
      if ($(this).hasClass("invalid")) return;
      var thisobj = $(this);
      if ($(this).val().length <= 1) {
        showInvalid(thisobj);
        return;
      } //end if
      tag = $(this).val();
      $(".tagGroupManager > .row > .progress").show();
      $.ajax({
        type: "POST",
        url: $("body").attr("base_url") + "/client/tags/add",
        data: {
          tag: tag,
          _token: $("body").attr("csrf")
        },
        success: function(response) {
          if (response.status) {
            $(thisobj).val("#");
            $(".control.active")
              .not(".settingControl")
              .click();
          } else {
            Swal.fire({
              title: "<strong>Error</strong>",
              type: "error",
              text: "Sorry! fail to add tag refresh and try again "
            });

            showInvalid(thisobj);
          }
          $(".tagGroupManager > .row > .progress").hide();
        }, //end success
        error: function(error) {
          Swal.fire({
            title: "<strong>Error</strong>",
            type: "error",
            text: "Sorry! fail to add tag refresh and try again "
          });
          showInvalid(thisobj);
          $(".tagGroupManager > .row > .progress").hide();
        }
      });
    } //end if
  });
  /******************************************Add Tag Manager********************************/

  /******************************************Edit Tag Manager********************************/
  $(".tags-container .tags").on(
    "click",
    ".editBox .tag-right .editTagButton",
    function() {
      $(this)
        .parents(".editBox")
        .addClass("edit");
      $(this)
        .parent()
        .parent()
        .append('<input spellcheck="false">');
      $(this)
        .parent()
        .parent()
        .find("input")
        .focus();
      $(this)
        .parent()
        .parent()
        .find("input")
        .val(
          $(this)
            .parent()
            .parent()
            .find(".tag-left")
            .find("span")
            .text()
        );
    }
  );
  $(".tags-container .tags").on("blur", ".editBox input", function() {
    editTag(this);
  });
  $(".tags-container .tags").on("keypress", ".editBox input", function(e) {
    var letters = /^[0-9a-zA-Z]/gi;
    if (!isAlphaNumaric(e.key) || $(this).val().length > 18) e.preventDefault();
  });
  $(".tags-container .tags").on("keyup", ".editBox input", function(e) {
    if ($(".tagGroupManager > .row > .progress").is(":visible")) return;
    currentVal = $(this).val();
    if (!currentVal.includes("#") || currentVal[0] != "#") {
      currentVal = currentVal.replace("#", "");
      $(this).val("#" + currentVal);
    }

    if (e.keyCode == 13) {
      $(this).blur();
    } //end if
  });
  /******************************************Edit Tag Manager********************************/

  /***Custom Functions */
  function validatePastEvent(e, pastObj) {
    var reg = /[^a-zA-Z0-9]/g;
    // access the clipboard using the api
    var pastedData = e.originalEvent.clipboardData.getData("text");
    var result = pastedData.match(reg);
    if (result != null) e.preventDefault();
    setTimeout(function() {
      if ($(pastObj).val().length > 18) {
        var orignal = $(pastObj).val();
        if (!orignal.includes("#"))
          $(pastObj).val("#" + orignal.substring(0, 19));
        else $(pastObj).val(orignal.substring(0, 19));
      }
    }, 100);
  }
  function isAlphaNumaric(value) {
    var letters = /^[0-9a-zA-Z]/gi;
    if (value.match(letters) == null) return false;
    return true;
  }
  function showInvalid(thisobj) {
    $(thisobj).addClass("invalid");
    $(thisobj).attr("placeholder", "Please Enter Some Thing");
    setTimeout(function() {
      $(thisobj).removeClass("invalid");
      $(thisobj).attr("placeholder", "Write Tag Then Press Enter");
    }, 500);
  }
  function editTag(editTagObj) {
    newTagVal = $(editTagObj).val();
    oldTagVal = $(editTagObj)
      .parent()
      .find(".tag-left")
      .find("span")
      .text()
      .trim();
    if (newTagVal.length <= 1 || newTagVal == oldTagVal) {
      $(editTagObj)
        .parents(".editBox")
        .removeClass("edit");
      $(editTagObj).remove();
      return;
    } //end if
    preloader =
      ".tagGroupManager > .row .tags-container > .progress, .tagGroupManager > .row .tags-container > .tags > .coverContainer";
    tagId = $(editTagObj)
      .parents(".editBox")
      .attr("id");
    $(preloader).show();
    $.ajax({
      type: "GET",
      url: $("body").attr("base_url") + "/client/tags/" + tagId + "/edit",
      data: {
        tagId: tagId,
        tagName: newTagVal
      },
      success: function(response) {
        if (response.status) {
          $(editTagObj)
            .parent()
            .find(".tag-left")
            .find("span")
            .text(newTagVal);
          $(editTagObj)
            .parents(".editBox")
            .removeClass("edit");
          $(editTagObj).remove();
          t.ajax.reload();
        } else {
          Swal.fire({
            title: "<strong>Error</strong>",
            type: "error",
            text: response.message
          });
        }
        $(preloader).hide();
      }, //end success
      error: function(error) {
        error.responseText;
        Swal.fire({
          title: "<strong>Error</strong>",
          type: "error",
          text: "Sorry! fail to add tag refresh and try again "
        });
        $(preloader).hide();
      }
    });
  }
  function ajaxForAsigningTag(ajaxData, preloader) {
    $(preloader).show();
    $.ajax({
      type: "POST",
      url: $("body").attr("base_url") + "/client/tags/asign",
      data: ajaxData,
      success: function(response) {
        if (response.status) {
          tdsASINS = $("tbody tr.activeTr").removeClass("activeTr");
          t.ajax.reload();
          setTimeout(() => {
            $(".closeButton").click();
          }, 400);
          $(".control.active")
            .not(".settingControl")
            .click();
          selectedAsin = [];
          selectedObject = {};
        } //endif
        else {
          Swal.fire({
            title: "<strong>Error</strong>",
            type: "error",
            text: "Sorry! fail to add tag refresh and try again "
          });
        }
        $(preloader).hide();
      }, //end success
      error: function(error) {
        Swal.fire({
          title: "<strong>Error</strong>",
          type: "error",
          text: "Sorry! fail to add tag refresh and try again "
        });
        $(preloader).hide();
      }
    });
  }
  /***Custom Functions */
}); //end ready function
