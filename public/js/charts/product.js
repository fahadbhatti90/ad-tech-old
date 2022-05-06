
var filterData = [];
var table,selected=[];
var dotsLimit, dotsContainerWidth;
var tableColumns = [
    {
        // "data": "isChecked",
        // adding the class name just to make finding the checkbox cells eaiser
    
        "orderable": false,
        "type":"checkbox",
        // Put the checkbox in the title bar
        width: '2px'

    },
    { title: "ASIN" },
    { title: "Product Title" },
    { title: "Fulfillment" },
    { title: "Shipped Units Last Week" },
    { title: "Shipped Units WoW" },
    { title: "Best Seller Rank" },
    { title: "Sellable Units" },
    { title: "Review Rating" },
    { title: "Review Count" }
]
var  chachedTableColumns = [
    { title: "Sr. #" },
    { title: "ASIN" },
    { title: "Product Title" },
    { title: "Fulfillment" },
    { title: "Shipped Units Last Week" },
    { title: "Shipped Units WoW" },
    { title: "Best Seller Rank" },
    { title: "Sellable Units" },
    { title: "Review Rating" },
    { title: "Review Count" }
]
$(function () {
    baseUrl = $("body").attr("base_url")+'/public';
    dotsContainerWidth = $(".itemCounts").width();

    categoryFilter();
    brandFilter();
    segmentFilter();
    tagFilter();
    colFilter();
    generateShippedChart();
    generateBBWinChart();
    generatePOUnitsChart();
    columnsToShow = $("#select-addcol").val();
    $(".filterBtn").click(function () {
        // #dataTable_filter
        if($("#filterSection").hasClass("showHide")){
            $("#dataTable_filter").css("margin-top","-225px");
            $("#filterSection").removeClass("showHide");
        } else{
            $("#dataTable_filter").css("margin-top","-58px");
            $("#filterSection").addClass("showHide");
        }
      });

    $("#upFilters").click(function(){
        // #dataTable_filter
        if($("#CatBrandSection").hasClass("showHide")){
            $(".filterLayout").css('height',200);
            $("#CatBrandSection").removeClass("showHide");
        } else{
            $(".filterLayout").css('height',50);
            $("#CatBrandSection").addClass("showHide");
        }
    });

    $("#resetBtn").click(function(){
        
        $("#select-addcol").val('').trigger('change') ;
        $("#select-segment").val('').trigger('change') ;
        $("#select-tag").val('').trigger('change') ;

    });

  
    $(".monthSelector").change(function (e) { 
        e.preventDefault();
        smv = $(this).val();
        smvArray = smv.split("|");
        monthIndex = smvArray[0];
        month = smvArray[1];
        fullmonth = smvArray[2];
        year = smvArray[3];
        $("#eventsChart .event").remove();
        generateSalesChart(monthIndex,month,year,true);
        $('#DatePart').text(fullmonth+" "+year );
    });
})
function fetchTableData(ajaxData,columnsToShow){
    $.ajax({
        type: "get",
        url: window.siteURL+"/getFilteredData",
        data:ajaxData,
        success: function (response) {
            filterData = (response);
            
            productTable_DataCall(filterData);
            
            $("#dataTable_filter").addClass("col-5");

            var label = $("#dataTable_filter").children('label').addClass("inner-addon right-addon");
            label.append('<span class="material-icons prefix" style="margin:8px 0px 2px -29px; color:#ccc7c7">search</span>');
            $('.dataTables_paginate,.dataTables_length,.dataTables_info').wrapAll('<div class="parentWrapper"></div>')


                $("td").click(function(){
                    $(this).find('input').attr('checked', true);
                });
            // if (columnsToShow.length > 0) {
                for (let index = 4; index < 10; index++) {
                    table.column(index).visible(false)
                }
                $.each(columnsToShow, function (indexInArray, valueOfElement) { 
                    if (valueOfElement != "all") {
                        table.column(valueOfElement).visible(true);
                    }//end if
                });
            // }
            
        $('.preloaderProduct').addClass("d-none");
            table.on('click', 'tr', function (event) {
                var $cell=$(event.target).closest('td');
                var data = table.row( this ).data();
                if($cell.index()>0){   
                        $("#myModal").modal("show");
                        month = "06";
                        year = "2020";
                        generateSalesChart(6,month,year,true);
                        $('#DatePart').text("June 2020");
                } else{
                    var id = this.id;
                    var index = $.inArray(id, selected);
            
                    if ( index === -1 ) {
                        selected.push( id );
                    } else {
                        selected.splice( index, 1 );
                    }
                    
                    $(this).toggleClass('selected');
                    var length = table.rows('.selected').data().length;

                    $(".tagGroupManager .counter").text(length);
                    var dot = '<span class="itemAdded"></span>';
                    var extradotCounter = '<span class="extraDotCounter"></span>';
                    if (length < dotsLimit) {
                        $(".itemCounts .itemAdded").remove();
                        $(".itemCounts .extraDotCounter").remove();
                
                        for (let index = 0; index < length; index++) {
                          $(".itemCounts").append(dot);
                        }
                      } else {
                        if ($(".extraDotCounter").length <= 0) {
                          $(".itemCounts").append(extradotCounter);
                        }
                        $(".extraDotCounter").text(
                          "+ " + (length - (dotsLimit - 1))
                        );
                      }
                      if(length > 1){
                        $(".itemLabel").text("Items")
                      }else{
                        $(".itemLabel").text("Item")
                      }
                    $(".tagGroupManager").addClass("active");
                    
                    $(".closeButton").click(function(e) {
                        e.preventDefault();
                        selected = [];
                        selectedObject = {};
                        $(".tagGroupManager").removeClass("active");
                        $("tbody tr.activeTr").click();
                        $("tr").removeClass("selected");
                        $('input[type="checkbox"]', table.cells().nodes()).prop('checked',false);
                        
                        $(".control.active").click();
                      });
                }
            });
            
        }
    });
}
function generateBBWinChart(){
    var bbWinChart = c3.generate({
        bindto: "#bbwinChart",
        data: {
            x:'x0',
            columns: [
                ['x0', 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31],
                ['bbWin',10, 50, 50, 40, 30, 60, 70, 20, 80, 70, 86, 70, 90, 60, 60, 60, 60, 80, 20, 20, 21, 22, 24, 25, 25, 46, 30, 40, 70, 90, 100],
             ],
             colors:{
                'bbWin' : '#E91E63'
             }
        },
        size: {
            height: 250
          },
        axis:{
            y: {
                show: true,
                label: {
                    text: 'BB Win',
                    position: 'outer-middle'
                },
                min : 0,
                padding : {
                    bottom : 0
                }
            }
        }  
    });
}
function generatePOUnitsChart(){
    var POUnitsChart = c3.generate({
        bindto: "#poUnitsChart",
        data: {
            x:'x0',
            columns: [
                ['x0', '2020-06-01','2020-05-31','2020-05-30','2020-05-29','2020-05-28','2020-05-27','2020-05-26','2020-05-25','2020-05-24','2020-05-23','2020-05-22','2020-05-21','2020-05-20','2020-05-19','2020-05-18','2020-05-17','2020-05-16','2020-05-15','2020-05-14','2020-05-13','2020-05-12','2020-05-11','2020-05-10','2020-05-9','2020-05-8','2020-05-7','2020-05-6','2020-05-5','2020-05-4','2020-05-3','2020-05-2','2020-05-1','2020-04-30','2020-04-29','2020-04-28','2020-04-27','2020-04-26','2020-04-25','2020-04-24','2020-04-23','2020-04-22','2020-04-21','2020-04-20','2020-04-19','2020-04-18','2020-04-17','2020-04-16','2020-04-15','2020-04-14','2020-04-13','2020-04-12','2020-04-11','2020-04-10','2020-04-9','2020-04-8','2020-04-7','2020-04-6','2020-04-5','2020-04-4','2020-04-3','2020-04-2','2020-02-2','2020-02-1','2020-03-31','2020-03-30','2020-03-29','2020-03-28','2020-03-27','2020-03-26','2020-03-25','2020-03-24','2020-03-23','2020-03-22','2020-03-21','2020-03-20','2020-03-19','2020-03-18','2020-03-17','2020-03-16','2020-03-15','2020-03-14','2020-03-13','2020-03-12','2020-03-11','2020-03-10','2020-03-9','2020-03-8','2020-03-7','2020-03-6','2020-03-5','2020-03-4','2020-03-3','2020-03-2','2020-03-1','2020-02-31','2020-02-30','2020-02-29','2020-02-28','2020-02-27','2020-02-26','2020-02-25','2020-02-24','2020-02-23','2020-02-22','2020-02-21','2020-02-20','2020-02-19','2020-02-18','2020-02-17','2020-02-16','2020-02-15','2020-02-14','2020-02-13','2020-02-12','2020-02-11','2020-02-10','2020-02-9','2020-02-8','2020-02-7','2020-02-6','2020-02-5','2020-02-4','2020-02-2','2020-02-1','2020-01-31','2020-01-30','2020-01-29','2020-01-28','2020-01-27','2020-01-26','2020-01-25','2020-01-24','2020-01-23','2020-01-22','2020-01-21','2020-01-20','2020-01-19','2020-01-18','2020-01-17','2020-01-16','2020-01-15','2020-01-14','2020-01-13','2020-01-12','2020-01-11','2020-01-10','2020-01-9','2020-01-8','2020-01-7','2020-01-6','2020-01-5','2020-01-4','2020-01-3','2020-01-2','2020-01-1','2019-12-31','2019-12-30','2019-12-29','2019-12-28','2019-12-27','2019-12-26','2019-12-25','2019-12-24','2019-12-23','2019-12-22','2019-12-21','2019-12-20','2019-12-19','2019-12-18','2019-12-17','2019-12-16','2019-12-15','2019-12-14','2019-12-13','2019-12-12','2019-12-11','2019-12-10','2019-12-9','2019-12-8','2019-12-7','2019-12-6','2019-12-5','2019-12-4'],
                ['PO Units',7457,5192,4646,6875,6855,7307,6794,5812,5360,5241,9193,7433,7519,7315,7859,6415,5650,6840,7378,7345,7035,6685,4663,4989,5899,6637,7263,6642,6381,5521,5338,6265,6799,6662,7173,7598,6604,6073,7027,8227,7285,7521,7935,7296,7498,7666,7611,8529,7644,7661,4923,5576,5862,9598,7982,7363,7740,6024,7570,9749,8820,3792,3536,6779,7067,6840,6640,6907,3665,3270,5215,6022,6312,6609,5385,3420,3935,5391,6910,6878,6384,6951,3364,3142,5149,5477,5760,6082,5457,3174,2708,4444,4554,2357,4981,4020,2631,2445,3127,3162,1358,1715,2644,2658,4890,5681,5486,6395,6823,7115,5507,4532,5917,6471,6614,6115,5846,3783,3243,4990,5823,5812,3792,3536,6779,7067,6840,6640,6907,3665,3270,5215,6022,6312,6609,5385,3420,3935,5391,6910,6878,6384,6951,3364,3142,5149,5477,5760,6082,5457,3174,2708,4444,4554,2357,4981,4020,2631,2445,3127,3162,1358,1715,2644,2658,4890,5681,5486,6395,6823,7115,5507,4532,5917,6471,6614,6115,5846,3783,3243,4990,5823,5812
            ],
            ],
            colors: {
                'PO Units': '#C70039',
            }
        },
        zoom: {
            enabled: true,
            // rescale: true
        },
        size:{
            // width:570,
            height: 260
        },
        tooltip: {
            format: {
                title: function (d) { return d3.timeFormat( '%b-%d, %Y')(d); },
                value: function (value, ratio, id) {
                    return id === 'PO Units' ? d3.format(",")(value) : value;
                }
                //value: d3.format(',') // apply this format to both y and y2
            }
        },
        axis: {
            x: {
                type : 'timeseries',
                tick: {
                    count: 40,
                    culling: {
                        max: 6 // the number of tick texts will be adjusted to less than this value
                    },
                    rotate: 0,
                    format: '%b%Y'
                }
            },
            y: {
                show: true,
                label: {
                    text: 'PO Units',
                    position: 'outer-middle'
                },
                min : 0,
                padding : {
                    bottom : 0
                },
                tick: {
                    format: d3.format(",")
                }
            },
            'PO Units': 'y'
            
        } ,
        grid: {
            y: {
                show:true
            }
        } 
    });
}
function generateShippedChart(){
    var shippedChart = c3.generate({
        bindto: "#shippedChart",
        data: {
            x:'x0',
            columns: [
                ['x0', '2020-06-01','2020-05-31','2020-05-30','2020-05-29','2020-05-28','2020-05-27','2020-05-26','2020-05-25','2020-05-24','2020-05-23','2020-05-22','2020-05-21','2020-05-20','2020-05-19','2020-05-18','2020-05-17','2020-05-16','2020-05-15','2020-05-14','2020-05-13','2020-05-12','2020-05-11','2020-05-10','2020-05-9','2020-05-8','2020-05-7','2020-05-6','2020-05-5','2020-05-4','2020-05-3','2020-05-2','2020-05-1','2020-04-30','2020-04-29','2020-04-28','2020-04-27','2020-04-26','2020-04-25','2020-04-24','2020-04-23','2020-04-22','2020-04-21','2020-04-20','2020-04-19','2020-04-18','2020-04-17','2020-04-16','2020-04-15','2020-04-14','2020-04-13','2020-04-12','2020-04-11','2020-04-10','2020-04-9','2020-04-8','2020-04-7','2020-04-6','2020-04-5','2020-04-4','2020-04-3','2020-04-2','2020-02-2','2020-02-1','2020-03-31','2020-03-30','2020-03-29','2020-03-28','2020-03-27','2020-03-26','2020-03-25','2020-03-24','2020-03-23','2020-03-22','2020-03-21','2020-03-20','2020-03-19','2020-03-18','2020-03-17','2020-03-16','2020-03-15','2020-03-14','2020-03-13','2020-03-12','2020-03-11','2020-03-10','2020-03-9','2020-03-8','2020-03-7','2020-03-6','2020-03-5','2020-03-4','2020-03-3','2020-03-2','2020-03-1','2020-02-31','2020-02-30','2020-02-29','2020-02-28','2020-02-27','2020-02-26','2020-02-25','2020-02-24','2020-02-23','2020-02-22','2020-02-21','2020-02-20','2020-02-19','2020-02-18','2020-02-17','2020-02-16','2020-02-15','2020-02-14','2020-02-13','2020-02-12','2020-02-11','2020-02-10','2020-02-9','2020-02-8','2020-02-7','2020-02-6','2020-02-5','2020-02-4','2020-02-2','2020-02-1','2020-01-31','2020-01-30','2020-01-29','2020-01-28','2020-01-27','2020-01-26','2020-01-25','2020-01-24','2020-01-23','2020-01-22','2020-01-21','2020-01-20','2020-01-19','2020-01-18','2020-01-17','2020-01-16','2020-01-15','2020-01-14','2020-01-13','2020-01-12','2020-01-11','2020-01-10','2020-01-9','2020-01-8','2020-01-7','2020-01-6','2020-01-5','2020-01-4','2020-01-3','2020-01-2','2020-01-1','2019-12-31','2019-12-30','2019-12-29','2019-12-28','2019-12-27','2019-12-26','2019-12-25','2019-12-24','2019-12-23','2019-12-22','2019-12-21','2019-12-20','2019-12-19','2019-12-18','2019-12-17','2019-12-16','2019-12-15','2019-12-14','2019-12-13','2019-12-12','2019-12-11','2019-12-10','2019-12-9','2019-12-8','2019-12-7','2019-12-6','2019-12-5','2019-12-4'],
                ['Shipped Units',7457,5192,4646,6875,6855,7307,6794,5812,5360,5241,9193,7433,7519,7315,7859,6415,5650,6840,7378,7345,7035,6685,4663,4989,5899,6637,7263,6642,6381,5521,5338,6265,6799,6662,7173,7598,6604,6073,7027,8227,7285,7521,7935,7296,7498,7666,7611,8529,7644,7661,4923,5576,5862,9598,7982,7363,7740,6024,7570,9749,8820,3792,3536,6779,7067,6840,6640,6907,3665,3270,5215,6022,6312,6609,5385,3420,3935,5391,6910,6878,6384,6951,3364,3142,5149,5477,5760,6082,5457,3174,2708,4444,4554,2357,4981,4020,2631,2445,3127,3162,1358,1715,2644,2658,4890,5681,5486,6395,6823,7115,5507,4532,5917,6471,6614,6115,5846,3783,3243,4990,5823,5812,3792,3536,6779,7067,6840,6640,6907,3665,3270,5215,6022,6312,6609,5385,3420,3935,5391,6910,6878,6384,6951,3364,3142,5149,5477,5760,6082,5457,3174,2708,4444,4554,2357,4981,4020,2631,2445,3127,3162,1358,1715,2644,2658,4890,5681,5486,6395,6823,7115,5507,4532,5917,6471,6614,6115,5846,3783,3243,4990,5823,5812
            ],
             ],
             colors: {
                'Shipped Units': '#FF5733',
             }
        },
        zoom: {
            enabled: true,
            // rescale: true
        },
        size:{
            // width:570,
            height: 260
        },
        tooltip: {
            format: {
                title: function (d) { return d3.timeFormat( '%b-%d, %Y')(d); },
                value: function (value, ratio, id) {
                    return id === 'Shipped Units' ? d3.format(",")(value) : value;
                }
                //value: d3.format(',') // apply this format to both y and y2
            }
        },
        axis: {
            x: {
                type : 'timeseries',
                tick: {
                    count: 40,
                    culling: {
                        max: 6 // the number of tick texts will be adjusted to less than this value
                    },
                    rotate: 0,
                    format: '%b%Y'
                }
            },
            y: {
                show: true,
                label: {
                    text: 'Shipped Units',
                    position: 'outer-middle'
                },
                min : 0,
                padding : {
                    bottom : 0
                },
                tick: {
                    format: d3.format(",")
                }
            },
            'Shipped Units': 'y'
            
        } ,
        grid: {
            y: {
                show:true
            }
        } 
    });
}
var dataMonthWise = [
    {
        "x": [
            'x0', 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31
        ],
        "Sales": [
            'Sales',606,626,975,980,846,894,899,744,925,1055,1074,933,948,904,663,635,458,291,284,523,641,557,647,787,1714
        ],
        "events":  [
            [4,'Crap', 23, 31, 5,"background-image: linear-gradient(180deg, #4e73df00 10%, #224abe80 100%);","#224abe"],
            [6,'Seller change', 24,25, 25,"background-image: linear-gradient(180deg, #4e73df00 10%, #795548d4 100%);","#795548"],
            [7,'Price change', 9,22, 45,"background-image: linear-gradient(180deg, #4e73df00 10%, #9c27b0c9 100%);","#9c27b0"],
        ],
        "eventIds": [
            4,6,7
        ]
    },//december
    {
        "x": [
            'x0',1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31
        ],
        "Sales": [
            'Sales',335,505,536,422,522,698,819,710,806,613,556,552,698,772,790,747,730,1085,872,921,1183,1002,974,802,628,802,1093,1019,1055,1086,1077
        ],
        "events":  [
            [4,'Crap', 1,16, 5,"background-image: linear-gradient(180deg, #4e73df00 10%, #224abe80 100%);","#224abe"],
            [7,'Price change', 17,31, 25,"background-image: linear-gradient(180deg, #4e73df00 10%, #9c27b0c9 100%);","#9c27b0"],
        ],
        "eventIds": [
            4,7
        ]
    },//january
    {
        "x": [
            'x0', 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29
        ],
        "Sales": [
            'Sales',618,714,955,1329,1275,1283,1150,912,1060,1382,1564,1498,1262,1019,871,1029,1321,1436,1330,1475,1415,1115,867,1138,1585,2194,2268,2051,1350
        ],
        "events":  [
            [4,'Crap', 15, 24, 5,"background-image: linear-gradient(180deg, #4e73df00 10%, #224abe80 100%);","#224abe"],
            [6,'Seller change', 1, 3, 25,"background-image: linear-gradient(180deg, #4e73df00 10%, #795548d4 100%);","#795548"],
            [7,'Price change', 4, 22, 45,"background-image: linear-gradient(180deg, #4e73df00 10%, #9c27b0c9 100%);","#9c27b0"],
            [7,'Price change', 23, 29, 45,"background-image: linear-gradient(180deg, #4e73df00 10%, #9c27b0c9 100%);","#9c27b0"],
            [8,'Advertising', 16, 17, 85,"background-image: linear-gradient(180deg, #4e73df00 10%, #4caf50c2 100%);","#4CAF50"],
        ],
        "eventIds": [
            4,6,7,8
        ]
    },//february
    {
        "x": [
            'x0',1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31
        ],
        "Sales": [
            'Sales',997,834,2664,787,1031,1170,869,618,1280,1414,2392,3412,2842,1800,2135,1826,2090,2382,2163,1750,1234,1344,1432,1244,1229,1109,1049,1136,1062,1106,1154
        ],
        "events":  [
            [6,'Seller change', 6, 9, 5,"background-image: linear-gradient(180deg, #4e73df00 10%, #795548d4 100%);","#795548"],
            [7,'Price change', 1, 4, 25,"background-image: linear-gradient(180deg, #4e73df00 10%, #9c27b0c9 100%);","#9c27b0"],
            [7,'Price change', 19, 20, 25,"background-image: linear-gradient(180deg, #4e73df00 10%, #9c27b0c9 100%);","#9c27b0"],
            [7,'Price change', 24, 25, 25,"background-image: linear-gradient(180deg, #4e73df00 10%, #9c27b0c9 100%);","#9c27b0"],
            [8,'Advertising', 14, 15, 45,"background-image: linear-gradient(180deg, #4e73df00 10%, #4caf50c2 100%);","#4CAF50"],
        ],
        "eventIds": [
            6,7,8
        ]
    },//march
    {
        "x": [
            'x0',1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30
        ],
        "Sales": [
            'Sales',1180, 1108, 1115, 1148, 1158, 1027, 929, 952, 929, 793, 802, 799, 953, 1005, 1413, 1159, 1044, 971, 1060, 1034, 1016, 1043, 953, 969, 965, 1049, 1030, 1054, 1074, 1112
        ],
        "events":  [
            [1,'Content', 4, 5,5,"background-image: linear-gradient(180deg, #4e73df00 10%, #3f51b5ba 100%);","#323f8a"],
        ],
        "eventIds": [
            1
        ]
    },//april
    {
        "x": [
            'x0',1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31
        ],
        "Sales": [
            'Sales',1031,965,1092,1099,1141,1191,1209,1094,1060,1048,1214,1170,1223,1192,1100,1177,1526,1429,1373,1437,1180,1021,907,1092,1256,1126,1236,941,931,785,1023
        ],
        "events":  [
            [7,'Price change', 20, 21, 5,"background-image: linear-gradient(180deg, #4e73df00 10%, #9c27b0c9 100%);","#9c27b0"],
            [7,'Price change', 27, 28, 5,"background-image: linear-gradient(180deg, #4e73df00 10%, #9c27b0c9 100%);","#9c27b0"],
            
        ],
        "eventIds": [
            7
        ]
    },//may
    {
        "x": ['x0',1,2,3,4],
        "Sales": ['Sales',1162,1275,1698,1658],
        "events":  [
            [7,'Price change', 2, 3, 5,"background-image: linear-gradient(180deg, #4e73df00 10%, #9c27b0c9 100%);","#9c27b0"],
        ],
        "eventIds": [7]
    },//jun
];

