
//for radio button as they were causing issues
$("#assinScrapingForm input[name='daily-instant[]']").click(function(){
    $("#assinScrapingForm input[name='daily-instant[]']").removeAttr("checked");
    $(this).attr("checked","checked");
})


document.addEventListener('DOMContentLoaded', function(e) {

    $('#addCollectionFormModel').on('hidden.bs.modal', function (e) {
        $("#assinScrapingForm").get(0).reset();
        
        ($("#assinScrapingForm label.custom-file-label").text("Choose ASINs File"));
       fv.resetForm(true);
     })
    $("#assinScrapingForm input[type='file']").on("change",function (e) { 
        if($(this).get(0).files.length <= 0){
            ($(this).parent().find("label.custom-file-label ").text("Choose ASINs File"));
            return;
        }
        fileName = $(this).get(0).files[0].name;
        
       
        ($(this).parent().find("label.custom-file-label ").text(fileName));
         
     });
    const form = document.getElementById('assinScrapingForm');
    const fv = FormValidation.formValidation(form, {
            fields: {
                excel: {
                    validators: {
                        notEmpty: {
                            message: 'The File is required'
                        },
                        file: {
                            extension: 'xlsx,xls,csv',
                            type: '.csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel',
                            message: 'Please choose a CSV or excel file'
                        }
                    }
                },
                colectionName: {
                    validators: {
                        notEmpty: {
                            message: 'This field is required'
                        }, 
                        stringLength: {
                            min: 1,
                            max: 99,
                            message: 'Collection name must not be greater than 100 characters'
                        },
                        regexp: {
                            regexp: /^[a-zA-Z0-9_ ]+$/,
                            message: 'The colectionName can only consist of alphabetical, number and underscore'
                        }
                    }
                },
             
                'agreements[]': {
                    validators: {
                        choice: {
                            min: 1,
                            max: 1,
                            message: 'Please check all agreements to continue'
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
                    colectionName: {
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
            var DatatoUpload = new FormData();
            $(".collectionSubmitButton").attr("disabled","disabled");
            $(".collectionSubmitButton").html(
                '<img src="'+base_url+'/public/images/inlinepreloader.gif" class="inlinePreLoader" />'
            );
            if( window.FormData === undefined )
            {  
                $(".collectionSubmitButton").removeAttr("disabled");
                $(".collectionSubmitButton").html(
                    'Save'
                );
                Swal.fire({
                    title: '<strong>Error</strong>',
                    type: 'error',
                    text:"Sorry you browser Dose not support Form Data, Please use latest browser (suggestion:Google Chrome) "
                })
                 return;
            }
            DatatoUpload.append("scrapType",$(" input[name='daily-instant[]']:checked").val());
            DatatoUpload.append("colectionName",$("#assinScrapingForm input[name='colectionName']").val());
            DatatoUpload.append("excel",$("#assinScrapingForm input[type='file']").get(0).files[0]);
            DatatoUpload.append("_token",$("#assinScrapingForm input[type='hidden']").val());
           
           $.ajax({
                type: "post",
                url: $("#assinScrapingForm").attr("action"),
                data:DatatoUpload,
                contentType: false,
                processData: false,
                success: function   (response) {
                     response = JSON.parse(response)
                 
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
                            $(".collectionSubmitButton").removeAttr("disabled");
                            $(".collectionSubmitButton").html(
                                'Save'
                            );
                            $("#assinScrapingForm")[0].reset();
                            $("#assinScrapingForm label.custom-file-label").text("Choose ASINs File");
                            fv.resetForm(true);
                          },1500);
                        if(response.collection_type != null){
                            $.ajax({
                                type: "get",
                                url: $("body").attr("base_url")+"/scrap/ScrapData",
                                data:{
                                    'c_id':response.collection_type
                                },
                                success: function (response) {
                                    console.log(response);
                                },error:function(e){
                                   console.log(e.responseText);
                                }
                            });
                        }
                       
                      
                    }
                    else{
                        $(".collectionSubmitButton").removeAttr("disabled");
                        $(".collectionSubmitButton").html(
                            'Save'
                        );
                        Swal.fire({
                            title: '<strong>Error</strong>',
                            type: 'error',
                            text:response.message
                        })
                    }
                    setTimeout(function(){
                        $(".collectionSubmitButton").removeAttr("disabled");
                        $(".collectionSubmitButton").html(
                            'Save'
                        );
                      },1500);
                  
                },

                error:function(e){ 
                    if(e.responseText.includes("Unauthenticed")){
                        location.reload();
                    }
                    else
                    {    
                        $(".collectionSubmitButton").removeAttr("disabled");
                        $(".collectionSubmitButton").html(
                            'Save'
                        );
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
    