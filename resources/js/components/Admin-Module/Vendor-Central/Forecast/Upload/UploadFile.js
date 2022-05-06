import React, {Component} from 'react';
import {Grid, withStyles} from "@material-ui/core";
import TextFieldInput from "../../../../../general-components/Textfield";
//import {styles} from "../../styles";
import {customStyle, useStyles} from "../../../Manage-Users/styles";
import {connect} from "react-redux";
import SingleSelect from "../../../../../general-components/Select";
import {getAllVendors, uploadForecast} from "../../apiCalls"
import {ShowSuccessMsg} from "../../../../../general-components/successDailog/actions";
import {ShowFailureMsg} from "../../../../../general-components/failureDailog/actions";
import * as Yup from "yup";
import {
    objectRequiredValidationHelper,
    stringRequiredValidationHelper,
} from "../../../../../helper/yupHelpers";

import moment from "moment";
import PrimaryButton from "../../../../../general-components/PrimaryButton";
import CustomDateRangePicker from "../../../../Manager-Module/Events/CustomDateRangePicker";

const list = (errorsData) => {
    return (
        <>
            {errorsData.map(item => (
                <li className="list-none"> {item} </li>
            ))}
        </>
    )
}

const SUPPORTED_FORMATS = ["xls", "xlsx", "csv"];

class UploadFile extends Component {
    constructor(props) {
        super(props);
        this.state = {
            forecastDate: '',
            selectedFile: '',
            selectedVendor: null,
            vendorOptions: [],
            btnText: 'Submit',
            showDRP: false,
            isBtnEnableDisable: false,
            dateRangeObj: {
                startDate: new Date(),
                endDate: new Date(),
                key: 'selection',
            },
            errors: {
                forecastDateE: '',
                selectedVendorE: ''
            }
        }
    }

    onVendorChange = (value) => {
        this.setState({
            selectedVendor: value,
        }, () => {
            this.resetErrors('selectedVendorE')
        })
    }

    resetErrors = (key) => {
        let {errors} = this.state;
        errors[key] = ""
        this.setState({
            ...errors
        })
    }

    componentDidMount() {

        getAllVendors((fetchVendors) => {

            if (fetchVendors.length > 0) {
                let allVendors = fetchVendors.map((obj, idx) => {
                    return {
                        value: obj.vendor_id,
                        label: obj.vendor_name
                    }
                })

                this.setState({
                    vendorOptions: allVendors
                })
            }
        })

    }

    onChangeHandler = (e) => {
        this.setState({
            selectedFile: e.target.files[0]
        }, () => {
            this.resetErrors('selectedFileE')
        })
    }

    formSubmissionForecast = (e) => {
        e.preventDefault();
        var event = e.target;
        let validationSchema = Yup.object().shape({
            selectedVendorE: objectRequiredValidationHelper("vendor"),
            forecastDateE: stringRequiredValidationHelper("date"),
            selectedFileE: stringRequiredValidationHelper('upload file')
        });

        let dataToValidateObject = {
            selectedVendorE: this.state.selectedVendor,
            forecastDateE: this.state.forecastDate,
            selectedFileE: this.state.selectedFile
        }

        let validationFormData = htk.validateAllFields(validationSchema, dataToValidateObject);

        if (Object.size(validationFormData) > 0) {
            const {errors} = this.state;
            $.each(validationFormData, function (indexInArray, valueOfElement) {
                errors[indexInArray] = valueOfElement;
            });

            this.setState((prevState) => ({
                errors: errors
            }));
        } else {
            this.setState({
                btnText: 'Submitting...',
                isBtnEnableDisable: true
            })
            let params = new FormData();
            params.append('forecast_upload_file', this.state.selectedFile)
            params.append('vendor', this.state.selectedVendor.value)
            params.append('forecast_date', this.state.forecastDate)

            if (params != null) {
                uploadForecast(params, (data) => {
                    this.setState({
                        btnText: 'Submit',
                        isBtnEnableDisable: false
                    })
                    if (data.ajax_status != false) {
                        event.reset();
                        this.setState({
                            forecastDate: '',
                            selectedFile: '',
                            selectedVendor: null
                        })
                        this.props.dispatch(ShowSuccessMsg(data.success, "Successfully", true, ""));
                    } else {
                        this.props.dispatch(ShowFailureMsg("", "", true, "", list(data.error)))
                    }
                })
            } else {

            }
        }
    }

