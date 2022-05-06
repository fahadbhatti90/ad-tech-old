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

    <script type="text/javascript" src="{{ asset('public/tooltipster/dist/js/tooltipster.bundle.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/es6-shim/0.35.3/es6-shim.min.js"></script>
    <script src="{{ asset('public/js/scraper_scripts/proxyUpload.js?'.time()) }}"></script>
@endpush

 <!-- Begin Page Content -->
 <div class="container-fluid">
        <!-- Begin Breadcrumb -->

        {{ Breadcrumbs::render('asin_upload') }}
        <!-- End Breadcrumb -->
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{isset($pageHeading)?$pageHeading:''}}</h1>
            <a class="btn btn-danger" href="{{ route("deleteAllProxies") }}"><i class="fa fa-trash"></i> Delete All Proxy</a>
        </div>
        @if(session()->has('message'))
            <div class="alert alert-success">
                {{ session()->get('message') }}
            </div>
        @endif
    <div class="row">
           
        <!-- Area Chart -->
        <div class="col-xl-12 col-lg-12">
            <div class="card shadow mb-4 proxyUploadCard">
                    @include('partials.formPreloader')
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Upload Proxy  <span class="tooltip" data-tooltip-content="#tooltip_content">
                            <i class="fa fa-info " ></i>
                            </span></h6>
                    <form action="{{ route('uploadProxy') }}" id="proxyUploadForm">
                            @csrf
                            <div class="row">
                            <div class="form-group col-lg-8 addProxyFormGroup">
                               
                            </div>
                            <div class="form-group">
                                <div class="custom-file">
                                    <input type="file" accept="text/txt"  class="custom-file-input" id="proxy" name="proxy" >
                                    <label class="custom-file-label" for="proxy">Choose File</label>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <button type="submit" class="btn btn-primary">Upload</button>

                            </div>
                        </div>
                        </form>
                    <div class="tooltip_templates">
                        <span id="tooltip_content">
                            Proxies in proxy file should be formated like 
                            <br/>107.158.32.204:80<strong>;</strong>codeht:c0d3ht,
                            <br/>196.247.18.133:80<strong>;</strong>codeht:c0d3ht,
                            <br/>196.196.47.113:80<strong>;</strong>codeht:c0d3ht,
                        </span>
                    </div>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="row">
                            <div class="col-xl-10 col-lg-10 offset-1">
                                    <div class="form-group col-lg-4 offset-8">
                                            <input type="text" class="form-control"
                                                id="coll-name" name="proxy_search_box" placeholder="search...">
                                        </div>
                                    @if(!empty($proxies))
                                        <table class="table mt-5 cronListTable proxyTable" base_url = {{ url('/') }} csrf = {{ csrf_token() }}>
                                            <thead>
                                            <tr> 
                                                <th scope="col">#</th>
                                                <th scope="col">IP</th>
                                                <th scope="col">Auth Token</th>
                                            </tr>
                                            </thead>
                                            <tbody class="cronList">
                                            @foreach($proxies as $proxy)
                                                <tr id="{{ $proxy->id }}">
                                                    <td><b>{{$proxy->id}}</b></td>
                                                    <td><b>{{$proxy->proxy_ip}}</b></td>
                                                    <td>{{ $proxy->proxy_auth }}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <p>No Cron List Found.</p>
                                    @endif
                            </div>
                    </div>
                   
                </div>
            </div>
        </div>
    </div>
 </div>
    <!-- Content Row -->

@endsection
