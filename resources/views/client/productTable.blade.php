@extends('layout.appclient')
@extends('inc.side_menu')
@extends('inc.nav_bar')
@extends('modal.logout')
@section('title', isset($pageTitle)?$pageTitle:'')
@section('content')

@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/tooltipster/dist/css/tooltipster.bundle.min.css')}}" />
    <link rel="stylesheet" href="{{ asset('public/vendor/select/dist/css/bootstrap-select.min.css') }}">
    <link href="{{asset('public/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('public/vendor/datatables/responsive.dataTables.min.css')}}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{ asset('public/css/scraping_custom_style.css?'.time())  }}">
    <link rel="stylesheet" href="{{ asset('public/css/client/productTable.css?'.time())  }}">
@endpush

@push('daterangepickercss')
    <link rel="stylesheet" href="{{ asset('public/vendor/daterangepicker/daterangepicker.css') }}">
@endpush

@push('daterangepickerjs')
<script src="{{ asset('public/vendor/daterangepicker/moment.min.js') }}"></script>
@endpush
@push('js')
    <script type="text/javascript" src="{{ asset('public/tooltipster/dist/js/tooltipster.bundle.min.js')}}"></script>
	<link href="{{asset('public/vendor/formvalidation/dist/css/formValidation.min.css')}}" rel="stylesheet">
    <script src="{{asset('public/vendor/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('public/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{asset('public/vendor/datatables/dataTables.responsive.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script src="{{ asset('public/js/multiAuth/client/productTable.js?'.time()) }}"></script>
