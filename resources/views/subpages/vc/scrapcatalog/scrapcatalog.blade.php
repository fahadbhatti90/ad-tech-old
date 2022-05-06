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
        <!-- Begin Breadcrumb -->
        {{ Breadcrumbs::render('scrap_catalog') }}

        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{isset($pageHeading)?$pageHeading:''}}</h1>
        </div>
        <!-- Content Row -->
        <div class="row">
            <div class="col-xl-12 col-lg-12">
                <div class="card shadow mb-4">
                    <!-- Card Header - Dropdown -->
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">{{isset($pageHeading)?$pageHeading:''}}</h6>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif
                        <form action="{{ route('scrap_catalog') }}" id="scrapCatalogFormId"
                              enctype="multipart/form-data" method="post">
                            @csrf

                            <button type = "submit" class = "btn btn-primary">Scrap Catalog Data</button>
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
    {{--<script src="{{asset('public/js/vc_scripts/script_catalog.js')}}"></script>--}}
    <script src="{{asset('public/js/vc_scripts/vccustom.js')}}"></script>
@endsection
