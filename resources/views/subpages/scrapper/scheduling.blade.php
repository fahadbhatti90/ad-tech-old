@extends('layout.app')
@extends('inc.side_menu')
@extends('inc.nav_bar')
@extends('modal.logout')
@section('title', isset($pageTitle)?$pageTitle:'')
@section('content')

@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/tooltipster/dist/css/tooltipster.bundle.min.css')}}" />
    <link href="{{asset('public/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('public/vendor/datatables/responsive.dataTables.min.css')}}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{asset('public/css/scraping_custom_style.css?'.time())  }}">
@endpush
@push('js')
    <script type="text/javascript" src="{{ asset('public/tooltipster/dist/js/tooltipster.bundle.min.js')}}"></script>
    <script src="{{asset('public/vendor/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('public/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{asset('public/vendor/datatables/dataTables.responsive.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/es6-shim/0.35.3/es6-shim.min.js"></script>
    <script src="{{ asset('public/js/scraper_scripts/schedulingFromValidation.js?'.time()) }}"></script>
    <script src="{{ asset('public/js/scraper_scripts/ScheduleASINUploadFromCustomVaildation.js?'.time()) }}"></script>
@endpush

    <!-- Begin Page Content -->
    <div class="container-fluid">
        
            @include('partials.formPreloader')
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4 topActionsContainer">
                <form id="scheduleTimeForm" method="POST" action="{{ route('SetSchedulingTime') }}" >
                        @csrf
                            @php
                                $time = strtotime('00:00');
                                $endTime = date("H:i A", strtotime('+30 minutes', $time));
                            @endphp
                                <select class="form-control"   name="scheduleTime" id="scheduleTime">
                                
                                 @for($i=0; $i<=18; $i++)
                                    @php
                                        if($i<10){
                                            $time_now_new ="0$i:00";
                                        }
                                        else{
                                            $time_now_new ="$i:00";
                                        }
                                    @endphp
                                     <option value="{{ $time_now_new }}|{{ $scheduleTime->id }}" 
                                         @if ($scheduleTime->value==$time_now_new)
                                             {{ 'selected = "selected"' }}
                                         @endif
                                     >
                                     {{$time_now_new}} 
                                 
                                 </option>
                                   
                                 @endfor
                             </select>
                </form>
                    <button type="button" class="btn btn-primary multipleButtons addSchedule  sc-move-right" data-toggle="modal" data-target="#addScheduleFormModel">
                       Add Schedule
                     </button>
                    {{-- <button type="button" class="btn btn-primary multipleButtons addCollection  sc-move-right" data-toggle="modal" data-target="#addCollectionFormModel">
                       Add Collection
                     </button> --}}
        </div>
        <div class="card mb-4">
            <div class="card-header py-3" style="display: flex;">
                <h6 class="m-0 font-weight-bold text-primary" style="flex-basis:70%">Manage Scheduling
                   
                        
                </h6>
                  {{-- Time --}}
              
            </div>
            <div class="card-body schedulingCardBody">
                 
                    <table id="asinCronTable" class="table table-striped table-bordered  cronListTable" style="width:100%">
                            <thead>
                                <tr>
                                    
                                    <th>Sr. #</th>
                                    <th>Name</th>
                                    <th>Duration</th>
                                    <th>Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody class="cronList">
                                @php
                                    $sr = 0;
                                @endphp
                                @foreach($crons as $key => $cron)
                                    <tr id="{{ $cron->id }}">
                                       
                                        <td> {{ ++$sr }} </td>
                                        <td class="text-capitalize">

                                            <div  class="tooltip" title="{{str_replace('','_',$cron->asin_collection->c_name)."_".$cron->asin_collection->id}}">
                                                {{ 
                                                str_limit(
                                                str_replace('','_',$cron->asin_collection->c_name)."_".$cron->asin_collection->id

                                                ,18 )
                                            }}

                                            </div>
                                        </td>
                                        <td>{{ $cron->cronDuration }}</td>
                                        <td class="text-capitalize">{{  ucwords($cron->cronStatus)}}</td>
                                        <td class="text-center">
                                                <i class="fa fa-trash" aria-hidden="true"></i>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            </tbody>
                           
                    </table>
                    <div class="tooltip_templates">
                            <span id="tooltip_content">
                                Proxies in proxy file should be formated like 
                                <br/>107.158.32.204:80<strong>;</strong>codeht:c0d3ht,
                                <br/>196.247.18.133:80<strong>;</strong>codeht:c0d3ht,
                                <br/>196.196.47.113:80<strong>;</strong>codeht:c0d3ht,
                            </span>
                        </div>
                    <div class="modal fade" id="addScheduleFormModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">Add Schedule</h5>
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
                                                        <form  id="schedulingFrom" method="POST" action="{{ route('AddSchedual') }}" action_type="1">
                                                            @csrf
                                                            {{-- Collection --}}
                                                            <div class="form-group ">
                                                                {{-- <label for="crontype">List</label> --}}
                                                                <select class=" form-control" name="crontype" id="crontype">
                                                                        <option value=""  selected>Choose List</option>
                                                                        @if (isset($asin_collection_list))
                                                                            @foreach ($asin_collection_list as $collection)
                                                                                <option value="{{ $collection->id }}">{{ str_replace(' ',"_",$collection->name)."_$collection->id"  }}
                                                                                </option>
                                                                            @endforeach
                                                                        @endif
                                                                </select>
                                                            </div>
                                                            
                                                            {{-- Duration --}}
                                                            <div class="form-group">
                                                                {{-- <label for="duration">Duration</label> --}}
                                                                <select class=" form-control" name="duration" id="duration">
                                                                        <option value=""  selected>Choose Duration</option>
                                                                        <option value="1w">1 week</option>
                                                                        <option value="2w">2 week</option>
                                                                        <option value="3w">3 week</option>
                                                                        <option value="1m">1 month</option>
                                                                </select>
                                                            </div>
                                                            
                                                            {{-- Status --}}
                                                            <div class="form-group ">
                                                                    {{-- <label class="">Status</label> --}}
                                                                    
                                                                    <div class="radio_lable">
                                                                        <div class="custom-control custom-radio">
                                                                            <input type="radio" class="custom-control-input" id="customRadio1" value="stop" checked name="cronstatus"
                                                                                >
                                                                            <label class="custom-control-label" for="customRadio1">Stop</label>
                                                                        </div>  
                                                                        <div class="custom-control custom-radio">
                                                                            <input type="radio" class="custom-control-input" id="customRadio2" value="run"  name="cronstatus"
                                                                            >
                                                                            <label class="custom-control-label" for="customRadio2">Run</label>
                                                                        </div>
                                                                    </div> 
                                                            </div>
                                                            <!-- Do NOT use name="submit" or id="submit" for the Submit button -->
                                                            
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        </div> 
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button class="btn btn-primary asinScheduleSubmitButton" type="submit">Save</button>
                            </div>
                            </form>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="addCollectionFormModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLongTitle">Add Schedule</h5>
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
                                                                <form action="{{ route('uploadFile') }}" id="assinScrapingForm" >
                                                                        @csrf
                                                                        
                                                                        <div class="form-group">
                                                                            {{-- <label for="excel">ASINs File</label> --}}
                                                                            <div class="custom-file">
                                                                                <input type="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"  class="custom-file-input" id="excel" name="excel">
                                                                                <label class="custom-file-label" for="excel"><span>Choose ASINs File</span></label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            {{-- <label for="coll-name">Collection Name</label> --}}
                                                                            <input type="text" class="form-control"
                                                                                id="coll-name" name="colectionName" placeholder="Collection Name">
                                                                        </div>
                                                                        
                                                                        <div class="form-group ">
                                                                            <div class="radio_lable">
                                                                                <div class="custom-control custom-radio">
                                                                                        <input type="radio" class="custom-control-input" id="customRadio" value="d"  name="daily-instant[]"
                                                                                        checked   >
                                                                                    <label class="custom-control-label" for="customRadio">Daily</label>
                                                                                </div>  
                                                                                <div class="custom-control custom-radio">
                                                                                    <input type="radio" class="custom-control-input" id="customRadio3" value="i" name="daily-instant[]"
                                                                                        >
                                                                                    <label class="custom-control-label" for="customRadio3">Instant</label>
                                                                                </div>
                                                                            </div> 
                                                                        </div> 
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> 
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button class="btn btn-primary collectionSubmitButton" type="submit">Save</button>
                                </div>
                                </form>
                                </div>
                            </div>
                        </div>
              
            </div>
        </div>

    </div>
    <!-- /.container-fluid -->
    {{-- <script src="{{asset('public/js/ams_scripts/profile.js')}}"></script> --}}
@endsection
