var fv;
document.addEventListener('DOMContentLoaded', function (e) {
    const jqueryFromName = $('#biddingRuleForm');
    const bidRuleSchedule = document.getElementById('biddingRuleForm');
    const pfCampaignsField = jQuery(bidRuleSchedule.querySelector('[name="pfCampaigns[]"]'));
    // Validator rules
    // Will be reused later when we add dynamic fields
    const metricValidators = {
        validators: {
            notEmpty: {
                message: 'This field is required'
            },
            stringLength: {
                min: 1,
                max: 60
            },
            regexp: {
                regexp: /^[a-zA-Z0-9]+$/,
                message: 'Only alphanumeric characters'
            }
        }
    };
    const conditionValidators = {
        validators: {
            notEmpty: {
                message: 'This field is required'
            }
        }
    };
    const integerValuesValidators = {
        validators: {
            notEmpty: {
                message: 'This field is required'
            },
            regexp: {
                regexp: /(<=\s|^)\d+(?=\s|$)/,
                message: 'Only contain numeric values'
            },
            stringLength: {
                max: 15,
                message: 'Maximum length exceeded'
            },
        }
    };
    const andOrValuesValidators = {
        validators: {
            notEmpty: {
                message: 'AND / OR is required.'
            }
        }
    };
    // FormValidation instance
     fv = FormValidation.formValidation(bidRuleSchedule, {
        fields: {
            ruleName: {
                validators: {
                    stringLength: {
                        max: 100,
                        message: 'The rule name must be less than 100 characters'
                    },
                    notEmpty: {
                        message: 'This field is required'
                    }
                }
            },
            sponsoredType: {
                validators: {
                    notEmpty: {
                        message: 'This field is required'
                    }
                }
            },
            profileFkId: {
                validators: {
                    notEmpty: {
                        message: 'This field is required'
                    }
                }
            },
            type: {
                validators: {
                    notEmpty: {
                        message: 'This field is required'
                    }
                }
            },
            /*'pfCampaigns[]': {
                validators: {
                    notEmpty: {
                        message: 'This field is required'
                    }
                }
            },*/
            'pfCampaigns[]': {
                validators: {
                    callback: {
                        message: 'This field is required',
                        callback: function (input) {
                            // Get the selected options
                            const options = pfCampaignsField.select2('data');
                            return (options != null && options.length >= 1);
                        }
                    }
                }
            },
            lookBackPeriod: {
                validators: {
                    notEmpty: {
                        message: 'This field is required'
                    }
                }
            },
            frequency: {
                validators: {
                    notEmpty: {
                        message: 'This field is required'
                    }
                }
            },
            thenClause: {
                validators: {
                    notEmpty: {
                        message: 'This field is required'
                    }
                }
            },
            bidBy: {
                validators: {
                    notEmpty: {
                        message: 'This field is required'
                    },
                    regexp: {
                        regexp: /(<=\s|^)\d+(?=\s|$)/,
                        message: 'Only contain numeric values'
                    },
                    stringLength: {
                        max: 15,
                        message: 'Maximum length exceeded'
                    },
                    /*,
                    regexp: {
                        regexp: /^0*(?:[1-9][0-9]?|100000)$/,
                        message: 'Maximum length exceeded'
                    }*/

                }
            },
            'metric[0]': metricValidators,
            'condition[0]': conditionValidators,
            'andOr[0]': andOrValuesValidators,
            'integerValues[0]': integerValuesValidators,
        },
        plugins: {
            trigger: new FormValidation.plugins.Trigger(),
            bootstrap: new FormValidation.plugins.Bootstrap(),
            submitButton: new FormValidation.plugins.SubmitButton(),
            defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
            excluded: new FormValidation.plugins.Excluded({
                excluded: function (field, ele, eles) {
                    triggerButotn = fv.plugins.submitButton.clickedButton.classList;
                    if ($.inArray("saveRule", triggerButotn) != -1) {
                        switch (field) {
                            case "sponsoredType":
                                return true;
                                break;
                            case "profileFkId":
                                return true;
                                break;
                            case "type":
                                return true;
                                break;
                            case "pfCampaigns[]":
                                return true;
                                break;
                        }
                    }
                },
            }),
        },
    }).on('core.form.valid', function ()
    {
        triggerButotn = fv.plugins.submitButton.clickedButton.classList;
        if ($.inArray("saveRule", triggerButotn) != -1) {
            saveAsRuleAjax();
        }
        else
        {
            onBiddingRuleFormSubmit();
        }
    });
    // remove selected row of if condition
    function onBiddingRuleFormSubmit() {

        var Url = jqueryFromName.data('action');
        var formData = new FormData();
        var params = jqueryFromName.serializeArray();
        var timer = 3000;
        // Get simple form data
        $.each(params, function (i, val) {
            formData.append(val.name, val.value);
        });
        axios.post(Url, formData).then(function (response) {
            // Check response your status
            if (response.data.status == 'fail') {
                $errorMessage = '';
                if (response.data.message.ruleName) {
                    $errorMessage = response.data.message.ruleName + '<br/>';
                } else if (response.data.message.ccEmails) {
                    $errorMessage = response.data.message.ccEmails + '<br/>';
                }else{
                    $errorMessage = response.data.message + '<br/>';
                }
                Swal.fire({
                    title: '<strong>Error</strong>',
                    type: 'error',
                    html: $errorMessage.replace(/\n/g, "<br/>")
                })
            } else {
                //
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: timer
                });

                Toast.fire({
                    type: 'success',
                    title: response.data.message
                });
                setTimeout(function () {
                    location.reload();
                }, 2000);
            }
        });
    }
    function saveAsRuleAjax() {
        var action = $('#biddingRuleForm').attr('data-save-as-rule');
        var data = $('#biddingRuleForm').serializeArray();
        var timer = 3000;
        $.ajax({
            type: "post",
            url: action,
            data: data,
            success: function (response) {

                if (response.status == 'fail') {
                    $errorMessage = '';
                    $errorMessage = response.message.ruleName + '<br/>';
                    /*$mess = "";
                    $.each($errorMessage, function (indexInArray, valueOfElement) {
                        $mess += valueOfElement + "\n";
                    });*/
                    Swal.fire({
                        title: '<strong>Error</strong>',
                        type: 'error',
                        //html: $mess.replace(/\n/g, "<br/>")
                        html: $errorMessage
                    })

                } else {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: timer
                    });
                    Toast.fire({
                        type: 'success',
                        title: response.message
                    });
                    setTimeout(function () {
                        location.reload();
                    }, 2000);
                }
            }
        });
    }
    const removeRow = function (rowIndex) {
        const row = bidRuleSchedule.querySelector('[data-row-index="' + rowIndex + '"]');
        // Remove field
        fv.removeField('metric[' + rowIndex + ']')
            .removeField('condition[' + rowIndex + ']')
            .removeField('andOr[' + rowIndex + ']')
            .removeField('integerValues[' + rowIndex + ']');
        // Remove row
        row.parentNode.removeChild(row);
        if (rowIndex != '') {
            elem = document.getElementById('addButton');
            elem.style.display = "block";
            return false;
        }
    };
    // The event handler for `addButton`
    let rowIndex = 0;
    const template = document.getElementById('template');
    document.getElementById('addButton').addEventListener('click', function () {
        rowIndex++;

        const clone = template.cloneNode(true);
        clone.removeAttribute('id');
        clone.classList.remove('d-none');
        clone.setAttribute('data-row-index', rowIndex);

        // Insert before the template
        template.before(clone);
        clone.querySelector('[data-name="metric"]').setAttribute('name', 'metric[' + rowIndex + ']');
        clone.querySelector('[data-name="condition"]').setAttribute('name', 'condition[' + rowIndex + ']');
        clone.querySelector('[data-name="andOr"]').setAttribute('name', 'andOr[' + rowIndex + ']');
        clone.querySelector('[data-name="integerValues"]').setAttribute('name', 'integerValues[' + rowIndex + ']');

        // Add new fields
        // Note that we also pass the validator rules for new field as the third parameter
        fv.addField('metric[' + rowIndex + ']', metricValidators)
            .addField('condition[' + rowIndex + ']', conditionValidators)
            .addField('andOr[' + rowIndex + ']', andOrValuesValidators)
            .addField('integerValues[' + rowIndex + ']', integerValuesValidators);

        // Handle the click event of `removeButton`
        const removeBtn = clone.querySelector('.removeButton');
        removeBtn.setAttribute('data-row-index', rowIndex);
        removeBtn.addEventListener('click', function (e) {
            // Get the row index
            const index = e.currentTarget.getAttribute('data-row-index');
            removeRow(index);
        });
        if (rowIndex != '') {
            elem = document.getElementById('addButton');
            elem.style.display = "none";
            return false;
        }
    });
    // Edit Form Validation
    /*const jqueryFromName = $('#biddingRuleForm');
    const bidRuleSchedule = document.getElementById('biddingRuleForm');*/

    const editBiddingRuleFormJquery = $('#editBiddingRuleForm');
    const editBiddingRuleForm = document.getElementById('editBiddingRuleForm');
    const edit_pfCampaignsField = jQuery(editBiddingRuleForm.querySelector('[name="pfCampaigns[]"]'));

    // FormValidation instance
    const fv2 = FormValidation.formValidation(editBiddingRuleForm,
        {
        fields: {
            ruleName: {
                validators: {
                    stringLength: {
                        max: 100,
                        message: 'The rule name be less than 200 characters'
                    },
                    notEmpty: {
                        message: 'This field is required'
                    }
                }
            },
            sponsoredType: {
                validators: {
                    notEmpty: {
                        message: 'This field is required'
                    }
                }
            },
            type: {
                validators: {
                    notEmpty: {
                        message: 'This field is required'
                    }
                }
            },
/*            'pfCampaigns[]': {
                validators: {
                    notEmpty: {
                        message: 'This field is required'
                    }
                }
            },*/
            'pfCampaigns[]': {
                validators: {
                    callback: {
                        message: 'This field is required',
                        callback: function (input) {
                            // Get the selected options
                            const options = edit_pfCampaignsField.select2('data');
                            return (options != null && options.length >= 1);
                        }
                    }
                }
            },
            lookBackPeriod: {
                validators: {
                    notEmpty: {
                        message: 'This field is required'
                    }
                }
            },
            frequency: {
                validators: {
                    notEmpty: {
                        message: 'This field is required'
                    }
                }
            },
            thenClause: {
                validators: {
                    notEmpty: {
                        message: 'This field is required'
                    }
                }
            },
            bidBy: {
                validators: {
                    notEmpty: {
                        message: 'This field is required'
                    },
                    regexp: {
                        regexp: /(<=\s|^)\d+(?=\s|$)/,
                        message: 'Only contain numeric values'
                    }
                }
            },
            'metric[0]': metricValidators,
            'condition[0]': conditionValidators,
            'andOr[0]': andOrValuesValidators,
            'integerValues[0]': integerValuesValidators,
        },
        plugins: {
            trigger: new FormValidation.plugins.Trigger(),
            bootstrap: new FormValidation.plugins.Bootstrap(),
            submitButton: new FormValidation.plugins.SubmitButton(),
            defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
        },
    }).on('core.form.valid', function ()
    {
        var Url = editBiddingRuleFormJquery.data('action');
        var formData = new FormData();
        var params = editBiddingRuleFormJquery.serializeArray();
        var timer = 3000;
        // Get simple form data
        $.each(params, function (i, val) {
            formData.append(val.name, val.value);
        });
        axios.post(Url, formData).then(function (response) {
            /*console.log(response);
            return false;*/
          // Check response your status
            if (response.data.status == 'fail') {
                $errorMessage = '';
                if (response.data.message.ruleName) {
                    $errorMessage = response.data.message.ruleName + '<br/>';
                }
                Swal.fire({
                    title: '<strong>' + response.data.title + '</strong>',
                    type: 'info',
                    html:
                    $errorMessage,
                    showCloseButton: true,
                    showCancelButton: true,
                    focusConfirm: false,
                    confirmButtonText:
                        '<i class="fa fa-thumbs-up"></i> Great!',
                    confirmButtonAriaLabel: 'Thumbs up, great!',
                    cancelButtonText:
                        '<i class="fa fa-thumbs-down"></i>',
                    cancelButtonAriaLabel: 'Thumbs down',
                })
            } else {
                //
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: timer
                });

                Toast.fire({
                    type: 'success',
                    title: response.data.message
                });
                setTimeout(function () {
                    location.reload();
                    document.getElementById("editBiddingRuleForm").reset();
                }, 2000);
            }
        });
    });

     /*edit modal append buttons starts*/
    const removeRow2 = function (rowIndex2) {

        const row2 = editBiddingRuleForm.querySelector('[data-row-index="' + rowIndex2 + '"]');
        // Remove field
        fv2.removeField('metric[' + rowIndex2 + ']')
            .removeField('condition[' + rowIndex2 + ']')
            .removeField('andOr[' + rowIndex2 + ']')
            .removeField('integerValues[' + rowIndex2 + ']');
        // Remove row
        row2.parentNode.removeChild(row2);
        if (rowIndex2 != '') {
            elem = document.getElementById('edit_addButton');
            elem.style.display = "block";
            return false;
        }
    };
    // The event handler for `addButton`
    let rowIndex2 = 0;
    const template2 = document.getElementById('edit_template');
    document.getElementById('edit_addButton').addEventListener('click', function () {
        rowIndex2++;

        const clone2 = template2.cloneNode(true);
        clone2.removeAttribute('id');
        clone2.classList.remove('d-none');
        clone2.setAttribute('data-row-index', rowIndex2);

        // Insert before the template
        template2.before(clone2);
        clone2.querySelector('[data-name="metric"]').setAttribute('name', 'metric[' + rowIndex2 + ']');
        clone2.querySelector('[data-name="condition"]').setAttribute('name', 'condition[' + rowIndex2 + ']');
        clone2.querySelector('[data-name="andOr"]').setAttribute('name', 'andOr[' + rowIndex2 + ']');
        clone2.querySelector('[data-name="integerValues"]').setAttribute('name', 'integerValues[' + rowIndex2 + ']');

        // Add new fields
        // Note that we also pass the validator rules for new field as the third parameter
        fv2.addField('metric[' + rowIndex2 + ']', metricValidators)
            .addField('condition[' + rowIndex2 + ']', conditionValidators)
            .addField('andOr[' + rowIndex2 + ']', andOrValuesValidators)
            .addField('integerValues[' + rowIndex2 + ']', integerValuesValidators);

        // Handle the click event of `removeButton`
        const removeBtn2 = clone2.querySelector('.edit_removeButton');
        removeBtn2.setAttribute('data-row-index', rowIndex2);
        removeBtn2.addEventListener('click', function (e) {
            // Get the row index
            const index2 = e.currentTarget.getAttribute('data-row-index');
            removeRow2(index2);
        });
        if (rowIndex2 != '') {
            elem2 = document.getElementById('edit_addButton');
            elem2.style.display = "none";
            return false;
        }
    });
    /*edit modal append button ends*/

});

$(document).ready(function() {
    $(window).keydown(function(event){
        if(event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });
});