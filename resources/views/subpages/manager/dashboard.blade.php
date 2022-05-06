@extends('layout.appManager')
@extends('inc.manager_side_menu')
@extends('inc.nav_bar')
@extends('modal.logout')
@section('title', isset($pageTitle)?$pageTitle:'Dashboard')

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

@section('content')
        <!-- /.container-fluid -->
        <!-- Begin Page Content -->
        <div class="container-fluid clientDasboard">
            <!-- Begin Breadcrumb -->
            {{ Breadcrumbs::render('admin_dashboard') }}
            <!-- End Breadcrumb -->
          
            <!-- Content Row -->
            <div class="row noSpace options">
                    <!-- Earnings (Monthly) Card Example -->
                <div class="col-xl-12 col-md-12  mb-3">
                    <div class="card left_card shadow h-100">
                        <div class="cardTitleCustom">Filters</div>
                        <div class="card-body">
                           @include('partials.graphsPreloader')
                           
                            <div class="row no-gutters align-items-center">
                                <div class="col col-lg-2 col-md-5 offset-md-1 col-sm-12 col-12 mr-3">
                                    <select class="mdb-select md-form attributesSelect">
                                        <option value="" disabled >Select Attributes</option>
                                        <option value="1" selected>Price</option>
                                        <option value="2">Sales</option>
                                        <option value="3">Sales Rank</option>
                                    </select>
                                </div>
                                
                                <div class="col col-lg-3 col-md-5 col-sm-12 col-12 mr-3">
                                    <select class="mdb-select md-form category">
                                        <option value="" disabled selected>Select Categories</option>
                                        @isset($categories)
                                            @foreach ($categories as $key=>$category)
                                                <option value="{{ $category->category_id }}">{{ $category->category_name }}</option>
                                            @endforeach
                                        @endisset
                                    </select>
                                </div>
                                
                                <div class="col col-lg-3 col-md-5 col-sm-12 col-12 mr-3 subCategoryParent">
                                    <select class="mdb-select md-form subCategory">
                                        <option value="" disabled >Select Sub Categories</option>
                                    </select>
                                </div>
                                
                                <div class="col col-lg-2 col-md-5 col-sm-12 col-12 mr-3 asinParent">
                                    <select class="mdb-select md-form asin">
                                        <option value="" disabled >Select ASIN</option>
                                    </select> 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-12 col-md-12 mb-4">
                    <div class="card right_card shadow h-100">
                        <div class="card-body">
                           @include('partials.graphsPreloader')
                            <div class="row no-gutters align-items-center">
                                <div class="col py-4 mr-4 ml-4"> 
                                        <div class="datesFilter row text-md-center text-sm-center text-xl-center" defaultMonth="{{ $defaultMonth }}" defaultYear="@isset($availableYears[0]) {{ $availableYears[0]->availableYear }} @else NA @endisset">
                                            <div class="col-lg-3 col-md-3 col-sm-12">
                                                {{-------------------------------------------- Daily --------------------------------------------}}
                                                <a href="#!" class="btn waves-effect waves-blue btn-small changeGraph customTooltip dailyDropdownTrigger" data-dtype = "1" data-df = "%j" title="1 to 30 days of month" data-target='dailyYearDropdown'>Day</a>

                                                <ul id='dailyYearDropdown' class='dropdown-content'> 
                                                    @isset($availableYears)
                                                        @foreach ($availableYears as $availableYear)
                                                            <li><a data-target='dailyMonthDropdown' href="#!" class="availableYear" year="{{ $availableYear->availableYear }}">{{ ($availableYear->availableYear) }}</a></li> 
                                                        @endforeach
                                                    @endisset
                                                </ul>
                                                <ul id='dailyMonthDropdown' class='dropdown-content'> 
                                                    <li><a href="#!" class="availableMonth" month="">Loading...
                                                    </a></li>
                                                </ul>
                                                {{-------------------------------------------- Daily --------------------------------------------}}
                                                {{-------------------------------------------- Weekly --------------------------------------------}}
                                                <a href="#!" class="btn waves-effect waves-white waves-blue btn-small changeGraph customTooltip weeklyDropdownTrigger" data-dtype = "2" data-df = "%a %d %b" title="0 to 52 weeks of year" data-target='weeklyYearDropdown'>week</a>

                                                <ul id='weeklyYearDropdown' class='dropdown-content'> 
                                                    @isset($availableYears)
                                                        @foreach ($availableYears as $availableYear)
                                                            <li><a data-target='weeklyMonthDropdown' href="#!" class="availableYear" year="{{ $availableYear->availableYear }}">{{ ($availableYear->availableYear) }}</a></li> 
                                                        @endforeach
                                                    @endisset
                                                </ul>
                                                {{-------------------------------------------- Weekly --------------------------------------------}}
                                                {{-------------------------------------------- Monthly --------------------------------------------}}
                                                <a href="#!" class="btn waves-effect waves-white waves-blue btn-small changeGraph customTooltip monthlyDropdownTrigger" data-dtype = "3" data-df = "%Y-%m-%d" title="1 to 12 Months of year" data-target='monthlyYearDropdown'>month</a>

                                                <ul id='monthlyYearDropdown' class='dropdown-content'> 
                                                    @isset($availableYears)
                                                        @foreach ($availableYears as $availableYear)
                                                            <li><a data-target='monthlyMonthDropdown' href="#!" class="availableYear" year="{{ $availableYear->availableYear }}">{{ ($availableYear->availableYear) }}</a></li> 
                                                        @endforeach
                                                    @endisset
                                                </ul>
                                                {{-------------------------------------------- Monthly --------------------------------------------}}
                                            </div>
                                            <div class="col-lg-7 col-md-7 col-sm-12">
                                                <div class="row salesCard">
                                                    <div class="col-lg-6">
                                                        <div class="preCard">
                                                            <b>Pre Period Sales Rate:</b> <span></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="postCard">
                                                        <b>Post Period Sales Rate:</b> <span></span>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-2 col-md-2 col-sm-12">
                                                <span class="selectedAsin badge"></span>
                                            </div>
                                            
                                        </div>
                                       
                                      
                                        <div id="chart">
                                            <div class="bg">
                                                <div class="title">
                                                    Please select a filter to continue
                                                </div>
                                            </div>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
                    
            <div class="row noSpace productPreviewTable">
                <!-- Earnings (Monthly) Card Example -->
                <div class="col-xl-12 col-md-12 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-0">
                                    <div class="table-responsive productViewTable">
                                        
                                        @include('partials.graphsPreloader')
                                        <table class="table table-bordered">
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <table class="table table-bordered">
                                                            <tbody>
                                                                <tr class="userAction0" style="transition: transform 0.5s ease-in-out;/* transform: rotateX(90deg) scale(0);*/">
                                                                   
                                                                    <td class="majorCol majorCol secondColumn" colspan="2" style="font-size: 20px;text-align: center;
                                                                    border: 0;background-color: #4e73df;color: #fff;">Dates</td>
                                                                    
                                                                    @isset($userActions["Action1"])
                                                                        @foreach ($userActions["Action1"] as $item)
                                                                            <td class="{{ $item ?'dynamicData active':'dynamicData' }}">
                                                                                <i class="fas fa-check"></i>
                                                                            </td>    
                                                                        @endforeach
                                                                    @else
                                                                        @for ($i = 1; $i <= 31; $i++)
                                                                            <td class="dynamicData" style="background-color: #4e73df;color:#fff">
                                                                                <i class="fas fa-check"></i>
                                                                            {{ $i }} 
                                                                            </td>
                                                                        @endfor
                                                                    @endisset
                                                                  
                                                                </tr>
                                                                <tr class="userAction1">
                                                                    <td scope="row" rowspan="6" class=" majorCol firstColumn">
                                                                        <div >User Actions
                                                                            </div> 
                                                                        </td>
                                                                    <td class="majorCol majorCol secondColumn" style="font-size: 12px;">Brand Price Change</td>
                                                                    
                                                                    @isset($userActions["Action1"])
                                                                        @foreach ($userActions["Action1"] as $item)
                                                                            <td class="{{ $item ?'dynamicData active':'dynamicData' }}">
                                                                                <i class="fas fa-check"></i>
                                                                            </td>    
                                                                        @endforeach
                                                                    @else
                                                                        @for ($i = 1; $i <= 31; $i++)
                                                                            <td class="dynamicData">
                                                                                <i class="fas fa-check"></i>
                                                                            </td>
                                                                        @endfor
                                                                    @endisset
                                                                  
                                                                </tr>
                                                                <tr class="userAction2">
                                                                    <td class="majorCol secondColumn">Cost Change</td>
                                                                    @isset($userActions["Action2"])
                                                                        @foreach ($userActions["Action2"] as $item)
                                                                            <td class="{{ $item ?'dynamicData active':'dynamicData' }}">
                                                                                <i class="fas fa-check"></i>
                                                                            </td>    
                                                                        @endforeach
                                                                    @else
                                                                        @for ($i = 1; $i <= 31; $i++)
                                                                            <td class="dynamicData">
                                                                                <i class="fas fa-check"></i>
                                                                            </td>
                                                                        @endfor
                                                                    @endisset
                                                                    
                                                                </tr>
                                                                <tr class="userAction3">
                                                                    <td class="majorCol secondColumn">Promotion</td>
                                                                    
                                                                        @isset($userActions["Action3"])
                                                                            @foreach ($userActions["Action3"] as $item)
                                                                                <td class="{{ $item ?'dynamicData active':'dynamicData' }}">
                                                                                    <i class="fas fa-check"></i>
                                                                                </td>    
                                                                            @endforeach
                                                                        @else
                                                                            @for ($i = 1; $i <= 31; $i++)
                                                                                <td class="dynamicData">
                                                                                    <i class="fas fa-check"></i>
                                                                                </td>
                                                                            @endfor
                                                                        @endisset
                                                                    
                                                                </tr>
                                                                <tr class="userAction4">
                                                                    <td class="majorCol secondColumn"  style="font-size: 12px;">Content Change</td>
                                                                    @isset($userActions["Action4"])
                                                                        @foreach ($userActions["Action4"] as $item)
                                                                            <td class="{{ $item ?'dynamicData active':'dynamicData' }}">
                                                                                <i class="fas fa-check"></i>
                                                                            </td>    
                                                                        @endforeach
                                                                    @else
                                                                        @for ($i = 1; $i <= 31; $i++)
                                                                            <td class="dynamicData">
                                                                                <i class="fas fa-check"></i>
                                                                            </td>
                                                                        @endfor
                                                                    @endisset
                                                                </tr>
                                                                <tr class="userAction5">
                                                                    <td class="majorCol secondColumn">Ad Changes</td>
                                                                    @isset($userActions["Action5"])
                                                                        @foreach ($userActions["Action5"] as $item)
                                                                            <td class="{{ $item ?'dynamicData active':'dynamicData' }}">
                                                                                <i class="fas fa-check"></i>
                                                                            </td>    
                                                                        @endforeach
                                                                    @else
                                                                        @for ($i = 1; $i <= 31; $i++)
                                                                            <td class="dynamicData">
                                                                                <i class="fas fa-check"></i>
                                                                            </td >
                                                                        @endfor
                                                                    @endisset
                                                                </tr>
                                                                <tr class="userAction6">
                                                                    <td class="majorCol secondColumn">New Platform</td>
                                                                    @isset($userActions["Action6"])
                                                                        @foreach ($userActions["Action6"] as $item)
                                                                            <td class="{{ $item ?'dynamicData active':'dynamicData' }}">
                                                                                <i class="fas fa-check"></i>
                                                                            </td>    
                                                                        @endforeach
                                                                    @else
                                                                        @for ($i = 1; $i <= 31; $i++)
                                                                            <td class="dynamicData">
                                                                                <i class="fas fa-check"></i>
                                                                            </td>
                                                                        @endfor
                                                                    @endisset
                                                                </tr>
                                                            </tbody>
                                                        </table>    
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <table class="table table-bordered">
                                                            <tbody>
                                                                <tr class="event1">
                                                                    <td scope="row" rowspan="7" class="majorCol firstColumn">
                                                                        <div>Events</div> 
                                                                        </td>
                                                                    <td class="majorCol secondColumn">OOS</td>
                                                                    @isset($events["event1"])
                                                                        @foreach ($events["event1"] as $item)
                                                                            <td class="{{ $item ?'dynamicData active':'dynamicData' }}">
                                                                                <i class="fas fa-check"></i>
                                                                            </td>    
                                                                        @endforeach
                                                                    @else
                                                                        @for ($i = 1; $i <= 31; $i++)
                                                                            <td class="dynamicData">
                                                                                <i class="fas fa-check"></i>
                                                                            </td>
                                                                        @endfor
                                                                    @endisset
                                                                </tr>
                                                                <tr class="event2">
                                                                    <td class="majorCol secondColumn">Seller Change</td>
                                                                    @isset($events["event2"])
                                                                        @foreach ($events["event2"] as $item)
                                                                            <td class="{{ $item ?'dynamicData active':'dynamicData' }}">
                                                                                <i class="fas fa-check"></i>
                                                                            </td>    
                                                                        @endforeach
                                                                    @else
                                                                        @for ($i = 1; $i <= 31; $i++)
                                                                            <td class="dynamicData">
                                                                                <i class="fas fa-check"></i>
                                                                            </td>
                                                                        @endfor
                                                                    @endisset
                                                                </tr>
                                                                <tr class="event3">
                                                                    <td class="majorCol secondColumn">CRAP</td>
                                                                    @isset($events["event3"])
                                                                        @foreach ($events["event3"] as $item)
                                                                            <td class="{{ $item ?'dynamicData active':'dynamicData' }}">
                                                                                <i class="fas fa-check"></i>
                                                                            </td>    
                                                                        @endforeach
                                                                    @else
                                                                        @for ($i = 1; $i <= 31; $i++)
                                                                            <td class="dynamicData">
                                                                                <i class="fas fa-check"></i>
                                                                            </td>
                                                                        @endfor
                                                                    @endisset
                                                                </tr>
                                                                <tr class="event4">
                                                                    <td class="majorCol secondColumn">Andon Cord</td>
                                                                    @isset($events["event4"])
                                                                        @foreach ($events["event4"] as $item)
                                                                            <td class="{{ $item ?'dynamicData active':'dynamicData' }}">
                                                                                <i class="fas fa-check"></i>
                                                                            </td>    
                                                                        @endforeach
                                                                    @else
                                                                        @for ($i = 1; $i <= 31; $i++)
                                                                            <td class="dynamicData">
                                                                                <i class="fas fa-check"></i>
                                                                            </td>
                                                                        @endfor
                                                                    @endisset
                                                                </tr>
                                                                <tr class="event5">
                                                                    <td class="majorCol secondColumn">Compliance Issue</td>
                                                                    @isset($events["event5"])
                                                                        @foreach ($events["event5"] as $item)
                                                                            <td class="{{ $item ?'dynamicData active':'dynamicData' }}">
                                                                                <i class="fas fa-check"></i>
                                                                            </td>    
                                                                        @endforeach
                                                                    @else
                                                                        @for ($i = 1; $i <= 31; $i++)
                                                                            <td class="dynamicData">
                                                                                <i class="fas fa-check"></i>
                                                                            </td>
                                                                        @endfor
                                                                    @endisset
                                                                </tr>
                                                                <tr class="event6">
                                                                    <td class="majorCol secondColumn">Drop From Site</td>
                                                                    @isset($events["event6"])
                                                                        @foreach ($events["event6"] as $item)
                                                                            <td class="{{ $item ?'dynamicData active':'dynamicData' }}">
                                                                                <i class="fas fa-check"></i>
                                                                            </td>    
                                                                        @endforeach
                                                                    @else
                                                                        @for ($i = 1; $i <= 31; $i++)
                                                                            <td class="dynamicData">
                                                                                <i class="fas fa-check"></i>
                                                                            </td>
                                                                        @endfor
                                                                    @endisset
                                                                </tr>
                                                                <tr class="event7">
                                                                    <td class="majorCol secondColumn">AMZ Price Change</td>
                                                                    @isset($events["event7"])
                                                                        @foreach ($events["event7"] as $item)
                                                                            <td class="{{ $item ?'dynamicData active':'dynamicData' }}">
                                                                                <i class="fas fa-check"></i>
                                                                            </td>    
                                                                        @endforeach
                                                                    @else
                                                                        @for ($i = 1; $i <= 31; $i++)
                                                                            <td class="dynamicData">
                                                                                <i class="fas fa-check"></i>
                                                                            </td>
                                                                        @endfor
                                                                    @endisset
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                               
                                              </tbody>
                                        </table>
                                      </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
            <div class="row productTable">
                <div class="col-xl-12 col-md-12 mb-4">
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
                                                                {{ number_format($asin->sumOfShippedUnits, 2) }}
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
            </div>
        </div>
        <!-- /.container-fluid -->
        <div class="tooltip_templates">
            <span id="tooltip_content">
               
            </span>
        </div>
@endsection
@push('js')
    <script type="text/javascript" src="{{ asset('public/tooltipster/dist/js/tooltipster.bundle.min.js')}}"></script>
    <script src="{{asset('public/vendor/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('public/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{asset('public/vendor/datatables/dataTables.responsive.min.js')}}"></script>
    <script src="https://d3js.org/d3.v4.min.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script src="{{ asset('public/js/multiAuth/client/productTable.js?'.time()) }}"></script>
    <script type="text/javascript" src="{{asset('public/js/charts/clientLineChart.js?'.time())}}"></script>
@endpush