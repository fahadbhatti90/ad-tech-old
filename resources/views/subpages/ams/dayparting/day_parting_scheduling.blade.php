@extends('layout.app')
@extends('inc.side_menu')
@extends('inc.nav_bar')
@extends('modal.logout')
@section('title',$pageTitle)
@section('content')
    @push('select2css')
        <link href="{{asset('public/css/select2.min.css')}}" rel="stylesheet">
        <link href="{{asset('public/css/bootstrap-datetimepicker.min.css')}}" rel="stylesheet">
        <link rel="stylesheet" href="{{asset('public/tooltipster/dist/css/tooltipster.bundle.min.css')}}"/>
        <link href="{{asset('public/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet"
              type="text/css">
        <link href="{{asset('public/css/multiple-emails.css')}}" rel="stylesheet">
        <link rel="stylesheet" href="{{asset('public/ams/dayparting/css/dayparting.css')}}"/>
        <style>
            .multiple_emails-container input {
                margin-bottom: 0px !important;
                padding-top: 2% !important;
            }
        </style>
    @endpush

    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Begin Breadcrumb -->
    {{--    {{ Breadcrumbs::render('day_partying_schedule') }}--}}
    <!-- End Breadcrumb -->
        <!-- Page Heading -->
        <div class="row">
            <!-- Area Chart -->
            <div class="col-xl-12 col-lg-12">
                {{-- Show Success and Errro Messages --}}
                <div class="card shadow mb-4">
                {{--@include('subpages.vc.messages')--}}
                <!-- Card Header - Dropdown -->
                    <a href="#dayPartingScheduleContainer" class="d-block card-header py-3" data-toggle="collapse"
                       role="button" aria-expanded="true" aria-controls="dayPartingScheduleContainer">
                        <h6 class="m-0 font-weight-bold text-primary">{{isset($pageHeading)?$pageHeading:''}}</h6>
                    </a>
                    <!-- Card Content - Collapse -->
                    <div class="collapse show" id="dayPartingScheduleContainer">
                        <!-- Card Body -->
                        <div class="card-body row">
                            <div class="col-md-12">
                                <form id="dayPartingSchedule" enctype="multipart/form-data">
                                    <div class="form-row">
                                        @csrf
                                        <div class="form-group col-md-6">
                                            <label class="col-form-label">Schedule Name<sup
                                                        class="required">*</sup></label>
                                            <input type="text" class="form-control" name="scheduleName"
                                                   autocomplete="off"
                                                   placeholder="Schedule Name" required maxlength="25">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="col-form-label">Child Brand<sup
                                                        class="required">*</sup></label>
                                            <select class="form-control fkProfileIdDayParting" name="fkProfileId"
                                                    autocomplete="off">
                                                <option value="" selected>Select Child Brand</option>
                                                @if(!empty($brands))
                                                    @foreach($brands as $brand)
                                                        @isset($brand->fkId)
                                                            @if(!empty(trim($brand->ams['name'])) || !empty($brand->brand_alias[0]->overrideLabel))
                                                                @php
                                                                    $brandOptionValue = '';
                                                                       $brandOptionValue =   $brand->brand_alias != null ?
                                                                          ($brand->brand_alias != null &&
                                                                          count($brand->brand_alias) > 0 ?
                                                                          ($brand->brand_alias[0]->overrideLabel != null ?
                                                                           ($brand->brand_alias[0]->overrideLabel > 40 ?  $brand->brand_alias[0]->overrideLabel: str_limit($brand->brand_alias[0]->overrideLabel,40)):
                                                                          ($brand->ams != null ? ($brand->ams['name'] > 40 ?  $brand->ams['name']:  str_limit($brand->ams['name'],40)) : '')) :
                                                                          ($brand->ams != null ? ($brand->ams['name'] > 40 ?  $brand->ams['name']:  str_limit($brand->ams['name'],40)) : '')):
                                                                          ($brand->ams != null ? ($brand->ams['name'] > 40 ?  $brand->ams['name']:  str_limit($brand->ams['name'],40)): '');
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
                                                                <option title="{{$brandOptionTitle}}"
                                                                        value="{{$brand->fkId}}">

                                                                    {{$brandOptionValue}}
                                                                </option>
                                                            @endif
                                                        @endisset
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label class="col-form-label">Portfolio/Campaign<sup
                                                        class="required">*</sup></label>
                                            <select class="form-control portfolioCampaignType col-md-6"
                                                    name="portfolioCampaignType" autocomplete="off">
                                                <option value="" selected>Select Type</option>
                                                <option value="Campaign">Campaign</option>
                                                <option value="Portfolio">Portfolio</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="col-form-label">Portfolios/Campaigns<sup
                                                        class="required">*</sup></label>
                                            <select class="form-control col-md-6 js-example-basic-multiple" name="pfCampaigns[]"
                                                    id="pfCampaigns" autocomplete="off" multiple="multiple" required
                                                    disabled="disabled">
                                            </select>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label class="col-form-label mr-4">Days of Week<sup class="required">*</sup></label>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" value="1" name="mon" class="custom-control-input" id="userAction1">
                                                <label class="custom-control-label" for="userAction1">M</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" value="1" name="tue" class="custom-control-input" id="userAction2">
                                                <label class="custom-control-label" for="userAction2">T</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" value="1" name="wed" class="custom-control-input" id="userAction3">
                                                <label class="custom-control-label" for="userAction3">W</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" value="1" name="thu" class="custom-control-input" id="userAction4">
                                                <label class="custom-control-label" for="userAction4">TH</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" value="1" name="fri" class="custom-control-input" id="userAction5">
                                                <label class="custom-control-label" for="userAction5">F</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" value="1" name="sat" class="custom-control-input" id="userAction6">
                                                <label class="custom-control-label" for="userAction6">SA</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" value="1" name="sun" class="custom-control-input" id="userAction7">
                                                <label class="custom-control-label" for="userAction7">SU</label>
                                            </div>

                                        </div>
                                        <div class="form-group col-md-12" style="margin-bottom: 2px !important;">
                                            <div style="display: flex;">
                                                <div class="form-group" style="flex: 1">
                                                    <lable>Start Time<sup
                                                                style="font-size: 15px;color:red;font-weight: bold;">*</sup><sup
                                                                class="timezone-offset">{{Config::get('app.timeOffset')}}</sup>
                                                    </lable>
                                                    <input style="margin-top: 4px;" type="text" class="form-control timepicker startTime"
                                                           id="startTime" placeholder="Start Time" name="startTime">
                                                </div>
                                                <div class="form-group pl-2" style="flex: 1">
                                                    <lable>End Time<sup
                                                                style="font-size: 15px;color:red;font-weight: bold;">*</sup><sup
                                                                class="timezone-offset">{{Config::get('app.timeOffset')}}</sup>
                                                    </lable>
                                                    <input style="margin-top: 4px;" type="text" class="form-control timepicker endTime"
                                                           id="endTime" placeholder="End Time" name="endTime">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-12" style="margin-bottom: 3px;">
                                            <label class="col-form-label mr-4">Email Receipts</label>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" value="1" name="emailReceiptStart"
                                                       class="custom-control-input" id="userAction8">
                                                <label class="custom-control-label" for="userAction8">Start</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" value="1" name="emailReceiptEnd"
                                                       class="custom-control-input" id="userAction9">
                                                <label class="custom-control-label" for="userAction9">End</label>
                                            </div>
                                        </div>
                                        <div class="form-group ccEmailArea email-id-row col-md-6">
                                            <label class="col-form-label"> Add cc</label>
                                            <input type='text' id='cc_emailBS' name='ccEmails'
                                                   class='form-control ccemail'
                                                   title="Why is this tooltip showing on hover?" autocomplete="off">
                                        </div>
                                        <div class="form-group col-sm-3 offset-sm-3">
                                            <div class="form-group float-right" style="margin-top: 50px">
                                                <button type="submit" class="btn btn-primary emailInputArea float-right">
                                                    Submit
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <!-- Area Chart -->
            <div class="col-xl-12 col-lg-12">

                <div class="card shadow mb-4">
                    <a href="#dayPartingScheduleHistory" class="d-block card-header py-3" data-toggle="collapse"
                       role="button" aria-expanded="true" aria-controls="dayPartingScheduleHistory">
                        <h6 class="m-0 font-weight-bold text-primary">Active schedules</h6>
                    </a>
                    <div class="collapse show" id="dayPartingScheduleHistory">
                        <!-- Card Body -->
                        <div class="card-body">
                            <table id="dayPartingHistoryTable" class="table table-striped table-bordered"
                                   style="width:100%">
                                <thead>
                                <tr>
                                    <th style="display: none">Sr #</th>
                                    <th>Sr.#</th>
                                    <th>Schedule Name</th>
                                    <th>Portfolio/Campaign</th>
                                    <th>Included</th>
                                    <th>Monday <br> (Start / End)</th>
                                    <th>Tuesday <br> (Start / End)</th>
                                    <th>Wednesday <br> (Start / End)</th>
                                    <th>Thursday<br> (Start / End)</th>
                                    <th>Friday <br>(Start / End)</th>
                                    <th>Saturday <br> (Start / End)</th>
                                    <th>Sunday <br> (Start / End)</th>
                                    <th class="dayPartingActionBtn">Action</th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>


                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('subpages.ams.dayparting.day_parting_scheduling_edit')
@endsection
@push('select2js')
    <script type="text/javascript" src="{{asset('public/js/select2.min.js')}}"></script>
    <script src="{{asset('public/vendor/daterangepicker/moment.min.js') }}"></script>
    <script src="{{asset('public/vendor/daterangepicker/moment-timezone.js') }}"></script>
    <script type="text/javascript" src="{{asset('public/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('public/tooltipster/dist/js/tooltipster.bundle.min.js')}}"></script>
    <script src="{{asset('public/vendor/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('public/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('public/ams/dayparting/js/dayparting.js?'.time())}}"></script>
    <script type="text/javascript" src="{{asset('public/js/multiple-emails.js?'.time())}}"></script>
    {{--    <script type="text/javascript" src="{{asset('public/js/jquery.email.multiple.js?'.time())}}"></script>--}}
    <script src="{{asset('public/js/vc_scripts/vccustom.js?'.time())}}"></script>
    <script>
        $(function () {
            loadDatatables("dayPartingHistoryTable", "{{ route('getScheduleList') }}");
            $('#cc_emailBS').multiple_emails({position: "bottom"});
            ccEmailTooltip();

        });
    </script>
@endpush
