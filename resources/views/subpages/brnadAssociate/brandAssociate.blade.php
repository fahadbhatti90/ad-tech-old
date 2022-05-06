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
        <link rel="stylesheet" href="{{ asset('public/css/brandAssociate/brandAssociate.css?'.time()) }}" type="text/css"/>
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
        <script type="text/javascript" src="{{ asset('public/js/brandAssociate/brandAssociate.js?'.time()) }}"></script>
        <link rel="stylesheet" href="{{ asset('public/css/tooltipUpdatedCss.css?'.time()) }}"
              type="text/css"/>
    @endpush
    <style>
        .select2 {
            width: 100% !important;
        }
        .select2-selection__choice__remove {
            float: right !important;
            padding-left: 5px;
        }
    </style>
    <!-- Begin Page Content -->
    <div class="container-fluid">
    @include('partials.formPreloader')
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <button type="button" class="btn btn-primary addSchedule sc-move-right" data-toggle="modal"
                    data-target="#addScheduleFormModel">
                Associate Brands
            </button>
        </div>
        <div class="card mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Associated brands list</h6>
            </div>
            <div class="card-body">

                <table id="accountTable" class="table table-striped table-bordered addClientTableClass"
                       style="width:100%">
                    <thead>
                    <tr>
                        <th>Sr. #</th>
                        <th>Brand Name</th>
                        <th>User Name</th>
                        <!-- <th>Associated At</th> -->
                        <th class="text-center">Action</th> 
                    </tr>
                    </thead>
                    <tbody>
                    @isset($accounts)
                        @foreach($accounts as  $key => $account)
                            <tr id="{{ $account->id }}">
                                <td> {{ $key+1 }} </td>
                                <td id= {{ $account->id }}>
                                    @isset($account->name)

                                        @if(strlen($account->name) < 23 )
                                            <div class="tooltip" >{{ $account->name }}</div>
                                        @else
                                            <div class="tooltip" title="{{ $account->name }}">{{ str_limit($account->name,23) }}</div>
                                        @endif
                                    @endisset
                                </td>
                                 <td id= {{ $account->fkManagerId }}>
                                    @isset($account->manager->name)
                                         @if(strlen($account->manager->name) < 23 )
                                             <div class="tooltip" >{{ $account->manager->name }}</div>
                                         @else
                                             <div class="tooltip" title="{{ $account->manager->name }}">{{ str_limit($account->manager->name,23) }}</div>
                                         @endif
                                    @endisset
                                </td>
                                 <td class="text-center">
                                    <i class="fa fa-trash"></i>
                                    <!-- <i class="fa fa-edit"> </i> -->
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
                                <h5 class="modal-title" id="exampleModalLongTitle">Associate Brand</h5>
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
                                                <form id="addAccountForm" method="POST"
                                                      action="{{ route('manage.brandAssociation') }}" action_type="1"
                                                      enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="form-group ">

                                                        <div class="form-group ">
                                                            <label for="mwsSeller">Select Brands</label>
                                                            <select class=" form-control" {{ isset($clients[0])? "" : "disabled" }} name="brandId" id="brandId" >
                                                                <option value="" disabled>Choose...</option>
                                                                @isset($clients[0])
                                                                    @foreach ($clients as $client)
                                                                        <option
                                                                                value="{{ $client->id }}">

                                                                            <span class="text-capitalize">
                                                                                {{ ucfirst ($client->name)}}
                                                                            </span>
                                                                            {{ " <".  str_limit($client->email,25) .">" }}
                                                                        </option>
                                                                    @endforeach
                                                                @endisset
                                                            </select>
                                                        </div>

                                                        <div class="form-group ">
                                                            <label for="Manager">Select User</label>
                                                            <select class=" form-control js-example-basic-multiple" name="managerId" id="managerId" multiple>


                                                                @isset($managers)
                                                                    @foreach ($managers as $managers)
                                                                        <option
                                                                                value="{{ $managers->id }}">

                                                                            <span class="text-capitalize">
                                                                                {{ ucfirst ($managers->name)}}
                                                                            </span>
                                                                            {{ " <".  str_limit($managers->email,25) .">" }}
                                                                        </option>
                                                                    @endforeach
                                                                @endisset
                                                            </select>
                                                        </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                                data-dismiss="modal">Close
                                                        </button>
                                                        <button class="btn btn-primary accountSubmitButton"
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
@endsection