function generateSalesChart(monthIndex,month,year,isDropDownChange){  
    var totalTiks = dataMonthWise[monthIndex].x.length-1;
    var diffrenceInTick = 31 - totalTiks;
    
    var graphWidth = $(".modal-body").width();
    var salesChart = c3.generate({
        bindto: "#salesChart",
        data: {
            x:'x0',
            columns: [
                dataMonthWise[monthIndex].x,
                dataMonthWise[monthIndex].Sales,
             ]
        },
        oninit: function () {
            setTimeout(() => {
                ManagerEvents(monthIndex);
            }, 100);
        },
        legend:{
            position: 'right',
            // hide: 'Sales'
            show: false
        },
        size: {
            // width:graphWidth,
            // height: 270
        }
        ,
        tooltip: {
            format: {
                title: function (d) { return  d3.timeFormat( '%b %d, %Y')(new Date(year+"-"+month+"-"+(d+1)));},
                value: function (value, ratio, id) {
                    // return value;
                    return d3.format("$,")(value);
                }
                //value: d3.format(',') // apply this format to both y and y2
            }
        },
        axis:{
            y: {
                show: true,
                label: {
                    text: 'Sales',
                    position: 'outer-middle'
                },
                padding : {
                    bottom : 0
                },
                tick: {
                    outer: false,
                    format:function (d) {
                        return d3.format("$,.2s")(d)    ;
                    } 
                },
                    
            },
            x:{
                type: 'category',
                tick: {
                    outer: false,
                },
            }
        },
        grid: {
            y: {
                show:true
            }
        }
    });

}
function ManagerEvents(monthIndex) {
        $(".extraLine").remove();
        $(".productTableVisuals .c3-axis.c3-axis-y > path").show();
        $("#salesChart").append('<div class="extraLine" ><div class="eventLabelContainer"><div class="eventsLabel">Events</div></div></div>')
        firstOffset = $($("#salesChart svg > g > .c3-axis.c3-axis-x > .tick")[0]).offset().left - parseInt($(".c3-axis.c3-axis-y path").offset().left);
        $(".extraLine").css("left",((parseInt($("#salesChart svg > g > .c3-axis.c3-axis-y path").offset().left) - parseInt($("#salesChart svg").offset().left))-1)+"px")
        var eventsUi = dataMonthWise[monthIndex].events;
        // [0,'Reviews', 7, 9,5,"background-image: linear-gradient(180deg, #9e9e9e00 10%, #b3b3b3ad 100%);","#b3b3b3"],
        // [1,'Content', 15, 20,25,"background-image: linear-gradient(180deg, #4e73df00 10%, #3f51b5ba 100%);","#323f8a"],
        // [1,'Content', 10, 13,25,"background-image: linear-gradient(180deg, #4e73df00 10%, #3f51b5ba 100%);","#323f8a"],
        // [2,'page not found', 11, 15, 45,"background: linear-gradient(180deg, #4e73df00 10%, #ff00f4b0 100%);","#ff00f4"],
        // [3,'Andon Cord', 5, 9, 65,"background-image: linear-gradient(180deg, #f4433600 10%, #f44336ad 100%);","#f44336"],
        // [4,'Crap', 23, 31, 85,"background-image: linear-gradient(180deg, #4e73df00 10%, #224abe80 100%);","#224abe"],
        // [5,'Out of Stock', 11, 15, 105,"background-image: linear-gradient(180deg, #4e73df00 10%, #03ffc58c 100%);","#03ffc5"],
        // [6,'Seller change', 24,25, 125,"background-image: linear-gradient(180deg, #4e73df00 10%, #795548d4 100%);","#795548"],
        // [7,'Price change', 9,22, 145,"background-image: linear-gradient(180deg, #4e73df00 10%, #9c27b0c9 100%);","#9c27b0"],
        // [8,'Advertising', 26, 31, 165,"background-image: linear-gradient(180deg, #4e73df00 10%, #4caf50c2 100%);","#4CAF50"],
        if ($("#eventsChart .event").length <= 0) {
            $("#eventsChart .event").remove();
            var topDistance = 5;
            for (let index = 0; index < eventsUi.length; index++) {
                const element = eventsUi[index];
                var eventLine = ' <div class="event event'+element[0]+' eventPosition'+index+'" style="background:'+element[6]+';top:'+(element[4])+'px"><div class="eventGradientContainer"><div class="eventGradient" style="'+element[5]+'"></div></div></div>'
                /**Event Width */
                var index1 = element[2] - 1;
                var index2 = element[3] - 1;
                if (monthIndex == 0) {
                    if (index1 > 25) {
                        index1 = index1 - 6;
                    }
                    if (index2 > 25) {
                        index2 = index2 - 6;
                    }
                }
                var element1OffsetForWidth = $($("#salesChart svg > g > .c3-axis.c3-axis-x > .tick")[(index1)]).offset().left;
                var element2OffsetForWidth = $($("#salesChart svg > g > .c3-axis.c3-axis-x > .tick")[(index2)]).offset().left;
                var newElementWidth = element2OffsetForWidth - element1OffsetForWidth;
                
                /**Event Width */
                /**Poitioning event */
                var element1Offset = $($("#salesChart svg > g > .c3-axis.c3-axis-x > .tick")[index1]).offset().left;
                var svgOffset = $("#salesChart svg").offset().left;
                var startPoint = element1Offset - svgOffset;
                /**Poitioning event */
                
                $("#eventsChart").append(eventLine);
                $(".event.eventPosition"+index).css("left",startPoint);
                $(".event.eventPosition"+index).width(newElementWidth+"px");
            }
        }
        
        $(".checkboxContainer").hide();
        console.log(monthIndex)
        console.log(dataMonthWise[monthIndex].eventIds)
        console.log(dataMonthWise[monthIndex].events)
        for (let index = 0; index <  dataMonthWise[monthIndex].eventIds.length; index++) {
            const element = dataMonthWise[monthIndex].eventIds[index];
            $(".checkboxContainer[data-index='"+element+"']").show();
        }
        $(".productTableVisuals .c3-axis.c3-axis-y > path").hide();
   
}
function productTable_DataCall(filterData){
    table = $('#dataTables1').DataTable( {
        data:filterData,
        columns: tableColumns,
        createdRow: function (row, data, dataIndex) {
            // get the column defs from settings
            var colDefs = this.api().settings()[0].aoColumns;

            // for each column in the columns
            // this logic assumes that columns:[] contains an entery for every column.
            $.each(colDefs, function (i, item) {
                // get the associated td
                var cell = $("td", row).eq(i);
                // figure out data associated with the row
                // it may be an array, it may be an object
                cellData = null;
                if (typeof item.data == "string" && typeof data == "object") {
                    cellData = data[item.data];
                }
                else if(Array.isArray(data)){
                    cellData = data[i];
                }
                switch (item.type) {
                    case "money":
                        // not implemented
                        break;
                    case "selectbox":
                        //not implemented
                        break;
                    case "checkbox":
                        // assumed that if the data is type boolean and it is true
                        // apply it to the checkbox
                        if (cellData === true) {
                            cell.html("<input type='checkbox' checked />");
                        }
                        else {
                            cell.html("<input type='checkbox'  />");
                        }
                     
                        break;
                    default:
                        // take no action, use defaults
                        break;
                }
            })
            
        },
        'columnDefs': [ 
            {
                'targets': 0,
                'checkboxes': {
                   'selectRow': true
                }
             },
            {
            'targets': "_all", /* column index */
            'orderable': true, /* true or false */
        }],
        'select': {
            'style': 'multi'
         },
        "initComplete": function(settings, json) { // this gets rid of duplicate headers
            console.log($('.dataTables_scrollBody thead tr').height()); 
            // $('').addClass("VisibilityCollapsed"); 
        },
        "ordering": true,
        "processing": true,
        "scrollX": true,
        "pagingType": "simple_numbers",
        "dom": '<f<t>lip>'
    });
}

