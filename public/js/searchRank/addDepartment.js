
document.addEventListener('DOMContentLoaded', function(e) {
    $('.tooltip').tooltipster({
        interactive:true,
        maxWidth:500
     });


    const form = document.getElementById('addDepartmentForm');
    const fv = FormValidation.formValidation(form, {
            fields: {
                departmentName: {
                    validators: {
                        notEmpty: {
                            message: 'This field is required'
                        }, 
                        stringLength: {
                            min: 1,
                            max: 255,
                            message: 'String Lengeth Must be 255'
                        },
                        regexp: {
                            regexp: /^[a-zA-Z0-9 _+-,]+$/,
                            message: 'The departmentAlias can only consist of alphabetical, number and underscore'
                        }
                    }
                },
                departmentAlias: {
                    validators: {
                        notEmpty: {
                            message: 'This field is required'
                        },
                        stringLength: {
                            min: 1,
                            max: 255,
                            message: 'String Lengeth Must be 255'
                        },
                        regexp: {
                            regexp: /^[a-zA-Z0-9_+-]+$/,
                            message: 'The departmentAlias can only consist of alphabetical, number and underscore'
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
                 transformer: new FormValidation.plugins.Transformer({
                    departmentName: {
                        notEmpty: function(field, element, validator) {
                            // Get the field value
                            const value = element.value;
        
                            // Remove the spaces from beginning and ending
                            return value.trim();
                        },
                    },
                    departmentAlias: {
                        notEmpty: function(field, element, validator) {
                            // Get the field value
                            const value = element.value;
        
                            // Remove the spaces from beginning and ending
                            return value.trim();
                        },
                    },
                }),
            },
        }).on('core.form.valid', function() {
            $(".overlayAjaxStatus .status").text("Please Wait...");
            $(".overlayAjaxStatus").fadeIn();
            var DatatoUpload = new FormData();

            if( window.FormData === undefined )
            {  
                Swal.fire({
                    title: '<strong>Error</strong>',
                    type: 'error',
                    text:"Sorry you browser Dose not support Form Data, Please use latest browser (suggestion:Google Chrome) "
                })
                 return;
            }
            DatatoUpload.append("d_name",$("#addDepartmentForm input[name='departmentName']").val());
            DatatoUpload.append("d_alias",$("#addDepartmentForm input[name='departmentAlias']").val());
            DatatoUpload.append("_token",$("#addDepartmentForm input[type='hidden']").val());
           
           $.ajax({
                type: "post",
                url: $("#addDepartmentForm").attr("action"),
                data:DatatoUpload,
                contentType: false,
                processData: false,
                success: function   (response) {
                    // response = JSON.parse(response)
                    if(response.status)
                    {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                        Toast.fire({
                            type: 'success',
                            title: response.message
                        });
                        setTimeout(function(){
                            $("#addDepartmentForm")[0].reset();
                            fv.resetForm(true);
                          },1500);
                    }
                    else{
                        
                    $(".overlayAjaxStatus").fadeOut();
                        Swal.fire({
                            title: '<strong>Error</strong>',
                            type: 'error',
                            text:response.message
                        })
                    }
                    setTimeout(function(){
                        $(".overlayAjaxStatus").fadeOut();
                      },1500);
                },

                error:function(e){
                    if(e.responseText.includes("Unauthenticed")){
                        location.reload();
                    }
                    else
                    { 
                        $(".overlayAjaxStatus").fadeOut();
                        Swal.fire({
                            title: '<strong>Error</strong>',
                            type: 'error',
                            text:"See Console"
                        })
                        console.log(e.responseText);
                    }
                }
           });
        });
    
    
        
    });
    