    handleOnDateRangeClick = (e) => {
        this.setState({
            showDRP: true
        })
    }

    handleSingleDateChange = (date) => {
        this.setState({
            forecastDate: moment(date).format('DD-MM-YYYY'),
            showDRP: false
        }, () => {
            this.resetErrors('forecastDateE')
        })
    }
    helperCloseDRP = (event) => {
        this.setState({
            showDRP: false
        })
    }
    render() {
        const {classes} = this.props;
        return (
            <div className="p-5 rounded-lg mb-10">
                <form onSubmit={this.formSubmissionForecast}>
                    <Grid container justify="center" spacing={3}>
                        <Grid item xs={12} sm={6} md={6} lg={6}
                              className="vendorCentralGridElement">
                            <label className="inline-block ml-2 text-sm">
                                Select Vendor <span className="required-asterisk">*</span>
                            </label>
                            <SingleSelect
                                placeholder="Select Any Vendor"
                                name={"vendor"}
                                className="rounded-full bg-white"
                                value={this.state.selectedVendor}
                                onChangeHandler={this.onVendorChange}
                                fullWidth={true}
                                open="true"
                                Options={this.state.vendorOptions}
                                isClearable={false}
                                customClassName="ThemeSelect"
                                styles={customStyle}
                            />
                            <div className="error pl-2">{this.state.errors.selectedVendorE}</div>
                        </Grid>
                        <Grid item xs={12} sm={6} md={6} lg={6}
                              className="vendorCentralGridElement">
                            <label className="inline-block mb-2 ml-2 text-sm">
                                Upload <span className="required-asterisk">*</span>
                            </label>

                            <TextFieldInput
                                type="file"
                                className="vendorCentralTextField rounded-full bg-white vendorCentralFileUpload"
                                onChange={this.onChangeHandler}
                                fullWidth={true}
                                classesstyle={classes}
                                inputProps={{accept: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel, .csv'}}
                            />
                            <div className="error pl-2">{this.state.errors.selectedFileE}</div>
                        </Grid>
                        <Grid item xs={12} sm={12} md={12} lg={12}>
                        <Grid item xs={12} sm={6} md={6} lg={6}
                              className="vendorCentralGridElement">
                            <label className="inline-block ml-2 text-sm">
                                Date <span className="required-asterisk">*</span>
                            </label>
                            <div onClick={this.handleOnDateRangeClick}>
                                <TextFieldInput
                                    disabled={false}
                                    placeholder="Date"
                                    type="text"
                                    name={"forecastDate"}
                                    value={this.state.forecastDate}
                                    fullWidth={true}
                                    classesstyle={classes}
                                />
                                <div className="error pl-2">{this.state.errors.forecastDateE}</div>
                            </div>
                            <div className={`absolute z-50 ${classes.datepickerClass}`}>
                                {
                                    this.state.showDRP ?
                                        <CustomDateRangePicker 
                                            range={this.state.dateRangeObj}
                                            helperCloseDRP={this.helperCloseDRP}
                                            setSingleDate={this.handleSingleDateChange}
                                            date={new Date()}
                                            direction="vertical"
                                            isDateRange={false} 
                                        />
                                        : null
                                }
                            </div>
                        </Grid>
                        </Grid>
                    </Grid>
                    <div className="flex mt-8 justify-end">
                        <PrimaryButton
                            type="submit"
                            variant={"contained"}
                            disabled={this.state.isBtnEnableDisable}
                            btntext={this.state.btnText}
                        />
                    </div>
                </form>
            </div>
        );
    }
}

export default withStyles(useStyles)(connect(null)(UploadFile))