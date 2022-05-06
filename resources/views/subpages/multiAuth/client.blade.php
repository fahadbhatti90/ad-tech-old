@extends('layout.app')
@extends('inc.side_menu')
@extends('inc.nav_bar')
@extends('modal.logout')
@section('title', isset( $pageTitle ) ? $pageTitle : '')
@section('content')

    @push('css')
        <link href="{{asset('public/css/select2.min.css')}}" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('public/tooltipster/dist/css/tooltipster.bundle.min.css') }}"
              type="text/css"/>
        <link rel="stylesheet" href="{{ asset('public/vendor/datatables/dataTables.bootstrap4.min.css') }}"
              type="text/css"/>
        <link rel="stylesheet" href="{{ asset('public/css/scraping_custom_style.css?'.time()) }}" type="text/css"/>
        <link rel="stylesheet" href="{{ asset('public/css/multiAuth/superAdmin/clientPage.css?'.time()) }}"
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
        <script type="text/javascript" src="{{asset('public/js/select2.min.js')}}"></script>
        <script type="text/javascript"
                src="{{asset('public/vendor/formvalidation/dist/js/plugins/PasswordStrength.min.js')}}"></script>
        <script type="text/javascript" src="{{ asset('public/js/multiAuth/client/client.js?'.time()) }}"></script>

    @endpush
     <style>
         .select2 {
             width: 100% !important;
         }
         .select2-selection__choice__remove {
             float: right !important;
             padding-left: 5px;
         }
         .append_users div{
             padding: 5px;
             /*padding-left: -0.25rem !important;*/
         }
         .append_users{
             width: 100%;
             max-height:150px;
             overflow:auto;
         }
         .deletePopupText {
             text-transform: none;
         }
     </style>
    <!-- Begin Page Content -->
    <div class="container-fluid">
    @include('partials.formPreloader')
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <button type="button" class="btn btn-primary addSchedule sc-move-right" data-toggle="modal"
                    data-target="#addScheduleFormModel">
                Add Brand
            </button>
        </div>
        <div class="card mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Brand list</h6>
            </div>
            <div class="card-body">

                <table id="addClientTable" class="table table-striped table-bordered addClientTableClass"
                       style="width:100%">
                    <thead>
                    <tr>
                        <th>Sr. #</th>
                        <th>Name</th>
                        <th>Email</th>
                        <!-- <th>Agency</th> -->
                        <th>Created At</th>
                        <th class="text-center">Action</th>
                       <!--  <th class="text-center">Log In Status</th> -->
                    </tr>
                    </thead>
                    <tbody>
                    @isset($clients)
                        @foreach($clients as  $key => $client)
                            <tr id="{{ $client->id }}">

                                <td> {{ $key+1 }} </td>
                                <td class="text-capitalize">
                                     @php
                                     $managers=[];
                                    $managersName=[];
                                     foreach ($client->brandAssignedUsers as $brandAssignedUsers) {
                                         $managers[] = trim($brandAssignedUsers->id);
                                         $managersName[] = trim($brandAssignedUsers->name);
                                         }
                                  $brandManagers = implode(',',$managers);
                                  $brandManagerNames = implode(',',$managersName);
                                     @endphp
                                    @if(strlen($client->name) < 30 )
                                        <div class="tooltip" name="{{ $client->name }}"  managers="{{ $brandManagers }}" managersNames="{{ $brandManagerNames }}" >{{$client->name}}</div>
                                    @else
                                        <div class="tooltip" title="{{ $client->name }}" name="{{ $client->name }}" managers="{{ $brandManagers }}" managersNames="{{ $brandManagerNames }}" >{{ str_limit($client->name,30) }}</div>
                                    @endif
                                </td>
                                <td>

                                    @if(strlen($client->email) < 30 )
                                        <div class="tooltip"  email="{{ $client->email }}">{{ $client->email }}</div>
                                    @else
                                        <div class="tooltip" title="{{ $client->email }}"  email="{{ $client->email }}">{{ str_limit($client->email,30) }}</div>
                                    @endif

                                </td>
                              
                                <td>{{  date("Y-m-d",strtotime($client->created_at))}}</td>
                                <td class="text-center">
                                    @if($client->isParentBrand != 1 )
                                        <i class="fa fa-trash"> | </i>
                                        <i class="fa fa-edit"> | </i>
                                        <i class="fa fa-info-circle"></i>
                                        @else
                                        &nbsp;&nbsp;<i style="margin-left: 11%;" class="fa fa-edit"> | </i>
                                        <i class="fa fa-info-circle"></i>
                                    @endif
                                    <!-- <i class="fa fa-key"></i> -->
                                </td>
                                
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
                                <h5 class="modal-title" id="exampleModalLongTitle">Add Brand</h5>
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
                                                      action="{{ route('super.clients.submit') }}" action_type="1"
                                                      enctype="multipart/form-data">
                                                    @csrf
                                                    {{-- Client Name --}}
                                                    <div class="form-group ">
                                                        <label for="clientName">Name</label>
                                                        <input type="text" name="clientName" class="form-control"
                                                               placeholder="Brand Name" id="clientName">
                                                    </div>
                                                    {{-- Client Email --}}
                                                    <div class="form-group ">
                                                        <label for="clientEmail">Email</label>
                                                        <input type="text" name="clientEmail" class="form-control"
                                                               placeholder="Brand Email" id="clientEmail">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="selectedUsers">Select User</label>
                                                        <select class="form-control js-example-basic-multiple"
                                                                name="selectedUsers" id="selectedUsers" multiple>
                                                            <!--    <option value="" selected>Please Select Sponsord Type</option> -->
                                                            @isset($users)
                                                                @foreach ($users as $user)
                                                                    <option value="{{$user->id}}"  title="{{$user->name.'<'.$user->email.'>'}}">{{str_limit($user->name.'<'.$user->email.'>',50)}}</option>
                                                                @endforeach
                                                            @endisset

                                                        </select>
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
               {{-- <div class="modal fade assignedUserModel" id="assignedUserModel" tabindex="-1" role="dialog"
                     aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">Assigned Users</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">


                                     <div >

                                     </div>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>--}}
                {{--pop to show  assigned brands starts--}}
                <div class="modal fade assignedUserModel" id="assignedUserModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                    <div class="modal-dialog modal-ls modal-dialog-centered" role="document" >
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">Metrics List</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">

                                <div class="row">
                                    <div class="col-12">
                                        <div class=" mt-0">
                                            <div class="col-xl-12 input-group mb-3">
                                                    <div class="append_users" id="append_users">

                                                    </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                {{--popup for show assigned brands ends--}}
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
    {{-- <script src="{{asset('public/js/ams_scripts/profile.js')}}"></script> --}}
@endsection
