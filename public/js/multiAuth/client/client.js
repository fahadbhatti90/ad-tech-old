

document.addEventListener('DOMContentLoaded', function(e) {
    $('.js-example-basic-multiple').select2({
        placeholder: 'Choose',
    });

    var t = $('#addClientTable').DataTable( {
        "ordering": false,
        "scrollX": true
    });//end datatable function
    $('.tooltip').tooltipster({
        interactive:true,
        maxWidth:300
    });//end toottipster
    $('#addProductPreviewModel').on('show.bs.modal', function (e) {
        if (opType == null) {
            fv.resetForm(true);
        }
    });
    $('#addScheduleFormModel').on('hidden.bs.modal', function (e) {
        fv.resetForm(true);
        //alert('test');
       // $("#addClientForm").get(0).reset();
        $("#addClientForm").get(0).reset();
        $("#selectedUsers").select2('val', '[]');

         $(".addClientModel .modal-title").text("Add Brand");
         ($("#addClientForm label.custom-file-label").text("Choose File"));
         //$("#passwordMeter").css("width","0");
         //$("table tbody").children('tr').removeClass("onEdit");
         $("#addClientForm .form-control").removeClass("editing");
         if($clientID != null){
             $clientID = null;
             $("#addClientForm .form-group").show();
             //$("#addClientForm .passwordParent > label").show()
         }
       // fv.resetForm(true);
    })//end on function


    $("#addClientForm input[type='file']").on("change",function (e) { 
        if($(this).get(0).files.length <= 0){
      
            ($(this).parent().find("label.custom-file-label").text("Choose File"));
            return;
        }
        $fileName = $(this).get(0).files[0].name;
        
         ($(this).parent().find("label.custom-file-label").text($fileName));
         
     });
    const form = document.getElementById('addClientForm');
    const selectedUsersField = jQuery(form.querySelector('[name="selectedUsers"]'));
    //const passwordMeter = document.getElementById('passwordMeter');
    var $clientID = opType = null;
   // var PasswordElementForEdit= null
    const randomNumber = function(min, max) {
        return Math.floor(Math.random() * (max - min + 1) + min);
    };
    const fv = FormValidation.formValidation(form, {
            fields: {
                clientName: {
                    validators: {
                        notEmpty: {
                            message: 'This field is required'
                        }, 
                        stringLength: {
                            min: 1,
                            max: 199,
                            message: 'Name must be less than 200 Characters'
                        },
                        regexp: {
                            regexp: /^[a-zA-Z0-9_ ]+$/,
                            message: 'The name can only consist of alphabetical, number and underscore'
                        }
                    }
                },//end
                clientEmail: {
                    validators: {
                        notEmpty: {
                            message: 'This field is required'
                        }, 
                        stringLength: {
                            min: 1,
                            max: 199,
                            message: 'Email must be less than 200 Characters'
                        },
                    regexp: {
                        regexp: /^[^@\s]+@([^@\s]+\.)+[^@\s]+$/     ,
                        message: 'The value is not a valid email address'
                    },
                        /*emailAddress: {
                            message: 'The value is not a valid email address'
                        }*/
                    }
                },//end
                selectedUsers: {
                    validators: {
                        callback: {
                            message: 'Please choose any option',
                            callback: function (input) {
                                // Get the selected options
                                const options = selectedUsersField.select2('data');
                                return (options != null && options.length >= 1);
                            }
                        }
                    }
                },
                /*agency: {
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
                },*///end
                /*password: {
                    validators: {
                        notEmpty: {
                            message: 'The password is required'
                        },
                    }
                },*///end
                
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
                 transformer: new FormValidation.plugins.Transformer({
                    clientName: {
                        notEmpty: function(field, element, validator) {
                            // Get the field value
                            const value = element.value;
        
                            // Remove the spaces from beginning and ending
                            return value.trim();
                        },
                    },
                    clientEmail: {
                        notEmpty: function(field, element, validator) {
                            // Get the field value
                            const value = element.value;
        
                            // Remove the spaces from beginning and ending
                            return value.trim();
                        },
                    },
                }),//end
            },
        }).on('core.form.valid', function() {
            $(".ClientSubmitButton").attr("disabled","disabled");
            $(".ClientSubmitButton").html(
                '<img src="'+base_url+'/public/images/inlinepreloader.gif" class="inlinePreLoader" />'
            );
            var DatatoUpload = new FormData();

            if( window.FormData === undefined )
            {  
                $(".ClientSubmitButton").removeAttr("disabled");
                $(".ClientSubmitButton").html(
                    'Save'
                );
                Swal.fire({
                    title: '<strong>Error</strong>',
                    type: 'error',
                    text:"Sorry you browser Dose not support Form Data, Please use latest browser (suggestion:Google Chrome)"
                })
                 return;
            }
            var selectedUsers = $("#addClientForm #selectedUsers").val();
            //DatatoUpload.append("agency",$("#addClientForm #agency").val());
            DatatoUpload.append("clientName",$("#addClientForm #clientName").val());
            DatatoUpload.append("clientEmail",$("#addClientForm #clientEmail").val());
            DatatoUpload.append("selectedUsers", selectedUsers);
            //DatatoUpload.append("password",$("#addClientForm #password").val());
            DatatoUpload.append("_token",$("#addClientForm input[type='hidden']").val());
            
            if($clientID == null){
                DatatoUpload.append("opType",1);
            }
            else{
                DatatoUpload.append("id",$clientID);
                DatatoUpload.append("opType",opType);
            }
            $.ajax({
                type: "post",
                url: $("#addClientForm").attr("action"),
                data:DatatoUpload,
                contentType: false,
                processData: false,
                success: function   (response) {
                   
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
                            $(".ClientSubmitButton").removeAttr("disabled");
                            $(".ClientSubmitButton").html(
                                'Save'
                            );
                            $("#addClientForm")[0].reset();
                            fv.resetForm(true); 
                            $("#addClientForm label.custom-file-label").text("Choose File");
                            window.location.reload();
                          },1500);
                       
                    }
                    else{
                        $(".ClientSubmitButton").removeAttr("disabled");
                        $(".ClientSubmitButton").html(
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
                        $(".ClientSubmitButton").removeAttr("disabled");
                        $(".ClientSubmitButton").html(
                            'Save'
                        );
                      },1500);
                  
                },

                error:function(e){ 
                    if(e.responseText.includes("Unauthenticed")){
                        location.reload();
                    }
                    $(".ClientSubmitButton").removeAttr("disabled");
                        $(".ClientSubmitButton").html(
                            'Save'
                        );
                    Swal.fire({
                        title: '<strong>Error</strong>',
                        type: 'error',
                        text:"Their is some Error"
                    })
                }
           });
        });
    selectedUsersField
        .select2()
        .on('change.select2', function () {
            // Revalidate the color field when an option is chosen
            fv.revalidateField('selectedUsers');
        });

      // deleteConfirmationBoxConfirmButtonOnClick
