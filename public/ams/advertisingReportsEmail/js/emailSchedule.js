
$(function () {


    //$("#scheduleDate").datepicker({ dateFormat: 'yy-mm-dd' });
    $('.js-example-basic-multiple').select2({
        placeholder: 'Choose',
    });

    //  Time Picker Starts

    // Time Picker Ends


    var t = $('#dataTable').DataTable({
        "ordering": true,
        "processing": true,
        "responsive": true,
    });
    /* $('.datepicker').datepicker({
    autoclose:true,
    orientation:"right auto",
    })
    .on("hide", function(e,date) {
    fv.revalidateField('occurrenceDate');
    });*/
    $('.tooltip').tooltipster({
        interactive: true,
        maxWidth: 300
    });

    $('#addProductPreviewModel').on('show.bs.modal', function (e) {
        if (opType == null) {
            $(".addProductPreviewModel .modal-title").text("Create Schedule");
            $(".asinScheduleSubmitButton").html('Submit');
            fv.resetForm(true);
            /*remove multiple cc starts*/
            $('.multiple_emails-ul li').remove();
            $('#addAdvertisingReportSchedule #cc_emailBS').val('[]');
            $('#addAdvertisingReportSchedule .multiple_emails-container').remove();
            $('#addAdvertisingReportSchedule .multiple_emails-ul li').remove();
            $('#cc_emailBS').multiple_emails({position: "bottom"});
            $('.timeFrameChange').text('days');
            $("#timeFrame").empty();
            $('.timeFrameChange').text('days');
            var i;
            var dayText = 'Days';
            for (i = 1; i < 366; i++) {
                if (i==1){
                    dayText ='Day';
                }else{
                    dayText ='Days';
                }
                $('<option value="' + i + '" >Last ' + i +' '+ dayText + '</option>').appendTo($('#timeFrame'));
            }
            //$("#timeFrame").val(1);

            ccEmailTooltip();
            /*add multiple cc ends*/
        }else{
            $(".addProductPreviewModel .modal-title").text("Update Schedule");
            $(".asinScheduleSubmitButton").html('Update');
        }
    });
    $('#addProductPreviewModel').on('hidden.bs.modal', function (e) {
        //$('.multiple_emails-ul li').remove();
        /*remove multiple emil container starts*/
        //$("#addAdvertisingReportSchedule").trigger('reset');
        /*remove multiple emil container ends*/
        $("#sponsordType").select2('val', '[]');
        $("#reportType").select2('val', '[]');
        $("#timeFrame").select2('val', '[]');
        $("#addAdvertisingReportSchedule").get(0).reset();
        $(".addAdvertisingReportSchedule .modal-title").text("Create Schedule");
        $("table tbody").children('tr').removeClass("onEdit");
        $("#addClientForm .form-control").removeClass("editing");
        if ($scheduleId != null) {
            $scheduleId = opType = null;
            /*add multiple cc starts*/
            $('.multiple_emails-ul li').remove();
            $('#addAdvertisingReportSchedule #cc_emailBS').val('[]');
            $('#addAdvertisingReportSchedule .multiple_emails-container').remove();
            $('#addAdvertisingReportSchedule .multiple_emails-ul li').remove();
            /*add multiple cc ends*/
            $("#addClientForm .form-group").show();
            $("#addClientForm .passwordParent > label").show();

        }
        fv.resetForm(true);
    });
    $(".gj-datepicker").click(function () {
        $(this).find("input").click();
    }).children().on("click", function (e) {
        e.stopPropagation();
    });
    const form = document.getElementById('addAdvertisingReportSchedule');
    const reportTypeField = jQuery(form.querySelector('[name="reportType"]'));
    const sponsordTypeField = jQuery(form.querySelector('[name="sponsordType"]'));
    const timeFrameField = jQuery(form.querySelector('[name="timeFrame"]'));
    var $scheduleId = opType = null;
    const fv = FormValidation.formValidation(form, {
        fields: {
            reportName: {
                validators: {
                    notEmpty: {
                        message: 'This field is required'
                    },
                    stringLength: {
                        min: 1,
                        max: 199,
                        message: 'Report name must be less than 200 Characters'
                    },
                    regexp: {
                        regexp: /^[a-zA-Z0-9_ ]+$/,
                        message: 'Report name can only consist of alphabetical, number and underscore'
                    },
                    regexp: {
                        regexp: /^(?!\s*$).+/,
                        message: 'This field is required'
                    }
                }
            },
            sponsordType: {
                validators: {
                    callback: {
                        message: 'Please choose any option',
                        callback: function (input) {
                            // Get the selected options
                            const options = sponsordTypeField.select2('data');
                            return (options != null && options.length >= 1);
                        }
                    }
                }
            },
            brand: {
                validators: {
                    notEmpty: {
                        message: 'This field is required'
                    },
                    choice: {
                        min: 1,
                        max: 1,
                        message: 'Please select one brand to continue'
                    }
                }
            },
            reportType: {
                validators: {
                    callback: {
                        message: 'Please choose any option',
                        callback: function (input) {
                            // Get the selected options
                            const options = reportTypeField.select2('data');
                            return (options != null && options.length >= 1);
                            // return (options != null && options.length >= 2 && options.length <= 4);
                        }
                    }
                }
            },
            granularity: {
                validators: {
                    notEmpty: {
                        message: 'This field is required'
                    },
                    choice: {
                        min: 1,
                        max: 1,
                        message: 'Please select one option to continue'
                    }
                }
            },
            timeFrame: {
                /*validators: {
                    notEmpty: {
                        message: 'This field is required'
                    },
                    regexp: {
                        regexp: /^[1-9][0-9]*$/,
                        message: 'Enter number greater than 0'
                    }
                }*/
                validators: {
                    callback: {
                        message: 'Please choose any option',
                        callback: function (input) {
                            // Get the selected options
                            const options = timeFrameField.select2('data');
                            return (options != null && options.length >= 1);
                        }
                    }
                }
            },

            'selectDays[]': {
                validators: {
                    notEmpty: {
                        message: 'These fields are required'
                    }
                }
            },
            time: {
                validators: {
                    notEmpty: {
                        message: 'The start time is required'
                    }
                }
            },
            /*ccEmails: {
                validators: {
                    emailAddress: {
                        message: 'The value is not a valid email address'
                    },
                    stringLength: {
                        min: 1,
                        max: 199,
                        message: 'Report name must be less than 200 Characters'
                    },
                }
            },*/
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
        if (opType == null) {
            var btnText = 'Submit';
        }else{
            var btnText = 'Update';
        }
        var btnHtml = '<i class="fas fa-circle-notch fa-spin"></i>';
        $(".asinScheduleSubmitButton").attr("disabled", "disabled");
        $(".asinScheduleSubmitButton").html(btnHtml);
        var DatatoUpload = new FormData();

        if (window.FormData === undefined) {
            $(".asinScheduleSubmitButton").removeAttr("disabled");
            $(".asinScheduleSubmitButton").html(btnText);
            Swal.fire({
                title: '<strong>Error</strong>',
                type: 'error',
                text: "Sorry you browser Dose not support Form Data, Please use latest browser (suggestion:Google Chrome) "
            })

            return;
        }
        var campaignMetricsCheckBox = $("input[name='campaignMetricsCheckBox[]']:checked");
        var selectedcampaignMetricsCheckBox = [];
        $.each(campaignMetricsCheckBox, function () {
            selectedcampaignMetricsCheckBox.push($(this).val());
        });

        var adGroupMetricsCheckBox = $("input[name='adGroupMetricsCheckBox[]']:checked");
        var selectedadGroupMetricsCheckBox = [];
        $.each(adGroupMetricsCheckBox, function () {
            selectedadGroupMetricsCheckBox.push($(this).val());
        });
        var productAdsMetricsCheckBox = $("input[name='productAdsMetricsCheckBox[]']:checked");
        var selectedProductAdsMetricsCheckBox = [];
        $.each(productAdsMetricsCheckBox, function () {
            selectedProductAdsMetricsCheckBox.push($(this).val());
        });
        var keywordMetricsCheckBox = $("input[name='keywordMetricsCheckBox[]']:checked");
        var selectedkeywordMetricsCheckBox = [];
        $.each(keywordMetricsCheckBox, function () {
            selectedkeywordMetricsCheckBox.push($(this).val());
        });
        var asinMetricsCheckBox = $("input[name='asinMetricsCheckBox[]']:checked");
        var selectedAsinMetricsCheckBox = [];
        $.each(asinMetricsCheckBox, function () {
            selectedAsinMetricsCheckBox.push($(this).val());
        });
        var selectDays = $("input[name='selectDays[]']:checked");
        var selectedDays = [];
        $.each(selectDays, function () {
            selectedDays.push($(this).val());
        });
        var reportName = $("#addAdvertisingReportSchedule #reportName").val();
        var timeFrame = $("#addAdvertisingReportSchedule #timeFrame").val();
        var sponsordType = $("#addAdvertisingReportSchedule #reportName").val();
        var brand = $("#addAdvertisingReportSchedule #brand").val();
        var sponsordType = $("#addAdvertisingReportSchedule #sponsordType").val();
        var reportType = $("#addAdvertisingReportSchedule #reportType").val();
        var granularity = $("#addAdvertisingReportSchedule #granularity").val();
        var ccEmails = $("#addAdvertisingReportSchedule #cc_emailBS").val();
        var time = $("#addAdvertisingReportSchedule #time").val();
        DatatoUpload.append("reportName", reportName);
        DatatoUpload.append("timeFrame", timeFrame);
        DatatoUpload.append("sponsordType", sponsordType);
        DatatoUpload.append("brand", brand);
        DatatoUpload.append("sponsordType", sponsordType);
        DatatoUpload.append("reportType", reportType);
        DatatoUpload.append("granularity", granularity);
        DatatoUpload.append("ccEmails", ccEmails);
        DatatoUpload.append("time", time);
        DatatoUpload.append("selectedDays", selectedDays);
        //DatatoUpload.append("fkSelectedMetricsIds",fkSelectedMetricsIds);
        DatatoUpload.append("selectedcampaignMetricsCheckBox", selectedcampaignMetricsCheckBox);
        DatatoUpload.append("selectedadGroupMetricsCheckBox", selectedadGroupMetricsCheckBox);
        DatatoUpload.append("selectedProductAdsMetricsCheckBox", selectedProductAdsMetricsCheckBox);
        DatatoUpload.append("selectedkeywordMetricsCheckBox", selectedkeywordMetricsCheckBox);
        DatatoUpload.append("selectedAsinMetricsCheckBox", selectedAsinMetricsCheckBox);
        if ($scheduleId == null) {
            DatatoUpload.append("opType", 1);
        } else {
            DatatoUpload.append("id", $scheduleId);
            DatatoUpload.append("opType", opType);
        }
        DatatoUpload.append("_token", $("#addAdvertisingReportSchedule input[type='hidden']").val());
        $.ajax({
            type: "post",
            url: $("#addAdvertisingReportSchedule").attr("action"),
            data: DatatoUpload,
            contentType: false,
            processData: false,
            success: function (response) {
                $(".asinScheduleSubmitButton").removeAttr("disabled");
                $(".asinScheduleSubmitButton").html(btnText);
                if (response.status) {
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
                    location.reload();
                    setTimeout(function () {
                        $("#addAdvertisingReportSchedule")[0].reset();
                        resetEditSettings();
                        fv.resetForm(true);
                        $(".asinScheduleSubmitButton").removeAttr("disabled");
                        $(".asinScheduleSubmitButton").html(btnText);
                        location.reload();
                    }, 1500);
                } else {
                    $(".asinScheduleSubmitButton").removeAttr("disabled");
                    $(".asinScheduleSubmitButton").html(btnText);
                    $mess = "";
                    $.each(response.message, function (indexInArray, valueOfElement) {
                        $mess += valueOfElement + "\n";
                    });
                    Swal.fire({
                        title: '<strong>Error</strong>',
                        type: 'error',
                        html: $mess.replace(/\n/g, "<br/>")
                    })
                }
                setTimeout(function () {

                }, 1500);
            },
            error: function (e) {
                if (e.responseText.includes("Unauthenticed")) {
                    location.reload();
                } else {
                    $(".asinScheduleSubmitButton").removeAttr("disabled");
                    $(".asinScheduleSubmitButton").html(btnText);
                    Swal.fire({
                        title: '<strong>Error</strong>',
                        type: 'error',
                        text: "Some thing went wrong"
                    })
                }
            }
        });
    });

    reportTypeField
        .select2()
        .on('change.select2', function () {
            // Revalidate the color field when an option is chosen
            fv.revalidateField('reportType');
        });
    sponsordTypeField
        .select2()
        .on('change.select2', function () {
            // Revalidate the color field when an option is chosen
            fv.revalidateField('sponsordType');
        });
    timeFrameField
        .select2()
        .on('change.select2', function () {
            // Revalidate the color field when an option is chosen
            fv.revalidateField('sponsordType');
        });


    function resetEditSettings() {
        $("#clients option").removeAttr("selected");
        $('#customRadio1').attr('checked', 'checked');
        $("#clients option:nth-child(1),#accountType option:nth-child(1),#category option:nth-child(1),#subCategory option:nth-child(1),#asin option:nth-child(1)").attr("selected");
        $("form#addProductPreviewForm .form-control,.cronList tr").removeClass("onEdit");
        $("form#addProductPreviewForm button[type='reset']").hide();
        $("form#addProductPreviewForm").attr("action_type", 1);
    }
    var rowID = null;
    /**************************Delete functionality***********************/
    $("table tbody").on('click', 'i.fa-trash', function (e) {
        row = $(this).parents("tr");
        $(row).parent().children('tr').removeClass("onEdit");
        //$(row).addClass("onEdit");
        rowID = $(this).data('api-config-id');
        csrf = $("table").attr('csrf');
        Swal.fire({
            title: '<h5 class="modal-title w-100 text-center text-primary" id="staticDeleteModalLabel">Advertising Reports</h5>',
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

            /*showLoaderOnConfirm: true,*/
            preConfirm: (ddetails) => {
                $(".deleteConfirmationBoxConfirmButton").addClass("deleteConfirmationBoxConfirmButtonOnClick");
                return fetch(base_url + '/advertisingReports/' + rowID + '/deleteSchedule/', {
                    method: 'DELETE',
                    headers: {
                        'Content-type': 'application/json; charset=UTF-8' // Indicates the content
                    },
                    body: JSON.stringify({'_token': _token})
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(response.statusText)
                        }
                        return response.json()
                    }) // OR res.text()
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
            if (result.value.status != "fail") {
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
                Swal.showValidationMessage(
                    `Request failed: ${result.value.title}`
                )
            }
        })
    });//end function
    /**************************Delete functionality***********************/
    /**************************Edit functionality***********************/
    $("table tbody").on('click', 'i.fa-edit', function (e) {
        row = $(this).parents("tr");
        $(row).parent().children('tr').removeClass("onEdit");
        //$(row).addClass("onEdit");
        rowID = $(this).data('api-config-id');
        $scheduleId = rowID;
        opType = 2;
        $tds = row.find("td");
        $(".addProductPreviewModel .modal-title").text("Update Schedule");
        $(".asinScheduleSubmitButton").html('Update');
        var DatatoUpload = new FormData();
        if (window.FormData === undefined) {
            Swal.fire({
                title: '<strong>Error</strong>',
                type: 'error',
                text: "Sorry you browser Dose not support Form Data, Please use latest browser (suggestion:Google Chrome)"
            });
            return;
        }//end if
        DatatoUpload.append("scheduleId", $scheduleId);
        DatatoUpload.append("_token", $("body").attr("csrf"));
        base_url = $("body").attr("base_url");
        $.ajax({
            type: "post",
            url: base_url + ("/advertisingReports/getEitFormData"),
            data: DatatoUpload,
            contentType: false,
            processData: false,
            success: function (response) {
                console.log(response);
                $reportName = response.reportName;
                $("#addAdvertisingReportSchedule #reportName").val($reportName);
                $granularity = response.granularity;
                $('#granularity').val(response.granularity);

                /*Time frame set daily monthly weekly start*/
                //alert(response.granularity);
                if (response.granularity == 'Daily') {
                    $('.timeFrameChange').text('days');
                    $("#timeFrame").empty();
                    $('.timeFrameChange').text('days');
                    var i;
                    var dayText = 'Days';
                    for (i = 1; i < 366; i++) {
                        if (i==1){
                            dayText ='Day';
                        }else{
                            dayText ='Days';
                        }
                        $('<option value="' + i + '" >Last ' + i +' '+ dayText + '</option>').appendTo($('#timeFrame'));
                    }
                    //$('#timeFrame').attr("placeholder", "Please Enter Number Of Days");
                }
                if (response.granularity == 'Weekly') {
                    $('.timeFrameChange').text('weeks');
                    $("#timeFrame").empty();
                    $('.timeFrameChange').text('weeks');
                    var i;
                    var weekText = 'Weeks';
                    for (i = 1; i < 53; i++) {
                        if (i==1){
                            weekText ='Week';
                        }else{
                            weekText ='Weeks';
                        }
                        $('<option value="' + i + '" >Last ' + i +' '+ weekText + '</option>').appendTo($('#timeFrame'));
                    }
                    //$('#timeFrame').attr("placeholder", "Please Enter Number Of Weeks");
                }
                if (response.granularity == 'Monthly') {
                    $('.timeFrameChange').text('months');
                    $("#timeFrame").empty();
                    $('.timeFrameChange').text('months');
                    var i;
                    var weekText = 'Months';
                    for (i = 1; i < 13; i++) {
                        if (i==1){
                            weekText ='Month';
                        }else{
                            weekText ='Months';
                        }
                        $('<option value="' + i + '" >Last ' + i +' '+ weekText + '</option>').appendTo($('#timeFrame'));
                    }
                    //$('#timeFrame').attr("placeholder", "Please Enter Number Of Months");
                }
                /*Time frame set daily monthly weekly start*/
                $fkProfileId = response.fkProfileId;
                $('#brand').val($fkProfileId);
                $timeFrame = response.timeFrame;
                $('#timeFrame').val($timeFrame);
                $time = response.time;
                $('#time').val($time);

                /*cc eamil reset value start*/
                $('.multiple_emails-ul li').remove();
                $('#addAdvertisingReportSchedule #cc_emailBS').val('[]');
                $('#addAdvertisingReportSchedule .multiple_emails-container').remove();
                $('#addAdvertisingReportSchedule .multiple_emails-ul li').remove();
                /*cc eamil reset value end*/
                /*cc eamil append value start*/
                var ccEmails = response.addCC;

                //$('#ccEmails').val($addCC);
                if (ccEmails != 'NA' && ccEmails !=''){
                    var replaceColonByComma = ccEmails.replace(/\;/g, ',');
                    var preAppendEmailDataArray = replaceColonByComma.split(',');
                } else{
                    preAppendEmailDataArray = [];
                }

                $('#cc_emailBS').val(JSON.stringify(preAppendEmailDataArray));
                $('#addAdvertisingReportSchedule > .multiple_emails-container').remove();
                $('#addAdvertisingReportSchedule > .multiple_emails-ul li').remove();
                $('#cc_emailBS').multiple_emails({position: "bottom"});
                ccEmailTooltip();
                /*cc eamil append value ends*/
                $mon = response.mon;
                if ($mon == 1) {
                    $("input[value=M]").prop("checked", true);
                }

                $tue = response.tue;
                if ($tue == 1) {
                    $("input[value=T]").prop("checked", true);
                }
                $wed = response.wed;
                if ($wed == 1) {
                    $("input[value=W]").prop("checked", true);
                }
                $thu = response.thu;
                if ($thu == 1) {
                    $("input[value=TH]").prop("checked", true);
                }
                // $("input[value=TH]").attr("checked",true);
                $fri = response.fri;
                if ($fri == 1) {
                    $("input[value=F]").prop("checked", true);
                }
                $sat = response.sat;
                if ($sat == 1) {
                    $("input[value=SA]").prop("checked", true);
                }
                $sun = response.sun;
                if ($sun == 1) {
                    $("input[value=SU]").prop("checked", true);
                }
                $('#sponsordType').val(response.sponsordTypeValueArray).trigger('change');
                setTimeout(
                    function () {
                        $('#reportType').val(response.selectedReportTypesArray).trigger('change');
                        $.each(response.selectedReportsMetricsArray, function (indexInMetricsArray, valueOfMetricsElement) {

                            $(":checkbox[value=" + valueOfMetricsElement + "]").prop("checked", "true");
                        });
                        $('.addProductPreviewModel').modal('show');
                    }, 1000);

            },
            error: function (e) {
                $("#addProductPreviewModel .preloadOnDropdownChange").fadeOut();
                $(".overlayAjaxStatus").fadeOut();
                Swal.fire({
                    title: "<strong>Their's been an error on server side refresh page and try again!</strong>",
                    type: 'error',
                    html: $mess.replace(/\n/g, "<br/>")
                })
            }
        });
    });//end function
    /**************************Edit functionality***********************/
    /*show metrics details in popup starts*/
    $("table tbody").on('click','div.showMetricsPopup',function (e) {
        var metricScheduleId = $(this).data('api-config-id');
        //$('.viewMetricsModel').modal('show');
        //alert(metricScheduleId);
        //$(".addProductPreviewModel .modal-title").text("Metrics List");
        var DatatoUpload = new FormData();
        if (window.FormData === undefined) {
            Swal.fire({
                title: '<strong>Error</strong>',
                type: 'error',
                text: "Sorry you browser Dose not support Form Data, Please use latest browser (suggestion:Google Chrome)"
            });
            return;
        }//end if
        DatatoUpload.append("scheduleId", metricScheduleId);
        DatatoUpload.append("_token", $("body").attr("csrf"));
        base_url = $("body").attr("base_url");
        $.ajax({
            type: "post",
            url: base_url + ("/advertisingReports/getMetricsPopupData"),
            data: DatatoUpload,
            contentType: false,
            processData: false,
            success: function (response) {
                console.log(response.getSelectedParameterTypesArray);
                $('.showCampaignSelectedMetrics').empty();
                $('.showAdgroupSelectedMetrics').empty();
                $('.showProudctAdsSelectedMetrics').empty();
                $('.showKeywordSelectedMetrics').empty();
                $('.showAsinSelectedMetrics').empty();
                $(".campaignSelectedMetrics").attr("style", "display:none");
                $(".adgroupSelectedMetrics").attr("style", "display:none");
                $(".proudctAdsSelectedMetrics").attr("style", "display:none");
                $(".keywordSelectedMetrics").attr("style", "display:none");
                $(".asinSelectedMetrics").attr("style", "display:none");
                if (jQuery.inArray(1, response.getSelectedParameterTypesArray) !== -1) {
                    $(".campaignSelectedMetrics").attr("style", "display:block");
                    $('.showCampaignSelectedMetrics').append(response.campaingMetricsString);
                }
                if (jQuery.inArray(2, response.getSelectedParameterTypesArray) !== -1) {
                    $(".adgroupSelectedMetrics").attr("style", "display:block");
                    $('.showAdgroupSelectedMetrics').append(response.adGroupMetricsString);
                }
                if (jQuery.inArray(3, response.getSelectedParameterTypesArray) !== -1) {
                    $(".proudctAdsSelectedMetrics").attr("style", "display:block");
                    $('.showProudctAdsSelectedMetrics').append(response.productAdsMetricsString);
                }
                if (jQuery.inArray(4, response.getSelectedParameterTypesArray) !== -1) {
                    $(".keywordSelectedMetrics").attr("style", "display:block");
                    $('.showKeywordSelectedMetrics').append(response.keywordMetricsString);
                }
                if (jQuery.inArray(5, response.getSelectedParameterTypesArray) !== -1) {
                    $(".asinSelectedMetrics").attr("style", "display:block");
                    $('.showAsinSelectedMetrics').append(response.asinsMetricsString);
                }

                $('.viewMetricsModel').modal('show');
/*                setTimeout(
                    function () {*/
                       /* $('#reportType').val(response.selectedReportTypesArray).trigger('change');
                        $.each(response.selectedReportsMetricsArray, function (indexInMetricsArray, valueOfMetricsElement) {

                            $(":checkbox[value=" + valueOfMetricsElement + "]").prop("checked", "true");
                        });*/
                        //$('.addProductPreviewModel').modal('show');
                   // }, 1000);

            },
            error: function (e) {
                $("#addProductPreviewModel .preloadOnDropdownChange").fadeOut();
                $(".overlayAjaxStatus").fadeOut();
                Swal.fire({
                    title: "<strong>Their's been an error on server side refresh page and try again!</strong>",
                    type: 'error',
                    html: $mess.replace(/\n/g, "<br/>")
                })
            }
        });
    });//end function
    /*show metrics details in popup ends*/
    autosize($(".userActionsTextAreas"));
    autosize($(".eventsTextAreas"));
    currentDate = moment().format("YYYY-MM-DD");
    var statDate = moment(currentDate, "YYYY-MM-DD").subtract(6, 'days').format("YYYY-MM-DD");
    $(".noControl").click(function (e) {
        e.preventDefault();
        $(this).parent().parent().hide();
        $(this).parent().parent().find("textarea").val("");
    });
    $(".yesControl").click(function (e) {
        e.preventDefault();
        $(this).parent().parent().hide();
    });

    $(document).on('change', '#sponsordType', function () {
        $('.campaignMetrics :checkbox:enabled').prop('checked', false);
        $('.adGroupMetrics :checkbox:enabled').prop('checked', false);
        $('.productAdsMetrics :checkbox:enabled').prop('checked', false);
        $('.keywordMetrics :checkbox:enabled').prop('checked', false);
        $('.asinMetrics :checkbox:enabled').prop('checked', false);
        $("#checkAllMetrics").prop("checked", false);
        $('.selectAllMetrics').text('Select All Metrics');
        var DatatoUpload = new FormData();
        if (window.FormData === undefined) {
            Swal.fire({
                title: '<strong>Error</strong>',
                type: 'error',
                text: "Sorry you browser Dose not support Form Data, Please use latest browser (suggestion:Google Chrome)"
            });
            return;
        }//end if
        $("#addProductPreviewModel .preloadOnDropdownChange").fadeIn();
        var sponsordTypename = $(this).attr("name");
        var sponsordTypeValue = $("#sponsordType").val();
        DatatoUpload.append("sponsordTypename", sponsordTypename);
        DatatoUpload.append("sponsordTypeValue", sponsordTypeValue);
        DatatoUpload.append("_token", $("body").attr("csrf"));
        base_url = $("body").attr("base_url");
        $.ajax({
            type: "post",
            url: base_url + ("/advertisingReports/getReportTypes"),
            data: DatatoUpload,
            contentType: false,
            processData: false,
            success: function (response) {
                $("#addProductPreviewModel .preloadOnDropdownChange").fadeOut();
                $('#reportType').empty();
                $.each(response.reportsTypesArray, function (indexInArray, valueOfElement) {
                    var valueOfReportType = valueOfElement.replace("<", "&lt;");
                    $('#reportType').append('<option value="' + indexInArray + '">' + valueOfReportType + '</option>');
                });
            },
            error: function (e) {
                $("#addProductPreviewModel .preloadOnDropdownChange").fadeOut();
                $(".overlayAjaxStatus").fadeOut();
                Swal.fire({
                    title: "<strong>Their's been an error on server side refresh page and try again!</strong>",
                    type: 'error',
                    html: $mess.replace(/\n/g, "<br/>")
                })
            }
        });
    });
    $(document).on('change', '#reportType', function () {
        //$(".keywordMetrics").attr("style", "display:block");
        //alert('test');
        var DatatoUpload = new FormData();
        if (window.FormData === undefined) {
            Swal.fire({
                title: '<strong>Error</strong>',
                type: 'error',
                text: "Sorry you browser Dose not support Form Data, Please use latest browser (suggestion:Google Chrome)"
            });
            return;
        }
        var reportTypename = $(this).attr("name");
        var reportTypeValue = $("#reportType").val();
        DatatoUpload.append("reportTypename", reportTypename);
        DatatoUpload.append("reportTypeValue", reportTypeValue);
        DatatoUpload.append("_token", $("body").attr("csrf"));
        base_url = $("body").attr("base_url");
        $.ajax({
            type: "post",
            url: base_url + ("/advertisingReports/getReportMetrics"),
            data: DatatoUpload,
            contentType: false,
            processData: false,
            success: function (response) {
                /*hide checkboxes if no metrics found*/
                if ((jQuery.inArray('1', response.reportsParameterTypes) == -1) && (jQuery.inArray('2', response.reportsParameterTypes) == -1) && (jQuery.inArray('3', response.reportsParameterTypes) == -1) && (jQuery.inArray('4', response.reportsParameterTypes) == -1) && (jQuery.inArray('5', response.reportsParameterTypes) == -1)) {
                    $('.campaignMetrics :checkbox:enabled').prop('checked', false);
                    $('.adGroupMetrics :checkbox:enabled').prop('checked', false);
                    $('.productAdsMetrics :checkbox:enabled').prop('checked', false);
                    $('.keywordMetrics :checkbox:enabled').prop('checked', false);
                    $('.asinMetrics :checkbox:enabled').prop('checked', false);
                    $("#checkAllMetrics").prop("checked", false);
                    $(".metrics_div").attr("style", "display:none");
                }
                if (jQuery.inArray('1', response.reportsParameterTypes) !== -1) {
                    //alert('campaign found');
                    $(".metrics_div").attr("style", "display:block");
                    $(".campaignMetrics").attr("style", "display:block");
                } else {
                    $('.campaignMetrics :checkbox:enabled').prop('checked', false);
                    $(".campaignMetrics").attr("style", "display:none");
                }
                if (jQuery.inArray('2', response.reportsParameterTypes) !== -1) {
                    $(".metrics_div").attr("style", "display:block");
                    $(".adGroupMetrics").attr("style", "display:block");
                } else {
                    $('.adGroupMetrics :checkbox:enabled').prop('checked', false);
                    $(".adGroupMetrics").attr("style", "display:none");
                }
                if (jQuery.inArray('3', response.reportsParameterTypes) !== -1) {
                    $(".metrics_div").attr("style", "display:block");
                    $(".productAdsMetrics").attr("style", "display:block");
                } else {
                    $('.productAdsMetrics :checkbox:enabled').prop('checked', false);
                    $(".productAdsMetrics").attr("style", "display:none");
                }
                if (jQuery.inArray('4', response.reportsParameterTypes) !== -1) {
                    $(".metrics_div").attr("style", "display:block");
                    $(".keywordMetrics").attr("style", "display:block");
                } else {
                    $('.keywordMetrics :checkbox:enabled').prop('checked', false);
                    $(".keywordMetrics").attr("style", "display:none");
                }
                if (jQuery.inArray('5', response.reportsParameterTypes) !== -1) {
                    $(".metrics_div").attr("style", "display:block");
                    $(".asinMetrics").attr("style", "display:block");
                } else {
                    $('.asinMetrics :checkbox:enabled').prop('checked', false);
                    $(".asinMetrics").attr("style", "display:none");
                }
                console.log(response.reportsParameterTypes);
                $mess = "";
            },
            error: function (e) {
                $("#addProductPreviewModel .preloadOnDropdownChange").fadeOut();
                $(".overlayAjaxStatus").fadeOut();
                Swal.fire({
                    title: "<strong>Their's been an error on server side refresh page and try again!</strong>",
                    type: 'error',
                    html: $mess.replace(/\n/g, "<br/>")
                })
            }
        });
    });
    $(document).on('change', '#granularity', function () {
        var timeFrameTypeValue = $("#granularity").val();
        if (timeFrameTypeValue == 'Daily') {
            $("#timeFrame").empty();
            $('.timeFrameChange').text('days');
            var i;
            var dayText = 'Days';
            for (i = 1; i < 366; i++) {
                if (i==1){
                    dayText ='Day';
                }else{
                    dayText ='Days';
                }
                $('<option value="' + i + '" >Last ' + i +' '+ dayText + '</option>').appendTo($('#timeFrame'));
            }
            //$('#timeFrame').attr("placeholder", "Please Enter Number Of Days");
        }
        if (timeFrameTypeValue == 'Weekly') {
            $("#timeFrame").empty();
            $('.timeFrameChange').text('weeks');
            var i;
            var weekText = 'Weeks';
            for (i = 1; i < 53; i++) {
                if (i==1){
                    weekText ='Week';
                }else{
                    weekText ='Weeks';
                }
                $('<option value="' + i + '" >Last ' + i +' '+ weekText + '</option>').appendTo($('#timeFrame'));
            }
            //$('#timeFrame').attr("placeholder", "Please Enter Number Of Weeks");
        }
        if (timeFrameTypeValue == 'Monthly') {
            $("#timeFrame").empty();
            $('.timeFrameChange').text('months');
            var i;
            var weekText = 'Months';
            for (i = 1; i < 13; i++) {
                if (i==1){
                    weekText ='Month';
                }else{
                    weekText ='Months';
                }
                $('<option value="' + i + '" >Last ' + i +' '+ weekText + '</option>').appendTo($('#timeFrame'));
            }
            //$('#timeFrame').attr("placeholder", "Please Enter Number Of Months");
        }
    });
    $(document).on('change', '#checkAllMetrics', function () {
        if ($('input[name=checkAllMetrics]').is(':checked')) {
            $('.selectAllMetrics').text('Select All Metrics');
            if ($('.campaignMetrics').is(":visible")) {
                $('.campaignMetrics :checkbox:enabled').prop('checked', true);
            } else {
                $('.campaignMetrics :checkbox:enabled').prop('checked', false);
            }
            if ($('.adGroupMetrics').is(":visible")) {
                $('.adGroupMetrics :checkbox:enabled').prop('checked', true);
            } else {
                $('.adGroupMetrics :checkbox:enabled').prop('checked', false);
            }
            if ($('.productAdsMetrics').is(":visible")) {
                $('.productAdsMetrics :checkbox:enabled').prop('checked', true);
            } else {
                $('.productAdsMetrics :checkbox:enabled').prop('checked', false);
            }
            if ($('.keywordMetrics').is(":visible")) {
                $('.keywordMetrics :checkbox:enabled').prop('checked', true);
            } else {
                $('.keywordMetrics :checkbox:enabled').prop('checked', false);
            }
            if ($('.asinMetrics').is(":visible")) {
                $('.asinMetrics :checkbox:enabled').prop('checked', true);
            } else {
                $('.asinMetrics :checkbox:enabled').prop('checked', false);
            }
        } else {
            $('.selectAllMetrics').text('Deselect All Metrics');
            $('.campaignMetrics :checkbox:enabled').prop('checked', false);
            $('.adGroupMetrics :checkbox:enabled').prop('checked', false);
            $('.productAdsMetrics :checkbox:enabled').prop('checked', false);
            $('.keywordMetrics :checkbox:enabled').prop('checked', false);
            $('.asinMetrics :checkbox:enabled').prop('checked', false);
        }
    });
    $(document).on('click', '.metrics_checkboxes', function () {
        $("#checkAllMetrics").prop("checked", false);
    });

});//end ready function
function ccEmailTooltip() {
    $('.multiple_emails-input').tooltipster({
        content: 'Please press tab,comma,enter or space to add multiple emails'
    }).focus(function(){
        $(this).tooltipster('show');
    });
}

//stop form submission on enter button
$(document).ready(function() {

    $('#time').datetimepicker({
        format: 'hh:mm A',
    });
    // Time Picker Show Only Time
    $(".timepicker").click(function (e) {
        e.preventDefault();
        e.stopPropagation();
        $("span.glyphicon-chevron-down").addClass("fas fa-angle-down");
        $("span.glyphicon-chevron-down").removeClass("glyphicon glyphicon-chevron-down");

        $("span.glyphicon-chevron-up").addClass("fas fa-angle-up");
        $("span.glyphicon-chevron-up").removeClass("glyphicon glyphicon-chevron-up");

    });

    $(window).keydown(function(event){
        if(event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });
});