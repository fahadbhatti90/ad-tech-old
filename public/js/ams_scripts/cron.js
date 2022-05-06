document.addEventListener('DOMContentLoaded', function (e) {
    const cronFrom = document.getElementById('cronFrom');
    const fv = FormValidation.formValidation(cronFrom, {
            fields: {
                crontype: {
                    validators: {
                        notEmpty: {
                            message: 'The cron type is required'
                        }
                    }
                },
                crontime: {
                    validators: {
                        notEmpty: {
                            message: 'The cron time is required'
                        }
                    }
                },
                cronstatus: {
                    validators: {
                        notEmpty: {
                            message: 'The Cron Status is required'
                        }
                    }
                },
            },
            plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                bootstrap: new FormValidation.plugins.Bootstrap(),
                submitButton: new FormValidation.plugins.SubmitButton(),
            },
        }
    ).on('core.form.valid', function () {
        var formData = new FormData();
        var params = $('#cronFrom').serializeArray();
        var timer = 3000;
        // Get simple form data
        $.each(params, function (i, val) {
            formData.append(val.name, val.value);
        });
        axios.post(base_url + '/ams/cronCall', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        }).then(function (response) {
            // Check response your status
            if (response.data.status == 'fail') {
                $errorMessage = '';
                if (response.data.message.crontype) {
                    $errorMessage = response.data.message.crontype + '<br/>';
                }
                if (response.data.message.crontime) {
                    $errorMessage += response.data.message.crontime + '<br/>';
                }
                if (response.data.message.cronstatus) {
                    $errorMessage += response.data.message.cronstatus + '<br/>';
                }
                Swal.fire({
                    title: '<strong>' + response.data.title + '</strong>',
                    type: 'info',
                    html:
                    $errorMessage,
                    showCloseButton: true,
                    showCancelButton: true,
                    focusConfirm: false,
                    confirmButtonText:
                        '<i class="fa fa-thumbs-up"></i> Great!',
                    confirmButtonAriaLabel: 'Thumbs up, great!',
                    cancelButtonText:
                        '<i class="fa fa-thumbs-down"></i>',
                    cancelButtonAriaLabel: 'Thumbs down',
                })
            } else {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: timer
                });
                Toast.fire({
                    type: 'success',
                    title: response.data.title
                });
                setTimeout(function () {
                    location.reload();
                    document.getElementById("cronFrom").reset();
                }, timer / 2);
            }
        });
    });
});