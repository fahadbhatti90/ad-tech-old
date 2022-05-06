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

            <!-- Area Chart -->
            <div class="col-xl-12 col-lg-12">

                <div class="card shadow mb-4">
                    <!-- Card Header - Dropdown -->
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Upload Files CSV Format</h6>

                    </div>
                    <!-- Card Body -->
                    <div class="card-body">
                        <form id="purchaseOrderFormId" enctype="multipart/form-data">
                            @csrf
                            <div class = "form-group">
                                <label class="sr-only" for ="vendor">Vendors</label>
                                <select class = "form-control"  name="vendor" autocomplete="off">
                                    <option value="">Select Any Vendor</option>
                                    @foreach($vendors as $key)
                                        <option value="{{$key->vendor_id}}">{{$key->vendor_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class = "form-group">
                                <label for = "open-agg">Open AGG</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="open-agg" name="open_agg_file" accept=".csv,.xlsx">
                                    <label class="custom-file-label" for="open-agg">Choose File</label>
                                </div>
                            </div>
                            <div class = "form-group">
                                <label for = "open-nonagg">Open Non-AGG</label>
                                <div class="custom-file">
                                    <input type = "file" class = "custom-file-input" id = "open-nonagg" name="open_nonagg_file" accept=".csv,.xlsx">
                                    <label class="custom-file-label" for="open-nonagg">Choose File</label>
                                </div>
                            </div>
                            <div class = "form-group">
                                <label for = "close-agg">Close AGG</label>
                                <div class="custom-file">

                                    <input type = "file" class = "custom-file-input" id = "close-agg" name="close_agg_file" accept=".csv,.xlsx">
                                    <label class="custom-file-label" for="close-agg">Choose File</label>
                                </div>
                            </div>
                            <div class = "form-group">
                                <label for = "close-agg">Close Non-AGG</label>
                                <div class="custom-file">

                                    <input type = "file" class = "custom-file-input" id = "close-nonagg" name="close_nonagg_file" accept=".csv,.xlsx">
                                    <label class="custom-file-label" for="close-nonagg">Choose File</label>
                                </div>
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
    @include('subpages.vc.formvalidation')
    <script src="{{asset('public/js/vc_scripts/purchase_order.js')}}"></script>
    <script src="{{asset('public/js/vc_scripts/vccustom.js')}}"></script>
@endsection