function categoryFilter(){
    $("#select-category").select2({
        placeholder: 'Select Category',
        closeOnSelect: false,
        allowClear: true,
        width :'500px',
        height: '34px',
        debug: true,
        dropdownParent: $('.selectCategoryParent')
    });
    let optgroupState = {};

    $("body").on('click', '.select2-container--open .select2-results__group', function() {
    $(this).siblings().toggle();
    let id = $(this).closest('.select2-results__options').attr('id');
    let index = $('.select2-results__group').index(this);
    optgroupState[id][index] = !optgroupState[id][index];
    })

    $('#select-category').on('select2:open', function() {
    $('.select2-dropdown--below').css('opacity', 0);
    setTimeout(() => {
        let groups = $('.select2-container--open .select2-results__group');
        let id = $('.select2-results__options').attr('id');
        if (!optgroupState[id]) {
        optgroupState[id] = {};
        }
        $.each(groups, (index, v) => {
        optgroupState[id][index] = optgroupState[id][index] || false;
        optgroupState[id][index] ? $(v).siblings().show() : $(v).siblings().hide();
        })
        $('.select2-dropdown--below').css('opacity', 1);
    }, 0);
    })

}

function brandFilter(){
    $("#select-brand").select2({
        placeholder: 'Select Brand',
        closeOnSelect: false,
        allowClear: true,
        maximumSelectionLength: 10,
        tags : true,
        width :'500px',
        // height: '34px',
        debug: true,
        dropdownParent: $('.selectChildBrandParent')
    });

    let optgroupState = {};

    // $("body")
    $('#select-brand').on('click', '.select2-container--open .select2-results__group', function() {
    $(this).siblings().toggle();
    let id = $(this).closest('.select2-results__options').attr('id');
    let index = $('.select2-results__group').index(this);
    optgroupState[id][index] = !optgroupState[id][index];
    })

    $('#select-brand').on('select2:open', function() {
    $('.select2-dropdown--below').css('opacity', 0);
    setTimeout(() => {
        let groups = $('.select2-container--open .select2-results__group');
        let id = $('.select2-results__options').attr('id');
        if (!optgroupState[id]) {
        optgroupState[id] = {};
        }
        $.each(groups, (index, v) => {
        optgroupState[id][index] = optgroupState[id][index] || false;
        optgroupState[id][index] ? $(v).siblings().show() : $(v).siblings().hide();
        })
        $('.select2-dropdown--below').css('opacity', 1);
    }, 0);
    })

}

