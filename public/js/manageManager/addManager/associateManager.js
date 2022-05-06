

document.addEventListener('DOMContentLoaded', function(e) {
    $('#associateBrandModel').on('hidden.bs.modal', function (e) {
        //alert('test');
        $("#associateManager").get(0).reset();
        $("#selectedUsers").select2('val', '[]');
        $(".associateBrandModel .modal-title").text("Reassign Brand And Delete User");
        $("table tbody").children('tr').removeClass("onEdit");
        $("#associateManager .form-control").removeClass("editing");
        if($clientID != null){
            $clientID = null;
        }
        fv2.resetForm(true);
    })//end on function

    const form = document.getElementById('associateManager');
    //const passwordMeter = document.getElementById('passwordMeter');
    const selectedUsersField = jQuery(form.querySelector('[name="selectedUsers"]'));
    var $clientID = opType = null;
    var PasswordElementForEdit= null
    const randomNumber = function(min, max) {
        return Math.floor(Math.random() * (max - min + 1) + min);
    };
    const fv2 = FormValidation.formValidation(form, {
        fields: {


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
        var selectedUsers = $("#associateManager #selectedUsers").val();
        var assignedBrandToOtherIds = $("#associateManager #assignedBrandToOtherIds").val();
        var deleteUserId = $("#associateManager #deleteUserId").val();
        DatatoUpload.append("deleteUserId", deleteUserId);
        DatatoUpload.append("assignedBrandToOtherIds", assignedBrandToOtherIds);
        DatatoUpload.append("selectedUsers", selectedUsers);
        DatatoUpload.append("_token", $("body").attr("csrf"));
        if($clientID == null){
            DatatoUpload.append("opType",1);
        }
        else{
            DatatoUpload.append("id",$clientID);
            DatatoUpload.append("opType",opType);
        }
        $.ajax({
            type: "post",
            url: $("#associateManager").attr("action"),
            data:DatatoUpload,
            contentType: false,
            processData: false,
            success: function   (response) {
                if(response.status)
                {
                    //alert(response.status);
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
                        $("#associateManager")[0].reset();
                        fv2.resetForm(true);
                        $("#associateManager label.custom-file-label").text("Choose File");
                        window.location.reload();
                    },1500);

                }
                else{
                    //alert('status2');
                    $(".ClientSubmitButton").removeAttr("disabled");
                    $(".ClientSubmitButton").html(
                        'Continue'
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
                console.log("So:me Internal Errors"+e.responseText);
            }
        });
    });
    selectedUsersField
        .select2()
        .on('change.select2', function () {
            // Revalidate the color field when an option is chosen
            fv2.revalidateField('selectedUsers');
        });


});//end ready function
