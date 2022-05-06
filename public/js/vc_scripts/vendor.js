/* Vendors Add Form */
document.addEventListener('DOMContentLoaded', function (e) {
    const form = document.getElementById('vendorAddFormId');
    const formValidation = FormValidation.formValidation(form, {
            fields: {
                vendor_name: {
                    validators: {
                        notEmpty: {
                            message: 'The vendor is required'
                        }
                    }
                },
                domain: {
                    validators: {
                        notEmpty: {
                            message: 'The domain is required'
                        }
                    }
                },
                tier: {
                    validators: {
                        notEmpty: {
                            message: 'The tier is required'
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
    ).on('core.form.valid', function() {
        var btnHtml = '<i class="fas fa-circle-notch fa-spin"></i>';
        submitBtnEnableDisable(true, btnHtml);
        var formData = new FormData();
        var params = $(form).serializeArray();
        $.each(params, function(i, val) {
            formData.append(val.name, val.value);
        });

        axios.post(siteURL +'/vc/vendors', formData, {
        }).then(function(response) {
            submitBtnEnableDisable(false)
            if (response.data.ajax_status == true){
                Swal.fire({type: 'success', title: response.data.success, showConfirmButton: true});
                formDataAndValidationReset(form, formValidation);
            }else{
                var htmlErrors = showErrors(response.data.error);
                Swal.fire({type: 'error', html: htmlErrors, showConfirmButton: true});
            }
        }).catch(function (error) {
            //handle error
            submitBtnEnableDisable(false);
            errorResponseShow(error);
        });

    });
});