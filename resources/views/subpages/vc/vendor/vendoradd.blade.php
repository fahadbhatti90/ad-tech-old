@extends('layout.app')
@extends('inc.side_menu')
@extends('inc.nav_bar')
@extends('modal.logout')
@section('title', $pageTitle)

@section('content')

    {{-- Include Data picker Css & JS --}}
    <link href="{{asset('public/css/bootstrap-datepicker.min.css')}}" rel="stylesheet">
    <script src="{{asset('public/js/bootstrap-datepicker.min.js')}}"></script>

    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{isset($pageHeading)?$pageHeading:''}}</h1>
        </div>
        <!-- Content Row -->
        <div class="row">
            <div class="col-xl-12 col-lg-12">
                <div class="card shadow mb-4">
                    <!-- Card Header - Dropdown -->
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Add Vendor</h6>
                    </div>
                    <!-- Card Body -->
                    <div class="card-body">
                        <form id="vendorAddFormId" enctype="multipart/form-data">
                            @csrf
                            <div class = "form-group">
                                <label for ="vendor-name">Vendor</label>
                                <input type = "text" name="vendor_name" class = "form-control"
                                       placeholder="Enter Vendor e.g Xyz" maxlength="40">
                            </div>
                            <div class = "form-group">
                                <label for ="domain">Domain</label>
                                <input type = "text" name="domain" class = "form-control" placeholder="Enter Domain e.g US" maxlength="10">
                            </div>
                            <div class = "form-group">
                                <label for ="tier">Tier</label>
                                <input type = "text" name="tier" class = "form-control"
                                       placeholder="Enter Tier e.g Platinum" maxlength="10">
                            </div>
                            <button type = "submit" class = "btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Content Row -->
    </div>
    <!-- /.container-fluid -->
    {{-- Include Js Validation files --}}
    @include('subpages.vc.formvalidation')
    <script src="{{asset('public/js/vc_scripts/vendor.js')}}"></script>
    <script src="{{asset('public/js/vc_scripts/vccustom.js')}}"></script>
@endsection
