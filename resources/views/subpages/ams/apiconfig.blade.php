@extends('layout.app')
@extends('inc.side_menu')
@extends('inc.nav_bar')
@extends('modal.logout')
@extends('modal.ams_apiConfig')
@section('title',$pageTitle)
@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800 sr-only">{{isset($pageHeading)?$pageHeading:''}}</h1>
                        <a class="btn btn-primary" href="https://www.amazon.com/ap/oa?client_id=amzn1.application-oa2-client.c9f64774daa347ad8f741984216ace51&scope=cpc_advertising:campaign_management&response_type=code&redirect_uri=https://dev.helo-tek.com/ams/handle_login">
                            Account setup
                        </a>
            {{-- Login With Amazon Button --}}
{{--            <a href id="LoginWithAmazon">--}}
{{--                <img border="0" alt="Login with Amazon"--}}
{{--                     src="https://images-na.ssl-images-amazon.com/images/G/01/lwa/btnLWA_gold_156x32.png"--}}
{{--                     width="156" height="32"/>--}}
{{--            </a>--}}
            @if(Session::has('lwamessage'))
                <p id="lwa" class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('lwamessage') }}</p>
            @endif
            @if(!isset($api_parameter->client_id))
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#amsKeysModal">
                    Add API Parameter
                </button>
            @endif
        </div>
        @if(!isset($api_parameter->client_id))
            <div class="alert alert-danger" role="alert">
                API Parameter Required
            </div>
        @endif
        @if(Session::has('message'))
            <p class="alert {{ session('alert-class', 'alert-info') }}">{{ session('message') }}</p>
        @endif
        {{-- Show API Entered Parameter Value --}}
        <div class="card shadow mb-4">
            <!-- Card Header - Accordion -->
            <a href="#collapseCardExample" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardExample">
                <h6 class="m-0 font-weight-bold text-primary">Parameters</h6>
            </a>
            <!-- Card Content - Collapse -->
            <div class="collapse show" id="collapseCardExample">
                <div class="card-body">
                    @if(Session::has('bodyToken'))
                        <pre>@php print_r(json_decode(Session::get('bodyToken')))@endphp</pre>
                    @endif
                    @if(Session::has('bodyProfile'))
                        <pre>@php print_r(json_decode(Session::get('bodyProfile')))@endphp</pre>
                    @endif
                    <form class="user">
                        <div class="form-group row">
                            <div class="col-sm-12 mb-6 mb-sm-0">
                                <label for="clientId">Client ID</label>
                                <input type="text"
                                       class="form-control form-control-user"
                                       id="client_id"
                                       placeholder="Client ID" value="{{isset($api_parameter->client_id)?$api_parameter->client_id:''}}">
                            </div>
                            <div class="col-sm-12 mt-3">
                                <label for="clientSecret">Client Secret</label>
                                <input type="text"
                                       class="form-control form-control-user"
                                       id="client_secret"
                                       placeholder="Client Secret" value="{{isset($api_parameter->client_secret)?$api_parameter->client_secret:''}}">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
    <div id="amazon-root"></div>
    <script type="text/javascript">
        window.onAmazonLoginReady = function () {
            amazon.Login.setClientId('amzn1.application-oa2-client.c9f64774daa347ad8f741984216ace51');
        };
        (function (d) {
            var a = d.createElement('script');
            a.type = 'text/javascript';
            a.async = true;
            a.id = 'amazon-login-sdk';
            a.src = 'https://assets.loginwithamazon.com/sdk/na/login1.js';
            d.getElementById('amazon-root').appendChild(a);
        })(document);
        document.getElementById('LoginWithAmazon').onclick = function () {
            options = {}
            options.scope = 'profile';
            options.scope_data = {
                'profile': {'essential': false}
            };
            amazon.Login.authorize(options,
                'https://dev.helo-tek.com/ams/handle_login');
            return false;
        };
    </script>
    <script type="text/javascript">
        $(function() {
            setTimeout(function() {
                $(".alert").hide()
            }, 5000);
        });
    </script>
@endsection
