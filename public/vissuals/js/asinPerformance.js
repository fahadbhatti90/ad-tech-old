var dateRangePicker;
var selectedCampaigns;
var selectedAsin;
//charts variables
var chartPerformance,chartEfficiency,chartAwareness;

var filterData ={
    sp:null,
    profileId:null,
    campaignId:null,
    startDate:null,
    endDate:null,
    ASIN:null
    }
$(function () {    
     dataUrl = $("#select-asin").attr("data-url");
    /**
     * Check if the session is not expired and brand is selected
     */
    let brand = $("#brandSwitcher").attr("brand-id");
    
    let datePickerObject = {
        autoUpdateInput: false,
        linkedCalendars: false,
        locale: {
            cancelLabel: 'Clear',
            format: 'MM/DD/YYYY'
        },
        ranges: {
            'Last Week': [moment().subtract(6, 'days'), moment()],
            'Last 2 weeks': [moment().subtract(12, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
         },
        opens: 'left', 
        drops:'down'
    }

    let localfilterData = JSON.parse(localStorage.getItem("asinfilterData"));
   
    if(brand !=""){
       
        if(localfilterData){
            if(localfilterData.startDate !== null && localfilterData.endDate !== null) {
                let startDate = localfilterData.startDate;
                let endDate = localfilterData.endDate;
                localfilterData.startDate = moment(localfilterData.startDate).format('YYYYMMDD');
                localfilterData.endDate = moment(localfilterData.endDate).format('YYYYMMDD');
                if(brand !== ""){
                    let newObj = {startDate: startDate,
                                    endDate: endDate}
                    Object.assign(datePickerObject,newObj);
                    datePickerObject.autoUpdateInput = true;
                }
            }
        }
          
        dateRangePicker = $('input[name="datefilter"]').daterangepicker(datePickerObject,
        function(start, end, label) {
            $(this).val(start.format('MM/DD/YYYY') + ' - ' + end.format('MM/DD/YYYY'));
           });
    
        dateRangePicker.on('apply.daterangepicker', function(ev, picker) {
            filterData.startDate = picker.startDate.format('MM/DD/YYYY');
            filterData.endDate = picker.endDate.format('MM/DD/YYYY');
            filterDataUpdate("startDate",filterData.startDate);
            filterDataUpdate("endDate",filterData.endDate);
            
            filterData.startDate = picker.startDate.format('YYYYMMDD');
            filterData.endDate = picker.endDate.format('YYYYMMDD');
    
            
            if(filterData.startDate && filterData.endDate && filterData.campaignId && filterData.profileId && filterData.ASIN){
                CampaignAjaxCalls(filterData);
                
            }//end if
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        });
        
        dateRangePicker.on('cancel.daterangepicker', function(ev, picker) {
           $(this).val('');
           filterData.startDate = "";
           filterData.endDate = "";
           filterDataUpdate("startDate",null);
           filterDataUpdate("endDate",null);
        });
    
        $(".profileSelect").on("change", function () {
            filterData.profileId = $(this).val();
            if(filterData.profileId == ""){ 
                filterData.profileId = null
            }
            resetFilters();
           
            filterData.campaignId = null;
            $.ajax({
                type: "get",
                url: $(this).attr("campaign-url"),
                data: {
                    profileId: filterData.profileId
                },
                success: function (response) {
                    $campaignsOption = [];
                    if(response.campaigns.length >0){
                        $campaignsOption = '<option data-title="Select All" value="All">Select All</option>';
                    }

                    $.each(response.campaigns, function (indexInArray, valueOfElement) { 
                        $campaignsOption += '<option data-title="' + valueOfElement.name + '" value="' + valueOfElement.campaignId + '">'+(valueOfElement.name.length>15?valueOfElement.name.substr(0,15)+"...":valueOfElement.name)+'</option>';
                    });
                    $(".campaignSelect").html($campaignsOption)    
                }//end success
            });//end ajax function
        });//end change function
    
        $("#sidebarToggle").on("click", function () {
            if(filterData.startDate && filterData.endDate && filterData.campaignId && filterData.profileId){
                chartPerformance.resize();
                chartEfficiency.resize();
                chartAwareness.resize();
            }
    
        });
    
        $(".asinSelect").on("change",function(){
            filterData.ASIN = $(this).val();
            localStorage.setItem("asinfilterData",JSON.stringify(filterData));
            if(filterData.startDate && filterData.endDate && filterData.campaignId && filterData.profileId && filterData.ASIN){
                CampaignAjaxCalls(filterData); 
            }//end if
        })
        selectCampaign();
      
        defaultMetricValues();
    }

    $(".campaignSelect").select2({
        placeholder: "Select Campaigns",
        closeOnSelect: false,
        allowClear: true,
        width: '100%',
        height: '40px',
        dropdownParent:$(".campaignSelectParent")
       })
    $(".asinSelect").select2({
        placeholder: "Select ASIN",
        closeOnSelect: true,
        allowClear: true,
        width: '100%',
        height: '40px'
    });

    if(brand !== ""){
        if(localStorage.getItem("asinfilterData")){
            let localfilterData = JSON.parse(localStorage.getItem("asinfilterData"));
            filterData = localfilterData;
            if(localfilterData.profileId){
                $(".profileSelect option").removeAttr("selected")
                if($(".profileSelect option[value='"+localfilterData.profileId+"']").length > 0){
                    $(".profileSelect option[value='"+localfilterData.profileId+"']").attr("selected","selected")
                } else{
                    localfilterData.profileId = "";
                    resetFilters();
                }
                $.ajax({
                    type: "get",
                    url: $(".profileSelect").attr("campaign-url"),
                    data: {
                        profileId: localfilterData.profileId
                    },
                    success: function (response) {
                        $campaignsOption = [];
                        if(response.campaigns.length >0){
                            $campaignsOption = '<option data-title="Select All" value="All">Select All</option>';
                        }
                        
                        $.each(response.campaigns, function (indexInArray, valueOfElement) { 
                            $campaignsOption += '<option data-title="' + valueOfElement.name + '" value="' + valueOfElement.campaignId + '">'+(valueOfElement.name.length>15?valueOfElement.name.substr(0,15)+"...":valueOfElement.name)+'</option>';
                 
                        });
                        $(".campaignSelect").html($campaignsOption)    
                        if(localfilterData.campaignId){
                            let dataCampaigns;
                            $(".campaignSelect option").removeAttr("selected")
                            if(localfilterData.campaignId!="All"){
                                dataCampaigns = JSON.parse("[" + localfilterData.campaignId + "]");
                                if(dataCampaigns.length < 2){
                                    $('.select2-selection').css("overflow-y","hidden");                                    
                                }
                                $.each(dataCampaigns, function (indexInArray, valueOfElement) { 
                                    $(".campaignSelect option[value="+valueOfElement+"]").attr("selected","selected");
                                })
                            }else{
                                $('.select2-selection').css("overflow-y","hidden");
                                dataCampaigns=localfilterData.campaignId;
                                $(".campaignSelect option[value="+dataCampaigns+"]").attr("selected","selected");        
                            }
                            $.ajax({
                                type: "get",
                                url: $("#select-campaign").attr("asin-url"),
                                data: {
                                    profileId: localfilterData.profileId,
                                    campaignId: localfilterData.campaignId
                                },
                                success: function (response) {
                                    $asinData = '<option value="" selected>Select ASIN</option>';
                                    $.each(response.asins, function (indexInArray, valueOfElement) { 
                                        $asinData += '<option value="' + valueOfElement.asin + '">'+(valueOfElement.asin)+'</option>';
                                    });
                                    $(".asinSelect").html($asinData);

                                    if(localfilterData.ASIN){
                                        $(".asinSelect option").removeAttr("selected")
                                        $(".asinSelect option[value="+localfilterData.ASIN+"]").attr("selected","selected");
            
                                    }
                                }
                            });
                        }
                    }//end success
                });//end ajax function
               

            }
            if(localfilterData.startDate && localfilterData.endDate && localfilterData.campaignId && localfilterData.profileId && localfilterData.ASIN){
                localfilterData.startDate = moment(localfilterData.startDate).format('YYYYMMDD');
                localfilterData.endDate = moment(localfilterData.endDate).format('YYYYMMDD');
                filterData = localfilterData;
                CampaignAjaxCalls(filterData);
            }
        }
    }

});

/**
 * 
 * @param {*} filterData 
 */
function filterDataUpdate(key,obj){
    let newfilter = filterData;
    newfilter[key] = obj;
    if(newfilter.startDate){
        newfilter.startDate = moment(newfilter.startDate).format('MM/DD/YYYY')
        newfilter.endDate = moment(newfilter.endDate).format('MM/DD/YYYY')
    }
    localStorage.setItem("asinfilterData",JSON.stringify(newfilter));
}
/**
 * Ajax Data calls for Campaigns 
 */
function CampaignAjaxCalls(filterData){
    if(filterData.startDate && filterData.endDate){
        filterData.startDate = moment(filterData.startDate).format('YYYYMMDD')
        filterData.endDate = moment(filterData.endDate).format('YYYYMMDD')
    }
    if(filterData.startDate && filterData.endDate && filterData.campaignId && filterData.profileId && filterData.ASIN){
        showPreLoaders(".preLoader");
        $('input[name="datefilter"]').prop( "disabled", true );

        $("#sectionA").removeClass("defaultHide");
        getMetricsScoreCards();
        performanceChart();
        efficiencyChart();
        awarenessChart();
        refreshContent(".reloadIcon");
        refreshContent(".reloadIcon-sm");

    }//end if
}
/**
 * get Metrics Scores
 */
function getMetricsScoreCards(){
    filterData.sp = "spCalculateAMSScoreCardsAsinLevel";
    $.ajax({
        method: "GET",
        data:filterData,
        url: dataUrl,
        success: function(result){
            if(result.length!=0){
                $("#impressions_txt_box").text(commaSeparator(+result[0].Impressions));
                if($('#impressions_txt_box').hasClass("tooltipstered")){
                    $('#impressions_txt_box').tooltipster('content',result[0].Impressions);
                }else{
                    $("#impressions_txt_box").attr("title",result[0].Impressions);
                }
               

                $("#clicks_txt_box").text(commaSeparator(+result[0].Clicks));
                $("#ctr_txt_box").text(result[0].CTR+"%");
                
                $("#cpc_txt_box").text("$"+commaSeparator(+result[0].CPC));
                
                if($('#cpc_txt_box').hasClass("tooltipstered")){
                    $('#cpc_txt_box').tooltipster('content', "$"+result[0].CPC);
                }else{
                    $("#cpc_txt_box").attr("title","$"+result[0].CPC);
                }

                
                $("#conversions_txt_box").text(commaSeparator(+result[0].order_conversion));
                if($('#conversions_txt_box').hasClass("tooltipstered")){
                    $('#conversions_txt_box').tooltipster('content', result[0].order_conversion);
                }else{
                    $("#conversions_txt_box").attr("title",result[0].order_conversion);
                }

                $("#cpa_txt_box").text(result[0].CPA);
                $("#spend_txt_box").text("$"+commaSeparator(+result[0].Cost));

                if($('#spend_txt_box').hasClass("tooltipstered")){
                    $('#spend_txt_box').tooltipster('content', "$"+result[0].Cost);
                }else{
                    $("#spend_txt_box").attr("title","$"+result[0].Cost);
                }


                
                
                $("#sales_txt_box").text("$"+commaSeparator(+result[0].Revenue));
                if($('#sales_txt_box').hasClass("tooltipstered")){
                    $('#sales_txt_box').tooltipster('content', "$"+result[0].Revenue);
                }else{
                    $("#sales_txt_box").attr("title","$"+result[0].Revenue);
                }



                $("#acos_txt_box").text(result[0].ACOS+"%");
                $("#roas_txt_box").text("$"+commaSeparator(+result[0].ROAS));
                if($('#roas_txt_box').hasClass("tooltipstered")){
                    $('#roas_txt_box').tooltipster('content', "$"+result[0].ROAS);
                }else{
                    $("#roas_txt_box").attr("title","$"+result[0].ROAS);
                }
                $('.metricTooltip').tooltipster({
                    multiple: true
                });
                
            } else{
                if($('#impressions_txt_box').hasClass("tooltipstered")){
                    $("#impressions_txt_box").text("0");
                    $("#clicks_txt_box").text("0");
                    $("#ctr_txt_box").text("0%");
                    $("#cpc_txt_box").text("$0");
                    $("#conversions_txt_box").text("0");
                    $("#cpa_txt_box").text("0");
                    $("#spend_txt_box").text("$0");
                    $("#sales_txt_box").text("$0");
                    $("#acos_txt_box").text("0%");
                    $("#roas_txt_box").text("$0");

                    $('#impressions_txt_box').tooltipster('content',"0");
                    $('#cpc_txt_box').tooltipster('content',"0");
                    $('#conversions_txt_box').tooltipster('content',"0");
                    $('#spend_txt_box').tooltipster('content',"0");
                    $('#sales_txt_box').tooltipster('content',"0");
                    $('#roas_txt_box').tooltipster('content',"0");
                 } else{
                    defaultMetricValues();
                }
            }   
            hidePreLoaders(".metrics"); 
        }
    });
};

/**
 * default Metric Values
 */
function defaultMetricValues(){
    $("#impressions_txt_box").text("0");
    $("#clicks_txt_box").text("0");
    $("#ctr_txt_box").text("0%");
    $("#cpc_txt_box").text("$0");
    $("#conversions_txt_box").text("$0");
    $("#cpa_txt_box").text("0");
    $("#spend_txt_box").text("$0");
    $("#sales_txt_box").text("$0");
    $("#acos_txt_box").text("0%");
    $("#roas_txt_box").text("$0");
    if($('#impressions_txt_box').hasClass("tooltipstered")){
        $('#impressions_txt_box').tooltipster('content',"0");
        $('#cpc_txt_box').tooltipster('content',"0");
        $('#conversions_txt_box').tooltipster('content',"0");
        $('#spend_txt_box').tooltipster('content',"0");
        $('#sales_txt_box').tooltipster('content',"0");
        $('#roas_txt_box').tooltipster('content',"0");
    }
}

function helperDateFunction(date){
    var dateFormat = new Date(Date.parse(date));
    const ye = new Intl.DateTimeFormat('en', { year: 'numeric' }).format(dateFormat)
    const mo = new Intl.DateTimeFormat('en', { month: 'short' }).format(dateFormat)
    const da = new Intl.DateTimeFormat('en', { day: '2-digit' }).format(dateFormat)
    return `${mo} ${da},${ye}`;
}


/**
 * Convert 0 entries to null 
 * @param {*} arr 
 * @param {*} prop 
 */
function getConvertedResults(arr, prop) {
    let results = arr.map(function(obj){
        let dataObj = obj;
        if((+dataObj.acos_) == 0){
            dataObj.acos_="NA";
        }
        dataObj.revenue ="$"+dataObj.revenue 
        dataObj.spend = "$"+dataObj.spend
        if(dataObj.acos_ != "NA"){
            dataObj.acos_ = (+dataObj.acos_).toFixed(2)+"%"
        }


        return dataObj;
    })
    return results;
}
/**
 * Performance Percentages
 */
function performanceGrandTotal(){
    filterData.sp = "spCalculateAsinLevelPerformanceGrandTotal"
    $.ajax({
        method: "GET",
        data:filterData,
        url: dataUrl,
        // data: data,
        success: function(result){
            if(result.length!=0){
                $("#perf-reven-currency").text("$"+commaSeparator(+result[0].revenue));
                if($('#perf-reven-currency').hasClass("tooltipstered")){
                    $('#perf-reven-currency').tooltipster('content', "$"+result[0].revenue);
                }else{
                    $("#perf-reven-currency").attr("title","$"+result[0].revenue);
                }
                $('.tooltip1').tooltipster();

                $("#perf-cost-currency").text("$"+commaSeparator(result[0].cost));
                if($('#perf-cost-currency').hasClass("tooltipstered")){
                    $('#perf-cost-currency').tooltipster('content', "$"+result[0].cost);
                }else{
                    $("#perf-cost-currency").attr("title","$"+result[0].cost);
                 }
              
                $('.tooltip2').tooltipster();
                
                $("#perf-acos-currency").text(result[0].acos_+"%");
                hidePreLoaders('#performanceDiv');
            } 
            $('input[name="datefilter"]').prop( "disabled", false );
            hidePreLoaders('#performanceDiv');  
        }
    });
}

function removeClasses(id,classToRemove){
    $(id).removeClass(classToRemove);
}
function addClasses(id,classToAdd){
    $(id).addClass(classToAdd)
}

function performancePercentageCall(){
    var revenue,acos,cost; 
    filterData.sp = "spCalculateAsinLevelPreformancePrecentages";
    $.ajax({
        method: "GET",
        data:filterData,
        url: dataUrl,
        success: function(result){
            revenue = +result[0].revenue_perc;
            cost = +result[0].cost_perc;
            acos = +result[0].acos_perc;
            //Rotate and change arrow/text color for Revenue
            if(revenue < 0) {
                $('#perf-revenue-lbl').removeClass('downGreen downRed');
                $('#perf-revenue-lbl').addClass('downRed')
                
                $('#perf-reven-svg').removeClass('green-color arrow-up');
                $('#perf-reven-svg').addClass('red-color')

                $("#perf-revenue-lbl").text(revenue+"%");
            } else {
                $('#perf-revenue-lbl').removeClass('downGreen downRed');
                $('#perf-revenue-lbl').addClass('downGreen')
                
                $('#perf-reven-svg').removeClass('red-color');
                $('#perf-reven-svg').addClass('green-color arrow-up');
                
                $("#perf-revenue-lbl").text(revenue+"%");   
            }

            //Rotate and change arrow/text color for Cost
            if(cost < 0) {
                $('#perf-cost-lbl').removeClass('downGreen downRed');
                $('#perf-cost-lbl').addClass('downRed')
                
                $('#perf-cost-svg').removeClass('green-color arrow-up');
                $('#perf-cost-svg').addClass('red-color')

                $("#perf-cost-lbl").text(cost+"%");
            } else {
                $('#perf-cost-lbl').removeClass('downGreen downRed');
                $('#perf-cost-lbl').addClass('downGreen')
                
                $('#perf-cost-svg').removeClass('red-color');
                $('#perf-cost-svg').addClass('green-color arrow-up');
                
                $("#perf-cost-lbl").text(cost+"%");   
            }
        
            //Rotate and change arrow/text color for Cost
            if(acos < 0) {
                $('#perf-acos-lbl').removeClass('downGreen downRed');
                $('#perf-acos-lbl').addClass('downRed')
                
                $('#perf-acos-svg').removeClass('green-color arrow-up');
                $('#perf-acos-svg').addClass('red-color')

                $("#perf-acos-lbl").text(acos+"%");
            } else {
               
                $('#perf-acos-lbl').removeClass('downGreen downRed');
                $('#perf-acos-lbl').addClass('downGreen')
                
                $('#perf-acos-svg').removeClass('red-color');
                $('#perf-acos-svg').addClass('green-color arrow-up');
                $("#perf-acos-lbl").text(acos+"%");
            }
            //Grand Total Call
            performanceGrandTotal();
        }
      });
}

function resetFilters(){

    $(".campaignSelect").val([]);
    $(".asinSelect").empty();
    $(".asinSelect").select2("val", "");
    $('input[name="datefilter"]').val('');
    filterDataUpdate("profileId",filterData.profileId);
    filterDataUpdate("campaignId",null);
    filterDataUpdate("ASIN",null);
    filterDataUpdate("startDate",null);
    filterDataUpdate("endDate",null);
    defaultMetricValues();
    generatePerformanceGraph([],0,0);
    performanceDefaultPercentages();
    performanceDefaultGrandTotals();
    generateEfficiencyGraph([],0,0);
    efficiencyDefaultPercentages();
    efficiencyDefaultGrandTotals();

    generateAwarenessGraph([],0,0);
    awarenessDefaultPercentages();
    awarenessDefaultGrandTotals();

}

/**
 * Efficiency Percentages
 */
function efficiencyGrandTotal(){
    filterData.sp = "spCalculateAsinLevelEfficiencyGrandTotal";
    $.ajax({
        method: "GET",
        data:filterData,
        url: dataUrl,
        // data: data,
        success: function(result){
            if(result.length!=0){
                $("#effi-cpc-currency").text("$"+commaSeparator(result[0].cpc));

                if($("#effi-cpc-currency").hasClass("tooltipstered")){
                    $("#effi-cpc-currency").tooltipster('content', "$"+result[0].cpc);
                }else{
                    $("#effi-cpc-currency").attr("title","$"+result[0].cpc);
                 }
                $('.tooltip3').tooltipster();
                
                $("#effi-roas-currency").text("$"+commaSeparator(result[0].roas));
                
                if($("#effi-roas-currency").hasClass("tooltipstered")){
                    $("#effi-roas-currency").tooltipster('content', "$"+result[0].roas);
                }else{
                    $("#effi-roas-currency").attr("title","$"+result[0].roas);
                 }
                
                $('.tooltip4').tooltipster();
                
                $("#effi-cpa-currency").text("$"+commaSeparator(result[0].cpa));

                if($("#effi-cpa-currency").hasClass("tooltipstered")){
                    $("#effi-cpa-currency").tooltipster('content', "$"+result[0].cpa);
                }else{
                    $("#effi-cpa-currency").attr("title","$"+result[0].cpa);
                }


                $('.tooltip5').tooltipster();
            }   
            hidePreLoaders('#efficiencyDiv');
        }
    });
}

function efficiencyPercentageCall(){
    var cpc,roas,cpa; 
    filterData.sp = "spCalculateAsinLevelEfficiencyPrecentages";
    $.ajax({
        method: "GET",
        data:filterData,
        url: dataUrl,
        // data: data,
        success: function(result){
            cpc = +result[0].prct_cpc;
            roas = +result[0].prct_roas;
            cpa = +result[0].prct_cpa;

            //Rotate and change arrow/text color for Cost

            if(cpc < 0) {
                $('#effi-cpc-lbl').removeClass('downGreen downRed');
                $('#effi-cpc-lbl').addClass('downRed')
                
                $('#effi-cpc-svg').removeClass('green-color arrow-up');
                $('#effi-cpc-svg').addClass('red-color')

                $("#effi-cpc-lbl").text(cpc+"%");   
            } else {
                $('#effi-cpc-lbl').removeClass('downGreen downRed');
                $('#effi-cpc-lbl').addClass('downGreen')
                
                $('#effi-cpc-svg').removeClass('red-color');
                $('#effi-cpc-svg').addClass('green-color arrow-up');
                
                $("#effi-cpc-lbl").text(cpc+"%");      
            }

            //Rotate and change arrow/text color for Cost
            if(roas < 0) {
                $('#effi-roas-lbl').removeClass('downGreen downRed');
                $('#effi-roas-lbl').addClass('downRed')
                
                $('#effi-roas-svg').removeClass('green-color arrow-up');
                $('#effi-roas-svg').addClass('red-color')

                $("#effi-roas-lbl").text(roas+"%");
            } else {
                $('#effi-roas-lbl').removeClass('downGreen downRed');
                $('#effi-roas-lbl').addClass('downGreen')
                
                $('#effi-roas-svg').removeClass('red-color');
                $('#effi-roas-svg').addClass('green-color arrow-up');
                
                $("#effi-roas-lbl").text(roas+"%");   
            }
        
            //Rotate and change arrow/text color for Cost
            if(cpa < 0) {
                $('#effi-cpa-lbl').removeClass('downGreen downRed');
                $('#effi-cpa-lbl').addClass('downRed')
                
                $('#effi-cpa-svg').removeClass('green-color arrow-up');
                $('#effi-cpa-svg').addClass('red-color')

                $("#effi-cpa-lbl").text(cpa);
            } else {
               
                $('#effi-cpa-lbl').removeClass('downGreen downRed');
                $('#effi-cpa-lbl').addClass('downGreen')
                
                $('#effi-cpa-svg').removeClass('red-color');
                $('#effi-cpa-svg').addClass('green-color arrow-up');
                $("#effi-cpa-lbl").text(cpa);
            }
            //Grand Total Call
            efficiencyGrandTotal();
        }
      });
}

/**
 * Awareness Percentages
 */
function awarenessGrandTotal(){
    filterData.sp ="spPopulateAMSAwarenessAsinLevelGrandTotal";
    $.ajax({
        method: "GET",
        data:filterData,
        url: dataUrl,
        // data: data,
        success: function(result){
            if(result.length!=0){
                $("#awar-impre-currency").text(commaSeparator(+result[0].impressions));
                if($("#awar-impre-currency").hasClass("tooltipstered")){
                    $("#awar-impre-currency").tooltipster('content', result[0].impressions);
                }else{
                    $("#awar-impre-currency").attr("title",result[0].impressions);
                }
                $('.tooltip6').tooltipster();

                $("#awar-clk-currency").text(commaSeparator(+result[0].clicks));
                if($("#awar-clk-currency").hasClass("tooltipstered")){
                    $("#awar-clk-currency").tooltipster('content', result[0].clicks);
                }else{
                    $("#awar-clk-currency").attr("title",result[0].clicks);
                }

                $("#awar-ctr-currency").text(result[0].ctr+"%");
                hidePreLoaders('#awarenessDiv');
            } 
            hidePreLoaders('#awarenessDiv');  
        }
    });
}

function awarenessPercentageCall(){
    var impr,click,ctr; 
    filterData.sp ="spCalculateAMSAwarenessAsinLevelPercentage";
    $.ajax({
        method: "GET",
        data:filterData,
        url: dataUrl,
        // data: data,
        success: function(result){
            impr = +result[0].impressions_perc;
            click = +result[0].clicks_perc;
            ctr = +result[0].ctr_perc;
           
            //Rotate and change arrow/text color for Revenue
            if(impr < 0) {
                $('#awar-impre-lbl').removeClass('downGreen downRed');
                $('#awar-impre-lbl').addClass('downRed')
                
                $('#awar-impre-svg').removeClass('green-color arrow-up');
                $('#awar-impre-svg').addClass('red-color')
                $("#awar-impre-lbl").text(impr+"%");           
            } else {
                $('#awar-impre-lbl').removeClass('downGreen downRed');
                $('#awar-impre-lbl').addClass('downGreen')
                
                $('#awar-impre-svg').removeClass('red-color');
                $('#awar-impre-svg').addClass('green-color arrow-up');  
                $("#awar-impre-lbl").text(impr+"%");   
            }

            //Rotate and change arrow/text color for Cost
            if(click < 0) {

                $('#awar-clk-lbl').removeClass('downGreen downRed');
                $('#awar-clk-lbl').addClass('downRed')
                
                $('#awar-clk-svg').removeClass('green-color arrow-up');
                $('#awar-clk-svg').addClass('red-color')
                $("#awar-clk-lbl").text(click+"%");            
            } else {
              
                $('#awar-clk-lbl').removeClass('downGreen downRed');
                $('#awar-clk-lbl').addClass('downGreen')
                
                $('#awar-clk-svg').removeClass('red-color');
                $('#awar-clk-svg').addClass('green-color arrow-up');
                $("#awar-clk-lbl").text(click+"%");
            }
        
            //Rotate and change arrow/text color for Cost
            if(ctr < 0) {
                $('#awar-ctr-lbl').removeClass('downGreen downRed');
                $('#awar-ctr-lbl').addClass('downRed')
                
                $('#awar-ctr-svg').removeClass('green-color arrow-up');
                $('#awar-ctr-svg').addClass('red-color')
                $("#awar-ctr-lbl").text(ctr+"%");               
            } else {
                $('#awar-ctr-lbl').removeClass('downGreen downRed');
                $('#awar-ctr-lbl').addClass('downGreen') 
                $('#awar-ctr-svg').removeClass('red-color');
                $('#awar-ctr-svg').addClass('green-color arrow-up');
                $("#awar-ctr-lbl").text(ctr+"%");
            }
            //Grand Total Call
            awarenessGrandTotal();
        }
      });
}
/**
 * Performance Chart Functions
 */
function performanceChart(){
    var cost,rev,acos,date_key;
    filterData.sp = "spPopulateAsinPerformance";
    $.ajax({
        method: "GET",
        data:filterData,
        data:filterData,
        url: dataUrl,
        // data: data,
        success: function(result){
            cost = result.map(function(obj){
                return +obj.cost;
            });
            rev = result.map(function(obj){
                return +obj.revenue;
            });
            acos = result.map(function(obj){
                return +obj.acos;
            });
            date_key = result.map(function(obj){
                return helperDateFunction(obj.date_key);
            });

            cost.unshift("Cost");
            rev.unshift("Rev");
            acos.unshift("ACOS");

            let getY2Min = Math.min.apply(Math, rev)
            
            // generate chart
            if(rev.length >1 || acos.length >1 || cost.length >1){
                generatePerformanceGraph([rev,cost,acos],date_key,getY2Min);
            } else{
                generatePerformanceGraph([],date_key,getY2Min); 
            }

            $("#chart1").height("280px");
            $("#chart1").prev().height(($("#chart1").height()-60) + "px");
            performancePercentageCall();
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) { 
            $('input[name="datefilter"]').prop( "disabled", false );
            hidePreLoaders('#performanceDiv');
        } 
      });
}

function generatePerformanceGraph(dataChart1,category,getY2Min){
    chartPerformance = c3.generate({
        bindto: '#chart1',
        data: {
            columns:dataChart1, 
            colors: {
                Cost: '#4a47a3',
                Rev: '#21bf73',
                ACOS:'#ffc107'
            },
            types: {
                Cost: 'spline',
                ACOS: 'spline',
                Rev: 'bar'
            },
            axes: {
                Cost : 'y',
                ACOS: 'y',
                Rev: 'y2'
            },
            empty: { 
                label: { 
                    text: "No Data Available To Plot" 
                }   
            },
        },
        tooltip:{
            show: true,
            point: true,
            format: {
               value: d3.format('') // apply this format to both y and y2
            }
        },
        point: {
            focus: {
                expand: {
                enabled: true,
                }
            }
        },
        legend: {
            show: true,
            position: 'inset',
            inset: {
                anchor: 'top-left',
                x:0,
                y: -10,
                step: -5
            },

            item: {
                onclick: function(id) {
                    chartPerformance.toggle(id);
                    setTimeout(() => {
                        chartPerformance.axis.labels({
                            y: $(".c3-legend-item-Rev").hasClass("c3-legend-item-hidden") ? 'Cost' 
                            :$(".c3-legend-item-Cost").hasClass("c3-legend-item-hidden") ?  'ACOS': 'Cost/ACOS',
                            y2: $(".c3-legend-item-Rev").hasClass("c3-legend-item-hidden") ? 'ACOS' : 'Rev',
                       });
                       chartPerformance.data.axes({
                            ACOS: $(".c3-legend-item-Rev").hasClass("c3-legend-item-hidden") ? 'y2' 
                            :$(".c3-legend-item-Cost").hasClass("c3-legend-item-hidden") ?  'y':'y'
                      });
                    }, 100);
                }
            }
        },
        size: {
          height: "90%"
        },
        grid: {
            x: {show: true},
            y: {show: true}
        },
           bar: {
                width: {
                    ratio: 0.4 // this makes bar width 50% of length between ticks
                }
            },
        axis: {
            x: {    
                type: 'category',
                categories: category,
                label:{
                    position: 'middle'
                }, 
                tick: {
                    rotate: -45,
                    multiline: false,
                    culling: {
                        max: 20 // the number of tick texts will be adjusted to less than this value
                    },
                },    
            },
            y: {
                show: true,
                label: {
                    text: 'Cost /ACOS',
                    position: 'outer-middle'
                },
                tick:{
                    format: d3.format(".2s")
                },
                // min:0,
                padding: {top: 30,
                    bottom: 30
                    }
            },
            y2: {
                show: true,
                label: {
                    text: 'Rev',
                    position: 'outer-middle'
                },
                tick:{
                    format: getY2Min>1?d3.format(".2s"):d3.format("")
                },
                // min:0, 
                padding: {top: 30,
                    bottom: 30
                    }
           
            }
        }
        });

        if(dataChart1.length == 0 ){
            var element = document.createElement('div');
            element.setAttribute('class', 'message');
            element.innerText = 'No data available';
            chartPerformance.element.appendChild(element)
        }
}

/**
 * Efficiency Chart Functions
 */
function efficiencyChart(){
    var roas,cpa,cpc,date_Ekey;
    filterData.sp = "spPopulateAsinLevelEfficiency";
    $.ajax({
        method: "GET",
        data:filterData,
        url: dataUrl,
        success: function(result){
                roas = result.map(function(obj){
                    return +obj.roas;
                });
                cpa = result.map(function(obj){
                    return +obj.cpa;
                });
                cpc = result.map(function(obj){
                    return +obj.cpc;
                });
                date_Ekey = result.map(function(obj){
                    return helperDateFunction(obj.date_key);
                });
                
                roas.unshift("ROAS");
                cpa.unshift("CPA");
                cpc.unshift("CPC");
                
                // generate chart
                if(roas.length >1 || cpa.length >1 || cpc.length >1){
                    generateEfficiencyGraph([roas,cpa,cpc],date_Ekey);
                } else{
                    generateEfficiencyGraph([],date_Ekey);
                }
                $("#chart2").height("290px");
                $("#chart2").prev().height("290px");
            efficiencyPercentageCall();
        }
      });
}

function generateEfficiencyGraph(dataChart2,category){
    chartEfficiency = c3.generate({
        bindto: '#chart2',
        data: {
            empty: {
                label: {
                  text: "No Data"
                }
              },
            columns: dataChart2,
            colors: {
                ROAS: '#ce93d8',
                CPA: '#ffc107',
                CPC:'#000000'
            },
            types: {
                ROAS: 'bar',
                CPA: 'spline',
                CPC: 'spline'
            },
            axes: {
                CPA : 'y',
                CPC: 'y',
                ROAS: 'y2'
            },
        },
        tooltip:{
            show: true,
            point: true,
            format: {
               value: d3.format('') // apply this format to both y and y2
            }
        },
        point: {
            focus: {
                expand: {
                enabled: true,
                }
            }
        },
        legend: {
            item: {
                onclick: function(id) {
                    
                    chartEfficiency.toggle(id);
                    setTimeout(() => {
                        chartEfficiency.axis.labels({
                            y: $(".c3-legend-item-ROAS").hasClass("c3-legend-item-hidden") ? 'CPA' 
                            :$(".c3-legend-item-CPA").hasClass("c3-legend-item-hidden") ?  'CPC':'CPA/CPC',
                            y2: $(".c3-legend-item-ROAS").hasClass("c3-legend-item-hidden") ? 'CPC' : 'ROAS'
                        });

                        chartEfficiency.data.axes({
                            CPC:$(".c3-legend-item-ROAS").hasClass("c3-legend-item-hidden") ? 'y2' 
                            :$(".c3-legend-item-CPA").hasClass("c3-legend-item-hidden") ?  'y':'y'
                      });
                        
                    }, 100);
                }
            },
            show: true,
            position: 'inset',
            inset: {
                anchor: 'top-left',
                x:0,
                y: -10,
                step: -5
            }
        },
        size: {
          height: "100%"
        },
        grid: {
            x: {show: true},
            y: {show: true}
        },
           bar: {
                width: {
                    ratio: 0.4 // this makes bar width 50% of length between ticks
                }
            },
        axis: {
            x: {    
                type: 'category',
                categories: category,
                label:{
                    position: 'middle'
                }, 
                tick: {
                    rotate: -45,
                    multiline: false,
                    culling: {
                        max: 20 // the number of tick texts will be adjusted to less than this value
                    },
                },    
            },
            y: {
                label: {
                    text: "CPA /CPC",
                    position: 'outer-middle'
                },
                tick:{
                    format: d3.format(".2s")
                },
                // min : 0,
                padding: {top: 50,
                    bottom: 50
                    }
            },
            y2: {
                show: true,
                label: {
                    text: "ROAS",
                    position: 'outer-middle'
                },
                tick:{
                    format: d3.format(".2s")
                },
                // min:0,
                padding: {top: 50,
                    bottom: 50
                    }
            }
        }
        });

        if(dataChart2.length == 0 ){
            var element = document.createElement('div');
            element.setAttribute('class', 'message');
            element.innerText = 'No data available';
            chartEfficiency.element.appendChild(element)
        }
}

/**
 * Awareness Chart Functions
 */
function awarenessChart(){
    var impr,ctr,clicks,date_Akey;
    filterData.sp = "spPopulateAMSAwarenessAsinLevel";
    $.ajax({
        method: "GET",
        data:filterData,
        url: dataUrl,
        success: function(result){
                impr = result.map(function(obj){
                    return +obj.impressions;
                });
                ctr = result.map(function(obj){
                    return +obj.CTR;
                });
                clicks = result.map(function(obj){
                    return +obj.clicks;
                });
                date_Akey = result.map(function(obj){
                    return helperDateFunction(obj.date_key);
                });

                impr.unshift("Impressions");
                ctr.unshift("CTR");
                clicks.unshift("Clicks");

                // generate chart
                if(impr.length >1 || ctr.length >1 || clicks.length >1){
                    generateAwarenessGraph([impr,ctr,clicks],date_Akey);
                } else{
                    generateAwarenessGraph([],date_Akey);
                }
                $("#chart3").height("290px");
                $("#chart3").prev().height("290px");
               
             // populate percentage and currency
             awarenessPercentageCall();
        }
      });
}

function generateAwarenessGraph(dataChart3,category){
    chartAwareness = c3.generate({
        bindto: '#chart3',
        data: {
            columns: dataChart3,
            colors: {
                Impressions: '#08bdda',
                CTR: '#059656',
                Clicks:'#6a1b9a'
            },
            types: {
                Impressions: 'bar',
                CTR: 'spline',
                Clicks: 'spline'
            },
            axes: {
                Impressions : 'y',
                Clicks: 'y',
                CTR: 'y2'
            },
        },
        tooltip:{
            show: true,
            point: true,
            format: {
               value: d3.format('') // apply this format to both y and y2
            }
        },
        point: {
            focus: {
                expand: {
                enabled: true,
                }
            }
        },
        legend: {
            show: true,
            position: 'inset',
            inset: {
                anchor: 'top-left',
                x:0,
                y: -10,
                step: -5
            },
            item: {
                onclick: function(id) {
                    chartAwareness.toggle(id);
                    setTimeout(() => {
                        chartAwareness.axis.labels({
                            y: $(".c3-legend-item-CTR").hasClass("c3-legend-item-hidden") ? 'Impressions' 
                             :$(".c3-legend-item-Impressions").hasClass("c3-legend-item-hidden") ?  'Clicks':'Impressions /Clicks',
                            y2: $(".c3-legend-item-CTR").hasClass("c3-legend-item-hidden") ? 'Clicks' : 'CTR'
                        });

                        chartAwareness.data.axes({
                            Clicks: $(".c3-legend-item-CTR").hasClass("c3-legend-item-hidden") ? 'y2' 
                            :$(".c3-legend-item-Impressions").hasClass("c3-legend-item-hidden") ?  'y':'y'
                            
                      });
                    }, 100);
                }
            },
        },
        size: {
            height: "100%"
          },
        grid: {
            x: {show: true},
            y: {show: true}
        },
           bar: {
                width: {
                    ratio: 0.3 // this makes bar width 50% of length between ticks
                }
            },
        axis: {
            x: {    
                type: 'category',
                categories: category,
                label:{
                    position: 'middle'
                }, 
                tick: {
                    rotate: -45,
                    multiline: false,
                    culling: {
                        max: 20 // the number of tick texts will be adjusted to less than this value
                    },
                },    
            },
            y: {
                label: {
                    text: 'Impressions /Clicks',
                    position: 'outer-middle'
                },
                tick: {
                    values:  dataChart3.CTR,
                    width: 0,
                    format: d3.format(".2s")
                    
                },
                // min : 0,
                padding: {
                    top: 30,
                    bottom: 30
                          }
            },
            y2: {
                show: true,
                label: {
                    text: 'CTR.',
                    position: 'outer-middle'
                },
                tick:{
                    format: d3.format(".2s")
                },
                // min:0,
                padding: {
                    top: 30,
                    bottom: 30
                          }
            }
        }
        });

    if(dataChart3.length == 0 ){
        var element = document.createElement('div');
        element.setAttribute('class', 'message');
        element.innerText = 'No data available';
        chartAwareness.element.appendChild(element)
    }
}

function selectCampaign(){
    $('.campaignSelect').on('select2:opening', function (e) {
        setTimeout(() => {
            select2Li = $(".select2-results .select2-results__options .select2-results__option");
            defaultOptions = $("#select-campaign option");
            $.each(select2Li, function (indexInArray, valueOfElement) {
                titleContent = $(defaultOptions[indexInArray]).attr("data-title");
                if (typeof titleContent != "undefined") {
                    $(valueOfElement).attr("title", titleContent);
                    $(valueOfElement).tooltipster({});
                }
            });
        }, 100);
    });

    $('.campaignSelect').on('select2:select', function (e) {
        let values =$(this).val();
        if(values.includes("All")){
            selectedCampaigns="All";
            $(".campaignSelect option").not(':first-child').each(function (index) {
             $(this).prop('disabled', true);
             $('.campaignSelect').val('All').trigger('opening'); 
             $('.campaignSelect').val('All').trigger('change'); 
            $('.campaignSelect').select2({
                placeholder: "Select Campaigns",
                closeOnSelect: false,
                allowClear: true,
                width: '100%',
                height: '40px'
            });
            $('.select2-selection').css("overflow-y","hidden");
          });            
        } else{
            $(".campaignSelect option").each(function (index) {
                $(this).prop('disabled', false);
             }); 
        }
        if(values.includes("All")){
            if(values.length<2){
                $('.select2-selection').css("overflow-y","hidden");
            }
            callAsinSp(selectedCampaigns);
        } else if(values.length<2){
            $('.select2-selection').css("overflow-y","hidden");
        } else{
            $('.select2-selection').css("overflow-y","auto");
        }
    });
    $('.campaignSelect').on('select2:unselecting', function (e) {
        $(".campaignSelect option").not(':first-child').each(function (index) {
            $(this).prop('disabled', false);
        });
    });
    
    $('.campaignSelect').on('select2:clearing', function (e) {
       $(".campaignSelect option").not(':first-child').each(function (index) {
        $(this).prop('disabled', false);
      });
        filterDataUpdate("campaignId",null)       
    });

    $('.campaignSelect').on('select2:close', function (e) {
        selectedCampaigns = $(this).val();
        if(selectedCampaigns.includes("All")) {
            selectedCampaigns="All";
        }
        callAsinSp(selectedCampaigns);
      });
}

function selectAsin(){
    $.ajax({
        type: "get",
        url: $("#select-campaign").attr("asin-url"),
        data: {
            profileId: filterData.profileId,
            campaignId: filterData.campaignId
        },
        success: function (response) {
            filterDataUpdate("ASIN",null);
            $asinData = '<option value="" selected>Select ASIN</option>';
            $.each(response.asins, function (indexInArray, valueOfElement) { 
                $asinData += '<option value="' + valueOfElement.asin + '">'+(valueOfElement.asin)+'</option>';
            });
            $(".asinSelect").html($asinData);
        }
    });
   
}

function showPreLoaders(id){    
    $(id).LoadingOverlay("show", {
        size: 7,
        background  : "rgba(255, 255, 255, 0.95)"
    });
}

function hidePreLoaders(id){
        $(id+".preLoader").LoadingOverlay("hide", true);
  }

/**
 * Refresh the content of the each class and based on its 
 * parent id show corresponding preloader 
 * 
 * @param {css class name attribute} className 
 */
function refreshContent(className){
    if(filterData.startDate && filterData.endDate && filterData.campaignId && filterData.profileId && filterData.ASIN){
        $(className).on("click", function () {
            id= $(this).attr('loader-id');
            showPreLoaders("#"+id);
            fn=$(this).attr('name');
            eval(fn);
        });
    }
}

/**
 * Number Comma Separator
 */
function commaSeparator(x){
    let formatComma = d3.format(","),formatSuffixDecimal2 = d3.format(".5s");
    if(x>10000){
        return formatSuffixDecimal2(x);
    } else{
        return formatComma(x);
    }
}

function performanceDefaultPercentages(){
    let revenue = 0;
    let cost = 0;
    let acos = 0;
    if(revenue < 0) {
        $('#perf-revenue-lbl').removeClass('downGreen downRed');
        $('#perf-revenue-lbl').addClass('downRed')
        
        $('#perf-reven-svg').removeClass('green-color arrow-up');
        $('#perf-reven-svg').addClass('red-color')

        $("#perf-revenue-lbl").text(revenue+"%");
    } else {
        $('#perf-revenue-lbl').removeClass('downGreen downRed');
        $('#perf-revenue-lbl').addClass('downGreen')
        
        $('#perf-reven-svg').removeClass('red-color');
        $('#perf-reven-svg').addClass('green-color arrow-up');
        
        $("#perf-revenue-lbl").text(revenue+"%");   
    }

    //Rotate and change arrow/text color for Cost
    if(cost < 0) {
        $('#perf-cost-lbl').removeClass('downGreen downRed');
        $('#perf-cost-lbl').addClass('downRed')
        
        $('#perf-cost-svg').removeClass('green-color arrow-up');
        $('#perf-cost-svg').addClass('red-color')

        $("#perf-cost-lbl").text(cost+"%");
    } else {
        $('#perf-cost-lbl').removeClass('downGreen downRed');
        $('#perf-cost-lbl').addClass('downGreen')
        
        $('#perf-cost-svg').removeClass('red-color');
        $('#perf-cost-svg').addClass('green-color arrow-up');
        
        $("#perf-cost-lbl").text(cost+"%");   
    }

    //Rotate and change arrow/text color for Cost
    if(acos < 0) {
        $('#perf-acos-lbl').removeClass('downGreen downRed');
        $('#perf-acos-lbl').addClass('downRed')
        
        $('#perf-acos-svg').removeClass('green-color arrow-up');
        $('#perf-acos-svg').addClass('red-color')

        $("#perf-acos-lbl").text(acos+"%");
    } else {
       
        $('#perf-acos-lbl').removeClass('downGreen downRed');
        $('#perf-acos-lbl').addClass('downGreen')
        
        $('#perf-acos-svg').removeClass('red-color');
        $('#perf-acos-svg').addClass('green-color arrow-up');
        $("#perf-acos-lbl").text(acos+"%");
    }
}
function performanceDefaultGrandTotals(){
    let revenue=0,cost=0,acos_=0;
    $("#perf-reven-currency").text("$"+commaSeparator(+revenue));
    if($('#perf-reven-currency').hasClass("tooltipstered")){
        $('#perf-reven-currency').tooltipster('content', "$"+revenue);
    }else{
        $("#perf-reven-currency").attr("title","$"+revenue);
    }
    $('.tooltip1').tooltipster();

    $("#perf-cost-currency").text("$"+commaSeparator(cost));
    if($('#perf-cost-currency').hasClass("tooltipstered")){
        $('#perf-cost-currency').tooltipster('content', "$"+cost);
    }else{
        $("#perf-cost-currency").attr("title","$"+cost);
     }
  
    $('.tooltip2').tooltipster();
    
    $("#perf-acos-currency").text(acos_+"%");
}
function efficiencyDefaultPercentages(){
    let roas=0,cpc=0,cpa = 0;
    if(cpc < 0) {
        $('#effi-cpc-lbl').removeClass('downGreen downRed');
        $('#effi-cpc-lbl').addClass('downRed')
        
        $('#effi-cpc-svg').removeClass('green-color arrow-up');
        $('#effi-cpc-svg').addClass('red-color')

        $("#effi-cpc-lbl").text(cpc+"%");   
    } else {
        $('#effi-cpc-lbl').removeClass('downGreen downRed');
        $('#effi-cpc-lbl').addClass('downGreen')
        
        $('#effi-cpc-svg').removeClass('red-color');
        $('#effi-cpc-svg').addClass('green-color arrow-up');
        
        $("#effi-cpc-lbl").text(cpc+"%");      
    }

    //Rotate and change arrow/text color for Cost
    if(roas < 0) {
        $('#effi-roas-lbl').removeClass('downGreen downRed');
        $('#effi-roas-lbl').addClass('downRed')
        
        $('#effi-roas-svg').removeClass('green-color arrow-up');
        $('#effi-roas-svg').addClass('red-color')

        $("#effi-roas-lbl").text(roas+"%");
    } else {
        $('#effi-roas-lbl').removeClass('downGreen downRed');
        $('#effi-roas-lbl').addClass('downGreen')
        
        $('#effi-roas-svg').removeClass('red-color');
        $('#effi-roas-svg').addClass('green-color arrow-up');
        
        $("#effi-roas-lbl").text(roas+"%");   
    }

    //Rotate and change arrow/text color for Cost
    if(cpa < 0) {
        $('#effi-cpa-lbl').removeClass('downGreen downRed');
        $('#effi-cpa-lbl').addClass('downRed')
        
        $('#effi-cpa-svg').removeClass('green-color arrow-up');
        $('#effi-cpa-svg').addClass('red-color')

        $("#effi-cpa-lbl").text(cpa);
    } else {
       
        $('#effi-cpa-lbl').removeClass('downGreen downRed');
        $('#effi-cpa-lbl').addClass('downGreen')
        
        $('#effi-cpa-svg').removeClass('red-color');
        $('#effi-cpa-svg').addClass('green-color arrow-up');
        $("#effi-cpa-lbl").text(cpa);
    }
}
function efficiencyDefaultGrandTotals(){
    let cpc=0,roas=0,cpa=0;
    $("#effi-cpc-currency").text("$"+commaSeparator(cpc));

    if($("#effi-cpc-currency").hasClass("tooltipstered")){
        $("#effi-cpc-currency").tooltipster('content', "$"+cpc);
    }else{
        $("#effi-cpc-currency").attr("title","$"+cpc);
     }
    $('.tooltip3').tooltipster();
    
    $("#effi-roas-currency").text("$"+commaSeparator(roas));
    
    if($("#effi-roas-currency").hasClass("tooltipstered")){
        $("#effi-roas-currency").tooltipster('content', "$"+roas);
    }else{
        $("#effi-roas-currency").attr("title","$"+roas);
     }
    
    $('.tooltip4').tooltipster();
    
    $("#effi-cpa-currency").text("$"+commaSeparator(cpa));

    if($("#effi-cpa-currency").hasClass("tooltipstered")){
        $("#effi-cpa-currency").tooltipster('content', "$"+cpa);
    }else{
        $("#effi-cpa-currency").attr("title","$"+cpa);
    }


    $('.tooltip5').tooltipster();
}
function awarenessDefaultPercentages(){
    let impr=0,click=0,ctr=0;
     //Rotate and change arrow/text color for Revenue
     if(impr < 0) {
        $('#awar-impre-lbl').removeClass('downGreen downRed');
        $('#awar-impre-lbl').addClass('downRed')
        
        $('#awar-impre-svg').removeClass('green-color arrow-up');
        $('#awar-impre-svg').addClass('red-color')
        $("#awar-impre-lbl").text(impr+"%");           
    } else {
        $('#awar-impre-lbl').removeClass('downGreen downRed');
        $('#awar-impre-lbl').addClass('downGreen')
        
        $('#awar-impre-svg').removeClass('red-color');
        $('#awar-impre-svg').addClass('green-color arrow-up');  
        $("#awar-impre-lbl").text(impr+"%");   
    }

    //Rotate and change arrow/text color for Cost
    if(click < 0) {

        $('#awar-clk-lbl').removeClass('downGreen downRed');
        $('#awar-clk-lbl').addClass('downRed')
        
        $('#awar-clk-svg').removeClass('green-color arrow-up');
        $('#awar-clk-svg').addClass('red-color')
        $("#awar-clk-lbl").text(click+"%");            
    } else {
      
        $('#awar-clk-lbl').removeClass('downGreen downRed');
        $('#awar-clk-lbl').addClass('downGreen')
        
        $('#awar-clk-svg').removeClass('red-color');
        $('#awar-clk-svg').addClass('green-color arrow-up');
        $("#awar-clk-lbl").text(click+"%");
    }

    //Rotate and change arrow/text color for Cost
    if(ctr < 0) {
        $('#awar-ctr-lbl').removeClass('downGreen downRed');
        $('#awar-ctr-lbl').addClass('downRed')
        
        $('#awar-ctr-svg').removeClass('green-color arrow-up');
        $('#awar-ctr-svg').addClass('red-color')
        $("#awar-ctr-lbl").text(ctr+"%");               
    } else {
        $('#awar-ctr-lbl').removeClass('downGreen downRed');
        $('#awar-ctr-lbl').addClass('downGreen') 
        $('#awar-ctr-svg').removeClass('red-color');
        $('#awar-ctr-svg').addClass('green-color arrow-up');
        $("#awar-ctr-lbl").text(ctr+"%");
    }
}
function awarenessDefaultGrandTotals(){
    let impressions=0,clicks=0,ctr=0;
    $("#awar-impre-currency").text(commaSeparator(impressions));
    if($("#awar-impre-currency").hasClass("tooltipstered")){
        $("#awar-impre-currency").tooltipster('content', impressions);
    }else{
        $("#awar-impre-currency").attr("title",impressions);
    }
    $('.tooltip6').tooltipster();

    $("#awar-clk-currency").text(commaSeparator(clicks));
    if($("#awar-clk-currency").hasClass("tooltipstered")){
        $("#awar-clk-currency").tooltipster('content', clicks);
    }else{
        $("#awar-clk-currency").attr("title",clicks);
    }

    $("#awar-ctr-currency").text(ctr+"%");
    hidePreLoaders('#awarenessDiv');
}

function callAsinSp(selectedCampaigns){
    filterData.campaignId = selectedCampaigns.toString();
    let localfilterData = JSON.parse(localStorage.getItem("asinfilterData"));
    
    if(localfilterData){
        filterData.profileId = localfilterData.profileId;
    }
    selectAsin();
}