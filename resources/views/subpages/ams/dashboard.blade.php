@extends('layout.app')
@extends('inc.side_menu')
@extends('inc.nav_bar')
@extends('modal.logout')
@section('title', $pageTitle)
@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Begin Breadcrumb -->
{{--    {{ Breadcrumbs::render('ams_dashboard') }}--}}
    <!-- End Breadcrumb -->
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800 sr-only">{{isset($pageHeading)?$pageHeading:''}}</h1>
        </div>
        <!-- Content Row -->
        <div class="row">
            <!-- Profile -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Number of
                                    Profiles
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    Count: {{$allData['profiles']}}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Advertising Campaign Reports -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Advertising
                                    Campaign
                                    Reports
                                </div>
                                <div class="h6 mb-0 small font-weight-bold text-gray-800">
                                    @php
                                        $campaigns_reports_sp = '';
                                        if(!isset($allData['campaigns_reports_sp']->reportDate)){
                                            $campaigns_reports_sp = 'NA';
                                        }else{
                                            $campaigns_reports_sp = date_format(date_create($allData['campaigns_reports_sp']->reportDate),"d M Y");
                                        }
                                    @endphp
                                    Last Report Date: {{ $campaigns_reports_sp }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Ad Group Reports -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Ad Group Reports
                                </div>
                                <div class="h6 mb-0 small font-weight-bold text-gray-800">
                                    @php
                                        $adgroup_reports = '';
                                        if(!isset($allData['adgroup_reports']->reportDate)){
                                            $adgroup_reports = 'NA';
                                        }else{
                                            $adgroup_reports = date_format(date_create($allData['adgroup_reports']->reportDate),"d M Y");
                                        }
                                    @endphp
                                    Last Report Date: {{ $adgroup_reports }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Keyword Reports -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Keyword Reports
                                </div>
                                <div class="h6 mb-0 small font-weight-bold text-gray-800">
                                    @php
                                        $keyword_reports_sp = '';
                                        if(!isset($allData['keyword_reports_sp']->reportDate)){
                                            $keyword_reports_sp = 'NA';
                                        }else{
                                            $keyword_reports_sp = date_format(date_create($allData['keyword_reports_sp']->reportDate),"d M Y");
                                        }
                                    @endphp
                                    Last Report Date: {{ $keyword_reports_sp }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Product Ads Report -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Product Ads
                                    Report
                                </div>
                                <div class="h6 mb-0 small font-weight-bold text-gray-800">
                                    @php
                                        $productads_reports = '';
                                        if(!isset($allData['productads_reports']->reportDate)){
                                            $productads_reports = 'NA';
                                        }else{
                                            $productads_reports = date_format(date_create($allData['productads_reports']->reportDate),"d M Y");
                                        }
                                    @endphp
                                    Last Report Date: {{ $productads_reports }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ASINs Report -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">ASINs Report
                                </div>
                                <div class="h6 mb-0 small font-weight-bold text-gray-800">
                                    @php
                                        $ASIN_reports = '';
                                        if(!isset($allData['ASIN_reports']->reportDate)){
                                            $ASIN_reports = 'NA';
                                        }else{
                                            $ASIN_reports = date_format(date_create($allData['ASIN_reports']->reportDate),"d M Y");
                                        }
                                    @endphp
                                    Last Report Date: {{ $ASIN_reports }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Product Attribute Targeting Reports -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Product
                                    Attribute Targeting Reports
                                </div>
                                <div class="h6 mb-0 small font-weight-bold text-gray-800">
                                    @php
                                        $targets_reports = '';
                                        if(!isset($allData['targets_reports']->reportDate)){
                                            $targets_reports = 'NA';
                                        }else{
                                            $targets_reports = date_format(date_create($allData['targets_reports']->reportDate),"d M Y");
                                        }
                                    @endphp
                                    Last Report Date: {{ $targets_reports }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Sponsored Brand Reports -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Sponsored Brand
                                    Reports
                                </div>
                                <div class="h6 mb-0 small font-weight-bold text-gray-800">
                                    @php
                                        $keyword_reports_sb = '';
                                        if(!isset($allData['keyword_reports_sb']->reportDate)){
                                            $keyword_reports_sb = 'NA';
                                        }else{
                                            $keyword_reports_sb = date_format(date_create($allData['keyword_reports_sb']->reportDate),"d M Y");
                                        }
                                    @endphp
                                    Last Report Date: {{ $keyword_reports_sb }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
@endsection
