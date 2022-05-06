$(document).ready(function () {
    var t = $('#buyboxCronTable').DataTable( {
        "ordering": false,
        "scrollX": true
    } );
    $('.custom-file-input').on("change",function (e) { 
        if($(this).get(0).files.length <= 0){
            console.log("No File Selected");
            ($(this).parent().find("label.custom-file-label").text("Chose File"));
            return;
        }
        fileName = $(this).get(0).files[0].name;
        
        console.log(fileName);
        ($(this).parent().find("label.custom-file-label").text(fileName));
         
     });
    $('.deleteCollection').click(function () {
        $('.deleteCollection').hide();
        var collection_id = $(this).data('id');
        var row = $(this).parents("tbody").find("#"+collection_id);
        
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            type: 'warning',
            customClass: {
                confirmButton: 'deleteConfirmationBoxConfirmButton',
              },
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Confirm',     
            showLoaderOnConfirm: true,
            allowOutsideClick: () => !Swal.isLoading(),
            preConfirm: (ddetails) => {
                $(".deleteConfirmationBoxConfirmButton").addClass("deleteConfirmationBoxConfirmButtonOnClick");
                   

                return fetch(base_url + '/buybox/deletebatch/'+collection_id, {
                    method: 'DELETE',
                    headers: {
                        'Content-type': 'application/json; charset=UTF-8' // Indicates the content 
                       },
                    body:JSON.stringify({'_token':_token})
                  })
                  .then(response =>{
                    return response.json()
                  } ) // OR res.text()
                  .catch(error => {
                    $('.deleteCollection').show();
                    $(".deleteConfirmationBoxConfirmButton").removeClass("deleteConfirmationBoxConfirmButtonOnClick");
                          console.log(error);
                        Swal.showValidationMessage(
                          `Request failed: ${error}`
                        )
                      })
              },
              onClose: () => {
                    $('.deleteCollection').show();
              }
          }).then((result) => {
            $(".deleteConfirmationBoxConfirmButton").removeClass("deleteConfirmationBoxConfirmButtonOnClick");
                  
            if(result.value.status !="fail"){
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });

                Toast.fire({
                    type: 'success',
                    title: result.value.message
                }); 
                location.reload();
                // Swal.fire({
                //     type: 'success',
                //     title: result.value.title,
                //     text: result.value.message,
                //     showConfirmButton: false,
                // });
                // location reload
                
            }else{
                
                $('.deleteCollection').show();
                Swal.showValidationMessage(
                    `Request failed: ${result.value.title}`
                  )
                console.log(result.value.consoleMessage);
                // Swal.fire({
                //     title: '<strong>'+result.value.title+'</strong>',
                //     type: 'error',
                //     text:result.value.message
                // })
            }
          })

    });
});
document.addEventListener('DOMContentLoaded', function (e) {
    $("#collapseSix h6").hide();
    $('#addScheduleFormModel').on('hidden.bs.modal', function (e) {
        $("#buyboxForm").get(0).reset();
        $("#buyboxForm label.custom-file-label").text("Chose File")
       fv.resetForm(true);
     })
    const form = document.getElementById('buyboxForm');
    const fv = FormValidation.formValidation(form, {
        fields: {
            c_name_buybox: {
                validators: {
                    notEmpty: {
                        message: 'The collection name is required'
                    },
                    stringLength: {
                        min: 1,
                        max: 99,
                        message: 'Collection name must be less than 100 characters'
                    },
                }
            },
            buybox_email: {
                validators: {
                    notEmpty: {
                        message: 'The email address is required'
                    },
                    stringLength: {
                        min: 1,
                        max: 99,
                        message: 'Email must be less than 100 characters'
                    },
                    emailAddress: {
                        message: 'The email is not a valid email address'
                    }
                }
            },
            asinfiles: {
                validators: {
                    notEmpty: {
                        message: 'The file is required'
                    },
                    file: {
                        extension: 'xlsx,xls,csv',
                        type: '.csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel',
                        message: 'Please choose a CSV or excel file'
                    }
                }
            },
            frequency: {
                validators: {
                    notEmpty: {
                        message: 'The frequency is required'
                    }
                }
            },
            duration: {
                validators: {
                    notEmpty: {
                        message: 'The duration is required'
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
                validating: 'fa fa-refresh',
            }),
        },
    }).on('core.form.valid', function () {
        console.log("Working");
        $(".buyboxSubmitButton").attr("disabled","disabled");
        $(".buyboxSubmitButton").html(
            '<img src="'+siteURL+'/public/images/InLinePreloader.gif" class="inlinePreLoader" >'
        );
        var formData = new FormData();
        var params = $(form).serializeArray();
        $.each(params, function (i, val) {
            formData.append(val.name, val.value);
        });
        var uploadFiles = form.querySelector('[name="asinfiles"]').files;
        if (uploadFiles.length > 0) {
            formData.append('asinfiles', uploadFiles[0]);
        }
        
        axios.post(base_url + '/buybox/addbatch', formData, {}).then(function (response) {
            if (response.data.status == true) {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });

                Toast.fire({
                    type: 'success',
                    title: response.data.message
                }); 
                // location reload
                setTimeout(function () {
                    location.reload()
                }, 2000);
            } else if (response.data.status == false) {
                $(".buyboxSubmitButton").removeAttr("disabled");
                $(".buyboxSubmitButton").html(
                    'Save'
                );
                Swal.fire({
                    type: 'error',
                    title: response.data.title,
                    text: response.data.message
                })
            }
        }).catch(function (error) {
                console.log(error);
                $(".buyboxSubmitButton").removeAttr("disabled");
                    $(".buyboxSubmitButton").html(
                        'Save'
                    );
                Swal.fire({
                    type: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!'
                })
        });
    });


});


