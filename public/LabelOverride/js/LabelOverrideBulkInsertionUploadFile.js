
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

$(function () {
    $(".bulkInsertAliasButton").click(function (e) { 
        e.preventDefault();
        $(".bulkInsertAliasFileControl").click()
    });//end click funciton
    $(".bulkInsertAliasFileControl").change(function (e) { 
        e.preventDefault();
        fileObj = $(this);
        var DatatoUpload = new FormData();
        $(".bulkInsertAliasButton").attr("disabled","disabled");
        if( window.FormData === undefined )
        {
            $(".bulkInsertAliasButton").removeAttr("disabled");
            Swal.fire({
                title: '<strong>Error</strong>',
                type: 'error',
                text:"Sorry you browser Dose not support Form Data, Please use latest browser (suggestion:Google Chrome) "
            })
            return;
        }
        DatatoUpload.append("attributeFile",$(this).get(0).files[0]);
        DatatoUpload.append("_token", $("body").attr("csrf"));
        $.ajax({
            type: "post",
            url: $(this).attr("action-url"),
            data:DatatoUpload,
            contentType: false,
            processData: false,
            success: function (response) {
                $(".bulkInsertAliasButton").removeAttr("disabled");
                $(fileObj).val("");
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
                    t.clearPipeline().draw();
                }
                else{
                    Swal.fire({
                        title: 'Fail To Upload Aliases',
                        type: 'error',
                        text:response.message
                    })
                }
            },//end success funciton
            error: function (e) {
                
                $(fileObj).val("");
                if(e.responseText.includes("Unauthenticed")){
                    location.reload();
                }
                else
                {    
                    $(".bulkInsertAliasButton").removeAttr("disabled");
                    Swal.fire({
                        title: 'Error',
                        type: 'error',
                        text:"Some thing went wrong"
                    })
                }
            }
        });
    });
});//end ready function 