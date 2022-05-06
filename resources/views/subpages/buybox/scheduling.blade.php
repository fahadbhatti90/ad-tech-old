@extends('layout.app')
@extends('inc.side_menu')
@extends('inc.nav_bar')
@extends('modal.logout')
@section('title',$pageTitle)
@push('js')
    <script src="{{asset('public/vendor/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('public/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{asset('public/js/buybox_scripts/asinbatch.js?'.time())}}"></script>
 
@endpush
@push('styles')
{{-- <link href="{{asset('public/vendor/datatables/jquery.dataTables.min.css')}}" rel="stylesheet" type="text/css"> --}}
<link href="{{asset('public/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet" type="text/css">
<link href="{{asset('public/css/buybox/buybox.css?'.time())}}" rel="stylesheet" type="text/css">

@endpush
@section('content')
    <!-- Begin Page Content InLinePreloader.gif -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <button type="button" class="btn btn-primary addSchedule sc-move-right" data-toggle="modal" data-target="#addScheduleFormModel">
                      Add Schedule
                    </button>
        </div>
        <div class="card shadow mb-4">
            <!-- Card Header - Accordion -->
            <div class="d-block card-header py-3" >
                <h6 class="m-0 font-weight-bold text-primary">Scheduled BuyBox</h6>
            </div>
            <!-- Card Content - Collapse -->
            <div class="collapse show" id="collapseCardExample">
                <div class="card-body">
                    <table id="buyboxCronTable" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th >Sr. #</th>
                                <th >Email</th>
                                <th >Collection Name</th>
                                <th >Frequency</th>
                                <th >Duration</th>
                                <th >Next Run</th>
                                <th >Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $sr = 0;
                            @endphp
                                @foreach($CronListData as $single)
                                <tr id="{{ $single->id }}">
                                    <td>{{ ++$sr }}</td>
                                    <td>{{$single->email}}</td>
                                    <td class="text-capitalize">{{$single->cNameBuybox}}</td>
                                    <td class="text-center">{{$single->frequency}}</td>
                                    <td class="text-center">{{$single->duration}}</td>
                                    <td class="text-center">{{$single->nextRun}}</td>
                                    <td class="text-center">
                                        <button class="btn btn-danger btn-sm deleteCollection" data-id="{{ $single->id }}">
                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </tbody>
                       
                    </table>
                   
                </div>
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
                                        {{-- Get Profile List --}}
                                        <div class="col-xl-12 input-group">
                                            <form id="buyboxForm" method="POST">
                                                @csrf
                                                <div class="form-group row">
                                                    <label class="col-sm-6 col-form-label">Collection Name</label>
                                                    <div class="col-sm-12">
                                                            <div class="mr-sm-2 form-group">
                                                              
                                                            <input type="text"
                                                                   placeholder="Enter Collection Name"
                                                                   class="form-control"
                                                                   name="c_name_buybox"
                                                                   id="c_name_buybox">
                                                                </div>
                                                            </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-sm-6 col-form-label">Email Address</label>
                                                    <div class="col-sm-12">
                                                        <div class="mr-sm-2 form-group">
                                                            <input type="email"
                                                                   placeholder="Enter Email"
                                                                   class="form-control"
                                                                   name="buybox_email"
                                                                   id="buybox_email">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-sm-6 col-form-label">Choose file</label>
                                                    <div class="col-sm-12">
                                                        <div class="mr-sm-2 form-group">
                                                            <input type="file"
                                                                   class=" custom-file-input"
                                                                   name="asinfiles"
                                                                   id="asinfiles">
                                                            <label class="custom-file-label" for="asinfiles">Choose file</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-sm-6 col-form-label">Frequency</label>
                                                    <div class="col-sm-12">
                                                        <div class="mr-sm-2 form-group">
                                                            <select class="form-control" name="frequency" id="frequency">
                                                                <option value="" selected>Choose...</option>
                                                                <option value="1">One time in day</option>
                                                                {{-- <option value="2">Two times in day</option>
                                                                <option value="3">Three times in day</option>
                                                                <option value="4">Four times in day</option> 10-20-2019 FUNCTIONALITY ON HOLD --}}
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group row mt-0">
                                                    <label class="col-sm-6 col-form-label">Duration</label>
                                                    <div class="col-sm-12">
                                                        <div class="mr-sm-2 form-group">
                                                            <select class="form-control" name="duration" id="duration">
                                                                <option value="" selected>Choose...</option>
                                                                <option value="1">One Week</option>
                                                                <option value="2">Two Week</option>
                                                                <option value="3">Three Week</option>
                                                                <option value="4">Monthly</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                        </div>
                                    </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                          <button class="btn btn-primary buyboxSubmitButton" type="submit">Save</button>
                        </div>
                        </form>
                      </div>
                    </div>
                  </div>
        </div>
        

    </div>
    <!-- /.container-fluid -->
   
@endsection