function segmentFilter(){
    $("#select-segment").select2({
        placeholder: 'Enter Product Segment',
        closeOnSelect: false,
        allowClear: true,
        maximumSelectionLength: 10,
        tags : true,
        width :'260px',
        // height: '34px',
        debug: true,
        dropdownParent: $('.selectProductSegmentParent')
    });

    $(".select2-search__field").val('');
    let optgroupState = {};

    // $("body")
    $('#select-segment').on('click', '.select2-container--open .select2-results__group', function() {
    $(this).siblings().toggle();
    let id = $(this).closest('.select2-results__options').attr('id');
    let index = $('.select2-results__group').index(this);
    optgroupState[id][index] = !optgroupState[id][index];
    })

    $('#select-segment').on('select2:open', function() {
    $('.select2-dropdown--below').css('opacity', 0);
    setTimeout(() => {
        let groups = $('.select2-container--open .select2-results__group');
        let id = $('.select2-results__options').attr('id');
        if (!optgroupState[id]) {
        optgroupState[id] = {};
        }
        $.each(groups, (index, v) => {
        optgroupState[id][index] = optgroupState[id][index] || false;
        optgroupState[id][index] ? $(v).siblings().show() : $(v).siblings().hide();
        })
        $('.select2-dropdown--below').css('opacity', 1);
    }, 0);
    })

}

