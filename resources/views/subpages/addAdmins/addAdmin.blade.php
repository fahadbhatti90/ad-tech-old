@extends('layout.app')
@extends('inc.side_menu')
@extends('inc.nav_bar')
@extends('modal.logout')
@section('title', isset( $pageTitle ) ? $pageTitle : '')
@section('content')

    @push('css')
        <link rel="stylesheet" href="{{ asset('public/tooltipster/dist/css/tooltipster.bundle.min.css') }}"
              type="text/css"/>
        <link rel="stylesheet" href="{{ asset('public/vendor/datatables/dataTables.bootstrap4.min.css') }}"
              type="text/css"/>
        <link rel="stylesheet" href="{{ asset('public/css/addManager/add_manager_custom_style.css?'.time()) }}" type="text/css"/>
        <link rel="stylesheet" href="{{ asset('public/css/addManager/addManagerPage.css?'.time()) }}"
              type="text/css"/>
        <link rel="stylesheet" href="{{ asset('public/css/tooltipUpdatedCss.css?'.time()) }}"
              type="text/css"/>
    @endpush

    @push('js')
        <script type="text/javascript"
                src="{{ asset('public/tooltipster/dist/js/tooltipster.bundle.min.js')}}"></script>
        <script type="text/javascript" src="{{asset('public/vendor/datatables/jquery.dataTables.min.js')}}"></script>
        <script type="text/javascript"
                src="{{asset('public/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
        <script type="text/javascript"
                src="https://cdnjs.cloudflare.com/ajax/libs/es6-shim/0.35.3/es6-shim.min.js"></script>
        <script type="text/javascript"
                src="{{asset('public/vendor/formvalidation/dist/js/plugins/zxcvbn.js')}}"></script>
        <script type="text/javascript"
                src="{{asset('public/vendor/formvalidation/dist/js/plugins/PasswordStrength.min.js')}}"></script>
        <script type="text/javascript" src="{{ asset('public/js/manageAdmins/addAdmin/addAdmin.js?'.time()) }}"></script>
    @endpush

    <!-- Begin Page Content -->
    <div class="container-fluid">
    @include('partials.formPreloader')
        <!-- Page Heading -->
        @if($clients->isEmpty())
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <button type="button" class="btn btn-primary addSchedule sc-move-right" data-toggle="modal"
                    data-target="#addScheduleFormModel">
                Add Agency
            </button>
        </div>
         @endif
        
        <div class="card mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Agencies list</h6>
            </div>
            <div class="card-body">

                <table id="addClientTable" class="table table-striped table-bordered addClientTableClass"
                       style="width:100%">
                    <thead>
                    <tr>
                        <th>Sr. #</th>
                        <th>Name</th>
                        <th>Email</th>
                        {{-- <th>Agency</th>--}}
                        <th>Created At</th>
                        <th class="text-center">Action</th>
                        {{--<th class="text-center">Log In Status</th>--}}
                    </tr>
                    </thead>
                    <tbody>
                    @isset($clients)
                        @foreach($clients as  $key => $client)
                            <tr id="{{ $client->id }}">

                                <td> {{ $key+1 }} </td>
                                <td class="text-capitalize">
                                    <div class="tooltip" title="{{ $client->name }}">
                                        {{ str_limit($client->name,18) }}
                                    </div>
                                </td>
                                <td>
                                    <div class="tooltip" title="{{ $client->email }}" email="{{ $client->email }}">
                                        {{ str_limit($client->email,25) }}
                                    </div>
                                </td>
                                {{--<td class="text-capitalize"
                                    id= {{ $client->fkAgencyId }}>{{ $client->agency->name }}</td>--}}

                                <td>{{  date("Y-m-d",strtotime($client->created_at))}}</td>
                                <td class="text-center">
                                   <!-- <i class="fa fa-trash"> | </i> -->
                                    <i class="fa fa-edit"> | </i>
                                    <i class="fa fa-key"></i>
                                </td>
                                {{--<td class="text-center login_status" status="{{ $client->isLoggedIn($client->id) }}">
                                    @if ($client->isLoggedIn($client->id))
                                        <span class="loggedIn"></span>
                                    @else
                                        <span class="loggedOut"></span>
                                    @endif
                                </td>--}}
                            </tr>
                        @endforeach
                    @endisset
                    </tbody>

                </table>

                <div class="modal fade addClientModel" id="addScheduleFormModel" tabindex="-1" role="dialog"
                     aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">Add Agency</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    {{-- Cron Job List --}}
                                    <div class="col-12">
                                        <div class=" mt-3">
                                            {{-- Get Profile List --}}
                                            <div class="col-xl-12 input-group mb-3">
                                                <form id="addClientForm" method="POST"
                                                      action="{{ route('super.admin.submit') }}" action_type="1"
                                                      enctype="multipart/form-data">
                                                    @csrf
                                                    {{-- Client Name --}}
                                                    <div class="form-group ">
                                                        <label for="clientName">Name</label>
                                                        <input type="text" name="clientName" class="form-control"
                                                               placeholder="Agency Name" id="clientName">
                                                    </div>
                                                    {{-- Client Email --}}
                                                    <div class="form-group ">
                                                        <label for="clientEmail">Email</label>
                                                        <input type="text" name="clientEmail" class="form-control"
                                                               placeholder="Agency Email" id="clientEmail">
                                                    </div>
                                                    {{-- Agency --}}
                                                    {{-- <div class="form-group ">
                                                         <label for="agency">Select Agency</label>
                                                         <select class="form-control" name="agency" id="agency" disabled>
                                                             @foreach ($agencies as $agency)
                                                                 <option class="text-capitalize"
                                                                         value="{{ $agency->id }}">{{ $agency->name }}
                                                                 </option>
                                                             @endforeach
                                                         </select>
                                                     </div>--}}
                                                    {{-- password --}}
                                                    {{-- Client Name --}}
                                                    <div class="form-group passwordParent">
                                                        <label for="password">Password</label>
                                                        <input type="password" name="password" class="form-control"
                                                               placeholder="Password" id="password">
                                                    </div>
                                                    <div class="cf mb2">
                                                        <div class="fl w-100">
                                                            <div class="fl w-25 pa2"></div>
                                                            <div class="fl w-100 ba b--black-10 h1"
                                                                 style="height: 0.25rem">
                                                                <div id="passwordMeter" class="h-100"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                                data-dismiss="modal">Close
                                                        </button>
                                                        <button class="btn btn-primary ClientSubmitButton"
                                                                type="submit">Save
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
    {{-- <script src="{{asset('public/js/ams_scripts/profile.js')}}"></script> --}}
@endsection
