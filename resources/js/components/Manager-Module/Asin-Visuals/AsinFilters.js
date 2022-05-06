import React, {useState} from 'react';
import SingleSelect from "./../../../general-components/Select";
import MultiSelect from "./../../../general-components/MultiSelect";
import {Grid} from '@material-ui/core';
import TextFieldInput from "./../../../general-components/Textfield";
import CustomizedDateRangePicker from "./../../../general-components/DateRangePicker/CustomizedDateRangePicker";

function AsinFilters(props) {
    const [showDRP, setShowDRP] = useState(false);
    const getValueRange = (range) => {
        setShowDRP(false);
        props.getValue(range);
    }
    return (
        <div className="pt-5">
            <Grid container justify="center" spacing={1}>
                <Grid item xs={12} sm={6} md={6} lg={3}>
                    <label className=" font-semibold ml-2">
                        Select Child Brand
                    </label>
                    <SingleSelect
                        isDisabled={props.disableFilters}
                        placeholder="Child Brand"
                        id="cbn"
                        name={"selectedProfile"}
                        value={props.selectedProfile}
                        onChangeHandler={props.onProfileChangeHandler}
                        fullWidth={true}
                        Options={props.profileOptions}
                    />
                </Grid>

                <Grid item xs={12} sm={6} md={6} lg={3} className="autoScrl">
                    <label className=" font-semibold ml-2">
                        Select Campaigns
                    </label>

                    <MultiSelect
                        isDisabled={props.disableFilters}
                        placeholder="Select Campaigns"
                        id="sc"
                        name={"selectedCampaign"}
                        closeMenuOnSelect={false}
                        value={props.selectedCampaign}
                        onChangeHandler={props.onCampaignChangeHandler}
                        Options={props.campaignOptions}
                    />
                </Grid>

                <Grid item xs={12} sm={6} md={6} lg={3}>
                    <label className=" font-semibold ml-2">
                        Select ASIN
                    </label>

                    <SingleSelect
                        isDisabled={props.disableFilters}
                        placeholder="Select ASIN"
                        id="pt"
                        name="text"
                        value={props.selectedAsin}
                        onChangeHandler={props.onAsinChangeHandler}
                        Options={props.asinOptions}
                    />
                </Grid>

                <Grid item xs={12} sm={6} md={6} lg={3}>
                    <label className=" font-semibold ml-2">
                        Select Date Range
                    </label>
                    <div onClick={() => setShowDRP(!showDRP)}>
                        <TextFieldInput
                            disabled={props.disableFilters}
                            placeholder="Date Range"
                            id="dr"
                            type="text"
                            name={"selectedDate"}
                            value={props.selectedDate}
                            fullWidth={true}
                        />
                    </div>

                    <div className={`absolute right-0 ${props.datepickerClass}`}>
                        {showDRP ? <CustomizedDateRangePicker range={props.dateRangeObj} helperCloseDRP={setShowDRP}
                                                              getValue={getValueRange} direction="horizontal"/> : null}
                    </div>
                </Grid>
            </Grid>
        </div>
    );
}

export default AsinFilters;