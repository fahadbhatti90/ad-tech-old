document.addEventListener('DOMContentLoaded', function(e) {

    var t = $('#accountTable').DataTable( {
        "ordering": false,
        "scrollX": true
    });//end datatable function
    $('.tooltip').tooltipster({
        interactive:true,
        maxWidth:300
     });//end toottipster 
    $('#addScheduleFormModel').on('hidden.bs.modal', function (e) {
         $("#addAccountForm").get(0).reset();
         $(".addClientModel .modal-title").text("Add Account");
         ($("#addAccountForm label.custom-file-label").text("Choose File"));
         $("table tbody").children('tr').removeClass("onEdit");
         $("#addAccountForm .form-control").removeClass("editing");
         if($clientID != null){
             $clientID = null;
             $("#addAccountForm .form-group").show();
            //  $("#addAccountForm .passwordParent").html(PasswordElementForEdit)
         }
        fv.resetForm(true);
      })//end on function

    const form = document.getElementById('addAccountForm');
    var $clientID = opType = null;
    const randomNumber = function(min, max) {
        return Math.floor(Math.random() * (max - min + 1) + min);
    };
    const fv = FormValidation.formValidation(form, {
            fields: {
                
                clientId: {
                    validators: {
                        notEmpty: {
                            message: 'This field is required'
                        },
                        choice: {
                            min: 1,
                            max: 1,
                            message: 'Please Select One to continue'
                        }
                    }
                },//end
                amsProfile: {
                    validators: {
                        choice: {
                            min: 0,
                            message: 'Please Select One to continue'
                        }
                    }
                },//end
                sellerId: {
                    validators: {
                        choice: {
                            min: 0,
                            message: 'Please Select One to continue'
                        }
                    }
                },//end
                vendorId: {
                    validators: {
                        choice: {
                            min: 0,
                            message: 'Please Select One to continue'
                        }
                    }
                },//end
            },
            plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                bootstrap: new FormValidation.plugins.Bootstrap(),
                submitButton: new FormValidation.plugins.SubmitButton(),
                excluded: new FormValidation.plugins.Excluded(),
                icon: new FormValidation.plugins.Icon({
                    valid: 'fa fa-check',
                    invalid: 'fa fa-times',
                    validating: 'fa fa-refresh',
                }),
            },
        }).on('core.form.valid', function() {
            $(".accountSubmitButton").attr("disabled","disabled");
            $(".accountSubmitButton").html(
                '<img src='+base_url+'"/public/images/inlinepreloader.gif" class="inlinePreLoader" />'
            );
            var DatatoUpload = new FormData();

            if( window.FormData === undefined )
            {  
                $(".accountSubmitButton").removeAttr("disabled");
                $(".accountSubmitButton").html(
                    'Save'
                );
                Swal.fire({
                    title: '<strong>Error</strong>',
                    type: 'error',
                    text:"Sorry you browser Dose not support Form Data, Please use latest browser (suggestion:Google Chrome)"
                })
                 return;
            }
            amsProfile = $("#addAccountForm #amsProfile").val();
            sellerId = $("#addAccountForm #sellerId").val();
            vendorId = $("#addAccountForm #vendorId").val();
           
            if(
                amsProfile == "" &&
                sellerId == "" &&
                vendorId == ""
             ){
                $(".accountSubmitButton").removeAttr("disabled");
                $(".accountSubmitButton").html(
                    'Save'
                );
                // $(".accountSubmitButton").hide();
                Swal.fire({
                    title: 'Sorry!',
                    type: 'info',
                    text:"Nothing to associate"
                })
                 return;
             }
            DatatoUpload.append("amsProfile",amsProfile);
            DatatoUpload.append("sellerId",sellerId);
            DatatoUpload.append("vendorId",vendorId);
            DatatoUpload.append("clientId",$("#addAccountForm #clientId").val());
            DatatoUpload.append("_token",$("#addAccountForm input[type='hidden']").val());
            
            if($clientID == null){
                DatatoUpload.append("opType",1);
            }
            else{
                DatatoUpload.append("id",$clientID);
                DatatoUpload.append("opType",opType);
            }
            $.ajax({
                type: "post",
                url: $("#addAccountForm").attr("action"),
                data:DatatoUpload,
                contentType: false,
                processData: false,
                success: function   (response) {
                    console.log(response);
                 
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
                            $(".accountSubmitButton").removeAttr("disabled");
                            $(".accountSubmitButton").html(
                                'Save'
                            );
                            $("#addAccountForm")[0].reset();
                            fv.resetForm(true); 
                            $("#addAccountForm label.custom-file-label").text("Choose File");
                            window.location.reload();
                          },1500);
                       
                    }
                    else{
                        $(".accountSubmitButton").removeAttr("disabled");
                        $(".accountSubmitButton").html(
                            'Save'
                        );
                        $mess = "";
                        $.each(response.message, function (indexInArray, valueOfElement) { 
                            $mess += valueOfElement + "\n";
                        });
                        Swal.fire({
                            title: '<strong>Error</strong>',
                            type: 'error',
                            html:$mess.replace(/\n/g, "<br/>")
                        })
                    }
                    setTimeout(function(){
                        $(".accountSubmitButton").removeAttr("disabled");
                        $(".accountSubmitButton").html(
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
                        $(".accountSubmitButton").removeAttr("disabled");
                            $(".accountSubmitButton").html(
                                'Save'
                            );
                        Swal.fire({
                            title: '<strong>Error</strong>',
                            type: 'error',
                            text:"See Console"
                        })
                        console.log("Some Internal Errors"+e.responseText);
                        
                    }
                    
                }
           });
        });
        // deleteConfirmationBoxConfirmButtonOnClick
        $("body").on("click", ".deleteConfirmationBoxConfirmButton", function (e) {
            e.preventDefault();
            alert("working click");
        });

        /**************************Delete functionality***********************/
        $("table tbody").on('click','i.fa-trash',function (e) {
            row = $(this).parents("tr");
            $(row).parent().children('tr').removeClass("onEdit");
            $(row).addClass("onEdit");
            rowID = $(this).parents("tr").attr("id");
           
            csrf = $("table").attr('csrf');
            console.log(base_url+'/accounts/'+rowID+'/deleteAccount/')
            Swal.fire({
                title: '<h5 class="modal-title w-100 text-center text-primary" id="staticDeleteModalLabel">Are You Sure?</h5>',
               /* type:"warning",*/
                /*text:"You want to unassociate this account!",*/
                html:
                    '<p class="text-center deletePopupText">You want to unassociate this account!?</p>',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-secondary'
                },
                padding: '1em',
                position: 'top',
                showCancelButton: true,
                confirmButtonText: 'Continue',
                preConfirm: (ddetails) => {
                    $(".deleteConfirmationBoxConfirmButton").addClass("deleteConfirmationBoxConfirmButtonOnClick");
                    return fetch(base_url+'/accounts/'+rowID+'/deleteAccount/', {
                        method: 'DELETE',
                        headers: {
                            'Content-type': 'application/json; charset=UTF-8' // Indicates the content 
                           },
                        body:JSON.stringify({'_token':_token})
                      })
                      .then(response =>{
                        if (!response.ok) {
                            console.log(response.body);
                            throw new Error(response.statusText)
                          }            
                        return response.json()
                      } ) // OR res.text()
                      .catch(error => {
                          
                    $(".deleteConfirmationBoxConfirmButton").removeClass("deleteConfirmationBoxConfirmButtonOnClick");
                          console.log(error);
                        Swal.showValidationMessage(
                          `Request failed: ${error}`
                        )
                      })
                  },
                  allowOutsideClick: () => !Swal.isLoading(),
                  
                }).then((result) => {
                    $(".deleteConfirmationBoxConfirmButton").removeClass("deleteConfirmationBoxConfirmButtonOnClick");
                  
                    if(result.value.status !="fail"){
                        /*delete response started*/
                        if (result.value.status) {
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

                            t.row(row).remove().draw(false);
                            location.reload();
                        } else {
                            /* $mess = "";
                             $.each(result.message, function (indexInArray, valueOfElement) {
                                 $mess += valueOfElement + "\n";
                             });*/
                            Swal.fire({
                                title: '<strong>Error</strong>',
                                type: 'error',
                                html: result.value.message
                            })
                        }
                        /*const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
        
                        Toast.fire({
                            type: 'success',
                            title: result.value.message
                        }); 
                        
                        t.row(row).remove().draw(false);
                        location.reload();*/
                    }else{
                        Swal.showValidationMessage(
                            `Request failed: ${result.value.title}`
                          )
                        // Swal.fire({
                        //     title: '<strong>'+result.value.title+'</strong>',
                        //     type: 'error',
                        //     text:result.value.message
                        // })
                    }
                })
        });//end function
        /**************************Delete functionality***********************/
        /**************************Edit functionality***********************/
        $("table tbody").on('click','i.fa-edit',function (e) {
            row = $(this).parents("tr");
            //Marking row
            $(row).parent().children('tr').removeClass("onEdit");
            $(row).addClass("onEdit");
            //Marking row
            rowID = $(this).parents("tr").attr("id");
            $clientID = rowID;
            opType = 2;
            $tds = row.find("td");
            $name = $($tds[1]).text().trim();
            $email = $($tds[2]).text().trim();
            $("#addAccountForm #clientName").val($name);
            $("#addAccountForm #clientName").addClass("editing");
            $("#addAccountForm #clientEmail").val($email);
            $("#addAccountForm #clientEmail").addClass("editing");
            $(".addClientModel .modal-title").text("Update client "+$name.toLowerCase()+" with ID#"+rowID);    
            $('.addClientModel').modal('show');   
             $("#addAccountForm .passwordParent").hide();
            // $("#addAccountForm .passwordParent").html('');
        });//end function
        /**************************Edit functionality***********************/
        /**************************Change Password funcitonality***********************/
        $("table tbody").on('click','i.fa-key',function (e) {
            row = $(this).parents("tr");
            //Marking row
            $(row).parent().children('tr').removeClass("onEdit");
            $(row).addClass("onEdit");
            //Marking row
            rowID = $(this).parents("tr").attr("id");
            $clientID = rowID;
            opType = 3;
            $tds = row.find("td");
            $name = $($tds[1]).text().trim();
            $(".addClientModel .modal-title").text("Change password of client "+$name.toLowerCase()+" With ID#"+rowID);    
            $('.addClientModel').modal('show'); 
             $("#addAccountForm .form-group").hide();
             $("#addAccountForm .passwordParent").show();
            // $("#addAccountForm .passwordParent").html('');
        });//end function
        /**************************Change Password functionality***********************/
      
    });//end ready function
