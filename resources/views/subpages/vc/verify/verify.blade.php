@extends('layout.app')
@extends('inc.side_menu')
@extends('inc.nav_bar')
@extends('modal.logout')
@section('title', $pageTitle)

@section('content')

    {{-- Include Data picker Css & JS --}}
    @push('daterangepickercss')
        <link rel="stylesheet" href="{{ asset('public/vendor/daterangepicker/daterangepicker.css') }}">
    @endpush
    @push('daterangepickerjs')
        <script src="{{ asset('public/vendor/daterangepicker/moment.min.js') }}"></script>
        <script src="{{asset('public/vendor/daterangepicker/daterangepicker.js')}}"></script>
        <script src="{{asset('public/js/vc_scripts/deleterecord.js?'.time())}}"></script>
        <script src="{{asset('public/js/vc_scripts/vccustom.js')}}"></script>
    @endpush

    <!-- Begin Page Content -->
    <div class="container-fluid">

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
                        <h6 class="m-0 font-weight-bold text-primary">Verify Record</h6>
                    </div>
                    <!-- Card Body -->
                    <div class="card-body">
                        <form id="deleteFormId" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label class="sr-only" for="purchase-vendor">Vendors</label>
                                <select class="form-control" name="vendor" autocomplete="off">
                                    <option value="">Select Any Vendor</option>
                                    @foreach($vendors as $key)
                                        <option value="{{$key->vendor_id}}">{{$key->vendor_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="" for="type">Type</label>
                                <select class="form-control" name="type" autocomplete="off">
                                    <option value="">Select Report Type</option>
                                    <option value="daily_sales">Daily Sales</option>
                                    <option value="purchase_order">Purchase Order</option>
                                    <option value="daily_inventory">Daily Inventory</option>
                                    <option value="traffic">Traffic</option>
                                    <option value="forecast">Forecast</option>
                                    <option value="product_catalog">Product Catalog</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="dateRange">Select Start & End Date Range (Only for delete)</label>
                                <input type="text" class="form-control" name="daterange"/>
                            </div>
                            <button id="delete" type="button" class="btn btn-primary btn_form_submit">Delete</button>
                            <button id="verify" type="button" class="btn btn-primary btn_form_submit">Verify</button>
                            <button id="move_to_main" type="button" class="btn btn-primary btn_form_submit">Move to Main</button>
                        </form>
                    </div>
                </div>
                <div id="carrierReturnData" ></div>
            </div>
        </div>
        <!-- Content Row -->
    </div>
    <!-- /.container-fluid -->
    <!-- Modal -->
    <div class="modal fade" id="comfirmationModal" tabindex="-1" role="dialog" aria-labelledby="comfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title w-100 text-center text-primary" id="exampleModalLabel">Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-center">Are you sure to <span id="type"></span> the data ?</p>
                </div>
                <div class="modal-footer d-flex justify-content-center border-0">
                    <button type="button" class="btn btn-secondary" id="modal-btn-no" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="modal-btn-yes"></button>
                </div>
            </div>
        </div>
    </div>
@endsection
