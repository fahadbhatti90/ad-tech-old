/******************************************************Variables************************************************/
var w = 1020,
  h = 500;





  
var monthNames = [
  "January",
  "February",
  "March",
  "April",
  "May",
  "June",
  "July",
  "August",
  "September",
  "October",
  "November",
  "December"
];
var monthNamesShort = [
  "Jan",
  "Feb",
  "Mar",
  "Apr",
  "May",
  "June",
  "July",
  "Aug",
  "Sept",
  "Oct",
  "Nov",
  "Dec"
];
$sizeChangeAjaxData = {};
var instancee;
var maxDataPointsForDots = 1000,
  transitionDuration = 1000;

var svg = null,
  yAxisGroup = null,
  xAxisGroup = null,
  dataCirclesGroup = null,
  dataLinesGroup = null;

var CurrentData = null;
var currentFormatSelected = [1, 31];
var totalTickes = 31;
var lastTotalTickes = 31;
var tooltipInstance = null;
var activeFilter = null;
var attribute = null;
var attributeAlias = null;
var activeDateFormate = "day";
var previousDateFormate = "day";
var activeMonth = null;
var activeYear = null;
/******************************************************Variables************************************************/
String.prototype.capitalize = function() {
  return this.replace(/(?:^|\s)\S/g, function(a) {
    return a.toUpperCase();
  });
};
// JqShortReady functon customTheme defaultThem
$(function() {
  $(".changeGraph").dropdown({
    constrainWidth: false,
    container: ".datesFilter"
  });
  $("#dailyYearDropdown .availableYear").dropdown({
    constrainWidth: false,
    container: ".datesFilter",
    closeOnClick: false
  });

  /******************************************************Daily, Weekly and Monthly Buttons Dropdowns************************************************/

  $(".changeGraph").on("click", function() {}); //end changeGraph Button click event function

  $("#dailyYearDropdown .availableYear").on("click", function(e) {
    asin = $(".asin").val();
    if (asin == null) {
      Swal.fire({
        title: "Sorry, Please select category, subcategory and Asin",
        type: "info"
      });
      $(
        ".productViewTable .preloadOnDropdownChange, .options .right_card .preloadOnDropdownChange"
      ).fadeOut();
      $("#dailyMonthDropdown").removeAttr("style");
      return;
    }
    if (asin.length <= 0) {
      Swal.fire({
        title: "Sorry, Please select asin",
        type: "info"
      });
      $(
        ".productViewTable .preloadOnDropdownChange, .options .right_card .preloadOnDropdownChange"
      ).fadeOut();
      $("#dailyMonthDropdown").removeAttr("style");
      return;
    }
    activeYear = $(this).attr("year");
    $.ajax({
      type: "get",
      url: $("body").attr("base_url") + "/client/getAvailableMonths",
      data: {
        year: activeYear
      },
      success: function(response) {
        lis = "";
        $.each(response, function(indexInArray, valueOfElement) {
          lis +=
            '<li><a href="#!" class="availableMonth" month="' +
            valueOfElement.availableMonth +
            '">' +
            monthNames[valueOfElement.availableMonth - 1] +
            "</a></li>";
        });
        $("#dailyMonthDropdown").html(lis);
      }
    }); //end ajax function
  }); //end function click
  $("#dailyMonthDropdown").on("click", ".availableMonth", function() {
    previousDateFormate = $(".changeGraph:not(.waves-white)").attr(
      "data-dtype"
    );
    $("#dailyMonthDropdown").html(
      '<li><a href="#!" class="availableMonth" month="" style="text-decoration:none;cursor:not-allowed">Loading...</a></li>'
    );
    $(".datesFilter a.changeGraph").addClass("waves-white");
    $(".datesFilter a.dailyDropdownTrigger").removeClass("waves-white");

    activeMonth = $(this).attr("month");
    if (activeMonth.length <= 0) {
      return;
    }
    $("#dailyMonthDropdown").removeAttr("style");
    $(
      ".productViewTable .preloadOnDropdownChange, .options .right_card .preloadOnDropdownChange"
    ).fadeIn();
    asin = $(".selectedAsin")
      .text()
      .trim();
    if (asin == null) {
      Swal.fire({
        title: "Sorry, Please select category, subcategory and Asin",
        type: "info"
      });

      $(
        ".productViewTable .preloadOnDropdownChange, .options .right_card .preloadOnDropdownChange"
      ).fadeOut();
      return;
    }
    if (asin.length <= 0) {
      Swal.fire({
        title: "Sorry, No ASIN Selected",
        type: "info"
      });
      $(
        ".productViewTable .preloadOnDropdownChange, .options .right_card .preloadOnDropdownChange"
      ).fadeOut();
      return;
    }

    currentFormatSelected = [1, 31];
    lastTotalTickes = totalTickes;
    totalTickes = 31;
    activeDateFormate = "day";

    category_id = $(".selectedAsin").attr("c_id");
    sub_category_id = $(".selectedAsin").attr("sub_c_id");
    attribute = $(".selectedAsin").attr("attribute");
    activeMonth = activeMonth;
    ajaxData = {
      dateFormate: "day",
      filterType: activeFilter,
      filterValue: activeFilterValue,
      activeMonth: activeMonth,
      activeYear: activeYear,
      attribute: attribute,
      category_id: category_id,
      subcategory_id: sub_category_id,
      asin: asin
    };
    getDataByDateFormatedateData(ajaxData);
  });

  $("#monthlyYearDropdown .availableYear").on("click", function(e) {
    previousDateFormate = $(".changeGraph:not(.waves-white)").attr(
      "data-dtype"
    );
    $(
      ".productViewTable .preloadOnDropdownChange, .options .right_card .preloadOnDropdownChange"
    ).fadeIn();

    asin = $(".selectedAsin")
      .text()
      .trim();
    if (asin == null) {
      Swal.fire({
        title: "Sorry, Please select category, subcategory and Asin",
        type: "info"
      });
      $(
        ".productViewTable .preloadOnDropdownChange, .options .right_card .preloadOnDropdownChange"
      ).fadeOut();
      return;
    }
    if (asin.length <= 0) {
      Swal.fire({
        title: "Sorry, No ASIN Selected",
        type: "info"
      });
      $(
        ".productViewTable .preloadOnDropdownChange, .options .right_card .preloadOnDropdownChange"
      ).fadeOut();
      return;
    }
    activeYear = $(this).attr("year");

    category_id = $(".selectedAsin").attr("c_id");
    sub_category_id = $(".selectedAsin").attr("sub_c_id");
    attribute = $(".selectedAsin").attr("attribute");

    $(".datesFilter a.changeGraph").addClass("waves-white");
    $(".datesFilter a.monthlyDropdownTrigger").removeClass("waves-white");
    $(
      ".productViewTable .preloadOnDropdownChange, .options .right_card .preloadOnDropdownChange"
    ).fadeIn();
    activeDateFormate = "month";
    currentFormatSelected = [1, 12];
    lastTotalTickes = totalTickes;
    totalTickes = 12;
    ajaxData = {
      dateFormate: "month",
      filterType: activeFilter, //category,subcategory or asin default is asin
      filterValue: activeFilterValue, //category,subcategory or asin default is asin
      attribute: attribute,
      category_id: category_id,
      subcategory_id: sub_category_id,
      activeYear: activeYear,
      asin: asin
    };
    getDataByDateFormatedateData(ajaxData);
  }); //end function click
  $("#weeklyYearDropdown .availableYear").on("click", function(e) {
    previousDateFormate = $(".changeGraph:not(.waves-white)").attr(
      "data-dtype"
    );
    $(
      ".productViewTable .preloadOnDropdownChange, .options .right_card .preloadOnDropdownChange"
    ).fadeIn();
    asin = $(".selectedAsin").text();
    if (asin == null) {
      Swal.fire({
        title: "Sorry, Please select category, subcategory and Asin",
        type: "info"
      });
      $(
        ".productViewTable .preloadOnDropdownChange, .options .right_card .preloadOnDropdownChange"
      ).fadeOut();
      return;
    }

    if (asin.length <= 0) {
      Swal.fire({
        title: "Sorry, No ASIN Selected",
        type: "info"
      });
      $(
        ".productViewTable .preloadOnDropdownChange, .options .right_card .preloadOnDropdownChange"
      ).fadeOut();
      return;
    }
    activeYear = $(this).attr("year");
    category_id = $(".selectedAsin").attr("c_id");
    sub_category_id = $(".selectedAsin").attr("sub_c_id");
    attribute = $(".selectedAsin").attr("attribute");

    $(".datesFilter a.changeGraph").addClass("waves-white");
    $(".datesFilter a.weeklyDropdownTrigger").removeClass("waves-white");
    $(
      ".productViewTable .preloadOnDropdownChange, .options .right_card .preloadOnDropdownChange"
    ).fadeIn();
    activeDateFormate = "week";
    currentFormatSelected = [0, 52];
    lastTotalTickes = totalTickes;
    totalTickes = 52;
    ajaxData = {
      dateFormate: "week",
      filterType: activeFilter,
      filterValue: activeFilterValue,
      attribute: attribute,
      category_id: category_id,
      subcategory_id: sub_category_id,
      activeYear: activeYear,
      asin: asin
    };
    getDataByDateFormatedateData(ajaxData);
  }); //end function click

  /******************************************************Daily, Weekly and Monthly Buttons Dropdowns************************************************/

  /******************************************************Category, Subcategory and asin dropdown change************************************************/
  $(".category").on("change", function() {
    // <i class="fas fa-circle-notch fa-spin"></i>
    activeFilter = "sub-category";
    $(".left_card .preloadOnDropdownChange").fadeIn();
    var catId = $(this).val();
    activeFilterValue = catId;
    var defaultYearSelect = $(".datesFilter").attr("defaultyear");
    $.ajax({
      type: "get",
      url: $("body").attr("base_url") + "/client/getSubCategoryByCategory/",
      data: {
        categorId: catId,
        attribute: attribute,
        defaultYearSelect:defaultYearSelect,
      },
      success: function(response) {
        $(".left_card .preloadOnDropdownChange").fadeOut();
        if ($.isEmptyObject(response)) {
          Swal.fire({
            title: "No Sub Category Found",
            type: "info"
          });
          return;
        }
        // $(".subCategoryParent, .asinParent").css({"visibility":"visible"});
        $(".asin").attr("disabled", "disabled");
        $(".asin").formSelect();
        sub_cat = response.sub_cats;
        dataSubCat = response.data;
        if (sub_cat.length > 0) activeFilterValue = sub_cat[0];
        selectOptions =
          '<option value="" disabled selected>Select Sub Category</option>';
        $.each(sub_cat, function(indexInArray, valueOfElement) {
          Selected = indexInArray == 0 ? "" : "";
          selectOptions +=
            "<option " +
            Selected +
            ' value="' +
            valueOfElement.subcategory_id +
            '" >' +
            (valueOfElement.sub_category_alias != null && valueOfElement.sub_category_alias.length > 0 ? (valueOfElement.sub_category_alias[0].overrideLabel == null ? valueOfElement.subcategory_name : valueOfElement.sub_category_alias[0].overrideLabel ) : valueOfElement.subcategory_name) +
            "</option>";
        });
        $(".subCategory").html(selectOptions);
        $(".subCategory").formSelect();
      }, //end success function
      error: function(error) {
        $(".left_card .preloadOnDropdownChange").fadeOut();
      }
    }); //end ajax function
  });
  $(".subCategory").on("change", function() {
    // <i class="fas fa-circle-notch fa-spin"></i>
    $(".left_card .preloadOnDropdownChange").fadeIn();
    var subCatId = $(this).val();
    var defaultYearSelect = $(".datesFilter").attr("defaultyear");
    activeFilterValue = subCatId;
    activeFilter = "asin";
    $.ajax({
      type: "get",
      url: $("body").attr("base_url") + "/client/getAsinBySubCategory/",
      data: {
        subCatrogory: subCatId,
        attribute: attribute,
        defaultYearSelect:defaultYearSelect,
      },
      success: function(response) {
        $(".left_card .preloadOnDropdownChange").fadeOut();
        if ($.isEmptyObject(response)) {
          Swal.fire({
            title: "No ASIN Found",
            type: "info"
          });
          return;
        }
        $(".asin").removeAttr("disabled");

        asins = response.asins;
        dataSubCat = response.data;

        if (asins.length > 0) activeFilterValue = asins[0];
        selectOptions =
          '<option value="" disabled selected>Select Product (ASIN)</option>';
        $.each(asins, function(indexInArray, valueOfElement) {
          Selected = indexInArray == -1 ? "selected" : "";
          let productName = (valueOfElement.product_alias != null && valueOfElement.product_alias.length > 0 ?
              (valueOfElement.product_alias[0].overrideLabel == null ?
              valueOfElement.ASIN :
              (valueOfElement.product_alias[0].overrideLabel.length >= 45 ?
                "("+(valueOfElement.ASIN)+") "+valueOfElement.product_alias[0].overrideLabel.slice(0, 45) + "...":"("+(valueOfElement.ASIN)+") "+valueOfElement.product_alias[0].overrideLabel))
            : valueOfElement.ASIN);
          selectOptions +=
            "<option " +
            Selected +
            ' value="' +
            valueOfElement.ASIN +
            '" >' + productName +
            "</option>";
        });
        $(".asin").html(selectOptions);
        $(".asin").formSelect();
      }, //end success function
      error: function(error) {
        $(".left_card .preloadOnDropdownChange").fadeOut();
      }
    }); //end ajax function
  });
  $(".asin").on("change", function() {
    $(".right_card .preloadOnDropdownChange").fadeIn();
    $(
      ".productViewTable .preloadOnDropdownChange, .options .right_card .preloadOnDropdownChange"
    ).fadeIn();
    $("#chart .bg").fadeOut();
    var asin = $(this).val();
    activeFilterValue = asin;
    activeFilter = "asin";
    category_id = $(".category").val();
    sub_category_id = $(".subCategory").val();

    if (activeYear == null) {
      activeYear = $(".datesFilter").attr("defaultYear");
      activeMonth = $(".datesFilter").attr("defaultMonth");
    } else {
      if (activeMonth == null) {
        activeMonth = $(".datesFilter").attr("defaultMonth");
      }
    }
    if (activeYear == null || activeYear == "NA") {
      category_id = $(".selectedAsin").attr("c_id");
      sub_category_id = $(".selectedAsin").attr("sub_c_id");
      attribute = $(".selectedAsin").attr("attribute");
      asin = $(".selectedAsin").text();
      Swal.fire({
        title: "No Data Found",
        type: "info"
      });
      $(".right_card .preloadOnDropdownChange").fadeOut();
      $(
        ".productViewTable .preloadOnDropdownChange, .options .right_card .preloadOnDropdownChange"
      ).fadeOut();
      return;
    } else {
      if (activeMonth == null) {
        activeMonth = $(".datesFilter").attr("defaultMonth");
      }
    }
    asinDropDownChangeAjaxData = {
      asin: asin,
      attribute: attribute,
      category_id: category_id,
      subcategory_id: sub_category_id,
      dateFormate: activeDateFormate,
      activeMonth: activeMonth,
      activeYear: activeYear
    };
    manageDailyGraph(asinDropDownChangeAjaxData);
  });
  /******************************************************Category, Subcategory and asin dropdown change************************************************/

  /******************************************************OnPriceSalesSalesRank attributes Dropdown change************************************************/
  $(".attributesSelect").on("change", function() {
    category = $(".selectedAsin").attr("c_id");
    attribute = $(this).val();
    switch (attribute) {
      case "1":
        attribute = "price";
        attributeAlias = "Price";
        break;
      case "2":
        attribute = "shipped_cogs";
        attributeAlias = "Sales";
        break;
      case "3":
        attribute = "salesrank";
        attributeAlias = "Sales Rank";
        break;
    } //end switch
    attributeData = {
      category: category,
      attribute: attribute
    };
    category_id = $(".selectedAsin").attr("c_id");
    sub_category_id = $(".selectedAsin").attr("sub_c_id");
    asin = $(".selectedAsin").text();
    //setting filter type
    activeFilterValue = asin;
    activeFilter = "asin";

    if (asin == null) return;
    if (asin.length <= 0) return;

    $(".right_card .preloadOnDropdownChange").fadeIn();
    $(
      ".productViewTable .preloadOnDropdownChange, .options .right_card .preloadOnDropdownChange"
    ).fadeIn();
    attributeAjaxData = {
      asin: asin,
      attribute: attribute,
      category_id: category_id,
      subcategory_id: sub_category_id,
      dateFormate: activeDateFormate,
      activeMonth: activeMonth,
      activeYear: activeYear
    };

    manageDailyGraph(attributeAjaxData);
  }); //end function
  /******************************************************OnPriceSalesSalesRank attributes Dropdown change************************************************/

  /******************************************************Chart Data Points Click and tooltip on hover************************************************/
  $("#chart").on("click", ".data-point", function() {
    category_id = $(".selectedAsin").attr("c_id");
    sub_category_id = $(".selectedAsin").attr("sub_c_id");
    asin = $(".selectedAsin").text();
    activeDateForamteValue = $(this).attr("data-activeDateForamteValue");
    tooltipDate = $(this).attr("data-date");
    if(activeDateFormate == "month")
    activeDateForamteValue = $(this).attr("data-date");

    if (tooltipDate.includes("|")) 
      activeDateForamteValue = tooltipDate.split("|")[0];
    compCardAjaxData = {
      asin: asin,
      category_id: category_id,
      subcategory_id: sub_category_id,
      dateFormate: activeDateFormate,
      activeDateForamteValue: activeDateForamteValue,
      activeMonth: activeMonth,
      activeYear: activeYear
    };
    getCompCardsValue(compCardAjaxData);
  });
  $("#chart").on("mouseover", ".data-point", function() {
    // alert($(this).attr("data-value"));
    tooltipDate = $(this).attr("data-date");
    compCardData = $(this).attr("data-activeDateForamteValue")
    tooltipContent = "<b>Date:</b> ";
    if (tooltipDate.includes("|")) {
      tooltipContent = "<b>Start:</b> " + tooltipDate.split("|")[0];
      tooltipContent += "</br><b>End:</b> " + tooltipDate.split("|")[1];
    } else {
      if (activeDateFormate == "month") {
        tooltipContent = "<b>Month:</b> ";
        tooltipContent += moment(compCardData).format("MMMM");
        tooltipContent += "<br><b>Year:</b> ";
        tooltipContent += activeYear;
      } else {
        tooltipContent += tooltipDate;
      }
    }
    attributeAlias = $(".yLabel").text();
    tooltipContent += "</br><b>" + attributeAlias.capitalize() + ":</b> ";
    if (attributeAlias == "Sales Rank") {
      tooltipContent += d3.format(".0f")($(this).attr("data-value"));
    } else {
      tooltipContent += d3.format(".2f")($(this).attr("data-value"));
    }
    $thisobj = this;
    instancee[$(this).index()].open().content(tooltipContent);
  });
  
  /******************************************************Chart Data Points Click and tooltip on hover************************************************/

  /******************************************************Ajax Functions************************************************/
  function manageDailyGraph(dataForAjax) {
    $.ajax({
      type: "get",
      url: $("body").attr("base_url") + "/client/getDataByAsin/",
      data: dataForAjax,
      success: function (response) {
        $(".right_card .preloadOnDropdownChange").fadeOut();
        $(
          ".productViewTable .preloadOnDropdownChange, .options .right_card .preloadOnDropdownChange"
        ).fadeOut();
        if (response.graphData.length <= 0) {
          asin = $(".selectedAsin").text();
          if (asin.trim().length > 0) {
            setAllVariables();
            activeFilterValue = asin;
            activeFilter = "asin";
          }
          Swal.fire({
            title: "No Data Found",
            type: "info"
          });
          return;
        }
        updateValues(dataForAjax);
        var parsedData = parseData(response.graphData, response.tooltipData);
        CurrentData = response.graphData;
        $sizeChangeAjaxData.graphData = response.graphData;
        $sizeChangeAjaxData.tooltipData = response.tooltipData;

        draw(parsedData, response.tooltipData);
        setProductPreviewTable();
        ManageUserAction(response.userActions);
        ManageEvents(response.events);
        $(".data-point").tooltipster({
          animation: "fade",
          delay: 50,
          content: "",
          debug: false,
          contentAsHTML: true
        });
        instancee = $.tooltipster.instances(".data-point");
      }, //end success function
      error: function(error) {
        $(".left_card .preloadOnDropdownChange").fadeOut();
      }
    }); //end ajax function
  }
  function getDataByDateFormatedateData(dateData) {
    $.ajax({
      type: "get",
      url: $("body").attr("base_url") + "/client/getDataByDateFilter/",
      data: dateData,
      success: function(response) {
        userActions = response.userActions;
        events = response.events;

        $(
          ".productViewTable .preloadOnDropdownChange, .options .right_card .preloadOnDropdownChange"
        ).fadeOut();
        if (response.graphData.graphData.length <= 0) {
          setAllVariables();
          resetActiveFilterDateFunction(previousDateFormate);
          Swal.fire({
            title: "No Data Found",
            type: "info"
          });
          totalTickes = lastTotalTickes;
          setProductPreviewTable();
          return;
        } else {
          setProductPreviewTable();
          ManageUserAction(userActions);
          ManageEvents(events);
          DrawGraph(response.graphData);
          updateValues(dateData);
          $(".data-point").tooltipster({
            animation: "fade",
            delay: 50,
            debug: false,
            content: "",
            contentAsHTML: true
          });
          instancee = $.tooltipster.instances(".data-point");
        }
      },
      error: function(e) {}
    });
  } //end function
  function getCompCardsValue(dataForAjax) {
    $(".right_card .preloadOnDropdownChange").fadeIn();
    $.ajax({
      type: "get",
      url: $("body").attr("base_url") + "/client/getDailyCompCards/",
      data: dataForAjax,
      success: function(response) {
        $(".right_card .preloadOnDropdownChange").fadeOut();
        $(".options .right_card .preloadOnDropdownChange").fadeOut();
        $(".preCard span").text(
          response.length > 0 ? response[0].pre < 0 || response[0].pre == null ? 0 : response[0].pre : 0
        );
    $(".postCard span").text(
      response.length > 0 ? response[0].post < 0 || response[0].post == null
        ? 0
        : response[0].post:0
        );
        $(".salesCard").css("opacity", 1);
        // if(response.graphData.length <= 0)
        // {
        //     Swal.fire({
        //         title:"No Data Found",
        //         type: 'info',
        //     });
        //   return;
        // }
      }, //end success function
      error: function(error) {
        $(".right_card .preloadOnDropdownChange").fadeOut();
      }
    }); //end ajax function
  }
  /******************************************************Ajax Functions************************************************/

  function generateGraph(attributeData) {
    var data = [];
    activeFilter = "category";
    activeFilterValue = category;
    data = [];
    draw(data, null);
  } //end generateGraph Function

  /******************************************************On Side Bar Close/Open and Chart Container Width Changes************************************************/

  $("#sidebarToggle").click(function(e) {
    $sizeChangeAjaxData.selectedAsin = $(".selectedAsin").text();
    if ($sizeChangeAjaxData.selectedAsin.length > 0) {
      $("#chart svg").remove();
      (svg = null),
        (yAxisGroup = null),
        (xAxisGroup = null),
        (dataCirclesGroup = null),
        (dataLinesGroup = null);
      var parsedData = parseData(
        $sizeChangeAjaxData.graphData,
        $sizeChangeAjaxData.tooltipData
      );
      draw(parsedData, $sizeChangeAjaxData.tooltipData);
      $(".data-point").tooltipster({
        animation: "fade",
        delay: 50,
        content: "",
        debug: false,
        contentAsHTML: true
      });
      instancee = $.tooltipster.instances(".data-point");
    } else {
      $("#chart svg").remove();
      (svg = null),
        (yAxisGroup = null),
        (xAxisGroup = null),
        (dataCirclesGroup = null),
        (dataLinesGroup = null);
      data = [];
      draw(data, null);
    }
  });
  /******************************************************On Side Bar Close/Open and Chart Container Width Changes************************************************/

  $(".attributesSelect").val(1);

  $(".attributesSelect, .category, .subCategory, .asin").formSelect();
  $(".customTooltip").tooltipster({
    debug: false
  });
  $(".selectedAsin").tooltipster({
    debug: false,
    contentAsHTML: true,
    trigger: "click"
  });
  category = $(".category").val();
  activeYear = $(".datesFilter").attr("defaultYear");
  activeMonth = $(".datesFilter").attr("defaultMonth");
  attribute = "price";
  attributeAlias = "Price";

  attributeData = {
    category: category,
    attribute: "price"
  };
  generateGraph(attributeData);
}); //end ready function

