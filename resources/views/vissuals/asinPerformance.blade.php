@extends('layout.app')
@extends('inc.side_menu')
@extends('inc.nav_bar')
@extends('modal.logout')
@section('title', isset($pageTitle)?$pageTitle:'ASIN Performance')

@push('css')
    <!-- Load c3.css -->

    <link href="https://cdnjs.cloudflare.com/ajax/libs/c3/0.7.15/c3.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/tooltipster/3.3.0/css/tooltipster.min.css" />
    <link rel="stylesheet" href="{{ asset('public/css/select2.min.css?'.time())  }}"/>
    <link rel="stylesheet" href="{{ asset('public/vendor/daterangepicker/daterangepicker.css') }}">  
        
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
                            <div class="row noSpace options justify-content-center ">
                                <div class="col-sm-6 col-md-6 col-xl-3 h-25">
                                    <label class="labelFilter" for="select-profile">Select Child Brand</label>
                                    <select tabindex="1" id="select-profile" class="selectpicker filtersHeight profileSelect custom" campaign-url = "{{ route('vissuals.getAsinPerformanceVisualsCampaigns') }}">
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
                                <div class="col-sm-6 col-md-6 col-xl-3 h-25 multiSelect2 campaignSelectParent">
                                    <label class="labelFilter" for="select-campaign">Select Campaigns</label>    
                                    <select tabindex="2" id="select-campaign" class="selectpicker filtersHeight campaignSelect" asin-url ="{{ route('vissuals.getAsinPerformanceVisualsAsins') }}" multiple="multiple">    
                                    </select>
                                </div>
                                <!-- Asin Selection Drop Down -->
                                <div class="col-sm-6 col-md-6 col-xl-3 h-25 filterSelects selectPosition selectASIN">
                                    <label class="labelFilter" for="select-asin">Select ASIN</label>
                                    <select tabindex="3" id="select-asin" class="selectpicker filtersHeight asinSelect" data-url ="{{ route('vissuals.asinlevelspdata') }}">
                                    <option value="">Select ASIN</option>
                                    </select>
                                 </div>
                            
                                <div class="col-sm-6 col-md-6 col-xl-3 h-25">
                                    <label class="labelFilter" for="select-date">Select Date Range</label>
                                    <input tabindex="4" id="select-date" class="filtersHeight" placeholder="Select Date Range" type="text" asin-url ="{{ route('vissuals.getAsinPerformanceVisualsAsins') }}" name="datefilter" autocomplete="off" value="" />
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
                                                <p id="clicks_txt_box" class="metricTooltip innerPara">
                                                    
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
                    <div id="sectionA" class="row noSpace options">
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
                                                        <label id="awar-clk-currency" class="tooltip6 currency"></label> 
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
    <script src="{{ asset('public/vendor/daterangepicker/daterangepicker.js') }}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js"></script>
    <script src="{{ asset('public/vissuals/js/asinPerformance.js?'.time()) }}"></script>
@endpush