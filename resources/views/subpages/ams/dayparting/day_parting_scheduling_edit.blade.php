<div class="modal fade" data-backdrop="static" id="schedule-edit">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Update {{isset($pageHeading)?$pageHeading:''}}</h4>
                <button type="button" class="close close-btn" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <div class="row">
                    {{-- Cron Job List --}}
                    <div class="col-12">
                        <div class=" mt-3">
                            {{-- Get Profile List --}}
                            <div class="col-xl-12">
                                <form id="dayPartingScheduleEditForm" method="POST"
                                      enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group ">
                                        <label class="scheduleName">Schedule Name<sup class="required">*</sup></label>
                                        <input type="text" class="form-control" name="scheduleName" id="scheduleEditName" autocomplete="off"
                                               placeholder="Schedule Name" value="" required maxlength="25">
                                    </div>
                                    <input type="hidden" id="scheduleCountPopup" value="1">
                                    <input type="hidden" name="scheduleId" id="scheduleId" value="">
                                    <input type="hidden" name="removeCampaigns" id="removeCampaings" value="">
                                    <input type="hidden" name="campaignOptionSelected" id="campaignOptionSelected" value="">
                                    <input type="hidden" name="portfolioCampaignEditTypeOldValue" id="portfolioCampaignEditTypeOldValue" value="">
                                    <input type="hidden" name="fkProfileId" id="fkProfileIdEdit" value="">

                                    <div class="form-group">
                                        <label class="portfolioCampaignEditType">Portfolio/Campaign<sup class="required">*</sup></label>
                                            <select class="form-control portfolioCampaignEditType" id="portfolioCampaignEditType" name="portfolioCampaignType" autocomplete="off">
                                                <option value="Campaign">Campaign</option>
                                                <option value="Portfolio">Portfolio</option>
                                            </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="pfCampaignsEdit">Portfolios/Campaigns<sup class="required">*</sup></label>
                                        <div class="form-group editCampaignPortfolio">
                                            <select class="form-control col-md-6 preSelectedCampaigns" name="pfCampaigns[]" id="pfCampaignsEdit" autocomplete="off" multiple="multiple" required>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="mr-4">Days of Week<sup class="required">*</sup></label>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" value="1" name="mon" class="custom-control-input monEdit" id="editchecbox-1">
                                            <label class="custom-control-label" for="editchecbox-1">M</label>
                                        </div>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" value="1" name="tue" class="custom-control-input tueEdit" id="editchecbox-2">
                                            <label class="custom-control-label" for="editchecbox-2">T</label>
                                        </div>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" value="1" name="wed" class="custom-control-input wedEdit" id="editchecbox-3">
                                            <label class="custom-control-label" for="editchecbox-3">W</label>
                                        </div>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" value="1" name="thu" class="custom-control-input thuEdit" id="editchecbox-4">
                                            <label class="custom-control-label" for="editchecbox-4">TH</label>
                                        </div>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" value="1" name="fri" class="custom-control-input friEdit" id="editchecbox-5">
                                            <label class="custom-control-label" for="editchecbox-5">F</label>
                                        </div>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" value="1" name="sat" class="custom-control-input satEdit" id="editchecbox-6">
                                            <label class="custom-control-label" for="editchecbox-6">SA</label>
                                        </div>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" value="1" name="sun" class="custom-control-input sunEdit" id="editchecbox-7">
                                            <label class="custom-control-label" for="editchecbox-7">SU</label>
                                        </div>
                                    </div>
                                    <div class="">
                                        <div style="display: flex">
                                            <div class="form-group" style="flex: 1">
                                                <lable>Start Time<sup class="required">*</sup><sup class="timezone-offset"> {{Config::get('app.timeOffset')}}</sup></lable>
                                                <input type="text" class="form-control timepicker startTimeEdit" id="startTimeEdit" placeholder="Start Time" name="startTime">
                                            </div>
                                            <div class="form-group pl-2" style="flex: 1">
                                                <lable>End Time<sup class="required">*</sup><sup class="timezone-offset">{{Config::get('app.timeOffset')}}</sup></lable>
                                                <input type="text" class="form-control timepicker endTimeEdit" id="endTimeEdit" placeholder="End Time" name="endTime">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-form-label mr-2">Email Receipts</label>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" value="1" name="emailReceiptStart" class="custom-control-input emailReceiptStartEdit" id="userAction10">
                                            <label class="custom-control-label" for="userAction10">Start</label>
                                        </div>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" value="1" name="emailReceiptEnd" class="custom-control-input emailReceiptEndEdit" id="userAction11">
                                            <label class="custom-control-label" for="userAction11">End</label>
                                        </div>
                                    </div>

                                    <div class="form-group email-id-row">
                                        <label class="col-form-label"> Add cc</label>
                                        <input type="text" class="form-control ccEditEmails" name="ccEmails" id="ccEmailsEdit" autocomplete="off"
                                               placeholder="Write CC Email" >
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button"  class="btn btn-secondary close-btn"
                                                data-dismiss="modal">Cancel
                                        </button>
                                        <button type="submit" class="btn btn-primary editScheduleButton">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

