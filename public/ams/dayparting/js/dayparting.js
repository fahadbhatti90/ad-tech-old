function ccEmailTooltip() {
    $('.multiple_emails-input').tooltipster({
        content: 'Press tab, enter, comma or space to add multiple emails',
        debug: false
    }).focus(function(){
        $(this).tooltipster('show');
    });
}
function timePickerCtrl(startId, EndId) {
    var startTime = $('#'+startId).datetimepicker({
        format: 'hh:mm A',
    }).on('dp.change', function(e) {
        endTime
            .data("DateTimePicker")
            .date(moment(startTime.data("DateTimePicker").date()).add(1, 'm'));

        return endTime
            .data("DateTimePicker")
            .minDate(moment(startTime.data("DateTimePicker").date()).add(1, 'm'))
    });


    var endTime = $('#'+EndId).datetimepicker({
        format: 'hh:mm A',
        minDate: startTime.data("DateTimePicker").date(),
        maxDate: moment().endOf('day')

    });
}
var t;
// Datatables
function loadDatatables(fieldId, route) {
    $.fn.dataTable.ext.errMode = "none";
    t =$("#" + fieldId).DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "ajax": route,
        "columns": [
            { "data": "id", visible: false },
            { "data": "DT_RowIndex", "name": "DT_RowIndex", orderable: false, searchable: false},
            {"data": "scheduleName"},
            {"data": "portfolioCampaignType"},
            {"data": "included"},
            {"data": "mon"},
            {"data": "tue"},
            {"data": "wed"},
            {"data": "thu"},
            {"data": "fri"},
            {"data": "sat"},
            {"data": "sun"},
            {"data": "action"}
        ],
        "order": [ [0, 'desc'] ],
        "drawCallback": function( settings ) {
            // Tool Tip Tipster
            $('.tooltip-dayParting').tooltipster({
                interactive: true,
                maxWidth: 350,
                contentAsHTML:true,
                trigger:'click',
                functionReady: function(instance, helper){
                    globalToolTipHeight = $(".tooltipster-base").height()
                    var custToolTip = '<div class="tooltipster-base tooltipster-sidetip tooltipster-top tooltipster-fade tooltipster-show" id="tooltipster-250980" style="max-width: 300px;\
                    pointer-events: auto;\
                    z-index: 1025;\
                    display:none;\
                    left: 0px;\
                    top: -37px;\
                    height: auto;\
                    width: 100%;\
                    animation-duration: 350ms;\
                    transition-duration: 350ms;"><div class="tooltipster-box"><div class="tooltipster-content">\
                    <p class="toolTipContent"></p>\
                </div></div><div class="tooltipster-arrow" style="left: 119px;"><div class="tooltipster-arrow-uncropped"><div class="tooltipster-arrow-border"></div><div class="tooltipster-arrow-background"></div></div></div></div>';
                    $(".tooltipster-base .tooltipster-content").append(custToolTip);

                }

            });
        }
    });
}

