@extends('layout.appclient')
@extends('inc.side_menu')
@extends('inc.nav_bar')
@extends('modal.logout')
@section('title', isset($pageTitle)?$pageTitle:'Dashboard')

@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/tooltipster/dist/css/tooltipster.bundle.min.css')}}" />
    
    <link href="{{asset('public/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('public/vendor/datatables/responsive.dataTables.min.css')}}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{ asset('public/LabelOverride/css/LabelOverride.css?'.time())  }}">
@endpush
@section('content')
        <!-- /.container-fluid -->
        <!-- Begin Page Content -->
        <div class="container-fluid clientDasboard">


            <div class="row productTable" action-add-tag="">
                
                <div class="col-xl-12 col-md-12 mb-4">
                    @if (!$inventoryHasData)
                        <div class="alert alert-danger text-capitalize" role="alert">
                            No Record Found In Inventory Label Override Will Not Work
                        </div>   
                    @endif
                    <div class="alert alert-info text-capitalize" role="alert">
                        Use buttons to the right for downloading file format of respective attribute 
                    </div>  
                    <div class="d-flex flex-row justify-content-end mb-3">
                        <input type="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" class="d-none bulkInsertAliasFileControl" action-url="{{ route('label.override.upload.alias') }}">
                        <div class="downloadButton">
                            <a href="{{ route('download.attribute.data',1) }}" class="btn btn-primary downloadAttributeData mx-3" attibute-type="1" id="download_xls" >Product <span class="fa fa-download"></span></a>
                            <a href="{{ route('download.attribute.data',2) }}" class="btn btn-primary downloadAttributeData mx-3" attibute-type="2" id="download_xls" >Sub Category <span class="fa fa-download"></span></a>
                            <a href="{{ route('download.attribute.data',3) }}" class="btn btn-primary downloadAttributeData mx-3" attibute-type="3" id="download_xls" >Category <span class="fa fa-download"></span></a>
                            <a href="{{ route('download.attribute.data',4) }}" class="btn btn-primary downloadAttributeData mx-3" attibute-type="4" id="download_xls" >Brand <span class="fa fa-download"></span></a>
                        </div>
                        <button class="btn btn-primary bulkInsertAliasButton" id="download_xls" >XLS / CSV <span class="fa fa-upload"></span></button>
                    </div>
                   
                    <div class="card mb-4 shadow">
                        <div class="card-header py-3  d-flex flex-row align-items-center justify-content-between" >
                            
                            <h6 class="m-0 font-weight-bold text-primary" style="flex-basis:70%">
                                Label Override 
                            </h6>
                            <div class="dropdown no-arrow">
                                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                  <i class="fas fa-filter fa-sm fa-fw text-gray-700"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink" x-placement="bottom-end" style="position: absolute; transform: translate3d(-156px, 18px, 0px); top: 0px; left: 0px; will-change: transform;">
                                    <div class="dropdown-header">View Single</div>
                                    <a class="dropdown-item filter" href="#" data-column="1" data-name="ASIN" >Product title</a>
                                    <a class="dropdown-item filter" href="#" data-column="2" data-name="subcategory_id" >SubCategory</a>
                                    <a class="dropdown-item filter" href="#" data-column="3" data-name="category_id" >Category</a>
                                    <a class="dropdown-item filter" href="#" data-column="4" data-name="fk_account_id" >Brand</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item filter" href="#" data-column="all" data-name="all" >Show All</a>
                                    </div>
                                </div>
                        </div>
                        <div class="card-body LabelOverrdieCardBody">
                                <table id="dataTable" class="table table-bordered  cronListTable" style="width:100%" datatable-url="{{ route("label.override.data") }}">
                                        <thead>
                                            <tr>
                                                <th>ASIN</th>
                                                <th>Product Title</th>
                                                <th>Sub Category Name</th>
                                                <th>Category Name</th>
                                                <th>AccountName</th>
                                            </tr>
                                        </thead>
                                        <tbody class="allASINS">
                                        
                                        </tbody>
                                </table>
                                <div class="addAliasContainer bg-white border-0 position-absolute w-100 ">
                                   
                                    <div class="aliasDetails bg-light position-relative ">
                                        <span class="border d-inline-block position-absolute cloaseButton">×</span>
                                        <p class="m-0 text-capitalize labelOverride-title ">
                                            <b>Original Product Title :</b>
                                            <span class="orignal">Suave Kids Shampoo and Conditioner Coconut 12 Ounc...</span> 
                                        </p>
                                        <p class="mb-0 text-capitalize labelOverride-alias"><b>Alias:</b><span class="alias">Not Available</span></p>
                                    </div>

                                    <div class="functionalitySection position-relative">
                                        <span class="badge bg-white information position-absolute px-2">Press enter to save</span>
                                        <input action-url = "{{ route('label.override.post.data') }}" class="addAliasBox border-0 shown w-100" row="1" placeholder="Please Enter Alias Brand">
                                        <span class="bg-white border border-top-0 characterCounter d-none position-absolute w-auto">
                                            <span class="dynamicCounter">0</span>-<span class="counterLimit">100</span>
                                        </span>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
@push('js')
    <script type="text/javascript" src="{{ asset('public/tooltipster/dist/js/tooltipster.bundle.min.js')}}"></script>
    <script src="{{asset('public/vendor/datatables/jquery.dataTables.min.js')}}"></script> 
    <script src="{{asset('public/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{asset('public/vendor/datatables/dataTables.responsive.min.js')}}"></script>
    <script src="{{ asset('public/LabelOverride/js/LabelOverride.js?'.time()) }}"></script>
    <script src="{{ asset('public/LabelOverride/js/LabelOverrideAddAlias.js?'.time()) }}"></script>
    <script src="{{ asset('public/LabelOverride/js/LabelOverrideBulkInsertionUploadFile.js?'.time()) }}"></script>
@endpush