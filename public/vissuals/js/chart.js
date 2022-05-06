
var baseUrl;
var performanceDataUrl;
var efficiencyDataUrl;
var awarenessDataUrl;
var momDataUrl;
var wowDataUrl;
var dodDataUrl;
var adTypeDataUrl;
var productTypeDataUrl;

var stragtegyTypeDataUrl;
var customTypeDataUrl;
  
var AMSScoreCards_DataUrl;
var pref_days_DataUrl;
var pref_ytd_DataUrl;
var top_campaigns_DataUrl;
var perf_percentage_DataUrl;
var perf_Grand_DataUrl;
var effi_percentage_DataUrl;
var effi_Grand_DataUrl;
var awar_percentage_DataUrl;
var awar_Grand_DataUrl;
var MOM_percentage_DataUrl;
var Wow_percentage_DataUrl;
var DOD_percentage_DataUrl;
var ytd_percentage_DataUrl;
var wtd_percentage_DataUrl;
var ytdDataUrl;
var wtdDataUrl;
var dateRangePicker;
//charts variables
var chartPerformance,chartEfficiency,chartAwareness;
var filterData = {
    profileId:null,
    campaignId:null,
    startDate:null,
    endDate:null,
    TopXcampaign: null
};

var strategyType = productType = null;

$(function () {
    baseUrl = $("body").attr("base_url")+'/public';

    /**Custom type, Stretagy Type and Product Type routes */
     customTypeDataUrl = baseUrl+"/manager/visuals/spCalculateCustomCampTagingVisual";
    
     stragtegyTypeDataUrl = baseUrl+"/manager/visuals/spCalculateStragTypeCampTagingVisual";
     
     productTypeDataUrl = baseUrl+"/manager/visuals/spCalculateProdTypeCampTagingVisual";
    /**Custom type, Stretagy Type and Product Type routes*/


    performanceDataUrl = baseUrl+"/vissuals/latestJson/spPopulateCampaignPerformance.json";
    performanceDataUrl = baseUrl+"/manager/visuals/spPopulateCampaignPerformance";

    efficiencyDataUrl = baseUrl+"/vissuals/latestJson/spPopulateCampaignEfficiency.json";
    efficiencyDataUrl = baseUrl+"/manager/visuals/spPopulateCampaignEfficiency";
    
    awarenessDataUrl = baseUrl+"/vissuals/latestJson/spPopulateCampaignAwareness.json";
    awarenessDataUrl = baseUrl+"/manager/visuals/spPopulateCampaignAwareness";
    
    momDataUrl = baseUrl+"/vissuals/latestJson/spPopulateCampaignMTD.json";
    momDataUrl = baseUrl+"/manager/visuals/spPopulateCampaignMTD";
    
    wowDataUrl = baseUrl+"/vissuals/latestJson/spPopulatePresentationWowTable.json";
    wowDataUrl = baseUrl+"/manager/visuals/spPopulatePresentationWowTable";
    
    dodDataUrl = baseUrl+"/vissuals/latestJson/spPopulatePresentationDODTable.json";
    dodDataUrl = baseUrl+"/manager/visuals/spPopulatePresentationDODTable";
    
    ytdDataUrl = "./latestJson/spPopulatePresentationCpgYTDTable.json";
    ytdDataUrl = baseUrl+"/manager/visuals/spPopulatePresentationCpgYTDTable";

    wtdDataUrl = "./latestJson/spPopulatePresentationWTDTable.json";
    wtdDataUrl = baseUrl + "/manager/visuals/spPopulatePresentationWTDTable";
    
    adTypeDataUrl = baseUrl+"/vissuals/latestJson/spPopulatePresentationAdType.json";
    adTypeDataUrl = baseUrl+"/manager/visuals/spPopulatePresentationAdType";
    
    AMSScoreCards_DataUrl = baseUrl+"/vissuals/latestJson/spCalculateAMSScoreCards.json";
    AMSScoreCards_DataUrl = baseUrl+"/manager/visuals/spCalculateAMSScoreCards";
    prod_type_DataUrl = baseUrl+"/vissuals/latestJson/spPopulatePresentationAdType.json";
    prod_type_DataUrl = baseUrl+"/manager/visuals/spPopulatePresentationAdType";
    
    pref_days_DataUrl = baseUrl+"/vissuals/latestJson/spPerformancePre30Day.json"
    pref_days_DataUrl = baseUrl+"/manager/visuals/spPerformancePre30Day";
    
    pref_ytd_DataUrl = baseUrl+"/vissuals/latestJson/spPerformanceytd.json";
    pref_ytd_DataUrl = baseUrl+"/manager/visuals/spPerformanceytd";
    
    top_campaigns_DataUrl = baseUrl+"/vissuals/latestJson/spPopulatePresentationTopCampiagnTable.json";
    top_campaigns_DataUrl = baseUrl+"/manager/visuals/spPopulatePresentationTopCampiagnTable";

    /**
     * Percentage's Url
     */
    perf_percentage_DataUrl = baseUrl+"/vissuals/latestJson/spCalculatePreformancePrecentages.json";
    perf_percentage_DataUrl = baseUrl+"/manager/visuals/spCalculatePreformancePrecentages";
    
    perf_Grand_DataUrl = baseUrl+"/vissuals/latestJson/spCalculateCampaignPerformanceGrandTotal.json";
    perf_Grand_DataUrl = baseUrl+"/manager/visuals/spCalculateCampaignPerformanceGrandTotal";

    /**
     * Efficiency's Url
     */
    effi_percentage_DataUrl = baseUrl+"/vissuals/latestJson/spCalculateEfficiencyPrecentages.json";
    effi_percentage_DataUrl = baseUrl+"/manager/visuals/spCalculateEfficiencyPrecentages";
    
    effi_Grand_DataUrl = baseUrl+"/vissuals/latestJson/spCalculateCampaignEfficiencyGrandTotal.json";
    effi_Grand_DataUrl = baseUrl+"/manager/visuals/spCalculateCampaignEfficiencyGrandTotal";
    /**
     * Awareness's Url
     */
    awar_percentage_DataUrl = baseUrl+"/vissuals/latestJson/spCalculateAwarenessPrecentages.json";
    awar_percentage_DataUrl = baseUrl+"/manager/visuals/spCalculateAwarenessPrecentages";
    
    awar_Grand_DataUrl = baseUrl+"/vissuals/latestJson/spCalculateCampaignAwarenessGrandTotal.json";
    awar_Grand_DataUrl = baseUrl+"/manager/visuals/spCalculateCampaignAwarenessGrandTotal";
    /**
     * MOM WOW DOD
     */
    MOM_percentage_DataUrl = baseUrl+"/vissuals/latestJson/spCalculateMTDPercentages.json";
    MOM_percentage_DataUrl = baseUrl+"/manager/visuals/spCalculateMTDPercentages";
    
    Wow_percentage_DataUrl = baseUrl+"/vissuals/latestJson/spCalculateWowPercentages.json";
    Wow_percentage_DataUrl = baseUrl+"/manager/visuals/spCalculateWowPercentages";
    
    DOD_percentage_DataUrl = baseUrl+"/vissuals/latestJson/spCalculateDODPrecentages.json";
    DOD_percentage_DataUrl = baseUrl+"/manager/visuals/spCalculateDODPrecentages";
    
    ytd_percentage_DataUrl = "./latestJson/spCalculateYTDPercentages.json";
    ytd_percentage_DataUrl = baseUrl+"/manager/visuals/spCalculateYTDPercentages";
    wtd_percentage_DataUrl = "./latestJson/spCalculateWTDPercentages.json";
    wtd_percentage_DataUrl = baseUrl+"/manager/visuals/spCalculateWTDPercentages";

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

    let localfilterData = JSON.parse(localStorage.getItem("filterData"));
   
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


        if (filterData.startDate && filterData.endDate && filterData.profileId){
            $(".topXcampaignSelect").change();
        }
        
        if(filterData.startDate && filterData.endDate && filterData.campaignId && filterData.profileId){
            $("#sectionA").removeClass("defaultHide");
            $('input[name="datefilter"]').prop( "disabled", true );
            showPreLoaders(".preLoader");
            getMetricsScoreCards();
            performanceChart();
            efficiencyChart();
            awarenessChart();
            populateMOM();
            populateWOW();
            populateDOD();
            // Ytd Call
            populateYTD()
            populateWTD()
            if(adtype_dataTable){
                adtype_dataTable.destroy();
            }
            Ad_type_DataCall();
            if(strategy_dataTable){
                strategy_dataTable.destroy();
            }
 
            Strategy_DataCall();
            if(target_dataTable){
                target_dataTable.destroy();
            }
            custom_DataCall();
            if(product_dataTable){
                product_dataTable.destroy();
            }
            prodType_DataCall();
            if(perf_dataTable){
                perf_dataTable.destroy();
            }
            perf_Pre30_DataCall();
            if(perf_ytd_dataTable){
                perf_ytd_dataTable.destroy();
            }
            perf_ytd_DataCall();
            hidePreLoaders('#topTen_table_Div');
            refreshContent(".reloadIcon");
            refreshContent(".reloadIcon-sm");
            
        }//end if
        $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
    });
    
    dateRangePicker.on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
        filterData.startDate = "";
        filterData.endDate = "";
    });
    $(".campaignSelect").select2({
        placeholder: "Select Campaigns",
        closeOnSelect: false,
        allowClear: true,
        // width: '100%',
        height: '40px'
       })
    $(".profileSelect").on("change", function () {
        filterData.profileId = $(this).val();
        if(filterData.profileId == ""){
            filterData.profileId = null
        }
        resetFilters();
        filterDataUpdate("profileId",filterData.profileId);
        filterDataUpdate("campaignId",null);
        if (filterData.startDate && filterData.endDate && filterData.profileId){
            filterData.startDate = moment(filterData.startDate).format('YYYYMMDD')
            filterData.endDate = moment(filterData.endDate).format('YYYYMMDD')
            
            $(".topXcampaignSelect").change();
        }
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
                    $campaignsOption += '<option data-title="' + valueOfElement.name + '" value="' + valueOfElement.campaignId + '">'+(valueOfElement.name.length>11?valueOfElement.name.substr(0,11)+"...":valueOfElement.name)+'</option>';
                });
                $(".campaignSelect").html($campaignsOption);
                $productTypeOption = '<option value="" selected>Product Type</option>';
                $.each(response.productType, function (indexInArray, valueOfElement) { 
                    $productTypeOption += '<option value="' + valueOfElement.fkTagId + '">'+(valueOfElement.tag)+'</option>';
                });
                $(".productSelect").html($productTypeOption)
                $stretagyTypeOption = '<option value="" selected>Strategy Type</option>';
                $.each(response.stretagyType, function (indexInArray, valueOfElement) { 
                    $stretagyTypeOption += '<option value="' + valueOfElement.fkTagId + '">'+(valueOfElement.tag)+'</option>';
                });
                $(".strategySelect").html($stretagyTypeOption)
            }//end success
        });//end ajax function
    });//end change function

    $(".productSelect, .strategySelect").on("change", function (e) {
        let fkTagId = $(this).val();
        if($(this).hasClass("productSelect"))
        {
            productType = 1;
        }
        else
        {
            strategyType = 2;
        }
        $.ajax({
            type: "get",
            url: $(this).attr("tag-campaigns-url"),
            data: {
                fkTagId: fkTagId,
                strategyType: strategyType,
                productType: productType,
                profileId: filterData.profileId
            },
            success: function (response) {
                $campaignsOption = [];
                if(response.campaigns.length >0){
                    $campaignsOption = '<option data-title="Select All" value="All">Select All</option>';
                }

                $.each(response.campaigns, function (indexInArray, valueOfElement) { 
                    $campaignsOption += '<option data-title="' + valueOfElement.name + '" value="' + valueOfElement.campaignId + '">'+(valueOfElement.name.length>11?valueOfElement.name.substr(0,11)+"...":valueOfElement.name)+'</option>';
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
    selectCampaign();
    $(".topXcampaignSelect").on("change", function () {
        filterData.TopXcampaign = $(this).val();
        if(top_campaigns_dataTable){
            top_campaigns_dataTable.destroy();
        }
        $('input[name="datefilter"]').attr('disabled','disabled');
        showPreLoaders('#topTen_table_Div');
        top_campaigns_DataCall();
        $('input[name="datefilter"]').removeAttr('disabled');
        
    });

    if(brand !== ""){
        if(localStorage.getItem("filterData")){
            let localfilterData = JSON.parse(localStorage.getItem("filterData"));
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
                            $campaignsOption += '<option data-title="' + valueOfElement.name + '" value="' + valueOfElement.campaignId + '">'+(valueOfElement.name.length>11?valueOfElement.name.substr(0,11)+"...":valueOfElement.name)+'</option>';
                        });
                        $(".campaignSelect").html($campaignsOption)
                        

                        $productTypeOption = '<option value="" selected>Product Type</option>';
                        $.each(response.productType, function (indexInArray, valueOfElement) { 
                            $productTypeOption += '<option value="' + valueOfElement.fkTagId + '">'+(valueOfElement.tag)+'</option>';
                        });
                        $(".productSelect").html($productTypeOption)
                        $stretagyTypeOption = '<option value="" selected>Strategy Type</option>';
                        $.each(response.stretagyType, function (indexInArray, valueOfElement) { 
                            $stretagyTypeOption += '<option value="' + valueOfElement.fkTagId + '">'+(valueOfElement.tag)+'</option>';
                        });
                        $(".strategySelect").html($stretagyTypeOption)
                        
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
    
                        }
                    }//end success
                });//end ajax function
            }

            if(localfilterData.profileId && localfilterData.startDate && localfilterData.endDate){
                localfilterData.startDate = moment(localfilterData.startDate).format('YYYYMMDD');
                localfilterData.endDate = moment(localfilterData.endDate).format('YYYYMMDD');
                filterData = localfilterData;
                $(".topXcampaignSelect").change(); 
                refreshContent(".reloadIcon");
                refreshContent(".reloadIcon-sm");     
            }
            if(localfilterData.startDate && localfilterData.endDate && localfilterData.campaignId && localfilterData.profileId){
                localfilterData.startDate = moment(localfilterData.startDate).format('YYYYMMDD');
                localfilterData.endDate = moment(localfilterData.endDate).format('YYYYMMDD');
                filterData = localfilterData;
                CampaignAjaxCalls(filterData);
            }
        }
    }
});

