@extends('layout.app')
@extends('inc.side_menu')
@extends('inc.nav_bar')
@extends('modal.logout')
@section('title', isset($pageTitle)?$pageTitle:'')
@section('content')


@push('css')
<link rel="stylesheet" type="text/css" href="{{ asset('public/tooltipster/dist/css/tooltipster.bundle.min.css')}}" />
<link href="{{asset('public/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{asset('public/css/scraping_custom_style.css?'.time())  }}">
@endpush
@push('js')
<script type="text/javascript" src="{{ asset('public/tooltipster/dist/js/tooltipster.bundle.min.js')}}"></script>
<script src="{{asset('public/vendor/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('public/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/es6-shim/0.35.3/es6-shim.min.js"></script>
    <script src="{{ asset('public/js/searchRank/searchRankCrawler.js?'.time()) }}"></script>
@endpush




    <!-- Begin Page Content -->
    <div class="container-fluid">
        
            @include('partials.formPreloader')
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <form id="scheduleTimeForm" method="POST" action="{{ route('SetSerachRankSchedulingTime') }}" >
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
                <button type="button" class="btn btn-primary addSchedule sc-move-right" data-toggle="modal" data-target="#addScheduleFormModel">
                     Add Schedule
                </button>
        </div>
        <div class="card mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Manage Search Rank</h6>
            </div>
            <div class="card-body searchRankCardBody">

                    <table id="asinCronTable" class="table table-striped table-bordered cronListTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Sr. #</th>
                                    <th>Name</th>
                                    <th>Department</th>
                                    <th>Next Run Date</th>
                                    <th>Running Status</th>
                                    <th>Created At</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody class="cronList">
                                    @foreach($crawlers as  $key => $crawler)
                                    <tr id="{{ $crawler->id }}">
                                        <td> {{ $key+1 }} </td>
                                        <td class="text-capitalize">

                                            <div class="tooltip" title="{{ $crawler->c_name."_".$crawler->id}}">
                                                     
                                                       
                                            {{ str_limit($crawler->c_name."_".$crawler->id,18) }}
                                                    
        
                                            </div>
                                        </td>
                                        <td class="text-capitalize">{{ $crawler->department->d_name }}</td>
                                        <td>{{ $crawler->c_nextRun }}</td>
                                        <td>{{ $crawler->isRunning?"Running":"Pending" }}</td>
                                        <td>{{  date("Y-m-d",strtotime($crawler->created_at))}}</td>
                                        <td class="text-center"><i class="fa fa-trash"></i> </td>
                                    </tr>
                                @endforeach
                              
                            </tbody>
                           
                    </table>

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
                                                            <form  id="searchRankFrom" method="POST" action="{{ route('addSearchRankCrawler') }}" action_type="1" enctype="multipart/form-data">
                                                                @csrf
                            
                                                                {{-- Crawl Name --}}
                                                                <div class="form-group ">
                                                                    <label for="crawlName">Crawler Name</label>
                                                                    <input type="text" name="crawlName" class="form-control" placeholder="Crawler Name" id="crawlName">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="searchTerm">Search Term</label>
                                                                    <div class="custom-file">
                                                                        <input type="file" class="custom-file-input" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"   id="searchTerm" name="searchTerm">
                                                                        <label class="custom-file-label" for="searchTerm">Choose File</label>
                                                                    </div>
                                                                </div>
                                                                {{-- Department --}}
                                                                <div class="form-group ">
                                                                    <label for="department">Select Department</label>
                                                                    <select class=" form-control" name="department" id="department">
                                                                            <option value=""  selected>Choose...</option>
                                                                            @if (isset($departments))
                                                                                @foreach ($departments as $department)
                                                                                    <option value="{{ $department->id }}">{{ $department->d_name }}
                                                                                    </option>
                                                                                @endforeach
                                                                            @endif
                                                                    </select>
                                                                </div>
                                                                {{-- Frequancy --}}
                                                                <div class="form-group">
                                                                    <label for="frequancy">Frequency</label>
                                                                    <select class=" form-control" name="frequancy" id="frequancy">
                                                                            <option value=""  selected>Choose...</option>
                                                                            <option value="1">Daily</option>
                                                                            <option value="7">1 week</option>
                                                                            <option value="14">2 week</option>
                                                                            <option value="21">3 week</option>
                                                                            <option value="30">1 month</option>
                                                                    </select>
                                                                </div>
                                                              
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
                       

            </div>
        </div>

    </div>
    <!-- /.container-fluid -->
    {{-- <script src="{{asset('public/js/ams_scripts/profile.js')}}"></script> --}}
@endsection
