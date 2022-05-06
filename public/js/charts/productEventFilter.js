$(function () {
    $(".checkboxContainer:nth-child(1)").index()
    $(".checkboxContainer").click(function (e) { 
        if ($(this).find("input").is(":checked")) {
            $(".event" + $(this).attr("data-index") + " .eventGradient").addClass("d-block");
            $(this).addClass("isChecked")
        }
        else {
            $(".event" + $(this).attr("data-index") + " .eventGradient").removeClass("d-block");
            $(this).removeClass("isChecked")
        }
    });
    $(".checkboxContainer").hover(function () {
        if ($(this).hasClass("isChecked"))
            return;
            $(".event" + $(this).attr("data-index") + " .eventGradient").addClass("d-block");
            $(".checkboxContainer:not(.isChecked)").css("opacity", 0.3);
            $(this).css("opacity", 1);
        }, function () {
            if ($(this).hasClass("isChecked"))
            return;
            $(".event" + $(this).attr("data-index") + " .eventGradient").removeClass("d-block");
            $(".checkboxContainer:not(.isChecked)").css("opacity", 1);
        }
    );//end check box hover event
    $(".productsVisuals #dataTable_filter1 in1put").keyup(function (ev) { 
      
        var value = $(this).val().toLowerCase();
        var noResultFOund = '<td valign="top" colspan="5" class="dataTables_empty">No matching records found</td>'
        $("#dataTable tbody tr:not(.dataTables_empty)").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            
        });
                if ($("#dataTable tbody tr:not(.dataTables_empty)").is(":visible")) {
                    $(".dataTables_empty").remove();
                } else {
                    $(".dataTables_empty").remove();
                    $("#dataTable tbody tr:not(.dataTables_empty)").parent().append(noResultFOund);
                }
    });
});//end document ready event