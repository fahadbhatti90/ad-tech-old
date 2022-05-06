@section('mws_editApiConfig_modal')
    <!-- Modal -->
   {{-- <div class="modal" id="my_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Modal header</h4>
                </div>
                <div class="modal-body">
                    <p>some content</p>
                    <input type="text" name="seller_id" value=""/>
                    <input type="text" name="bookId" value=""/>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>--}}
    <!-- Modal -->
    <div class="modal fade" id="mws_EditApiConfigModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit API Parameters</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="user" name="edit_api_config" id="edit_api_config" action="{{url('/mws/editConfig')}}" method="POST">
                        @csrf
                        <div class="form-group row">
                            <div class="col-sm-12 mb-6 mb-sm-0">
                                <label for="exampleInputEmail1">Seller Name</label>
                                <input type="hidden" id="api_config_id" name="api_config_id">
                                <input type="text"
                                       name="merchant_name"
                                       id="merchant_name"
                                       class="form-control form-control-user"
                                       placeholder="Seller Name" >
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
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" id="update_config_btn" name="update_config_btn" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>



    </script>
    <script src="{{asset('public/js/mws_scripts/mws_EditApiConfig.js?'.time())}}"></script>
@endsection