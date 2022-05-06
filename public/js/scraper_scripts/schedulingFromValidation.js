
document.addEventListener('DOMContentLoaded', function(e) {
    var t = $('#asinCronTable').DataTable( {
        "ordering": false,
        "scrollX": true
    });

    $('.tooltip').tooltipster({
        interactive:true,
        maxWidth:300
    });
    $('#addScheduleFormModel').on('hidden.bs.modal', function (e) {
        $("#schedulingFrom").get(0).reset();
        ($("#schedulingFrom label.custom-file-label").text("Choose File"));
        fv.resetForm(true);
    })
    const form = document.getElementById('schedulingFrom');
    const fv = FormValidation.formValidation(form, {
        fields: {
            crontype: {
                validators: {
                    notEmpty: {
                        message: 'This field is required'
                    },
                    choice: {
                        min: 1,
                        max: 1,
                        message: 'Please Select One Cron Type to continue'
                    }
                }
            },
            duration: {
                validators: {
                    notEmpty: {
                        message: 'This field is required'
                    },
                    choice: {
                        min: 1,
                        max: 1,
                        message: 'Please Select Duration to continue'
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
    }).on('core.form.valid', function() {
        $(".asinScheduleSubmitButton").attr("disabled","disabled");
        $(".asinScheduleSubmitButton").html(
            '<img src="'+base_url+'/public/images/inlinepreloader.gif" class="inlinePreLoader" />'
        );
        var DatatoUpload = new FormData();

        if( window.FormData === undefined )
        {
            $(".asinScheduleSubmitButton").removeAttr("disabled");
            $(".asinScheduleSubmitButton").html(
                'Save'
            );
            Swal.fire({
                title: '<strong>Error</strong>',
                type: 'error',
                text:"Sorry you browser Dose not support Form Data, Please use latest browser (suggestion:Google Chrome) "
            })

            return;
        }
        var cronStatus = $(" input[name='cronstatus']:checked").val();
        var crontype = $("#schedulingFrom #crontype").val();
        var cronduration = $("#schedulingFrom #duration").val();

        DatatoUpload.append("cronstatus",cronStatus);
        DatatoUpload.append("crontype",crontype);
        DatatoUpload.append("cronduration",cronduration);
        DatatoUpload.append("_token",$("#schedulingFrom input[type='hidden']").val());

        $.ajax({
            type: "post",
            url: $("#schedulingFrom").attr("action"),
            data:DatatoUpload,
            contentType: false,
            processData: false,
            success: function (response) {
                console.log(response);
                if(response.status =="success")
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
                        $("#schedulingFrom")[0].reset();
                        resetEditSettings();
                        fv.resetForm(true);
                        $(".asinScheduleSubmitButton").removeAttr("disabled");
                        $(".asinScheduleSubmitButton").html(
                            'Save'
                        );
                        location.reload();
                        // if(response.isEdited){
                        //     $(".cronList tr#"+response.cron_id).remove();
                        //     if($(".cronList tr").length > 0){
                        //         $(".cronList tr:nth-child(1)").before(response.newRow);
                        //     }else{
                        //         $(".cronList").append(response.newRow);
                        //     }
                        // }else{
                        //     if($(".cronList tr").length > 0){
                        //         $(".cronList tr:nth-child(1)").before(response.newRow);
                        //     }else{
                        //         $(".cronList").append(response.newRow);
                        //     }
                        // }
                    },1500);
                }
                else{
                    $(".asinScheduleSubmitButton").removeAttr("disabled");
                    $(".asinScheduleSubmitButton").html(
                        'Save'
                    );
                    Swal.fire({
                        title: '<strong>Error</strong>',
                        type: 'error',
                        text:response.message
                    })
                }
                setTimeout(function(){

                },1500);
            },
            error:function(e){
                if(e.responseText.includes("Unauthenticed")){
                    location.reload();
                }
                else
                { 
                    $(".asinScheduleSubmitButton").removeAttr("disabled");
                    $(".asinScheduleSubmitButton").html(
                        'Save'
                    );
                    Swal.fire({
                        title: '<strong>Error</strong>',
                        type: 'error',
                        text:"Some thing went wrong"
                    })
                    console.log(e.responseText);
                }
            }
        });
    });
    function resetEditSettings(){
        $("#crontype option").removeAttr("selected");
        $("input[name='cronstatus']").removeAttr("checked");
        $('#customRadio1').attr('checked','checked');
        $("#crontime option:nth-child(1),#crontype option:nth-child(1)").attr("selected");
        $("form#schedulingFrom .form-control,.cronList tr").removeClass("onEdit");
        $("form#schedulingFrom button[type='reset']").hide();
        $("form#schedulingFrom").attr("action_type",1);
    }

    var rowID = null;

    $(".cronList").on('click','i.fa-trash',function (e) {
        row = $(this).parents("tr");
        $(row).parent().children('tr').removeClass("onEdit");
        $(row).addClass("onEdit");
        rowID = $(this).parents("tr").attr("id");
        console.log(base_url+'/scrap/DeleteSchedual/'+rowID);
        url = base_url+'/scrap/DeleteSchedual/'+rowID;
        csrf = $(".cronListTable").attr('csrf');

        Swal.fire({
            title: '<strong>Are you sure?</strong>',
            type:"warning",
            text:"You won't be able to revert this!",
            customClass: {
                confirmButton: 'deleteConfirmationBoxConfirmButton',
            },
            showCloseButton: true,
            showCancelButton: true,
            focusConfirm: false,
            focusCancel:true,
            confirmButtonText: 'Confirm',
            showLoaderOnConfirm: true,
            preConfirm: (ddetails) => {
            $(".deleteConfirmationBoxConfirmButton").addClass("deleteConfirmationBoxConfirmButtonOnClick");
        return fetch(url, {
            method: 'DELETE',
            headers: {
                'Content-type': 'application/json; charset=UTF-8' // Indicates the content
            },
            body:JSON.stringify({'_token':_token})
        })
            .then(response =>{
            return response.json()
        } )  // OR res.text()
    .catch(error => {

            $(".deleteConfirmationBoxConfirmButton").removeClass("deleteConfirmationBoxConfirmButtonOnClick");
        console.log(error);
        Swal.showValidationMessage(
            `Request failed: ${error}`
        )
    })
    },
        allowOutsideClick: () => !Swal.isLoading(),
            onClose:()=>{
            $(row).parent().children('tr').removeClass("onEdit");
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
            t.row(row).remove().draw(false);
        }else{

            console.log(result.value.message);
            Swal.fire({
                title: '<strong>'+result.value.title+'</strong>',
                type: 'error',
                text:"Please See Console"
            })
        }
    })
    });//end function

    $("#scheduleTime").change(function (e) {
        e.preventDefault();
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
        $val = $(this).val().split("|");
        $time = $val[0];
        $setting_id = $val[1];
        DatatoUpload.append("scheduleTime",$time);
        DatatoUpload.append("setting_id",$setting_id);
        DatatoUpload.append("_token",$("#scheduleTimeForm input[type='hidden']").val());
        $.ajax({
            type: "post",
            url: $("#scheduleTimeForm").attr("action"),
            data:DatatoUpload,
            contentType: false,
            processData: false,
            success: function (response) {
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
                    $(".overlayAjaxStatus").fadeOut();
                }
                else{
                    if(response.error500){
                        console.log(response.exception)
                    }
                    $(".overlayAjaxStatus").fadeOut();
                    Swal.fire({
                        title: '<strong>Error</strong>',
                        type: 'error',
                        text:response.message
                    })
                }

            },
            error:function(e){
                $(".overlayAjaxStatus").fadeOut();
                console.log(e.responseText);
            }
        });
    });


});//end load function
    