//"%b %d" "%Y-%m-%d"
/*****************************************Graph Related Code*********************************************/

var line, yLabel;
function setYLable() {
  switch (attribute) {
    case "price":
      $(".yLabel").text("Price");
      break;
    case "shipped_cogs":
      $(".yLabel").text("Sales");
      break;
    case "salesrank":
      $(".yLabel").text("Sales Rank");
      break;
  } //end switch
}
function setXLable() {
  switch (activeDateFormate) {
    case "day":
      return monthNames[activeMonth - 1] + ", " + activeYear;
      break;
    case "week":
      return "weeks of " + activeYear;
      break;
    case "month":
      return "months of " + activeYear;
      break;
  }
}
margin = {};
function updateDimensions(winWidth) {
  margin.top = 5;
  margin.right = 5;
  margin.left = 50;
  margin.bottom = 50;

  w = winWidth - margin.left - margin.right;
  h = 500 - margin.top - margin.bottom;
}
function parseData(gdata, tooltipData) {
  var arr = [];
  $.each(gdata, function (indexInArray, valueOfElement) { 
    arr.push({
      date: indexInArray,
      value: +valueOfElement
    });
  });
  return arr;
} //end function
function draw(data, tootipData) {
  var margin = 70;
  updateDimensions($("#chart").innerWidth());
  var parse = d3.timeParse("%Y-%m-%d");
  // data manipulation first
  // data = data.map( function( datum ) {
  //     datum.cratedAt = parse( datum.cratedAt );
  //     return datum;
  // });
  var max = d3.max(data, function(d) {
    return d.value;
  });
  var min = 0;
  var pointRadius = 4;
  xData = [];
  //900-70*2
  var x =
    // d3.scaleUtc()
    // d3.domain(d3.extent(xData, d =>d.week))
    d3
      .scaleLinear()
      .domain(currentFormatSelected)
      // .domain(d3.extent(data, d => d.date))
      .range([0, w - margin * 2]);
  // .range([0,  700])
  //   var x = d3.time.scale().range([0, w - margin * 2]).domain([data[0].date, data[data.length - 1].date]);
  var y = d3
    .scaleLinear()
    .range([h - margin * 2, 0])
    .domain([min, max]);

  var xAxis = d3
    .axisBottom(x)
    // .tickSize(h - margin * 2)
    .tickSize(h - margin * 2)
    // .tickFormat(d3.timeFormat(dateFormate))
    .tickPadding(10)
    .ticks(totalTickes);
  // var yAxis ;
  // switch(attribute){
  //   case "price":
  //   case "shipped_cogs":
  //     yAxis = d3.axisLeft(y)
  //     .tickSize(-w + margin * 2).tickPadding(10).tickFormat(d3.format(".2f"));
  //     break;
  //   case "salesrank":
  //     yAxis = d3.axisLeft(y)
  //     .tickSize(-w + margin * 2).tickPadding(10);
  //     break;
  // }

  var yAxis = d3
    .axisLeft(y)
    .tickSize(-w + margin * 2)
    .tickPadding(10);

  var t = null;

  const createGlowFilter = select => {
    const filter = select
      .select("defs")
      .append("filter")
      .attr("id", "glow");

    filter
      .append("feGaussianBlur")
      .attr("stdDeviation", "4")
      .attr("result", "coloredBlur");

    const femerge = filter.append("feMerge");

    femerge.append("feMergeNode").attr("in", "coloredBlur");
    femerge.append("feMergeNode").attr("in", "SourceGraphic");
  }; //end svg filter

  svg = d3
    .select("#chart")
    .select("svg")
    .select("g");
  if (svg.empty()) {
    svg = d3
      .select("#chart")
      .append("svg:svg")
      .attr("width", w)
      .attr("height", h)
      .attr("class", "viz")
      // .attr('transform', 'translate(170,0)')
      .append("svg:g")
      .attr(
        "transform",
        "translate(" + (margin + 40) + "," + (margin - 20) + ")"
      );

    var svgDefs = svg.append("defs");

    var mainGradient = svgDefs
      .append("linearGradient")
      .attr("is", "true")
      .attr("x1", "0%")
      .attr("y1", "100%")
      .attr("x2", "0%")
      .attr("y2", "0%")
      .attr("spreadMethod", "pad")
      .attr("id", "area")
      .attr("data-reactid", ".0.0.0.0.1.0.0.0");
    // Create the stops of the main gradient. Each stop will be assigned
    // a class to style the stop using CSS.
    if (localStorage.getItem("activeThemeStopLeftColor")) {
      mainGradient
        .append("stop")
        .attr("class", "stop-left")
        .attr("offset", "5%")
        .attr("stop-color", localStorage.getItem("activeThemeStopLeftColor"))
        .attr("stop-opacity", "0.4")
        .attr("data-reactid", ".0.0.0.0.1.0.0.0.0");

      mainGradient
        .append("stop")
        .attr("class", "stop-right")
        .attr("stop-opacity", "1")
        .attr("stop-color", localStorage.getItem("activeThemeStopRightColor"))
        .attr("offset", "95%")
        .attr("data-reactid", ".0.0.0.0.1.0.0.0.1");
    } else {
      mainGradient
        .append("stop")
        .attr("class", "stop-left")
        .attr("offset", "5%")
        .attr("stop-color", "#4e73df6b")
        .attr("stop-opacity", "0.4")
        .attr("data-reactid", ".0.0.0.0.1.0.0.0.0");

      mainGradient
        .append("stop")
        .attr("class", "stop-right")
        .attr("stop-opacity", "1")
        .attr("stop-color", "#4e73df")
        .attr("offset", "95%")
        .attr("data-reactid", ".0.0.0.0.1.0.0.0.1");
    } //end else
  } //end if

  svg.call(createGlowFilter);

  t = svg.transition().duration(transitionDuration);

  // y ticks and labels
  if (!yAxisGroup) {
    yAxisGroup = svg
      .append("svg:g")
      .attr("class", "yTick")
      .call(yAxis)
      .append("text")
      .attr("class", "yLabel")
      .attr(
        "transform",
        "translate(" +
          -margin +
          "," +
          ((h - margin) / 2 - margin) +
          ")  rotate(-90)"
      )
      .text("");
  } else {
    t.select(".yTick").call(yAxis);
  } //end else

  // x ticks and labels

  $(".xTick").remove();
  xAxisGroup = null;
  if (!xAxisGroup) {
    xAxisGroup = svg
      .append("svg:g")
      .attr("class", "xTick")
      .call(xAxis)
      .append("text")
      .attr("class", "xLabel")
      .attr(
        "transform",
        "translate(" + (w / 2 - margin) + "," + (h - margin) + ")"
      )
      .text(setXLable());
  } else {
    t.select(".xTick").call(xAxis);
  } //end else

  // Draw the lines
  if (!dataLinesGroup) {
    dataLinesGroup = svg.append("svg:g");
  } //end else

  var dataLines = dataLinesGroup.selectAll(".data-line").data([data]);

  line = d3
    .line()
    .defined(d => !isNaN(d.value))
    .x(d => x(d.date))
    .y(d => y(d.value))
    // .curve(d3.curveCatmullRom.alpha(0.5));
    .curve(d3.curveLinear);

  // x.domain( [ data[ 0 ].date, data[ data.length - 1 ].date ] );
  //  hacky hacky hacky :(
  // y.domain( [ 0, d3.max( data, function( d ) { return d.value; } ) + 700 ] );

  /*
        .attr("d", d3.svg.line()
        .x(function(d) { return x(d.date); })
        .y(function(d) { return y(0); }))
        .transition()
        .delay(transitionDuration / 2)
        .duration(transitionDuration)
        .style('opacity', 1)
        .attr("transform", function(d) { return "translate(" + x(d.date) + "," + y(d.value) + ")"; });
      */

  var garea = d3
    .area()
    .x(function(d) {
      // verbose logging to show what's actually being done
      return x(d.date);
    })
    .y0(h - margin * 2)
    .y1(function(d) {
      // verbose logging to show what's actually being done
      return y(d.value);
    })

    // .curve(d3.curveCatmullRom.alpha(0.5));
    .curve(d3.curveLinear);

  dataLines
    .enter()
    .append("svg:path")
    .attr("class", "area")
    .attr("d", garea(data));

  dataLines
    .enter()
    .append("path")
    .attr("class", "data-line")
    .style("opacity", 0.3)
    //   .style('filter', 'url(#glow)')
    .attr("d", line(data));
  /*
      .transition()
      .delay(transitionDuration / 2)
      .duration(transitionDuration)
      .style('opacity', 1)
      .attr('x1', function(d, i) { return (i > 0) ? xScale(data[i - 1].date) : xScale(d.date); })
      .attr('y1', function(d, i) { return (i > 0) ? yScale(data[i - 1].value) : yScale(d.value); })
      .attr('x2', function(d) { return xScale(d.date); })
      .attr('y2', function(d) { return yScale(d.value); });
    */

  dataLines
    .transition()
    .attr("d", line)
    .duration(transitionDuration)
    .style("opacity", 1)
    .attr("transform", function(d) {
      return "translate(" + x(d.date) + "," + y(d.value) + ")";
    });

  dataLines
    .exit()
    .transition()
    .attr("d", line)
    .duration(transitionDuration)
    .attr("transform", function(d) {
      return "translate(" + x(d.date) + "," + y(0) + ")";
    })
    .style("opacity", 1e-6)
    .remove();

  d3.selectAll(".area")
    .transition()
    .duration(transitionDuration)
    .attr("d", garea(data));

  // Draw the points
  if (!dataCirclesGroup) {
    dataCirclesGroup = svg.append("svg:g");
  }
  var folatUpto2 = d3.format("s");
  var circles = dataCirclesGroup.selectAll(".data-point").data(data);

  circles
    .enter()
    .append("svg:circle")
    .attr("class", "data-point")
    .attr("data-tooltip-content", "#tooltip_content")
    .style("opacity", 1e-6)
    .attr("data-date", function(d) {
      if (tootipData != null) {
        if (Array.isArray(tootipData[d.date])) {
          return tootipData[d.date][0] + "|" + tootipData[d.date][1];
        }
        return tootipData[d.date];
      }
      return "";
    })
    .attr("data-value", function(d) {
      var f = d3.format(".2f");
      return d3.format(".2f")(d.value);
    })
    .attr("data-activeDateForamteValue", function(d) {
      if (tootipData != null) {
        if (Array.isArray(tootipData[d.date])) {
          return d.date;
        }
        if (activeDateFormate == "month") {
          
        }
        return tootipData[d.date];
      }
      return "";
    })
    .attr("cx", function(d) {
      return x(d.date);
    })
    .attr("cy", function() {
      return y(0);
    })
    .attr("r", function() {
      return data.length <= maxDataPointsForDots ? pointRadius : 0;
    })
    .transition()
    .duration(transitionDuration)
    .style("opacity", 1)
    .attr("cx", function(d) {
      return x(d.date);
    })
    .attr("cy", function(d) {
      return y(d.value);
    });

  circles
    .transition()
    .duration(transitionDuration)
    .attr("cx", function(d) {
      return x(d.date);
    })
    .attr("cy", function(d) {
      return y(d.value);
    })
    .attr("data-date", function(d) {
      if (tootipData != null) {
        if (Array.isArray(tootipData[d.date])) {
          return tootipData[d.date][0] + "|" + tootipData[d.date][1];
        }
        
        return tootipData[d.date];
      }
      return "";
    })
    .attr("data-activeDateForamteValue", function(d) {
      if (tootipData != null) {
        if (Array.isArray(tootipData[d.date])) {
          return d.date;
        }
        return tootipData[d.date];
      }
      return "";
    })
    .attr("data-value", function(d) {
      var f = d3.format(".2f");
      return d3.format(".2f")(d.value);
    })
    .attr("r", function() {
      return data.length <= maxDataPointsForDots ? pointRadius : 0;
    })
    .style("opacity", 1);

  circles
    .exit()
    .transition()
    .duration(transitionDuration)
    // Leave the cx transition off. Allowing the points to fall where they lie is best.
    // .attr('cx', function(d, i) { return xScale(i) })
    .attr("cy", function() {
      return y(0);
    })
    .attr("data-activeDateForamteValue", function(d) {
      if (tootipData != null) {
        if (Array.isArray(tootipData[d.date])) {
          return d.date;
        }
        return tootipData[d.date];
      }
      return "";
    })
    .attr("data-value", function(d) {
      return d3.format(".2f")(d.value);
    })
    .attr("data-date", function(d) {
      if (tootipData != null) {
        if (Array.isArray(tootipData[d.date])) {
          return tootipData[d.date][0] + "|" + tootipData[d.date][1];
        }
        
        return tootipData[d.date];
      }
      return "";
    })
    .style("opacity", 1e-6)
    .remove();
  setYLable();

  tt = $(".xTick .tick");
  d1 = $(tt[1]).position().left;
  d0 = $(tt[0]).position().left;
  diffTT = d1 - d0;
} //end drawa function

