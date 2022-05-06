document.addEventListener('DOMContentLoaded', function (e) {

    const add_cron_from = document.getElementById('add_cron_from');
    const fv = FormValidation.formValidation(add_cron_from, {
            fields: {
                report_type: {
                    validators: {
                        notEmpty: {
                            message: 'Cron Type Is Required.'
                        },
                        regexp: {
                            regexp: /^(?!\s*$).+/,
                            message: 'Cron Type Is Required.'
                        }
                    }
                },
                crontime: {
                    validators: {
                        notEmpty: {
                            message: 'The Cron time is required'
                        }
                    }
                },
                cron_time: {
                    validators: {
                        notEmpty: {
                            message: 'Cron Time Is Required.'
                        },
                        regexp: {
                            regexp: /^(?!\s*$).+/,
                            message: 'Cron Time Is Required.'
                        }

                    }
                },
                cronstatus: {
                    validators: {
                        notEmpty: {
                            message: 'The Cron Status Is Required'
                        }
                    }
                },
            },
            plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                bootstrap: new FormValidation.plugins.Bootstrap(),
                submitButton: new FormValidation.plugins.SubmitButton(),
                icon: new FormValidation.plugins.Icon({
                    //valid: 'fa fa-check',
                   // invalid: 'fa fa-times',
                    validating: 'fa fa-refresh'
                }),
            },
        }
    ).on('core.form.valid', function () {
        $("#btnSubmitMwsConfig").attr("disabled", true);
        var formData = new FormData();
        var params = $('#add_cron_from').serializeArray();
        var timer = 3000;
        // Get simple form data
        $.each(params, function (i, val) {
            formData.append(val.name, val.value);
        });
        axios.post(base_url + '/mws/addCron', formData, {
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
                /*Swal.fire({
                    title: '<strong>' + response.data.title + '</strong>',
                    type: 'info',
                    html:
                    $errorMessage,
                    showCloseButton: true,
                    showCancelButton: true,
                    focusConfirm: false,
                    confirmButtonText:
                        '<i class="fa fa-thumbs-up"></i> Ok!',
                    confirmButtonAriaLabel: 'Thumbs up, great!',
                    cancelButtonText:
                        '<i class="fa fa-thumbs-down"></i>',
                    cancelButtonAriaLabel: 'Thumbs down',
                })*/
                Swal.fire({
                    type: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!'

                })
                $("#btnSubmitMwsConfig").attr("disabled", false);
            } else {
                //
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
                    document.getElementById("add_cron_from").reset();
                }, timer / 2);
            }
        });
    });
});