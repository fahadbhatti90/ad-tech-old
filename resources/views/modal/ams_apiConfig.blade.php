@section('ams_apiConfig_modal')
    <!-- Modal -->
    <div class="modal fade" id="amsKeysModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add API Parameter</h5>
                    <button type="button" class="close amscloseForm" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="apiConfigForm" class="user" method="POST">
                        @csrf
                        <div class="form-group row">

                            @if($errors->has('client_id') && $errors->has('client_secret'))
                                <ul class="alert alert-danger col-sm-12" role="alert">
                                    @foreach ($errors->all() as $message)
                                        <li>{{$message}}</li>
                                    @endforeach
                                </ul>
                            @endif
                            <div class="col-sm-12 mb-6 mb-sm-0">
                                <label for="exampleInputEmail1">Client ID</label>
                                <input type="text"
                                       name="client_id"
                                       class="form-control form-control-user"
                                       placeholder="amzn1.application-oa2-client.a8358a60…" required>
                            </div>
                            <div class="col-sm-12 mt-3">
                                <label for="exampleInputEmail1">Client Secret Key</label>
                                <input type="text"
                                       class="form-control form-control-user"
                                       name="client_secret"
                                       placeholder="208257577110975193121591895857093449424" required>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary amscloseForm" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @if($errors->has('client_id') && $errors->has('client_secret'))
        <script>
            $('#amsKeysModal').modal('show');
        </script>
    @endif
    <script src="{{asset('public/js/ams_scripts/ams_apiConfig.js?'.time())}}"></script>
@endsection
