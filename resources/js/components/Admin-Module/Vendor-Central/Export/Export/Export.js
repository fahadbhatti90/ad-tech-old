import React, {Component} from 'react';
import {Grid, withStyles} from "@material-ui/core";
import TextFieldInput from "../../../../../general-components/Textfield";
//import {styles} from "../../styles";
import {customStyle, useStyles} from "../../../Manage-Users/styles";
import {connect} from "react-redux";
import SingleSelect from "../../../../../general-components/Select";
import {exportData} from "../../apiCalls"
import {ShowSuccessMsg} from "../../../../../general-components/successDailog/actions";
import {ShowFailureMsg} from "../../../../../general-components/failureDailog/actions";
import * as Yup from "yup";
import {
    objectRequiredValidationHelper,
    stringRequiredValidationHelper
} from "../../../../../helper/yupHelpers";
import moment from "moment";
import CustomizedDateRangePicker from "../../../../../general-components/DateRangePicker/CustomizedDateRangePicker";
import PrimaryButton from "../../../../../general-components/PrimaryButton";
import DownloadUrlModal from "../../../AMS/Export-Csv/DownloadUrlModal";


class Export extends Component {
    constructor(props) {
        super(props);
        this.state = {
            dateRangeObj: {
                startDate: new Date(),
                endDate: new Date(),
                key: 'selection',
            },
            reportTitle:'',
            selectedDate: '',
            selectedReport: null,
            reportOptions: [],
            showDRP: false,
            btnText: 'Submit',
            isBtnEnableDisable: false,
            openDialog: false,
            status: false,
            url: null,
            errors: {
                selectedDateE: '',
                selectedReportE: ''
            }
        }
    }

    onReportChange = (value) => {
        this.setState({
            selectedReport: value,
        }, () => {
            this.resetErrors('selectedReportE')
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
        this.setState({
            reportOptions: [
                {value: "daily_sales", label: 'Daily Sales'},
                {value: "purchase_order", label: 'Purchase Order'},
                {value: "daily_inventory", label: 'Daily Inventory'},
                {value: "traffic", label: 'Traffic'},
                {value: "forecast", label: 'Forecast'},
                {value: "product_catalog", label: 'Product Catalog'}
            ]
        })
    }


    formSubmissionForecast = (e) => {
        e.preventDefault();
        let validationSchema = Yup.object().shape({
            selectedReportE: objectRequiredValidationHelper("report type"),
            selectedDateE: stringRequiredValidationHelper("date"),
        });

        let dataToValidateObject = {
            selectedReportE: this.state.selectedReport,
            selectedDateE: this.state.selectedDate,

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
            params.append('historicalDataReportType', this.state.selectedReport.value)
            params.append('daterange', this.state.selectedDate)

            if (params != null) {
                exportData(params, (data) => {
                    console.log('data', data);
                    this.setState({
                        btnText: 'Submit',
                        isBtnEnableDisable: false
                    })
                    if (data.status != false) {
                        this.setState({
                            selectedDate: '',
                            selectedReport: null,
                            dateRangeObj: {
                                startDate: new Date(),
                                endDate: new Date(),
                                key: 'selection',
                            },
                            openDialog: true,
                            status: data.status,
                            message: data.message,
                            reportTitle: data.title,
                            url: data.url

                        })
                       // this.props.dispatch(ShowSuccessMsg(data.message, "Successfully", true, data.url));
                    } else {

                        this.props.dispatch(ShowFailureMsg(data.message, "", true, ""))
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
            this.resetErrors('selectedDateE')
        })
    }
    handleModalClose = ()=>{
        this.setState({
            openDialog: false
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
                                Choose <span className="required-asterisk">*</span>
                            </label>
                            <SingleSelect
                                placeholder="Select Any Report"
                                name={"vendor"}
                                className="rounded-full bg-white"
                                value={this.state.selectedReport}
                                onChangeHandler={this.onReportChange}
                                fullWidth={false}
                                open={true}
                                Options={this.state.reportOptions}
                                isClearable={false}
                                styles={customStyle}
                                customClassName="ThemeSelect"
                            />
                            <div className="error pl-2">{this.state.errors.selectedReportE}</div>
                        </Grid>

                        <Grid item xs={12} sm={6} md={6} lg={6}
                              className="vendorCentralGridElement px-2">
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
                                <div className="error pl-2">{this.state.errors.selectedDateE}</div>
                            </div>

                            <div className={`absolute right-0 ${classes.datepickerClass}`}>
                                {this.state.showDRP ? <CustomizedDateRangePicker range={this.state.dateRangeObj}
                                                                                 helperCloseDRP={this.helperCloseDRP}
                                                                                 getValue={this.getValue}
                                                                                 maxDate={new Date()}
                                                                                 direction="horizontal"/> : null}
                            </div>
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
                <DownloadUrlModal
                    handleModalClose={this.handleModalClose}
                    status ={this.state.status}
                    open={this.state.openDialog}
                    modalTitle = {"Download Report"}
                    message = {this.state.message}
                    title={this.state.reportTitle}
                    url = {this.state.url}
                />
            </div>

        );
    }
}

export default withStyles(useStyles)(connect(null)(Export))