var AjaxFunction = function (fkProfileId, portfolioCampaignType, pfCampaignDivId, oldPortfolioCampaignType= '') {
    $("#" + pfCampaignDivId).prop('disabled', true);
    var token = $("body").attr("csrf");
    $.ajax({
        type: "post",
        url: siteURL + '/dayParting/getCampaignPortfolioData?_='+new Date().getTime(),
        data: {
            "portfolioCampaignType": portfolioCampaignType,
            "fkProfileId": fkProfileId,
            "_token": token,
            "oldPortfolioCampaignType":oldPortfolioCampaignType
        },
        success: function (response) {
            if (response.ajax_status == true) {
                $("#" + pfCampaignDivId).empty();
                if (response.text.length > 0) {
                    $("#" + pfCampaignDivId).prop('disabled', false);
                    $("#" + pfCampaignDivId).empty();
                    var responseData = '';
                    var responseData = response.text;
                    $.each(responseData, function (label, repo) {
                        $('<option value="' + repo.id + '-'+ repo.name +'" >' + repo.name + '</option>').appendTo($("#" + pfCampaignDivId));
                    });
                } else {
                    Swal.fire({type: 'error', html: 'No Data found!', showConfirmButton: true});
                }

            } else {
                // something went wrong
            }
        },
        error:function(e){
            if(e.responseText.includes("Unauthenticed")){
                location.reload();
            }
        }
    });
}
// Form validation for adding and editing Form
document.addEventListener('DOMContentLoaded', function (e) {
    $(window).keydown(function(event){
        if(event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });
    const form = document.getElementById('dayPartingSchedule');
    const formValidation = FormValidation.formValidation(form, {
            fields: {
                scheduleName: {
                    validators: {
                        notEmpty: {
                            message: 'The schedule name is required'
                        },
                        regexp: {
                            regexp: /^[a-zA-Z0-9:_-]+$/,
                            message: 'The name can only consist of alphabetical,number,underscore,hyphen and colon'
                        }
                    }
                },
                fkProfileId: {
                    validators: {
                        notEmpty: {
                            message: 'The profile is required'
                        }
                    }
                },
                'pfCampaigns[]': {
                    validators: {
                        notEmpty: {
                            message: 'The Portfolio/Campaign is required'
                        }
                    }
                },
                portfolioCampaignType: {
                    validators: {
                        notEmpty: {
                            message: 'The portfolio campaign type is required'
                        }
                    }
                },
                startTime: {
                    validators: {
                        notEmpty: {
                            message: 'The start time is required'
                        }
                    }
                },
                endTime: {
                    validators: {
                        notEmpty: {
                            message: 'The end time is required'
                        }
                    }
                }
            },
            plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                bootstrap: new FormValidation.plugins.Bootstrap(),
                submitButton: new FormValidation.plugins.SubmitButton(),
            },
        }
    ).on('core.form.valid', function () {
        var btnHtml = '<i class="fas fa-circle-notch fa-spin"></i>';
        submitBtnEnableDisable(true, btnHtml);
        var formData = new FormData();
        var params = $(form).serializeArray();
        $.each(params, function (i, val) {
            formData.append(val.name, val.value);
        });

        axios.post(siteURL + '/dayParting/schedule?_='+new Date().getTime(), formData, {}).then(function (response) {
            submitBtnEnableDisable(false);
            if (response.data.ajax_status == true) {
                //let htmlSuccess = showSuccessHtml(response.data.success);
                Swal.fire({type: 'success', html: response.data.success, showConfirmButton: true}).then(function () {
                    //location.reload();
                    t.ajax.reload();
                });
                $('.multiple_emails-ul li').remove();
                $('#pfCampaigns').val(null).trigger("change");
                $("#pfCampaigns").prop('disabled', true);
                $('.fkProfileIdDayParting').val(null).trigger("change");
                $('.portfolioCampaignType').val(null).trigger("change");
                formDataAndValidationReset(form, formValidation);

            } else {
                let htmlErrors = showErrors(response.data.error);
                Swal.fire({type: 'error', html: htmlErrors, showConfirmButton: true});
            }
        }).catch(function (error) {
            //handle error
            submitBtnEnableDisable(false);
            errorResponseShow(error);
        });

    });
    /*
        Edit Functionality Validation
    */
    const editForm = document.getElementById('dayPartingScheduleEditForm');
    const formValidation1 = FormValidation.formValidation(editForm, {
            fields: {
                scheduleName: {
                    validators: {
                        notEmpty: {
                            message: 'The schedule name is required'
                        },
                        regexp: {
                            regexp: /^[a-zA-Z0-9:_-]+$/,
                            message: 'The name can only consist of alphabetical,number,underscore,hyphen and colon'
                        }
                    }
                },
                'pfCampaigns[]': {
                    validators: {
                        notEmpty: {
                            message: 'The Portfolio/Campaign is required'
                        }
                    }
                },
                portfolioCampaignType: {
                    validators: {
                        notEmpty: {
                            message: 'The portfolio campaign type is required'
                        }
                    }
                },
                startTime: {
                    validators: {
                        notEmpty: {
                            message: 'The start time is required'
                        }
                    }
                },
                endTime: {
                    validators: {
                        notEmpty: {
                            message: 'The end time is required'
                        }
                    }
                }
            },
            plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                bootstrap: new FormValidation.plugins.Bootstrap(),
                submitButton: new FormValidation.plugins.SubmitButton(),
            },
        }
    ).on('core.form.valid', function () {
        var btnHtml = '<i class="fas fa-circle-notch fa-spin"></i>';
        submitBtnEnableDisable(true, btnHtml);
        var formData = new FormData(editForm);
        var params = $(editForm).serializeArray();
        $.each(params, function (i, val) {
            formData.append(val.name, val.value);
        });

        axios.post(siteURL + '/dayParting/editScheduleSubmit?_='+new Date().getTime(), formData, {}).then(function (response) {
            submitBtnEnableDisable(false);
            if (response.data.ajax_status == true) {

                Swal.fire({type: 'success', html: response.data.success, showConfirmButton: true}).then(function () {
                    $("#schedule-edit .modal-footer button:nth-child(1)").click();
                    t.ajax.reload();
                    // location.reload();
                });

                $('#pfCampaigns').val(null).trigger("change");
                formDataAndValidationReset(editForm, formValidation);
            } else {
                let htmlErrors = showErrors(response.data.error);
                Swal.fire({type: 'error', html: htmlErrors, showConfirmButton: true});
            }
        }).catch(function (error) {
            //handle error
            submitBtnEnableDisable(false);
            errorResponseShow(error);
        });
    }); // Edit Functionality Validation End

    $('.js-example-basic-multiple').select2({
        placeholder: 'Choose'
    });

    // reset pop up data on modal close
    $(".close-btn").on("click",function(){
        $('#dayPartingScheduleEditForm #ccEmailsEdit').val('[]');
        $('#dayPartingScheduleEditForm .multiple_emails-container').remove();
        $('#dayPartingScheduleEditForm .multiple_emails-ul li').remove();
        $("#dayPartingScheduleEditForm").trigger('reset');
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

    timePickerCtrl('startTime', 'endTime');
    timePickerCtrl('startTimeEdit', 'endTimeEdit');

    // Add CC Emails Field toggling
    $('.anchorClassNew').on('click', function (e) {
        e.preventDefault();
        $('.ccEmailArea').show();
        $('.anchorClassNew').hide();
        $('.ccEmailSelect2').prop('disabled', false);
    });

    // On change profileId (ADD)
    $(".fkProfileIdDayParting").on('change', function () {
        var fkProfileId = $(this).val();
        var PfCampType = $('select.portfolioCampaignType').val();
        if (PfCampType != '' && fkProfileId != '') {
            AjaxFunction(fkProfileId, PfCampType, 'pfCampaigns');
        }
    }); // End on change functionality

    // On change portfolio or campaign will come in select2
    $("select.portfolioCampaignType").on('change', function () {
        var fkProfileId = $('.fkProfileIdDayParting').val();
        var portfolioCampaignType = this.value;

        if (portfolioCampaignType != ''){
            AjaxFunction(fkProfileId, portfolioCampaignType, 'pfCampaigns');

        }
    }); // End on change functionality
    // ask user three option to select while removing any campaigns from the schedule
    $('#pfCampaignsEdit').on('select2:unselect', function (e) {
        e.preventDefault();
        var element = e.params.data.element;

        var preSelectedItem = $( element ).attr( "selected" );
        var removedCampaigns = $( element ).val();
        var popUpCount = $("#scheduleCountPopup").val();

        if (preSelectedItem == 'selected'){
            if(popUpCount == 1 ){
                (async () => {

                    /* inputOptions can be an object or Promise */
                    const inputOptions = new Promise((resolve) => {
                        setTimeout(() => {
                            resolve({
                                "1": "Run today's schedule, then pause",
                                "2": "Pause campaigns immediately",
                                "3": "Campaigns enabled permanently"
                            })
                        }, 500)
                    })

                    const { value: inputValue } = await Swal.fire({
                        title: 'Day Parting',
                        text: "This will permanently remove these campaigns from the schedule!",
                        input: 'radio',
                        customClass: 'swal-width',
                        inputOptions: inputOptions,
                        //showCancelButton: true,
                        confirmButtonText: 'Yes, Proceed!',
                        allowOutsideClick: false,
                        inputValidator: (value) => {
                            if (!value) {
                                return 'Please select one option!'
                            }
                        }
                    })
                    if (inputValue) {
                        $('#campaignOptionSelected').val(inputValue);
                        $("#scheduleCountPopup").val(2);
                    }
                })()
            }
            var currentVal = $('#removeCampaings').val();
            if(currentVal)
                $('#removeCampaings').val(currentVal + "," + removedCampaigns);
            else
                $('#removeCampaings').val(removedCampaigns);
        }
    });

    $("select.portfolioCampaignEditType").bind('click', function (e) {
        var lastValue = $(this).val();
    }).bind('change', function (e) {
        var preSelectedItem = $(this).find('option:selected').attr('selected');
        $("#pfCampaignsEdit").prop('disabled', true);
        var token = $("body").attr("csrf");
        var portfolioCampaignType = this.value;
        var fkProfileId = $("#fkProfileIdEdit").val();
        var PfCampType = $('select.portfolioCampaignEditType').val();
        var scheduleId = $("#scheduleId").val();
        var oldPortfolioCampaignType = $("#portfolioCampaignEditTypeOldValue").val();
        if (preSelectedItem != 'selected'){
            (async () => {
                /* inputOptions can be an object or Promise */
                const inputOptions = new Promise((resolve) => {
                    setTimeout(() => {
                        resolve({
                            "1": "Run today's schedule, then pause",
                            "2": "Pause campaigns immediately",
                            "3": "Campaigns enabled permanently"
                        })
                    }, 500)
                })

                const { value: inputValue } = await Swal.fire({
                    title: 'Day Parting',
                    text: "This will permanently remove these campaigns from the schedule!",
                    input: 'radio',
                    customClass: 'swal-width',
                    inputOptions: inputOptions,
                    //showCancelButton: true,
                    confirmButtonText: 'Yes, Proceed!',
                    allowOutsideClick:false,
                    inputValidator: (value) => {
                        if (!value) {
                            return 'Please select one option!'
                        }
                    }
                });
                if (inputValue) {
                    $('#campaignOptionSelected').val(inputValue);
                    AjaxFunction(fkProfileId, PfCampType, 'pfCampaignsEdit', oldPortfolioCampaignType);
                }
            })()
        }else{
            AjaxFunction(fkProfileId, PfCampType, 'pfCampaignsEdit', oldPortfolioCampaignType);
        }
    });

    $('#pfCampaignsEdit').on('select2:unselect', function (e) {
        e.preventDefault();
        var element = e.params.data.element;

        var preSelectedItem = $(element).attr("selected");
        var removedCampaigns = $(element).val();
        var popUpCount = $("#scheduleCountPopup").val();
    });
    // Pre Append Values on edit schedule
    $('#dayPartingHistoryTable').on('click', '.schedule-edit', function () {
        var csrfToken = $("body").attr('csrf');
        var rowId = $(this).closest('div').attr("data-id");
        var scheduleName = $(this).closest('div').attr("data-name");
        var portfolioCampaignType = $(this).closest('div').attr("data-schedule-type");
        var monday = $(this).closest('div').attr("data-mon");
        var tuesday = $(this).closest('div').attr("data-tue");
        var wednesday = $(this).closest('div').attr("data-wed");
        var thursday = $(this).closest('div').attr("data-thu");
        var friday = $(this).closest('div').attr("data-fri");
        var saturday = $(this).closest('div').attr("data-sat");
        var sunday = $(this).closest('div').attr("data-sun");
        var startScheduleTime = $(this).closest('div').attr("data-start-time");
        var endScheduleTime = $(this).closest('div').attr("data-end-time");
        var emailReceiptStart = $(this).closest('div').attr("data-email-receipt-start");
        var emailReceiptEnd = $(this).closest('div').attr("data-email-receipt-end");
        var ccEmails = $(this).closest('div').attr("data-cc-emails");
        var fkProfile = $(this).closest('div').attr("data-pkProfile-edit");

        $.ajax({
            type: "post",
            url: siteURL + '/dayParting/editSchedule?_='+new Date().getTime(),
            data: {
                "_token": csrfToken,
                "scheduleId" : rowId
            },
            success: function (response) {
                if (response.ajax_status == true) {
                    $("#schedule-edit").modal('show');
                    $('#removeCampaings').val('');
                    $('#campaignOptionSelected').val('');
                    $('#scheduleCountPopup').val(1);
                    $('#pfCampaignsEdit').empty();
                    $(".preSelectedCampaigns").select2({
                        placeholder: 'Choose'
                    });

                    $("#scheduleId").val(rowId);
                    $("#portfolioCampaignEditTypeOldValue").val(portfolioCampaignType);
                    $("#fkProfileIdEdit").val(fkProfile);
                    $("#scheduleEditName").val(scheduleName);
                    $('#portfolioCampaignEditType option:selected').removeAttr('selected');
                    $('select[id^="portfolioCampaignEditType"] option[value="'+portfolioCampaignType+'"]').attr("selected","selected");
                    $('select[id^="fkProfileIdDayPartingEdit"] option[value="'+fkProfile+'"]').attr("selected","selected");

                    (monday === '1' ) ? $(".monEdit").attr("checked", true) : $(".monEdit").attr("checked", false);
                    (tuesday === '1' ) ? $(".tueEdit").attr("checked", true) : $(".tueEdit").attr("checked", false);
                    (wednesday === '1' ) ? $(".wedEdit").attr("checked", true) : $(".wedEdit").attr("checked", false);
                    (thursday === '1' ) ? $(".thuEdit").attr("checked", true) : $(".thuEdit").attr("checked", false);
                    (friday === '1' ) ? $(".friEdit").attr("checked", true) : $(".friEdit").attr("checked", false);
                    (saturday === '1' ) ? $(".satEdit").attr("checked", true) : $(".satEdit").attr("checked", false);
                    (sunday === '1' ) ? $(".sunEdit").attr("checked", true) : $(".sunEdit").attr("checked", false);

                    (emailReceiptStart === '1' ) ? $(".emailReceiptStartEdit").attr("checked", true) : "";
                    (emailReceiptEnd === '1' ) ? $(".emailReceiptEndEdit").attr("checked", true) : "";

                    // replace function with start and end Time for removing seconds from time
                    var startTime = startScheduleTime.replace(/:[^:]*$/,'');
                    var endTime = endScheduleTime.replace(/:[^:]*$/,'');
                    $(".startTimeEdit").val(moment(startTime, 'HH:mm').format('hh:mm A'));
                    $(".endTimeEdit").val(moment(endTime, 'HH:mm').format('hh:mm A'));

                    if (ccEmails != 'NA' && ccEmails !=''){
                        var replaceColonByComma = ccEmails.replace(/\;/g, ',');
                        var preAppendEmailDataArray = replaceColonByComma.split(',');
                    } else{
                        preAppendEmailDataArray = [];
                    }
                    $('#ccEmailsEdit').val(JSON.stringify(preAppendEmailDataArray));
                    $('#dayPartingScheduleEditForm > .multiple_emails-container').remove();
                    $('#dayPartingScheduleEditForm > .multiple_emails-ul li').remove();
                    $('#ccEmailsEdit').multiple_emails({position: "bottom"});
                    ccEmailTooltip();
                    if (portfolioCampaignType == "Campaign"){
                        var allCampaignsArraySchedule = response.allCampaignListRecord;
                        var selectedCampaigns = response.allScheduleData.campaigns;
                        var mergeCampaignsWithOutDuplication = [];
                        for(var i=0; i<allCampaignsArraySchedule.length; i++) {
                            mergeCampaignsWithOutDuplication.push({
                                ...allCampaignsArraySchedule[i],
                                ...(selectedCampaigns.find((itmInner) => itmInner.id === allCampaignsArraySchedule[i].id))}
                            );
                        }
                        $.each(mergeCampaignsWithOutDuplication, function (label1, repo1) {

                            if (!repo1.pivot){
                                // Preselect those select2 options those are associated with this schedule
                                $('<option value="' + repo1.id + '-'+ repo1.name +'">' + repo1.name+ '</option>').appendTo($('#pfCampaignsEdit'));
                            }else{
                                $('<option value="' + repo1.id + '-'+ repo1.name + '"' + 'selected="selected">' + repo1.name+ '</option>').appendTo($('#pfCampaignsEdit'));
                            }
                        });
                    }
                    if (portfolioCampaignType === 'Portfolio'){
                        var allPortfoliosArray = response.allPortfolios;
                        var selectedPortfolio = response.allScheduleData.portfolios;
                        var mergePortfoliosWithOutDuplication = [];

                        for(var i=0; i<allPortfoliosArray.length; i++) {
                            mergePortfoliosWithOutDuplication.push({
                                ...allPortfoliosArray[i],
                                ...(selectedPortfolio.find((itmInner) => itmInner.id === allPortfoliosArray[i].id))}
                            );
                        }

                        $.each(mergePortfoliosWithOutDuplication, function (label1, repo1) {
                            if (!repo1.pivot){
                                // Preselect those select2 options those are associated with this schedule
                                $('<option value="' + repo1.id + '-'+ repo1.name +'">' + repo1.name+ '</option>').appendTo($('#pfCampaignsEdit'));
                            }else{
                                $('<option value="' + repo1.id + '-'+ repo1.name + '"' + 'selected="selected">' + repo1.name+ '</option>').appendTo($('#pfCampaignsEdit'));
                            }
                        });
                    }

                } else {
                    // something went wrong
                }

            },
            error:function(e){
                if(e.responseText.includes("Unauthenticed")){
                    location.reload();
                }
            }
        });

    });

    $("body").on("mouseenter",".listToolTip li", function () {
        tooltipFullValue = $(this).attr("datatitle");
        if(tooltipFullValue.length <=30){
            return;
        }
        childPos = $(this).position().top;
        $(".tooltipster-base .tooltipster-base").show()
        $(".tooltipster-base .toolTipContent").html(tooltipFullValue);
        $(".tooltipster-base .tooltipster-base").css("top", (childPos - $(".tooltipster-base .tooltipster-base").height()) + "px");

    }).on("mouseout",".listToolTip li", function () {
        $(".tooltipster-base .tooltipster-base").hide();
    });
    // Delete Functionality
    $("table tbody").on('click','i.fa-trash',function (e) {
        var rowId = $(this).closest('div').attr("data-id");
        // inputOptions can be an object or Promise
        (async () => {

            /* inputOptions can be an object or Promise */
            const inputOptions = new Promise((resolve) => {
                setTimeout(() => {
                    resolve({
                        "1": "Run today's schedule, then pause",
                        "2": "Pause campaigns immediately",
                        "3": "Campaigns enabled permanently"
                    })
                }, 500)
            })

            const { value: inputValue } = await Swal.fire({
                title: 'Day Parting',
                //type: 'question',
                text: "This will permanently remove these campaigns from the schedule!",
                input: 'radio',
                customClass: 'swal-width',
                inputOptions: inputOptions,
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Yes, Proceed!',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Please select one option!'
                    }
                }
            })
            if (inputValue) {
                $.ajax({
                    type: "post",
                    url: siteURL + '/dayParting/deleteSchedule',
                    data: {
                        "_token": _token,
                        "status": inputValue,
                        "scheduleId": rowId
                    },
                    success: function (response) {
                        if (response.status == true) {
                            Swal.fire(
                                'Deleted!',
                                'Schedule has been deleted.',
                                'success'
                            ).then(function () {
                                t.ajax.reload();
                                //location.reload();
                            });
                        } else {
                            // something went wrong
                        }
                    },
                    error:function(e){
                        if(e.responseText.includes("Unauthenticed")){
                            location.reload();
                        }
                    }
                });
//                Swal.fire({ html: `You selected: ${inputValue}` })
            }
        })()
    });//end function
});