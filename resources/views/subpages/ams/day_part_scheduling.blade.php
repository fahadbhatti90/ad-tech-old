@extends('layout.app')
@extends('inc.side_menu')
@extends('inc.nav_bar')
@extends('modal.logout')
@section('title',$pageTitle)
@section('content')
    @push('select2css')
        <link href="{{asset('public/css/select2.min.css')}}" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="{{asset('public/tooltipster/dist/css/tooltipster.bundle.min.css')}}" />
        <link rel="stylesheet" type="text/css" href="{{asset('public/ams/dayParting/css/dayParting.css')}}" />
    @endpush
    @push('select2js')
        <script  type="text/javascript" src="{{asset('public/js/select2.min.js')}}"></script>
        <script src="{{asset('public/vendor/daterangepicker/moment.min.js') }}"></script>
        <script  type="text/javascript" src="{{asset('public/js/bootstrap-datetimepicker.min.js')}}"></script>
        <script  type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
        <script type="text/javascript" src="{{ asset('public/tooltipster/dist/js/tooltipster.bundle.min.js')}}"></script>
        <script  type="text/javascript" src="{{asset('public/ams/dayParting/js/dayParting.js')}}"></script>
    @endpush

    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Begin Breadcrumb -->
    {{--    {{ Breadcrumbs::render('ams_scheduling') }}--}}
    <!-- End Breadcrumb -->
        <!-- Page Heading -->
        <div class="row">
            <!-- Area Chart -->
            <div class="col-xl-12 col-lg-12">
                {{-- Show Success and Errro Messages --}}
                <div class="card shadow mb-4">
                {{--@include('subpages.vc.messages')--}}
                <!-- Card Header - Dropdown -->
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">{{isset($pageHeading)?$pageHeading:''}}</h6>
                    </div>
                    <!-- Card Body -->
                    <div class="card-body">
                        <form id="dailySalesFormId" enctype="multipart/form-data" onsubmit="return false">
                            @csrf
                            <div class="form-group">
                                <label class="col-form-label">Schedule Name</label>
                                <input type="text" class="form-control" name="schedule_name" autocomplete="off"
                                       placeholder="Schedule Name">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label">Portfolio/Campaign</label>
                                <select class="form-control js-example-basic-multiple" name="compaign"
                                        autocomplete="off">
                                    <option value="">Campaign One</option>
                                    <option value="">Campaign Two</option>
                                    <option value="">Campaign Three</option>
                                    <option value="">Campaign Four</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="col-form-label">Portfolios/Campaigns</label>
                                <select class="form-control js-example-basic-multiple" name="compaigns" placeholder="sfafd"
                                        autocomplete="off" multiple="multiple">
                                    <option value="">Portfolio One</option>
                                    <option value="">Portfolio Two</option>
                                    <option value="">Portfolio Three</option>
                                    <option value="">Portfolio Four</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="col-form-label mr-4">Day of Week</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" value="0" name="day_of_weeks[]">
                                    <label class="form-check-label">M</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" value="1" name="day_of_weeks[]">
                                    <label class="form-check-label">T</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" value="2" name="day_of_weeks[]">
                                    <label class="form-check-label">W</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" value="3" name="day_of_weeks[]">
                                    <label class="form-check-label">TH</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" value="4" name="day_of_weeks[]">
                                    <label class="form-check-label">F</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" value="5" name="day_of_weeks[]">
                                    <label class="form-check-label">SA</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" value="6" name="day_of_weeks[]">
                                    <label class="form-check-label">SU</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-form-label mr-2">Start Time</label>
                                <div class="form-check form-check-inline">
                                    <input type="text" class="form-control timepicker">
                                </div>
                                <label class="col-form-label mr-2">End Time</label>
                                <div class="form-check form-check-inline">
                                    <input type="text" class="form-control timepicker">
                                </div>

                            </div>

                            <div class="form-group col-lg-5 customFormGroup">

                                <div class="ccInputContainer">
                                    <input type="text" placeholder="Write CC Emails" class="form-control ccInputField">
                                    <a href="#" class="btn btn-primary anchorClass">Add CC</a>
                                </div>

                                    <div class="mainControls">
                                        <label class="col-form-label mr-4 emailInputArea">Email Receipts</label>
                                        <div class="form-check form-check-inline emailInputArea">
                                            <input class="form-check-input" type="checkbox" value="0" name="day_of_weeks[]">
                                            <label class="form-check-label">Start</label>
                                        </div>
                                        <div class="form-check form-check-inline emailInputArea">
                                            <input class="form-check-input" type="checkbox" value="1" name="day_of_weeks[]">
                                            <label class="form-check-label">End</label>
                                        </div>
                                        <div class="form-check form-check-inline emailInputArea">
                                            <h6>
                                                <b><a href="#" class="anchorClass ">Add CC</a></b>
                                            </h6>
                                        </div>
                                        <button type="submit" class="btn btn-primary emailInputArea">Submit</button>
                                    </div>


                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row productTable">
            <div class="col-xl-12 col-md-12 mb-4">
                <div class="card mb-4">
                    <div class="card-header py-3" style="display: flex;">
                        <h6 class="m-0 font-weight-bold text-primary" style="flex-basis:70%">
                            Day Parting History
                        </h6>
                    </div>
                    <div class="card-body schedulingCardBody">
                        <table id="dayPartingHistoryTable" class="table table-striped table-bordered"
                               style="width:100%">
                            <thead>
                            <tr>
                                <th>Schedule Name</th>
                                <th>Compaign</th>
                                <th>Included</th>
                                <th>M <br> (Start / End)</th>
                                <th>T <br> (Start / End)</th>
                                <th>W <br> (Start / End)</th>
                                <th>Th<br> (Start / End)</th>
                                <th>F <br>(Start / End)</th>
                                <th>Sa <br> (Start / End)</th>
                                <th>Su <br> (Start / End)</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Weekend_pants <a href="#"> Edit</a></td>
                                    <td>Compaingn 1</td>
                                    <td class="tooltip-dayParty" title="compaign 1, compaign 2">
                                        List
                                    </td>
                                    <td>1.00 AM / 2.00 AM</td>
                                    <td>1.00 AM / 2.00 AM</td>
                                    <td>1.00 AM / 2.00 AM</td>
                                    <td>1.00 AM / 2.00 AM</td>
                                    <td>1.00 AM / 2.00 AM</td>
                                    <td>1.00 AM / 2.00 AM</td>
                                    <td>1.00 AM / 2.00 AM</td>
                                </tr>
                                <tr>
                                    <td>Weekend_pants <a href="#"> Edit</a></td>
                                    <td>Compaingn 1</td>
                                    <td class="tooltip-dayParty" title="compaign 1, compaign 2">
                                        List
                                    </td>
                                    <td>1.00 AM / 2.00 AM</td>
                                    <td>1.00 AM / 2.00 AM</td>
                                    <td>1.00 AM / 2.00 AM</td>
                                    <td>1.00 AM / 2.00 AM</td>
                                    <td>1.00 AM / 2.00 AM</td>
                                    <td>1.00 AM / 2.00 AM</td>
                                    <td>1.00 AM / 2.00 AM</td>
                                </tr>
                                <tr>
                                    <td>Weekend_pants <a href="#"> Edit</a></td>
                                    <td>Compaingn 1</td>
                                    <td class="tooltip-dayParty" title="compaign 1, compaign 2">
                                        List
                                    </td>
                                    <td>1.00 AM / 2.00 AM</td>
                                    <td>1.00 AM / 2.00 AM</td>
                                    <td>1.00 AM / 2.00 AM</td>
                                    <td>1.00 AM / 2.00 AM</td>
                                    <td>1.00 AM / 2.00 AM</td>
                                    <td>1.00 AM / 2.00 AM</td>
                                    <td>1.00 AM / 2.00 AM</td>
                                </tr>
                                <tr>
                                    <td>Weekend_pants <a href="#"> Edit</a></td>
                                    <td>Compaingn 1</td>
                                    <td class="tooltip-dayParty" title="compaign 1, compaign 2">
                                        List
                                    </td>
                                    <td>1.00 AM / 2.00 AM</td>
                                    <td>1.00 AM / 2.00 AM</td>
                                    <td>1.00 AM / 2.00 AM</td>
                                    <td>1.00 AM / 2.00 AM</td>
                                    <td>1.00 AM / 2.00 AM</td>
                                    <td>1.00 AM / 2.00 AM</td>
                                    <td>1.00 AM / 2.00 AM</td>
                                </tr>
                            </tbody>
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- /.container-fluid -->
    <script>
        $(document).ready(function () {
            $('.js-example-basic-multiple').select2({});
        });
        $('.tooltip-dayParty').tooltipster({
            interactive:true,
            maxWidth:300
        });
    </script>
@endsection
