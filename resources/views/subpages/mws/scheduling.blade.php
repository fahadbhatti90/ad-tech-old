@extends('layout.app')
@extends('inc.side_menu')
@extends('inc.nav_bar')
@extends('modal.logout')
@section('title',$pageTitle)
@section('content')
    @push('styles')
        <link href="{{asset('public/css/styles.css?'.time())}}" rel="stylesheet">
    @endpush
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
       {{-- <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{isset($pageHeading)?$pageHeading:''}}</h1>
        </div>--}}
        <div class="flash-message"> @if( Session::has("success") )
                <div class="alert alert-success alert-block" role="alert">
                    <button class="close" data-dismiss="alert"></button>
                    {{ Session::get("success") }}
                </div>
            @endif

            {{--//Bonus: you can also use this subview for your error, warning, or info messages--}}
            @if( Session::has("error") )
                <div class="alert alert-danger alert-block" role="alert">
                    <button class="close" data-dismiss="alert"></button>
                    {{ Session::get("error") }}
                </div>
            @endif
        </div>
        <div class="card mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Cron List</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    {{-- Cron Job List --}}
                    <div class="col-6">
                        <h6 class="m-0 font-weight-bold text-primary">Scheduling List</h6>
                        <div class="row mt-3">
                            {{-- Get Profile List --}}
                            <div class="col-xl-12 input-group mb-3">
                                <form style="min-width: 53%;" name="add_cron_from" id="add_cron_from" action="{{url('/mws/addCron')}}" method="POST">

                                    @csrf
                                    <div class="form-group">
                                        <label class="col-sm-6 col-form-label">Cron List</label>
                                        <select class="form-control" id="report_type" name="report_type">
                                            <option value="" selected>Choose...</option>
                                            <option value="Catalog">Catalog</option>
                                            <option value="Inventory">Inventory</option>
                                            <option value="Sales">Sales</option>
                                        </select>
                                    </div>
                                    <div class="form-group ">
                                        <label class="col-sm-6 col-form-label">Cron Time List</label>
                                        <select class="form-control" name="cron_time" id="cron_time">
                                            <option value="" selected>Choose...</option>
                                            @php
                                                $time_now_new = strtotime('00:00');
                                            @endphp
                                            @for($i=0; $i<24; $i++)
                                                @php $time_now_new = date("H:i", strtotime($time_now_new)); @endphp
                                                <option value="{{$time_now_new}}">{{$time_now_new}}</option>
                                                @php $time_now_new = date("H:i", strtotime('+60 minutes', strtotime($time_now_new))); @endphp
                                            @endfor
                                        </select>

                                    </div>
                                    <div class="form-group ">
                                        <label class="col-sm-6 col-form-label">Cron Status</label>
                                        <div class="form-check form-check-inline">
                                            <input type="radio" class="form-check-input" name="cronstatus"
                                                   value="0"/>
                                            <label class="form-check-label">Stop</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input type="radio" class="form-check-input" name="cronstatus"
                                                   value="1" checked/>
                                            <label class="form-check-label">Run</label>
                                        </div>
                                    </div>
                                    <!-- Do NOT use name="submit" or id="submit" for the Submit button -->
                                    <div class="form-group row mx-auto">
                                        <div class="col-sm-10">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" id="btnSubmitMwsConfig" type="submit">Save
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    {{-- Cron List --}}
                    <div class="col-6">
                        <h6 class="m-0 font-weight-bold text-primary">Scheduled Cron List</h6>

                            <table class="table mt-5">
                                <thead>
                                <tr>
                                    <th scope="col">Cron Name</th>
                                    <th scope="col">Time</th>
                                    <th scope="col">Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($allCrons->count() > 0)
                                @foreach ($allCrons as $allCrons_value)
                                    <tr>
                                        <td>{{$allCrons_value->report_type}}</td>
                                        <td>{{--{{ date('h:i A', strtotime($allCrons_value->cronStartTime))}}--}}
                                            {{ $allCrons_value->cronStartTime  }}
                                        </td>
                                        <td>
                                            @if($allCrons_value->status==1)
                                                Run
                                            @else
                                                Stop
                                            @endif</td>
                                    </tr>
                                @endforeach
                                @else
                                    <tr><td colspan="3" align="center">No Cron List Found.</td></tr>
                                @endif
                                </tbody>
                            </table>

                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- /.container-fluid -->
    <script src="{{asset('public/js/mws_scripts/mws_AddCron.js?'.time())}}"></script>
@endsection
