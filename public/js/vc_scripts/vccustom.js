/*
* Vendor Center Custom Js Work Will done here
* */

function singleDatePicker(fieldName){
    $('input[name="'+fieldName+ '"').daterangepicker({
    //$('.date').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        maxDate: new Date(),
        minYear: 1990,
        maxYear: parseInt(moment().format('YYYY'),10),
    });
}


$(function () {
    // Alert hide after 5 seoncd
    $('.alert').fadeIn('slow', function () {$('.alert').delay(5000).fadeOut();});
});

// Date Picker
$(".date").datepicker({autoclose: true});
/*$(document).on('click', '.date', function () {
    $('.date').datepicker({autoclose: true});
});*/

// following code will show the name of the file upload appear on select
$(document).on('change', '.custom-file-input', function () {
    var fileName = $(this).val().split("\\").pop();
    $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
});
// btn enabled disable on submit
function submitBtnEnableDisable(btnStatus, html='') {
    var btn = $(".btn-primary");
    btn.prop('disabled', btnStatus);
    btn.html(html + ' Submit');
}
/* form data reset as well as remove validation errors */
function formDataAndValidationReset(form, formValidation){
    $(form).trigger("reset");
    $(".custom-file-label").removeClass("selected").html("Choose File");
    // Remove validation attribute from html tags after validation
    formValidation.resetForm('true');
}
/* showing errors html*/
function showErrors(errorsData) {
    let htmlErrors = '<div class="alert alert-danger"><ul>';
    $.each( errorsData , function( key, value ) {
        htmlErrors += '<li style="list-style: none;">'+value+'</li>';
    });
    htmlErrors += '</ul></div>';
    return htmlErrors;
}
/* Showing Success html*/
function showSuccessHtml(successData) {
    let htmlSuccess = '<div class="alert alert-success"><ul>';
    $.each( successData , function( key, value ) {
        htmlSuccess += '<li style="list-style: none;">'+value+'</li>';
    });
    htmlSuccess += '</ul></div>';
    return htmlSuccess;
}

function errorResponseShow(error) {
    if (error.response.status == 401){
        location.reload();
    }else if(error.response.status == 500){
        Swal.fire({type: 'error', html: 'There is something wrong!', showConfirmButton: true});
    }
}