/**
 * table variables
 */

var adtype_dataTable, 
strategy_dataTable,
target_dataTable,
perf_dataTable,
perf_ytd_dataTable,
product_dataTable,
top_campaigns_dataTable,
max_meter; 

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
    localStorage.setItem("filterData",JSON.stringify(newfilter));
}
/**
 * Ajax Data calls for Campaigns 
 */
function CampaignAjaxCalls(filterData){
    if(filterData.startDate && filterData.endDate){
        filterData.startDate = moment(filterData.startDate).format('YYYYMMDD')
        filterData.endDate = moment(filterData.endDate).format('YYYYMMDD')
    }
    if(filterData.startDate && filterData.endDate && filterData.campaignId && filterData.profileId){
        showPreLoaders(".preLoader");
        $('input[name="datefilter"]').prop( "disabled", true );

        $("#sectionA").removeClass("defaultHide");
        getMetricsScoreCards();
        performanceChart();
        efficiencyChart();
        awarenessChart();
        populateMOM();
        populateWOW();
        populateDOD();
        // Ytd Call
        populateYTD()
        populateWTD()
        if(adtype_dataTable){
            adtype_dataTable.destroy();
        }
        Ad_type_DataCall();
        if(strategy_dataTable){
            strategy_dataTable.destroy();
        }

        Strategy_DataCall();
        if(target_dataTable){
            target_dataTable.destroy();
        }
        custom_DataCall();
        if(product_dataTable){
            product_dataTable.destroy();
        }
        prodType_DataCall();
        if(perf_dataTable){
            perf_dataTable.destroy();
        }
        perf_Pre30_DataCall();
        if(perf_ytd_dataTable){
            perf_ytd_dataTable.destroy();
        }
        perf_ytd_DataCall();
        hidePreLoaders('#topTen_table_Div');
        refreshContent(".reloadIcon");
        refreshContent(".reloadIcon-sm");

    }//end if
}
/**
 * get Metrics Scores
 */