function tagFilter(){
        $("#select-tag").select2({
            placeholder: 'Enter Campaign Tag Name',
            closeOnSelect: false,
            allowClear: true,
            maximumSelectionLength: 10,
            tags : true,
            width :'260px',
            debug: true,
            dropdownParent: $('.selectProductTagParent')
        });
    
        let optgroupState = {};
    
        // $("body")
        $('#select-tag').on('click', '.select2-container--open .select2-results__group', function() {
        $(this).siblings().toggle();
            let id = $(this).closest('.select2-results__options').attr('id');
            let index = $('.select2-results__group').index(this);
            optgroupState[id][index] = !optgroupState[id][index];
        })
    
        $('#select-tag').on('select2:open', function() {
        $('.select2-dropdown--below').css('opacity', 0);
        setTimeout(() => {
            let groups = $('.select2-container--open .select2-results__group');
            let id = $('.select2-results__options').attr('id');
            if (!optgroupState[id]) {
            optgroupState[id] = {};
            }
            $.each(groups, (index, v) => {
            optgroupState[id][index] = optgroupState[id][index] || false;
            optgroupState[id][index] ? $(v).siblings().show() : $(v).siblings().hide();
            })
            $('.select2-dropdown--below').css('opacity', 1);
        }, 0);
        })
    
}

