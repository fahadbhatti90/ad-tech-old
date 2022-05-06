document.addEventListener('DOMContentLoaded', function (e) {
    singleDatePicker('catalog_date');
    const form = document.getElementById('productCatalogFormId');
    const formValidation = FormValidation.formValidation(form, {
            fields: {
                vendor: {
                    validators: {
                        notEmpty: {
                            message: 'The vendor is required'
                        }
                    }
                },
                catalog_date: {
                    validators: {
                        notEmpty: {
                            message: 'The product catalog date is required'
                        },
                        date: {
                            format: 'MM/DD/YYYY',
                            message: 'The product catalog date is not a valid date',
                        }
                    }
                },
                catalog_upload_file: {
                    validators: {
                        notEmpty: {
                            message: 'The product catalog file is required'
                        },
                        file: {
                            extension: 'xls,xlsx,csv',
                            message: 'Please choose a excel file'
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
        var uploadFiles = form.querySelector('[name="catalog_upload_file"]').files;
        if (uploadFiles.length > 0) {
            formData.append('catalog_upload_file', uploadFiles[0]);
        }
        axios.post(siteURL + '/vc/catalog', formData, {
        }).then(function(response) {
            submitBtnEnableDisable(false);
            if (response.data.ajax_status == true){
                Swal.fire({type: 'success', title: response.data.success, showConfirmButton: true});
                //formDataAndValidationReset(form, formValidation);
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