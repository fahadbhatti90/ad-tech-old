$(function () {
    $('.amscloseForm').on('click', function() {
        $('#apiConfigForm')[0].reset();
        $('#apiConfigForm').find('.form-control-user').removeClass("is-valid");
        $('#apiConfigForm').find('.form-control-user').removeClass("is-invalid");
        $('#apiConfigForm').find('.fv-plugins-icon').removeClass("fa-check");
        $('#apiConfigForm').find('.fv-plugins-icon').removeClass("fa-times");
        $('#apiConfigForm').find('.fv-help-block').hide();
    });
});

document.addEventListener('DOMContentLoaded', function (e) {
    const apiConfigForm = document.getElementById('apiConfigForm');
    const fv = FormValidation.formValidation(apiConfigForm, {
            fields: {
                client_id: {
                    validators: {
                        notEmpty: {
                            message: 'The clientId is required'
                        }, regexp: {
                            regexp: /^amzn1\.application-oa2-client\.[a-z0-9]{32}$/i,
                            message: 'Invalid parameter value for clientId.'
                        }
                    }
                },
                client_secret: {
                    validators: {
                        notEmpty: {
                            message: 'The clientSecret is required'
                        },
                        regexp: {
                            regexp: /^[a-z0-9]{64}$/i,
                            message: 'Invalid parameter value for clientSecret.'
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
        var params = $('#apiConfigForm').serializeArray();
        var timer = 3000;
        // Get simple form data
        $.each(params, function (i, val) {
            formData.append(val.name, val.value);
        });
        axios.post(base_url + '/ams/addConfig', formData, {
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
                    document.getElementById("apiConfigForm").reset();
                }, timer / 2);
            }
        });
    });
});