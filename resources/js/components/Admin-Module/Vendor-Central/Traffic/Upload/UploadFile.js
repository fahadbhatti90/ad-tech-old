import React, {Component} from 'react';
import {Grid, withStyles} from "@material-ui/core";
import TextFieldInput from "../../../../../general-components/Textfield";
//import {styles} from "../../styles";
import {customStyle, useStyles} from "../../../Manage-Users/styles";
import {connect} from "react-redux";
import SingleSelect from "../../../../../general-components/Select";
import {getAllVendors, uploadTraffic} from "../../apiCalls"
import {ShowSuccessMsg} from "../../../../../general-components/successDailog/actions";
import {ShowFailureMsg} from "../../../../../general-components/failureDailog/actions";
import * as Yup from "yup";
import {
    objectRequiredValidationHelper,
    stringRequiredValidationHelper,
    uploadValidationHelper
} from "../../../../../helper/yupHelpers";
import moment from "moment";
import CustomizedDateRangePicker from "../../../../../general-components/DateRangePicker/CustomizedDateRangePicker";
import PrimaryButton from "../../../../../general-components/PrimaryButton";
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
            dateRangeObj: {
                startDate: new Date(),
                endDate: new Date(),
                key: 'selection',
            },
            selectedFile: '',
            selectedDate: '',
            selectedVendor: null,
            vendorOptions: [],
            showDRP: false,
            btnText: 'Submit',
            isBtnEnableDisable: false,
            errors: {
                trafficDateE: '',
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
            trafficDateE: stringRequiredValidationHelper("date"),
            selectedFileE: stringRequiredValidationHelper('upload file')
        });

        let dataToValidateObject = {
            selectedVendorE: this.state.selectedVendor,
            trafficDateE: this.state.selectedDate,
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
            params.append('traffic_upload_file', this.state.selectedFile)
            params.append('vendor', this.state.selectedVendor.value)
            params.append('daterange', this.state.selectedDate)

            if (params != null) {
                uploadTraffic(params, (data) => {
                    this.setState({
                        btnText: 'Submit',
                        isBtnEnableDisable: false
                    })
                    if (data.ajax_status != false) {
                        event.reset();
                        this.setState({
                            selectedDate: '',
                            selectedFile: '',
                            selectedVendor: null,
                            dateRangeObj: {
                                startDate: new Date(),
                                endDate: new Date(),
                                key: 'selection',
                            }
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
    helperCloseDRP = (event) => {
        this.setState({
            showDRP: false
        })
    }
    getValue = (range) => {
        this.setState({
            dateRangeObj: range,
            selectedDate: moment(range.startDate).format('YYYY-MM-DD') + " - " + moment(range.endDate).format('YYYY-MM-DD'),
            showDRP: false
        }, () => {
            this.resetErrors('trafficDateE')
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
                                name="domain"
                                onChange={this.onChangeHandler}
                                fullWidth={true}
                                inputProps={{accept: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel, .csv'}}
                            />
                            <div className="error pl-2">{this.state.errors.selectedFileE}</div>
                        </Grid>
                        <Grid item xs={12} sm={12} md={12} lg={12} >
                            <Grid item xs={12} sm={6} md={6} lg={6}
                                  className="vendorCentralGridElement">
                                <label className="inline-block ml-2 text-sm">
                                    Select Start & End Date Range <span className="required-asterisk">*</span>
                                </label>
                                <div onClick={this.handleOnDateRangeClick}>
                                    <TextFieldInput
                                        disabled={false}
                                        placeholder="Date Range"
                                        type="text"
                                        name={"selectedDate"}
                                        value={this.state.selectedDate}
                                        fullWidth={true}
                                        classesstyle={classes}
                                    />
                                    <div className="error pl-2">{this.state.errors.trafficDateE}</div>
                                </div>

                                <div className={`absolute ${classes.datepickerClass}`}>
                                    {this.state.showDRP ? <CustomizedDateRangePicker range={this.state.dateRangeObj}
                                                                                     helperCloseDRP={this.helperCloseDRP}
                                                                                     getValue={this.getValue}
                                                                                     maxDate={new Date()}
                                                                                     direction="horizontal"/> : null}
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