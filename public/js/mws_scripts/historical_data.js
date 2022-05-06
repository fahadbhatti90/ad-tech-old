var fv;
document.addEventListener('DOMContentLoaded', function (e) {

    const history_form = document.getElementById('ScHistoricalDataRetrivalForm');
    const fv = FormValidation.formValidation(history_form, {
            fields: {
                /*dateRamge: {
                    validators: {
                        notEmpty: {
                            message: 'Please Select Date Range.'
                        },
                        regexp: {
                            regexp: /^(?!\s*$).+/,
                            message: 'Please Select Date Range.'
                        }
                    }
                },*/
                report_type: {
                    validators: {
                        notEmpty: {
                            message: 'Please Select Report Type.'
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
                    //invalid: 'fa fa-times',
                    validating: 'fa fa-refresh'
                }),
            },
        }
    ).on('core.form.valid', function () {
       // $('form#ScHistoricalDataRetrivalForm').submit();
        $(".overlayAjaxStatus").fadeIn();
        var formData = new FormData();
        var params = $(history_form).serializeArray();
        var timer = 5000;
        // Get simple form data
        $.each(params, function (i, val) {
            formData.append(val.name, val.value);
        });
        axios.post(base_url + '/mws/checkScHistory', formData, {}).then(function (response) {
            $(".overlayAjaxStatus").fadeOut();
            fv.resetForm(true);
            $('#ScHistoricalDataRetrivalForm')[0].reset();
            var currentDate = moment().format("YYYY-MM-DD");
            var end_date = moment().format("YYYY-MM-DD");
            $('input[name="daterange"]').daterangepicker({
                opens: 'left',
                //drops:'down',
                autoUpdateInput: true,
                startDate: currentDate,
                maxDate: currentDate,
                endDate: end_date,
                locale: {
                    format: 'YYYY-MM-DD'
                }
            }, function(start, end, label) {
                currentDate = start;
                end_date = end;
            });

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
                var download_url=response.data.url;
                Swal.fire({
                    type: 'success',
                    title: response.data.title,
                    text: response.data.message,
                    showCloseButton: false,
                    showConfirmButton: false,
                    showCancelButton: false,
                    focusConfirm: false,
                   //footer: '<a onclick=getCsvFile("' + response.data.url + '");    href="javascript:void(0)" href="' + response.data.url + '">Click Here</a>'
                    //footer: '<a onclick="getCsvFile("'+ download_url+'")" href="javascript:void(0)" >Click Here</a>'
                    footer: '<a onclick="getCsvFile()" href="javascript:void(0)" id="getCsvFile" data-href="' + response.data.url + '">Click Here</a>'
                });
            }
        });

    });
    currentDate = moment().format("YYYY-MM-DD");
   // var end_date = moment(currentDate, "YYYY-MM-DD").add('days', 6).format("YYYY-MM-DD");
    var end_date = moment().format("YYYY-MM-DD");
    //console.log(currentDate);
    //console.log(end_date);
    $('input[name="daterange"]').daterangepicker({
        opens: 'left',
        //drops:'down',
        autoUpdateInput: true,
        startDate: currentDate,
        maxDate: currentDate,
        endDate: end_date,
        locale: {
            format: 'YYYY-MM-DD'
        }
    }, function(start, end, label) {
        currentDate = start;
        end_date = end;
    });
});
function getCsvFile() {
    const download_data = document.querySelector('#getCsvFile');
    var url=download_data.dataset.href;
    //location.href =download_data.dataset.href;
    window.open(url, '_blank');
    var timer=1000;
    setTimeout(function () {
        location.reload();
    }, timer / 2);
}
