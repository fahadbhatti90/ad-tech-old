@extends('layout.app')
@extends('inc.side_menu')
@extends('inc.nav_bar')
@extends('modal.logout')
@section('title', $pageTitle)

@section('content')
    @push('daterangepickercss')
        <link rel="stylesheet" href="{{ asset('public/vendor/daterangepicker/daterangepicker.css') }}">
    @endpush
    @push('daterangepickerjs')
        <script src="{{ asset('public/vendor/daterangepicker/moment.min.js') }}"></script>
        <script src="{{asset('public/vendor/daterangepicker/daterangepicker.js')}}"></script>
        <script src="{{asset('public/js/vc_scripts/daily_inventory.js?'.time())}}"></script>
        <script src="{{asset('public/js/vc_scripts/vccustom.js')}}"></script>
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

                <div class="card shadow mb-4">
                    <!-- Card Header - Dropdown -->
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Upload Files CSV Format</h6>
                    </div>
                    <!-- Card Body -->
                    <div class="card-body">
                        <form id="dailyInventoryFormId" enctype="multipart/form-data">
                            @csrf
                        <div class = "form-group">
                            <label class="sr-only" for ="vendor">Vendors</label>
                            <select class = "form-control" name="vendor" autocomplete="off">
                                <option value="">Select Any Vendor</option>
                                @foreach($vendors as $key)
                                    <option value="{{$key->vendor_id}}">{{$key->vendor_name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class = "form-group">
                            <label for = "daily-inventory">Upload</label>
                            <div class="custom-file">

                                <input type = "file" class = "custom-file-input" name="daily_inventory">
                                <label class="custom-file-label" for="daily-inventory">Choose File</label>
                            </div>
                        </div>

                        <div class = "form-group">
                            <label for = "upload-file-date">Date</label>

                            <input type = "text" name="daily_inventory_date" class="form-control date"
                                   placeholder="mm/dd/yyyy" autocomplete="off">
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
@endsection
