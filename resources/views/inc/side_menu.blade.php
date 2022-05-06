@section('side_menu')
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
        <!-- Sidebar - Brand -->
        @if((session()->get("activeRole")==1))
            <a class="sidebar-brand d-flex align-items-center justify-content-center"
               href="{{  (Request::segment(1) == 'client')?url('client/dashboard'):url('/dashboard')}}">
                <div class="sidebar-brand-text mx-3"><img src="{{ asset("public/images/logo.png") }}" class="logoImage"
                                                          alt="logo.png"></div>
            </a>
            <hr class="sidebar-divider my-0">
            <!-- Nav Item - Dashboard -->
            <!-- Side Menu Starts -->
            <!-- Divider -->
            <li class="nav-item {{(Request::segment(1) == '')?'active':''}}">
                <a class="nav-link" href="{{ url('/dashboard') }}">
                    <span>Dashboard</span></a>
            </li>
        @endif
        @if((session()->get("activeRole")==2))
            <a class="sidebar-brand d-flex align-items-center justify-content-center"
               href="{{  (Request::segment(1) == 'client')?url('client/dashboard'):url('admin/dashboard')}}">
                <div class="sidebar-brand-text mx-3"><img src="{{ asset("public/images/logo.png") }}" class="logoImage"
                                                          alt="logo.png"></div>
            </a>
            <!-- Divider -->
            <hr class="sidebar-divider my-0">
            <!-- Nav Item - Dashboard -->

            <!-- Divider -->
            <li class="nav-item {{(Request::segment(1) == '')?'active':''}}">
                <a class="nav-link" href="{{ url('admin/dashboard') }}">
                    <span>Dashboard</span></a>
            </li>
        @endif
        @if((session()->get("activeRole")==3))
            <a class="sidebar-brand d-flex align-items-center justify-content-center"
               href="{{ route('client.dashboard') }}">
                {{-- <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-laugh-wink"></i>
                </div> --}}
                <div class="sidebar-brand-text mx-3"><img src="{{ asset("public/images/logo.png") }}" class="logoImage"
                                                          alt="logo.png"></div>
            </a>
            <!-- Divider -->
            <hr class="sidebar-divider my-0">
            <!-- Nav Item - Dashboard -->
            
            @php
                $isProductDropDownOpen = $isAdvertisingDropDownOpen = false;
                if((Request::segment(2) == 'dashboard' && Request::segment(1) == 'client')){
                    $isProductDropDownOpen = $isAdvertisingDropDownOpen = false;
                }
                elseif ((Request::segment(2) == 'events' && Request::segment(1) == 'client')) {
                    $isProductDropDownOpen = true;
                    $isAdvertisingDropDownOpen = false;
                }
                else{
                    $isProductDropDownOpen = false;
                    $isAdvertisingDropDownOpen = true;
                }
            @endphp

            <li class="nav-item {{  (Request::segment(2) == 'dashboard' && Request::segment(1) == 'client')?'active':''}}">
                <a class="nav-link" href="{{ route('client.dashboard') }}">
                    <img class="mr-1" style="height: 15px" src="{{asset('public/svg/dashboard.svg')}}">
                    <span>Home</span></a>
            </li>

            <li class="nav-item {{($isProductDropDownOpen)?'active':''}}">
                <a class="nav-link  {{($isProductDropDownOpen)?'':'collapsed'}}" href="#"
                   data-toggle="collapse" data-target="#productCollapse" aria-expanded="true"
                   aria-controls="productCollapse">
                    <i class="fa fa-list-ul" style="color: white" aria-hidden="true"></i>
                    <span class="text-capitalize">Products</span>
                </a>
                <div id="productCollapse" class="collapse {{($isProductDropDownOpen)?'show':''}}"
                     aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item {{(Request::segment(2) == 'events' && Request::segment(1) == 'client')?'active':''}}"
                           href="{{  route('clients.notes') }}">
                            <img class="mr-1" style="height: 18px" src="{{asset('public/svg/Product-Notes.svg')}}">
                            Events
                        </a>
                    </div>
                </div>
            </li>
            <li class="nav-item {{($isAdvertisingDropDownOpen)? $isAdvertisingDropDownOpen.' active':''}}">
                <a class="nav-link  {{($isAdvertisingDropDownOpen)?'':'collapsed'}}" href="#"
                   data-toggle="collapse" data-target="#advertisingCollapse" aria-expanded="true"
                   aria-controls="advertisingCollapse">
                    <i class="fa fa-ad" aria-hidden="true"></i>
                    <span class="text-capitalize">Advertising</span>
                </a>
                <div id="advertisingCollapse" class="collapse {{($isAdvertisingDropDownOpen)?'show':''}}"
                     aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item {{(Request::segment(2) == 'visuals' && Request::segment(1) == 'manager')?'active':''}}"
                           href="{{ route('vissuals.view') }}">
                           <img class="mr-1 apblack" style="height: 18px" src="{{asset('public/svg/chart-Visuals-B.svg')}}">
                           <img class="mr-1 apblue" style="height: 18px" src="{{asset('public/svg/chart-Visuals.svg')}}">
                           
                            Advertising Visuals
                        </a>
                        <!-- route Asin performance -->
                        <a class="collapse-item {{(Request::segment(2) == 'asinvisuals' && Request::segment(1) == 'manager')?'active':''}}"
                           href="{{ route('vissuals.asinPerformanceVisuals') }}">
                           <img class="mr-1 apblack" style="height: 18px" src="{{asset('public/svg/AP-Black.svg')}}">
                           <img class="mr-1 apblue" style="height: 18px" src="{{asset('public/svg/AP-Blue.svg')}}">
                            ASIN Performance
                        </a>
                        

                        <a class="collapse-item {{(Request::segment(2) == 'campaign' && Request::segment(1) == 'client' && Request::segment(3) == 'tags')?'active':''}}"
                           href="{{ route('campaign.tags') }}">
                           <img class="mr-1 apblack" style="height: 18px" src="{{asset('public/svg/Campaign-Taging.svg')}}">
                           <img class="mr-1 apblue" style="height: 18px" src="{{asset('public/svg/Campaign-Taging-B.svg')}}">
                             Campaign Tagging
                        </a>
                        <a class="collapse-item {{(Request::segment(2) == 'schedule' && Request::segment(1) == 'dayParting')?'active':''}}"
                           href="{{ route('dayPartingSchedule') }}">
                           <img class="mr-1 apblack" style="height: 18px" src="{{asset('public/svg/Day-Parting.svg')}}">
                           <img class="mr-1 apblue" style="height: 18px" src="{{asset('public/svg/Day-Parting-B.svg')}}">
                            Day Parting Schedule
                        </a>
                        <a class="collapse-item {{(Request::segment(2) == 'history' && Request::segment(1) == 'dayParting')?'active':''}}"
                           href="{{ route('dayPartingHistory') }}">
                           <img class="mr-1 apblack" style="height: 18px" src="{{asset('public/svg/Day-Parting-History.svg')}}">
                           <img class="mr-1 apblue" style="height: 18px" src="{{asset('public/svg/Day-Parting-History-B.svg')}}">
                            Day Parting History
                        </a>

                        <a class="collapse-item {{(Request::segment(1) == 'advertisingReports') ?'active':''}}"
                           href="{{ route('advertisingReportsEmailView') }}">
                           <img class="mr-1 apblack" style="height: 18px" src="{{asset('public/svg/Email-Report.svg')}}">
                           <img class="mr-1 apblue" style="height: 18px" src="{{asset('public/svg/Email-Report-B.svg')}}">
                           Email Ad Reports
                        </a>

                        <a class="collapse-item {{(Request::segment(1) == 'bidding-rule')?'active':''}}"
                           href="{{ route('bidding-rule') }}">
                           <img class="mr-1 apblack" style="height: 18px" src="{{asset('public/svg/Bidding-Rule.svg')}}">
                           <img class="mr-1 apblue" style="height: 18px" src="{{asset('public/svg/Bidding-Rule-B.svg')}}">
                            Bidding Rules
                        </a>
                    </div>
                </div>
            </li>
        @endif
    <!-- Divider -->
        <hr class="sidebar-divider">
        @if((session()->get("activeRole")==1))
            <li class="nav-item {{(Request::segment(2) == 'admins' && Request::segment(1) == 'ht')?'active':''}}">
                <a class="nav-link  {{(Request::segment(2) == 'admins' && Request::segment(1) == 'ht')?'':'collapsed'}}"
                   href="#" data-toggle="collapse" data-target="#collapseZeroOne" aria-expanded="true"
                   aria-controls="collapseZeroOne">
                    <span class="text-capitalize">Agency</span>
                </a>
                <div id="collapseZeroOne"
                     class="collapse {{(Request::segment(2) == 'admins' && Request::segment(1) == 'ht')?'show':''}}"
                     aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item  {{(Request::segment(2) == 'admins' && Request::segment(1) == 'ht')?'active':''}}"
                           href="{{ route('super.admin.addAdmins') }}">Manage Agencies</a>

                    </div>
                </div>
            </li>
        @endif
        @if((session()->get("activeRole")==2))
            <li class="nav-item {{(Request::segment(2) == 'managers' && Request::segment(1) == 'ht')?'active':''}}">
                <a class="nav-link  {{(Request::segment(2) == 'managers' && Request::segment(1) == 'ht')?'active':''}}"
                   href="{{ route('admin.managers') }}">
                    <span class="text-capitalize">Users</span>
                </a>
            </li>
            <li class="nav-item {{((Request::segment(2) == 'brands' || Request::segment(2) == 'associateBrands') && Request::segment(1) == 'ht')?'active':''}}">
                <a class="nav-link {{((Request::segment(2) == 'brands' || Request::segment(2) == 'associateBrands') && Request::segment(1) == 'ht')?'':'collapsed'}}"
                   href="#" data-toggle="collapse" data-target="#collapseZero" aria-expanded="true"
                   aria-controls="collapseZero">
                    <span class="text-capitalize">Brands</span>
                </a>
                <div id="collapseZero"
                     class="collapse {{((Request::segment(2) == 'brands' || Request::segment(2) == 'associateBrands' || Request::segment(2) == 'account') && (Request::segment(1) == 'ht' || Request::segment(1) == 'accounts'))?'show':''}}"
                     aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item {{(Request::segment(2) == 'brands' && Request::segment(1) == 'ht')?'active':''}}"
                           href="{{ route('addBrands') }}">Manage Brands</a>
                        <a class="collapse-item {{(Request::segment(2) == 'account' && Request::segment(1) == 'accounts')?'active':''}}"
                           href="{{ route('account') }}">Accounts</a>

                    </div>
                </div>

            </li>
            {{--<li class="nav-item {{(Request::segment(2) == 'account' && Request::segment(1) == 'accounts')?'active':''}}">
                <a class="nav-link  {{(Request::segment(2) == 'accounts' && Request::segment(1) == 'account')?'active':''}}"
                   href="{{ route('account') }}">
                    <span class="text-capitalize">Accounts</span>
                </a>
            </li>--}}
            <li class="nav-item {{(Request::segment(2) == 'labelOverride' && Request::segment(1) == 'admin')?'active':''}}">
                <a class="nav-link  {{(Request::segment(2) == 'labelOverride' && Request::segment(1) == 'admin')?'active':''}}"
                   href="{{ route('label.override.attributes') }}">
                    <span class="text-capitalize">Label Override</span>
                </a>
            </li>
        @endif
    <!-- Nav Item - Pages Collapse Menu -->
        <!-- Clients -->
        <!-- Amazon Marketing Services -->
        @if((session()->get("activeRole")==1 || session()->get("activeRole")==2))
            <li class="nav-item {{(Request::segment(1) == 'ams')?'active':''}}">
                <a class="nav-link  {{(Request::segment(1) == 'ams')?'':'collapsed'}}" href="#" data-toggle="collapse"
                   data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    <span class="text-uppercase">ams</span>
                </a>
                <div id="collapseOne" class="collapse {{(Request::segment(1) == 'ams')?'show':''}}"
                     aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header sr-only"><span class="text-uppercase">ams</span> Components:</h6>
                        {{-- <a class="collapse-item {{(Request::segment(2) == 'dashboard'&& Request::segment(1) == 'ams')?'active':''}}"--}}
                        {{-- href="{{ url('/ams/dashboard') }}">Dashboard</a>--}}
                        @if((session()->get("activeRole")==2))
                            <a class="collapse-item {{(Request::segment(2) == 'apiconfig' && Request::segment(1) == 'ams')?'active':''}}"
                               href="{{ url('/ams/apiconfig') }}">API Config</a>
                        @endif
                        @if((session()->get("activeRole")==1))
                            <a class="collapse-item {{(Request::segment(2) == 'scheduling' && Request::segment(1) == 'ams')?'active':''}}"
                               href="{{ url('/ams/scheduling') }}">Scheduling</a>
                        @endif
                        @if((session()->get("activeRole")==2))
                            <a class="collapse-item {{ (Request::segment(2)=='export-csv'&& Request::segment(1)=='ams')?'active':'' }}"
                               href="{{ url('/ams/export-csv') }}">Export CSV</a>
                        @endif
                    </div>
                </div>
            </li>
        @endif
        @if((session()->get("activeRole")==2))
        <!-- Vendor Central-->
            <li class="nav-item {{(Request::segment(1) == 'vc')?'active':''}}">
                <a class="nav-link {{(Request::segment(1) == 'vc')?'':'collapsed'}}" href="#" data-toggle="collapse"
                   data-target="#collapseTwo"
                   aria-expanded="true" aria-controls="collapseTwo">
                    <span class="text-capitalize">vendor central</span>
                </a>
                <div id="collapseTwo" class="collapse {{(Request::segment(1) == 'vc')?'show':''}}"
                     aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        {{--<h6 class="collapse-header">VC components:</h6>--}}
                        {{-- <a class="collapse-item {{(Request::segment(2) == 'dashboard' && Request::segment(1) == 'vc')?'active':''}}"
                            href="{{ url('/vc/dashboard') }}">Dashboard</a>--}}
                        <a class="collapse-item {{(Request::segment(2) == 'dailysales' && Request::segment(1) == 'vc')?'active':''}}"
                           href="{{ url('/vc/dailysales') }}">Daily Sales</a>
                        <a class="collapse-item {{(Request::segment(2) == 'purchaseorder' && Request::segment(1) == 'vc')?'active':''}}"
                           href="{{ url('/vc/purchaseorder') }}">Purchase Order</a>
                        <a class="collapse-item {{(Request::segment(2) == 'dailyinventory' && Request::segment(1) == 'vc')?'active':''}}"
                           href="{{ url('/vc/dailyinventory') }}">Daily Inventory</a>
                        <a class="collapse-item {{(Request::segment(2) == 'traffic' && Request::segment(1) == 'vc')?'active':''}}"
                           href="{{ url('/vc/traffic') }}">Traffic</a>
                        <a class="collapse-item {{(Request::segment(2) == 'forecast' && Request::segment(1) == 'vc')?'active':''}}"
                           href="{{ url('/vc/forecast') }}">Forecast</a>
                        <a class="collapse-item {{(Request::segment(2) == 'catalog' && Request::segment(1) == 'vc')?'active':''}}"
                           href="{{ url('/vc/catalog') }}">Product Catalog</a>
                        <a class="collapse-item {{(Request::segment(2) == 'vendors' && Request::segment(1) == 'vc')?'active':''}}"
                           href="{{ url('/vc/vendors') }}">Vendor</a>
                        <a class="collapse-item {{(Request::segment(2) == 'delete' && Request::segment(1) == 'vc')?'active':''}}"
                           href="{{ route('verifyFrom') }}">Verify Record</a>
                        <a class="collapse-item {{ (Request::segment(2)=='history'&& Request::segment(1)=='vc')
                    ?'active':'' }}" href="{{ url('/vc/history') }}">Export CSV</a>
                        {{--<a class="collapse-item {{(Request::segment(2) == 'scrapcatalog' && Request::segment(1) == 'vc')
                        ?'active':''}}"
                           href="{{ url('/vc/scrapcatalog') }}">Scrap Catalog</a>--}}
                    </div>
                </div>
            </li>
        @endif
        @if((session()->get("activeRole")==1 || session()->get("activeRole")==2))
            <li class="nav-item {{(Request::segment(1) == 'mws')?'active':''}}">
                <a class="nav-link  {{(Request::segment(1) == 'mws')?'':'collapsed'}}" href="#" data-toggle="collapse"
                   data-target="#collapseThree" aria-expanded="true" aria-controls="collapseThree">
                    <span>Seller Central</span>
                </a>
                <div id="collapseThree" class="collapse {{(Request::segment(1) == 'mws')?'show':''}}"
                     aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        @if((session()->get("activeRole")==2))
                            <a class="collapse-item {{(Request::segment(2) == 'apiconfig' && Request::segment(1) == 'mws')?'active':''}}"
                               href="{{ url('/mws/apiconfig') }}">API Config</a>
                        @endif
                        @if((session()->get("activeRole")==1))
                            <a class="collapse-item {{(Request::segment(2) == 'scheduling' && Request::segment(1) == 'mws')?'active':''}}"
                               href="{{ url('/mws/scheduling') }}">Scheduling</a>
                        @endif
                        @if((session()->get("activeRole")==2))
                            <a class="collapse-item {{(Request::segment(2) == 'history' && Request::segment(1) == 'mws')?'active':''}}"
                               href="{{ url('/mws/history') }}">Export CSV</a>
                        @endif
                    </div>
                </div>
            </li>
        @endif
        @if((session()->get("activeRole")==1))
            <li class="nav-item {{(Request::segment(1) == 'scrap')?'show':''}}">
                <a class="nav-link collapsed  " href="#" data-toggle="collapse" data-target="#collapseFour"
                   aria-expanded="true" aria-controls="collapseFour">
                    <span class="text-capitalize">ASIN's </span>
                </a>
                <div id="collapseFour" class="collapse {{(Request::segment(1) == 'asin')?'show':''}}"
                     aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        {{-- <a class="collapse-item {{ (Request::segment(2)=='scrapboard'&&Request::segment(1)=='scrap')?'active':'' }}" href="{{ route('Scrapboard') }}">Dashboard</a> --}}
                        <a class="collapse-item {{ (Request::segment(2)=='uploadASIN'&&Request::segment(1)=='asin')?'active':'' }}"
                           href="{{ route('uploadASIN') }}">Add ASIN's Collection</a>
                        <a class="collapse-item {{ (Request::segment(2)=='scheduling'&&Request::segment(1)=='asin')?'active':'' }}"
                           href="{{ route('ScraperScheduling') }}">Schedule Scraping</a>
                        <a class="collapse-item {{ (Request::segment(2)=='history'&&Request::segment(1)=='asin')?'active':'' }}"
                           href="{{ route('showHistoryForm') }}">Export CSV</a>
                    </div>
                </div>
            </li>
            <!-- Search Rank -->
            <li class="nav-item {{(Request::segment(1) == 'sr')?'show':''}}">
                <a class="nav-link collapsed " href="#" data-toggle="collapse" data-target="#collapseFive"
                   aria-expanded="true" aria-controls="collapseFive">
                    <span class="text-capitalize">Search Rank</span>
                </a>
                <div id="collapseFive" class="collapse {{(Request::segment(1) == 'sr')?'show':''}}"
                     aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        {{-- <a class="collapse-item {{ (Request::segment(2)=='scrapboard'&&Request::segment(1)=='scrap')?'active':'' }}" href="{{ route('Scrapboard') }}">Dashboard</a> --}}
                        <a class="collapse-item {{ (Request::segment(2)=='crawlerForm'&&Request::segment(1)=='sr')?'active':'' }}"
                           href="{{ route('showSearchRankCrawlerForm') }}">Schedule Scraping</a>
                        <a class="collapse-item {{ (Request::segment(2)=='history'&&Request::segment(1)=='sr')?'active':'' }}"
                           href="{{ route('showSearchRankHistoryForm') }}">Export CSV</a>
                    </div>
                </div>
            </li>
            <!-- Divider -->
            <!-- Buy Box Scraper -->
            <li class="nav-item {{(Request::segment(1) == 'buybox')?'active':''}}">
                <a class="nav-link {{(Request::segment(1) == 'buybox')?'':'collapsed'}}" href="#" data-toggle="collapse"
                   data-target="#collapseSix"
                   aria-expanded="true" aria-controls="collapseSix">
                    <span class="text-capitalize">buy box scraper</span>
                </a>
                <div id="collapseSix" class="collapse {{(Request::segment(1) == 'buybox')?'show':''}}"
                     aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        {{-- <a class="collapse-item {{(Request::segment(2) == 'dashboard'&& Request::segment(1) == 'buybox')?'active':''}}" href="{{ url('/buybox/dashboard') }}">Dashboard</a>--}}
                        <a class="collapse-item {{(Request::segment(2) == 'scheduling'&& Request::segment(1) == 'buybox')?'active':''}}"
                           href="{{ url('/buybox/scheduling') }}">Scheduling</a>
                    </div>
                </div>
            </li>
        @endif
        @if((session()->get("activeRole")==3))
            {{-- <li class="nav-item {{(Request::segment(2) == 'visuals' && Request::segment(1) == 'manager')?'active':''}}">
                <a class="nav-link  {{(Request::segment(2) == 'visuals' && Request::segment(1) == 'manager')?'active':''}}"
                href="{{ route('vissuals.view')  }}">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span class="text-capitalize">Advertising Visuals</span>
                </a>
            </li> --}}

            {{-- <li class="nav-item {{(Request::segment(2) == 'notes' && Request::segment(1) == 'client')?'active':''}}">
                <a class="nav-link  {{(Request::segment(2) == 'notes' && Request::segment(1) == 'client')?'active':''}}"
                   href="{{ route('clients.notes') }}">
                    <i class="fas fa-sticky-note"></i>
                    <span class="text-capitalize">Product Notes</span>
                </a>
            </li> --}}
        <!-- Day Parting -->
            {{-- <li class="nav-item {{(Request::segment(1) == 'dayParting')?'active':''}}">
                <a class="nav-link  {{(Request::segment(1) == 'dayParting')?'':'collapsed'}}" href="#"
                   data-toggle="collapse" data-target="#dayPartingcollapseOne" aria-expanded="true"
                   aria-controls="dayPartingcollapseOne">
                    <img style="height: 18px" src="{{asset('public/images/day-parting.png')}}">
                    <span class="text-capitalize">Day Parting</span>
                </a>
                <div id="dayPartingcollapseOne" class="collapse {{(Request::segment(1) == 'dayParting')?'show':''}}"
                     aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item {{(Request::segment(2) == 'schedule' && Request::segment(1) == 'dayParting')?'active':''}}"
                           href="{{ route('dayPartingSchedule') }}">Schedule</a>
                        <a class="collapse-item {{(Request::segment(2) == 'history' && Request::segment(1) == 'dayParting')?'active':''}}"
                           href="{{ route('dayPartingHistory') }}">History</a>
                    </div>
                </div>
            </li> --}}
            {{-- <li class="nav-item {{(Request::segment(2) == 'campaign' && Request::segment(1) == 'client' && Request::segment(3) == 'tags')?'active':''}}">
                <a class="nav-link  {{(Request::segment(2) == 'campaign' && Request::segment(1) == 'client' && Request::segment(3) == 'tags')?'active':''}}"
                   href="{{ route('campaign.tags') }}">
                    <i class="fas fa-fw fa-hashtag"></i>
                    <span class="text-capitalize">Campaign Tagging</span>
                </a>
            </li> --}}
            {{-- <li class="nav-item {{(Request::segment(1) == 'advertisingReports') ? 'active':''}}">
                <a class="nav-link  {{(Request::segment(1) == 'advertisingReports') ?'active':''}}"
                   href="{{ route('advertisingReportsEmailView') }}">
                    <img style="height: 18px" src="{{asset('public/images/a-d-reports-side-bar-icon.png')}}">
                    <span class="text-capitalize">Email Ad Reports</span>
                </a>
            </li> --}}
        <!-- Biding Rules -->
            {{-- <li class="nav-item {{(Request::segment(1) == 'bidding-rule')?'active':''}}">
                <a class="nav-link ml-1 {{(Request::segment(1) == 'bidding-rule')?'active':''}}"
                   href="{{ route('bidding-rule') }}">
                    <img width="12px" src="{{url('public/svg/Biding-Rules.png.svg')}}" alt="Biding-Rules">
                    <span class="text-capitalize pl-1">Bidding Rules</span>
                </a>
            </li> --}}
        @endif

    <!-- Side Menu Ends -->

        @if((session()->get("activeRole")!=3))
            <hr class="sidebar-divider d-none d-md-block">
    @endif
    <!-- Sidebar Toggler (Sidebar) -->
        <div class="text-center d-none d-md-inline">
            <button class="rounded-circle waves-effect  border-0" id="sidebarToggle"></button>
        </div>
    </ul>
@endsection