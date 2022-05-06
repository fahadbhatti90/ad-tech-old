var fv;
document.addEventListener('DOMContentLoaded', function(e) {
    const form = document.getElementById('add_api_config');
     fv=FormValidation.formValidation(form, {
        fields: {
            merchant_name: {
                validators: {
                    notEmpty: {
                        message: 'The Seller Name Is Required.'
                    },
                    stringLength: {
                        min: 1,
                        max: 50,
                        message: 'Maximum Length Exceeded.'
                    },
                    regexp: {
                        regexp: /^(?!\s*$).+/,
                        message: 'The Seller Name Is Required.'
                    }
                }
            },
            seller_id: {
                validators: {
                    notEmpty: {
                        message: 'The Seller Id Is Required.'
                    },
                    stringLength: {
                        min: 1,
                        max: 14,
                        message: 'The Seller Id Must Be More Than 1 And Less Than 14 Characters Long.'
                    },
                    regexp: {
                        regexp: /^(?!\s*$).+/,
                        message: 'The Seller Id Is Required.'
                    }

                }
            },
            mws_access_key_id: {
                validators: {
                    notEmpty: {
                        message: 'Mws Access Key Is Required.'
                    },
                    stringLength: {
                        min: 20,
                        max: 20,
                        message: 'The Mws Access Key Must Be 20 Characters Long.'
                    },
                    regexp: {
                        regexp: /^(?!\s*$).+/,
                        message: 'Mws Access Key Is Required.'
                    }
                }
            },
            mws_authtoken: {
                validators: {
                    notEmpty: {
                        message: 'The Mws Auth Token Is Required.'
                    },
                    /*stringLength: {
                        min: 45,
                        max: 45,
                        message: 'The Mws access key must be 45 characters long.'
                    },*/
                    regexp: {
                        regexp: /^(?!\s*$).+/,
                        message: 'The Mws Auth Token Is Required.'
                    }
                    ,regexp: {
                        regexp: /^amzn\.mws\.[a-z0-9]{8}\-[a-z0-9]{4}\-[a-z0-9]{4}\-[a-z0-9]{4}\-[a-z0-9]{12}$/i,
                        message: 'Invalid Parameter Value.'
                    }
                }
            },
            mws_secret_key: {
                validators: {
                    notEmpty: {
                        message: 'The Secret Key Is Required.'
                    },
                stringLength: {
                    min: 40,
                    max: 40,
                    message: 'The Secret Key Must Be 40 Characters Long.'
                },
                    regexp: {
                        regexp: /^(?!\s*$).+/,
                        message: 'The Secret Key Is Required.'
                    }
                }
            },
           /* marketplace_id: {
                validators: {
                    notEmpty: {
                        message: 'The market place id is required.'
                    },
                    stringLength: {
                        min: 1,
                        max: 14,
                        message: 'The market place id must be more than 1 and less than 14 characters long.'
                    },
                    regexp: {
                        regexp: /^(?!\s*$).+/,
                        message: 'The market place id is required.'
                    }
                }
            },*/


        },
        plugins: {
            trigger: new FormValidation.plugins.Trigger(),
            bootstrap: new FormValidation.plugins.Bootstrap(),
            submitButton: new FormValidation.plugins.SubmitButton(),
            icon: new FormValidation.plugins.Icon({
               // valid: 'fa fa-check',
               // invalid: 'fa fa-times',
                validating: 'fa fa-refresh',
            }),
        },
    }).on('core.form.valid', function() {

         $("#submit_config_btn").attr("disabled", true);
         var formData = new FormData();
         var params = $('#add_api_config').serializeArray();
         var timer = 3000;
         // Get simple form data
         $.each(params, function (i, val) {
             formData.append(val.name, val.value);
         });
         axios.post(base_url + '/mws/addConfig', formData, {
             headers: {
                 'Content-Type': 'multipart/form-data'
             }
         }).then(function (response) {

             if (response.data.status == 'fail') {
                 $("#submit_config_btn").attr("disabled", false);
                 $errorMessage = '';
                 Swal.fire({
                     type: 'error',
                     title: '<strong>' + response.data.title + '</strong>',
                     text: $errorMessage,
                     showConfirmButton: true,
                     showCloseButton: true,
                     showCancelButton: false,
                     focusConfirm: false
                 });

             } else {
                 //
                 const Toast = Swal.mixin({
                     toast: true,
                     position: 'top-end',
                     showConfirmButton: false,
                     timer: timer
                 });

                 Toast.fire({
                     type: 'success',
                     title: response.data.title
                 });
                 setTimeout(function () {
                     location.reload();
                     document.getElementById("add_cron_from").reset();
                 }, timer / 2);
             }
         });


        });
});
$(document).ready(function(){

    setTimeout(function() {
        //alert('time');
        $('.flash-message').fadeOut('slow');
    }, 3000); // <-- time in milliseconds
});

$('[data-dismiss=modal]').on('click', function (e) {
    //alert('test');
   // $('#add_api_config')[0].reset();
    fv.resetForm(true);
    //$('#add_api_config').resetForm('true');


})
$('#timepicker').timepicker({
    uiLibrary: 'bootstrap4'
});

$('#mwsapiconfigModal').on('show.bs.modal', function(e) {
    fv.resetForm(true);
});