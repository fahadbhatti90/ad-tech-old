// Config date Picker range with input field
$(function () {
    currentDate = moment().format("YYYY-MM-DD");
    var end_date = moment(currentDate, "YYYY-MM-DD").add(6, 'days').format("YYYY-MM-DD");
    $('input[name="daterange"]').daterangepicker({
        opens: 'left',
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
    const form = document.getElementById('VCHistoricalDataRetrivalForm');
    const fv = FormValidation.formValidation(form, {
            fields: {
                historicalDataReportType: {
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
            },
        }
    ).on('core.form.valid', function () {
        var btnHtml = '<i class="fas fa-circle-notch fa-spin"></i>';
        //submitBtnEnableDisable(true, btnHtml);
        var formData = new FormData();
        var params = $(form).serializeArray();
        $.each(params, function(i, val) {
            formData.append(val.name, val.value);
        });
        var timer = 3000;
        axios.post(siteURL + '/vc/history', formData, {}).then(function (response) {
            console.log(response.data.status);
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
                    document.getElementById(form).reset();
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
