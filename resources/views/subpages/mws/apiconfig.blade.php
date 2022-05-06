@extends('layout.app')
@extends('inc.side_menu')
@extends('inc.nav_bar')
@extends('modal.logout')
@extends('modal.mws_apiConfig')
@extends('modal.mws.mws_editApiConfig')
@extends('modal.mws.mws_DeleteConfirm')
@section('title',$pageTitle)
@section('content')
    @push('styles')
        <link href="{{asset('public/css/scraping_custom_style.css')}}" rel="stylesheet" type="text/css">
    @endpush
    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
           {{-- <h1 class="h3 mb-0 text-gray-800">{{isset($pageHeading)?$pageHeading:''}}</h1>--}}
            {{-- @if(!isset($api_parameter->seller_id))--}}

            <button  style="margin-left: auto;" type="button" class="btn btn-primary sc-move-right" data-toggle="modal" data-target="#mwsapiconfigModal">
                Add API Parameter
            </button>
            {{-- @endif--}}
        </div>

        <div class="flash-message" > @if( Session::has("success") )
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

    <!-- Page Heading -->

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Sellers API Credentials</h6>
            </div>
            <style>
                .mws_config_table td{
                    max-width: 177px;
                }

            </style>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered mws_config_table" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>Seller Name</th>
                            <th>Seller Id</th>
                            <th>Access Key Id</th>
                            <th>Auth Token</th>
                            <th>Secret Key</th>
                           {{-- <th>Marketplace id</th>--}}
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if($api_parameter->count() > 0)
                        @foreach ($api_parameter as $api_parameter_value)
                            <tr>
                                <td>{{ $api_parameter_value->merchant_name }}</td>
                                <td>{{ $api_parameter_value->seller_id }}</td>
                                <td>{{ $api_parameter_value->mws_access_key_id }}</td>
                                <td>{{ $api_parameter_value->mws_authtoken }}</td>
                                <td >{{ $api_parameter_value->mws_secret_key }}</td>
                               {{-- <td>{{ $api_parameter_value->marketplace_id }}</td>--}}
                                {{--  <td>{{ $api_parameter_value->mws_config_id }}</td>--}}
                                <td>
                                    <a href="#mws_EditApiConfigModal" class="btn btn-success btn-circle btn-sm mws_auth"
                                       data-toggle="modal"
                                       data-book-id="my_id_value"
                                       data-api-config-id="{{ $api_parameter_value->mws_config_id }}"
                                       data-merchant-name="{{ $api_parameter_value->merchant_name }}"
                                       data-seller-id="{{ $api_parameter_value->seller_id }}"
                                       data-mws-access-key-id="{{ $api_parameter_value->mws_access_key_id }}"
                                       data-mws-authtoken="{{ $api_parameter_value->mws_authtoken }}"
                                       data-mws_secret-key="{{ $api_parameter_value->mws_secret_key }}"
                                       {{--data-marketplace-id="{{ $api_parameter_value->marketplace_id }}"--}}>
                                        <i class="fas fa-edit"></i>
                                    </a>


                                    <a class="btn btn-danger btn-circle btn-sm" href="javascript:void(0)" onclick="delete_config('{{ $api_parameter_value->mws_config_id }}','{{ csrf_token() }}')" {{--class="deleteConfig" data-id="{{ $api_parameter_value->mws_config_id }}" data-token="{{ csrf_token() }}"--}}><i class="fas fa-trash"></i></a>
                                    {{--<a class="btn btn-danger btn-circle btn-sm" href="#" data-href="deleteConfig/{{$api_parameter_value->mws_config_id}}" data-text="You won't be able to revert this!"  data-toggle="modal" data-target="#confirm-delete"><i class="fas fa-trash"></i></a>--}}
                                </td>
                            </tr>
                        @endforeach
                        @else
                            <tr>
                                <td colspan="7" align="center">No Record Found. </td> </tr>
                        @endif

                        </tbody>
                    </table>

                    @if($api_parameter->count() > 0)
                    <div class="col-md-12">
                    <div class="col-md-6 mr-auto">
                    Showing {{$api_parameter->firstItem()}} to {{$api_parameter->lastItem()}} of {{$api_parameter->total()}} entries
                    </div>
                        <div class="col-md-6 ml-auto">
                    {{ $api_parameter->links() }}
                        </div>
                    </div>
                    @endif
                </div>

            </div>

        </div>


    </div>

@endsection