function getMetricsScoreCards(){
    $.ajax({
        method: "GET",
        data:filterData,
        // dataType: "json",
        url: AMSScoreCards_DataUrl,
        // data: data,
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
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) { 
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
/**
 * 
 * @param {rank} number 
 */
function helperRankBasedColor(rank){
    switch(rank){
        case 1:
            return "#4caf50";
        case 2:
            return "#4caf505e";
        case 3:
            return "#4caf503d";
        case 4:
            return "#4caf502e";
        case 5:
            return "#4caf502e";
        case 6:
            return "#4caf502b";
        case 7:
            return "#4caf501d";
        case 8:
            return "#4caf500a";
        case 9:
            return "#4caf500a";
        case 10:
            return "#4caf500a";                                                                                    
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
 * get max row based on attribute of json
 * @param {*} arr 
 * @param {*} prop 
 */
function getMax_Meter(arr, prop) {
    var max;
    for (var i=0 ; i<arr.length ; i++) {
        if (max == null || parseInt(arr[i][prop]) > parseInt(max[prop]))
            max = arr[i];
    }
    return max;
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
    $.ajax({
        method: "GET",
        data:filterData,
        // dataType: "json",
        url: perf_Grand_DataUrl,
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
    $.ajax({
        method: "GET",
        data:filterData,
        // dataType: "json",
        url: perf_percentage_DataUrl,
        // data: data,
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

/**
 * Efficiency Percentages
 */
function efficiencyGrandTotal(){
    $.ajax({
        method: "GET",
        data:filterData,
        // dataType: "json",
        url: effi_Grand_DataUrl,
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
    $.ajax({
        method: "GET",
        data:filterData,
        // dataType: "json",
        url: effi_percentage_DataUrl,
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
    $.ajax({
        method: "GET",
        data:filterData,
        // dataType: "json",
        url: awar_Grand_DataUrl,
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
                $("#awar-ctr-currency").text(result[0].ctr+"%");
                hidePreLoaders('#awarenessDiv');
            } 
            hidePreLoaders('#awarenessDiv');  
        }
    });
}

function awarenessPercentageCall(){
    var impr,click,ctr; 
    $.ajax({
        method: "GET",
        data:filterData,
        // dataType: "json",
        url: awar_percentage_DataUrl,
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
    $.ajax({
        method: "GET",
        data:filterData,
        // dataType: "json",
        data:filterData,
        url: performanceDataUrl,
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

    $.ajax({
        method: "GET",
        data:filterData,
        // dataType: "json",
        url: efficiencyDataUrl,
        // data: data,
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

    $.ajax({
        method: "GET",
        data:filterData,
        // dataType: "json",
        url: awarenessDataUrl,
        // data: data,
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

/**
 * MOM percentages population
 */
function momPercentagesCall(){
    var impressions,revenue,cost,acos,cpc,roas;
    $.ajax({
        method: "GET",
        data:filterData,
        // dataType: "json",
        url: MOM_percentage_DataUrl,
        // data: data,
        success: function(result){
            revenue = +result[0].revenue_perc;
            cost = +result[0].cost_perc;
            acos = +result[0].acos_perc;
            impressions = +result[0].impressions_perc;
            cpc = +result[0].cpc_perc;
            roas = +result[0].roas_perc;

            //Rotate and change arrow/text color
            if(revenue < 0) {
                $('#mom-rev-lbl').removeClass('downGreen downRed');
                $('#mom-rev-lbl').addClass('downRed')
                
                $('#mom-rev-svg').removeClass('green-color arrow-up');
                $('#mom-rev-svg').addClass('red-color')
                $("#mom-rev-lbl").text(revenue+"%");         
            } else {
                $('#mom-rev-lbl').removeClass('downGreen downRed');
                $('#mom-rev-lbl').addClass('downGreen') 
                $('#mom-rev-svg').removeClass('red-color');
                $('#mom-rev-svg').addClass('green-color arrow-up');
                $("#mom-rev-lbl").text(revenue+"%");   
            }

            //Rotate and change arrow/text color
            if(cost < 0) {
                $('#mom-cost-lbl').removeClass('downGreen downRed');
                $('#mom-cost-lbl').addClass('downRed')
                
                $('#mom-cost-svg').removeClass('green-color arrow-up');
                $('#mom-cost-svg').addClass('red-color')
                $("#mom-cost-lbl").text(cost+"%");
                           
            } else {
                $('#mom-cost-lbl').removeClass('downGreen downRed');
                $('#mom-cost-lbl').addClass('downGreen') 
                $('#mom-cost-svg').removeClass('red-color');
                $('#mom-cost-svg').addClass('green-color arrow-up');
                $("#mom-cost-lbl").text(cost+"%");
            }
        
            //Rotate and change arrow/text color
            if(acos < 0) {
                $('#mom-acos-lbl').removeClass('downGreen downRed');
                $('#mom-acos-lbl').addClass('downRed')
                
                $('#mom-acos-svg').removeClass('green-color arrow-up');
                $('#mom-acos-svg').addClass('red-color')
                $("#mom-acos-lbl").text(acos+"%");           
            } else {
                $('#mom-acos-lbl').removeClass('downGreen downRed');
                $('#mom-acos-lbl').addClass('downGreen') 
                $('#mom-acos-svg').removeClass('red-color');
                $('#mom-acos-svg').addClass('green-color arrow-up');
                $("#mom-acos-lbl").text(acos+"%");
            }
        
            //Rotate and change arrow/text color
            if(impressions < 0) {
                $('#mom-impr-lbl').removeClass('downGreen downRed');
                $('#mom-impr-lbl').addClass('downRed')
                
                $('#mom-impr-svg').removeClass('green-color arrow-up');
                $('#mom-impr-svg').addClass('red-color')
                $("#mom-impr-lbl").text(impressions+"%");           
            } else {
                $('#mom-impr-lbl').removeClass('downGreen downRed');
                $('#mom-impr-lbl').addClass('downGreen') 
                $('#mom-impr-svg').removeClass('red-color');
                $('#mom-impr-svg').addClass('green-color arrow-up');
                $("#mom-impr-lbl").text(impressions+"%");
            }

            //Rotate and change arrow/text color
            if(cpc < 0) {
                $('#mom-cpc-lbl').removeClass('downGreen downRed');
                $('#mom-cpc-lbl').addClass('downRed')
                
                $('#mom-cpc-svg').removeClass('green-color arrow-up');
                $('#mom-cpc-svg').addClass('red-color')
                $("#mom-cpc-lbl").text(cpc+"%");           
            } else {
                $('#mom-cpc-lbl').removeClass('downGreen downRed');
                $('#mom-cpc-lbl').addClass('downGreen') 
                $('#mom-cpc-svg').removeClass('red-color');
                $('#mom-cpc-svg').addClass('green-color arrow-up');
                $("#mom-cpc-lbl").text(cpc+"%");
            }
        
            //Rotate and change arrow/text color
            if(roas < 0) {
                $('#mom-roas-lbl').removeClass('downGreen downRed');
                $('#mom-roas-lbl').addClass('downRed')
                
                $('#mom-roas-svg').removeClass('green-color arrow-up');
                $('#mom-roas-svg').addClass('red-color')
                $("#mom-roas-lbl").text(roas+"%");           
            } else {
                $('#mom-roas-lbl').removeClass('downGreen downRed');
                $('#mom-roas-lbl').addClass('downGreen') 
                $('#mom-roas-svg').removeClass('red-color');
                $('#mom-roas-svg').addClass('green-color arrow-up');
                $("#mom-roas-lbl").text(roas+"%");
            }
        }
    });
}
/**
 * Populate the Month Over Month
 */
function populateMOM(){
    $.ajax({
        method: "GET",
        data:filterData,
        // dataType: "json",
        url: momDataUrl,
        // data: data,
        success: function(result){
            if(result.length != 0){
                $("#mom-impr-currency").text(commaFormat(+result[0].impressions));
                $("#mom-cost-currency").text("$"+commaFormat((+result[0].cost).toFixed(2)));
                $("#mom-rev-currency").text("$"+commaFormat((+result[0].revenue).toFixed(2)));
                
                $("#mom-acos-currency").text((+result[0].acos_).toFixed(2)+"%");
                $("#mom-cpc-currency").text("$"+commaFormat((+result[0].CPC).toFixed(2)));
                $("#mom-roas-currency").text("$"+commaFormat((+result[0].ROAS).toFixed(2)));
                momPercentagesCall();
                hidePreLoaders('#momDiv');
            }
        }});
}

/**
 * WOW percentages population
 */
function wowPercentagesCall(){
    var impressions,revenue,cost,acos,cpc,roas;
    $.ajax({
        method: "GET",
        data:filterData,
        // dataType: "json",
        url: Wow_percentage_DataUrl,
        // data: data,
        success: function(result){
            revenue = +result[0].revenue_perc;
            cost = +result[0].cost_perc+"%";
            acos = +result[0].acos_perc+"%";
            impressions = +result[0].impressions_perc+"%";
            cpc = +result[0].cpc_perc+"%";
            roas = +result[0].roas_perc+"%";
            
            //Rotate and change arrow/text color
            if(revenue < 0) {
                $('#wow-rev-lbl').removeClass('downGreen downRed');
                $('#wow-rev-lbl').addClass('downRed')
                
                $('#wow-rev-svg').removeClass('green-color arrow-up');
                $('#wow-rev-svg').addClass('red-color')
                $("#wow-rev-lbl").text(revenue);         
            } else {
                $('#wow-rev-lbl').removeClass('downGreen downRed');
                $('#wow-rev-lbl').addClass('downGreen') 
                $('#wow-rev-svg').removeClass('red-color');
                $('#wow-rev-svg').addClass('green-color arrow-up');
                $("#wow-rev-lbl").text(revenue);   
            }

            //Rotate and change arrow/text color
            if(cost < 0) {
                $('#wow-cost-lbl').removeClass('downGreen downRed');
                $('#wow-cost-lbl').addClass('downRed')
                
                $('#wow-cost-svg').removeClass('green-color arrow-up');
                $('#wow-cost-svg').addClass('red-color')
                $("#wow-cost-lbl").text(cost);
                           
            } else {
                $('#wow-cost-lbl').removeClass('downGreen downRed');
                $('#wow-cost-lbl').addClass('downGreen') 
                $('#wow-cost-svg').removeClass('red-color');
                $('#wow-cost-svg').addClass('green-color arrow-up');
                $("#wow-cost-lbl").text(cost);
            }
        
            //Rotate and change arrow/text color
            if(acos < 0) {
                $('#wow-acos-lbl').removeClass('downGreen downRed');
                $('#wow-acos-lbl').addClass('downRed')
                
                $('#wow-acos-svg').removeClass('green-color arrow-up');
                $('#wow-acos-svg').addClass('red-color')
                $("#wow-acos-lbl").text(acos);           
            } else {
                $('#wow-acos-lbl').removeClass('downGreen downRed');
                $('#wow-acos-lbl').addClass('downGreen') 
                $('#wow-acos-svg').removeClass('red-color');
                $('#wow-acos-svg').addClass('green-color arrow-up');
                $("#wow-acos-lbl").text(acos);
            }
        
            //Rotate and change arrow/text color
            if(impressions < 0) {
                $('#wow-impr-lbl').removeClass('downGreen downRed');
                $('#wow-impr-lbl').addClass('downRed')
                
                $('#wow-impr-svg').removeClass('green-color arrow-up');
                $('#wow-impr-svg').addClass('red-color')
                $("#wow-impr-lbl").text(impressions);           
            } else {
                $('#wow-impr-lbl').removeClass('downGreen downRed');
                $('#wow-impr-lbl').addClass('downGreen') 
                $('#wow-impr-svg').removeClass('red-color');
                $('#wow-impr-svg').addClass('green-color arrow-up');
                $("#wow-impr-lbl").text(impressions);
            }

            //Rotate and change arrow/text color
            if(cpc < 0) {
                $('#wow-cpc-lbl').removeClass('downGreen downRed');
                $('#wow-cpc-lbl').addClass('downRed')
                
                $('#wow-cpc-svg').removeClass('green-color arrow-up');
                $('#wow-cpc-svg').addClass('red-color')
                $("#wow-cpc-lbl").text(cpc);           
            } else {
                $('#wow-cpc-lbl').removeClass('downGreen downRed');
                $('#wow-cpc-lbl').addClass('downGreen') 
                $('#wow-cpc-svg').removeClass('red-color');
                $('#wow-cpc-svg').addClass('green-color arrow-up');
                $("#wow-cpc-lbl").text(cpc);
            }
        
            //Rotate and change arrow/text color
            if(roas < 0) {
                $('#wow-roas-lbl').removeClass('downGreen downRed');
                $('#wow-roas-lbl').addClass('downRed')
                
                $('#wow-roas-svg').removeClass('green-color arrow-up');
                $('#wow-roas-svg').addClass('red-color')
                $("#wow-roas-lbl").text(roas);           
            } else {
                $('#wow-roas-lbl').removeClass('downGreen downRed');
                $('#wow-roas-lbl').addClass('downGreen') 
                $('#wow-roas-svg').removeClass('red-color');
                $('#wow-roas-svg').addClass('green-color arrow-up');
                $("#wow-roas-lbl").text(roas);
            }
        }
    });
}
/**
 * Populate the Week Over Week
 */
function populateWOW(){
    $.ajax({
        method: "GET",
        data:filterData,
        // dataType: "json",
        url: wowDataUrl,
        // data: data,
        success: function(result){
            if(result.length != 0){
                $("#wow-impr-currency").text(commaFormat(+result[0].impressions));
                $("#wow-cost-currency").text("$"+commaFormat((+result[0].cost).toFixed(2)));
                $("#wow-rev-currency").text("$"+commaFormat((+result[0].revenue).toFixed(2)));
                
                $("#wow-acos-currency").text((+result[0].acos_).toFixed(2)+"%");
                $("#wow-cpc-currency").text("$"+commaFormat((+result[0].CPC).toFixed(2)));
                $("#wow-roas-currency").text("$"+commaFormat((+result[0].ROAS).toFixed(2)));
                wowPercentagesCall();
                hidePreLoaders('#wowDiv');
            }
            hidePreLoaders('#wowDiv');
        }});
}

function dodPercentagesCall(){
    var impressions,revenue,cost,acos,cpc,roas;
    $.ajax({
        method: "GET",
        data:filterData,
        // dataType: "json",
        url: DOD_percentage_DataUrl,
        // data: data,
        success: function(result){
            revenue = +result[0].revenue_perc;
            cost = +result[0].cost_perc;
            acos = +result[0].acos_perc;
            impressions = +result[0].impressions_perc;
            cpc = +result[0].cpc_perc;
            roas = +result[0].roas_perc;
            
            //Rotate and change arrow/text color
            if(revenue < 0) {
                $('#dod-rev-lbl').removeClass('downGreen downRed');
                $('#dod-rev-lbl').addClass('downRed')
                
                $('#dod-rev-svg').removeClass('green-color arrow-up');
                $('#dod-rev-svg').addClass('red-color')
                $("#dod-rev-lbl").text(revenue+"%");         
            } else {
                $('#dod-rev-lbl').removeClass('downGreen downRed');
                $('#dod-rev-lbl').addClass('downGreen') 
                $('#dod-rev-svg').removeClass('red-color');
                $('#dod-rev-svg').addClass('green-color arrow-up');
                $("#dod-rev-lbl").text(revenue+"%");   
            }

            //Rotate and change arrow/text color
            if(cost < 0) {
                $('#dod-cost-lbl').removeClass('downGreen downRed');
                $('#dod-cost-lbl').addClass('downRed')
                
                $('#dod-cost-svg').removeClass('green-color arrow-up');
                $('#dod-cost-svg').addClass('red-color')
                $("#dod-cost-lbl").text(cost+"%");
                           
            } else {
                $('#dod-cost-lbl').removeClass('downGreen downRed');
                $('#dod-cost-lbl').addClass('downGreen') 
                $('#dod-cost-svg').removeClass('red-color');
                $('#dod-cost-svg').addClass('green-color arrow-up');
                $("#dod-cost-lbl").text(cost+"%");
            }
        
            //Rotate and change arrow/text color
            if(acos < 0) {
                $('#dod-acos-lbl').removeClass('downGreen downRed');
                $('#dod-acos-lbl').addClass('downRed')
                
                $('#dod-acos-svg').removeClass('green-color arrow-up');
                $('#dod-acos-svg').addClass('red-color')
                $("#dod-acos-lbl").text(acos+"%");           
            } else {
                $('#dod-acos-lbl').removeClass('downGreen downRed');
                $('#dod-acos-lbl').addClass('downGreen') 
                $('#dod-acos-svg').removeClass('red-color');
                $('#dod-acos-svg').addClass('green-color arrow-up');
                $("#dod-acos-lbl").text(acos+"%");
            }
        
            //Rotate and change arrow/text color
            if(impressions < 0) {
                $('#dod-impr-lbl').removeClass('downGreen downRed');
                $('#dod-impr-lbl').addClass('downRed')
                
                $('#dod-impr-svg').removeClass('green-color arrow-up');
                $('#dod-impr-svg').addClass('red-color')
                $("#dod-impr-lbl").text(impressions+"%");           
            } else {
                $('#dod-impr-lbl').removeClass('downGreen downRed');
                $('#dod-impr-lbl').addClass('downGreen') 
                $('#dod-impr-svg').removeClass('red-color');
                $('#dod-impr-svg').addClass('green-color arrow-up');
                $("#dod-impr-lbl").text(impressions+"%");
            }

            //Rotate and change arrow/text color
            if(cpc < 0) {
                $('#dod-cpc-lbl').removeClass('downGreen downRed');
                $('#dod-cpc-lbl').addClass('downRed')
                
                $('#dod-cpc-svg').removeClass('green-color arrow-up');
                $('#dod-cpc-svg').addClass('red-color')
                $("#dod-cpc-lbl").text(cpc+"%");           
            } else {
                $('#dod-cpc-lbl').removeClass('downGreen downRed');
                $('#dod-cpc-lbl').addClass('downGreen') 
                $('#dod-cpc-svg').removeClass('red-color');
                $('#dod-cpc-svg').addClass('green-color arrow-up');
                $("#dod-cpc-lbl").text(cpc+"%");
            }
        
            //Rotate and change arrow/text color
            if(roas < 0) {
                $('#dod-roas-lbl').removeClass('downGreen downRed');
                $('#dod-roas-lbl').addClass('downRed')
                
                $('#dod-roas-svg').removeClass('green-color arrow-up');
                $('#dod-roas-svg').addClass('red-color')
                $("#dod-roas-lbl").text(roas+"%");           
            } else {
                $('#dod-roas-lbl').removeClass('downGreen downRed');
                $('#dod-roas-lbl').addClass('downGreen') 
                $('#dod-roas-svg').removeClass('red-color');
                $('#dod-roas-svg').addClass('green-color arrow-up');
                $("#dod-roas-lbl").text(roas+"%");
            }
        }
    }); 
}
/**
 * Populate the Day Over Day
 */
function populateDOD(){
    $.ajax({
        method: "GET",
        data:filterData,
        // dataType: "json",
        url: dodDataUrl,
        // data: data,
        success: function(result){
            if(result.length != 0){
                $("#dod-impr-currency").text(commaFormat(+result[0].impressions));
                $("#dod-cost-currency").text("$"+commaFormat((+result[0].cost).toFixed(2)));
                $("#dod-rev-currency").text("$"+commaFormat((+result[0].revenue).toFixed(2)));
                
                $("#dod-acos-currency").text((+result[0].acos_).toFixed(2)+"%");
                $("#dod-cpc-currency").text("$"+commaFormat((+result[0].CPC).toFixed(2)));
                $("#dod-roas-currency").text("$"+commaFormat((+result[0].ROAS).toFixed(2)));              
            }
            dodPercentagesCall();
            hidePreLoaders('#dodDiv');
        }});
}

/**
 * Get all the ytd percentages and presentations
 */
function ytdPercentagesCall(){
    var impressions,revenue,cost,acos,cpc,roas;
    $.ajax({
        method: "GET",
        data:filterData,
        // dataType: "json",
        url: ytd_percentage_DataUrl,
        // data: data,
        success: function(result){
            revenue = (+result[0].revenue_perc);
            cost = (+result[0].cost_perc);
            acos = (+result[0].acos_perc);
            impressions = (+result[0].impressions_perc);
            cpc = (+result[0].cpc_perc);
            roas = (+result[0].roas_perc);
            
            //Rotate and change arrow/text color
            if(revenue < 0) {
                $('#ytd-rev-lbl').removeClass('downGreen downRed');
                $('#ytd-rev-lbl').addClass('downRed')
                
                $('#ytd-rev-svg').removeClass('green-color arrow-up');
                $('#ytd-rev-svg').addClass('red-color')
                $("#ytd-rev-lbl").text(revenue+"%");         
            } else {
                $('#ytd-rev-lbl').removeClass('downGreen downRed');
                $('#ytd-rev-lbl').addClass('downGreen') 
                $('#ytd-rev-svg').removeClass('red-color');
                $('#ytd-rev-svg').addClass('green-color arrow-up');
                $("#ytd-rev-lbl").text(revenue+"%");   
            }

            //Rotate and change arrow/text color
            if(cost < 0) {
                $('#ytd-cost-lbl').removeClass('downGreen downRed');
                $('#ytd-cost-lbl').addClass('downRed')
                
                $('#ytd-cost-svg').removeClass('green-color arrow-up');
                $('#ytd-cost-svg').addClass('red-color')
                $("#ytd-cost-lbl").text(cost+"%");
                           
            } else {
                $('#ytd-cost-lbl').removeClass('downGreen downRed');
                $('#ytd-cost-lbl').addClass('downGreen') 
                $('#ytd-cost-svg').removeClass('red-color');
                $('#ytd-cost-svg').addClass('green-color arrow-up');
                $("#ytd-cost-lbl").text(cost+"%");
            }
        
            //Rotate and change arrow/text color
            if(acos < 0) {
                $('#ytd-acos-lbl').removeClass('downGreen downRed');
                $('#ytd-acos-lbl').addClass('downRed')
                
                $('#ytd-acos-svg').removeClass('green-color arrow-up');
                $('#ytd-acos-svg').addClass('red-color')
                $("#ytd-acos-lbl").text(acos+"%");           
            } else {
                $('#ytd-acos-lbl').removeClass('downGreen downRed');
                $('#ytd-acos-lbl').addClass('downGreen') 
                $('#ytd-acos-svg').removeClass('red-color');
                $('#ytd-acos-svg').addClass('green-color arrow-up');
                $("#ytd-acos-lbl").text(acos+"%");
            }
        
            //Rotate and change arrow/text color
            if(impressions < 0) {
                $('#ytd-impr-lbl').removeClass('downGreen downRed');
                $('#ytd-impr-lbl').addClass('downRed')
                
                $('#ytd-impr-svg').removeClass('green-color arrow-up');
                $('#ytd-impr-svg').addClass('red-color')
                $("#ytd-impr-lbl").text(impressions+"%");           
            } else {
                $('#ytd-impr-lbl').removeClass('downGreen downRed');
                $('#ytd-impr-lbl').addClass('downGreen') 
                $('#ytd-impr-svg').removeClass('red-color');
                $('#ytd-impr-svg').addClass('green-color arrow-up');
                $("#ytd-impr-lbl").text(impressions+"%");
            }

            //Rotate and change arrow/text color
            if(cpc < 0) {
                $('#ytd-cpc-lbl').removeClass('downGreen downRed');
                $('#ytd-cpc-lbl').addClass('downRed')
                
                $('#ytd-cpc-svg').removeClass('green-color arrow-up');
                $('#ytd-cpc-svg').addClass('red-color')
                $("#ytd-cpc-lbl").text(cpc+"%");           
            } else {
                $('#ytd-cpc-lbl').removeClass('downGreen downRed');
                $('#ytd-cpc-lbl').addClass('downGreen') 
                $('#ytd-cpc-svg').removeClass('red-color');
                $('#ytd-cpc-svg').addClass('green-color arrow-up');
                $("#ytd-cpc-lbl").text(cpc+"%");
            }
        
            //Rotate and change arrow/text color
            if(roas < 0) {
                $('#ytd-roas-lbl').removeClass('downGreen downRed');
                $('#ytd-roas-lbl').addClass('downRed')
                
                $('#ytd-roas-svg').removeClass('green-color arrow-up');
                $('#ytd-roas-svg').addClass('red-color')
                $("#ytd-roas-lbl").text(roas+"%");           
            } else {
                $('#ytd-roas-lbl').removeClass('downGreen downRed');
                $('#ytd-roas-lbl').addClass('downGreen') 
                $('#ytd-roas-svg').removeClass('red-color');
                $('#ytd-roas-svg').addClass('green-color arrow-up');
                $("#ytd-roas-lbl").text(roas+"%");
            }
        }
    });
}
/**
 * Populate the YTD
 */
function populateYTD(){
    $.ajax({
        method: "GET",
        data:filterData,
        // dataType: "json",
        url: ytdDataUrl,
        // data: data,
        success: function(result){
            if(result.length != 0){
                $("#ytd-impr-currency").text(commaFormat(+result[0].impressions));
                $("#ytd-cost-currency").text("$"+commaFormat((+result[0].cost).toFixed(2)));
                $("#ytd-rev-currency").text("$"+commaFormat((+result[0].revenue).toFixed(2)));
                
                $("#ytd-acos-currency").text((+result[0].acos_).toFixed(2)+"%");
                $("#ytd-cpc-currency").text("$"+commaFormat((+result[0].CPC).toFixed(2)));
                $("#ytd-roas-currency").text("$"+commaFormat((+result[0].ROAS).toFixed(2)));
              
                ytdPercentagesCall();
            }
            hidePreLoaders('#ytdDiv');
        }});
}

/**
 * Get all the wtd percentages and presentations
 */
function wtdPercentagesCall(){
    var impressions,revenue,cost,acos,cpc,roas;
    $.ajax({
        method: "GET",
        data:filterData,
        // dataType: "json",
        url: wtd_percentage_DataUrl,
        // data: data,
        success: function(result){
            revenue = (+result[0].revenue_perc);
            cost = (+result[0].cost_perc);
            acos = (+result[0].acos_perc);
            impressions = (+result[0].impressions_perc);
            cpc = (+result[0].cpc_perc);
            roas = (+result[0].roas_perc);
            
             //Rotate and change arrow/text color
             if(revenue < 0) {
                $('#wtd-rev-lbl').removeClass('downGreen downRed');
                $('#wtd-rev-lbl').addClass('downRed')
                
                $('#wtd-rev-svg').removeClass('green-color arrow-up');
                $('#wtd-rev-svg').addClass('red-color')
                $("#wtd-rev-lbl").text(revenue+"%");         
            } else {
                $('#wtd-rev-lbl').removeClass('downGreen downRed');
                $('#wtd-rev-lbl').addClass('downGreen') 
                $('#wtd-rev-svg').removeClass('red-color');
                $('#wtd-rev-svg').addClass('green-color arrow-up');
                $("#wtd-rev-lbl").text(revenue+"%");   
            }

            //Rotate and change arrow/text color
            if(cost < 0) {
                $('#wtd-cost-lbl').removeClass('downGreen downRed');
                $('#wtd-cost-lbl').addClass('downRed')
                
                $('#wtd-cost-svg').removeClass('green-color arrow-up');
                $('#wtd-cost-svg').addClass('red-color')
                $("#wtd-cost-lbl").text(cost+"%");
                           
            } else {
                $('#wtd-cost-lbl').removeClass('downGreen downRed');
                $('#wtd-cost-lbl').addClass('downGreen') 
                $('#wtd-cost-svg').removeClass('red-color');
                $('#wtd-cost-svg').addClass('green-color arrow-up');
                $("#wtd-cost-lbl").text(cost+"%");
            }
        
            //Rotate and change arrow/text color
            if(acos < 0) {
                $('#wtd-acos-lbl').removeClass('downGreen downRed');
                $('#wtd-acos-lbl').addClass('downRed')
                
                $('#wtd-acos-svg').removeClass('green-color arrow-up');
                $('#wtd-acos-svg').addClass('red-color')
                $("#wtd-acos-lbl").text(acos+"%");           
            } else {
                $('#wtd-acos-lbl').removeClass('downGreen downRed');
                $('#wtd-acos-lbl').addClass('downGreen') 
                $('#wtd-acos-svg').removeClass('red-color');
                $('#wtd-acos-svg').addClass('green-color arrow-up');
                $("#wtd-acos-lbl").text(acos+"%");
            }
        
            //Rotate and change arrow/text color
            if(impressions < 0) {
                $('#wtd-impr-lbl').removeClass('downGreen downRed');
                $('#wtd-impr-lbl').addClass('downRed')
                
                $('#wtd-impr-svg').removeClass('green-color arrow-up');
                $('#wtd-impr-svg').addClass('red-color')
                $("#wtd-impr-lbl").text(impressions+"%");           
            } else {
                $('#wtd-impr-lbl').removeClass('downGreen downRed');
                $('#wtd-impr-lbl').addClass('downGreen') 
                $('#wtd-impr-svg').removeClass('red-color');
                $('#wtd-impr-svg').addClass('green-color arrow-up');
                $("#wtd-impr-lbl").text(impressions+"%");
            }

            //Rotate and change arrow/text color
            if(cpc < 0) {
                $('#wtd-cpc-lbl').removeClass('downGreen downRed');
                $('#wtd-cpc-lbl').addClass('downRed')
                
                $('#wtd-cpc-svg').removeClass('green-color arrow-up');
                $('#wtd-cpc-svg').addClass('red-color')
                $("#wtd-cpc-lbl").text(cpc+"%");           
            } else {
                $('#wtd-cpc-lbl').removeClass('downGreen downRed');
                $('#wtd-cpc-lbl').addClass('downGreen') 
                $('#wtd-cpc-svg').removeClass('red-color');
                $('#wtd-cpc-svg').addClass('green-color arrow-up');
                $("#wtd-cpc-lbl").text(cpc+"%");
            }
        
            //Rotate and change arrow/text color
            if(roas < 0) {
                $('#wtd-roas-lbl').removeClass('downGreen downRed');
                $('#wtd-roas-lbl').addClass('downRed')
                
                $('#wtd-roas-svg').removeClass('green-color arrow-up');
                $('#wtd-roas-svg').addClass('red-color')
                $("#wtd-roas-lbl").text(roas+"%");           
            } else {
                $('#wtd-roas-lbl').removeClass('downGreen downRed');
                $('#wtd-roas-lbl').addClass('downGreen') 
                $('#wtd-roas-svg').removeClass('red-color');
                $('#wtd-roas-svg').addClass('green-color arrow-up');
                $("#wtd-roas-lbl").text(roas+"%");
            }
        }
    });
}
/**
 * Populate the WTD
 */
function populateWTD(){
    $.ajax({
        method: "GET",
        data:filterData,
        // dataType: "json",
        url: wtdDataUrl,
        // data: data,
        success: function(result){
            if(result.length != 0){
                $("#wtd-impr-currency").text(commaFormat(+result[0].impressions));
                $("#wtd-cost-currency").text("$"+commaFormat((+result[0].cost).toFixed(2)));
                $("#wtd-rev-currency").text("$"+commaFormat((+result[0].revenue).toFixed(2)));
                
                $("#wtd-acos-currency").text((+result[0].acos_).toFixed(2)+"%");
                $("#wtd-cpc-currency").text("$"+commaFormat((+result[0].CPC).toFixed(2)));
                $("#wtd-roas-currency").text("$"+commaFormat((+result[0].ROAS).toFixed(2)));
              
                wtdPercentagesCall();
            }
            hidePreLoaders('#wtdDiv');
        }});
}

/**
 * AD Type 
 */
function Ad_type_DataCall(){
    $.ajax({
        method: "GET",
        data:filterData,
        // dataType: "json",
        url: adTypeDataUrl,
        // data: data,
        success: function(result){
            Ad_type_table(result.tableData,result.grandTotals);
            hidePreLoaders('#adType_table_Div');
        }
      });
    }

/**
 * Ad Type Table
 */
function Ad_type_table(AdDataJson,grandTotals){
    $("#adtype_head").removeClass( "trCells" );
    adtype_dataTable = $('#table_adtype').removeAttr('width').DataTable( {
        "data" : AdDataJson,
        "bFilter": false,
        "info":     false,
        "pageLength": 5,
        "bAutoWidth": true,
        "scrollX": "100%",
        "ordering":true,
        "sScrollY": "180px",
        "autoWidth":true,
        "destroy": true,
        "pagingType": "simple",
        'columns': [
            {data: 'campaign_type'},
            { data: 'impressions' , 
            render: function ( data, type, row ) {
                var tooltipAttr = '<div class="customTooltip" title="'+data+'">' + commaSeparator(+data) + '</div>'
                return tooltipAttr;
              }}, /* index = 0 */
            { data: 'revenue' , 
            render: function ( data, type, row ) {
                var tooltipAttr = '<div class="customTooltip" title="'+"$"+data+'">'+ "$"+ commaSeparator(+data) + '</div>'
                return tooltipAttr;
              }}, /* index = 1 */
            { data: 'acos_' }, /* index = 2 */
            { data: 'CTR' }, /* index = 4 */
            { data: 'cost' , 
            render: function ( data, type, row ) {
                var tooltipAttr = '<div class="customTooltip" title="'+"$"+data+'">'+ "$"+ commaSeparator(+data) + '</div>'
                return tooltipAttr;
              }}, /* index = 5 */
         ],
        'columnDefs': [ 
            {
            'targets': [1,2,3,4,5], /* column index */
            'orderable': false, /* true or false */
        },
        {
            "targets":[1,2,3,4,5],
            className: 'dt-body-center'
        }
        ],
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
        
            // Update footer by showing the total with the reference of the column index 
            $( api.column( 0 ).footer() ).html(grandTotals[0].Grand_tot);
            
            $( api.column( 1 ).footer() ).html(commaSeparator((+grandTotals[0].impressions).toFixed(2)));
            if($(api.column( 1 ).footer() ).hasClass("tooltipstered")){
                $(api.column( 1 ).footer() ).tooltipster('content', grandTotals[0].impressions);
            }else{
                $(api.column( 1 ).footer() ).attr("title",grandTotals[0].impressions);
            }
            
            
            
            $( api.column( 2 ).footer() ).html("$"+commaSeparator((+grandTotals[0].revenue).toFixed(2)));
            if($(api.column( 2 ).footer() ).hasClass("tooltipstered")){
                $(api.column( 2 ).footer() ).tooltipster('content', "$"+grandTotals[0].revenue);
            }else{
                $(api.column( 2 ).footer() ).attr("title","$"+grandTotals[0].revenue);
            }
           

            $( api.column( 3 ).footer() ).html((+grandTotals[0].acos_).toFixed(2)+"%");
            $( api.column( 4 ).footer() ).html((+grandTotals[0].CTR).toFixed(2)+"%");
            $( api.column( 5 ).footer() ).html("$"+commaSeparator((+grandTotals[0].cost).toFixed(2)));
            if($(api.column( 5 ).footer() ).hasClass("tooltipstered")){
                $(api.column( 5 ).footer() ).tooltipster('content',"$"+grandTotals[0].cost);
            }else{
                $(api.column( 5 ).footer() ).attr("title","$"+grandTotals[0].cost);
            }
           
            $('.adtypeToolTip').tooltipster();
           
        },
        "drawCallback": function(settings) {
            $('.customTooltip').tooltipster({contentAsHTML: 'true',
            multiple:true,
            animation: 'grow',});
        }
    },
     );
     $( "div" ).remove( ".dataTables_length" );
}
/**
 * Strategy Type 
 */
function Strategy_DataCall(){
    $.ajax({
        method: "GET",
        data:filterData,
        // dataType: "json",
        url: stragtegyTypeDataUrl,
        // data: data,
        success: function(result){
            Strategy_type_table(result.tableData,result.grandTotals);
            hidePreLoaders('#strategy_table_Div');
        }
    });
}

/**
 * Strategy Type Table
 */
function Strategy_type_table(AdDataJson,grandTotals){
    $( "#str_head" ).removeClass( "trCells" );
    strategy_dataTable = $('#table_Str_type').DataTable( {
        "data" : AdDataJson,
        "bFilter": false,
        "info":     false,
        "scrollX": true,
        "sScrollY": "180px",
        "pagingType": "simple",
        "ordering":true,
        "destroy": true,
        "autoWidth":true,
        "pageLength": 5,
        'columns': [
            {data: 'tag'},
            { data: 'imp' , 
            render: function ( data, type, row ) {
                var tooltipAttr = '<div class="customTooltip" title="'+data+'">' + commaSeparator(+data) + '</div>'
                return tooltipAttr;
              }}, /* index = 0 */
            { data: 'rev' , 
            render: function ( data, type, row ) {
                var tooltipAttr = '<div class="customTooltip" title="'+"$"+data+'">'+ "$"+ commaSeparator(+data) + '</div>'
                return tooltipAttr;
              }}, /* index = 1 */
            { data: 'acos' }, /* index = 2 */
            { data: 'ctr' }, /* index = 4 */
            { data: 'cost' , 
            render: function ( data, type, row ) {
                var tooltipAttr = '<div class="customTooltip" title="'+"$"+data+'">' +"$"+ commaSeparator(+data) + '</div>'
                return tooltipAttr;
              }}, /* index = 5 */
            ],
        'columnDefs': [ {
            'targets': [1,2,3,4,5], /* column index */
            'orderable': false, /* true or false */
        
         }],
         "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;	

        // Update footer by showing the total with the reference of the column index 
        $( api.column( 0 ).footer() ).html(grandTotals[0].total);
        
        $( api.column( 1 ).footer() ).html(commaSeparator((+grandTotals[0].imp).toFixed(2)));

        if($(api.column( 1 ).footer() ).hasClass("tooltipstered")){
            $(api.column( 1 ).footer() ).tooltipster('content', grandTotals[0].imp);
        }else{
            $(api.column( 1 ).footer() ).attr("title",grandTotals[0].imp);
        }

      
        
        $( api.column( 2 ).footer() ).html("$"+commaSeparator((+grandTotals[0].rev).toFixed(2)));
        if($(api.column( 2 ).footer() ).hasClass("tooltipstered")){
            $(api.column( 2 ).footer() ).tooltipster('content', "$"+grandTotals[0].rev);
        }else{
            $(api.column( 2 ).footer() ).attr("title","$"+grandTotals[0].rev);
        }

        
        $( api.column( 3 ).footer() ).html((+grandTotals[0].acos).toFixed(2)+"%");
        $( api.column( 4 ).footer() ).html((+grandTotals[0].ctr).toFixed(2)+"%");

        $( api.column( 5 ).footer() ).html("$"+commaSeparator((+grandTotals[0].cost).toFixed(2)));
        if($(api.column( 5 ).footer() ).hasClass("tooltipstered")){
            $(api.column( 5 ).footer() ).tooltipster('content', "$"+grandTotals[0].cost);
        }else{
            $(api.column( 5 ).footer() ).attr("title","$"+grandTotals[0].cost);
        }


        $('.strToolTip').tooltipster();
        },
        "drawCallback": function(settings) {
            $('.customTooltip').tooltipster({contentAsHTML: 'true',
            multiple: true,
            animation: 'grow',});
        }
    },
     );
     $( "div" ).remove( ".dataTables_length" );
}

/**
 * Targeting Type 
 */
function custom_DataCall(){
    $.ajax({
        method: "GET",
        data:filterData,
        // dataType: "json",
        url: customTypeDataUrl,
        // data: data,
        success: function(result){
            target_type_table(result.tableData,result.grandTotals);
            hidePreLoaders('#custom_table_Div');
        }
      });
    }

/**
 * Targeting Type Table
 */
function target_type_table(AdDataJson,grandTotals){
    $( "#trg_head" ).removeClass( "trCells" );
    target_dataTable = $('#table_trg_type').DataTable( {
        "data" : AdDataJson,
        "bFilter": false,
        "info":     false,
        "pageLength": 5,
        "scrollX": "100%",
        "sScrollY": "180px",
        "pagingType": "simple",
        "destroy": true,
        "ordering":true,
        'columns': [
            {data: 'tag'},
            { data: 'imp' , 
            render: function ( data, type, row ) {
                var tooltipAttr = '<div class="customTooltip" title="'+data+'">' + commaSeparator(+data) + '</div>'
                return tooltipAttr;
              }}, /* index = 0 */
            { data: 'rev' , 
            render: function ( data, type, row ) {
                var tooltipAttr = '<div class="customTooltip" title="'+"$"+data+'">' +"$" +commaSeparator(+data) + '</div>'
                return tooltipAttr;
              }}, /* index = 1 */
            { data: 'acos' }, /* index = 2 */
            { data: 'ctr' }, /* index = 4 */
            { data: 'cost' , 
            render: function ( data, type, row ) {
                var tooltipAttr = '<div class="customTooltip" title="'+"$"+data+'">' +"$" +commaSeparator(+data) + '</div>'
                return tooltipAttr;
              }}, /* index = 5 */
            ],
        'columnDefs': [ {
            'targets': [1,2,3,4,5], /* column index */
            'orderable': false, /* true or false */
         }],
         "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
        // Update footer by showing the total with the reference of the column index 
        $( api.column( 0 ).footer() ).html(grandTotals[0].total);

        $( api.column( 1 ).footer() ).html(commaSeparator((+grandTotals[0].imp).toFixed(2)));
        if($(api.column( 1 ).footer() ).hasClass("tooltipstered")){
            $(api.column( 1 ).footer() ).tooltipster('content', grandTotals[0].imp);
        }else{
            $(api.column( 1 ).footer() ).attr("title",grandTotals[0].imp);
        }

        

        $( api.column( 2 ).footer() ).html("$"+commaSeparator((+grandTotals[0].rev).toFixed(2)));
        if($(api.column( 2 ).footer() ).hasClass("tooltipstered")){
            $(api.column( 2 ).footer() ).tooltipster('content', "$"+grandTotals[0].rev);
        }else{
            $(api.column( 2 ).footer() ).attr("title","$"+grandTotals[0].rev);
        }

        
        $( api.column( 3 ).footer() ).html((+grandTotals[0].acos).toFixed(2)+"%");
        $( api.column( 4 ).footer() ).html((+grandTotals[0].ctr).toFixed(2)+"%");
        $( api.column( 5 ).footer() ).html("$"+commaSeparator((+grandTotals[0].cost).toFixed(2)));
        if($(api.column( 5 ).footer() ).hasClass("tooltipstered")){
            $(api.column( 5 ).footer() ).tooltipster('content', "$"+grandTotals[0].cost);
        }else{
            $(api.column( 5 ).footer() ).attr("title","$"+grandTotals[0].cost);  
        }


        $('.trgToolTip').tooltipster();
    },
        "drawCallback": function(settings) {
            $('.customTooltip').tooltipster({contentAsHTML: 'true',
            multiple:true,
            animation: 'grow',});
        }
    },
     );
    
     $( "div" ).remove( ".dataTables_length" );
}

/**
 * Product Type Datacall 
 */
function prodType_DataCall(){
    $.ajax({
        method: "GET",
        data:filterData,
        // dataType: "json",
        url: productTypeDataUrl,
        // data: data,
        success: function(result){
            prod_type_table(result.tableData,result.grandTotals);
            hidePreLoaders('#product_table_Div');
        }
      });
    }

/**
 * Product Type Table
 */
function prod_type_table(AdDataJson,grandTotals){
    $( "#prod_head" ).removeClass( "trCells" );
    product_dataTable = $('#table_prod_type').DataTable( {
        "data" : AdDataJson,
        "bFilter": false,
        "info":     false,
        "ordering":true,
        "scrollX": true,
        "destroy": true,
        "sScrollY": "180px",
        "pagingType": "simple",
        "pageLength": 5,
        'columns': [
            { data: 'tag' },
            { data: 'imp' , 
            render: function ( data, type, row ) {
                var tooltipAttr = '<div class="customTooltip" title="'+data+'">' + commaSeparator(+data) + '</div>'
                return tooltipAttr;
              }}, /* index = 0 */
            { data: 'rev' , 
            render: function ( data, type, row ) {
                var tooltipAttr = '<div class="customTooltip" title="'+"$"+data+'">'+"$" + commaSeparator(+data) + '</div>'
                return tooltipAttr;
              }}, /* index = 1 */
            { data: 'acos' }, /* index = 2 */
            { data: 'ctr' }, /* index = 4 */
            { data: 'cost' , 
            render: function ( data, type, row ) {
                var tooltipAttr = '<div class="customTooltip" title="'+"$"+data+'">' +"$"+ commaSeparator(+data) + '</div>'
                return tooltipAttr;
              }}, /* index = 5 */
            ],
        'columnDefs': [ {
            'targets': [1,2,3,4,5], /* column index */
            'orderable': false, /* true or false */
         }],
         "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;

            // Update footer by showing the total with the reference of the column index 
            $( api.column( 0 ).footer() ).html(grandTotals[0].total);

            $( api.column( 1 ).footer() ).html(commaSeparator((+grandTotals[0].imp).toFixed(2)));
            if($(api.column( 1 ).footer() ).hasClass("tooltipstered")){
                $(api.column( 1 ).footer() ).tooltipster('content', grandTotals[0].imp);
            }else{
                $(api.column( 1 ).footer() ).attr("title",grandTotals[0].imp);
            }
    
   


            $( api.column( 2 ).footer() ).html("$"+commaSeparator((+grandTotals[0].rev).toFixed(2)));

            if($(api.column( 2 ).footer() ).hasClass("tooltipstered")){
                $(api.column( 2 ).footer() ).tooltipster('content', "$"+grandTotals[0].rev);
            }else{
                $(api.column( 2 ).footer() ).attr("title","$"+grandTotals[0].rev);
            }


            $( api.column( 3 ).footer() ).html((+grandTotals[0].acos).toFixed(2)+"%");
            $( api.column( 4 ).footer() ).html((+grandTotals[0].ctr).toFixed(2)+"%");
           
            $( api.column( 5 ).footer() ).html("$"+commaSeparator((+grandTotals[0].cost).toFixed(2)));
           
            if($(api.column( 5 ).footer() ).hasClass("tooltipstered")){
                $(api.column( 5 ).footer() ).tooltipster('content', "$"+grandTotals[0].cost);
            }else{
                $(api.column( 5 ).footer() ).attr("title","$"+grandTotals[0].cost);
            }


            $('.prodToolTip').tooltipster();
        },
        "drawCallback": function(settings) {
            $('.customTooltip').tooltipster({contentAsHTML: 'true',
            multiple:true,
            animation: 'grow',});
        }
    },
     );
     $( "div" ).remove( ".dataTables_length" );
    }

/**
 * PerformancePre 30 DAYs 
 */
function perf_Pre30_DataCall(){
    $.ajax({
        method: "GET",
        data:filterData,
        // dataType: "json",
        url: pref_days_DataUrl,
        // data: data,
        success: function(result){
            perf_Pre30_table(result.tableData,result.grandTotals);
            hidePreLoaders('#perf_table_Div');
        }
      });
    }

/**
 * Performance 30  Table
 */
function perf_Pre30_table(AdDataJson,grandTotals){
    $( "#perf_head" ).removeClass( "trCells" );
    perf_dataTable = $('#table_perf_type').DataTable( {
        "data" : AdDataJson,
        "bFilter": false,
        "info":     false,
        "ordering":true,
        "destroy": true,
        "scrollX": true,
        "sScrollY": "180px",
        "pagingType": "simple",
        "pageLength": 5,
        "bDeferRender": true,
        'columns': [
            {data: 'account_name'},
            { data: 'revenue' , 
            render: function ( data, type, row ) {
                var tooltipAttr = '<div class="customTooltip" title="'+"$"+data+'">' +"$"+ commaSeparator(+data) + '</div>'
                return tooltipAttr;
              }}, /* index = 1 */
            { data: 'acos_' }, /* index = 2 */
            { data: 'cost' , 
            render: function ( data, type, row ) {
                var tooltipAttr = '<div class="customTooltip" title="'+"$"+data+'">'+"$" + commaSeparator(+data) + '</div>'
                return tooltipAttr;
              }}, /* index = 3 */
            ],
        'columnDefs': [ {
            'targets': [1,2,3], /* column index */
            'orderable': false, /* true or false */
         }],
         "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            if(grandTotals.length==0){
                // Update footer by showing the total with the reference of the column index 
                $( api.column( 0 ).footer() ).html("Grand Total");
                $( api.column( 1 ).footer() ).html("$0");
                if($(api.column( 1 ).footer() ).hasClass("tooltipstered")){
                    $(api.column( 1 ).footer() ).tooltipster('content', "$0");
                }else{
                    $(api.column( 1 ).footer() ).attr("title","$0");
                }


                $( api.column( 2 ).footer() ).html("0%");
                $( api.column( 3 ).footer() ).html("$0");
                if($(api.column( 3 ).footer() ).hasClass("tooltipstered")){
                    $(api.column( 3 ).footer() ).tooltipster('content', "$0");
                }else{
                    $(api.column( 3 ).footer() ).attr("title","$0");
                }
            }else{
                // Update footer by showing the total with the reference of the column index 
                $( api.column( 0 ).footer() ).html(grandTotals[0].grand_total);
                $( api.column( 1 ).footer() ).html("$"+commaSeparator((+grandTotals[0].revenue).toFixed(2)));
                if($(api.column( 1 ).footer() ).hasClass("tooltipstered")){
                    $(api.column( 1 ).footer() ).tooltipster('content', "$"+grandTotals[0].revenue);
                }else{
                    $(api.column( 1 ).footer() ).attr("title","$"+grandTotals[0].revenue);
                }


                $( api.column( 2 ).footer() ).html((+grandTotals[0].acos_).toFixed(2)+"%");
                $( api.column( 3 ).footer() ).html("$"+commaSeparator((+grandTotals[0].cost).toFixed(2)));
                if($(api.column( 3 ).footer() ).hasClass("tooltipstered")){
                    $(api.column( 3 ).footer() ).tooltipster('content', "$"+grandTotals[0].cost);
                }else{
                    $(api.column( 3 ).footer() ).attr("title","$"+grandTotals[0].cost);
                }
            }



            $('.perfToolTip').tooltipster();
        },
        "drawCallback": function(settings) {
            $('.customTooltip').tooltipster({contentAsHTML: 'true',
            multiple: true,
            animation: 'grow',});
        },
         "lengthMenu": [[4, 8,12, -1], [4, 8, 12, "All"]]
    },
     );
    
     $( "div" ).remove( ".dataTables_length" );
}


/**
 * Performance YTD Datacall 
 */
function perf_ytd_DataCall(){
    $.ajax({
        method: "GET",
        data:filterData,
        // dataType: "json",
        url: pref_ytd_DataUrl,
        // data: data,
        success: function(result){
            perf_ytd_table(result.tableData,result.grandTotals);
            hidePreLoaders('#perfYtd_table_Div');
        }
      });
}

/**
 * Perfomance YTD Table
 */
function perf_ytd_table(AdDataJson,grandTotals){
    $( "#YTD_head" ).removeClass( "trCells" );
    perf_ytd_dataTable = $('#table_perf_YTD_type').DataTable( {
        "data" : AdDataJson,
        "bFilter": false,
        "info":     false,
        "ordering":true,
        "scrollX": true,
        "sScrollY": "180px",
        "pagingType": "simple", 
        "pageLength": 5,
        "destroy": true,
        'columns': [
            {data: 'account_name'},
            { data: 'revenue' , 
            render: function ( data, type, row ) {
                var tooltipAttr = '<div class="customTooltip" title="'+"$"+data+'">' +"$" +commaSeparator(+data) + '</div>'
                return tooltipAttr;
              
              }}, /* index = 1 */
            { data: 'ACOS' }, /* index = 2 */
            { data: 'cost' , 
            render: function ( data, type, row ) {
                var tooltipAttr = '<div class="customTooltip" title="'+"$"+data+'">'+"$" + commaSeparator(+data) + '</div>'
                return tooltipAttr;
              }}, /* index = 3 */
            ],
        'columnDefs': [ {
            'targets': [1,2,3], /* column index */
            'orderable': false, /* true or false */
         }],
         "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
            // Update footer by showing the total with the reference of the column index 
            if(grandTotals.length >0){
                $( api.column( 0 ).footer() ).html(grandTotals[0].Grand_total);
                $( api.column( 1 ).footer() ).html("$"+commaSeparator((+grandTotals[0].revenue).toFixed(2)));    
                if($(api.column( 1 ).footer() ).hasClass("tooltipstered")){
                    $(api.column( 1 ).footer() ).tooltipster('content', "$"+grandTotals[0].revenue);
                }else{
                    $(api.column( 1 ).footer() ).attr("title","$"+grandTotals[0].revenue);
                }

                $( api.column( 2 ).footer() ).html((+grandTotals[0].acos_).toFixed(2)+"%");
           
                $( api.column( 3 ).footer() ).html("$"+commaSeparator((+grandTotals[0].cost).toFixed(2)));
                if($(api.column( 3 ).footer() ).hasClass("tooltipstered")){
                    $(api.column( 3 ).footer() ).tooltipster('content', "$"+grandTotals[0].cost);
                }else{
                    $(api.column( 3 ).footer() ).attr("title","$"+grandTotals[0].cost);
                }

            } else{
                $( api.column( 0 ).footer() ).html("Grand Total");
                $( api.column( 1 ).footer() ).html("$0"); 
                $( api.column( 2 ).footer() ).html("0%");
                $( api.column( 3 ).footer() ).html("$0");          
            }

            $('.ytdToolTip').tooltipster();
        },
        "drawCallback": function(settings) {
            $('.customTooltip').tooltipster({contentAsHTML: 'true',
            multiple: true,
            animation: 'grow',});
        }
    },
     );
     $( "div" ).remove( ".dataTables_length" );
}


/**
 * Top 10 Campaigns Datacall 
 */
function top_campaigns_DataCall(){
    $.ajax({
        method: "GET",
        data:filterData,
        // dataType: "json",
        url: top_campaigns_DataUrl,
        // data: data,
        success: function(result){
            max_meter = getMax_Meter(result,"spend");
            result = getConvertedResults(result,"acos")
            top_campaigns_table(result);
            hidePreLoaders('#topTen_table_Div');
        }
      });
    }

function stringToEl(string) {
    var parser = new DOMParser(),
        content = 'text/html',
        DOM = parser.parseFromString(string, content);
    // return element
    return DOM.body.childNodes[0];
}
/**
 * Top 10 CAMPAIGNS
 */
function top_campaigns_table(AdDataJson){
    $.fn.dataTableExt.oSort["customSort-desc"] = function (x, y) {   
        let newX = stringToEl(x);
        let newY = stringToEl(y);
        newX = newX.title
        newY = newY.title
        
        newX = newX.slice(1, newX.length);
        newY = newY.slice(1, newY.length);
        
        newX = showOrginialValue(+newX)
        newY = showOrginialValue(+newY)
        
        if ( +newX > +newY) {
            return -1;
        }
        return 1;

    };

    $.fn.dataTableExt.oSort["customSort-asc"] = function (x, y) {
        
        let newX = stringToEl(x);
        let newY = stringToEl(y);
        newX = newX.title
        newY = newY.title
        
        newX = newX.slice(1, newX.length);
        newY = newY.slice(1, newY.length);
        
        newX = showOrginialValue(+newX)
        newY = showOrginialValue(+newY)
        
        if ( +newX > +newY) {
            return 1;
        }
        return -1;
    }

    $.fn.dataTableExt.oSort["acosSort-desc"] = function (x, y) {   
        let newX,newY;
        
        if(isNaN(x)){
            newX = "00";
        }
        if(isNaN(y)){
            newY= "00";
        }

        newX = x.slice(0,x.length - 1);
        newY = y.slice(0,y.length - 1);
        
        if ( +newX > +newY) {
            return -1;
        }
        return 1;

    };

    $.fn.dataTableExt.oSort["acosSort-asc"] = function (x, y) {
        let newX,newY;
        
        if(isNaN(x)){
            newX = "00";
        }
        if(isNaN(y)){
            newY= "00";
        }

        
        newX = x.slice(0,x.length - 1);
        newY = y.slice(0,y.length - 1);
        if ( +newX > +newY) {
            return 1;
        }
        return -1;
    }

    
    top_campaigns_dataTable=$('#table_top10campaign').DataTable( {
        "data" : AdDataJson,
        "bFilter": false,
        "info":     false,
        "paging": false,
        "scrollX": true,
        "scrollY": "300px",
        "autoWidth": true,
        "ordering":true,
        "order": [[ 4, "asc" ]],
        "retrieve": true,   
        "deferRender":    true,  
        "stripeClasses": [],
        'columns': [
            {data: 'campaign_name'},
            { data: 'spend', 
            render: function ( data, type, row ) {
                let newstr = data.substr(1);
                var tooltipAttr = '<div class="customTooltip" title="'+data+'">' +"$"+ commaSeparator(+newstr) + '</div>'
                return tooltipAttr;
              }  
            }, /* index = 1 */
            { data: 'revenue' , 
            render: function ( data, type, row ) {
                let newstr = data.substr(1);
                var tooltipAttr = '<div class="customTooltip" title="'+data+'">' +"$"+ commaSeparator(+newstr) + '</div>'
                return tooltipAttr;
            }}, /* index = 2 */
            { data: 'acos_' }, /* index = 3 */
            {data: 'Rank_'}    /* index = 4 */
        ],
        'columnDefs': [ 
            {
                "targets": [1,2,3,4],
                "createdCell": function (td, cellData, rowData, row, col) {
                    $(td,cellData).tooltip()
                }
              }, 
         {
            "visible": false,
            "targets": [4],

        },{
            "targets":[1],
            className: 'dt-body-left'
        },
        { "type": "customSort", targets: [1,2] },
        {"type": "acosSort", targets: 3}
        ],
        "createdRow": function ( row, data, index ) {
            /**
             * Give color to the Revenue column
             */
            let color = helperRankBasedColor(+data.Rank_);
            $('td', row).eq(2).css("background-color", color);
            /**
             * bar meter of spend column 
             */
            var div = document.createElement("div");
            div.setAttribute("id", "bar_"+data.Rank_);
            let bar = +data.spend.substring(1);
            let meterBar = data.spend.length;
            meterBar = (max_meter.spend.length)-meterBar;
            let newMargin = max_meter.spend.length + meterBar;
            if(data.spend.length != max_meter){
                newMargin = newMargin+5;    
            }
            
            bar = 1+(bar*0.01);
            bar = bar * 0.9;
            div.style.width = bar+"px";
            div.style.height = "8px";
            div.style.position = "relative";
            div.style.marginLeft = newMargin+"%";
            div.style.background = "#5994f5";
            div.style.display = "inline-block";
            // div.style.textAlign = "right";
            div.style.float = "right";
            
            $('td', row).eq(1).append(div);
        },
        "drawCallback": function(settings) {
            $('.customTooltip').tooltipster({contentAsHTML: 'true',
            multiple: true,
            animation: 'grow',});
        }
    },
     );
     top_campaigns_dataTable.rows().every ( function () {
        
        var adata = this.cells( 0 ).data();
        this.cells( 0 ).data('xx' + adata);
      } );
    //  top_campaigns_dataTable.draw();
}


function showLoader(timer){
    //show loader
    $.LoadingOverlay("show",{
        size: 6,
    });
    
    // Hide it after 3 seconds
    setTimeout(function(){
        $.LoadingOverlay("hide");
    }, timer);
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
 * All reload call
 */
function reloadContent(){
    showLoader(500);
    getMetricsScoreCards();
    performanceChart();
    efficiencyChart();
    awarenessChart();
    Ad_type_DataCall();
    Strategy_DataCall();
    custom_DataCall();
    prodType_DataCall();
    perf_Pre30_DataCall();
    perf_ytd_DataCall();

    adtype_dataTable.columns.adjust().draw();
    strategy_dataTable.columns.adjust().draw();
    target_dataTable.columns.adjust().draw();
    perf_dataTable.columns.adjust().draw();
    perf_ytd_dataTable.columns.adjust().draw();
    product_dataTable.columns.adjust().draw();

}
/**
 * Refresh the content of the each class and based on its 
 * parent id show corresponding preloader 
 * 
 * @param {css class name attribute} className 
 */
function refreshContent(className){
    $(className).on("click", function () {
        id= $(this).attr('loader-id');
        showPreLoaders("#"+id);
        fn=$(this).attr('name');
        eval(fn);
    });
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

function commaFormat(x){
    let formatComma = d3.format(",");
    return formatComma(x);
}

function showOrginialValue(x){
    let originalFormat = d3.format("");
    return originalFormat(x);
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
            filterData.campaignId = values.toString();
            if(filterData.campaignId == ""){
                filterData.campaignId = null
                defaultMetricValues();
                $("#sectionA").addClass("defaultHide");
            }
            filterDataUpdate("campaignId",filterData.campaignId);
            CampaignAjaxCalls(filterData);
        } else if(values.length<2){
            $('.select2-selection').css("overflow-y","hidden");
        } else{
            filterData.campaignId = values.toString();
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
        let selectedCampaigns = $(this).val();
        if(selectedCampaigns.includes("All")) {
            selectedCampaigns="All";
        }
        
        filterData.campaignId = selectedCampaigns.toString();
        if(filterData.campaignId == ""){
            filterData.campaignId = null
            defaultMetricValues();
            $("#sectionA").addClass("defaultHide");
        }

        filterDataUpdate("campaignId",filterData.campaignId);
        CampaignAjaxCalls(filterData);
      });

}

function resetFilters(){
    $('input[name="datefilter"]').val('');
    filterDataUpdate("profileId",filterData.profileId);
    filterDataUpdate("campaignId",null);
    filterDataUpdate("startDate",null);
    filterDataUpdate("endDate",null);
}