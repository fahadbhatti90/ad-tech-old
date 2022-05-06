@extends('layout.app')
@extends('inc.side_menu')
@extends('inc.nav_bar')
@extends('modal.logout')
@section('title', isset($pageTitle)?$pageTitle:'')
@section('content')

@push('css')

<link rel="stylesheet" type="text/css" href="{{ asset('public/tooltipster/dist/css/tooltipster.bundle.min.css')}}" />
    <link rel="stylesheet" href="{{asset('public/css/notification/notificationPreview.css?'.time())  }}">
   
@endpush
@push('js')

    <script type="text/javascript" src="{{ asset('public/tooltipster/dist/js/tooltipster.bundle.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/es6-shim/0.35.3/es6-shim.min.js"></script>
    <script src="{{ asset('public/js/scraper_scripts/proxyUpload.js?'.time()) }}"></script>
@endpush

 <!-- Begin Page Content -->
 <div class="container-fluid">
        <!-- Begin Breadcrumb -->

        {{ Breadcrumbs::render('notification',$notification["ID #:"]) }}
        <!-- End Breadcrumb -->
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{isset($pageHeading)?$pageHeading:''}}</h1>
        </div>
        @if(session()->has('message'))
            <div class="alert alert-success">
                {{ session()->get('message') }}
            </div>
        @endif
    <div class="row notiPreview">
        
            <div class="col-xl-12 col-lg-12 text-capitalize">
                    <h5><b>Notification:</b></h5>
                    <hr>
            </div>
        @foreach ($notification as $key => $noti)
            <div class="col-xl-4 col-lg-4">
                <b class="text-capitalize">{{ $key }}</b>
            </div>
            <div class="col-xl-8 col-lg-8 text-capitalize">
                {{ $noti }}
            </div>
            
        @endforeach
        <br><br><br>
            <div class="col-xl-12 col-lg-12 text-capitalize">
                <h5><b>Details:</b></h5>
               <p> {{ $message }} </p>
               
                <hr>
            </div>
            @foreach ($details as $key => $noti)

            <div class="col-xl-3 col-lg-3 text-capitalize">
                <b>
                    {{ $key }}
                </b>
            </div>
            <div class="col-xl-9 col-lg-9 text-capitalize">
                @if (str_contains($key,"Details Download Link"))
                    <a href="{{ route('notificationDownload',$notification['ID #:']) }}">{!! $noti !!}</a>
                @else
                    {!! $noti !!}
                @endif
            </div>  
                {{-- @if ($notiType == 2)
                    
                <div class="col-xl-12 col-lg-12">
                    
                    @if (str_contains($key,"item"))
                        <h5 class="text-capitalize"><b>{{ $key.":" }}</b></h5>
                        <br>
                        <div class="row">   
                                @php
                                    $table = json_decode($noti);
                                    $head =array();
                                    $body = array();
                                    foreach ($table as $key => $value) {
                                        array_push($head,$key);
                                        array_push($body,$value);
                                    }
                                    $keyindex = 0;
                                @endphp
                                @foreach ($table as $value)
                                    <div class="col-xl-2 col-lg-2 offset-1 text-capitalize">
                                        <b class="text-capitalize">{{ $head[$keyindex] }}</b>
                                    </div>
                                    <div class="col-xl-9 col-lg-9">
                                            @if ($keyindex == 0)
                                                @php
                                                    $tableInternal = json_decode($value);
                                                    
                                                    $headInternal =array();
                                                    $bodyInternal = array();
                                                    foreach ($tableInternal as $keyInternal => $valueInternal) {
                                                        array_push($headInternal,$keyInternal);
                                                        array_push($bodyInternal,$valueInternal);
                                                    }
                                                    $keyindexInternal = 0;
                                                @endphp
                                                <div class="row">
                                                    @foreach ($tableInternal as $v)
                                                        <div class="col-xl-3 col-lg-3 text-capitalize">
                                                            <b class="text-capitalize">{{ $headInternal[$keyindexInternal] }}</b>
                                                        </div>
                                                        <div class="col-xl-9 ">
                                                                {{ $v }}
                                                        </div> 
                                                        @php
                                                            $keyindexInternal++;
                                                        @endphp
                                                    @endforeach
                                                </div>
                                            @else
                                            @php
                                                $tableInternal = json_decode($value);
                                                $keyindexInternal = 0;
                                            @endphp
                                                <div class="row">
                                                    @if (is_array($tableInternal))
                                                        
                                                        @foreach($tableInternal as $v)
                                                            <div class="col-xl-12 col-lg-12 text-capitalize">
                                                                    <b class="text-capitalize">{{ ++$keyindexInternal }}.</b> {{ $v }}
                                                            </div>
                                                        @endforeach
                                                    @else
                                                          {{ $value }}  
                                                    @endif
                                                </div>
                                            @endif
                                            <br>
                                    </div>
                                    @php
                                        $keyindex++;
                                    @endphp
                                
                                @endforeach
                            
                        </div>
                    @else
                    <div class="row">
                        <div class="col-xl-3 col-lg-3 text-capitalize">
                            <h5 class="text-capitalize"><b>{{ $key }}</b></h5>
                        </div>
                        <div class="col-xl-9 col-lg-9 text-capitalize">
                            
                            {!! $noti !!}
                        </div>
                    </div>
                    @endif
                    <br><br>
                </div>
                @else
                    <div class="col-xl-3 col-lg-3 text-capitalize">
                        <b>
                            {{ $key }}
                        </b>
                    </div>
                    <div class="col-xl-9 col-lg-9 text-capitalize">
                        
                        {!! $noti !!}
                    </div>  
                @endif --}}
             
                
            @endforeach

    </div>
 </div>
    <!-- Content Row -->

@endsection
