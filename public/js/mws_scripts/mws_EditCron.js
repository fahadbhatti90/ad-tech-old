var fvec;
document.addEventListener('DOMContentLoaded', function(e) {
    const form = document.getElementById('edit_cron_from');
    fvec=FormValidation.formValidation(form, {
        fields: {
            title: {
                validators: {
                    notEmpty: {
                        message: 'Cront Title Is Required.'
                    },
                    stringLength: {
                        min: 1,
                        max: 50,
                        message: 'Maximum Length Exceeded.'
                    },
                    regexp: {
                        regexp: /^(?!\s*$).+/,
                        message: 'Cront Title Is Required.'
                    }
                }
            },
            report_type: {
                validators: {
                    notEmpty: {
                        message: 'Report Type Is Required.'
                    },
                    regexp: {
                        regexp: /^(?!\s*$).+/,
                        message: 'Report Type Is Required.'
                    }
                }
            },
            cron_time: {
                validators: {
                    notEmpty: {
                        message: 'Time Is Required.'
                    },
                    regexp: {
                        regexp: /^(?!\s*$).+/,
                        message: 'Time Is Required.'
                    }
                }
            },
        },
        plugins: {
            trigger: new FormValidation.plugins.Trigger(),
            bootstrap: new FormValidation.plugins.Bootstrap(),
            submitButton: new FormValidation.plugins.SubmitButton(),
            icon: new FormValidation.plugins.Icon({
               // valid: 'fa fa-check',
                //invalid: 'fa fa-times',
                validating: 'fa fa-refresh',
            }),
        },
    })
        .on('core.form.valid', function() {
            // alert($('#add_cron_from').serialize());
            // return false;
            $("#edit_cron_btn").attr("disabled", true);
            $.ajax({
                url:"editCron",
                type:"POST",
                // data:$(this).serialize(),
                data:$('#edit_cron_from').serialize(),
                cache: false,
                success: function(data){

                    $('#edit_cron_from')[0].reset();
                    location.reload();
                    /*$('#add_cron_from')[0].reset();
                    $('div.flash-message').html(data);
                    $('#mwsapiconfigModal').modal('toggle');*/
                }
            });

        });

});

$('#mwsEditCronModal').on('show.bs.modal', function(e) {
    fvec.resetForm(true);
    var title = $(e.relatedTarget).data('title');
    var cronTaskId = $(e.relatedTarget).data('cron-task-id');
    var time = $(e.relatedTarget).data('time');
    var report_type = $(e.relatedTarget).data('cron-report-type');
    $(e.currentTarget).find('input[name="title"]').val(title);
    $(e.currentTarget).find('input[name="cron_task_id"]').val(cronTaskId);
    $(e.currentTarget).find('input[name="cron_time"]').val(time);
    $(e.currentTarget).find('input[name="report_type_value"]').val(report_type);
    $("#edit_report_type").val(report_type);
});