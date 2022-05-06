
/**
 * Global Declared Variables in LabelOverride.js
 * 
 *  var t; datatable object
 *  var visibleColumnIndex;
 *  var columnCount;
 *  var columnName;
 *  var alaisLimit,addAliasContainer;
 *  var addAliasObject = {
 *      fkId: null,
 *      overrideLabel: null,
 *      type: null,
 *  };
 */

var reg = /[^a-zA-Z0-9 ]/g;
$(function () {
     //event handling of add Alias Text Area
    $(".card-body").on("keyup keypress", ".addAliasBox", function (e) {
        //when enter key press saving alias to database
        
        // $(this).val($(this).val().replace(reg, ''));
       
        if(e.which == 13 && e.type == "keypress")
        {
            e.preventDefault();
            e.stopPropagation();
            $(".labelOverridePreloader").remove();
            $(".addAliasContainer").append(labelOverridePreloader);
            addAliasToDataBase($(this));
            $(this).blur();
            return false; 
        }//end if
        $(".alias").text($(this).val().length > 0 ? $(this).val() :"Not Available");
        if ($(this).val().length > 0) {
            $(".information").show();
        }
        else
        {
            $(".information").hide();
        }
        
        addAliasObject.overrideLabel = $(this).val().length > 0 ? $(this).val() : null;

        $(".dynamicCounter").text($(this).val().length)
        //when input in text area exeed the limit
        if ($(this).val().length >= alaisLimit)
        {
            e.preventDefault();
            return false; 
        }//end if
    });//end key up function
    $(".addAliasBox").bind("paste", function(e) {
        var pastedData = e.originalEvent.clipboardData.getData("text");
        tempPastedWholeData = pastedData + $(this).val();
        // access the clipboard using the api
        var pastedData = e.originalEvent.clipboardData.getData("text");
        // pastedData = pastedData.replace(reg, '');
        if (pastedData.length > 0) {
            if (tempPastedWholeData.length >= alaisLimit) {
                e.preventDefault(); return false;
            }
            addAliasObject.overrideLabel = pastedData;
            $(".alias").text(pastedData);
            $(".information").show();
            $(".dynamicCounter").text($(this).val().length)
        }
    });
});//end documtnet ready function

function addAliasToDataBase(thisObj) {
    if (addAliasObject.fkId == null || addAliasObject.type == null || addAliasObject.overrideLabel == null) {

        $(".labelOverridePreloader").html('<span class="badge">One of the attributes missing</span>').css({"color":"red"});
                setTimeout(function() {
                    $(".labelOverridePreloader").fadeOut(1000);
                //     $(".labelOverridePreloader").remove();
                }, 1500);
        return;
    }
    if (addAliasObject.overrideLabel == $("tbody tr td.selected .attributeData").attr("alias")) {
        $(".labelOverridePreloader").remove();
        hideAliasBox(addAliasContainer);
        return;
    }
    addAliasObject._token = _token;
    $.ajax({
        type: "POST",
        url: ""+$(thisObj).attr("action-url")+"",
        data: addAliasObject,
        success: function (response) {
            if (response.status)
            {
                $(".labelOverridePreloader").html('<span class="badge">Alias Addedd Successfully</span>').css({"color":"#36b9cc"});
                setTimeout(function() {
                    $(".labelOverridePreloader").fadeOut(1000);
                    hideAliasBox(addAliasContainer);
                    t.clearPipeline().draw();
                }, 1500);
            }
            else {
                Swal.fire({
                    title: '<strong>Error</strong>',
                    type: 'error',
                    text: response.message
                });
                $(".labelOverridePreloader").remove();
            }
            
        },
        error: function (error) {
            Swal.fire({
                title: '<strong>Error</strong>',
                type: 'error',
                text: "Some thing went wrong"
            });
            $(".labelOverridePreloader").remove();
        }
    });

}