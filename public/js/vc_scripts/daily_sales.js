document.addEventListener('DOMContentLoaded', function (e) {
    singleDatePicker('daily_sales_date');
    const form = document.getElementById('dailySalesFormId');
    const formValidation = FormValidation.formValidation(form, {
            fields: {
                vendor: {
                    validators: {
                        notEmpty: {
                            message: 'The vendor is required'
                        }
                    }
                },
                daily_sales_date: {
                    validators: {
                        notEmpty: {
                            message: 'The daily sales date is required'
                        },
                        date: {
                            format: 'MM/DD/YYYY',
                            message: 'The daily sales date is not a valid date',
                        }
                    }
                },
                daily_sales: {
                    validators: {
                        notEmpty: {
                            message: 'The daily sales file is required'
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
        var uploadFiles = form.querySelector('[name="daily_sales"]').files;
        if (uploadFiles.length > 0) {
            formData.append('daily_sales', uploadFiles[0]);
        }
        axios.post(siteURL + '/vc/dailysales', formData, {
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