@extends('layout.app')
@extends('inc.side_menu')
@extends('inc.nav_bar')
@extends('modal.logout')
@extends('modal.userSettings.userSettingsModal')
@section('title', $pageTitle)
@section('content')
    @push('css')
        <link href="{{asset('public/css/superAdminDashboard/comingSoon.css?'.time())}}" rel="stylesheet">
    @endpush
    <!-- Begin Page Content -->
    <div class="container-fluid coming-soon">
        <div class="card" style="height: 440px;">
            <header>
                <h1>Coming Soon</h1>
            </header>

            <section>
               {{-- <h2>WE APOLOGIZE FOR INCONVENIENCE</h2>--}}

                {{--<p class="details">YOU NEED TO WAIT...</p>--}}
                {{-- <hr>--}}
                {{-- <p id="demo"></p>--}}
            </section>
        </div>
    </div>
    <script src={{asset('public/js/superAdminDashboard/comingSoon.js?'.time())}}"></script>
@endsection
