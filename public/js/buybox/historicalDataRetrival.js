// Config date Picker range with input field
$(function () {
    currentDate = moment().format("YYYY-MM-DD");
    var end_date = moment(currentDate, "YYYY-MM-DD").add('days', 6).format("YYYY-MM-DD");
    $('input[name="daterange"]').daterangepicker({
        opens: 'left',
        autoUpdateInput: true,
        startDate: currentDate,
        endDate: end_date,
        locale: {
            format: 'YYYY-MM-DD'
        }
    }, function (start, end, label) {
        currentDate = start;
        end_date = end;
    });
});
document.addEventListener('DOMContentLoaded', function (e) {
    const Form = document.getElementById('historicalDataRetrivalForm');
    const fv = FormValidation.formValidation(Form, {
            fields: {
                reporttype: {
                    validators: {
                        notEmpty: {
                            message: 'The Report Type is required'
                        }
                    }
                },
                daterange: {
                    validators: {
                        notEmpty: {
                            message: 'The Date Range is required'
                        }
                    }
                },
            },
            plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                bootstrap: new FormValidation.plugins.Bootstrap(),
                submitButton: new FormValidation.plugins.SubmitButton(),
                icon: new FormValidation.plugins.Icon({
                    valid: 'fa fa-check',
                    invalid: 'fa fa-times',
                    validating: 'fa fa-refresh'
                }),
            },
        }
    ).on('core.form.valid', function () {
        var formData = new FormData();
        var params = $(Form).serializeArray();
        var timer = 3000;
        // Get simple form data
        $.each(params, function (i, val) {
            formData.append(val.name, val.value);
        });
        axios.post(base_url + '/ams/checkHistory', formData, {}).then(function (response) {
            // Check response your status
            if (response.data.status == false) {
                Swal.fire({
                    type: 'error',
                    title: response.data.title,
                    text: response.data.message,
                    showConfirmButton: false,
                    showCloseButton: false,
                    showCancelButton: false,
                    focusConfirm: false
                });
                setTimeout(function () {
                    location.reload();
                    document.getElementById(Form).reset();
                }, timer / 2);
            } else {
                Swal.fire({
                    type: 'success',
                    title: response.data.title,
                    text: response.data.message,
                    showCloseButton: false,
                    showConfirmButton: false,
                    showCancelButton: false,
                    focusConfirm: false,
                    footer: '<a href="' + response.data.url + '" target="_blank">Click Here</a>'
                });
            }
        });
    });
});
