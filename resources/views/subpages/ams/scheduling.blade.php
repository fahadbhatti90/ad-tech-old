@extends('layout.app')
@extends('inc.side_menu')
@extends('inc.nav_bar')
@extends('modal.logout')
@section('title',$pageTitle)
@section('content')
    @push('styles')
        <link href="{{asset('public/css/styles.css?'.time())}}" rel="stylesheet">
    @endpush
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Begin Breadcrumb -->
{{--    {{ Breadcrumbs::render('ams_scheduling') }}--}}
    <!-- End Breadcrumb -->
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800 sr-only">{{isset($pageHeading)?$pageHeading:''}}</h1>
        </div>
        <div class="card mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">List</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    {{-- Cron Job List --}}
                    <div class="col-6">
                        <h6 class="m-0 font-weight-bold text-primary">Scheduling List</h6>
                        <div class="row mt-3">
                            {{-- Get Profile List --}}
                            <div class="col-xl-12 input-group mb-3">
                                <form id="cronFrom" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label class="col-sm-6 col-form-label">List</label>
                                        <select class="form-control" name="crontype" id="crontype">
                                            <option value="" selected>Choose...</option>
                                            <option value="Advertising_Campaign_Reports">Advertising Campaign
                                                Report
                                            </option>
                                            <option value="Ad_Group_Reports">Ad Group Report</option>
                                            <option value="Keyword_Reports">Keyword Report</option>
                                            <option value="Product_Ads_Report">Product Ads Report</option>
                                            <option value="ASINs_Report">ASINs Report</option>
                                            <option value="Product_Attribute_Targeting_Reports">
                                                Product Attribute Targeting Report
                                            </option>
                                            <option value="Sponsored_Brand_Reports">
                                                Sponsored Brand Keyword Report
                                            </option>
                                            <option value="Sponsored_Brand_Campaigns">
                                                Sponsored Brand Campaigns Report
                                            </option>
                                            <option value="Sponsored_Display_Campaigns">
                                                Sponsored Display Campaigns Report
                                            </option>
                                            <option value="Sponsored_Display_ProductAds">
                                                Sponsored Display ProductAds Report
                                            </option>
                                            <option value="Sponsored_Display_Adgroup">
                                                Sponsored Display Adgroup Report
                                            </option>
                                            <option value="Sponsored_Brand_Adgroup">
                                                Sponsored Brand Adgroup Report
                                            </option>
                                            <option value="Sponsored_Brand_Targeting">
                                                Sponsored Brand Targeting Report
                                            </option>
                                        </select>
                                    </div>
                                    <div class="form-group ">
                                        <label class="col-sm-6 col-form-label">Time List</label>
                                        <select class="form-control" name="crontime" id="crontime">
                                            <option value="" selected>Choose...</option>
                                            @php
                                                $time_now_new = strtotime('00:00');
                                            @endphp
                                            @for($i=0; $i<24; $i++)
                                                @php $time_now_new = date("H:i", strtotime($time_now_new)); @endphp
                                                <option value="{{$time_now_new}}">{{$time_now_new}}</option>
                                                @php $time_now_new = date("H:i", strtotime('+60 minutes', strtotime($time_now_new))); @endphp
                                            @endfor
                                        </select>

                                    </div>
                                    <div class="form-group ">
                                        <label class="col-sm-6 col-form-label">Status</label>

                                        <div class="form-check form-check-inline">
                                            <input type="radio" class="form-check-input" name="cronstatus"
                                                   value="stop"/>
                                            <label class="form-check-label">Stop</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input type="radio" class="form-check-input" name="cronstatus"
                                                   value="run"/>
                                            <label class="form-check-label">Run</label>
                                        </div>
                                    </div>
                                    <!-- Do NOT use name="submit" or id="submit" for the Submit button -->
                                    <div class="form-group row mx-auto">
                                        <div class="col-sm-10">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="submit">Save
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    {{-- Cron List --}}
                    <div class="col-6">
                        <h6 class="m-0 font-weight-bold text-primary">Scheduled List</h6>
                        @if(!empty($CronListData))
                            <table class="table mt-5">
                                <thead>
                                <tr>
                                    <th scope="col">Name</th>
                                    <th scope="col">Time</th>
                                    <th scope="col">Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($CronListData as $single)
                                    <tr>
                                        <td>{{str_replace('_',' ',($single->cronType == 'Sponsored_Brand_Reports')
                                        ?'Sponsored Brand Keyword':$single->cronType)}}</td>
                                        <td>{{$single->cronTime}}</td>
                                        <td>{{ucwords($single->cronStatus)}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @else
                            <p>No List Found.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- /.container-fluid -->
    <script src="{{asset('public/js/ams_scripts/cron.js?'.time())}}"></script>
@endsection
