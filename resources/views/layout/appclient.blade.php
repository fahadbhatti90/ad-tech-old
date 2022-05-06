<!DOCTYPE html>
<html lang="en">

<head>
    <title>{{config('app.name')}} - @yield('title')</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Custom fonts for this template-->
    <link href="{{asset('public/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
    <!-- Custom styles for this template-->
    <link href="{{asset('public/css/app.min.css?'.time())}}" rel="stylesheet">
    <link href="{{asset('public/css/sweetalert2.min.css')}}" rel="stylesheet">
    <link href="{{asset('public/css/notification/notification.css?'.time())}}" rel="stylesheet">
    <link href="{{asset('public/css/notification/toaster.css?'.time())}}" rel="stylesheet">
	<link href="{{asset('public/vendor/formvalidation/dist/css/formValidation.min.css')}}" rel="stylesheet">
    {{-- <link href="{{asset('public/css/materialize.css?'.time())}}" rel="stylesheet"> --}}
    <link href="{{asset('public/css/client/customMaterialize.css?'.time())}}" rel="stylesheet">
    <link href="{{asset('public/css/client/client.css?'.time())}}" rel="stylesheet">
	 {{-- <link href="{{asset('public/css/timepicker.min.css')}}" rel="stylesheet"> --}}
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    @stack('styles')
    @stack('daterangepickercss')
    @stack('css')
	{{-- Jquery--}} 
    
	<script src="{{asset('public/vendor/jquery/jquery.min.js')}}"></script>
	<script src="{{asset('public/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
	<!-- Core plugin JavaScript-->
	<script src="{{asset('public/vendor/jquery-easing/jquery.easing.min.js')}}"></script>
	<!-- Custom scripts for all pages-->
	<script src="{{asset('public/js/app.min.js')}}"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/es6-shim/0.35.3/es6-shim.min.js"></script>
	<script src="{{asset('public/vendor/formvalidation/dist/js/FormValidation.min.js')}}"></script>
	<script src="{{asset('public/vendor/formvalidation/dist/js/plugins/Transformer.min.js')}}"></script>
	<script src="{{asset('public/vendor/formvalidation/dist/js/plugins/Bootstrap.min.js')}}"></script>
	<script src="{{asset('public/vendor/sweetalert2/sweetalert2.all.min.js')}}"></script>
	<script src="{{asset('public/js/vc_scripts/sweetalert2.min.js')}}"></script>
	
	{{--on off plugin starts--}}
    {{-- link: http://www.bootstraptoggle.com/--}}
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    {{--on off plugin ends--}}
    {{-- time picker statrts  link: https://gijgo.com/timepicker/example/bootstrap-4  --}}
    {{-- <script src="{{asset('public/js/timepicker.min.js')}}"></script> --}}
    <script src="{{asset('public/js/vc_scripts/axios.min.js')}}"></script>
    
    <script src="{{asset('public/js/notifications/pusher.min.js')}}"></script>
        <script src="{{asset('public/js/notifications/toaster.js')}}"></script>
        @if(null !== session()->get("activeRole"))
        @if((session()->get("activeRole")==3))
        <script src="{{asset('public/js/notifications/clientNotification.js?'.time())}}"></script>
        @elseif((session()->get("activeRole")==2)) 
        <script src="{{asset('public/js/notifications/notification.js?'.time())}}"></script>
        @elseif((session()->get("activeRole")==1))
        <script src="{{asset('public/js/notifications/superAdminNotification.js?'.time())}}"></script>
        @endif
        @endif
            <script src="{{asset('public/js/multiAuth/client/theme.js?'.time())}}"></script>
	<script>
		var base_url = '{{URL::to('/')}}';
		var _token = '{{csrf_token()}}';
        var siteURL = '{{ url('/') }}';
        jQuery.fn.visible = function() {
            return this.css('visibility', 'visible');
        };
    
        jQuery.fn.invisible = function() {
            return this.css('visibility', 'hidden');
        };
    
        jQuery.fn.visibilityToggle = function() {
            return this.css('visibility', function(i, visibility) {
                return (visibility == 'visible') ? 'hidden' : 'visible';
            });
        };
	</script>
    @stack('daterangepickerjs')
	@stack('js')
</head>


<body id="page-top" base_url={{ url('/') }} csrf="{{csrf_token()}}"  host = "{{ getHostForNoti() }}" active = "{{auth()->user()->id}}">
<!-- Page Wrapper -->
<div id="wrapper" class="">
    <!-- Sidebar -->
@yield('side_menu')
<!-- End of Sidebar -->
    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
        <!-- Main Content -->
        <div id="content">
            <!-- Topbar -->
        @yield('nav_bar')
        <!-- End of Topbar -->
            @yield('content')

        </div>
        <!-- End of Main Content -->
        <!-- Footer -->
        <footer class="sticky-footer bg-white">
            <div class="container my-auto">
                <div class="copyright text-center my-auto">
                    <span>Copyright &copy; {{config('app.name')}} @php echo date('Y') @endphp </span>
                </div>
            </div>
        </footer>
    </div>
    <!-- End of Page Wrapper -->
    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    <!-- Logout Modal-->
@yield('modal_logout')
{{-- AMS API Config Modal --}}
@yield('ams_apiConfig_modal')
{{-- MWS Modal --}}
@yield('mws_apiConfig_modal')
@yield('mws_editApiConfig_modal')
@yield('mws_DeleteConfirm_modal')
@yield('mwsAddCron')
@yield('mwsEditCron')
{{-- @include('partials.settingsPopUp') --}}
</body>

</html>