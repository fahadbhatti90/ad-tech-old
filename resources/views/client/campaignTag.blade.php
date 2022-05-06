@extends('layout.appclient')
@extends('inc.side_menu')
@extends('inc.nav_bar')
@extends('modal.logout')
@section('title', isset($pageTitle)?$pageTitle:'Dashboard')

@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/tooltipster/dist/css/tooltipster.bundle.min.css')}}" />
    <link href="{{asset('public/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('public/vendor/datatables/responsive.dataTables.min.css')}}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{ asset('public/css/client/tagManager.css?'.time())  }}">
    <link rel="stylesheet" href="{{ asset('public/css/client/campaignTagging.css?'.time())  }}">
@endpush


@section('content')
        <!-- /.container-fluid -->
        <!-- Begin Page Content -->
        <div class="container-fluid clientDasboard">
            <!-- Begin Breadcrumb -->
            {{-- {{ Breadcrumbs::render('CampaignTagging') }} --}}
            <!-- End Breadcrumb -->
            <div class="row productTable" action-add-tag="">
                <div class="col-xl-12 col-md-12 mb-4">
                    <div class="card mb-4 shadow">
                        <div class="card-header py-3" style="display: flex;">
                            <h6 class="m-0 font-weight-bold text-primary" style="flex-basis:70%">
                                 Campaign Tag Manager
                            </h6>
                        </div>
                        <div class="card-body schedulingCardBody">
                                <table id="dataTable" class="table table-bordered  cronListTable" style="width:100%" un-assign-single-tag="{{ route("campaign.tag.single.unassign") }}">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <div class="selectContainer"><div class="checkboxMiniContainer"><span><i class="fas fa-check"></i></span></div></div>
                                                    Sr. #
                                                </th>
                                                <th class="d-none">Campaign Id</th>
                                                <th>Name</th>
                                                <th>Child Brand Name</th>
                                                {{-- <th>Created At</th> --}}
                                                <th>Tag</th>
                                                {{-- <th class="text-center">Action</th> --}}
                                            </tr>
                                        </thead>
                                        <tbody class="allASINS">
                                        
                                        </tbody>
                                        </tbody>
                                </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    @include('client.partials.tagPopup')
@endsection
@push('js')
    <script type="text/javascript" src="{{ asset('public/tooltipster/dist/js/tooltipster.bundle.min.js')}}"></script>
    <script src="{{asset('public/vendor/datatables/jquery.dataTables.min.js')}}"></script> 
    <script src="{{asset('public/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{asset('public/vendor/datatables/dataTables.responsive.min.js')}}"></script>
    <script src="{{asset('public/js/materialize.min.js')}}"></script>
    <script src="{{ asset('public/js/compainTagging/campaignTagging.js?'.time()) }}"></script>
    <script src="{{ asset('public/js/compainTagging/campaignTaggingSingleTagUnAssign.js?'.time()) }}"></script>
@endpush