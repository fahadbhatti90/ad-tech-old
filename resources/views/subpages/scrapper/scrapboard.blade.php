@extends('layout.app')
@extends('inc.side_menu')
@extends('inc.nav_bar')
@extends('modal.logout')
@section('title',isset($pageTitle)?$pageTitle:'')
@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Begin Breadcrumb -->
        {{ Breadcrumbs::render('scraping_dashboard') }}
    <!-- End Breadcrumb -->
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{isset($pageHeading)?$pageHeading:''}}</h1>

        </div>
        <!-- Content Row -->
        <div class="row">
            <!-- Earnings (Monthly) Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Schedules
                                    {{-- (Daily) --}}
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    @if (isset($schedules))
                                        {{ $schedules }}
                                    @endif
                                </div>
                            </div>
                            <div class="col-auto">
                               
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Schedule LAST ADDED -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Schedule LAST ADDED
                                    {{-- (Instants) --}}
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    @if (isset($lastScheduleUploaded))
                                        {{ $lastScheduleUploaded }}
                                    @endif
                                </div>
                            </div>
                            <div class="col-auto">
                            
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Schedule LAST ADDED -->
               <!-- Earnings (Monthly) Card Example -->
               <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Shedules Stopped
                                    {{-- (Instants) --}}
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    @if (isset($schedulesStoped))
                                        {{ $schedulesStoped }}
                                    @endif
                                </div>
                            </div>
                            <div class="col-auto">
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Schedule running-->
              <!--Schedule running -->
              <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Shedules Running
                                    {{-- (Instants) --}}
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    @if (isset($schedulesRunning))
                                        {{ $schedulesRunning }}
                                    @endif
                                </div>
                            </div>
                            <div class="col-auto">
                               
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Total Collections -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary     shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Collections</div>
                                <div class="row no-gutters align-items-center">
                                    <div class="col-auto">
                                        <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                            @if (isset($totalCollection))
                                            {{ $totalCollection }}
                                        @endif
                                        </div>
                                    </div>
                                    <div class="col">
                                       
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                              
                            </div>
                        </div>
                    </div>
                </div>
            </div>
              <!-- Collection LAST ADDED -->
              <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Collection LAST ADDED
                                    {{-- (Instants) --}}
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    @if (isset($lastCollectionUploaded))
                                        {{ $lastCollectionUploaded }}
                                    @endif
                                </div>
                            </div>
                            <div class="col-auto">
                            
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ASIN LAST ADDED -->
            <!-- Last Collection Added Type -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary     shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Last Collection Added Type</div>
                                <div class="row no-gutters align-items-center">
                                    <div class="col-auto">
                                        <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                            @if (isset($lastCollectionAddedType))
                                            {{ $lastCollectionAddedType }}
                                        @endif
                                        </div>
                                    </div>
                                    <div class="col">
                                       
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                              
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Total Collections Daily -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary     shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Collections Daily</div>
                                <div class="row no-gutters align-items-center">
                                    <div class="col-auto">
                                        <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                            @if (isset($totalCollectionDaily))
                                            {{ $totalCollectionDaily }}
                                        @endif
                                        </div>
                                    </div>
                                    <div class="col">
                                       
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                              
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Total Collections Instant -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary     shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Collections Instant</div>
                                <div class="row no-gutters align-items-center">
                                    <div class="col-auto">
                                        <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                            @if (isset($totalCollectionInstant))
                                            {{ $totalCollectionInstant }}
                                        @endif
                                        </div>
                                    </div>
                                    <div class="col">
                                       
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                              
                            </div>
                        </div>
                    </div>
                </div>
            </div>
              <!-- ASIN LAST ADDED -->
              <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">ASIN LAST ADDED
                                    {{-- (Instants) --}}
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    @if (isset($lastAsinUploaded))
                                        {{ $lastAsinUploaded }}
                                    @endif
                                </div>
                            </div>
                            <div class="col-auto">
                            
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ASIN LAST ADDED -->
            <!-- Pending Requests Card Example -->
            {{-- <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending
                                    Jobs
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">18</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-comments fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}
        </div>
    </div>
    <!-- /.container-fluid -->
@endsection
