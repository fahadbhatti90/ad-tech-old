/* Form validation Client Side */

$(function () {
    $('select[name="type"]').on('change', function() {
        if(this.value  == 'traffic'){
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
            });
        }else {
            $('input[name="daterange"]').val('').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                maxDate: new Date(),
                minYear: 1990,
                maxYear: parseInt(moment().format('YYYY'),10),
            });
        }
    });

});

document.addEventListener('DOMContentLoaded', function (e) {
    const form = document.getElementById('deleteFormId');
    const formValidation = FormValidation.formValidation(form, {
        fields: {
            vendor: {
                validators: {
                    notEmpty: {
                        message: 'The vendor is required'
                    }
                }
            },
            type: {
                validators: {
                    notEmpty: {
                        message: 'The Type is required'
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
        },
        plugins: {
            trigger: new FormValidation.plugins.Trigger(),
            bootstrap: new FormValidation.plugins.Bootstrap(),
            submitButton: new FormValidation.plugins.SubmitButton(),
        },
    });
    const deleteButton = document.getElementById('delete');
    const verifyButton = document.getElementById('verify');
    const moveToMainButton = document.getElementById('move_to_main');
    deleteButton.addEventListener('click', function () {
        formValidation.validate().then(function (status) {
            if (status === 'Valid') {
                deleteModal(form, formValidation)
            }
        });
    });
    verifyButton.addEventListener('click', function () {
        form.querySelector('[name="daterange"]').addEventListener('input', function(e) {
            const password = e.target.value;
            if (password === '') {
                formValidation.disableValidator('daterange');
            }
        });
        formValidation.validate().then(function (status) {
            if (status === 'Valid') {
                let btn = $('#verify');
                var formData = new FormData();
                var params = $(form).serializeArray();
                $.each(params, function (i, val) {
                    formData.append(val.name, val.value);
                });
                var btnHtml = '<i class="fas fa-circle-notch fa-spin"></i>';
                btn.prop('disabled', true);
                btn.html(btnHtml +' Verify');
                axios.post(siteURL + '/vc/verify', formData, {}).then(function (response) {
                    btn.prop('disabled', false);
                    btn.html('Verify');
                    if (response.data.ajax_status == true) {
                        $('#carrierReturnData').html('');
                        if(response.data.data_array.length > 0){
                            if(response.data.type != ''){
                                if(response.data.type == 'traffic'){
                                    let htmlData = '<table' + ' class="table table-striped table-bordered rounded"\n' +
                                        'style="width:100%"><thead><th>Vendor id</th><th>Start Date Column</th><th>End Date Column</th><th>Count</th><th>Duplication</th></thead><tbody>';
                                    for (let i=0; i<response.data.data_array.length; i++) {
                                        let fontcolor = '';
                                        let bgcolor = '';
                                        if(response.data.data_array[i].dup_count > 0){
                                            bgcolor = '#FF0000';
                                            fontcolor = '#fff';
                                        }
                                        htmlData += '<tr style="background-color:'+bgcolor+'; color:'+fontcolor+'"><td>'+response.data.data_array[i].fk_vendor_id +'</td><td>'+response.data.data_array[i].start_date_column +'</td><td>'+response.data.data_array[i].end_date_column +'</td><td>'+response.data.data_array[i].Row_Count +'</td><td>'+response.data.data_array[i].dup_count +'</td></tr>';
                                    }
                                    htmlData += '</tbody></table>';
                                    $('#carrierReturnData').html(htmlData);
                                }else{
                                    let htmlData = '<table' + ' class="table table-striped table-bordered rounded"\n' +
                                        'style="width:100%"><thead><th>Vendor id</th><th>Date</th><th>Count</th><th>Duplication</th></thead><tbody>';
                                    for (let i=0; i<response.data.data_array.length; i++) {
                                        let fontcolor = '';
                                        let bgcolor = '';
                                        if(response.data.data_array[i].dup_count > 0){
                                            bgcolor = '#FF0000';
                                            fontcolor = '#fff';
                                        }
                                        htmlData += '<tr style="background-color:'+bgcolor+'; color:'+fontcolor+'"><td>'+response.data.data_array[i].fk_vendor_id +'</td><td>'+response.data.data_array[i].date_column +'</td><td>'+response.data.data_array[i].Row_Count +'</td><td>'+response.data.data_array[i].dup_count +'</td></tr>';
                                    }
                                    htmlData += '</tbody></table>';
                                    $('#carrierReturnData').html(htmlData);
                                }
                            }
                        }else{
                            Swal.fire({
                                type: 'success',
                                title: response.data.success,
                                showConfirmButton: true,
                                target: document.getElementById('body')
                            });
                        }
                    } else if (response.data.ajax_status == false) {
                        Swal.fire({
                            type: 'error',
                            title: response.data.error,
                            showConfirmButton: true,
                            target: document.getElementById('body')
                        });
                        $('body').css("padding-right","0");
                    } else {
                        var htmlErrors = showErrors(response.data.error);
                        Swal.fire({
                            type: 'error',
                            html: htmlErrors,
                            showConfirmButton: true,
                            target: document.getElementById('body')
                        });
                    }
                }).catch(function (error) {
                    //handle error
                    btn.prop('disabled', false);
                    errorResponseShow(error);
                });
            }
        });
    });
    moveToMainButton.addEventListener('click', function () {
        form.querySelector('[name="daterange"]').addEventListener('input', function(e) {
            $('input[name="daterange"]').val('');
            const daterange = e.target.value;
            if (daterange.empty()) {
                formValidation.disableValidator('daterange');
            }
        });
        formValidation.validate().then(function (status) {
            if (status === 'Valid') {
                moveToMainModal(form, formValidation)
            }
        });
    });
});

/**
 * This function is used to Delete the record
 * @param form
 * @param formValidation
 */
function deleteModal(form, formValidation) {
    $('#carrierReturnData').html('');
    var type = 'Delete';
    $("#comfirmationModal").modal('show');
    $("#type").html(type);
    $("#modal-btn-yes").html(type);
    var btn = $("#modal-btn-yes");
    btn.one("click", function (e) {
        e.preventDefault();
        var formData = new FormData();
        var params = $(form).serializeArray();
        $.each(params, function (i, val) {
            formData.append(val.name, val.value);
        });
        var btnHtml = '<i class="fas fa-circle-notch fa-spin"></i>';
        btn.prop('disabled', true);
        btn.html(btnHtml +' Submit');
        axios.post(siteURL + '/vc/delete', formData, {}).then(function (response) {
            btn.prop('disabled', false);
            btn.html('Submit');
            $("#comfirmationModal").modal('hide');
            if (response.data.ajax_status == true) {
                Swal.fire({type: 'success', title: response.data.success, showConfirmButton: true});
                //formDataAndValidationReset(form, formValidation);
            } else if (response.data.ajax_status == false) {
                Swal.fire({type: 'error', title: response.data.error, showConfirmButton: true});
            } else {
                var htmlErrors = showErrors(response.data.error);
                Swal.fire({type: 'error', html: htmlErrors, showConfirmButton: true});
            }
        }).catch(function (error) {
            //handle error
            btn.prop('disabled', false);
            errorResponseShow(error);
        });
    });
    $("#modal-btn-no").one("click", function () {
        $("#comfirmationModal").modal('hide');
    });
}

/**
 * This function is used to Move To Main the record
 * @param form
 * @param formValidation
 */
function moveToMainModal(form, formValidation) {
    $('#carrierReturnData').html('');
    var type = 'Move to main';
    $("#comfirmationModal").modal('show');
    $("#type").html(type);
    $("#modal-btn-yes").html(type);
    var btn = $("#modal-btn-yes");
    btn.one("click", function (e) {
        e.preventDefault();
        var formData = new FormData();
        var params = $(form).serializeArray();
        $.each(params, function (i, val) {
            formData.append(val.name, val.value);
        });
        var btnHtml = '<i class="fas fa-circle-notch fa-spin"></i>';
        btn.prop('disabled', true);
        btn.html(btnHtml +' Submit');
        axios.post(siteURL + '/vc/move', formData, {}).then(function (response) {
            btn.prop('disabled', false);
            btn.html('Submit');
            $("#comfirmationModal").modal('hide');
            if (response.data.ajax_status == true) {
                Swal.fire({type: 'success', title: response.data.success, showConfirmButton: true});
                //formDataAndValidationReset(form, formValidation);
            } else if (response.data.ajax_status == false) {
                Swal.fire({type: 'error', title: response.data.error, showConfirmButton: true});
            } else {
                var htmlErrors = showErrors(response.data.error);
                Swal.fire({type: 'error', html: htmlErrors, showConfirmButton: true});
            }
        }).catch(function (error) {
            //handle error
            btn.prop('disabled', false);
            errorResponseShow(error);
        });
    });
    $("#modal-btn-no").one("click", function () {
        $("#comfirmationModal").modal('hide');
    });
}