function colFilter(){
    $("#select-addcol").select2({
        placeholder: 'Enter Column Name',
        closeOnSelect: false,
        allowClear: true,
        maximumSelectionLength: 10,
        tags : true,
        width :'260px',
        debug: true,
        dropdownParent: $('.selectColumnParent')
    });

    let optgroupState = {};

    // $("body")
    $('#select-addcol').on('click', '.select2-container--open .select2-results__group', function() {
    $(this).siblings().toggle();
        let id = $(this).closest('.select2-results__options').attr('id');
        let index = $('.select2-results__group').index(this);
        optgroupState[id][index] = !optgroupState[id][index];
    })

    $('#select-addcol').on('select2:open', function() {
    $('.select2-dropdown--below').css('opacity', 0);
    setTimeout(() => {
        let groups = $('.select2-container--open .select2-results__group');
        let id = $('.select2-results__options').attr('id');
        if (!optgroupState[id]) {
        optgroupState[id] = {};
        }
        $.each(groups, (index, v) => {
        optgroupState[id][index] = optgroupState[id][index] || false;
        optgroupState[id][index] ? $(v).siblings().show() : $(v).siblings().hide();
        })
        $('.select2-dropdown--below').css('opacity', 1);
    }, 0);
    })
    $('#select-addcol').val(['4','5','6',"7","8","9","10"]); // Select the option with a value of 'ASIN,'Product Title','Fulfillment Channel','Shipped Units','Review Score'
   
    $('#select-addcol').trigger('change'); // Notify any JS components that the value changed

}
