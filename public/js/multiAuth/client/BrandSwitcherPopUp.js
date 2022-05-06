var GBScustToolTip = '<div class="tooltipster-base tooltipster-sidetip tooltipster-top tooltipster-fade tooltipster-show" id="tooltipster-250980" style="max-width: 300px;\
                    pointer-events: auto;\
                    z-index: 9999999;\
                    display:none;\
                    left: 0px;\
                    top: -37px;\
                    height: auto;\
                    width: 100%;\
                    animation-duration: 350ms;\
                    transition-duration: 350ms;"><div class="tooltipster-box"><div class="tooltipster-content">\
                    <span class="toolTipContent"></span>\
                </div></div><div class="tooltipster-arrow" style="left: 119px;"><div class="tooltipster-arrow-uncropped"><div class="tooltipster-arrow-border"></div><div class="tooltipster-arrow-background"></div></div></div></div>';
                
                
$(function () {
    $("#brandSwitcher").click(function () {
        let thisObj = $(this);
        $(".customModel").addClass("in");
        $(".brandPrelaoder").show();
        $(".amazonBrandsListContainer").html("");
        $(".searchBrandInput").val("");
        setTimeout(function () {
            $(".customModel .customModelContent").addClass("show");
            $("body").addClass("stopScrolling");
        }, 100);
        $.ajax({
            type: "GET",
            url: $(this).attr("action-url"),
            success: function (response) {
                if (response.status) {
                    let data = response.data;
                    let tabIndex = 1;
                    let brandsHtml = '<div class="NoResult amazonBrand">No Match Found</div>';
                    if (data.length > 0) {
                        $.each(data, function (indexInArray, valueOfElement) {
                            brandNameOrignal = valueOfElement.brand.name;
                            brandNameShort = brandNameOrignal.length >= 30 ? (brandNameOrignal.substr(0, 30) + "..."): brandNameOrignal;
                            if (valueOfElement.brand.id == response.selected) {
                                brandsHtml += ' <div class="amazonBrand "><input "'+(++tabIndex)+'" type="radio" name="optradio" checked><a class="barndNameAnchor" tabindex="'+(++tabIndex)+'" href="#'+valueOfElement.brand.id+'" datatitle="'+brandNameOrignal+'">'+brandNameShort+'</a></div>'
                                return;
                            }
                            brandsHtml += ' <div class="amazonBrand "><input type="radio" name="optradio" ><a class="barndNameAnchor"  href="#'+valueOfElement.brand.id+'"  tabindex="'+(++tabIndex)+'" datatitle="'+brandNameOrignal+'">'+brandNameShort+'</a></div>'
                        }); 
                        
                        $(".searchBrandInput").removeAttr("disabled");
                        $(".searchBrandInput").focus();
                     
                    }
                    else {
                        $(".searchBrandInput").attr("disabled","disabled")
                        brandsHtml += ' <div class="amazonBrand ">No Brand Found</div>'
                    }
                    $(".amazonBrandsListContainer").html(brandsHtml);
                    // var $barndNameAnchor = $('.amazonBrand');
                    // var sortList = Array.prototype.sort.bind($barndNameAnchor);
                    // sortList(function ( a, b ) {

                    //     // Cache inner content from the first element (a) and the next sibling (b)
                    //     var aText = a.innerHTML;
                    //     var bText = b.innerHTML;
                    
                    //     // Returning -1 will place element `a` before element `b`
                    //     if ( aText < bText ) {
                    //         return -1;
                    //     }
    
                    //     // Returning 1 will do the opposite
                    //     if ( aText > bText ) {
                    //         return 1;
                    //     }
    
                    //     // Returning 0 leaves them as-is
                    //     return 0;
                    // });
                  
                    // $(".amazonBrandsListContainer").html($barndNameAnchor);
                    $(".customModelFooter a:nth-child(1)").attr("tabindex",++tabIndex)
                    $(".customModelFooter a:nth-child(2)").attr("tabindex",++tabIndex)
                    globalToolTipHeight = $(".tooltipster-base").height()
                    
                    $(".amazonBrandsListContainer").append(GBScustToolTip);
                    $(".brandPrelaoder").hide();
                } else {
                    Swal.fire({
                        title: "<strong>Error</strong>",
                        type: "error",
                        text: "Error loading brands try again "
                    });
                    $(".brandPrelaoder").hide();
                }
            },
            error: function (e) {
                Swal.fire({
                    title: "<strong>Error</strong>",
                    type: "error",
                    text: "Error loading brands try again "
                });
                $(".brandPrelaoder").hide();
            }
        });
        
    });//end function
    $("body").on("mouseenter",".amazonBrandsListContainer .barndNameAnchor", function () {
        tooltipFullValue = $(this).attr("datatitle");
        GBScustomTooltipInstance = $(this).parent().parent().find(".tooltipster-base");
        if(tooltipFullValue.length <=30){
            return;
        }
        childPos = $(this).position().top;
        $(GBScustomTooltipInstance).show()
        $(GBScustomTooltipInstance).find(".toolTipContent").html(tooltipFullValue);
        if($(this).attr("id") == "brandSwitcher")
        $(GBScustomTooltipInstance).css("top", (childPos + $(GBScustomTooltipInstance).height()) + "px");
        else
        $(GBScustomTooltipInstance).css("top", (childPos - $(GBScustomTooltipInstance).height()) + "px");
       
    }).on("mouseout",".amazonBrandsListContainer .barndNameAnchor", function () {
        $(GBScustomTooltipInstance).hide();
    });
    $(".customModel").on("click",".amazonBrand a" ,function (e) {
        e.preventDefault();
        $(this).prev("input").prop("checked",true);
    });//on brand name click take radio button click
    $(".switchBrandButton").click(function (e) { 
        e.preventDefault();
        brandId = ($(".amazonBrand input:checked").next("a").attr("href").replace("#",""))
        selectedBrandId = $("#brandSwitcher").attr("brand-id");
        if (brandId == selectedBrandId) {
            $(".closeBrandSwitchPopUpButton").click();
            return;
        }
        $(".brandPrelaoder").fadeIn();
        window.location.href = $(this).attr("action-url")+brandId;
    });
    $(".customModel .customModelContent .close,.closeBrandSwitchPopUpButton").click(function (e) { 
        e.preventDefault();
        $("body").removeClass("stopScrolling");
        $(".customModel").removeClass("in");
        $(".customModel .customModelContent").removeClass("show");
    });//end close popup function
    $(".searchBrandInput").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $(".amazonBrandsListContainer .amazonBrand:not(.NoResult)").filter(function() {
          $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
        if(!$(".amazonBrandsListContainer .amazonBrand:not(.NoResult)").is(":visible")){
            $(".amazonBrandsListContainer .amazonBrand.NoResult").show();
        }
        else
        {
            $(".amazonBrandsListContainer .amazonBrand.NoResult").hide();
        }//end else
    });//end search brand input key up function
    $(".customModel").click(function(e)
    {
        if($(".customModel .customModelContent").hasClass("show"))
        if(($(e.target).parents(".disableClose").length <= 0) )
        {
            $("body").removeClass("stopScrolling");
            $(".customModel").removeClass("in");
            $(".customModel .customModelContent").removeClass("show");
        }
    });//end body click function
});