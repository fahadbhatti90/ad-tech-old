@extends('layout.app')
@extends('inc.side_menu')
@extends('inc.nav_bar')
@extends('modal.logout')
@section('title', isset($pageTitle)?$pageTitle:'')
@section('content')

    @push('css')

        <link href="{{asset('public/css/select2.min.css')}}" rel="stylesheet">
        <link href="{{asset('public/css/bootstrap-datetimepicker.min.css')}}" rel="stylesheet">
        <link rel="stylesheet" type="text/css"
              href="{{ asset('public/tooltipster/dist/css/tooltipster.bundle.min.css')}}"/>
        <link href="{{asset('public/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet"
              type="text/css">
        <link href="{{asset('public/vendor/datatables/responsive.dataTables.min.css')}}" rel="stylesheet"
              type="text/css">
        <link rel="stylesheet" href="{{ asset('public/vendor/select/dist/css/bootstrap-select.min.css') }}">
        <link rel="stylesheet"
              href="{{asset('public/ams/advertisingReportsEmail/css/emailSchedule_custom_style.css?'.time())  }}">
        <link rel="stylesheet" href="{{asset('public/ams/advertisingReportsEmail/css/emailSchedule.css?'.time())  }}">
        <link href="{{asset('public/css/select2.min.css')}}" rel="stylesheet">
        <link href="{{asset('public/css/multiple-emails.css')}}" rel="stylesheet">



    @endpush

    @push('daterangepickercss')
        <link rel="stylesheet" href="{{ asset('public/vendor/daterangepicker/daterangepicker.css') }}">
    @endpush

    @push('daterangepickerjs')
       {{-- <script src="{{ asset('public/vendor/daterangepicker/moment.min.js') }}"></script>--}}
        {{--<script src="{{asset('public/vendor/daterangepicker/daterangepicker.js')}}"></script>--}}
    @endpush
    @push('js')

        <script src="{{asset('public/vendor/daterangepicker/moment.min.js') }}"></script>
        <script src="{{asset('public/vendor/daterangepicker/moment-timezone.js') }}"></script>
        <script type="text/javascript" src="{{asset('public/js/bootstrap-datetimepicker.min.js')}}"></script>

        <script type="text/javascript"
                src="{{ asset('public/tooltipster/dist/js/tooltipster.bundle.min.js')}}"></script>
        <script src="{{asset('public/vendor/autosize/autosize.min.js')}}"></script>
        <script src="{{asset('public/vendor/datatables/jquery.dataTables.min.js')}}"></script>
        <script src="{{asset('public/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
        <script src="{{asset('public/vendor/datatables/dataTables.responsive.min.js')}}"></script>
        <script type="text/javascript" src="{{asset('public/js/select2.min.js')}}"></script>
        <script src="{{ asset('public/ams/advertisingReportsEmail/js/emailSchedule.js?'.time()) }}"></script>
        <script type="text/javascript" src="{{asset('public/js/multiple-emails.js?'.time())}}"></script>
        <script>
            $(function () {
                $('#cc_emailBS').multiple_emails({position: "bottom"});
                ccEmailTooltip();
            });
        </script>
        <!--  Multi select js ends -->

        <!--  Multi select js ends -->
    @endpush
    <!-- Begin Page Content -->
    <div class="container-fluid">

    @include('partials.formPreloader')

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <button type="button" class="btn btn-primary addSchedule sc-move-right" data-toggle="modal"
                    data-target="#addProductPreviewModel">
                Create Schedule
            </button>
        </div>
        <div class="card mb-4">
            <div class="card-header py-3" style="display: flex;">
                <h6 class="m-0 font-weight-bold text-primary" style="flex-basis:70%">
                    Scheduled Reports
                </h6>
            </div>
            <div class="card-body schedulingCardBody">
                <table id="dataTable" class="table table-bordered table-striped  cronListTable" style="width:100%">
                    <thead>
                    <tr>
                        <th class="srNoTh">Sr. #</th>
                        <th>Report Name</th>
                        <th>Schedule</th>
                        <th>Ad Type</th>
                        <th>Report Type</th>
                        <th>Metrics</th>
                        {{--<th>Last Run</th>--}}
                        <th>Action</th>
                        {{-- <th class="text-center">Action</th> --}}
                    </tr>
                    </thead>
                    <tbody class="cronList">
                    @php
                        $sr = 0;
                    @endphp
                    @isset($scheduledEmails)
                        @if(count($scheduledEmails) > 0)
                            @foreach($scheduledEmails as $key => $scheduledEmails)
                                <tr id="{{ $scheduledEmails->id }}">

                                    <td> {{ ++$sr }} </td>
                                    <td >
                                        @if(strlen($scheduledEmails->reportName) < 23 )
                                            {{ $scheduledEmails->reportName }}
                                        @else
                                            <div class="tooltip" title="{{ $scheduledEmails->reportName }}">{{ str_limit($scheduledEmails->reportName,22) }}</div>
                                        @endif
                                    </td>
                                    @php
                                        $scheduleData='';
                                            //$scheduleData = $scheduledEmails->time.'test';
                               if($scheduledEmails->mon==1){
                                        $scheduleData .= 'Monday ';
                                        }
                                        if($scheduledEmails->tue == 1){
                                        $scheduleData .= 'Tuesday ';
                                        }
                                        if($scheduledEmails->wed == 1){
                                        $scheduleData .= 'Wednesday ';
                                        }
                                        if($scheduledEmails->thu == 1){
                                        $scheduleData .= 'Thursday ';
                                        }
                                        if($scheduledEmails->fri == 1){
                                        $scheduleData .= 'Friday ';
                                        }
                                        if($scheduledEmails->sat == 1){
                                        $scheduleData .= 'Saturday ';
                                        }
                                        if($scheduledEmails->sun == 1){
                                        $scheduleData .='Sunday ';
                                        }
                                        $countDays = str_word_count($scheduleData);
                                         $dayOne = strtok($scheduleData, " ");
                                         $scheduleData .= ' at ';
                                        $cronTime = $scheduledEmails->time;
                                        $scheduleData .= date('h:i A', strtotime($cronTime));
                                        $scheduleDayOne = $dayOne.'...';
                                    @endphp
                                    <td> @if($countDays == 1 ) <div class="tooltip">{{ $scheduleData }}</div>  @else <div class="tooltip" title="{{ $scheduleData }}">{{ $scheduleDayOne }}</div> @endif</td>
                                    @isset($scheduledEmails->selectedSponsoredTypes)
                                        @php
                                            $selectedSponsoredTypes = $scheduledEmails->selectedSponsoredTypes;
                                            $getSponsoredTypes = '';
                                        if(count($selectedSponsoredTypes) > 0){
                                            foreach($selectedSponsoredTypes as $selectedSponsoredType){
                                                $getSponsoredTypes .= $selectedSponsoredType->sponsordTypenName.' ' ;
                                            }
                                        }
                                        $countSponsoredTypes = str_word_count($getSponsoredTypes);
                                        $singleSponsoredTypes = implode(' ', array_slice(explode(' ', $getSponsoredTypes), 0, 2));;

                                        @endphp
                                    @endisset
                                    <td>

                                        @isset($getSponsoredTypes)
                                        @if($countSponsoredTypes <= 2 )
                                           <div class="tooltip">{{ $singleSponsoredTypes }}</div>
                                        @else
                                            <div class="tooltip" title="{{ $getSponsoredTypes }}">{{ $singleSponsoredTypes.'...' }}</div>
                                        @endif
                                        @endisset
                                    </td>
                                    @isset($scheduledEmails->selectedReportTypes)
                                        @php
                                            $selectedReportTypes = $scheduledEmails->selectedReportTypes;

                                        $getReportTypes = '';
                                        $countReportTypes = count($selectedReportTypes);
                                        if(count($selectedReportTypes) > 0){
                                            $countaReportNames = 1;
                                            foreach($selectedReportTypes as $selectedReportType){
                                                 if ($countaReportNames == 1){
                                                    if(count($selectedReportTypes) == 1){
                                              $singleReportType = $selectedReportType->reportName;
                                            }else{
                                              $singleReportType = $selectedReportType->reportName.'...';
                                            }
                                                 }
                                                 $getReportTypes .= $selectedReportType->reportName .' ' ;
                                                 $countaReportNames++;
                                            }


                                            }
                                        @endphp
                                    @endisset
                                    <td>
                                        @isset($getReportTypes)
                                            @if($countReportTypes == 1 )
                                                <div class="tooltip" >{{ $singleReportType }}</div>
                                            @else
                                                <div class="tooltip" title="{{ $getReportTypes }}">{{ $singleReportType }}</div>
                                            @endif
                                        @endisset
                                    </td>
                                    @isset($scheduledEmails->selectedReportsMetrics)
                                        @php
                                            $commanSeperateMetrics = [];
                                            $selectedReportsMetrics = $scheduledEmails->selectedReportsMetrics;
                                        @endphp
                                        @if(count($selectedReportsMetrics) > 0)
                                            @foreach($selectedReportsMetrics as $selectedReportsMetric)
                                                @php
                                                    $commanSeperateMetrics[] = $selectedReportsMetric->metricName;
                                                @endphp
                                            @endforeach
                                            @php
                                                $metricsValues = str_limit(implode(",",$commanSeperateMetrics),20);
                                                $allMetricsValues = implode(",",$commanSeperateMetrics);
                                            @endphp
                                    <td class="text-capitalize">
                                        <div title="Click Here To View Metrics" class="showMetricsPopup cursor-pointer" data-api-config-id="{{ $scheduledEmails->id }}">
                                            <a href="#">View Metrics</a>
                                        </div>
                                               {{--<div class="showMetricsPopup cursor-pointer" data-api-config-id="{{ $scheduledEmails->id }}">
                                                    {{ $metricsValues }}
                                                </div>
                                                   <div class="tooltip" title="{{ $allMetricsValues }}">{{ str_limit($metricsValues,20) }}</div>--}}
                                            @endif
                                        @endisset
                                    </td>
                                    @php
                                        $cronLastRunTime = $scheduledEmails->cronLastRunTime;
                                        $cronLastRunTimeVar = date('h:i a', strtotime($cronLastRunTime));
                                          if ($scheduledEmails->cronLastRunDate != ''){
                                               $lastRunSchedule = $scheduledEmails->cronLastRunDate.' at '.$cronLastRunTimeVar;
                                               }
                                            else
                                                {
                                                    $lastRunSchedule = 'Not Run';
                                                    }
                                    @endphp
                                   {{-- <td>
                                        {{ $lastRunSchedule }}
                                       </td>--}}
                                    <td class="text-center">
                                        <i class="fa fa-edit" data-api-config-id="{{ $scheduledEmails->id }}"> | </i>
                                        <i class="fa fa-trash" data-api-config-id="{{ $scheduledEmails->id }}"
                                           aria-hidden="true"></i>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    @endisset
                    </tbody>

                </table>
                {{--pop for add edit form starts--}}
                <div class="modal fade addProductPreviewModel" id="addProductPreviewModel" tabindex="-1" role="dialog"
                     aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">Create Schedule</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">

                                @include('partials.graphsPreloader')
                                <div class="row">
                                    <div class="col-12">
                                        <div class=" mt-0">
                                            <div class="col-xl-12 input-group mb-3">
                                                <form id="addAdvertisingReportSchedule" method="POST"
                                                      action="{{ route('manageEmailSchedule') }}" action_type="1">
                                                    @csrf
                                                    <div class="form-group">
                                                        <label for="">Report name<sup class="required">*</sup></label>
                                                        <input class="form-control" name="reportName" id="reportName"
                                                               type="text" placeholder="Please Enter Report Name"/>
                                                    </div>

                                                    <div class="form-group">

                                                        <label for="brand">Child brand<sup class="required">*</sup></label>
                                                        <select class="form-control" name="brand" id="brand">
                                                            <option value="" selected>Select Profile</option>
                                                            @if(!empty($amsProfiles))
                                                                @foreach($amsProfiles as $brand)
                                                                    @isset($brand->fkId)
                                                                        @if(!empty(trim($brand->ams['name'])) || !empty($brand->brand_alias[0]->overrideLabel))
                                                                            @php
                                                                                $brandOptionValue = '';
                                                                                   $brandOptionValue =   $brand->brand_alias != null ?
                                                                                      ($brand->brand_alias != null &&
                                                                                      count($brand->brand_alias) > 0 ?
                                                                                      ($brand->brand_alias[0]->overrideLabel != null ?
                                                                                       ($brand->brand_alias[0]->overrideLabel > 100 ?  $brand->brand_alias[0]->overrideLabel: str_limit($brand->brand_alias[0]->overrideLabel,100)):
                                                                                      ($brand->ams != null ? ($brand->ams['name'] > 100 ?  $brand->ams['name']:  str_limit($brand->ams['name'],100)) : '')) :
                                                                                      ($brand->ams != null ? ($brand->ams['name'] > 100 ?  $brand->ams['name']:  str_limit($brand->ams['name'],100)) : '')):
                                                                                      ($brand->ams != null ? ($brand->ams['name'] > 100 ?  $brand->ams['name']:  str_limit($brand->ams['name'],100)): '');
                                                                                   $brandOptionTitle = '';
                                                                                   $brandOptionTitle =   $brand->brand_alias != null ?
                                                                                      ($brand->brand_alias != null &&
                                                                                      count($brand->brand_alias) > 0 ?
                                                                                      ($brand->brand_alias[0]->overrideLabel != null ?
                                                                                       ( $brand->brand_alias[0]->overrideLabel):
                                                                                      ($brand->ams != null ? (  $brand->ams['name']) : '')) :
                                                                                      ($brand->ams != null ? (  $brand->ams['name']) : '')):
                                                                                      ($brand->ams != null ? (  $brand->ams['name']): '');
                                                                            @endphp
                                                                            <option title="{{$brandOptionTitle}}" value="{{$brand->fkId}}">

                                                                                {{$brandOptionValue}}
                                                                            </option>
                                                                        @endif
                                                                    @endisset
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>


                                                    <div class="form-group">
                                                        <label for="sponsordType">Ad type<sup class="required">*</sup></label>
                                                        <select class="form-control js-example-basic-multiple"
                                                                name="sponsordType" id="sponsordType" multiple>
                                                            <!--    <option value="" selected>Please Select Sponsord Type</option> -->
                                                            @isset($sponsordTypes)
                                                                @foreach ($sponsordTypes as $sponsordType)
                                                                    <option value="{{$sponsordType->id}}">{{$sponsordType->sponsordTypenName}}</option>
                                                                @endforeach
                                                            @endisset

                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="reportType">Report type<sup class="required">*</sup></label>
                                                        <select class="form-control js-example-basic-multiple"
                                                                name="reportType" id="reportType" multiple="multiple">
                                                            <!-- <option value="" selected>Please Select Report Type</option> -->
                                                            @isset($sponsordReports)
                                                                @foreach ($sponsordReports as $sponsordReport)
                                                                    <option value="{{$sponsordReport->id}}">{{$sponsordReport->reportName}}</option>
                                                                @endforeach
                                                            @endisset
                                                        </select>
                                                    </div>
                                                    <div class="form-group row metrics_div">
                                                        <div class="col-lg-12">
                                                            <fieldset class="userActions">
                                                                <legend>Select metrics<sup class="required">*</sup></legend>
                                                                <div class="custom-control custom-checkbox userActions">
                                                                    <input type="checkbox" value="0"
                                                                           name="checkAllMetrics"
                                                                           class="custom-control-input "
                                                                           id="checkAllMetrics">
                                                                    <label class="custom-control-label"
                                                                           for="checkAllMetrics"><span
                                                                                class="selectAllMetrics">Select All Metrics</span></label>
                                                                </div>
                                                                <div class="amsReportsMetrics"
                                                                     style="height:200px; overflow-y: scroll;">
                                                                    @isset($amsCampaignReportsMetrics)
                                                                        <div class="campaignMetrics">
                                                                            <div class="col-lg-12 text-center">
                                                                                Campaign
                                                                            </div>
                                                                            @foreach ($amsCampaignReportsMetrics as $amsCampaignReportsMetrics)
                                                                                <div class="custom-control custom-checkbox userActions">
                                                                                    <input type="checkbox"
                                                                                           value="{{ $amsCampaignReportsMetrics->id }}"
                                                                                           name="campaignMetricsCheckBox[]"
                                                                                           class="custom-control-input metrics_checkboxes"
                                                                                           id="campaignMetricsCheckBox{{ $amsCampaignReportsMetrics->id }}">
                                                                                    <label class="custom-control-label"
                                                                                           for="campaignMetricsCheckBox{{ $amsCampaignReportsMetrics->id }}">{{$amsCampaignReportsMetrics->metricName}}</label>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    @endisset
                                                                    @isset($amsAdGroupReportsMetrics)
                                                                        <div class="adGroupMetrics">
                                                                            <div class="col-lg-12 text-center">
                                                                                Ad Group
                                                                            </div>

                                                                            @foreach ($amsAdGroupReportsMetrics as $amsAdGroupReportsMetric)
                                                                                <div class="custom-control custom-checkbox userActions">
                                                                                    <input type="checkbox"
                                                                                           value="{{ $amsAdGroupReportsMetric->id }}"
                                                                                           name="adGroupMetricsCheckBox[]"
                                                                                           class="custom-control-input metrics_checkboxes"
                                                                                           id="adGroupMetricsCheckBox{{ $amsAdGroupReportsMetric->id }}">
                                                                                    <label class="custom-control-label"
                                                                                           for="adGroupMetricsCheckBox{{ $amsAdGroupReportsMetric->id }}">{{$amsAdGroupReportsMetric->metricName}}</label>
                                                                                </div>


                                                                            @endforeach
                                                                        </div>
                                                                    @endisset
                                                                    @isset($amsProductAdsReportsMetrics)
                                                                        <div class="productAdsMetrics">
                                                                            <div class="col-lg-12 text-center">
                                                                                Product Ads
                                                                            </div>
                                                                            @foreach ($amsProductAdsReportsMetrics as $amsProductAdsReportsMetric)
                                                                                <div class="custom-control custom-checkbox userActions">
                                                                                    <input type="checkbox"
                                                                                           value="{{ $amsProductAdsReportsMetric->id }}"
                                                                                           name="productAdsMetricsCheckBox[]"
                                                                                           class="custom-control-input metrics_checkboxes"
                                                                                           id="productAdsMetricsCheckBox{{ $amsProductAdsReportsMetric->id }}">
                                                                                    <label class="custom-control-label"
                                                                                           for="productAdsMetricsCheckBox{{ $amsProductAdsReportsMetric->id }}">{{$amsProductAdsReportsMetric->metricName}}</label>
                                                                                </div>


                                                                            @endforeach
                                                                        </div>
                                                                    @endisset
                                                                    @isset($amsKeywordReportsMetrics)
                                                                        <div class="keywordMetrics">
                                                                            <div class="col-lg-12 text-center">
                                                                                Keyword
                                                                            </div>

                                                                            @foreach ($amsKeywordReportsMetrics as $amsKeywordReportsMetrics)
                                                                                <div class="custom-control custom-checkbox userActions">
                                                                                    <input type="checkbox"
                                                                                           value="{{ $amsKeywordReportsMetrics->id }}"
                                                                                           name="keywordMetricsCheckBox[]"
                                                                                           class="custom-control-input metrics_checkboxes"
                                                                                           id="keywordMetricsCheckBox{{ $amsKeywordReportsMetrics->id }}">
                                                                                    <label class="custom-control-label"
                                                                                           for="keywordMetricsCheckBox{{ $amsKeywordReportsMetrics->id }}">{{$amsKeywordReportsMetrics->metricName}}</label>
                                                                                </div>


                                                                            @endforeach
                                                                        </div>
                                                                    @endisset
                                                                    @isset($amsAsinReportsMetrics)
                                                                        <div class="asinMetrics">
                                                                            <div class="col-lg-12 text-center">
                                                                                ASINS
                                                                            </div>

                                                                            @foreach ($amsAsinReportsMetrics as $amsAsinReportsMetric)
                                                                                <div class="custom-control custom-checkbox userActions">
                                                                                    <input type="checkbox"
                                                                                           value="{{ $amsAsinReportsMetric->id }}"
                                                                                           name="asinMetricsCheckBox[]"
                                                                                           class="custom-control-input metrics_checkboxes"
                                                                                           id="asinMetricsCheckBox{{ $amsAsinReportsMetric->id }}">
                                                                                    <label class="custom-control-label"
                                                                                           for="asinMetricsCheckBox{{ $amsAsinReportsMetric->id }}">{{$amsAsinReportsMetric->metricName}}</label>
                                                                                </div>


                                                                            @endforeach
                                                                        </div>
                                                                    @endisset
                                                                    <br>
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                    </div>


                                                    <div class="form-group">
                                                        <label for="granularity"> Granularity<sup class="required">*</sup></label>
                                                        <select class="form-control" name="granularity"
                                                                id="granularity">
                                                            <option value="Daily">Daily</option>
                                                            <option value="Weekly">Weekly</option>
                                                            <option value="Monthly">Monthly</option>
                                                        </select>
                                                    </div>
                                                    <fieldset>
                                                        <legend>Timeframe</legend>


                                                        <div class="form-group row">
                                                            <div class="col-lg-12">
                                                                <div class="userActions">

                                                                    <label for="timeFrame">Select <span
                                                                                class="timeFrameChange">Days</span><sup class="required">*</sup></label>
                                                                    {{--<input class="form-control timeFrame"
                                                                           name="timeFrame" id="timeFrame" type="Number"
                                                                           placeholder="Please Enter Number Of Days"/>--}}
                                                                    <select class="form-control js-example-basic-multiple"
                                                                            name="timeFrame" id="timeFrame">
                                                                        @for ($i = 1 ; $i < 366 ; $i++)
                                                                                <option value="{{$i}}">Last {{ $i }} {{ ($i == 1 ? 'Day' : 'Days')}}</option>
                                                                        @endfor
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                    <!--  <div class="form-group">
                                                         <label for="">Date Range</label>
                                                         <input type="text"  placeholder="Please Select Date Range" data-date-format="yyyy-mm-dd" data-date-end-date="0d" class="form-control datepicker" name="occurrenceDate" id="occurrenceDate" />
                                                     </div> -->
                                                    <fieldset>
                                                        <legend>Schedule</legend>
                                                        <div class="form-group row">
                                                            <div class="col-lg-12">
                                                                <div class="userActions">
                                                                    <legend>Select days<sup class="required">*</sup></legend>
                                                                    <div style="display: flex;">
                                                                        <div style="flex: 1;"
                                                                             class="custom-control custom-checkbox userActions">

                                                                            <input type="checkbox" value="M"
                                                                                   name="selectDays[]"
                                                                                   class="custom-control-input "
                                                                                   id="selectDays1">
                                                                            <label class="custom-control-label"
                                                                                   for="selectDays1">M</label>

                                                                        </div>
                                                                        <div style="flex: 1;"
                                                                             class="custom-control custom-checkbox userActions">

                                                                            <input type="checkbox" value="T"
                                                                                   name="selectDays[]"
                                                                                   class="custom-control-input "
                                                                                   id="selectDays2">
                                                                            <label class="custom-control-label"
                                                                                   for="selectDays2">T</label>

                                                                        </div>
                                                                        <div style="flex: 1;"
                                                                             class="custom-control custom-checkbox userActions">

                                                                            <input type="checkbox" value="W"
                                                                                   name="selectDays[]"
                                                                                   class="custom-control-input "
                                                                                   id="selectDays3">
                                                                            <label class="custom-control-label"
                                                                                   for="selectDays3">W</label>

                                                                        </div>
                                                                        <div style="flex: 1;"
                                                                             class="custom-control custom-checkbox userActions">

                                                                            <input type="checkbox" value="TH"
                                                                                   name="selectDays[]"
                                                                                   class="custom-control-input "
                                                                                   id="selectDays4">
                                                                            <label class="custom-control-label"
                                                                                   for="selectDays4">TH</label>

                                                                        </div>
                                                                        <div style="flex: 1;"
                                                                             class="custom-control custom-checkbox userActions">

                                                                            <input type="checkbox" value="F"
                                                                                   name="selectDays[]"
                                                                                   class="custom-control-input "
                                                                                   id="selectDays5">
                                                                            <label class="custom-control-label"
                                                                                   for="selectDays5">F</label>

                                                                        </div>
                                                                        <div style="flex: 1;"
                                                                             class="custom-control custom-checkbox userActions">

                                                                            <input type="checkbox" value="SA"
                                                                                   name="selectDays[]"
                                                                                   class="custom-control-input "
                                                                                   id="selectDays6">
                                                                            <label class="custom-control-label"
                                                                                   for="selectDays6">SA</label>

                                                                        </div>
                                                                        <div style="flex: 1;"
                                                                             class="custom-control custom-checkbox userActions">

                                                                            <input type="checkbox" value="SU"
                                                                                   name="selectDays[]"
                                                                                   class="custom-control-input "
                                                                                   id="selectDays7">
                                                                            <label class="custom-control-label"
                                                                                   for="selectDays7">SU</label>

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!--  <div class="form-group">
                                                             <label for="">Date</label>
                                                             <input type="text"  placeholder="Please Select Date" data-date-format="yyyy-mm-dd" data-date-end-date="0d" class="form-control" name="scheduleDate" id="scheduleDate" />
                                                         </div> -->
                                                        <div class="form-group">
                                                            <label for="">Time<sup class="required">*</sup></label>
                                                            <input class="form-control timepicker" name="time" id="time"
                                                                   type="text" placeholder="Please Select Time"

                                                                   />
                                                        </div>
                                                    </fieldset>
                                                    <!--  <div class="form-group">
                                                        <label for="">Add CC</label>
                                                        <input  class="form-control" name="ccEmails" type="text"  placeholder="Please Enter Email Address"  />
                                                    </div> -->
                                                    <div class="form-group ccEmailArea email-id-row ccChangePadding" >
                                                        <label for="">Add cc</label>
                                                        <input type='text' id='cc_emailBS' placeholder="testing" name='ccEmails' class='form-control ccemail' title="Why is this tooltip showing on hover?" autocomplete="off">
                                                        {{--<input type="text" placeholder="Write CC Email" id="essai" name="ccEmails" class="form-control ccEmailSelect2">--}}
                                                    </div>

                                                    {{--<div class="form-group">
                                                        <label for="">Add CC</label>
                                                        <input class="form-control" id="ccEmails" name="ccEmails"
                                                               type="text" placeholder="Please Enter Email Address"/>
                                                    </div>--}}

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                                data-dismiss="modal">Cancel
                                                        </button>
                                                        <button class="btn btn-primary asinScheduleSubmitButton"
                                                                type="submit">Submit
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                {{--popup for add edit form ends--}}
                {{--pop to show metrics starts--}}
                <div class="modal fade viewMetricsModel" id="viewMetricsModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                    <div class="modal-dialog modal-ls modal-dialog-centered" role="document" >
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">Metrics List</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">

                                <div class="row">
                                    <div class="col-12">
                                        <div class=" mt-0">
                                            <div class="col-xl-12 input-group mb-3">
                                                <div class="selectedMetricsDiv container-fluid">
                                                <div class="campaignSelectedMetrics">
                                                    <!--metrics_div-->
                                                    <fieldset class="userActions">
                                                        <legend>Campaign</legend>
                                                <div class="col-lg-12 text-left showCampaignSelectedMetrics">
                                                   {{--list metrics here--}}
                                                </div>
                                                    </fieldset>
                                                </div>
                                                <div class="adgroupSelectedMetrics">
                                                    <!--metrics_div-->
                                                    <fieldset class="userActions">
                                                        <legend>Ad Group</legend>

                                                        <div class="col-lg-12 text-left showAdgroupSelectedMetrics">
                                                            {{--list metrics here--}}
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="proudctAdsSelectedMetrics">
                                                    <!--metrics_div-->
                                                    <fieldset class="userActions">
                                                        <legend>Product Ads</legend>

                                                        <div class="col-lg-12 text-left showProudctAdsSelectedMetrics">
                                                            {{--list metrics here--}}
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="keywordSelectedMetrics">
                                                    <!--metrics_div-->
                                                    <fieldset class="userActions">
                                                        <legend>Keyword</legend>
                                                        <div class="col-lg-12 text-left showKeywordSelectedMetrics">
                                                            {{--list metrics here--}}
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="asinSelectedMetrics">
                                                        <!--metrics_div-->
                                                        <fieldset class="userActions">
                                                            <legend>Asins</legend>
                                                            <div class="col-lg-12 text-left showAsinSelectedMetrics">
                                                                {{--list metrics here--}}
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                {{--popup for show metrics ends--}}
            </div>
        </div>

    </div>
@endsection
