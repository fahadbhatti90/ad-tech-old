$(document).ready(function () {
    var maxField = 10; //Input fields increment limitation
    var addButton = $('.add_button'); //Add button selector
    var wrapper = $('.field_wrapper'); //Input field wrapper
    var fieldHTML = '<div class="form-row col-md-12">\n' +
        '                                        <div class="col-sm-3  offset-md-1">\n' +
        '                                            <div class="form-group">\n' +
        '                                                <select class="form-control portfolioCampaignType"\n' +
        '                                                        name="then_clause"\n' +
        '                                                        autocomplete="off">\n' +
        '                                                    <option value="" selected>AND/Or</option>\n' +
        '                                                    <option value="Bid Up">AND</option>\n' +
        '                                                    <option value="Bid Down">Or</option>\n' +
        '                                                </select>\n' +
        '                                            </div>\n' +
        '                                        </div>\n' +
        '                                    </div>'+'<div class="form-row col-md-12">' +
        '<label for="inputPassword3" class="col-sm-1 col-form-label">if</label>\n' +
        '                                        <div class="col-sm-2">\n' +
        '                                            <div class="form-group">\n' +
        '                                                <select class="form-control "\n' +
        '                                                        name="metric"\n' +
        '                                                        autocomplete="off">\n' +
        '                                                    <option value="" selected>Select Metric</option>\n' +
        '                                                    <option value="ACOS">ACOS</option>\n' +
        '                                                    <option value="ROAS">ROAS</option>\n' +
        '                                                    <option value="impressions">Impressions</option>\n' +
        '                                                    <option value="clicks">Clicks</option>\n' +
        '                                                    <option value="spend">Spend</option>\n' +
        '                                                </select>\n' +
        '                                            </div>\n' +
        '                                        </div>\n' +
        '                                        <label for="inputPassword3" class="col-sm-1 col-form-label">is</label>\n' +
        '                                        <div class="col-sm-3">\n' +
        '                                            <div class="form-group">\n' +
        '                                                <select class="form-control portfolioCampaignType"\n' +
        '                                                        name="condition"\n' +
        '                                                        autocomplete="off">\n' +
        '                                                    <option value="" selected>Select Condition</option>\n' +
        '                                                    <option value="Bid Up">Greater</option>\n' +
        '                                                    <option value="Bid Down">Lesser</option>\n' +
        '                                                </select>\n' +
        '                                            </div>\n' +
        '                                        </div>\n' +
        '                                        <div class="col-sm-2">\n' +
        '                                            <input type="text" class="form-control" id="inputPassword3"\n' +
        '                                                   name="value"\n' +
        '                                                   placeholder="value">\n' +
        '                                        </div>\n' +
        '<a href="javascript:void(0);" class="remove_button">' +
        '<i class="fas fa-minus-circle fa-2x mt-1"></i>' +
        '</a></div>';//New input field html
    var x = 1; //Initial field counter is 1

    //Once add button is clicked
    $(addButton).click(function () {
        //Check maximum number of input fields
        if (x < maxField) {
            x++; //Increment field counter
            $(wrapper).append(fieldHTML); //Add field html
        }
    });

    //Once remove button is clicked
    $(wrapper).on('click', '.remove_button', function (e) {
        e.preventDefault();
        $(this).parent('div').remove(); //Remove field html
        x--; //Decrement field counter
    });
});