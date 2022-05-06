document.addEventListener('DOMContentLoaded', function (e) {
    singleDatePicker('forecast_date');
    const form = document.getElementById('forecastFormId');
    const formValidation = FormValidation.formValidation(form, {
            fields: {
                vendor: {
                    validators: {
                        notEmpty: {
                            message: 'The vendor is required'
                        }
                    }
                },

                forecast_date: {
                    validators: {
                        notEmpty: {
                            message: 'The forecast date is required'
                        },
                        date: {
                            format: 'MM/DD/YYYY',
                            message: 'The forecast date is not a valid date',
                        }
                    }
                },
                forecast_upload_file: {
                    validators: {
                        notEmpty: {
                            message: 'The forecast file is required'
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
        var uploadFiles = form.querySelector('[name="forecast_upload_file"]').files;
        if (uploadFiles.length > 0) {
            formData.append('forecast_upload_file', uploadFiles[0]);
        }
        axios.post(siteURL + '/vc/forecast', formData, {
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