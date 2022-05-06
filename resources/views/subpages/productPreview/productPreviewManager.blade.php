@extends('layout.appclient')
@extends('inc.side_menu')
@extends('inc.nav_bar')
@extends('modal.logout')
@section('title', isset($pageTitle)?$pageTitle:'')
@section('content')

@push('css')
<link rel="stylesheet" type="text/css" href="{{ asset('public/tooltipster/dist/css/tooltipster.bundle.min.css')}}" />
<link href="{{asset('public/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet" type="text/css">
<link href="{{asset('public/vendor/datatables/responsive.dataTables.min.css')}}" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="{{ asset('public/vendor/select/dist/css/bootstrap-select.min.css') }}">
    <link rel="stylesheet" href="{{asset('public/css/scraping_custom_style.css?'.time())  }}">
    <link rel="stylesheet" href="{{asset('public/css/client/productPreviewManager.css?'.time())  }}">
@endpush

@push('daterangepickercss')
    <link rel="stylesheet" href="{{ asset('public/vendor/daterangepicker/daterangepicker.css') }}">
    
@endpush

@push('daterangepickerjs')
<script src="{{ asset('public/vendor/daterangepicker/moment.min.js') }}"></script>
<script src="{{ asset('public/vendor/daterangepicker/daterangepicker.js') }}"></script>
@endpush
@push('js')
    <script type="text/javascript" src="{{ asset('public/tooltipster/dist/js/tooltipster.bundle.min.js')}}"></script>
    <script src="{{asset('public/vendor/autosize/autosize.min.js')}}"></script>
    <script src="{{asset('public/vendor/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('public/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{asset('public/vendor/datatables/dataTables.responsive.min.js')}}"></script>
    <script src="{{ asset('public/js/productPreview/productPreview.js?'.time()) }}"></script>
