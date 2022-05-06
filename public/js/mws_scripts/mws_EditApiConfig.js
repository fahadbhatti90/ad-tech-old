var fcedit;

document.addEventListener('DOMContentLoaded', function(e) {


    const form = document.getElementById('edit_api_config');
    var fcedit=FormValidation.formValidation(form, {
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
                        message: 'The Seller id Must Be More Than 1 And Less Than 14 Characters Long.'
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
                //valid: 'fa fa-check',
                //invalid: 'fa fa-times',
                validating: 'fa fa-refresh',
            }),
        },
    })
        .on('core.form.valid', function() {
            $("#update_config_btn").attr("disabled", true);

           // $("#submit_config_btn").attr("disabled", true);
            var formData = new FormData();
            var params = $('#edit_api_config').serializeArray();
            var timer = 3000;
            // Get simple form data
            $.each(params, function (i, val) {
                formData.append(val.name, val.value);
            });
            axios.post(base_url + '/mws/editConfig', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }).then(function (response) {


                if (response.data.status == 'fail') {
                    $("#update_config_btn").attr("disabled", false);
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
                        //document.getElementById("add_cron_from").reset();
                    }, timer / 2);
                }
            });

        });
    $('#mws_EditApiConfigModal').on('show.bs.modal', function(e) {
        fcedit.resetForm(true);
        var api_config_id = $(e.relatedTarget).data('api-config-id');
        var merchant_name = $(e.relatedTarget).data('merchant-name');
        var seller_id = $(e.relatedTarget).data('seller-id');
        var mws_access_key_id = $(e.relatedTarget).data('mws-access-key-id');
        var mws_authtoken = $(e.relatedTarget).data('mws-authtoken');
        var mws_secret_key = $(e.relatedTarget).data('mws_secret-key');
        //var marketplace_id = $(e.relatedTarget).data('marketplace-id');
        $(e.currentTarget).find('input[name="api_config_id"]').val(api_config_id);
        $(e.currentTarget).find('input[name="merchant_name"]').val(merchant_name);
        $(e.currentTarget).find('input[name="seller_id"]').val(seller_id);
        $(e.currentTarget).find('input[name="mws_access_key_id"]').val(mws_access_key_id);
        $(e.currentTarget).find('input[name="mws_authtoken"]').val(mws_authtoken);
        $(e.currentTarget).find('input[name="mws_secret_key"]').val(mws_secret_key);
        // $(e.currentTarget).find('input[name="marketplace_id"]').val(marketplace_id);

    });



});
//$(".deleteConfig").click(function(){
 function delete_config(config_id,token) {

    var id = config_id;
    var token = token;

     Swal.fire({
         title: '<strong>Are you sure?</strong>',
         text: "Record Once Deleted Can Never Be Retrieved Again",
         type: 'warning',
         showCancelButton: true,
         customClass:{
             confirmButton: 'sweetAlertConfirmButtons',
             cancelButton: 'sweetAlertCancelButtons',
         },
         /*buttonsStyling:false,*/
         /*padding:0.25,*/
         confirmButtonColor: '#d33',
        /* cancelButtonColor: '#3085d6',*/
         confirmButtonText: 'Confirm'
     }).then((result) => {
         if (result.value) {
             /*Swal.fire(
                 'Deleted!',
                 'Your file has been deleted.',
                 'success'
             )*/
             $.ajax({
                 url:"deleteApiConfig",
                 type:"POST",
                 data: {
                     "id": id ,_token:token// method and token not needed in data
                 },
                 cache: false,
                 success: function(response) {

                     //console.log(response.status);
                     //return false;
                     if (response.status == 'fail') {
                         $("#update_config_btn").attr("disabled", false);
                         $errorMessage = '';
                         Swal.fire({
                             type: 'error',
                             title: '<strong>' + response.title + '</strong>',
                             text: $errorMessage,
                             showConfirmButton: true,
                             showCloseButton: true,
                             showCancelButton: false,
                             focusConfirm: false
                         });

                     } else {
                     var timer = 3000;
                     const Toast = Swal.mixin({
                         toast: true,
                         position: 'top-end',
                         showConfirmButton: false,
                         timer: timer
                     });

                     Toast.fire({
                         type: 'success',
                         title: response.title
                     });
                     setTimeout(function () {
                         location.reload();
                     }, timer / 2);
                 }
                 }
             });
         }
     })


}
/*
function delete_config(config_id,token) {

    var id = config_id;
    var token = token;

     $.ajax({
         url:"deleteApiConfig",
         type:"POST",
         data: {
             "id": id ,_token:token// method and token not needed in data
         },
         cache: false,
         success: function(response){

             var timer = 3000;
             const Toast = Swal.mixin({
                 toast: true,
                 position: 'top-end',
                 showConfirmButton: false,
                 timer: timer
             });

             Toast.fire({
                 type: 'success',
                 title: 'Configuration Successfully.'
             });
             setTimeout(function () {
                 location.reload();
             }, timer / 2);

         }
     });

}
*/