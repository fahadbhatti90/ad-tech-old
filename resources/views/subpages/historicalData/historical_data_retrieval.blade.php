@extends('layout.app')
@extends('inc.side_menu')
@extends('inc.nav_bar')
@extends('modal.logout')
@section('title', isset($pageTitle)?$pageTitle:'')
@section('content')

    @push('css')

    <link rel="stylesheet" href="{{ asset('public/vendor/daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{asset('public/css/scraping_custom_style.css?'.time())  }}">
    @endpush
    @push('js')

    <script src="{{ asset('public/vendor/daterangepicker/moment.min.js') }}"></script>
    <script src="{{asset('public/vendor/daterangepicker/daterangepicker.js')}}"></script>
    
    <script src="{{ asset('public/js/scraper_scripts/historicalDataRetrival.js?'.time()) }}"></script>
 
    @endpush

    <!-- Begin Page Content -->
    <div class="container-fluid">
            <!-- Begin Breadcrumb -->

            {{ Breadcrumbs::render('history') }}
            <!-- End Breadcrumb -->
            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">{{isset($pageHeading)?$pageHeading:''}}</h1>
            </div>
        <div class="row">

            <!-- Area Chart -->
            <div class="col-xl-12 col-lg-12">
                <div class="card shadow mb-4 asinUploadCard">
                    <div class="overlayAjaxStatus">
                        <div class="status"></div>
                    </div>
                    <!-- Card Header - Dropdown -->
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Export Data</h6>
                     
                    </div>
                    <!-- Card Body -->
                    <div class="card-body">
                    <form class=" col-lg-4" action="{{ route('checkHistory') }}"  id="historicalDataRetrivalForm">
                        @csrf
                        <div class="form-group">
                            <label for="dateRamge">Select Date Range</label>
                            <input type="text"  class="form-control" name="daterange" id="dateRamge" />
                        </div>
                        <button type="submit" class="btn btn-primary getHistData">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
        <!-- Content Row -->

@endsection
