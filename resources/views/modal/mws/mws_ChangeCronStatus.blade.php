@section('mws_ChangeCronStatus')


    {{--delete modal--}}
    <div class="modal fade" id="change-confirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    Are you sure?
                </div>
                <div class="modal-body confirm-text">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-danger btn-ok">Delete</a>
                </div>
            </div>
        </div>
    </div>
    {{--delete modal--}}


@endsection