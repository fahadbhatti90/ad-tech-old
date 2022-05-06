@section('mws_apiConfig_modal')
    <!-- Modal -->
    <div class="modal fade" id="mwsAddCronModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Cron Job</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="user" name="add_cron_from" id="add_cron_from" action="{{url('/mws/addCron')}}" method="POST">
                        @csrf
                        <div class="form-group row">
                            <div class="col-sm-12 mb-6 mb-sm-0">
                                <input type="text"
                                       name="title"
                                       id="title"
                                       class="form-control form-control-user"
                                       placeholder="Title" required>
                            </div>

                            <div class="col-sm-12 mt-3">
                                {{--<select class="form-control form-control-user" required>
                                    <option  class="form-control form-control-user" value="test" selected>
                                        test
                                    </option>
                                </select>--}}
                               {{-- <select  class="form-control" id="report_type" name="report_type">

                                    <option value="">Select Report Type</option>
                                    <option value="_GET_MERCHANT_LISTINGS_DATA_">Active Listings Report</option>
                                    <option value="_GET_MERCHANT_LISTINGS_INACTIVE_DATA_">Inactive Listings Report</option>
                                    <option value="_GET_FBA_FULFILLMENT_CUSTOMER_RETURNS_DATA_">FBA Returns Report</option>
                                    <option value="_GET_FLAT_FILE_RETURNS_DATA_BY_RETURN_DATE_">Flat File Returns Report by Return Date</option>
                                    <option value="_GET_FLAT_FILE_ALL_ORDERS_DATA_BY_LAST_UPDATE_">Flat File All Orders Report by Last Update</option>
                                    <option value="_GET_FLAT_FILE_ALL_ORDERS_DATA_BY_ORDER_DATE_">Flat File All Orders Report by Order Date</option>
                                    <option value="_GET_FBA_FULFILLMENT_INVENTORY_HEALTH_DATA_">FBA Inventory Health Report</option>

                                    <option value="_GET_FBA_FULFILLMENT_INVENTORY_RECEIPTS_DATA_">FBA Received Inventory Report</option>
                                    <option value="_GET_RESTOCK_INVENTORY_RECOMMENDATIONS_REPORT_">Restock Inventory Report</option>
                                </select>--}}

                                <select  class="form-control" id="report_type" name="report_type">

                                    <option value="">Select Report Type</option>
                                    <option value="Catalog">Catalog</option>
                                    <option value="Inventory">Inventory</option>
                                    <option value="Sales">Sales</option>
                                </select>

                            </div>

                            <div class="col-sm-12 mt-3">
                                {{--<input id="timepicker"
                                       class="form-control form-control-user"
                                       name="timepicker" placeholder="Select Time"
                                       required>--}}
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
                            <button type="submit" id="submit_cron_btn" name="submit_cron_btn" class="btn btn-primary">Add</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script type="application/javascript">


    </script>
    <script src="{{asset('public/js/mws_scripts/mws_AddCron.js?'.time())}}"></script>

@endsection