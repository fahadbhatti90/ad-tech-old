@extends('layout.app')
@extends('inc.side_menu')
@extends('inc.nav_bar')
@extends('modal.logout')
@section('title', isset($pageTitle)?$pageTitle:'')
@section('content')
    @push('daterangepickercss')
        <link rel="stylesheet" href="{{ asset('public/vendor/daterangepicker/daterangepicker.css') }}">
    @endpush
    @push('daterangepickerjs')
        <script src="{{ asset('public/vendor/daterangepicker/moment.min.js') }}"></script>
        <script src="{{asset('public/vendor/daterangepicker/daterangepicker.js')}}"></script>
        <script src="{{ asset('public/js/ams_scripts/historicalDataRetrival.js?'.time()) }}"></script>
    @endpush

    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Begin Breadcrumb -->

{{--    {{ Breadcrumbs::render('ams_history') }}--}}
    <!-- End Breadcrumb -->
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800 sr-only">{{isset($pageHeading)?$pageHeading:''}}</h1>
        </div>
        <div class="row">
            <!-- Area Chart -->
            <div class="col-xl-6 col-lg-6">
                <div class="card shadow mb-4 asinUploadCard">
                    <!-- Card Header - Dropdown -->
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Export Data</h6>
                    </div>
                    <!-- Card Body -->
                    <div class="card-body row">
                        <form class="col" id="historicalDataRetrivalForm">
                            @csrf
                            <div class="form-group">
                                <label class="col-form-label">List</label>
                                <select class="form-control" name="reporttype" id="reporttype">
                                    <option value="" selected>Choose...</option>
                                    <option value="Advertising_Campaign_Reports">Advertising Campaign
                                        Reports
                                    </option>
                                    <option value="Ad_Group_Reports">Ad Group Reports</option>
                                    <option value="Keyword_Reports">Keyword Reports</option>
                                    <option value="Product_Ads_Report">Product Ads Report</option>
                                    <option value="ASINs_Report">ASINs Report</option>
                                    <option value="Product_Attribute_Targeting_Reports">Product  Attribute Targeting Reports
                                    </option>
                                    <option value="Sponsored_Brand_Reports">Sponsored Brand Reports
                                    </option>
                                    <option value="Sponsored_Brand_Campaigns">
                                        Sponsored Brand Campaigns
                                    </option>
                                    <option value="Sponsored_Display_Campaigns">
                                        Sponsored Display Campaigns
                                    </option>
                                    <option value="Sponsored_Display_ProductAds">
                                        Sponsored Display ProductAds
                                    </option>
                                    <option value="Sponsored_Display_Adgroup">
                                        Sponsored Display Adgroup
                                    </option>
                                    <option value="Sponsored_Brand_Adgroup">
                                        Sponsored Brand Adgroup Report
                                    </option>
                                    <option value="Sponsored_Brand_Targeting">
                                        Sponsored Brand Targeting Report
                                    </option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="dateRange">Select Date Range</label>
                                <input type="text" class="form-control" name="daterange" id="dateRange" />
                            </div>
                            <button type="submit" class="btn btn-primary float-right" >Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Content Row -->
@endsection
