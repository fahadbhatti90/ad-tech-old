
document.addEventListener('DOMContentLoaded', function(e) {
    $("#proxyUploadForm input[type='file']").on("change",function (e) {
        $fileName = $(this).get(0).files[0].name;
        ($(this).parent().find("label.custom-file-label").text($fileName));

    });
    $('.tooltip').tooltipster({
        interactive:true,
        maxWidth:500
    });
    $("input[name='proxy_search_box']").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $(".proxyTable tbody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
    window.setTimeout(function () {
        $(".alert").remove();
    }, 2000);
    const form = document.getElementById('proxyUploadForm');
    const fv = FormValidation.formValidation(form, {
        fields: {
            proxy: {
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

        $(".proxyUploadCard .overlayAjaxStatus").fadeIn();
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
        DatatoUpload.append("proxy",$("#proxyUploadForm input[type='file']").get(0).files[0]);
        DatatoUpload.append("_token",$("#proxyUploadForm input[type='hidden']").val());

        $.ajax({
            type: "post",
            url: $("#proxyUploadForm").attr("action"),
            data:DatatoUpload,
            contentType: false,
            processData: false,
            success: function   (response) {
                console.log(response);
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
                        $("#proxyUploadForm")[0].reset();
                        fv.resetForm(true);
                        location.reload();
                    },1500);
                }
                else{

                    $(".proxyUploadCard .overlayAjaxStatus").fadeOut();
                    Swal.fire({
                        title: '<strong>Error</strong>',
                        type: 'error',
                        text:response.message
                    })
                }
                setTimeout(function(){
                    $(".proxyUploadCard .overlayAjaxStatus").fadeOut();
                },1500);
            },

            error:function(e){
                if(e.responseText.includes("Unauthenticed")){
                    location.reload();
                }
                else
                { 
                    $(".proxyUploadCard .overlayAjaxStatus").fadeOut();
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
    