@endpush

    <!-- Begin Page Content -->
    <div class="container-fluid">
        
            @include('partials.formPreloader')
        <!-- Begin Breadcrumb -->
        {{-- {{ Breadcrumbs::render('clientProductPreview') }} --}}
        <!-- End Breadcrumb -->
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4 topActionsContainer ">
            <button type="button" class="btn btn-primary addSchedule  sc-move-right" data-toggle="modal" data-target="#addProductPreviewModel">
                Create Events
            </button>
        </div>
        <div class="card mb-4 shadow">
            <div class="card-header py-3" style="display: flex;">
                <h6 class="m-0 font-weight-bold text-primary" style="flex-basis:70%">
                    Manage Events
                </h6>
            </div>
            <div class="card-body schedulingCardBody">
                    <table id="dataTable" class="table table-bordered  cronListTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Sr. #</th>
                                    <th>Child Brand</th>
                                    <th>ASIN</th>
                                    {{-- <th>Product Preview Type</th> --}}
                                    <th>Event</th>
                                    <th>Notes</th>
                                    <th>Occurrence Date</th>
                                    <th>Created Date</th>
                                    {{-- <th class="text-center">Action</th> --}}
                                </tr>
                            </thead>
                            <tbody class="cronList">
                                @php
                                    $sr = 0;
                                @endphp
                                @isset($productPreviews)
                                    @if(count($productPreviews) > 0)
                                        @foreach($productPreviews as $key => $productPreview)
                                            <tr id="{{ $productPreview->id }}">
                                            
                                                <td> {{ ++$sr }} </td>
                                                <td class="text-capitalize">
                                                    @php
                                                            $productTitle = str_limit(
                                                        str_replace('','_',$productPreview->account != null?$productPreview->account->accountName:"")
                                                        ,18);
                                                        $fullProductTitle = $productPreview->account != null?$productPreview->account->accountName:"";
                                                        $fullProductTitle = (($productPreview->brand_alias != null && count($productPreview->brand_alias) > 0) ? ($productPreview->brand_alias[0]->overrideLabel == null ? $fullProductTitle : $productPreview->brand_alias[0]->overrideLabel) : $fullProductTitle);
                                                        @endphp
                                                    <div  class="tooltip" title="{{str_replace('','_',$fullProductTitle)}}">
                                                        
                                                        {{(($productPreview->brand_alias != null && count($productPreview->brand_alias) > 0) ? ($productPreview->brand_alias[0]->overrideLabel == null ? $productTitle : $productPreview->brand_alias[0]->overrideLabel) : $productTitle)}}
                                                    </div>
                                                </td>
                                                <td  class="text-capitalize">{{ $productPreview->asin }}</td>
                                                {{-- <td class="text-capitalize">{{  ($productPreview->ppt->typeTitle)}}</td> --}}
                                                <td class="text-capitalize">
                                                    @if  ($productPreview->fkProductPreviewTypeId == 1)
                                                        {{ $productPreview->actions->actionName }}
                                                    @else
                                                        {{ $productPreview->events->eventName }}
                                                    @endif
                                                </td>
                                                <td class="text-capitalize">
                                                    <div  class="tooltip" title="{{ ($productPreview->notes) }}">
                                                        {{ str_limit($productPreview->notes, 18) }}
                                                    </div>
                                                </td>
                                                <td class="text-capitalize">{{  ($productPreview->occurrenceDate)}}</td>
                                                <td class="text-capitalize">{{  ($productPreview->createdAt)}}</td>
                                                {{-- <td class="text-center">
                                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                                </td> --}}
                                            </tr>
                                        @endforeach
                                    @endif
                                @endisset
                            </tbody>
                            </tbody>
                           
                    </table>
                    <div class="modal fade" id="addProductPreviewModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                            <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLongTitle">Create Events</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                            </div>
                            <div class="modal-body">
                                
                           @include('partials.graphsPreloader')
                                    <div class="row">
                                            <div class="col-12">
                                                <div class=" mt-0">
                                                    <div class="col-xl-12 input-group mb-3">
                                                        <form  id="addProductPreviewForm" method="POST" action="{{ route('manage.notes') }}" action_type="1">
                                                            @csrf 
                                                            <div class="form-group">
                                                                <label for="accountType">Child Brand</label>
                                                                <select class="form-control  onDropDownChange" name="accountType" id="accountType">
                                                                    <option value="" selected>Please Select Child Brand</option>
                                                                    @isset($accounts)
                                                                        @foreach ($accounts as $account)
                                                                        <option value="{{$account->attr1}}">{{(($account->brand_alias != null && count($account->brand_alias) > 0) ? ($account->brand_alias[0]->overrideLabel == null ? $account->attr2 : $account->brand_alias[0]->overrideLabel) : $account->attr2)}}</option>
                                                                        @endforeach
                                                                    @endisset
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="marketPlaceId">Marketplace Id</label>
                                                                <select class="form-control" name="marketPlaceId" id="marketPlaceId">
                                                                    <option value="" selected>Please Select Marketplace Id</option>
                                                                    <option value="US" >US</option>
                                                                </select>
                                                            </div>
                                                            <div class="form-group dropDownContainer">
                                                                <label for="asinCustom">ASIN</label>
                                                                <input class="form-control" name="asinCustom" customValue="" placeholder="Please select an ASIN" id="asinCustom" >
                                                                <div class="mainDropDown">
                                                                    <div class="dropdown-menu open show" role="combobox" x-placement="bottom-start" style="max-height: 588.4px; overflow: hidden; min-height: 0px;border-radius: 0 !important; position: absolute; will-change: transform; left: 0px;  box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14), 0 3px 1px -2px rgba(0, 0, 0, 0.12), 0 1px 5px 0 rgba(0, 0, 0, 0.2); padding:0">
                                                                        {{-- <div class="bs-searchbox">
                                                                            <input type="text" class="form-control searchbar" autocomplete="off" role="textbox" aria-label="Search">
                                                                        </div> --}}
                                                                        <div class="inner open" role="listbox" aria-expanded="true" tabindex="-1" style="">
                                                                        <ul class="dropdown-menu inner ">
                                                                            <li asin = "" class="options NoResult">
                                                                                <span class="text">No record found</span>
                                                                            </li>
                                                                            {{-- @isset($asins)
                                                                                @foreach ($asins as $asin)
                                                                                    <li asin = "{{ $asin->attr1 }}" class="options">
                                                                                         <span class="text">{{ $asin->attr1."=>".str_limit($asin->attr2, 70) }}</span>
                                                                                    </li>
                                                                                @endforeach
                                                                            @endisset --}}
                                                                           
                                                                        </ul>
                                                                    </div>
                                                                </div>

                                                                </div>
                                                            </div>
                                                            <div class="form-group"> 
                                                                <label for="">Date Range</label>
                                                                <input type="text"  placeholder="Please Select Date Range" data-date-format="yyyy-mm-dd" data-date-end-date="0d" class="form-control datepicker" name="occurrenceDate" id="occurrenceDate" />
                                                                {{-- <i class="fa fa-calendar "></i> --}}
                                                                <i class="far fa-calendar-alt calendarIcon"></i>
                                                            </div>
                                                            <div  class="form-group row">
                                                                <div class="col-lg-6">
                                                                    <fieldset class="userActions">
                                                                        <legend>Events</legend>
                                                                        @isset($userActions)
                                                                            @foreach ($userActions as $userAction)
                                                                                    <div class="custom-control custom-checkbox userActions"  >
                                                                                        <div class="custonTooltip">
                                                                                            <div id="userActionNotes{{ $userAction->id }}">
                                                                                                <label for="">Add Description</label>
                                                                                                <textarea class="form-control userActionsTextAreas" name="notes"  rows="3" cols="30"></textarea>
                                                                                                <span class="notesControls yesControl"><i class="fas fa-check"></i></span>
                                                                                                <span class="notesControls noControl"><i class="fas fa-times"></i></span>
                                                                                            </div>
                                                                                        </div>
                                                                                        <input type="checkbox" value="1|{{ $userAction->id }}" name="checkPoint[]" class="custom-control-input " id="userAction{{ $userAction->id }}">
                                                                                        <label class="custom-control-label" for="userAction{{ $userAction->id }}">{{ $userAction->actionName }}</label>
                                                                                    </div>
                                                                            @endforeach
                                                                        @endisset
                                                                        <br>
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col-lg-6"> 
                                                                    <fieldset class="events">
                                                                        <legend>Events</legend>
                                                                        @isset($events)
                                                                            @foreach ($events as $event)
                                                                                    <div class="custom-control custom-checkbox events" data-tooltip-content="#EventsNotes{{ $event->id }}" >
                                                                                        <div class="custonTooltip">
                                                                                            <div id="EventsNotes{{ $event->id }}">
                                                                                                <label for="">Add Description</label>
                                                                                                <textarea class="form-control eventsTextAreas" rows="3" cols="30"></textarea>
                                                                                                <span class="notesControls yesControl"><i class="fas fa-check"></i></span>
                                                                                                <span class="notesControls noControl"><i class="fas fa-times"></i></span>
                                                                                            </div>
                                                                                        </div>
                                                                                        <input type="checkbox" value="2|{{ $event->id }}" name="checkPoint[]" class="custom-control-input" id="event{{ $event->id }}" >
                                                                                        <label class="custom-control-label" for="event{{ $event->id }}">{{ $event->eventName }}</label>
                                                                                        
                                                                                    </div>
                                                                            @endforeach
                                                                        @endisset
                                                                    </fieldset>
                                                                </div>
                                                            </div>  
                                                           
                                                            
                                                             <div class="modal-footer">
                                                                <button type="button" class="btn btn-close " data-dismiss="modal">Close</button>
                                                                <button class="btn  waves-effect waves-light asinScheduleSubmitButton" type="submit">Save</button>
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
@endsection