/*****************************************Graph Related Code*********************************************/

/*****************************************Helper Function Used In Ajax Functions*********************************************/
function DrawGraph(graphData) {
  var parsedData = parseData(graphData.graphData, graphData.tooltipData);
  data = parsedData;

  $sizeChangeAjaxData.graphData = graphData.graphData;
  $sizeChangeAjaxData.tooltipData = graphData.tooltipData;
  CurrentData = graphData;
  draw(data, graphData.tooltipData);
} //end function
function ManageUserAction(userActions) {
  actionNumber = 1;
  var dateForamate = userActions.dateFormate;
  var notes = userActions.notes;
  start = 1;

  data = userActions.data;
  tds0 = "";
  for (let index = start; index <= dateForamate; index++) {
    if (dateForamate == 12) {
      tds0 +=
        '<td class="dynamicData">' + monthNamesShort[index - 1] + "</td>\n";
    } else {
      tds0 += '<td class="dynamicData" >' + index + "</td>\n";
    }
  }
  $(".userAction0 .secondColumn").text(getDateFilterTitle(dateForamate));
  $(".userAction0 .dynamicData").remove();
  $(".userAction0").append(tds0);
  for (let i = 1; i <= 6; i++) {
    if (!data.hasOwnProperty("Action" + i)) {
      tds = "";
      for (let j = start; j <= dateForamate; j++) {
        tds += '<td class="dynamicData"><i class="fas fa-check"></i></td>\n';
      } //end for
      $(".userAction" + i + " .dynamicData").remove();
      $(".userAction" + i).append(tds);
    } //end if
    else {
      $m = 1;
      tds = "";
      $.each(data["Action" + i], function(indexInArray, valueOfElement) {
        selectClass = valueOfElement ? "dynamicData active js" : "dynamicData";

        tds +=
          '<td class="' +
          selectClass +
          '"  ' +
          (valueOfElement && notes["Action" + i][$m] != "NA"
            ? 'title="' + notes["Action" + i][$m] + '"'
            : "") +
          '><i class="fas fa-check"></i></td>\n';
        $m++;
      });
      $(".userAction" + i + " .dynamicData").remove();
      $(".userAction" + i).append(tds);
    } //end else
  } //end for
} //end function
function ManageEvents(events) {
  eventNumber = 1;
  var dateForamate = events.dateFormate;
  start = 1;

  var notes = events.notes;
  data = events.data;
  for (let i = 1; i <= 7; i++) {
    if (!data.hasOwnProperty("event" + i)) {
      tds = "";
      for (let j = start; j <= dateForamate; j++) {
        tds += '<td class="dynamicData"><i class="fas fa-check"></i></td>\n';
      } //end for
      $(".event" + i + " .dynamicData").remove();
      $(".event" + i).append(tds);
    } //end if
    else {
      $m = 1;
      tds = "";
      $.each(data["event" + i], function(indexInArray, valueOfElement) {
        selectClass = valueOfElement ? "dynamicData active js" : "dynamicData";
        tds +=
          '<td class="' +
          selectClass +
          '" ' +
          (valueOfElement && notes["event" + i][$m] != "NA"
            ? 'title="' + notes["event" + i][$m] + '"'
            : "") +
          '><i class="fas fa-check"></i></td>\n';
        $m++;
      });
      $(".event" + i + " .dynamicData").remove();
      $(".event" + i).append(tds);
    } //end else
  } //end for

  $(".dynamicData").tooltipster({
    debug: false
  });
} //end function
/*****************************************Helper Function Used In Ajax Functions*********************************************/

