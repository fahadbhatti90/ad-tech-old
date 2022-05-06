/* Form validation Client Side */
$(function () {
    currentDate = moment().format("YYYY-MM-DD");
    var end_date = moment(currentDate, "YYYY-MM-DD").add(6, 'days').format("YYYY-MM-DD");
    $('input[name="daterange"]').daterangepicker({
        opens: 'right',
        autoUpdateInput: true,
        maxDate: currentDate,
        startDate: currentDate,
        endDate: end_date,
        locale: {
            format: 'YYYY-MM-DD'
        }
    }, function (start, end, label) {
        currentDate = start;
        end_date = end;
        console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
    });
});

document.addEventListener('DOMContentLoaded', function (e) {
    const form = document.getElementById('trafficFormId');
    const formValidation = FormValidation.formValidation(form, {
            fields: {
                vendor: {
                    validators: {
                        notEmpty: {
                            message: 'The vendor is required'
                        }
                    }
                },
                daterange: {
                    validators: {
                        notEmpty: {
                            message: 'The date field is required'
                        }
                    }
                },
                traffic_upload_file: {
                    validators: {
                        notEmpty: {
                            message: 'The traffic file is required'
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
        var uploadFiles = form.querySelector('[name="traffic_upload_file"]').files;
        if (uploadFiles.length > 0) {
            formData.append('traffic_upload_file', uploadFiles[0]);
        }
        axios.post(siteURL + '/vc/traffic', formData, {
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