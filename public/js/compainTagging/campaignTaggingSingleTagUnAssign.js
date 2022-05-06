$(function () {
    jQuery.fn.visible = function() {
        return this.css('visibility', 'visible');
    };
  
    jQuery.fn.invisible = function() {
        return this.css('visibility', 'hidden');
    };
  
    jQuery.fn.visibilityToggle = function() {
        return this.css('visibility', function(i, visibility) {
            return (visibility == 'visible') ? 'hidden' : 'visible';
        });
    };
    $("body").on("click",".tagToolTip .unAssignSingleTag", function () {
        let thisObj = $(this);
        campaignId = $(this).attr("campaignid");
        accountId = $(this).attr("accountid");
        tagType = $(this).attr("tag-type");
        rowId = $(this).attr("row-id");
        tagId = $(this).attr("tagid");
        // console.lo[(camp]ignId, accountId, tagId);
        let progessbarSelector = ".tooltipProgressBar";
        let deleteClassRemoveSelector = $(this).parent().children("span:nth-child(1)");
        let mainTagToolTipsParent = $(this).parents(".tagToolTip").parent();
        let selectRow = $("#dataTable tbody > tr")[rowId - 1];
        let tooltipIndex = $(this).attr("tooltip-index");
        
        let allTagToolTips = $(mainTagToolTipsParent).children(".tagToolTip");
        
        $(progessbarSelector).visible();
        $(deleteClassRemoveSelector).addClass("delete");

        $.ajax({
            type: "POST",
            url: $("#dataTable").attr("un-assign-single-tag"),
            data: {
                campaignId: campaignId,
                accountId: accountId,
                tagType: tagType,
                tagId: tagId,
                _token: $("body").attr("csrf")
            },
            success: function(response) {
                if (response.status) {
                    $(thisObj).parents(".tagToolTip").remove();
                    let allTagToolTips = $(mainTagToolTipsParent).children(".tagToolTip")
                    if (allTagToolTips.length > 0) {
                       tagsUpdated = "";
                        for (let index = 0; index < allTagToolTips.length; index++) {
                            const element = allTagToolTips[index];
                            tagsUpdated += '<span class="badge badge-primary mr-1">' + $(element).find(".mainTag").text() + '</span><span class="badge badge-light">...</span>';
                            if (index >= 2)
                                break;
                        }
                        $(selectRow).find("td:last-child").children("span").html(tagsUpdated)
                    }
                    else {
                        t.ajax.reload();
                    }
                    
                    $(progessbarSelector).invisible();
                    newContent = $(mainTagToolTipsParent).html();
                    instancee = $.tooltipster.instancesLatest(".tagTootltip");
                    if (allTagToolTips.length > 0) {
                        instancee[tooltipIndex].open().content(newContent);
                    }
                    else {
                        
                        instancee[tooltipIndex].content(newContent);
                    }
                    if ($(".tooltipster-arrow").position().top == 0) {
                        $(".progress").removeAttr("style");
                        $(".progress").css({
                          "bottom" : "0",
                          "top" : "unset",
                        });
                    }
                    else {
                        $(".progress").removeAttr("style");
                        $(".progress").css({
                          "top": "0",
                          "bottom" : "unset",
                        });
                    }
                } else {
                    Swal.fire({
                        title: "<strong>Error</strong>",
                        type: "error",
                        text: "Sorry! fail to add tag refresh and try again "
                    });
                    $(deleteClassRemoveSelector).removeClass("delete");
                }
                $(progessbarSelector).invisible();
            }, //end success
            error: function(error) {
              error.responseText;
              Swal.fire({
                title: "<strong>Error</strong>",
                type: "error",
                text: "Sorry! fail to unassign tag refresh and try again "
              });
              $(deleteClassRemoveSelector).removeClass("delete");
              $(progessbarSelector).invisible();
            }
          });



    });//end single tag unassigned function 
});