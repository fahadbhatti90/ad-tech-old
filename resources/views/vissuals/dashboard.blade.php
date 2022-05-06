@extends('layout.app')
@extends('inc.side_menu')
@extends('inc.nav_bar')
@extends('modal.logout')
@section('title', isset($pageTitle)?$pageTitle:'Advertising Visuals')

@push('css')
    <!-- Load c3.css -->

    <link href="https://cdnjs.cloudflare.com/ajax/libs/c3/0.7.15/c3.min.css" rel="stylesheet">
    
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/tooltipster/3.3.0/css/tooltipster.min.css" />
    <link href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css" rel="stylesheet"/>
    <link href="{{asset('public/vendor/datatables/jquery.dataTables.min.css')}}" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="{{ asset('public/vendor/daterangepicker/daterangepicker.css') }}">  
    <link rel="stylesheet" href="{{ asset('public/css/select2.min.css?'.time())  }}"/>
    <!-- Load Material Icons  -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
    <!-- Load d3.js and c3.js -->
    <link rel="stylesheet" href="{{ asset('public/vissuals/css/chart.css?'.time())  }}"/>
    
    @endpush


@push('daterangepickerjs')
<script src="{{ asset('public/vendor/daterangepicker/moment.min.js') }}"></script>
@endpush

@section('content')
        <!-- /.container-fluid -->
        <!-- Begin Page Content -->
        <div class="container-fluid vissuals">
            <div class="row noSpace options">
                <!-- Earnings (Monthly) Card Example -->
                <div class="col-xl-12 col-md-12 mb-4">
                    <div class="card right_card shadow h-100">
                        <div class="card-body"> 
                            <!-- Charts Html -->
                            <div class="row noSpace options justify-content-center mb-3">
                                <div class="col py-2">
                                    <label class="labelFilter" for="select-profile">Select Child Brand</label>
                                    <select id="select-profile" class="selectpicker filtersHeight profileSelect" campaign-url = "{{ route('vissuals.campaigns') }}">
                                        <option value="" selected>Child Brand Name</option>
                                        @isset($profiles)
                                            @foreach ($profiles as $profile)
                                                <option value="{{ $profile->id."|".$profile->profileId }}">{{ 
                                                $profile->accounts != null ? 
                                                    ($profile->accounts->brand_alias != null &&
                                                    count($profile->accounts->brand_alias) > 0 ? 
                                                        
                                                        ($profile->accounts->brand_alias[0]->overrideLabel != null ? 
                                                        str_limit($profile->accounts->brand_alias[0]->overrideLabel,20):
                                                        str_limit($profile->name,20)) : 

                                                        str_limit($profile->name,20)):
                                                        str_limit($profile->name,20) }}</option>
                                            @endforeach
                                        @endisset
                                    </select>
                                </div>
                                <div class="col py-2">
                                    <label class="labelFilter" for="select-campaign">Select Campaigns</label>    
                                    <select id= "select-campaign" class="selectpicker filtersHeight campaignSelect"  multiple="multiple">
                                    </select>
                                </div>
                                <div class="col py-2">
                                    <label class="labelFilter" for="select-product">Select Product Type</label>
                                    <select id="select-product" class="selectpicker filtersHeight productSelect" tag-campaigns-url ="{{ route('vissuals.tagCampaigns') }}">
                                        <option value="" selected>Product Type</option>
                                    </select>
                                </div>

                                <div class="col py-2">
                                    <label class="labelFilter" for="select-strategy">Select Product Type</label>
                                    <select id="select-strategy" class="selectpicker filtersHeight strategySelect" tag-campaigns-url ="{{ route('vissuals.tagCampaigns') }}">
                                    <option value="" selected>Strategy Type</option>
                                    </select>
                                </div>

                                <div class="col py-2">
                                    <label class="labelFilter" for="select-date">Select Date Range</label>
                                    <input id="select-date" class="filtersHeight" placeholder="Select Date Range" type="text" name="datefilter" autocomplete="off" value="" />
                                </div>        
                                
                            </div>

                        
                            <!-- Metrics Boxes -->
                            <div class="metrics preLoader">
                                <!-- Border Boxes -->
                                <div class="row noSpace options mt-3">
                                    <!-- Impression box -->
                                    <div class="col">
                                        <fieldset class="border">
                                            <legend class ='text-center'>Impressions</legend>
                                                <p id="impressions_txt_box" class="metricTooltip innerPara">
                                                   
                                                </p>
                                        </fieldset>
                                    </div>

                                    <!-- Clicks box -->
                                    <div class="col">
                                        <fieldset class="border">
                                            <legend class ='text-center'>Clicks</legend>
                                                <p id="clicks_txt_box" class="innerPara">
                                                    
                                                </p>
                                        </fieldset>
                                    </div>

                                    <!-- CTR box -->
                                    <div class="col">
                                        <fieldset class="border">
                                            <legend class ='text-center'>CTR</legend>
                                                <p id="ctr_txt_box" class="innerPara">
                                                   
                                                </p>
                                        </fieldset>
                                    </div>

                                    <!-- CPC box -->
                                    <div class="col">
                                        <fieldset class="border">
                                            <legend class ='text-center'>CPC</legend>
                                                <p id="cpc_txt_box" class="metricTooltip innerPara">
                                                    
                                                </p>
                                        </fieldset>
                                    </div>

                                    <!-- Conversions box -->
                                    <div class="col">
                                        <fieldset class="border">
                                            <legend class ='text-center'>Conversions</legend>
                                                <p id="conversions_txt_box" class="metricTooltip innerPara">
                                                    
                                                </p>
                                        </fieldset>
                                    </div>
                                </div>
                                <div class="row noSpace options mt-3">
                                    <!-- Conversion ROI box -->
                                    <div class="col">
                                        <fieldset class="border">
                                            <legend class ='text-center'>CPA</legend>
                                                <p id="cpa_txt_box" class="innerPara" class="symbFont">
                                                   
                                                </p>
                                        </fieldset>
                                    </div>

                                    <!-- Spend box -->
                                    <div class="col">
                                        <fieldset class="border">
                                            <legend class ='text-center'>Spend</legend>
                                                <p id="spend_txt_box" class="metricTooltip innerPara" class="symbFont" >
                                                   
                                                </p>
                                        </fieldset>
                                    </div>

                                    <!-- Sales box -->
                                    <div class="col">
                                        <fieldset class="border">
                                            <legend class ='text-center'>Sales</legend>
                                                <p id="sales_txt_box" class="metricTooltip innerPara" class="symbFont" >
                                                 
                                                </p>
                                        </fieldset>
                                    </div>

                                    <!-- ACOS box -->
                                    <div class="col">
                                        <fieldset class="border">
                                            <legend class ='text-center'>ACOS</legend>
                                                <p id="acos_txt_box" class="innerPara" class="symbFont">
                                                </p>
                                        </fieldset>
                                    </div>

                                    <!-- ROAS box -->
                                    <div class="col">
                                        <fieldset class="border">
                                            <legend class ='text-center'>ROAS</legend>
                                                <p id="roas_txt_box" class="metricTooltip innerPara" class="symbFont" >
                                                   
                                                </p>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>



                    <!-- Charts Html -->
                    <div data-toggle="tooltip" id="tooltip"><span></span></div>
                    <div id="sectionA" class="row noSpace options defaultHide">
                        <div class="col py-2 mr-2 ml-2"> 

                                <!-- Saqib's embedded html starts here  -->
                                    <!-- Performance Graph  
                                            with 
                                        COST / ACOS and Rev
                                    -->
                                    <!-- <div class="row mt-3"> -->
                                        <div class="row mt-3 shadowCustom">
                                            <font class="font headings" color="#1f426e">
                                                PERFORMANCE
                                                <i name="performanceChart()" loader-id="performanceDiv" class="reloadIcon fas fa-redo-alt"></i>
                                            </font>
                                            <hr />
                                            <div id="performanceDiv" style="width: 100%; overflow: hidden;" class="preLoader" >
                                                <div id="perf-analysis" class="currencyDiv mt-2">   
                                                    <div>
                                                        <label class="labelData">Revenue</label>
                                                        <label id="perf-reven-currency" class="tooltip1 currency"></label>
                                                        <span class="spanCenter">
                                                            <svg id="perf-reven-svg" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="4 0 16 24" enable-background="new 0 0 24 24" style="vertical-align: middle; transform: scale(0.7, 0.8); fill: rgb(244, 67, 54); height: 12px;" x="0px" y="0px" ng-style="{'fill': compareDelta.deltaColor,
                                                                'height': $ctrl.svgHeight}" ng-if="compareDelta.deltaDirection === '-'" class="ng-scope">
                                                                <polygon points="19,14.3 16,14.3 16,11 8,11 8,14.3 5,14.3 12,21.3 "></polygon>
                                                                <rect x="8" y="7" width="8" height="3"></rect>
                                                                <polyline points="8,4 8,6 16,6 16,4 "></polyline>
                                                                <polyline points="8,2 8,3 16,3 16,2 "></polyline>
                                                                </svg>
                                                                <label class="" id="perf-revenue-lbl"></label>
                                                        </span> 
                                                    </div>
                                                    
                                                    <div>
                                                        <label class="labelData">Cost</label> 
                                                        <label id="perf-cost-currency" class="tooltip2 currency"></label> 
                                                        <span class="spanCenter">
                                                            <svg id="perf-cost-svg" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="4 0 16 24" enable-background="new 0 0 24 24" style="vertical-align: middle; transform: scale(0.7, 0.8); fill: rgb(244, 67, 54); height: 12px;" x="0px" y="0px" ng-style="{'fill': compareDelta.deltaColor,
                                                                'height': $ctrl.svgHeight}" ng-if="compareDelta.deltaDirection === '-'" class="ng-scope">
                                                                <polygon points="19,14.3 16,14.3 16,11 8,11 8,14.3 5,14.3 12,21.3 "></polygon>
                                                                <rect x="8" y="7" width="8" height="3"></rect>
                                                                <polyline points="8,4 8,6 16,6 16,4 "></polyline>
                                                                <polyline points="8,2 8,3 16,3 16,2 "></polyline>
                                                            </svg>
                                                            <label id="perf-cost-lbl"></label>
                                                        </span>  
                                                    </div>
                                                    
                                                    <div>
                                                        <label class="labelData">ACOS</label> 
                                                        <label id="perf-acos-currency" class="currency"></label> 
                                                        <span class="spanCenter">
                                                            <svg id="perf-acos-svg" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="4 0 16 24" enable-background="new 0 0 24 24" style="vertical-align: middle; transform: scale(0.7, 0.8); fill: rgb(76, 175, 80); height: 12px;" x="0px" y="0px" ng-style="{'fill': compareDelta.deltaColor,
                                                            'height': $ctrl.svgHeight}" ng-if="compareDelta.deltaDirection === '-'" class="ng-scope">
                                                                <polygon points="19,14.3 16,14.3 16,11 8,11 8,14.3 5,14.3 12,21.3 "></polygon>
                                                                <rect x="8" y="7" width="8" height="3"></rect>
                                                                <polyline points="8,4 8,6 16,6 16,4 "></polyline>
                                                                <polyline points="8,2 8,3 16,3 16,2 "></polyline>
                                                            </svg>
                                                            <label id="perf-acos-lbl"></label>
                                                        </span>   
                                                    </div>
                                                </div>
                                                <div  id="chart1" class="cloud"  width="90%">  </div>
                                            </div>
                                        </div>
                                        <!-- Efficiency Graph  
                                                with 
                                            CPA / CPC and ROAS
                                        -->
                                        <div class="row mt-3 shadowCustom">
                                            <font class="font headings" color="#1f426e">
                                                EFFICIENCY
                                                <i name="efficiencyChart()" loader-id="efficiencyDiv" class="reloadIcon fas fa-redo-alt"></i>
                                            </font>
                                            <hr />
                                            <div id="efficiencyDiv" style="width: 100%; overflow: hidden;" class="preLoader">
                                                <div id="analysisDiv2" class="currencyDiv mt-2">   
                                                    <div>
                                                        <label class="labelData">CPC</label>
                                                        <label id="effi-cpc-currency" class="tooltip3 currency"></label>
                                                        <span class="spanCenter">
                                                            <svg id="effi-cpc-svg" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="4 0 16 24" enable-background="new 0 0 24 24" style="vertical-align: middle; transform: scale(0.7, 0.8); fill: rgb(244, 67, 54); height: 12px;" x="0px" y="0px" ng-style="{'fill': compareDelta.deltaColor,
                                                                'height': $ctrl.svgHeight}" ng-if="compareDelta.deltaDirection === '-'" class="ng-scope">
                                                                <polygon points="19,14.3 16,14.3 16,11 8,11 8,14.3 5,14.3 12,21.3 "></polygon>
                                                                <rect x="8" y="7" width="8" height="3"></rect>
                                                                <polyline points="8,4 8,6 16,6 16,4 "></polyline>
                                                                <polyline points="8,2 8,3 16,3 16,2 "></polyline>
                                                                </svg>
                                                                <label id="effi-cpc-lbl"></label>
                                                        </span> 
                                                    </div>
                                                    
                                                    <div>
                                                        <label class="labelData">ROAS</label> 
                                                        <label id= "effi-roas-currency" class="tooltip4 currency"></label> 
                                                        <span class="spanCenter">
                                                            <svg id="effi-roas-svg" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="4 0 16 24" enable-background="new 0 0 24 24" style="vertical-align: middle; transform: scale(0.7, 0.8); fill: rgb(244, 67, 54); height: 12px;" x="0px" y="0px" ng-style="{'fill': compareDelta.deltaColor,
                                                                'height': $ctrl.svgHeight}" ng-if="compareDelta.deltaDirection === '-'" class="ng-scope">
                                                                <polygon points="19,14.3 16,14.3 16,11 8,11 8,14.3 5,14.3 12,21.3 "></polygon>
                                                                <rect x="8" y="7" width="8" height="3"></rect>
                                                                <polyline points="8,4 8,6 16,6 16,4 "></polyline>
                                                                <polyline points="8,2 8,3 16,3 16,2 "></polyline>
                                                            </svg>
                                                            <label id="effi-roas-lbl"></label>
                                                        </span>  
                                                    </div>
                                                    
                                                    <div>
                                                        <label class="labelData">CPA</label> 
                                                        <label id="effi-cpa-currency" class="tooltip5 currency"></label> 
                                                        <span class="spanCenter">
                                                            <svg id="effi-cpa-svg" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="4 0 16 24" enable-background="new 0 0 24 24" style="vertical-align: middle; transform: scale(0.7, 0.8); fill: rgb(76, 175, 80); height: 12px;" x="0px" y="0px" ng-style="{'fill': compareDelta.deltaColor,
                                                            'height': $ctrl.svgHeight}" ng-if="compareDelta.deltaDirection === '-'" class="ng-scope">
                                                            <polygon points="19,14.3 16,14.3 16,11 8,11 8,14.3 5,14.3 12,21.3 "></polygon>
                                                            <rect x="8" y="7" width="8" height="3"></rect>
                                                            <polyline points="8,4 8,6 16,6 16,4 "></polyline>
                                                            <polyline points="8,2 8,3 16,3 16,2 "></polyline>
                                                        </svg>
                                                        <label id="effi-cpa-lbl"></label>
                                                        </span>   
                                                    </div>
                                                </div>
                                                <div  id="chart2" class="cloud" width="90%">  </div>
                                            </div>
                                        </div>
                                        <!-- Awareness Graph
                                                with
                                            -->
                                        <div class="row mt-3 shadowCustom">
                                            <font class="font headings" color="#1f426e">
                                                AWARENESS
                                                <i name="awarenessChart()" loader-id="awarenessDiv" class="reloadIcon fas fa-redo-alt"></i>
                                            </font>
                                            <hr />
                                            <div id="awarenessDiv" style="width: 100%;overflow: hidden;" class="preLoader" >
                                                <div id="analysisDiv" class="currencyDiv mt-2">   
                                                    <div>
                                                        <label class="labelData">Impressions</label>
                                                        <label id="awar-impre-currency" class="tooltip6 currency"></label>
                                                        <span class="spanCenter">
                                                            <svg id="awar-impre-svg" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="4 0 16 24" enable-background="new 0 0 24 24" style="vertical-align: middle; transform: scale(0.7, 0.8); fill: rgb(244, 67, 54); height: 12px;" x="0px" y="0px" ng-style="{'fill': compareDelta.deltaColor,
                                                                'height': $ctrl.svgHeight}" ng-if="compareDelta.deltaDirection === '-'" class="ng-scope">
                                                                <polygon points="19,14.3 16,14.3 16,11 8,11 8,14.3 5,14.3 12,21.3 "></polygon>
                                                                <rect x="8" y="7" width="8" height="3"></rect>
                                                                <polyline points="8,4 8,6 16,6 16,4 "></polyline>
                                                                <polyline points="8,2 8,3 16,3 16,2 "></polyline>
                                                                </svg>
                                                                <label id="awar-impre-lbl"></label>
                                                        </span> 
                                                    </div>
                                                    
                                                    <div>
                                                        <label class="labelData">Clicks</label> 
                                                        <label id="awar-clk-currency" class="currency"></label> 
                                                        <span class="spanCenter">
                                                            <svg id="awar-clk-svg" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="4 0 16 24" enable-background="new 0 0 24 24" style="vertical-align: middle; transform: scale(0.7, 0.8); fill: rgb(244, 67, 54); height: 12px;" x="0px" y="0px" ng-style="{'fill': compareDelta.deltaColor,
                                                                'height': $ctrl.svgHeight}" ng-if="compareDelta.deltaDirection === '-'" class="ng-scope">
                                                                <polygon points="19,14.3 16,14.3 16,11 8,11 8,14.3 5,14.3 12,21.3 "></polygon>
                                                                <rect x="8" y="7" width="8" height="3"></rect>
                                                                <polyline points="8,4 8,6 16,6 16,4 "></polyline>
                                                                <polyline points="8,2 8,3 16,3 16,2 "></polyline>
                                                                </svg>
                                                                <label id="awar-clk-lbl"></label>
                                                        </span>  
                                                    </div>
                                                    
                                                    <div>
                                                        <label class="labelData">CTR.</label> 
                                                        <label id="awar-ctr-currency" class="currency"></label> 
                                                        <span class="spanCenter">
                                                            <svg id="awar-ctr-svg" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="4 0 16 24" enable-background="new 0 0 24 24" style="vertical-align: middle; transform: scale(0.7, 0.8); fill: rgb(76, 175, 80); height: 12px;" x="0px" y="0px" ng-style="{'fill': compareDelta.deltaColor,
                                                            'height': $ctrl.svgHeight}" ng-if="compareDelta.deltaDirection === '-'" class="ng-scope">
                                                            <polygon points="19,14.3 16,14.3 16,11 8,11 8,14.3 5,14.3 12,21.3 "></polygon>
                                                            <rect x="8" y="7" width="8" height="3"></rect>
                                                            <polyline points="8,4 8,6 16,6 16,4 "></polyline>
                                                            <polyline points="8,2 8,3 16,3 16,2 "></polyline>
                                                        </svg>
                                                        <label id="awar-ctr-lbl"></label>
                                                        </span>   
                                                    </div>
                                                </div>
                                                <div  id="chart3" class="cloud" width="90%">  </div>
                                            </div>
                                        </div>
                                    <!-- </div> -->
                                    <!-- MOM, WOW,DOD Row -->                        
                                    <div class="row justify-content-center mt-3 shadowCustom">
                                    <!-- Month Over Month Info  -->
                                        <div id="momDiv" class="col-sm-4  mt-2 preLoader boxHeight">
                                            <font class="hText_Res" color="#1f426e">
                                                MONTH OVER MONTH <font class="shText_Res">​(mtd vs last mtd)</font>
                                                <i name="populateMOM()" loader-id="momDiv" class="reloadIcon-sm fas fa-redo-alt"></i>
                                            </font>
                                            <hr />
                                            <div class="row align-items-center over-div">
                                                <div id="momFlex1" class="col flexProp">
                                                    <div>
                                                        <label class="labelData">Impressions</label> 
                                                        <label id="mom-impr-currency" class="currency"></label> 
                                                        <span class="spanCenter">
                                                            <svg id="mom-impr-svg" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="4 0 16 24" enable-background="new 0 0 24 24" style="vertical-align: middle; transform: scale(0.7, 0.8); fill: rgb(76, 175, 80); height: 12px;" x="0px" y="0px" ng-style="{'fill': compareDelta.deltaColor,
                                                            'height': $ctrl.svgHeight}" ng-if="compareDelta.deltaDirection === '-'" class="ng-scope">
                                                                <polygon points="19,14.3 16,14.3 16,11 8,11 8,14.3 5,14.3 12,21.3 "></polygon>
                                                                <rect x="8" y="7" width="8" height="3"></rect>
                                                                <polyline points="8,4 8,6 16,6 16,4 "></polyline>
                                                                <polyline points="8,2 8,3 16,3 16,2 "></polyline>
                                                            </svg>
                                                            <label id="mom-impr-lbl"></label>
                                                        </span>
                                                    </div>
                                                
                                                    <div>
                                                        <label class="labelData">Cost</label> 
                                                        <label id="mom-cost-currency" class="currency"></label> 
                                                        <span class="spanCenter">
                                                            <svg id="mom-cost-svg" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="4 0 16 24" enable-background="new 0 0 24 24" style="vertical-align: middle; transform: scale(0.7, 0.8); fill: rgb(76, 175, 80); height: 12px;" x="0px" y="0px" ng-style="{'fill': compareDelta.deltaColor,
                                                            'height': $ctrl.svgHeight}" ng-if="compareDelta.deltaDirection === '-'" class="ng-scope">
                                                                <polygon points="19,14.3 16,14.3 16,11 8,11 8,14.3 5,14.3 12,21.3 "></polygon>
                                                                <rect x="8" y="7" width="8" height="3"></rect>
                                                                <polyline points="8,4 8,6 16,6 16,4 "></polyline>
                                                                <polyline points="8,2 8,3 16,3 16,2 "></polyline>
                                                            </svg>
                                                            <label id="mom-cost-lbl"></label>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <label class="labelData">Rev</label> 
                                                            <label id="mom-rev-currency" class="currency"></label> 
                                                            <span class="spanCenter">
                                                                <svg id="mom-rev-svg" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="4 0 16 24" enable-background="new 0 0 24 24" style="vertical-align: middle; transform: scale(0.7, 0.8); fill: rgb(76, 175, 80); height: 12px;" x="0px" y="0px" ng-style="{'fill': compareDelta.deltaColor,
                                                                'height': $ctrl.svgHeight}" ng-if="compareDelta.deltaDirection === '-'" class="ng-scope">
                                                                    <polygon points="19,14.3 16,14.3 16,11 8,11 8,14.3 5,14.3 12,21.3 "></polygon>
                                                                    <rect x="8" y="7" width="8" height="3"></rect>
                                                                    <polyline points="8,4 8,6 16,6 16,4 "></polyline>
                                                                    <polyline points="8,2 8,3 16,3 16,2 "></polyline>
                                                                </svg>
                                                                <label id="mom-rev-lbl"></label>
                                                            </span>
                                                    </div>
                                                
                                                </div>
                                            
                                                <div id="momFlex2" class="col flexProp">
                                                    <div>
                                                        <label class="labelData">ACOS</label> 
                                                        <label id="mom-acos-currency" class="currency"></label> 
                                                        <span class="spanCenter">
                                                            <svg id="mom-acos-svg" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="4 0 16 24" enable-background="new 0 0 24 24" style="vertical-align: middle; transform: scale(0.7, 0.8); fill: rgb(76, 175, 80); height: 12px;" x="0px" y="0px" ng-style="{'fill': compareDelta.deltaColor,
                                                            'height': $ctrl.svgHeight}" ng-if="compareDelta.deltaDirection === '-'" class="ng-scope">
                                                            <polygon points="19,14.3 16,14.3 16,11 8,11 8,14.3 5,14.3 12,21.3 "></polygon>
                                                            <rect x="8" y="7" width="8" height="3"></rect>
                                                            <polyline points="8,4 8,6 16,6 16,4 "></polyline>
                                                            <polyline points="8,2 8,3 16,3 16,2 "></polyline>
                                                        </svg>
                                                        <label id="mom-acos-lbl"></label>
                                                    </div>

                                                    <div>
                                                        <label class="labelData">CPC</label> 
                                                        <label id="mom-cpc-currency" class="currency"></label> 
                                                        <span class="spanCenter">
                                                            <svg id="mom-cpc-svg" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="4 0 16 24" enable-background="new 0 0 24 24" style="vertical-align: middle; transform: scale(0.7, 0.8); fill: rgb(76, 175, 80); height: 12px;" x="0px" y="0px" ng-style="{'fill': compareDelta.deltaColor,
                                                            'height': $ctrl.svgHeight}" ng-if="compareDelta.deltaDirection === '-'" class="ng-scope">
                                                            <polygon points="19,14.3 16,14.3 16,11 8,11 8,14.3 5,14.3 12,21.3 "></polygon>
                                                            <rect x="8" y="7" width="8" height="3"></rect>
                                                            <polyline points="8,4 8,6 16,6 16,4 "></polyline>
                                                            <polyline points="8,2 8,3 16,3 16,2 "></polyline>
                                                        </svg>
                                                        <label id="mom-cpc-lbl"></label>
                                                    </div>

                                                <div>
                                                    <label class="labelData">ROAS</label> 
                                                    <label id="mom-roas-currency" class="currency"></label> 
                                                    <span class="spanCenter">
                                                        <svg id="mom-roas-svg" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="4 0 16 24" enable-background="new 0 0 24 24" style="vertical-align: middle; transform: scale(0.7, 0.8); fill: rgb(76, 175, 80); height: 12px;" x="0px" y="0px" ng-style="{'fill': compareDelta.deltaColor,
                                                        'height': $ctrl.svgHeight}" ng-if="compareDelta.deltaDirection === '-'" class="ng-scope">
                                                        <polygon points="19,14.3 16,14.3 16,11 8,11 8,14.3 5,14.3 12,21.3 "></polygon>
                                                        <rect x="8" y="7" width="8" height="3"></rect>
                                                        <polyline points="8,4 8,6 16,6 16,4 "></polyline>
                                                        <polyline points="8,2 8,3 16,3 16,2 "></polyline>
                                                    </svg>
                                                    <label id="mom-roas-lbl"></label>
                                                </div>
                                            </div>
                                            
                                            </div>
                                        </div>
                            
                                        <!-- Week Over Week Info  -->
                                        <div id="wowDiv" class="col-sm-4 mt-2 preLoader boxHeight">
                                            <font class="hText_Res" color="#1f426e">
                                                WEEK OVER WEEK 
                                                <font class="shText_Res">​(last 7 days vs previous)</font>
                                                <i name="populateWOW()" loader-id="wowDiv" class="reloadIcon-sm fas fa-redo-alt"></i>
                                            </font>
                                            <hr />
                                                <div class="row align-items-center over-div">
                                                    <div id="wowFlex1" class="col flexProp">
                                                    <div>
                                                        <label class="labelData">Impressions</label> 
                                                        <label id="wow-impr-currency" class="currency"></label> 
                                                        <span class="spanCenter">
                                                            <svg id="wow-impr-svg" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="4 0 16 24" enable-background="new 0 0 24 24" style="vertical-align: middle; transform: scale(0.7, 0.8); fill: rgb(76, 175, 80); height: 12px;" x="0px" y="0px" ng-style="{'fill': compareDelta.deltaColor,
                                                            'height': $ctrl.svgHeight}" ng-if="compareDelta.deltaDirection === '-'" class="ng-scope">
                                                            <polygon points="19,14.3 16,14.3 16,11 8,11 8,14.3 5,14.3 12,21.3 "></polygon>
                                                            <rect x="8" y="7" width="8" height="3"></rect>
                                                            <polyline points="8,4 8,6 16,6 16,4 "></polyline>
                                                            <polyline points="8,2 8,3 16,3 16,2 "></polyline>
                                                            </svg>
                                                            <label id="wow-impr-lbl"></label>
                                                        </span>
                                                    </div>

                                                    <div>
                                                        <label class="labelData">Cost</label> 
                                                        <label id="wow-cost-currency" class="currency"></label> 
                                                        <span class="spanCenter">
                                                            <svg id="wow-cost-svg" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="4 0 16 24" enable-background="new 0 0 24 24" style="vertical-align: middle; transform: scale(0.7, 0.8); fill: rgb(76, 175, 80); height: 12px;" x="0px" y="0px" ng-style="{'fill': compareDelta.deltaColor,
                                                            'height': $ctrl.svgHeight}" ng-if="compareDelta.deltaDirection === '-'" class="ng-scope">
                                                            <polygon points="19,14.3 16,14.3 16,11 8,11 8,14.3 5,14.3 12,21.3 "></polygon>
                                                            <rect x="8" y="7" width="8" height="3"></rect>
                                                            <polyline points="8,4 8,6 16,6 16,4 "></polyline>
                                                            <polyline points="8,2 8,3 16,3 16,2 "></polyline>
                                                            </svg>
                                                            <label id="wow-cost-lbl"></label>
                                                        </span>
                                                    </div>

                                                    <div>
                                                        <label class="labelData">Rev</label> 
                                                        <label id="wow-rev-currency" class="currency"></label> 
                                                        <span class="spanCenter">
                                                            <svg id="wow-rev-svg" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="4 0 16 24" enable-background="new 0 0 24 24" style="vertical-align: middle; transform: scale(0.7, 0.8); fill: rgb(76, 175, 80); height: 12px;" x="0px" y="0px" ng-style="{'fill': compareDelta.deltaColor,
                                                            'height': $ctrl.svgHeight}" ng-if="compareDelta.deltaDirection === '-'" class="ng-scope">
                                                            <polygon points="19,14.3 16,14.3 16,11 8,11 8,14.3 5,14.3 12,21.3 "></polygon>
                                                            <rect x="8" y="7" width="8" height="3"></rect>
                                                            <polyline points="8,4 8,6 16,6 16,4 "></polyline>
                                                            <polyline points="8,2 8,3 16,3 16,2 "></polyline>
                                                            </svg>
                                                            <label id="wow-rev-lbl"></label>
                                                        </span>
                                                    </div>
                                                </div>
                                                
                                                    <div id="wowFlex2" class="col flexProp">
                                                    
                                                    <div>
                                                        <label class="labelData">ACOS</label> 
                                                        <label id="wow-acos-currency" class="currency"></label> 
                                                        <span class="spanCenter">
                                                            <svg id="wow-acos-svg" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="4 0 16 24" enable-background="new 0 0 24 24" style="vertical-align: middle; transform: scale(0.7, 0.8); fill: rgb(76, 175, 80); height: 12px;" x="0px" y="0px" ng-style="{'fill': compareDelta.deltaColor,
                                                            'height': $ctrl.svgHeight}" ng-if="compareDelta.deltaDirection === '-'" class="ng-scope">
                                                            <polygon points="19,14.3 16,14.3 16,11 8,11 8,14.3 5,14.3 12,21.3 "></polygon>
                                                            <rect x="8" y="7" width="8" height="3"></rect>
                                                            <polyline points="8,4 8,6 16,6 16,4 "></polyline>
                                                            <polyline points="8,2 8,3 16,3 16,2 "></polyline>
                                                            </svg>
                                                            <label id="wow-acos-lbl"></label>
                                                        </span>
                                                    </div>
                                                    
                                                    <div>
                                                        <label class="labelData">CPC</label> 
                                                        <label id="wow-cpc-currency" class="currency"></label> 
                                                        <span class="spanCenter">
                                                            <svg id="wow-cpc-svg" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="4 0 16 24" enable-background="new 0 0 24 24" style="vertical-align: middle; transform: scale(0.7, 0.8); fill: rgb(76, 175, 80); height: 12px;" x="0px" y="0px" ng-style="{'fill': compareDelta.deltaColor,
                                                            'height': $ctrl.svgHeight}" ng-if="compareDelta.deltaDirection === '-'" class="ng-scope">
                                                            <polygon points="19,14.3 16,14.3 16,11 8,11 8,14.3 5,14.3 12,21.3 "></polygon>
                                                            <rect x="8" y="7" width="8" height="3"></rect>
                                                            <polyline points="8,4 8,6 16,6 16,4 "></polyline>
                                                            <polyline points="8,2 8,3 16,3 16,2 "></polyline>
                                                            </svg>
                                                            <label id="wow-cpc-lbl"></label>
                                                        </span>
                                                    </div>

                                                    <div>
                                                        <label class="labelData">ROAS</label> 
                                                        <label id="wow-roas-currency" class="currency"></label> 
                                                        <span class="spanCenter">
                                                            <svg id="wow-roas-svg" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="4 0 16 24" enable-background="new 0 0 24 24" style="vertical-align: middle; transform: scale(0.7, 0.8); fill: rgb(76, 175, 80); height: 12px;" x="0px" y="0px" ng-style="{'fill': compareDelta.deltaColor,
                                                            'height': $ctrl.svgHeight}" ng-if="compareDelta.deltaDirection === '-'" class="ng-scope">
                                                            <polygon points="19,14.3 16,14.3 16,11 8,11 8,14.3 5,14.3 12,21.3 "></polygon>
                                                            <rect x="8" y="7" width="8" height="3"></rect>
                                                            <polyline points="8,4 8,6 16,6 16,4 "></polyline>
                                                            <polyline points="8,2 8,3 16,3 16,2 "></polyline>
                                                            </svg>
                                                            <label id="wow-roas-lbl"></label>
                                                        </span>    
                                                    </div>
                                                </div>
                                                
                                                </div>
                                        </div>
                                        
                                        <!-- Day Over Day Info  -->
                                        <div id="dodDiv" class="col-sm-4 mt-2 preLoader boxHeight">
                                            <font class="hText_Res" color="#1f426e">
                                                DAY OVER DAY 
                                                <font class="shText_Res">​(yesterday vs prior day)</font>
                                                <i name="populateDOD()" loader-id="dodDiv" class="reloadIcon-sm fas fa-redo-alt"></i>
                                            </font>
                                            <hr />
                                            <div class="row align-items-center over-div">
                                                <div id="dodFlex1" class="col flexProp">
                                                    <div>
                                                        <label class="labelData">Impressions</label> 
                                                        <label id="dod-impr-currency" class="currency"></label> 
                                                        <span class="spanCenter">
                                                            <svg id="dod-impr-svg" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="4 0 16 24" enable-background="new 0 0 24 24" style="vertical-align: middle; transform: scale(0.7, 0.8); fill: rgb(76, 175, 80); height: 12px;" x="0px" y="0px" ng-style="{'fill': compareDelta.deltaColor,
                                                            'height': $ctrl.svgHeight}" ng-if="compareDelta.deltaDirection === '-'" class="ng-scope">
                                                            <polygon points="19,14.3 16,14.3 16,11 8,11 8,14.3 5,14.3 12,21.3 "></polygon>
                                                            <rect x="8" y="7" width="8" height="3"></rect>
                                                            <polyline points="8,4 8,6 16,6 16,4 "></polyline>
                                                            <polyline points="8,2 8,3 16,3 16,2 "></polyline>
                                                            </svg>
                                                            <label id="dod-impr-lbl"></label>
                                                        </span>
                                                        </div>
                                                    <div>
                                                        <label class="labelData">Cost</label> 
                                                        <label id="dod-cost-currency" class="currency"></label> 
                                                        <span class="spanCenter">
                                                            <svg id="dod-cost-svg" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="4 0 16 24" enable-background="new 0 0 24 24" style="vertical-align: middle; transform: scale(0.7, 0.8); fill: rgb(76, 175, 80); height: 12px;" x="0px" y="0px" ng-style="{'fill': compareDelta.deltaColor,
                                                            'height': $ctrl.svgHeight}" ng-if="compareDelta.deltaDirection === '-'" class="ng-scope">
                                                            <polygon points="19,14.3 16,14.3 16,11 8,11 8,14.3 5,14.3 12,21.3 "></polygon>
                                                            <rect x="8" y="7" width="8" height="3"></rect>
                                                            <polyline points="8,4 8,6 16,6 16,4 "></polyline>
                                                            <polyline points="8,2 8,3 16,3 16,2 "></polyline>
                                                            </svg>
                                                            <label id="dod-cost-lbl"></label>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <label class="labelData">Rev</label> 
                                                        <label id="dod-rev-currency" class="currency"></label> 
                                                        <span class="spanCenter">
                                                            <svg id="dod-rev-svg" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="4 0 16 24" enable-background="new 0 0 24 24" style="vertical-align: middle; fill: rgb(76, 175, 80); height: 12px;" x="0px" y="0px" ng-style="{'fill': compareDelta.deltaColor,
                                                            'height': $ctrl.svgHeight}" ng-if="compareDelta.deltaDirection === '-'" class="ng-scope">
                                                            <polygon points="19,14.3 16,14.3 16,11 8,11 8,14.3 5,14.3 12,21.3 "></polygon>
                                                            <rect x="8" y="7" width="8" height="3"></rect>
                                                            <polyline points="8,4 8,6 16,6 16,4 "></polyline>
                                                            <polyline points="8,2 8,3 16,3 16,2 "></polyline>
                                                            </svg>
                                                            <label id="dod-rev-lbl"></label>
                                                        </span>
                                                        </div>
                                                    </div>
                                            
                                                <div id="dodFlex2" class="col flexProp">
                                                    <div>
                                                        <label class="labelData">ACOS</label> 
                                                        <label id="dod-acos-currency" class="currency"></label> 
                                                        <span class="spanCenter">
                                                            <svg id="dod-acos-svg" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="4 0 16 24" enable-background="new 0 0 24 24" style="vertical-align: middle; fill: rgb(76, 175, 80); height: 12px;" x="0px" y="0px" ng-style="{'fill': compareDelta.deltaColor,
                                                            'height': $ctrl.svgHeight}" ng-if="compareDelta.deltaDirection === '-'" class="ng-scope">
                                                            <polygon points="19,14.3 16,14.3 16,11 8,11 8,14.3 5,14.3 12,21.3 "></polygon>
                                                            <rect x="8" y="7" width="8" height="3"></rect>
                                                            <polyline points="8,4 8,6 16,6 16,4 "></polyline>
                                                            <polyline points="8,2 8,3 16,3 16,2 "></polyline>
                                                            </svg>
                                                            <label id="dod-acos-lbl"></label>
                                                        </span>
                                                    </div>
                                                    <div>    
                                                        <label class="labelData">CPC</label> 
                                                        <label id="dod-cpc-currency" class="currency"></label> 
                                                        <span class="spanCenter">
                                                            <svg id="dod-cpc-svg" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="4 0 16 24" enable-background="new 0 0 24 24" style="vertical-align: middle; fill: rgb(76, 175, 80); height: 12px;" x="0px" y="0px" ng-style="{'fill': compareDelta.deltaColor,
                                                            'height': $ctrl.svgHeight}" ng-if="compareDelta.deltaDirection === '-'" class="ng-scope">
                                                            <polygon points="19,14.3 16,14.3 16,11 8,11 8,14.3 5,14.3 12,21.3 "></polygon>
                                                            <rect x="8" y="7" width="8" height="3"></rect>
                                                            <polyline points="8,4 8,6 16,6 16,4 "></polyline>
                                                            <polyline points="8,2 8,3 16,3 16,2 "></polyline>
                                                            </svg>
                                                            <label id="dod-cpc-lbl"></label>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <label class="labelData">ROAS</label> 
                                                        <label id="dod-roas-currency" class="currency"></label> 
                                                        <span class="spanCenter">
                                                            <svg id="dod-roas-svg" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="4 0 16 24" enable-background="new 0 0 24 24" style="vertical-align: middle; fill: rgb(76, 175, 80); height: 12px;" x="0px" y="0px" ng-style="{'fill': compareDelta.deltaColor,
                                                            'height': $ctrl.svgHeight}" ng-if="compareDelta.deltaDirection === '-'" class="ng-scope">
                                                            <polygon points="19,14.3 16,14.3 16,11 8,11 8,14.3 5,14.3 12,21.3 "></polygon>
                                                            <rect x="8" y="7" width="8" height="3"></rect>
                                                            <polyline points="8,4 8,6 16,6 16,4 "></polyline>
                                                            <polyline points="8,2 8,3 16,3 16,2 "></polyline>
                                                            </svg>
                                                            <label id="dod-roas-lbl"></label>
                                                        </span>
                                                    </div>
                                                </div>
                                            
                                            </div>
                                        </div>
                                    
                                    </div>
                                    <!-- YTD WTD Row -->
                                    <div class="row justify-content-center shadowCustom mt-3 boxHeight">
                                        <!-- YTD Info  -->
                                            <div id="ytdDiv" class="col mt-2 preLoader">
                                                <font class="hText_Res" color="#1f426e">
                                                    YTD <font class="shText_Res"></font>
                                                    <i name="populateYTD()" loader-id="ytdDiv" class="reloadIcon-sm fas fa-redo-alt"></i>
                                                </font>
                                                <hr />
                                                <div class="row align-items-center over-div">
                                                    <div id="ytdFlex1" class="col flexProp">
                                                        <div>
                                                            <label class="labelData">Impressions</label> 
                                                            <label id="ytd-impr-currency" class="currency"></label> 
                                                            <span class="spanCenter">
                                                                <svg id="ytd-impr-svg" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="4 0 16 24" enable-background="new 0 0 24 24" style="vertical-align: middle; fill: rgb(76, 175, 80); height: 12px;" x="0px" y="0px" ng-style="{'fill': compareDelta.deltaColor,
                                                                'height': $ctrl.svgHeight}" ng-if="compareDelta.deltaDirection === '-'" class="ng-scope">
                                                                <polygon points="19,14.3 16,14.3 16,11 8,11 8,14.3 5,14.3 12,21.3 "></polygon>
                                                                <rect x="8" y="7" width="8" height="3"></rect>
                                                                <polyline points="8,4 8,6 16,6 16,4 "></polyline>
                                                                <polyline points="8,2 8,3 16,3 16,2 "></polyline>
                                                                </svg>
                                                                <label id="ytd-impr-lbl"></label>
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <label class="labelData">Cost</label> 
                                                            <label id="ytd-cost-currency" class="currency"></label> 
                                                            <span class="spanCenter">
                                                                <svg id="ytd-cost-svg" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="4 0 16 24" enable-background="new 0 0 24 24" style="vertical-align: middle; fill: rgb(76, 175, 80); height: 12px;" x="0px" y="0px" ng-style="{'fill': compareDelta.deltaColor,
                                                                'height': $ctrl.svgHeight}" ng-if="compareDelta.deltaDirection === '-'" class="ng-scope">
                                                                <polygon points="19,14.3 16,14.3 16,11 8,11 8,14.3 5,14.3 12,21.3 "></polygon>
                                                                <rect x="8" y="7" width="8" height="3"></rect>
                                                                <polyline points="8,4 8,6 16,6 16,4 "></polyline>
                                                                <polyline points="8,2 8,3 16,3 16,2 "></polyline>
                                                                </svg>
                                                                <label id="ytd-cost-lbl"></label>
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <label class="labelData">Rev</label> 
                                                            <label id="ytd-rev-currency" class="currency"></label> 
                                                            <span class="spanCenter">
                                                                <svg id="ytd-rev-svg" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="4 0 16 24" enable-background="new 0 0 24 24" style="vertical-align: middle; fill: rgb(76, 175, 80); height: 12px;" x="0px" y="0px" ng-style="{'fill': compareDelta.deltaColor,
                                                                'height': $ctrl.svgHeight}" ng-if="compareDelta.deltaDirection === '-'" class="ng-scope">
                                                                <polygon points="19,14.3 16,14.3 16,11 8,11 8,14.3 5,14.3 12,21.3 "></polygon>
                                                                <rect x="8" y="7" width="8" height="3"></rect>
                                                                <polyline points="8,4 8,6 16,6 16,4 "></polyline>
                                                                <polyline points="8,2 8,3 16,3 16,2 "></polyline>
                                                                </svg>
                                                                <label id="ytd-rev-lbl"></label>
                                                            </span>
                                                        </div>
                                                    </div>
                                                
                                                    <div id="ytdFlex2" class="col flexProp">
                                                        <div>
                                                            <label class="labelData">ACOS</label> 
                                                            <label id="ytd-acos-currency" class="currency"></label> 
                                                            <span class="spanCenter">
                                                                <svg id="ytd-acos-svg" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="4 0 16 24" enable-background="new 0 0 24 24" style="vertical-align: middle; fill: rgb(76, 175, 80); height: 12px;" x="0px" y="0px" ng-style="{'fill': compareDelta.deltaColor,
                                                                'height': $ctrl.svgHeight}" ng-if="compareDelta.deltaDirection === '-'" class="ng-scope">
                                                                <polygon points="19,14.3 16,14.3 16,11 8,11 8,14.3 5,14.3 12,21.3 "></polygon>
                                                                <rect x="8" y="7" width="8" height="3"></rect>
                                                                <polyline points="8,4 8,6 16,6 16,4 "></polyline>
                                                                <polyline points="8,2 8,3 16,3 16,2 "></polyline>
                                                                </svg>
                                                                <label id="ytd-acos-lbl"></label>
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <label class="labelData">CPC</label> 
                                                            <label id="ytd-cpc-currency" class="currency"></label> 
                                                            <span class="spanCenter">
                                                                <svg id="ytd-cpc-svg" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="4 0 16 24" enable-background="new 0 0 24 24" style="vertical-align: middle; fill: rgb(76, 175, 80); height: 12px;" x="0px" y="0px" ng-style="{'fill': compareDelta.deltaColor,
                                                                'height': $ctrl.svgHeight}" ng-if="compareDelta.deltaDirection === '-'" class="ng-scope">
                                                                <polygon points="19,14.3 16,14.3 16,11 8,11 8,14.3 5,14.3 12,21.3 "></polygon>
                                                                <rect x="8" y="7" width="8" height="3"></rect>
                                                                <polyline points="8,4 8,6 16,6 16,4 "></polyline>
                                                                <polyline points="8,2 8,3 16,3 16,2 "></polyline>
                                                                </svg>
                                                                <label id="ytd-cpc-lbl"></label>
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <label class="labelData">ROAS</label> 
                                                            <label id="ytd-roas-currency" class="currency"></label> 
                                                            <span class="spanCenter">
                                                                <svg id="ytd-roas-svg" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="4 0 16 24" enable-background="new 0 0 24 24" style="vertical-align: middle; fill: rgb(76, 175, 80); height: 12px;" x="0px" y="0px" ng-style="{'fill': compareDelta.deltaColor,
                                                                'height': $ctrl.svgHeight}" ng-if="compareDelta.deltaDirection === '-'" class="ng-scope">
                                                                <polygon points="19,14.3 16,14.3 16,11 8,11 8,14.3 5,14.3 12,21.3 "></polygon>
                                                                <rect x="8" y="7" width="8" height="3"></rect>
                                                                <polyline points="8,4 8,6 16,6 16,4 "></polyline>
                                                                <polyline points="8,2 8,3 16,3 16,2 "></polyline>
                                                                </svg>
                                                                <label id="ytd-roas-lbl"></label>
                                                            </span>
                                                        </div>
                                                    </div>
                                                
                                                </div>
                                            </div>
                                        <!-- WTD Info -->
                                            <div id="wtdDiv" class="col mt-2 preLoader">
                                                <font class="hText_Res" color="#1f426e">
                                                    WTD <font class="shText_Res"></font>
                                                    <i name="populateWTD()" loader-id="wtdDiv" class="reloadIcon-sm fas fa-redo-alt"></i>
                                                </font>
                                                <hr />
                                                <div class="row align-items-center over-div">
                                                    <div id="wtdFlex1" class="col flexProp">
                                                        <div>
                                                            <label class="labelData">Impressions</label> 
                                                            <label id="wtd-impr-currency" class="currency"></label> 
                                                            <span class="spanCenter">
                                                                <svg id="wtd-impr-svg" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="4 0 16 24" enable-background="new 0 0 24 24" style="vertical-align: middle; fill: rgb(76, 175, 80); height: 12px;" x="0px" y="0px" ng-style="{'fill': compareDelta.deltaColor,
                                                                'height': $ctrl.svgHeight}" ng-if="compareDelta.deltaDirection === '-'" class="ng-scope">
                                                                <polygon points="19,14.3 16,14.3 16,11 8,11 8,14.3 5,14.3 12,21.3 "></polygon>
                                                                <rect x="8" y="7" width="8" height="3"></rect>
                                                                <polyline points="8,4 8,6 16,6 16,4 "></polyline>
                                                                <polyline points="8,2 8,3 16,3 16,2 "></polyline>
                                                                </svg>
                                                                <label id="wtd-impr-lbl"></label>
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <label class="labelData">Cost</label> 
                                                            <label id="wtd-cost-currency" class="currency"></label> 
                                                            <span class="spanCenter">
                                                                <svg id="wtd-cost-svg" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="4 0 16 24" enable-background="new 0 0 24 24" style="vertical-align: middle; fill: rgb(76, 175, 80); height: 12px;" x="0px" y="0px" ng-style="{'fill': compareDelta.deltaColor,
                                                                'height': $ctrl.svgHeight}" ng-if="compareDelta.deltaDirection === '-'" class="ng-scope">
                                                                <polygon points="19,14.3 16,14.3 16,11 8,11 8,14.3 5,14.3 12,21.3 "></polygon>
                                                                <rect x="8" y="7" width="8" height="3"></rect>
                                                                <polyline points="8,4 8,6 16,6 16,4 "></polyline>
                                                                <polyline points="8,2 8,3 16,3 16,2 "></polyline>
                                                                </svg>
                                                                <label id="wtd-cost-lbl"></label>
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <label class="labelData">Rev</label> 
                                                            <label id="wtd-rev-currency" class="currency"></label> 
                                                            <span class="spanCenter">
                                                                <svg id="wtd-rev-svg" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="4 0 16 24" enable-background="new 0 0 24 24" style="vertical-align: middle; fill: rgb(76, 175, 80); height: 12px;" x="0px" y="0px" ng-style="{'fill': compareDelta.deltaColor,
                                                                'height': $ctrl.svgHeight}" ng-if="compareDelta.deltaDirection === '-'" class="ng-scope">
                                                                <polygon points="19,14.3 16,14.3 16,11 8,11 8,14.3 5,14.3 12,21.3 "></polygon>
                                                                <rect x="8" y="7" width="8" height="3"></rect>
                                                                <polyline points="8,4 8,6 16,6 16,4 "></polyline>
                                                                <polyline points="8,2 8,3 16,3 16,2 "></polyline>
                                                                </svg>
                                                                <label id="wtd-rev-lbl"></label>
                                                            </span>
                                                        </div>
                                                    </div>
                                                
                                                    <div id="wtdFlex2" class="col flexProp">
                                                        <div>
                                                            <label class="labelData">ACOS</label> 
                                                            <label id="wtd-acos-currency" class="currency"></label> 
                                                            <span class="spanCenter">
                                                                <svg id="wtd-acos-svg" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="4 0 16 24" enable-background="new 0 0 24 24" style="vertical-align: middle; fill: rgb(76, 175, 80); height: 12px;" x="0px" y="0px" ng-style="{'fill': compareDelta.deltaColor,
                                                                'height': $ctrl.svgHeight}" ng-if="compareDelta.deltaDirection === '-'" class="ng-scope">
                                                                <polygon points="19,14.3 16,14.3 16,11 8,11 8,14.3 5,14.3 12,21.3 "></polygon>
                                                                <rect x="8" y="7" width="8" height="3"></rect>
                                                                <polyline points="8,4 8,6 16,6 16,4 "></polyline>
                                                                <polyline points="8,2 8,3 16,3 16,2 "></polyline>
                                                                </svg>
                                                                <label id="wtd-acos-lbl"></label>
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <label class="labelData">CPC</label> 
                                                            <label id="wtd-cpc-currency" class="currency"></label> 
                                                            <span class="spanCenter">
                                                                <svg id="wtd-cpc-svg" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="4 0 16 24" enable-background="new 0 0 24 24" style="vertical-align: middle; fill: rgb(76, 175, 80); height: 12px;" x="0px" y="0px" ng-style="{'fill': compareDelta.deltaColor,
                                                                'height': $ctrl.svgHeight}" ng-if="compareDelta.deltaDirection === '-'" class="ng-scope">
                                                                <polygon points="19,14.3 16,14.3 16,11 8,11 8,14.3 5,14.3 12,21.3 "></polygon>
                                                                <rect x="8" y="7" width="8" height="3"></rect>
                                                                <polyline points="8,4 8,6 16,6 16,4 "></polyline>
                                                                <polyline points="8,2 8,3 16,3 16,2 "></polyline>
                                                                </svg>
                                                                <label id="wtd-cpc-lbl"></label>
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <label class="labelData">ROAS</label> 
                                                            <label id="wtd-roas-currency" class="currency"></label> 
                                                            <span class="spanCenter">
                                                                <svg id="wtd-roas-svg" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="4 0 16 24" enable-background="new 0 0 24 24" style="vertical-align: middle; fill: rgb(76, 175, 80); height: 12px;" x="0px" y="0px" ng-style="{'fill': compareDelta.deltaColor,
                                                                'height': $ctrl.svgHeight}" ng-if="compareDelta.deltaDirection === '-'" class="ng-scope">
                                                                <polygon points="19,14.3 16,14.3 16,11 8,11 8,14.3 5,14.3 12,21.3 "></polygon>
                                                                <rect x="8" y="7" width="8" height="3"></rect>
                                                                <polyline points="8,4 8,6 16,6 16,4 "></polyline>
                                                                <polyline points="8,2 8,3 16,3 16,2 "></polyline>
                                                                </svg>
                                                                <label id="wtd-roas-lbl"></label>
                                                            </span>
                                                        </div>
                                                    </div>
                                                
                                                </div>
                                            </div>
                                    </div>    
                                    
                                    <div class="row noSpace options shadowCustom mt-3">
                                        <!-- AD TYPE -sm-6 col-md-6 col-lg-4-->
                                        <div id="adType_table_Div" class="col-sm-6 col-md-6 col-lg-4 mt-3 preLoader boxHeight">
                                            <font class="hText_Res" color="#1f426e">
                                                AD TYPE 
                                                <i name="Ad_type_DataCall()" loader-id="adType_table_Div" class="reloadIcon-sm fas fa-redo-alt"></i>
                                            </font>
                                            <hr />       
                                            <table id="table_adtype" width="100%" class="table table-condensed display nowrap">
                                                <thead >
                                                    <tr id="adtype_head" class="trCells">
                                                        <th>Ad type</th>
                                                        <th>Imp</th>
                                                        <th>Rev. ($)</th>
                                                        <th>ACOS (%)</th>
                                                        <th>CTR. (%)</th>
                                                        <th>Cost ($)</th>
                                                    </tr>
                                                </thead>
                                                <tfoot align="left">
                                                    <tr><th></th><th class="adtypeToolTip"></th><th class="adtypeToolTip"></th><th ></th><th ></th><th class="adtypeToolTip"></th></tr>
                                                </tfoot>
                                            </table>
                                        </div>

                                        <!-- STRATEGY TYPE -sm-6 col-md-6 col-lg-4-->
                                        <div id="strategy_table_Div" class="col-sm-6 col-md-6 col-lg-4 mt-3 preLoader boxHeight">
                                            <font class="hText_Res" color="#1f426e">
                                                STRATEGY TYPE 
                                                <i name="Strategy_DataCall()" loader-id="strategy_table_Div" class="reloadIcon-sm fas fa-redo-alt"></i>
                                            </font>
                                            <hr />       
                                            <table id="table_Str_type" width="100%" class="table table-condensed display nowrap">
                                                <thead >
                                                    <tr id="str_head" class="trCells">
                                                        <th>Strategy type</th>
                                                        <th>Imp</th>
                                                        <th>Rev. ($)</th>
                                                        <th>ACOS (%)</th>
                                                        <th>CTR. (%)</th>
                                                        <th>Cost ($)</th>
                                                    </tr>
                                                </thead>
                                                <tfoot align="left">
                                                <tr><th></th><th class="strToolTip"></th><th class="strToolTip"></th><th ></th><th ></th><th class="strToolTip"></th></tr>
                                                
                                                </tfoot>
                                            </table>
                                        </div>
                            
                                        <!-- table_trg_type Custom TYPE -->
                                        <div id="custom_table_Div" class="col-sm-6 col-md-6 col-lg-4 mt-3 preLoader boxHeight">
                                            <font class="hText_Res" color="#1f426e">
                                                CUSTOM TYPE 
                                                <i name="custom_DataCall()" loader-id="custom_table_Div" class="reloadIcon-sm fas fa-redo-alt"></i>
                                            </font>
                                            <hr />       
                                            <table id="table_trg_type" width="100%" class="table table-condensed display nowrap">
                                                <thead >
                                                    <tr id="trg_head" class="trCells">
                                                        <th>Custom type</th>
                                                        <th>Imp</th>
                                                        <th>Rev. ($)</th>
                                                        <th>ACOS (%)</th>
                                                        <th>CTR. (%)</th>
                                                        <th>Cost ($)</th>
                                                    </tr>
                                                </thead>
                                                <tfoot align="left">
                                                <tr><th></th><th class="trgToolTip"></th><th class="trgToolTip"></th><th ></th><th ></th><th class="trgToolTip"></th></tr>
                                                
                                                </tfoot>
                                            </table>
                                        </div>
                                        <!-- PRODUCT TYPE -sm-6 col-md-6 col-lg-4-->
                                        <div id="product_table_Div" class="col-sm-6 col-md-6 col-lg-4 mt-3 preLoader boxHeight">
                                                <font class="hText_Res" color="#1f426e">
                                                    PRODUCT TYPE 
                                                    <i name="prodType_DataCall()" loader-id="product_table_Div" class="reloadIcon-sm fas fa-redo-alt"></i>
                                                </font>
                                                <hr />       
                                                <table id="table_prod_type" width="100%" class="table table-condensed display nowrap">
                                                    <thead >
                                                        <tr id="prod_head" class="trCells">
                                                            <th>Product type</th>
                                                            <th>Imp</th>
                                                            <th>Rev. ($)</th>
                                                            <th>ACOS (%)</th>
                                                            <th>CTR. (%)</th>
                                                            <th>Cost ($)</th>
                                                        </tr>
                                                    </thead>
                                                    <tfoot align="left">
                                                    <tr><th></th><th class="prodToolTip"></th><th class="prodToolTip"></th><th ></th><th ></th><th class="prodToolTip"></th></tr>
        
                                                    </tfoot>
                                                </table>
                                        </div> 
                                        
                                        <!-- Performance - PREV 30 DAYS -sm-6 col-md-6 col-lg-4-->
                                        <div id="perf_table_Div" class="col-sm-6 col-md-6 col-lg-4 mt-3 preLoader boxHeight">
                                            <font class="hText_Res" color="#1f426e">
                                                PERFORMANCE - PREV 30 DAYS 
                                                <i name="perf_Pre30_DataCall()" loader-id="perf_table_Div" class="reloadIcon-sm fas fa-redo-alt"></i>
                                            </font>
                                            <hr />       
                                            <table id="table_perf_type" width="100%" class="table table-condensed display nowrap">
                                                <thead >
                                                    <tr id="perf_head" class="trCells">
                                                        <th>Account Name</th>
                                                        <th>Rev. ($)</th>
                                                        <th>ACOS (%)</th>
                                                        <th>Cost ($)</th>
                                                    </tr>
                                                </thead>
                                                <tfoot align="left">
                                                    <tr>
                                                        <th></th>
                                                        <th class="perfToolTip"></th>
                                                        <th></th>
                                                        <th class="perfToolTip"></th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                            <!-- Performance- YTD -sm-6 col-md-6 col-lg-4-->
                                        <div id="perfYtd_table_Div" class="col-sm-6 col-md-6 col-lg-4 mt-3 preLoader boxHeight"> 
                                            <font class="hText_Res" color="#1f426e">
                                                PERFORMANCE - YTD
                                                <i name="perf_ytd_DataCall()" loader-id="perfYtd_table_Div" class="reloadIcon-sm fas fa-redo-alt"></i>
                                            </font>
                                            <hr />       
                                            <table id="table_perf_YTD_type" width="100%" class="table table-condensed display nowrap">
                                                <thead >
                                                    <tr id="YTD_head" class="trCells">
                                                        <th>Account Name</th>
                                                        <th>Rev.($)</th>
                                                        <th>ACOS (%)</th>
                                                        <th>Cost ($)</th>
                                                    </tr>
                                                </thead>
                                                <tfoot align="left">
                                                    <tr>
                                                        <th></th>
                                                        <th class="ytdToolTip"></th>
                                                        <th></th>
                                                        <th class="ytdToolTip"></th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div> 
                                    </div>                               
                            
                        </div>
                    </div>
                    <div class="row align-items-center mt-3 shadowCustom">
                                        <div id="topTen_table_Div" class="col-sm-12 col-md-12  col-lg-12 preLoader">
                                            <font class="hText_Res" color="#1f426e">
                                                TOP                                             
                                                <select id='topXCompaigns' class="topXcampaignSelect">
                                                    <option value='5'>5</option>
                                                    <option value='10' selected="selected">10</option>
                                                    <option value='15'>15</option>
                                                    <option value='20'>20</option>
                                                    <option value='25'>25</option>
                                                    <option value='30'>30</option>
                                                    <option value='35'>35</option>
                                                    <option value='40'>40</option>
                                                    <option value='45'>45</option>
                                                    <option value='50'>50</option>
                                                </select>
                                                CAMPAIGNS
                                                <i name="top_campaigns_DataCall()" loader-id="topTen_table_Div" class="reloadIcon-sm fas fa-redo-alt"></i>
                                            </font>
                                            <hr /> 
                            
                                            <table id="table_top10campaign"  style="width:100%;color:black" class="table table-top display">
                                                <thead >
                                                    <tr>
                                                        <th>Campaign Name</th>
                                                        <th>Spend ($)</th>
                                                        <th>Revenue ($)</th>
                                                        <th>ACOS (%)</th>
                                                        <th style="display: none;">Rank</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                </div>
            </div> 
        </div>
    </div>
</div>
        <!-- /.container-fluid -->
@endsection
@push('js')
    <!-- Load d3.js and c3.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/c3/0.7.15/c3.min.js" charset="utf-8"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/5.15.0/d3.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/tooltipster/3.3.0/js/jquery.tooltipster.js"></script>

    <!-- Ajax Query  -->
    <script src="{{ asset('public/js/select2.min.js?'.time()) }}"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="{{asset('public/vendor/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{ asset('public/vendor/daterangepicker/daterangepicker.js') }}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js"></script>
    <script src="{{ asset('public/vissuals/js/chart.js?'.time()) }}"></script>
@endpush