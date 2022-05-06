

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
        $("#searchRankFrom").get(0).reset();

        ($("#searchRankFrom label.custom-file-label").text("Choose File"));
        fv.resetForm(true);
    })


    $("#searchRankFrom input[type='file']").on("change",function (e) {
        if($(this).get(0).files.length <= 0){

            ($(this).parent().find("label.custom-file-label").text("Choose File"));
            return;
        }
        $fileName = $(this).get(0).files[0].name;

        ($(this).parent().find("label.custom-file-label").text($fileName));

    });
    const form = document.getElementById('searchRankFrom');

    const fv = FormValidation.formValidation(form, {
        fields: {
            crawlName: {
                validators: {
                    notEmpty: {
                        message: 'This field is required'
                    },
                    stringLength: {
                        min: 1,
                        max: 199,
                        message: 'Crawler name must be less than 200'
                    },
                    regexp: {
                        regexp: /^[a-zA-Z0-9_ ]+$/,
                        message: 'The colectionName can only consist of alphabetical, number and underscore'
                    }
                }
            },
            searchTerm: {
                validators: {
                    notEmpty: {
                        message: 'The File is required'
                    },
                    file: {
                        extension: 'csv',
                        type: '.csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel',
                        message: 'Please choose a CSV file'
                    }
                }
            },
            department: {
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
            },
            'frequancy': {
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
                crawlName: {
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

        DatatoUpload.append("crawlName",$("#searchRankFrom input[name='crawlName']").val());
        DatatoUpload.append("searchTerm",$("#searchRankFrom input[type='file']").get(0).files[0]);
        DatatoUpload.append("d_id",$("#searchRankFrom #department").val());
        DatatoUpload.append("frequancy",$("#searchRankFrom #frequancy").val());
        DatatoUpload.append("_token",$("#searchRankFrom input[type='hidden']").val());

        $.ajax({
            type: "post",
            url: $("#searchRankFrom").attr("action"),
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
                        $(".asinScheduleSubmitButton").removeAttr("disabled");
                        $(".asinScheduleSubmitButton").html(
                            'Save'
                        );
                        $("#searchRankFrom")[0].reset();
                        fv.resetForm(true);
                        $("#searchRankFrom label.custom-file-label").text("Choose File");
                        window.location.reload();
                    },1500);

                }
                else{
                    $(".asinScheduleSubmitButton").removeAttr("disabled");
                    $(".asinScheduleSubmitButton").html(
                        'Save'
                    );
                    $mess = "";
                    $.each(response.message, function (indexInArray, valueOfElement) {
                        $mess += valueOfElement + "\n";
                    });
                    Swal.fire({
                        title: '<strong>Error</strong>',
                        type: 'error',
                        text:$mess
                    })
                }
                setTimeout(function(){
                    $(".asinScheduleSubmitButton").removeAttr("disabled");
                    $(".asinScheduleSubmitButton").html(
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
                    $(".asinScheduleSubmitButton").removeAttr("disabled");
                    $(".asinScheduleSubmitButton").html(
                        'Save'
                    );
                    Swal.fire({
                        title: '<strong>Error</strong>',
                        type: 'error',
                        text:"Some Error Occur"
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
    $(".cronList").on('click','i.fa-trash',function (e) {
        row = $(this).parents("tr");
        $(row).parent().children('tr').removeClass("onEdit");
        $(row).addClass("onEdit");
        rowID = $(this).parents("tr").attr("id");

        csrf = $(".cronListTable").attr('csrf');
        Swal.fire({
            title: '<strong>Are you sure?</strong>',
            type:"warning",
            text:"You won't be able to revert this!",
            customClass: {
                confirmButton: 'deleteConfirmationBoxConfirmButton',
            },
            showCancelButton: true,
            confirmButtonText: 'Confirm',
            showLoaderOnConfirm: true,
            preConfirm: (ddetails) => {
            $(".deleteConfirmationBoxConfirmButton").addClass("deleteConfirmationBoxConfirmButtonOnClick");
        return fetch(base_url+'/sr/deleteSearchRankCrawler/'+rowID, {
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
        DatatoUpload.append("SrScheduleTime",$time);
        DatatoUpload.append("setting_id",$setting_id);
        DatatoUpload.append("_token",$("#scheduleTimeForm input[type='hidden']").val());
        $.ajax({
            type: "post",
            url: $("#scheduleTimeForm").attr("action"),
            data:DatatoUpload,
            contentType: false,
            processData: false,
            success: function (response) {
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

});
    