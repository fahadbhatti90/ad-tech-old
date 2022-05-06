@if((session()->get("activeRole")==3))
    @push("css")
        <link rel="stylesheet" type="text/css" href="{{ asset('public/css/client/CustomTooltipCss.css')}}"/>
        <link rel="stylesheet" href="{{ asset("public/css/client/BrandSwitcherPopUp.css?".time()) }}">
    @endpush
    @push("js")
        <script src="{{ asset('public/js/multiAuth/client/BrandSwitcherPopUp.js') }}"></script>
    @endpush
@endif
@section('nav_bar')
    <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
        <!-- Sidebar Toggle (Topbar) -->
        <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
            <i class="fa fa-bars"></i>
        </button>
        @if((session()->get("activeRole")==1))
            <h4 class="ml-2 mt-2">Super Admin Portal</h4>
        @endif
        @if((session()->get("activeRole")==2))
            <h4 class="ml-2 mt-2">Admin Portal</h4>
        @endif
        @if((session()->get("activeRole")==3))
            <h4 class="ml-2 mt-2">Brand Portal</h4>
        @endif
    <!-- Topbar Navbar -->
        <ul class="navbar-nav ml-auto Notifications">
            <li class="nav-item notificationLi ">
                {{-- notify --}}
                <a class="nav-link" href="#" id="notificationDropdown">
                    <span class="fa fa-bell"><mark>0</mark></span>
                </a>
                <div class="dropdown-menu keep-open notificationPopUp dropdown-menu-right shadow animated--grow-in">
                    @if((session()->get("activeRole")==3))
                        @include('partials.notificationsClient')
                    @elseif((session()->get("activeRole")==2))
                        @include('partials.notificationsAdmin')
                    @else
                        @include('partials.notificationsSuperAdmin')
                    @endif
                </div>
            </li>

            @if((session()->get("activeRole")==3))
                <div class="topbar-divider d-none d-sm-block"></div>
                <li class="nav-item">
                    <a href="#" class="nav-link text-gray-600" id="brandSwitcher"
                       brand-id="{{ managerHasBrand() ? getBrandId():'' }}"
                       datatitle="{{ managerHasBrand() ? getBrandName() : "" }}"
                       action-url="{{ route("manager.brands") }}">
                        @if (managerHasBrand())
                            {{ Str::limit(getBrandName(),20) }}
                        @else
                            No Brand Assigned
                        @endif
                    </a>
                </li>
            @endif
            {{-- {{-- <li class="nav-item">
                <a href="#">
                  <span class="fa fa-bell"><mark>23</mark></span>
                </a>
              </li> --}}

            {{-- <li class="nav-item">
              <a href="#">
              <span class="glyphicon glyphicon-time"></span>
              </a>
            </li> --}}

            <div class="topbar-divider d-none d-sm-block"></div>
            <!-- Nav Item - User Information -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
                   aria-haspopup="true" aria-expanded="false">
                    {{--                    <img class="img-profile mr-1 rounded-circle" src="https://source.unsplash.com/QAB-WJcbgJk/60x60">--}}
                    <img class="img-profile mr-1 rounded-circle"
                         src="https://dt2sdf0db8zob.cloudfront.net/wp-content/uploads/2019/12/9-Best-Online-Avatars-and-How-to-Make-Your-Own-for-Free-image1-5.png">
                    <span class="mr-2 d-none d-lg-inline text-gray-600 small text-capitalize font-weight-bold">{{ auth()->user()->name }}</span>
                    <i class="fa fa-chevron-down" aria-hidden="true"></i>
                </a>
                <!-- Dropdown - User Information -->
                <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                     aria-labelledby="">
                <!--  @if(auth()->user()->hasAnyRole(1))
                    @if(auth()->user()->hasAnyRole(1))
                        @if((session()->get("activeRole")!=1))
                            <a class="dropdown-item" href="{{route("adminDashboard")}}">
                            <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                           Super admin
                        </a>
                        @endif
                    @endif
                    @if(auth()->user()->hasAnyRole(2))
                        @if((session()->get("activeRole")!=2))
                            <a class="dropdown-item" href="{{route("admin.dashboard")}}">
                        <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                        Agency
                    </a>
                    @endif
                    @endif
                    @if(auth()->user()->hasAnyRole(3))
                        @if((session()->get("activeRole")!=3))
                            <a class="dropdown-item" href="{{route("client.dashboard")}}">
                        <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                        Manager
                    </a>
                    @endif
                    @endif
                @endif -->


                    <!-- <a class="dropdown-item sr-only" href="#">
                        <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                        Settings
                    </a>
                     <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#userSettingsModal">
                        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                        Settings
                    </a> -->
                    @if(auth()->user()->hasAnyRole(1))
                        @if((session()->get("activeRole")!=1))
                            <a class="dropdown-item" href="{{route("adminDashboard")}}">
                                <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                Super Admin Portal
                            </a>
                        @endif
                    @endif
                    @if(auth()->user()->hasAnyRole(2))
                        @if((session()->get("activeRole")!=2))
                            <a class="dropdown-item" href="{{route("admin.dashboard")}}">
                                <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                Admin Portal
                            </a>
                        @endif
                    @endif
                    @if(auth()->user()->hasAnyRole(3))
                        @if((session()->get("activeRole")!=3))
                    <a class="dropdown-item" href="{{route("client.dashboard")}}">
                        <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                        Brand Portal
                    </a>
                        @endif
                    @endif
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                        Logout
                    </a>
                </div>
            </li>
        </ul>

        @if(auth()->user()->hasAnyRole(3))
            @if((session()->get("activeRole")==3))
            @include('modal.brandSwitcherModel')
            @endif
        @endif
    </nav>
@endsection
@push('brandSwitcher')
    <script type="text/javascript" src="{{asset('public/js/brandSwitcher/brandSwitcher.js?'.time())}}"></script>
@endpush