/*****************************************Getters or Setters*********************************************/
function setProductPreviewTable() {
  switch (totalTickes) {
    case 31:
      $(".productViewTable").addClass("dailyActive");
      $(".productViewTable").removeClass("weekActive");
      $(".productViewTable").removeClass("monthActive");

      break;
    case 52:
      $(".productViewTable").addClass("weekActive");
      $(".productViewTable").removeClass("dailyActive");
      $(".productViewTable").removeClass("monthActive");
      break;
    case 12:
      $(".productViewTable").addClass("monthActive");
      $(".productViewTable").removeClass("dailyActive");
      $(".productViewTable").removeClass("weekActive");
      break;
  } //end switch
} //end function
function resetActiveFilterDateFunction(activeDateFilter) {
  switch (activeDateFilter) {
    case "1":
      $(".datesFilter a.changeGraph").addClass("waves-white");
      $(".datesFilter a.dailyDropdownTrigger").removeClass("waves-white");
      activeDateFormate = "day";
      currentFormatSelected = [1, 31];
      break;
    case "2":
      $(".datesFilter a.changeGraph").addClass("waves-white");
      $(".datesFilter a.weeklyDropdownTrigger").removeClass("waves-white");
      activeDateFormate = "week";
      currentFormatSelected = [0, 52];
      break;
    case "3":
      $(".datesFilter a.changeGraph").addClass("waves-white");
      $(".datesFilter a.monthlyDropdownTrigger").removeClass("waves-white");
      activeDateFormate = "month";
      currentFormatSelected = [1, 12];
      break;
  } //end switch
} //end function
function updateValues(dataForAjax) {
  $(".selectedAsin")
    .text(dataForAjax.asin)
    .attr("c_id", dataForAjax.category_id)
    .attr("sub_c_id", dataForAjax.subcategory_id)
    .attr("attribute", dataForAjax.attribute)
    .attr("activeYear", dataForAjax.activeYear)
    .attr("activeMonth", dataForAjax.activeMonth);
  intActiveMonth = parseInt(dataForAjax.activeMonth);
  tooltipMonth = monthNames[intActiveMonth - 1];
  if (typeof tooltipMonth == "undefined") {
    tooltipMonth = "NA";
  }
  asinTooltip = "<ul>";
  asinTooltip += "<li><b>ASIN: </b>" + dataForAjax.asin + "</li>";
  asinTooltip += "<li><b>Category: </b>" + dataForAjax.category_id + "</li>";
  asinTooltip +=
    "<li><b>Sub Category: </b>" + dataForAjax.subcategory_id + "</li>";
  asinTooltip +=
    "<li><b>Attribute: </b>" +
    getAttributeUserFriendly(dataForAjax.attribute).capitalize() +
    "</li>";
  asinTooltip += "<li><b>Active Year: </b>" + dataForAjax.activeYear + "</li>";
  asinTooltip += "<li><b>Active Month: </b>" + tooltipMonth + "</li>";
  asinTooltip += "</ul>";
  var selectedAsinTootip = $(".selectedAsin").tooltipster("instance");
  selectedAsinTootip.open().content(asinTooltip);
  setTimeout(function() {
    selectedAsinTootip.close();
  }, 3000);
}
function getDateFilterTitle(filterType) {
  switch (filterType) {
    case 31:
      return monthNames[activeMonth - 1];
      break;
    case 12:
      return "Months";
      break;

    default:
      return "Weeks";
      break;
  }
}
function getAttributeUserFriendly(attributeOrignal) {
  switch (attributeOrignal) {
    case "price":
      return attributeOrignal;
      break;
    case "shipped_cogs":
      return "Sales";
      break;

    default:
      return "Sales Rank";
      break;
  }
}
function setAllVariables() {
  category_id = $(".selectedAsin").attr("c_id");
  sub_category_id = $(".selectedAsin").attr("sub_c_id");
  attribute = $(".selectedAsin").attr("attribute");
  asin = $(".selectedAsin").text();
  activeYear = $(".selectedAsin").attr("activeYear");
  activeMonth = $(".selectedAsin").attr("activeMonth");
}
/*****************************************Getters or Setters*********************************************/
//************************************************************
//
//************************************************************
