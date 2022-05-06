@section('mws_editApiConfig_modal')
    <!-- Modal -->
    <div class="modal fade" id="mwsEditCronModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Cron Job</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="user" name="edit_cron_from" id="edit_cron_from" action="{{url('/mws/editCron')}}" method="POST">
                        @csrf
                        <input type="hidden" id="cron_task_id" name="cron_task_id">
                        <div class="form-group row">
                            <div class="col-sm-12 mb-6 mb-sm-0">
                                <input type="text"
                                       name="title"
                                       id="title"
                                       class="form-control form-control-user"
                                       placeholder="Title" required>
                            </div>
                            {{--<div class="col-sm-12 mt-3">
                                <input type="text" id="cron_task_id"
                                       class="form-control form-control-user"
                                       name="cron_task_id" placeholder="Select Time"
                                       required>
                            </div>--}}

                            <div class="col-sm-12 mt-3">
                                {{--<select class="form-control form-control-user" required>
                                    <option  class="form-control form-control-user" value="test" selected>
                                        test
                                    </option>
                                </select>--}}
                                <select class="form-control" id="edit_report_type" name="report_type" disabled>
                                    <option value="">Select Report Type</option>
                                    <option value="Catalog">Catalog</option>
                                    <option value="Inventory">Inventory</option>
                                    <option value="Sales">Sales</option>
                                </select>
                            </div>
                            <input type="hidden" name="report_type_value" id="report_type_value" value="">

                            <div class="col-sm-12 mt-3">
                                <input type="time" id="cron_time"
                                       class="form-control form-control-user"
                                       name="cron_time" placeholder="Select Time"
                                       required>
                            </div>

                            {{--<div class="col-sm-12 mt-3">
                                <input type="text"
                                       class="form-control form-control-user"
                                       name="seller_id" id="seller_id"
                                       placeholder="Seller ID" required>
                            </div>--}}

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" id="edit_cron_btn" name="edit_cron_btn" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
   
    <script src="{{asset('public/js/mws_scripts/mws_EditCron.js?'.time())}}"></script>
@endsection