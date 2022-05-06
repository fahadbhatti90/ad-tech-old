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
        <script type="text/javascript" src="{{asset('public/js/select2.min.js')}}"></script>
        <script type="text/javascript" src="{{ asset('public/js/manageManager/addManager/addManager.js?'.time()) }}"></script>
        <script type="text/javascript" src="{{ asset('public/js/manageManager/addManager/associateManager.js?'.time()) }}"></script>

    @endpush
    <style>
        .select2 {
            width: 100% !important;
        }
        .select2-selection__choice__remove {
            float: right !important;
            padding-left: 5px;
        }
        /*.swal2-content{
            overflow-y:auto !important;
        }*/
        .swal2-content{
            max-height: 300px;
            overflow: auto;
        }
        .deletePopupText {
            text-transform: none;
        }
        /*#append_brands{
            max-height:300px;
            overflow:auto;
        }*/
        .append_brands{
            width: 100%;
            max-height:150px;
            overflow:auto;
        }
        .append_brands div{
            padding: 5px;
        }
        #associateManagerFields{
            margin-top: -25px;
        }
    </style>

    <!-- Begin Page Content -->
    <div class="container-fluid">
    @include('partials.formPreloader')
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <button type="button" class="btn btn-primary addSchedule sc-move-right" data-toggle="modal"
                    data-target="#addScheduleFormModel">
                Add User
            </button>
        </div>
        <div class="card mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Users list</h6>
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
                                    @php
                                        $brandIdsArr=[];
                                       $brandNamesArr=[];
                                        foreach ($client->userAssignedbrands as $userAssignedBrand) {
                                            $brandIdsArr[] = trim($userAssignedBrand->id);
                                            $brandNamesArr[] = trim($userAssignedBrand->name);
                                            }
                                     $brandIds = implode(',',$brandIdsArr);
                                     $brandNames = implode(',',$brandNamesArr);
                                    @endphp

                                    @if(strlen($client->name) < 30 )
                                        <div class="tooltip" name="{{ $client->name }}" >{{$client->name}}</div>
                                    @else
                                        <div class="tooltip" title="{{ $client->name }}" name="{{ $client->name }}">{{ str_limit($client->name,30) }}</div>
                                    @endif
                                </td>
                                <td>
                                    @if(strlen($client->email) < 30 )
                                        <div class="tooltip" name="{{ $client->email }}" brandIds="{{ $brandIds }}" brandNames="{{ $brandNames }}" email="{{ $client->email }}" >{{$client->email}}</div>
                                    @else
                                        <div class="tooltip" title="{{ $client->email }}" brandIds="{{ $brandIds }}" brandNames="{{ $brandNames }}" email="{{ $client->email }}" >{{ str_limit($client->email,30) }}</div>
                                    @endif

                                </td>
                                {{--<td class="text-capitalize"
                                    id= {{ $client->fkAgencyId }}>{{ $client->agency->name }}</td>--}}

                                <td>{{  date("Y-m-d",strtotime($client->created_at))}}</td>
                                <td class="text-center">
                                     <i class="fa fa-trash"> | </i> 
                                    <i class="fa fa-edit"> | </i>
                                    <i class="fa fa-key"> | </i>
                                    <i class="fa fa-info-circle"></i>
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
                                <h5 class="modal-title" id="exampleModalLongTitle">Add User</h5>
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
                                                      action="{{ route('admin.manager.submit') }}" action_type="1"
                                                      enctype="multipart/form-data">
                                                    @csrf
                                                    {{-- Client Name --}}
                                                    <div class="form-group ">
                                                        <label for="clientName">Name</label>
                                                        <input type="text" name="clientName" class="form-control"
                                                               placeholder="User Name" id="clientName">
                                                    </div>
                                                    {{-- Client Email --}}
                                                    <div class="form-group ">
                                                        <label for="clientEmail">Email</label>
                                                        <input type="text" name="clientEmail" class="form-control"
                                                               placeholder="User Email" id="clientEmail">
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
                                                    <div class="form-group ">
                                                        <label for="mwsSeller">Associate Brands</label>
                                                        <select class=" form-control js-example-basic-multiple"  name="selectedBrands" id="selectedBrands" multiple>
                                                            @isset($brands)
                                                                @foreach ($brands as $brand)
                                                                    <option
                                                                            value="{{ $brand->id }}" title="{{ucfirst ($brand->name).'<'.$brand->email.'>'}}">
                                                                        {{str_limit(ucfirst ($brand->name).'<'.$brand->email.'>',50) }}
                                                                    </option>
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
                                                                type="submit">Continue
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
             {{--   <div class="modal fade associateBrandModel" id="associateBrandModel" tabindex="-1" role="dialog"
                     aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title w-100 text-center text-primary" id="exampleModalLongTitle">Delete User</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-lg-12 text-center">
                                        Assign following brands to any user before you delete this.
                                        <br>
                                    </div>

                                    <div class="text-center" id="append_no_user_brands">

                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class=" mt-3">
                                            <div class="col-xl-12 input-group mb-3">
                                                <form id="associateManager" method="POST"
                                                      action="{{ route('addBrandManagers') }}" action_type="1"
                                                      enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="form-group ">
                                                        <label for="mwsSeller">Select User</label>
                                                        <select class=" form-control js-example-basic-multiple" {{ isset($clients[0])? "" : "disabled" }} name="selectedUsers" id="selectedUsers" multiple>
                                                            @isset($clients[0])
                                                                @foreach ($clients as $clients)
                                                                    <option
                                                                            value="{{ $clients->id }}">

                                                                            <span class="text-capitalize">
                                                                                {{ ucfirst ($clients->name)}}
                                                                            </span>
                                                                        {{ " <".  str_limit($clients->email,25) .">" }}
                                                                    </option>
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
                                                    <div class="modal-footer text-center">
                                                        <button type="button" class="btn btn-secondary"
                                                                data-dismiss="modal">Cancle
                                                        </button>
                                                        <button class="btn btn-primary ClientSubmitButton"
                                                                type="submit">Continue
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
--}}
                {{--popup for show assigned brands starts--}}
                <div class="modal fade associateBrandModel" id="associateBrandModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                    {{--<div class="modal-dialog modal-ls modal-dialog-centered" role="document" >--}}
                        <div class="modal-dialog modal-ls modal-dialog-top" role="document">
                        <div class="modal-content">
                            <form id="associateManager" method="POST"
                                  action="{{ route('addBrandManagers') }}" action_type="1"
                                  enctype="multipart/form-data">
                            <div class="modal-header">
                                <h5 class="modal-title w-100 text-center text-primary" id="exampleModalLongTitle">Reassign Brand And Delete User</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">

                                <div class="row">
                                    <div class="col-12">
                                        <div class=" mt-0">
                                            <div class="col-xl-12 input-group mb-3">

                                                    <div class="append_brands" id="append_no_user_brands">

                                                    </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{--select element starts--}}
                                <div class="row">
                                    <div class="col-12">
                                        <div class=" mt-3">
                                            <div class="col-xl-12 input-group mb-3">
                                                    @csrf
                                                    <div id="associateManagerFields" >
                                                        <input type="hidden" id="assignedBrandToOtherIds" name="assignedBrandToOtherIds" value="">
                                                        <input type="hidden" id="deleteUserId" name="deleteUserId" value="">
                                                        <div class="form-group ">
                                                            <label for="mwsSeller">Select User Type</label>
                                                            <select  class=" form-control" name="assignWithUserType" id="assignWithUserType" >
                                                                <option value="Admin" selected="selected">Admin</option>
                                                                <option value="Manager">Manager</option>
                                                            </select>
                                                        </div>


                                                    <div class="form-group ">
                                                        <label for="mwsSeller">Select User</label>
                                                        <select  class=" form-control js-example-basic-multiple"  name="selectedUsers" id="selectedUsers" multiple>
                                                            @isset($adminUsers[0])
                                                                @foreach ($adminUsers as $adminUsers)
                                                                    <option
                                                                            value="{{ $adminUsers->id }}" selected="selected">

                                                                            <span class="text-capitalize">
                                                                                {{ ucfirst ($adminUsers->name)}}
                                                                            </span>
                                                                        {{ " <".  str_limit($adminUsers->email,25) .">" }}
                                                                    </option>
                                                                @endforeach
                                                            @endisset
                                                        </select>
                                                    </div>
                                                    </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{--select element ends--}}

                            </div>
                            <div class="modal-footer text-center">
                                <button type="button" class="btn btn-secondary"
                                        data-dismiss="modal">Cancle
                                </button>
                                <button class="btn btn-primary ClientSubmitButton"
                                        type="submit">Continue
                                </button>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
                {{--popup for assig users to brands ends--}}

                {{--popup for show assigned brands starts--}}
                <div class="modal fade associatedUserBrandModel" id="associatedUserBrandModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                    <div class="modal-dialog modal-ls modal-dialog-centered" role="document" >
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">Associated Brands</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">

                                <div class="row">
                                    <div class="col-12">
                                        <div class=" mt-0">
                                            <div class="col-xl-12 input-group mb-3">
                                                    <div class="append_brands" id="append_brands">

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
    <!-- /.container-fluid -->
    {{-- <script src="{{asset('public/js/ams_scripts/profile.js')}}"></script> --}}
@endsection
