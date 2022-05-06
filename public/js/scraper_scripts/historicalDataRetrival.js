$(function() {

    $(".getHistData").click(function(e){
        e.preventDefault();
        $(".overlayAjaxStatus").fadeIn();

        start = $('input[name="daterange"]').data('daterangepicker').startDate.format('YYYY-MM-DD');
        end = $('input[name="daterange"]').data('daterangepicker').endDate.format('YYYY-MM-DD');

        $.ajax({
            type: "post",
            url: $("#historicalDataRetrivalForm").attr("action"),
            data: {
                "startDate":start,
                "endDate":end,
                "_token":$("#historicalDataRetrivalForm input[type='hidden']").val(),
            },
            success: function (response) {
                if(response.status){
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
                $(".overlayAjaxStatus").fadeOut();
                    location.href =response.url;
                }
                else{
                    if(response.exceptionStatus){
                        console.log(response.exception)
                    }
                    $(".overlayAjaxStatus").hide();
                    Swal.fire({
                        title: '<strong>Sorry</strong>',
                        type: 'warning',
                        text:response.message
                    })
                }
            },
            error:function(error){
                if(e.responseText.includes("Unauthenticed")){
                    location.reload();
                }
                else
                { 
                    $(".overlayAjaxStatus").hide();
                    Swal.fire({
                        title: '<strong>Error</strong>',
                        type: 'error',
                        text:"Their is an error from server side"
                    })
                    console.log(error.responseText);
                }
            }
        });
    })
    currentDate = moment().format("YYYY-MM-DD");
    var statDate = moment(currentDate, "YYYY-MM-DD").subtract( 6,'days').format("YYYY-MM-DD");
  
    $('input[name="daterange"]').daterangepicker({
        opens: 'left', 
        drops:'down',
        autoUpdateInput: true,
        startDate:statDate ,
        endDate: currentDate,
        maxDate:currentDate,
        locale: {
            format: 'YYYY-MM-DD'
        }
    }, function(start, end, label) {
        currentDate = start;
        end_date = end;
       
    });
});