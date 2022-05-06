
$(function () {
    var t = $('#dataTable').DataTable( {
        "ordering": true,
        "processing": true,
        "responsive": true,

        drawCallback: function(settings) {   
            $('.tooltip').tooltipster({
                interactive:true,
                maxWidth:300,
                debug: false
            });
        }
    });
    currentDate = moment().format("YYYY-MM-DD");
    var statDate = moment(currentDate, "YYYY-MM-DD").subtract( 6,'days').format("YYYY-MM-DD");
  
    $('#occurrenceDate').daterangepicker({
        opens: 'left', 
        drops:'down',
        parentEl: $("#addProductPreviewModel .modal-body"),
        autoUpdateInput: true,
        linkedCalendars: false,
        startDate:statDate ,
        endDate: currentDate,
        autoApply:true,
        locale: {
            format: 'YYYY-MM-DD'
        },
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
         },
        "alwaysShowCalendars": true,
    }, function(start, end, label) {
        currentDate = start;
        end_date = end;
        fv.revalidateField('occurrenceDate');
    });
    $('.tooltip').tooltipster({
        interactive:true,
        maxWidth:300,
        debug: false
    });
  
    // $('div.userActions, div.events').tooltipster({
    //     interactive:true,
    //     minWidth:500,
    //     side:['top', 'left'],
    //     trigger:'click'
    // });
    $(".events > input, .events > label, .userActions > input, .userActions > label").click(function (e) { 
        $(".custonTooltip").hide()
        if($(this).parent().children("input").prop("checked") == true){
            $(this).parent().children(".custonTooltip").fadeIn("fast")
        }
        else
        {
            // $(this).parent().children(".custonTooltip").find("textarea").val("");
        }
    });
    $(".custonTooltip").click(function (e) { 
        e.stopPropagation();
    });
    $(".custonTooltip").blur(function (e) { 
        $(this).hide();
    }); 
   
    $('#addProductPreviewModel').on('hidden.bs.modal', function (e) {
        $("#addProductPreviewForm").get(0).reset();
        fv.resetForm(true);
        $(".custonTooltip").hide()
    })
    $(".calendarIcon").click(function() {
        $(this).parent().find("input").click();
    }).children().on("click", function(e) {
        e.stopPropagation();
    });
    const form = document.getElementById('addProductPreviewForm');
    const fv = FormValidation.formValidation(form, {
        fields: {
            
            accountType: {
                validators: {
                    notEmpty: {
                        message: 'This field is required'
                    },
                    choice: {
                        min: 1,
                        max: 1,
                        message: 'Please select one to continue'
                    }
                }
            },
            marketPlaceId: {
                validators: {
                    notEmpty: {
                        message: 'This field is required'
                    },
                    choice: {
                        min: 1,
                        max: 1,
                        message: 'Please select one Marketplace Id to continue'
                    }
                }
            },
            asinCustom: {
                validators: {
                    notEmpty: {
                        message: 'This field is required'
                    },
                    stringLength: {
                        min: 10,
                        max: 10,
                        message: 'ASIN must be 10 characters'
                    },
                    regexp: {
                        regexp: /^[a-zA-Z0-9]+$/,
                        message: 'The ASIN can only consist of alphabetical  and  number'
                    }
                }
            },
            notes: {
                validators: {
                    stringLength: {
                        max: 100,
                        message: 'Notes must not be greater than 100 characters'
                    },
                    regexp: {
                        regexp: /^[a-zA-Z0-9_\n ]+$/,
                        message: 'The Notes can only consist of alphabetical, number and underscore'
                    }
                }
            },
            'checkPoint[]': {
                validators: {
                    notEmpty: {
                        message: 'These fields are required'
                    }
                }
            },
            occurrenceDate: {
                validators: {
                    notEmpty: {
                        message: 'This field is required'
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
        
        $("#addProductPreviewModel .preloadOnDropdownChange").fadeIn();
        var DatatoUpload = new FormData();

        if( window.FormData === undefined )
        {
            $("#addProductPreviewModel .preloadOnDropdownChange").fadeOut();
            Swal.fire({
                title: '<strong>Error</strong>',
                type: 'error',
                text:"Sorry you browser Dose not support Form Data, Please use latest browser (suggestion:Google Chrome) "
            })

            return;
        }
        start = $('input[name="occurrenceDate"]').data('daterangepicker').startDate.format('YYYY-MM-DD');
        end = $('input[name="occurrenceDate"]').data('daterangepicker').endDate.format('YYYY-MM-DD');
        start = moment(start,"YYYY-MM-DD")
        end = moment(end,"YYYY-MM-DD")
        diff = end.diff(start, "days");
        var userActionsChecked = $(".userActions input[name='checkPoint[]']:checked");
        var userActionNotes = [];
        $.each(userActionsChecked, function(){
            tempValue = $(this).parent().find("textarea").val();
            if(tempValue.length <= 0)
            tempValue = "NA";
            userActionNotes[$(this).val().split("|")[1]] = tempValue;
        });
        
        var eventsChecked = $(".events input[name='checkPoint[]']:checked");
        var eventsNotes = [];
        $.each(eventsChecked, function(){
            tempValue = $(this).parent().find("textarea").val();
            if(tempValue.length <= 0)
            tempValue = "NA";
            eventsNotes[$(this).val().split("|")[1]] = tempValue;
        });
      
        occurrenceDates = [];
        occurrenceDates.push(start.format('YYYY-MM-DD'));
        for (let index = 1; index <= diff; index++) {
          date =  moment(start,"YYYY-MM-DD").add(index, 'days');
          occurrenceDates.push(date.format('YYYY-MM-DD'));
        }

        var checkPoint = $("input[name='checkPoint[]']:checked");
        var fkuserActionEventIds = [];
        $.each(checkPoint, function(){
            fkuserActionEventIds.push($(this).val());
        });
        
      
        var clients = $("#addProductPreviewForm #clients").val();
        var accountType = $("#addProductPreviewForm #accountType").val();
        var asin = $("#addProductPreviewForm #asinCustom").attr("customvalue");
        DatatoUpload.append("asin",asin);
        DatatoUpload.append("occurrenceDates",JSON.stringify(occurrenceDates));
        DatatoUpload.append("fkuserActionEventIds",fkuserActionEventIds);
        DatatoUpload.append("userActionNotes",JSON.stringify(userActionNotes));
        DatatoUpload.append("eventsNotes",JSON.stringify(eventsNotes));
        DatatoUpload.append("_token",$("#addProductPreviewForm input[type='hidden']").val());

        $.ajax({
            type: "post",
            url: $("#addProductPreviewForm").attr("action"),
            data:DatatoUpload,
            contentType: false,
            processData: false,
            success: function (response) {
                $("#addProductPreviewModel .preloadOnDropdownChange").fadeOut();
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
                        $("#addProductPreviewForm")[0].reset();
                        resetEditSettings();
                        fv.resetForm(true);
                        $("#addProductPreviewModel .preloadOnDropdownChange").fadeOut();
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
                    $("#addProductPreviewModel .preloadOnDropdownChange").fadeOut();
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
            },
            error:function(e){
                if(e.responseText.includes("Unauthenticed")){
                    location.reload();
                }
                else
                { 
                    $("#addProductPreviewModel .preloadOnDropdownChange").fadeOut();
                    Swal.fire({
                        title: '<strong>Error</strong>',
                        type: 'error',
                        text:"Some thing went wrong"
                    })
                }
            }
        });
    });
    function resetEditSettings(){
        $("#clients option").removeAttr("selected");
        $('#customRadio1').attr('checked','checked');
        $("#clients option:nth-child(1),#accountType option:nth-child(1),#category option:nth-child(1),#subCategory option:nth-child(1),#asin option:nth-child(1)").attr("selected");
        $("form#addProductPreviewForm .form-control,.cronList tr").removeClass("onEdit");
        $("form#addProductPreviewForm button[type='reset']").hide();
        $("form#addProductPreviewForm").attr("action_type",1);
    }

    var rowID = null;

    $(".cronList").on('click','i.fa-trash',function (e) {
        row = $(this).parents("tr");
        $(row).parent().children('tr').removeClass("onEdit");
        $(row).addClass("onEdit");
        rowID = $(this).parents("tr").attr("id");
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
            Swal.fire({
                title: '<strong>'+result.value.title+'</strong>',
                type: 'error',
                text:"Please See Console"
            })
        }
    })
    });//end function

    $(".onDropDownChange").change(function (e) {
        e.preventDefault();
        

        var DatatoUpload = new FormData();
        if( window.FormData === undefined )
        {
            Swal.fire({
                title: '<strong>Error</strong>',
                type: 'error',
                text:"Sorry you browser Dose not support Form Data, Please use latest browser (suggestion:Google Chrome)"
            });
            return;
        }//end if
        
        dropDownName = $(this).attr("name");
        dropDownValue = $(this).val();
      
        if(dropDownValue =="")
        return;

        $("#addProductPreviewModel .preloadOnDropdownChange").fadeIn();
        DatatoUpload.append("dropDownName",dropDownName);
        DatatoUpload.append("dropDownValue",dropDownValue);
        DatatoUpload.append("_token",$("body").attr("csrf"));
        base_url = $("body").attr("base_url");
        $.ajax({
            type: "post",
            url: base_url+("/client/getRequiredData"),
            data:DatatoUpload,
            contentType: false,
            processData: false,
            success: function (response) {
                $("#addProductPreviewModel .preloadOnDropdownChange").fadeOut();
                if(response.status)
                {
                    result = response.result;
                    nextDropDownName = response.dropDownName;
                    if(result.length>0) {
                        selectOptions = '';
                        $.each(result, function (indexInArray, valueOfElement) { 
                            attr1 = (valueOfElement.attr1);
                            attr2 =  (valueOfElement.product_alias != null && valueOfElement.product_alias.length > 0 ?(valueOfElement.product_alias[0].overrideLabel == null ? valueOfElement.attr2 : valueOfElement.product_alias[0].overrideLabel )  : valueOfElement.attr2);
                            selectOptions +='<li asin = "'+dropDownValue+'|'+attr1+'" class="options"><span class="text">'+attr1+' => '+(attr2.length > 70 ? attr2.slice(0, 70)+'...':attr2)+'</span></li>';
                        });
                        $(".NoResult").hide();
                        if($("#asinCustom").val().length > 0){
                            $("#asinCustom").val("");
                            fv.revalidateField("asinCustom")
                        }
                        $(".mainDropDown ul li.options:not(.NoResult)").remove();
                        $(".mainDropDown ul").append(selectOptions);
                    } else {
                        Swal.fire({
                            title: 'Sorry No Data Found',
                            type: 'info',
                        })
                    }
                }
                else{
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

            },
            error:function(e){
                $("#addProductPreviewModel .preloadOnDropdownChange").fadeOut();
                $(".overlayAjaxStatus").fadeOut();
                Swal.fire({
                    title: "<strong>Their's been an error on server side refresh page and try again!</strong>",
                    type: 'error',
                })
            }
        });
    });
    autosize($(".userActionsTextAreas"));
    autosize($(".eventsTextAreas"));
 
    $(document).mouseup(function(e) 
    {
        var container = $(".custonTooltip");
    
        // if the target of the click isn't the container nor a descendant of the container
        if (!container.is(e.target) && container.has(e.target).length === 0) 
        {
            container.hide();
        }
        var container2 = $(".mainDropDown");
    
        // if the target of the click isn't the container nor a descendant of the container
        if (!container2.is(e.target) && container2.has(e.target).length === 0) 
        {
            container2.hide();
            $(".mainDropDown ul li:not(.NoResult)").show();
        }
        
    });
    $("#asinCustom").click(function (e) { 
        e.preventDefault();
        $(".mainDropDown").toggle();
        $("#asinCustom").keyup();
    });
    $(".mainDropDown ul").on("click",".options", function (e) {
        e.preventDefault();
        $asinSelected = $(this).attr("asin");
        if($asinSelected.length <= 0)
        {
            // $(".mainDropDown").hide();
            $(".mainDropDown ul li:not(.NoResult)").hide();
            return;
        }
        $("#asinCustom").attr("customValue",$asinSelected);
        $asinSelected = $asinSelected.split("|");
        $("#asinCustom").val($asinSelected[1]);
        $(".mainDropDown").hide();
        $(".mainDropDown ul li:not(.NoResult)").show();    
        fv.revalidateField('asinCustom');
    });
    $("#asinCustom").on("blur",function(){
        var value = $(this).val().toLowerCase();
        var isValid = false;
        $(".mainDropDown ul li:not(.NoResult)").filter(function() {
            if(isValid)
            return;
            if($(this).text().toLowerCase().indexOf(value) > -1)
            isValid = true;
        });
        if(!isValid){
            $(this).val("");
        }
    });
    $("#asinCustom").on("keyup", function() {
        $(".mainDropDown").show();
        var value = $(this).val().toLowerCase();
        $(".mainDropDown ul li:not(.NoResult)").filter(function() {
          $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
        if(!$(".mainDropDown ul li:not(.NoResult)").is(":visible")){
            $(".mainDropDown ul li.NoResult").show();
        }
        else
        {
            $(".mainDropDown ul li.NoResult").hide();
        }//end else
    });
    $(".noControl").click(function (e) { 
        e.preventDefault();
        $(this).parent().parent().hide();
        $(this).parent().parent().find("textarea").val("");
    });
    $(".yesControl").click(function (e) { 
        e.preventDefault();
        $(this).parent().parent().hide();
    });
});//end ready function