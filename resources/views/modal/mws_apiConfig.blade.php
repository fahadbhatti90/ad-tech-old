@section('mws_apiConfig_modal')
    <!-- Modal -->
    <div class="modal fade" id="mwsapiconfigModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add API Parameters</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                   {{-- <div class="ajax-flash-message" >
                            <div class="alert alert-success alert-block" role="alert">
                                <button class="close" data-dismiss="alert"></button>

                            </div>

                    </div>--}}
                    <form class="user" name="add_api_config" id="add_api_config" action="{{url('/mws/addConfig')}}" method="POST">
                        @csrf
                        <div class="form-group row">
                            <div class="col-sm-12 mb-6 mb-sm-0">
                                <label for="exampleInputEmail1">Seller Name</label>
                                <input type="text"
                                       name="merchant_name"
                                       id="merchant_name"
                                       class="form-control form-control-user"
                                       placeholder="Seller Name" required>
                            </div>
                            <div class="col-sm-12 mt-3">
                                <label for="exampleInputEmail1">Seller Id</label>
                                <input type="text"
                                       class="form-control form-control-user"
                                       name="seller_id" id="seller_id"
                                       placeholder="A3MSDFDSF43565" required>
                            </div>


                            <div class="col-sm-12 mt-3">
                                <label for="exampleInputEmail1">Access Key Id</label>
                                <input type="text"
                                       class="form-control form-control-user"
                                       name="mws_access_key_id" id="mws_access_key_id"
                                       placeholder="CGFAT4VW7BAGJLM72M5B" required>
                            </div>
                            <div class="col-sm-12 mt-3">
                                <label for="exampleInputEmail1">Auth Token</label>
                                <input type="text"
                                       class="form-control form-control-user"
                                       name="mws_authtoken" id="aws_access_key_id"
                                       placeholder="amzn.mws.19j14cdh-gccg-2049-6d4h-5f5mf07f4ccd" required>
                            </div>
                            <div class="col-sm-12 mt-3">
                                <label for="exampleInputEmail1">Secret Key</label>
                                <input type="text"
                                       class="form-control form-control-user"
                                       name="mws_secret_key" id="mws_secret_key"
                                       placeholder="MTHCHIVdnk/THQXkjxll2KxiI5eTXrHHu8RcEsKm" required>
                            </div>
                           {{-- <div class="col-sm-12 mt-3">
                                <input type="text"
                                        class="form-control form-control-user"
                                        name="marketplace_id" id="marketplace_id"
                                        placeholder="Marketplace id" required>

                            </div>--}}
                           {{-- <div class="col-sm-12 mt-3">
                                <input id="timepicker"
                                       class="form-control form-control-user"
                                       name="timepicker"
                                        required>

                            </div>--}}
                            {{--<div class="col-sm-12 mt-3">
                                <select name="exampleFormControlSelect2" multiple class="form-control" id="exampleFormControlSelect2">
                                    <option>1</option>
                                    <option>2</option>
                                    <option>3</option>
                                    <option>4</option>
                                    <option>5</option>
                                </select>

                            </div>--}}

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" id="submit_config_btn" name="submit_config_btn" class="btn btn-primary">Add</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="{{asset('public/js/mws_scripts/mws_apiConfig.js?'.time())}}"></script>
@endsection