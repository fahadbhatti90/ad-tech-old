@section('mws_DeleteConfirm_modal')


    {{--delete modal--}}
    <div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
    <script>
        $('#confirm-delete').on('show.bs.modal', function(e) {
           // alert($(e.relatedTarget).data('confirmText'));
            //$(e.relatedTarget).data('confirmText');
            //alert($(e.relatedTarget).data('confirmText'));

           // return false;
            $(this).find('.confirm-text').text($(e.relatedTarget).data('text'));
            $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
        });



    </script>

@endsection