@endpush

    <!-- Begin Page Content -->
    <div class="container-fluid">
        
            @include('partials.formPreloader')
        <!-- Begin Breadcrumb -->
        {{ Breadcrumbs::render('clientProductPreview') }}
        <!-- End Breadcrumb -->
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4 topActionsContainer">
            {{-- <button type="button" class="waves-effect waves-light btn addSchedule  sc-move-right" data-toggle="modal" data-target="#addProductPreviewModel">
                Create Product Notes
            </button> --}}
        </div>
        {{-- <div class="card mb-4 productTableCard">
            <div class="card-header py-3" style="display: flex;">
                <div class="row">
                    <div class="col-lg-8 ">        
                        <span>Row Labels</span>
                    </div>
                    <div class="col-lg-2">Sum of shipped_units</div>
                    <div class="col-lg-2">Sum of paid_units</div>
                </div>
            </div>
            <div class="card-body ">
                <ul class="collapsible rowTitle">
                    <li>
                        <div class="collapsible-header">
                            <div class="row">
                                <div class="col-lg-8 ">        
                                    <i class="material-icons">add_circle_outline</i>
                                    <i class="material-icons">remove_circle_outline</i>
                                    <span>dash</span>
                                </div>
                                <div class="col-lg-2">101561</div>
                                <div class="col-lg-2">76031</div>
                            </div>
                        </div>
                        <div class="collapsible-body">
                            <ul class="collapsible category">
                                <li>
                                <div class="collapsible-header">
                                    <div class="row">
                                        <div class="col-lg-8 pl-5 ">
                                            <i class="material-icons">add_circle_outline</i>
                                            <i class="material-icons">remove_circle_outline</i>
                                            Home
                                        </div>
                                        <div class="col-lg-2">87008</div>
                                        <div class="col-lg-2">65769</div>
                                    </div>
                                </div>
                                <div class="collapsible-body ">  
                                    <ul class="collapsible subcategory">
                                        <li>
                                            <div class="collapsible-header">
                                                <div class="row">
                                                    <div class="col-lg-8 pl-10  ">
                                                        <i class="material-icons">add_circle_outline</i>
                                                        <i class="material-icons">remove_circle_outline</i>
                                                        Frames
                                                    </div>
                                                    <div class="col-lg-2">29064</div>
                                                    <div class="col-lg-2">21679</div>
                                                </div>
                                            </div>
                                            <div class="collapsible-body ">
                                                <div class="row ">
                                                    <div class="col-lg-8 pl-15">MCS 11x14 Inch Asher Wood Frame with 8x10 inch mat opening,Gray Woodgrain (66968)</div>
                                                    <div class="col-lg-2">14739</div>
                                                    <div class="col-lg-2">10932</div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-8 pl-15">MCS 8x10 Inch Asher Wood Frame, Gray Woodgrain (45968)</div>
                                                    <div class="col-lg-2">14325</div>
                                                    <div class="col-lg-2">10747</div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                    <ul class="collapsible subcategory">
                                        <li>
                                            <div class="collapsible-header">
                                                <div class="row">
                                                    <div class="col-lg-8 pl-10  ">
                                                        <i class="material-icons">add_circle_outline</i>
                                                        <i class="material-icons">remove_circle_outline</i>
                                                        Stationary
                                                    </div>
                                                    <div class="col-lg-2">29064</div>
                                                    <div class="col-lg-2">21679</div>
                                                </div>
                                            </div>
                                            <div class="collapsible-body ">
                                                <div class="row ">
                                                    <div class="col-lg-8 pl-15">MCS MBI 13.5x12.5 Inch Beach Life Theme Scrapbook Album with 12x12 Inch Pages (860121)</div>
                                                    <div class="col-lg-2">14739</div>
                                                    <div class="col-lg-2">10932</div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-8 pl-15">MCS MBI 13.5x12.5 Inch Live, Laugh, Love Theme Scrapbook Album with 12x12 Inch Pages (860123)</div>
                                                    <div class="col-lg-2">14325</div>
                                                    <div class="col-lg-2">10747</div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-8 pl-15">MCS MBI 13.5x12.5 Inch Magical Moments Theme Scrapbook Album with 12x12 Inch Pages (860138)</div>
                                                    <div class="col-lg-2">14325</div>
                                                    <div class="col-lg-2">10747</div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-8 pl-15">MCS MBI 13.5x12.5 Inch Red Wine Glitter Scrapbook Album with 12x12 Inch Pages with Photo Opening (860134)</div>
                                                    <div class="col-lg-2">14325</div>
                                                    <div class="col-lg-2">10747</div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                                </li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </div>
        </div> --}}
        <div class="card mb-4">
            <div class="card-header py-3" style="display: flex;">
                <h6 class="m-0 font-weight-bold text-primary" style="flex-basis:70%">
                     Product Table
                </h6>
            </div>
            <div class="card-body schedulingCardBody">
                    <table id="asinCronTable" class="table table-striped table-bordered  cronListTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Sr. #</th>
                                    <th>ASIN</th>
                                    <th>Product Title</th>
                                    <th>Full Fillment Method</th>
                                    <th>Sum Of Shipped Units</th>
                                    {{-- <th class="text-center">Action</th> --}}
                                </tr>
                            </thead>
                            <tbody class="cronList">
                                @php
                                    $sr = 0;
                                @endphp
                                @isset($asins)
                                    @foreach($asins as $key => $asin)
                                        <tr id="{{ $asin->id }}">
                                            <td> {{ ++$sr }} </td>
                                            <td  class="text-capitalize">{{ $asin->asin }}</td>
                                            <td class="text-capitalize">
                                                <div  class="tooltip" title="{{str_replace('','_',$asin->title)}}">
                                                    {{ 
                                                    str_limit(
                                                    str_replace('','_',$asin->title)
                                                    ,50)
                                                }}
                                                </div>
                                            </td>
                                            <td class="text-capitalize">{{  ($asin->channel)}}</td>
                                            <td class="text-capitalize">
                                                    {{ $asin->sumOfShippedUnits }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @endisset
                            </tbody>
                            </tbody>
                           
                    </table>
            </div>
        </div>
    </div>
@endsection
