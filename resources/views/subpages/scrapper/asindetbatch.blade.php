@extends('layout.app')
@extends('inc.side_menu')
@extends('inc.nav_bar')
@extends('modal.logout')
@section('title', isset($pageTitle)?$pageTitle:'')
@section('content')

    @push('css')
    <link rel="stylesheet" href="{{asset('public/css/scraping_custom_style.css?'.time())  }}">
    @endpush
    @push('js')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/es6-shim/0.35.3/es6-shim.min.js"></script>
        <script src="{{ asset('public/js/scraper_scripts/ASINUploadFromCustomVaildation.js?'.time()) }}"></script>
    @endpush

    <!-- Begin Page Content -->
    <div class="container-fluid">
            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                {{-- <h1 class="h3 mb-0 text-gray-800">{{isset($pageHeading)?$pageHeading:''}}</h1> --}}
            </div>
        <div class="row">

            <!-- Area Chart -->
            <div class="col-xl-12 col-lg-12">
                <div class="card shadow mb-4 asinUploadCard">
                        @include('partials.formPreloader')
                    <!-- Card Header - Dropdown -->
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Upload Batch</h6>
                     
                    </div>
                    <!-- Card Body -->
                    <div class="card-body">
                    <form action="{{ route('uploadFile') }}" id="assinScrapingForm" class="col-lg-4">
                        @csrf
                        
                        <div class="form-group">
                            <label for="excel">ASINs File</label>
                            <div class="custom-file">
                                <input type="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"  class="custom-file-input" id="excel" name="excel">
                                <label class="custom-file-label" for="excel"><span>Choose File</span></label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="coll-name">Collection Name</label>
                            <input type="text" class="form-control"
                                id="coll-name" name="colectionName" placeholder="Collection Name">
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-radio">
                                <input type="radio" class="custom-control-input" id="customRadio" value="d"  name="daily-instant[]"
                                    checked   >
                                <label class="custom-control-label" for="customRadio">Daily</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-radio">
                                <input type="radio" class="custom-control-input" id="customRadio2" value="i" name="daily-instant[]"
                                    >
                                <label class="custom-control-label" for="customRadio2">Instant</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary asinSubmitButton">Submit</button>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
        <!-- Content Row -->

@endsection
