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
        <script type="text/javascript" src="{{ asset('public/js/accounts/accounts.js?'.time()) }}"></script>
    @endpush
    <style>
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
                Associate Accounts
            </button>
        </div>
        <div class="card mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Accounts list</h6>
            </div>
            <div class="card-body">

                <table id="accountTable" class="table table-striped table-bordered addClientTableClass"
                       style="width:100%">
                    <thead>
                    <tr>
                        <th>Sr. #</th>
                        <th>Name</th>
                        <th>Account Type</th>
                        <th>Brand Name</th>
                        <th>Created At</th>
                        <th class="text-center">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @isset($accounts)
                        @foreach($accounts as  $key => $account)
                            <tr id="{{ $account->id }}">
                                <td> {{ $key+1 }} </td>

                                <td class="text-capitalize">
                                    @if(strlen($account->accountName) < 23 )
                                        <div class="tooltip" >{{ $account->accountName }}</div>
                                    @else
                                        <div class="tooltip" title="{{ $account->accountName }}">{{ str_limit($account->accountName,23) }}</div>
                                    @endif
                                </td>

                                <td id= {{ $account->fkAccountType }}>{{ $account->accountType->name }}</td>
                                <td id= {{ $account->fkBrandId }}>
                                    @isset($account->client->name)
                                        @if(strlen($account->client->name) < 23 )
                                            <div class="tooltip" >{{ $account->client->name }}</div>
                                        @else
                                            <div class="tooltip" title="{{ $account->client->name }}">{{ str_limit($account->client->name,23) }}</div>
                                        @endif
                                    @endisset
                                </td>
                                <td>
                                    {{  date("Y-m-d",strtotime($account->created_at)) }}
                                </td>

                                <td class="text-center">
                                    <i class="fa fa-trash"></i>
{{--                                    <i class="fa fa-edit"> </i>--}}
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
                                <h5 class="modal-title" id="exampleModalLongTitle">Add Account</h5>
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
                                                      action="{{ route('manage.account') }}" action_type="1"
                                                      enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="form-group ">
                                                        <label for="Client">Select Brand</label>
                                                        <select class=" form-control" name="clientId" id="clientId">
                                                            <option value="" disabled selected>Choose...</option>
                                                         
                                                            @isset($clients)
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
                                                        <label for="amsProfile">Select AMS Profile</label>
                                                        <select class=" form-control" {{ isset($amsProfile[0])? "" : "disabled" }} name="amsProfile" id="amsProfile"
                                                                multiple>
                                                                
                                                            <option value="" disabled>Choose...</option>
                                                            @isset($amsProfile[0])
                                                            {{ print_r("working") }}
                                                                @foreach ($amsProfile as $profile)
                                                                    <option class="text-capitalize"
                                                                            value="{{ $profile->id }}">{{ $profile->name }}
                                                                    </option>
                                                                @endforeach
                                                            @endisset
                                                        </select>
                                                    </div>
                                                    <div class="form-group ">
                                                        <label for="mwsSeller">Select MWS Seller</label>
                                                        <select class=" form-control" {{ isset($mwsSeller[0])? "" : "disabled" }} name="sellerId" id="sellerId"
                                                                multiple>
                                                            <option value="" disabled>Choose...</option>
                                                            @isset($mwsSeller[0])
                                                                @foreach ($mwsSeller as $Seller)
                                                                    <option class="text-capitalize"
                                                                        
                                                                            value="{{ $Seller->mws_config_id }}">{{ $Seller->merchant_name }}
                                                                    </option>
                                                                @endforeach
                                                            @endisset
                                                        </select>
                                                    </div>
                                                    <div class="form-group ">
                                                        <label for="Vendor">Select Vendor</label>
                                                        <select class=" form-control " {{ isset($vcVendor[0]) ? "" : "disabled" }} " name="vendorId" id="vendorId"
                                                                multiple>
                                                            <option value="" disabled>Choose...</option>
                                                            @isset($vcVendor[0])
                                                                @foreach ($vcVendor as $vendor)
                                                                    <option class="text-capitalize"
                                                                            value="{{ $vendor->vendor_id }}">{{ $vendor->vendor_name }}
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
