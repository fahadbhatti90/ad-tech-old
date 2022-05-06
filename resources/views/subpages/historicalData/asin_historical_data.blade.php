@extends('layout.app')
@extends('inc.side_menu')
@extends('inc.nav_bar')
@extends('modal.logout')
@section('title', isset($pageTitle)?$pageTitle:'')
@section('content')

    @push('daterangepickercss')
        <link rel="stylesheet" href="{{ asset('public/vendor/daterangepicker/daterangepicker.css') }}">
    @endpush
    
    @push('css')
        <link rel="stylesheet" href="{{asset('public/css/scraping_custom_style.css?'.time())  }}">
    @endpush

    @push('daterangepickerjs')
        <script src="{{ asset('public/vendor/daterangepicker/moment.min.js') }}"></script>
        <script src="{{asset('public/vendor/daterangepicker/daterangepicker.js')}}"></script>
    @endpush

    @push('js')
        <script src="{{ asset('public/js/scraper_scripts/historicalDataRetrival.js?'.time()) }}"></script>
    @endpush

    <!-- Begin Page Content -->
    <div class="container-fluid">
            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                {{-- <h1 class="h3 mb-0 text-gray-800">{{isset($pageHeading)?$pageHeading:''}}</h1> --}}
            </div>
        <div class="row">

            <!-- Area Chart -->
            <div class="col-xl-6 col-lg-6">
                <div class="card shadow mb-4 asinUploadCard">
                  @include('partials.formPreloader')
                    <!-- Card Header - Dropdown -->
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Export Data</h6>
                     
                    </div>
                    <!-- Card Body -->
                    <div class="card-body">
                        <form class="" action="{{ route('checkHistory') }}"  id="historicalDataRetrivalForm">
                            @csrf
                            <div class="form-group">
                                <label for="dateRamge">Select Date Range</label>
                                <input type="text"  class="form-control" name="daterange" id="dateRamge" />
                            </div>
                            <button type="submit" class="btn btn-primary getHistData">Download</button>
                        </form>
                    </div>
                </div>
            </div>
           
        </div>
    </div>
        <!-- Content Row -->

@endsection
