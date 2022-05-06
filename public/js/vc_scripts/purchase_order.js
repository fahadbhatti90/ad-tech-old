/* Purchase Order Form validation Client Side */
document.addEventListener('DOMContentLoaded', function (e) {
    const form = document.getElementById('purchaseOrderFormId');
    const formValidation = FormValidation.formValidation(form, {
            fields: {
                vendor: {
                    validators: {
                        notEmpty: {
                            message: 'The vendor is required'
                        }
                    }
                },
                open_agg_file: {
                    validators: {
                        file: {
                            extension: 'xls,xlsx,csv',
                            message: 'Please choose a excel file'
                        }
                    }
                },
                open_nonagg_file: {
                    validators: {
                        file: {
                            extension: 'xls,xlsx,csv',
                            message: 'Please choose a excel file'
                        }
                    }
                },
                close_agg_file: {
                    validators: {
                        file: {
                            extension: 'xls,xlsx,csv',
                            message: 'Please choose a excel file'
                        }
                    }
                },
                close_nonagg_file: {
                    validators: {
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
    ).on('core.form.valid', function () {
        var btnHtml = '<i class="fas fa-circle-notch fa-spin"></i>';
        submitBtnEnableDisable(true, btnHtml);
        var formData = new FormData();
        var params = $(form).serializeArray();
        $.each(params, function (i, val) {
            formData.append(val.name, val.value);
        });
        var openAggFile = form.querySelector('[name="open_agg_file"]').files;
        var openNonAggFile = form.querySelector('[name="open_nonagg_file"]').files;
        var closeAggFile = form.querySelector('[name="close_agg_file"]').files;
        var closeNonAggFile = form.querySelector('[name="close_nonagg_file"]').files;
        if (openAggFile.length > 0) {
            formData.append('open_agg_file', openAggFile[0]);
        }
        if (openNonAggFile.length > 0) {
            formData.append('open_nonagg_file', openNonAggFile[0]);
        }
        if (closeAggFile.length > 0) {
            formData.append('close_agg_file', closeAggFile[0]);
        }
        if (closeNonAggFile.length > 0) {
            formData.append('close_nonagg_file', closeNonAggFile[0]);
        }
        axios.post(siteURL + '/vc/purchaseorder', formData, {}).then(function (response) {
            submitBtnEnableDisable(false);
            if (response.data.ajax_status == true) {
                let htmlSuccess = showSuccessHtml(response.data.success);
                Swal.fire({type: 'success', html: htmlSuccess, showConfirmButton: true});
                //formDataAndValidationReset(form, formValidation);
            } else if (response.data.ajax_status == false) {
                let htmlErrors = showErrors(response.data.error);
                Swal.fire({type: 'error', html: htmlErrors, showConfirmButton: true});
            } else {
                let htmlErrors = showErrors(response.data.error);
                Swal.fire({type: 'error', html: htmlErrors, showConfirmButton: true});
            }
        }).catch(function (error) {
            //handle error
            submitBtnEnableDisable(false);
            errorResponseShow(error);
        });

    });
});