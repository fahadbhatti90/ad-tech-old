$(function () {
    $('.js-example-basic-multiple').select2({
        placeholder: 'Choose'
    });
    // On change portfolio or campaign will come in select2
    $("#type").on('change', function () {
        var profile_fk_id = $('.profileName').val();
        var sponsored_type = $('#sponsored_type').val();
        if (sponsored_type == '') {
            Swal.fire({type: 'error', html: 'Kindly Choose Sponsored Type First.', showConfirmButton: true});
            $("#pfCampaigns").empty();
            $(this).val();
        } else {
            $("#pfCampaigns").prop('disabled', true);
            var portfolio_campaign_type = this.value;
            AjaxFunction(profile_fk_id, portfolio_campaign_type, sponsored_type);
        }
    }); // End on change functionality
    //edit_sponsored_type


    // On change for sponsored type
    $(".profileName").on('change', function () {
        var profile_fk_id = $(this).val();
        var type = $('#type').val();
        var sponsored_type = $('#sponsored_type').val();
        if (type != '') {
            AjaxFunction(profile_fk_id, type, sponsored_type);
        }
    }); // End on change functionality
    $(".sponsored_type").on('change', function () {
        var profile_fk_id = $('.profileName').val();
        var sponsored_type = $('#sponsored_type').val();
        var type = $('#type').val();
        if (sponsored_type == '' && type != '') {
            Swal.fire({type: 'error', html: 'Kindly Choose Sponsored Type First.', showConfirmButton: true});
            $("#pfCampaigns").empty();
            $(this).val();
        } else {
            $("#pfCampaigns").prop('disabled', true);
            AjaxFunction(profile_fk_id, type, sponsored_type);
        }
    }); // End on change functionality
    $("#fKPreSetRule").on('change', function () {
        var fKPreSetRule = $('#fKPreSetRule').val();
        $.ajax({
            type: "post",
            url: $('#presetRule').val(),
            data: {
                "id": fKPreSetRule,
                "_token": $("body").attr("csrf")
            },
            success: function (response) {

                var responseData = '';
                /*reset all fields starts*/
                $('#metric').val('');
                $('#condition').val('');
                $('#integerValues').val('');
                $('#lookBackPeriod').val('');
                $('#frequency').val('');
                $('#thenClause').val('');
                $('#bidBy').val('');
                jQuery('.removeButton').click();
                /*reset all fields ends*/

                if (response.ajax_status == true) {
                    var responseData = response.text[0];
                    var id = responseData.id;
                    var presetName = responseData.presetName;
                    var thenClause = responseData.thenClause;
                    var bidBy = responseData.bidBy;
                    var andOr = responseData.andOr;
                    var frequency = responseData.frequency;
                    var lookBackPeriod = responseData.lookBackPeriod;
                    var lookBackPeriodDays = responseData.lookBackPeriodDays;

                    if (andOr != 'NA') {
                        jQuery('#addButton').click();
                    } else {
                        jQuery('.removeButton').click();
                    }
                    // alert(metric);
                    var metric = responseData.metric;
                    var metricArr = metric.split(',');
                    if (typeof metricArr[0] !== "undefined" && metricArr[0]) {
                        $('#metric').val(metricArr[0]);
                    } else {
                        $('#metric').val('');
                    }
                    var condition = responseData.condition;
                    var conditionArr = condition.split(',');
                    if (typeof conditionArr[0] !== "undefined" && conditionArr[0]) {
                        var res0 = conditionArr[0].replace("lesser", "less");
                        $('#condition').val(res0);
                    } else {
                        $('#condition').val('');
                    }
                    var integerValues = responseData.integerValues;
                    var integerValuesArr = integerValues.split(',');
                    if (typeof integerValuesArr[0] !== "undefined" && integerValuesArr[0]) {
                        $('#integerValues').val(integerValuesArr[0]);
                    } else {
                        $('#integerValues').val('');
                    }
                    //$('#integerValues1').val('');

                    if (andOr != 'NA') {

                        $('#andOr').val(andOr);
                        if (typeof metricArr[1] !== "undefined" && metricArr[1]) {
                            $('#metric1').val(metricArr[1]);
                        } else {
                            $('#metric1').val('');
                        }
                        if (typeof conditionArr[1] !== "undefined" && conditionArr[1]) {
                            var res1 = conditionArr[1].replace("lesser", "less");
                            $('#condition1').val(res1);
                        } else {
                            $('#condition1').val('');
                        }
                        if (typeof integerValuesArr[1] !== "undefined" && integerValuesArr[1]) {
                            $('#integerValues1').val(integerValuesArr[1]);
                        } else {
                            $('#integerValues1').val('');
                        }
                    } else {
                        $('#metric1').val('');
                        $('#condition1').val('');
                        $('#integerValues1').val('');
                    }
//integerValues1

                    $('#lookBackPeriod').val(lookBackPeriod);
                    $('#frequency').val(frequency);


                    $('#thenClause').val(thenClause);
                    $('#bidBy').val(bidBy);
                } else {
                    // something went wrong
                }
            }
        });
    }); // End on change functionality

    $("#edit_fKPreSetRule").on('change', function () {
        var fKPreSetRule = $('#edit_fKPreSetRule').val();
        /*alert(fKPreSetRule);
        return false;*/
        $.ajax({
            type: "post",
            url: $('#presetRule').val(),
            data: {
                "id": fKPreSetRule,
                "_token": $("body").attr("csrf")
            },
            success: function (response) {

                var responseData = '';
                /*reset all fields starts*/
                $('#edit_metric').val('');
                $('#edit_condition').val('');
                $('#edit_integerValues').val('');
                $('#edit_lookBackPeriod').val('');
                $('#edit_frequency').val('');
                $('#edit_thenClause').val('');
                $('#edit_bidBy').val('');
                jQuery('.edit_removeButton').click();
                /*reset all fields ends*/

                if (response.ajax_status == true) {
                    var responseData = response.text[0];
                    var id = responseData.id;
                    var presetName = responseData.presetName;
                    var thenClause = responseData.thenClause;
                    var bidBy = responseData.bidBy;
                    var andOr = responseData.andOr;
                    var frequency = responseData.frequency;
                    var lookBackPeriod = responseData.lookBackPeriod;
                    var lookBackPeriodDays = responseData.lookBackPeriodDays;

                    if (andOr != 'NA') {
                        jQuery('#edit_addButton').click();
                    } else {
                        jQuery('.edit_removeButton').click();
                    }
                    // alert(metric);
                    var metric = responseData.metric;
                    var metricArr = metric.split(',');
                    if (typeof metricArr[0] !== "undefined" && metricArr[0]) {
                        $('#edit_metric').val(metricArr[0]);
                    } else {
                        $('#edit_metric').val('');
                    }
                    var condition = responseData.condition;
                    var conditionArr = condition.split(',');
                    if (typeof conditionArr[0] !== "undefined" && conditionArr[0]) {
                        var res0 = conditionArr[0].replace("lesser", "less");
                        $('#edit_condition').val(res0);
                    } else {
                        $('#edit_condition').val('');
                    }
                    var integerValues = responseData.integerValues;
                    var integerValuesArr = integerValues.split(',');
                    if (typeof integerValuesArr[0] !== "undefined" && integerValuesArr[0]) {
                        $('#edit_integerValues').val(integerValuesArr[0]);
                    } else {
                        $('#edit_integerValues').val('');
                    }

                    if (andOr != 'NA') {

                        $('#edit_andOr').val(andOr);
                        if (typeof metricArr[1] !== "undefined" && metricArr[1]) {
                            $('#edit_metric1').val(metricArr[1]);
                        } else {
                            $('#edit_metric1').val('');
                        }
                        if (typeof conditionArr[1] !== "undefined" && conditionArr[1]) {
                            var res1 = conditionArr[1].replace("lesser", "less");
                            $('#edit_condition1').val(res1);
                        } else {
                            $('#edit_condition1').val('');
                        }
                        if (typeof integerValuesArr[1] !== "undefined" && integerValuesArr[1]) {
                            $('#edit_integerValues1').val(integerValuesArr[1]);
                        } else {
                            $('#edit_integerValues1').val('');
                        }
                    } else {
                        $('#edit_metric1').val('');
                        $('#edit_condition1').val('');
                        $('#edit_integerValues1').val('');
                    }
//integerValues1

                    $('#edit_lookBackPeriod').val(lookBackPeriod);
                    $('#edit_frequency').val(frequency);


                    $('#edit_thenClause').val(thenClause);
                    $('#edit_bidBy').val(Math.round(bidBy));
                } else {
                    // something went wrong
                }
            }
        });
    }); // End on change functionality
    var AjaxFunction = function (profile_fk_id, portfolio_campaign_type_value, sponsored_type_value) {
        $.ajax({
            type: "post",
            url: $('#campaignListUrl').val(),
            data: {
                "profile_fk_id": profile_fk_id,
                "portfolio_campaign_type": portfolio_campaign_type_value,
                "sponsored_type": sponsored_type_value,
                "_token": $("body").attr("csrf")
            },
            success: function (response) {
                if (response.ajax_status == true) {
                    if (response.type == 'Brand Assigned') {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2000
                        });

                        Toast.fire({
                            type: 'fail',
                            title: response.text
                        });
                    }
                    $("#pfCampaigns").empty();
                    if (response.text.length > 0) {
                        $("#pfCampaigns").prop('disabled', false);
                        $("#pfCampaigns").empty();
                        var responseData = '';
                        var responseData = response.text;
                        $.each(responseData, function (label, respo) {
                            $('<option value="' + respo.id + '" >' + respo.name + '</option>').appendTo($('#pfCampaigns'));
                        });
                    } else {
                        Swal.fire({type: 'error', html: 'No Data found!', showConfirmButton: true});
                    }

                } else {
                    // something went wrong
                }
            }
        });
    }
    $("#edit_type").on('change', function () {
        var profile_fk_id = $('#edit_profileid').val();
        var sponsored_type = $('#edit_sponsored_type').val();
        if (sponsored_type == '') {
            Swal.fire({type: 'error', html: 'Kindly Choose Sponsored Type First.', showConfirmButton: true});
            $("#edit_pfCampaigns").empty();
            $(this).val();
        } else {
            $("#edit_pfCampaigns").prop('disabled', true);
            var portfolio_campaign_type = this.value;
            editAjaxFunction(profile_fk_id, portfolio_campaign_type, sponsored_type);
        }
    }); // End on change functionality
    // On change for sponsored type
    $("#edit_profileid").on('change', function () {
        var profile_fk_id = $('#edit_profileid').val();
        var type = $('#edit_type').val();
        var sponsored_type = $('#edit_sponsored_type').val();
        if (type != '') {
            editAjaxFunction(profile_fk_id, type, sponsored_type);
        }
    }); // End on change functionality

    $("#edit_sponsored_type").on('change', function () {
        var profile_fk_id = $('#edit_profileid').val();
        var sponsored_type = $('#edit_sponsored_type').val();
        var type = $('#edit_type').val();
        if (sponsored_type == '' && type != '') {
            Swal.fire({type: 'error', html: 'Kindly Choose Sponsored Type First.', showConfirmButton: true});
            $("#edit_pfCampaigns").empty();
            $(this).val();
        } else {
            $("#edit_pfCampaigns").prop('disabled', true);
            editAjaxFunction(profile_fk_id, type, sponsored_type);
        }
    }); // End on change functionality
    var editAjaxFunction = function (profile_fk_id, portfolio_campaign_type_value, sponsored_type_value, selected_campaigns = false) {
        $.ajax({
            type: "post",
            url: $('#campaignListUrl').val(),
            data: {
                "profile_fk_id": profile_fk_id,
                "portfolio_campaign_type": portfolio_campaign_type_value,
                "sponsored_type": sponsored_type_value,
                "_token": $("body").attr("csrf")
            },
            success: function (response) {
                if (response.ajax_status == true) {
                    if (response.type == 'Brand Assigned') {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2000
                        });

                        Toast.fire({
                            type: 'fail',
                            title: response.text
                        });
                    }
                    $("#edit_pfCampaigns").empty();
                    if (response.text.length > 0) {
                        $("#edit_pfCampaigns").prop('disabled', false);
                        $("#edit_pfCampaigns").empty();
                        var responseData = '';
                        var responseData = response.text;
                        $.each(responseData, function (label, respo) {
                            $('<option value="' + respo.id + '" >' + respo.name + '</option>').appendTo($('#edit_pfCampaigns'));
                        });

                        $("#edit_pfCampaigns").val(selected_campaigns);
                        //$("#edit_pfCampaigns").val(["85", "86"]);
                    } else {
                        Swal.fire({type: 'error', html: 'No Data found!', showConfirmButton: true});
                    }

                } else {
                    // something went wrong
                }
            }
        });
    }
    $('#staticEditModal').on('hidden.bs.modal', function (e) {
        $('#editcc_emailBS').val('[]');
        $('#editBiddingRuleForm').get(0).reset();
    });

    // On click show edit Form
    $('#biddingRuleDataTable').on('click', '.editModal', function () {

        $(".js-example-basic-multiple-edit").select2({
            placeholder: 'Choose'
        });
        $("#pfCampaigns").select2('val', '[]');
        $("#edit_pfCampaigns").select2('val', '[]');
        jQuery('.edit_removeButton').click();
        var selectedpreset = $(this).data('selectedpreset');
        if (selectedpreset != 0) {
            $('#edit_fKPreSetRule').val(selectedpreset);
        }
        var andOr = $(this).data('andor');
        if (andOr != 'NA') {
            jQuery('#edit_addButton').click();
        } else {
            jQuery('.edit_removeButton').click();
        }

        /*bid rule statement starts*/
        var metric = $(this).data('metric');
        var metricArr = metric.split(',');
        if (typeof metricArr[0] !== "undefined" && metricArr[0]) {

            $('#edit_metric').val(metricArr[0]);
        } else {
            $('#edit_metric').val('');
        }
        var condition = $(this).data('condition');
        var conditionArr = condition.split(',');
        if (typeof conditionArr[0] !== "undefined" && conditionArr[0]) {
            var res0 = conditionArr[0].replace("lesser", "less");
            $('#edit_condition').val(res0);
        } else {
            $('#edit_condition').val('');
        }
        var integerValues = $(this).data('integervalues') + '';
        var integerValuesArr = integerValues.split(',');
        if (typeof integerValuesArr[0] !== "undefined" && integerValuesArr[0]) {
            $('#edit_integerValues').val(integerValuesArr[0]);
        } else {
            $('#edit_integerValues').val('');
        }

        if (andOr != 'NA') {

            $('#edit_andOr').val(andOr);
            if (typeof metricArr[1] !== "undefined" && metricArr[1]) {
                $('#edit_metric1').val(metricArr[1]);
            } else {
                $('#edit_metric1').val('');
            }
            if (typeof conditionArr[1] !== "undefined" && conditionArr[1]) {
                var res1 = conditionArr[1].replace("lesser", "less");
                $('#edit_condition1').val(res1);
            } else {
                $('#edit_condition1').val('');
            }
            if (typeof integerValuesArr[1] !== "undefined" && integerValuesArr[1]) {
                $('#edit_integerValues1').val(integerValuesArr[1]);
            } else {
                $('#edit_integerValues1').val('');
            }
        } else {
            $('#edit_metric1').val('');
            $('#edit_condition1').val('');
            $('#edit_integerValues1').val('');
        }
        /*bid rule statement ends*/
        $('#edit_lookBackPeriod').val($(this).data('lookbackperiod'));
        $('#edit_frequency').val($(this).data('frequency'));
        $('#edit_thenClause').val($(this).data('thenclause'));
        //alert($(this).data('bidby'));
        var bidby = $(this).data('bidby');
        //alert(bidby);
        $('#edit_bidBy').val(Math.round(bidby));
        strEmail = $(this).data('ccemails');
        arrayEmail = strEmail.split(',');
        if (strEmail != 'NA' && strEmail != '') {
            var replaceColonByComma = strEmail.replace(/\;/g, ',');
            var preAppendEmailDataArray = replaceColonByComma.split(',');
        } else {
            preAppendEmailDataArray = [];
        }

        // $('#editBiddingRuleForm #editcc_emailBS').val('[]');
        $('#editcc_emailBS').val(JSON.stringify(preAppendEmailDataArray));
        $('#editBiddingRuleForm .multiple_emails-container').remove();
        $('#editBiddingRuleForm .multiple_emails-ul li').remove();
        $('#editcc_emailBS').multiple_emails({position: "bottom"});
        // $('select[id^="edit_pfCampaigns"] option[value="' + portfolioCampaignList + '"]').attr("selected", "selected");
        ccEmailTooltip();
        $('#edit_pfCampaigns').empty();
        $('#edit_bidRuleId').val($(this).data('id'));
        $('#edit_ruleName').val($(this).data('rulename'));
        $('#edit_sponsored_type').val($(this).data('sponsoredtype'));
        $('#edit_type').val($(this).data('type'));
        $('#edit_profileid').val($(this).data('profileid'));
        $single = $(this).data('pfcampaigns').split(',');
        var profile_fk_id = $(this).data('profileid');
        var type = $(this).data('type');
        var sponsored_type = $(this).data('sponsoredtype');
        var edit_pfCampaigns_val = $(this).data('pfcampaigns');
        var selectedCampaigns = [];
        for (x of $single) {
            idValue = x.split('@');
            //$('<option value="'+idValue[0]+'" selected>' + idValue[1] + '</option>').appendTo($('#edit_pfCampaigns'));
            selectedCampaigns.push(idValue[0]);
        }
        if (edit_sponsored_type != '') {
            var show_campaingns = editAjaxFunction(profile_fk_id, type, sponsored_type, selectedCampaigns);
        }
        $('#staticEditModal').modal('show');
    });

    // open delete modal for confirmation
    $('#biddingRuleDataTable').on('click', '.deleteModal', function (e) {
        e.preventDefault();
        let id = $(this).data('id');
        $('#delete_bidding_rule_id').val(id);
        $('#staticDeleteModal').modal('show');
    });
    // delete modal ajax call
    $('#biddingRuleFormDelete').on('submit', function (e) {
        e.preventDefault();
        var url = $(this).data('action');
        $.ajax({
            type: "post",
            url: url,
            data: $(this).serializeArray(),
            success: function (response) {
                // Check response your status
                if (response.status == 'fail') {
                    $errorMessage = '';
                    Swal.fire({
                        title: '<strong>' + response.title + '</strong>',
                        type: 'info',
                        html:
                        response.message,
                        showCloseButton: true,
                        showCancelButton: true,
                        focusConfirm: false,
                        confirmButtonText:
                            '<i class="fa fa-thumbs-up"></i> Great!',
                        confirmButtonAriaLabel: 'Thumbs up, great!',
                        cancelButtonText:
                            '<i class="fa fa-thumbs-down"></i>',
                        cancelButtonAriaLabel: 'Thumbs down',
                    });
                    setTimeout(function () {
                        location.reload();
                    }, 2000);
                } else {
                    //
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000
                    });

                    Toast.fire({
                        type: 'success',
                        title: response.message
                    });
                }
                setTimeout(function () {
                    location.reload();
                }, 2000);
            },
            error: function (e) {
                if (e.responseText.includes("Unauthenticed")) {
                    location.reload();
                }
            }
        });
    });
});
var t;