//  1       $("body").on("click", ".deleteConfirmationBoxConfirmButton", function (e) {
//             e.preventDefault();
//             alert("working click");
//         });

        /**************************Delete functionality***********************/
        $("table tbody").on('click','i.fa-trash',function (e) {
            row = $(this).parents("tr");
            //$(row).parent().children('tr').removeClass("onEdit");
           // $(row).addClass("onEdit");
            rowID = $(this).parents("tr").attr("id");
           
            csrf = $("table").attr('csrf');
            Swal.fire({
                title: '<h5 class="modal-title w-100 text-center text-primary" id="staticDeleteModalLabel">Are You Sure?</h5>',
                /*type: "warning",*/
                /*text: "Do you really want to delete this record?",*/
                html:
                    '<p class="text-center deletePopupText">Do you really want to delete this record?</p>',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-secondary'
                },
                /* buttonsStyling: false,*/
                padding: '1em',
                position: 'top',
                showCancelButton: true,
                confirmButtonText: 'Continue',
                preConfirm: (ddetails) => {
                    $(".deleteConfirmationBoxConfirmButton").addClass("deleteConfirmationBoxConfirmButtonOnClick");
                    return fetch(base_url+'/ht/'+rowID+'/deleteClient/', {
                        method: 'DELETE',
                        headers: {
                            'Content-type': 'application/json; charset=UTF-8' // Indicates the content 
                           },
                        body:JSON.stringify({'_token':_token})
                      })
                      .then(response =>{
                        if (!response.ok) {
                            throw new Error(response.statusText)
                          }            
                        return response.json()
                      } ) // OR res.text()
                      .catch(error => {
                          
                    $(".deleteConfirmationBoxConfirmButton").removeClass("deleteConfirmationBoxConfirmButtonOnClick");
     
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
            //$(row).parent().children('tr').removeClass("onEdit");
           // $(row).addClass("onEdit");
            //Marking row
            rowID = $(this).parents("tr").attr("id");
            $clientID = rowID;
            opType = 2;
            $tds = row.find("td");
            $name = $($tds[1]).find("div").attr("name").trim();
            $managers = $($tds[1]).find("div").attr("managers").trim();
            $email = $($tds[2]).find("div").attr("email");

            var selectedValues = $managers.split(',');
            //$("#selectedUsers").select2('val', '[1,2,3]');
            //$("#selectedUsers").select2('val', selectedValues);
            $('#selectedUsers').val(selectedValues).trigger('change');
            //$('#reportType').val(response.selectedReportTypesArray).trigger('change');
            //$agencyId = $($tds[3]).attr("id").trim();
            $("#addClientForm #clientName").val($name);
            // $("#addClientForm #clientName").addClass("editing");
            $("#addClientForm #clientEmail").val($email);
            // $("#addClientForm #clientEmail").addClass("editing");
            //$("#addClientForm #agency").prop('selectedIndex',$('select option[value='+$agencyId+']').index());
            // $("#addClientForm #agency").addClass("editing");
            $(".addClientModel .modal-title").text("Update brand with ID#"+rowID);    
            $('.addClientModel').modal('show');   
             //$("#addClientForm .passwordParent").hide();
            // $("#addClientForm .passwordParent").html('');
        });//end function
        /**************************Edit functionality***********************/
    /**************************Assigned User popup***********************/
    $("table tbody").on('click','i.fa-info-circle',function (e) {
        //alert('test');
        /*row = $(this).parents("tr");
        rowID = $(this).parents("tr").attr("id");
        $clientID = rowID;
        $tds = row.find("td");
        $managersNames = $($tds[1]).find("div").attr("managersNames").trim();*/
       /* alert($managersNames);
        var $managersNamesValues = $managersNames.split(',');
        $('#selectedUsers').val(selectedValues).trigger('change');*/

        row = $(this).parents("tr");
        //Marking row
        //$(row).parent().children('tr').removeClass("onEdit");
        //$(row).addClass("onEdit");
        //Marking row
        rowID = $(this).parents("tr").attr("id");
        $clientID = rowID;
        $tds = row.find("td");
        $managersNames = $($tds[1]).find("div").attr("managersNames").trim();
        if (!$managersNames.trim()) {
            var managersNamesValues = [];
        }else {
            var managersNamesValues = $managersNames.split(',');
        }
        count = 1;
        $("#append_users").empty();
        if (managersNamesValues && managersNamesValues.length > 0) {
            $('<div class="col-lg-12 text-left" >Following users are associated to this brand.</div>').appendTo($('#append_users'));
            $.each(managersNamesValues, function (indexInArray, valueOfElement) {
                $('<div class="col-lg-12 text-left" >' + count + ')' + valueOfElement + '</div>').appendTo($('#append_users'));
                count++
            });
        }else {
            $('<div class="col-lg-12 text-center" >No user assigned to this brand.</div>').appendTo($('#append_users'));
        }
       // alert($managersNamesValues);

        $(".assignedUserModel .modal-title").text("Assigned Users");
        $('.assignedUserModel').modal('show');

    });//end function
    /**************************Assigned User popup***********************/
        /**************************Change Password funcitonality***********************/
        $("table tbody").on('click','i.fa-key',function (e) {
            row = $(this).parents("tr");
            //Marking row
            //$(row).parent().children('tr').removeClass("onEdit");
            //$(row).addClass("onEdit");
            //Marking row
            rowID = $(this).parents("tr").attr("id");
            $clientID = rowID;
            opType = 3;
            $tds = row.find("td");
            $name = $($tds[1]).text().trim();
            $(".addClientModel .modal-title").text("Change password of client with ID#"+rowID);    
            $('.addClientModel').modal('show'); 
             $("#addClientForm .form-group").hide();
             $("#addClientForm .passwordParent > label").hide();
             $("#addClientForm .passwordParent").show();
            // $("#addClientForm .passwordParent").html('');
        });//end function
        /**************************Change Password functionality***********************/
      
    });//end ready function



    var channelLoginStatus = pusher.subscribe('hello-tec-login-status');
    channelLoginStatus.bind('SendClientLoginStatus', function(data) {
        if (!('host' in data) || data.host == "404"){
            // alert("no HOst Found in incoming message");
            return;
        }
       
        var CurrentHost = $("body").attr("host");
        if (CurrentHost == "404") {
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "0",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
              }
              toastr["info"]("Major Host Error","No Host Found in Settings Table Please Contact Your Service Provider till then notifications Module will not display live notfication");
        } else {
            if(!CurrentHost.includes(data.host)){
                // alert(data.host.includes($("body").attr("base_url")));
                return;
            }
        }
       
        // $(".notificationLi").removeClass("notify");
        // $(".notificationLi").addClass("notify");
        $loginStatusParenttag = $('#addClientTable').find("#"+data.id).find(".login_status");
        $loginStatustag = $('#addClientTable').find("#"+data.id).find(".login_status span");
        $currentStatus = ($loginStatusParenttag.attr("status"));
       
        if($currentStatus=="1"){
            $loginStatusParenttag.attr("status","0");
            $loginStatustag.attr("class","loggedOut");
        }
        else{
            $loginStatusParenttag.attr("status","1");
            $loginStatustag.attr("class","loggedIn");
        }
        setTimeout(function(){
            // $(".notificationLi").removeClass("notify");
            // $("#notificationDropdown > span > mark").removeClass("on");
        },1000);
        // channelLoginStatus = pusher.subscribe('hello-tec-channel');
    });
