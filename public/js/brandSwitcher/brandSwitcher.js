$(function () {
    // delete modal ajax call
    $('#brandSwitcherForm').on('submit', function (e) {
        e.preventDefault();
        var url = $(this).data('action');
        $.ajax({
            type: "POST",
            url: url,
            data: $(this).serializeArray(),
            success: function (response) {
                // Check response your status
                if (response.status == 'fail') {
                    $errorMessage = '';
                    Swal.fire({
                        title: '<strong>' + response.title + '</strong>',
                        type: 'info',
                        html:
                        response.message,
                        showCloseButton: true,
                        showCancelButton: true,
                        focusConfirm: false,
                        confirmButtonText:
                            '<i class="fa fa-thumbs-up"></i> Great!',
                        confirmButtonAriaLabel: 'Thumbs up, great!',
                        cancelButtonText:
                            '<i class="fa fa-thumbs-down"></i>',
                        cancelButtonAriaLabel: 'Thumbs down',
                    });
                    setTimeout(function () {
                        location.reload();
                    }, 2000);
                } else {
                    //
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000
                    });

                    Toast.fire({
                        type: 'success',
                        title: response.message
                    });
                }
                setTimeout(function () {
                    location.reload();
                }, 2000);
            },
            error: function (e) {
                if (e.responseText.includes("Unauthenticed")) {
                    location.reload();
                }
            }
        });
    });
});