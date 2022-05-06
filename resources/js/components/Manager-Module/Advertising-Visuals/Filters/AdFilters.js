import React, { useState } from 'react';
import SingleSelect from "./../../../../general-components/Select";
import MultiSelect from "./../../../../general-components/MultiSelect";
import { Grid } from '@material-ui/core';
import TextFieldInput from "./../../../../general-components/Textfield";
import CustomizedDateRangePicker from "./../../../../general-components/DateRangePicker/CustomizedDateRangePicker";
import "./../styles.scss"

function AdFilters(props){
    const [showDRP,setShowDRP] = useState(false);
    const getValueRange = (range)=>{
        setShowDRP(false);
        props.getValue(range);
    }

        return (
            <div className="pt-5">
                <Grid container justify="flex-start" spacing={1}>
                    <Grid item xs={12} sm={6} md={4} lg={4}>
                        <label className=" font-semibold ml-2">
                            Select Child Brand
                        </label>
                        <SingleSelect
                            isDisabled={props.disableFilters}
                            placeholder="Child Brand"
                            id="cbn"
                            name="text"
                            value={props.selectedProfile}
                            onChangeHandler = {props.onProfileChange}
                            fullWidth={true}
                            Options={props.profileOptions}
                            />
                    </Grid>

                    <Grid item xs={12} sm={6} md={4} lg={4} className="autoScrl">
                        <label className=" font-semibold ml-2">
                            Select Campaigns
                        </label>
                        <MultiSelect
                            isDisabled={props.disableFilters}
                            placeholder="Select Campaigns"
                            id="sc"
                            name="selectedCampaign"
                            closeMenuOnSelect={props.closeMenuOnSelect}       
                            value={props.selectedCampaign}
                            onChangeHandler = {props.onCampaignChange}
                            Options={props.campaignsOptions}
                            />
                    </Grid>

                    <Grid item xs={12} sm={6} md={4} lg={4}>
                        <label className=" font-semibold ml-2">
                            Select Product Type
                        </label>
                        <SingleSelect
                            isDisabled={props.disableFilters}
                            placeholder="Product Type"
                            id="pt"
                            name="text"
                            value={props.selectedProduct}
                            onChangeHandler = {props.onProductChange}
                            Options={props.productOptions}
                            />
                    </Grid>
                    <Grid item xs={12} sm={6} md={4} lg={4}>
                        <label className=" font-semibold ml-2">
                            Select Strategy Type
                        </label>
                        <SingleSelect
                            isDisabled={props.disableFilters}
                            placeholder="Strategy Type"
                            id="st"
                            name="text"
                            value={props.selectedStrategy}
                            onChangeHandler = {props.onStrategyChange}
                            Options={props.strategyOptions}
                            />
                    </Grid>

                    <Grid item xs={12} sm={6} md={4} lg={4}> 
                            <label className=" font-semibold ml-2">
                                Select Date Range
                            </label>
                        <div onClick={()=>setShowDRP(!showDRP)}>
                            <TextFieldInput
                                disabled={props.disableFilters}
                                placeholder="Date Range"
                                id="dr"
                                type="text"
                                value={props.selectedDate}
                                fullWidth={true}
                            />
                        </div>
                        <div className={`absolute right-10 ${props.datepickerClass}`}>
                            {showDRP ? <CustomizedDateRangePicker range={props.dateRangeObj} helperCloseDRP = {setShowDRP} getValue = {getValueRange} direction="horizontal"/>:null}
                        </div> 
                    </Grid>
                </Grid>
            </div>
        );
}

export default AdFilters;