// Datatables
function loadDatatables(fieldId, route) {
    $.fn.dataTable.ext.errMode = "none";
    t = $('#' + fieldId).removeAttr('width').DataTable({
        paging: true,
        "processing": true,
        "serverSide": true,
        "responsive": false,
        "ajax": route,
        "columns": [
            {"data": "sr_no"},
            {"data": "rulename"},
            {"data": "type"},
            {"data": "included"},
            {"data": "rule"},
            {"data": "frequency"},
            {"data": "statement"},
            {"data": "action"}
        ],
        "columnDefs": [
            {"width": "8%", "targets": 0},
            {"width": "15%", "targets": 1},
            {"width": "5%", "targets": 2},
            {"width": "5%", "targets": 3},
            {"width": "5%", "targets": 4},
            {"width": "10%", "targets": 5},
            {"width": "42%", "targets": 6},
            {"width": "10%", "targets": 7},
        ],
        "drawCallback": function (settings) {
            // Tool Tip Tipster
            $('.tooltip-biddingRule').tooltipster({
                'interactive': true,
                'contentAsHTML': true,
                'trigger': 'click',
                "side": "right",
                plugins: ['tooltipster.sideTip', 'laa.scrollableTip'],
            });
            $('.tooltip-columns').tooltipster({
                'interactive': true,
                'contentAsHTML': true,
                'trigger': 'hover',
                "side": "top",
                plugins: ['tooltipster.sideTip', 'laa.scrollableTip'],
            });
        }
    });
}

function ccEmailTooltip() {
    $('.multiple_emails-input').tooltipster({
        content: 'Press tab, comma or space to add multiple emails',
    }).focus(function () {
        $(this).tooltipster('show');
    });
}
