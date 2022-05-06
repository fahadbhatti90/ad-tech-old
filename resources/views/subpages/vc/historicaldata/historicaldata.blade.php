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
        <script src="{{ asset('public/js/vc_scripts/historical_data.js?'.time()) }}"></script>
        {{--<script src="{{asset('public/js/vc_scripts/vccustom.js')}}"></script>--}}
    @endpush
    
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{isset($pageHeading)?$pageHeading:''}}</h1>
        </div>
        
        <!-- Content Row -->
        <div class="row">
            
            <!-- Area Chart -->
            <div class="col-xl-12 col-lg-12">
                {{-- Show Success and Errro Messages --}}
                <div class="card shadow mb-4">
                {{--@include('subpages.vc.messages')--}}
                <!-- Card Header - Dropdown -->
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Export Data</h6>
                    </div>
                    <!-- Card Body -->
                    <div class="card-body">
                        <form id="VCHistoricalDataRetrivalForm" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label class="sr-only" for="purchase-vendor">Vendors</label>
                                <select class="form-control" name="historicalDataReportType"
                                        id="historicalDataReportType" autocomplete="off">
                                    <option value="" selected="">Choose...</option>
                                    <option value="daily_sales">Daily Sales</option>
                                    <option value="purchase_order">Purchase Order</option>
                                    <option value="daily_inventory">Daily Inventory</option>
                                    <option value="traffic">Traffic</option>
                                    <option value="forecast">Forecast</option>
                                    <option value="product_catalog">Product Catalog</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="dateRange">Select Date Range</label>
                                <input type="text" class="form-control" name="daterange"/>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Content Row -->

@endsection
