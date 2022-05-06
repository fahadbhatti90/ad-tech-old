@extends('layout.app')
@extends('inc.side_menu')
@extends('inc.nav_bar')
@extends('modal.logout')
@section('title', isset($pageTitle)?$pageTitle:'')
@section('content')

@push('css')

    <link rel="stylesheet" type="text/css" href="{{ asset('public/tooltipster/dist/css/tooltipster.bundle.min.css')}}" />
    <link rel="stylesheet" href="{{asset('public/css/scraping_custom_style.css?'.time())  }}">
@endpush
@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/es6-shim/0.35.3/es6-shim.min.js"></script>
    
    <script type="text/javascript" src="{{ asset('public/tooltipster/dist/js/tooltipster.bundle.min.js')}}"></script>
    <script src="{{ asset('public/js/searchRank/addDepartment.js?'.time()) }}"></script>
@endpush

 <!-- Begin Page Content -->
 <div class="container-fluid">
        <!-- Begin Breadcrumb -->

        {{ Breadcrumbs::render('add_department') }}
        <!-- End Breadcrumb -->
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            {{-- <h1 class="h3 mb-0 text-gray-800">{{isset($pageHeading)?$pageHeading:''}}</h1> --}}
        </div>
    <div class="row">

        <!-- Area Chart -->
        <div class="col-xl-12 col-lg-12">
            <div class="card shadow mb-4 addDepartmentCard">
                    @include('partials.formPreloader')
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Add Department</h6>
                </div>
                <div class="tooltip_templates">
                    <span id="tooltip_content">
                        Like 'arts-crafts' for Department name 'Arts, Crafts & Sewing' get it from amazon site depratment url <strong>ex. </strong><a href="https://www.amazon.com/s?k=sewing+machine&i=arts-crafts" target="_blank">(https://www.amazon.com/s?k=sewing+machine&i=arts-crafts)</a>
                    </span>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                <form action="{{ route('addDepartment') }}" id="addDepartmentForm">
                    @csrf
                    <div class="form-group">
                        <label for="coll-name">Department Name</label>
                        <input type="text" class="form-control"
                            id="coll-name" name="departmentName" placeholder="Arts, Crafts & Sewing">
                    </div>
                    <div class="form-group">
                        <label for="coll-name">Department Alias <span class="tooltip" data-tooltip-content="#tooltip_content">
                            <i class="fa fa-info " ></i>
                            </span>
                        </label>
                        <input type="text" class="form-control"
                            id="coll-name" name="departmentAlias" placeholder="Like 'arts-crafts'">
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
