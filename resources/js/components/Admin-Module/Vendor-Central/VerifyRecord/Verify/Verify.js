import React, {Component} from 'react';
import {Grid, withStyles} from "@material-ui/core";
import TextFieldInput from "../../../../../general-components/Textfield";

import {customStyle, useStyles} from "../../../Manage-Users/styles";
import {connect} from "react-redux";
import SingleSelect from "../../../../../general-components/Select";
import {deleteData, verifyData, moveToMainData, getAllVendors} from "../../apiCalls"
import {ShowSuccessMsg} from "../../../../../general-components/successDailog/actions";
import {ShowFailureMsg} from "../../../../../general-components/failureDailog/actions";
import * as Yup from "yup";
import {
    objectRequiredValidationHelper,
    stringRequiredValidationHelper
} from "../../../../../helper/yupHelpers";
import moment from "moment";
import PrimaryButton from "../../../../../general-components/PrimaryButton";
import Typography from "@material-ui/core/Typography";
import Card from "@material-ui/core/Card/Card";
import DataTable from "react-data-table-component";
import {columns, trafficColumn} from "../TableContent/DataTablecolumns";
import clsx from 'clsx';
import CustomDateRangePicker from "../../../../Manager-Module/Events/CustomDateRangePicker";
import ConfirmDelete from "../TableContent/ConfirmDelete";

const conditionalRowStyles = [
    {
        when: row => row.dup_count > 0,
        style: {
            backgroundColor: 'pink',
        },
    },

];

class Verify extends Component {
    constructor(props) {
        super(props);
        this.state = {
            dateRangeObj: {
                startDate: new Date(),
                endDate: new Date(),
                key: 'selection',
            },
            selectedDate: '',
            isSingleCalendar: true,
            selectedVendor: null,
            vendorOptions: [],
            selectedReport: null,
            reportOptions: [],
            showDRP: false,
            btnDeleteText: 'Delete',
            btnDelete: false,
            btnMoveToMainText: 'Move To Main',
            btnMoveToMain: false,
            btnVerifyText: 'Verify',
            btnVerify: false,
            data: [],
            orignalData: [],
            loading: false,
            totalRows: 0,
            perPage: 10,
            module: '',
            isVerifyDataShow: false,
            confirmMsgModal: false,
            deleteMoveToMainCallback:'',
            errors: {
                selectedDateE: '',
                selectedReportE: '',
                selectedVendorE: ''
            }
        }
    }

    onReportChange = (value) => {
        let tempVal = value.value;
        this.setState({
            selectedReport: value,
            selectedDate: ''
        }, () => {
            if (tempVal == 'traffic') {
                this.setState({
                    isSingleCalendar: false,
                })
            } else {
                this.setState({
                    isSingleCalendar: true,
                })
            }
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
        getAllVendors((fetchVendors) => {

            if (fetchVendors.length > 0) {
                let allVendors = fetchVendors.map((obj, idx) => {
                    return {
                        value: obj.vendor_id,
                        label: obj.vendor_name
                    }
                })

                this.setState({
                    vendorOptions: allVendors,
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
        })
    }

    validationSchema = (method = null) => {
        return Yup.object().shape({
            selectedVendorE: objectRequiredValidationHelper("vendor"),
            selectedReportE: objectRequiredValidationHelper("report"),
            selectedDateE: (method == 'delete') ? stringRequiredValidationHelper("date") : '',
        });
    }

    dataToValidateObject = () => {
        return {
            selectedVendorE: this.state.selectedVendor,
            selectedReportE: this.state.selectedReport,
            selectedDateE: this.state.selectedDate,

        }
    }

    showValidationErrorOnSubmit = (validationFormData) => {
        const {errors} = this.state;
        $.each(validationFormData, function (indexInArray, valueOfElement) {
            errors[indexInArray] = valueOfElement;
        });

        this.setState((prevState) => ({
            errors: errors
        }));
    }

    resetFormData = () => {
        this.setState({
            selectedDate: '',
            selectedReport: null,
            dateRangeObj: {
                startDate: new Date(),
                endDate: new Date(),
                key: 'selection',
            },
            selectedVendor: null
        })
    }
    submitDeleteForm = (e) => {
        e.preventDefault();
        this.setState({
            btnDeleteText: 'Deleting...',
            btnDelete: true,
            isVerifyDataShow: false,
            confirmMsgModal: false
        })
        let params = new FormData();
        params.append('vendor', this.state.selectedVendor.value)
        params.append('type', this.state.selectedReport.value)
        params.append('daterange', this.state.selectedDate)

        if (params != null) {
            deleteData(params, (data) => {
                this.setState({
                    btnDeleteText: 'Delete',
                    btnDelete: false
                })
                if (data.ajax_status != false) {
                    //this.resetFormData();
                    this.props.dispatch(ShowSuccessMsg(data.success, "Successfully", true, ""));
                } else {
                    this.props.dispatch(ShowFailureMsg(data.error, "", true, ""))
                }
            })
        }
    }

    submitVerifyForm = (e) => {
        e.preventDefault();
        let validationSchema = this.validationSchema();
        let dataToValidateObject = this.dataToValidateObject();
        let validationFormData = htk.validateAllFields(validationSchema, dataToValidateObject);

        if (Object.size(validationFormData) > 0) {
            this.showValidationErrorOnSubmit(validationFormData);
        } else {
            this.setState({
                btnVerifyText: 'Verifying...',
                btnVerify: true,
                isVerifyDataShow: false
            })
            let params = new FormData();
            params.append('vendor', this.state.selectedVendor.value)
            params.append('type', this.state.selectedReport.value)
            params.append('daterange', this.state.selectedDate)

            if (params != null) {
                verifyData(params, (data) => {
                    this.setState({
                        btnVerifyText: 'Verify',
                        btnVerify: false
                    })

                    if (data.ajax_status != false) {

                        //this.resetFormData();
                        this.setState({
                            data: data.data_array,
                            orignalData: data.data_array,
                            totalRows: data.data_array.length,
                            module: this.state.selectedReport.value,
                            isVerifyDataShow: true
                        })
                        //this.props.dispatch(ShowSuccessMsg(data.success, "Successfully", true, ""));

                    } else {
                        this.props.dispatch(ShowFailureMsg(data.error, "", true, ""))
                    }
                })
            }
        }
    }

    submitMoveToMainForm = (e) => {
        e.preventDefault();
        this.setState({
            btnMoveToMainText: 'Moving Data...',
            btnMoveToMain: true,
            isVerifyDataShow: false,
            confirmMsgModal: false
        })
        let params = new FormData();
        params.append('vendor', this.state.selectedVendor.value)
        params.append('type', this.state.selectedReport.value)
        params.append('daterange', this.state.selectedDate)

        if (params != null) {
            moveToMainData(params, (data) => {
                this.setState({
                    btnMoveToMainText: 'Move To Main',
                    btnMoveToMain: false
                })
                if (data.ajax_status != false) {
                    this.props.dispatch(ShowSuccessMsg(data.success, "Successfully", true, ""));
                } else {
                    this.props.dispatch(ShowFailureMsg(data.error, "", true, ""))
                }
            })
        }

    }

    handleOnDateRangeClick = (e) => {
        this.setState({
            showDRP: true
        })
    }

    getValue = (range) => {
        this.setState({
            dateRangeObj: range,
            selectedDate: moment(range.startDate).format('DD-MM-YYYY') + " - " + moment(range.endDate).format('DD-MM-YYYY'),
            showDRP: false
        }, () => {
            this.resetErrors('selectedDateE')
        })
    }

    onVendorChange = (value) => {
        this.setState({
            selectedVendor: value,
        }, () => {
            this.resetErrors('selectedVendorE')
        })
    }
    handleSingleDateChange = (date) => {
        this.setState({
            selectedDate: moment(date).format('DD-MM-YYYY'),
            showDRP: false
        }, () => {
            this.resetErrors('selectedDateE')
        })
    }
    helperCloseDRP = (event) => {
        this.setState({
            showDRP: false
        })
    }
    closeConfirmModal = () => {
        this.setState({
            confirmMsgModal: false
        })
    }

    deleteConfirmation = (e) => {
        e.preventDefault();
        let validationSchema = this.validationSchema('delete');
        let dataToValidateObject = this.dataToValidateObject();
        let validationFormData = htk.validateAllFields(validationSchema, dataToValidateObject);

        if (Object.size(validationFormData) > 0) {
            this.showValidationErrorOnSubmit(validationFormData);
        } else {
            this.setState({
                confirmMsgModal: true,
                deleteMoveToMainCallback: 'submitDeleteForm'
            })
        }

    }


    moveToMainConfirmation = (e) => {
        e.preventDefault();
        let validationSchema = this.validationSchema();
        let dataToValidateObject = this.dataToValidateObject();
        let validationFormData = htk.validateAllFields(validationSchema, dataToValidateObject);

        if (Object.size(validationFormData) > 0) {
            this.showValidationErrorOnSubmit(validationFormData);
        } else {
            this.setState({
                confirmMsgModal: true,
                deleteMoveToMainCallback: 'submitMoveToMainForm'
            })
        }
    }

    render() {
        const {classes} = this.props;
        return (
            <>
                <Card classes={{root: classes.card}}>
                    <Typography variant="h6" className={`${classes.pageTitle}`} noWrap>
                        Verify Record
                    </Typography>
                    <div className="p-5 rounded-lg mb-10">
                        <form>
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
                                        customClassName="ThemeSelect"
                                        styles={customStyle}
                                    />
                                    <div className="error pl-2">{this.state.errors.selectedReportE}</div>
                                </Grid>
                                <Grid item xs={12} sm={12} md={12} lg={12}>
                                    <Grid item xs={12} sm={6} md={6} lg={6}
                                          className="vendorCentralGridElement">
                                        <label className="inline-block ml-2 text-sm">
                                            Select Start & End Date Range (only for delete) <span
                                            className="required-asterisk">*</span>
                                        </label>

                                        <div onClick={this.handleOnDateRangeClick}>
                                            <TextFieldInput
                                                disabled={false}
                                                placeholder="Date Range"
                                                type="text"
                                                name={"selectedDate"}
                                                value={this.state.selectedDate}
                                                classesstyle={classes}
                                                fullWidth={true}
                                            />
                                            <div className="error pl-2">{this.state.errors.selectedDateE}</div>
                                        </div>

                                        <div className={`absolute z-50 ${classes.datepickerClass}`}>
                                            {
                                                this.state.showDRP ?
                                                    <CustomDateRangePicker range={this.state.dateRangeObj}
                                                                           helperCloseDRP={this.helperCloseDRP}
                                                                           setSingleDate={this.handleSingleDateChange}
                                                                           getValue={this.getValue}
                                                                           date={new Date()}
                                                                           direction="horizontal"
                                                                           isDateRange={this.state.isSingleCalendar ? false : true}/>

                                                    : null
                                            }
                                        </div>
                                    </Grid>
                                </Grid>

                            </Grid>
                            <div className="flex mt-8 justify-end verifyBtn">
                                <PrimaryButton
                                    btntext={this.state.btnDeleteText}
                                    variant={"contained"}
                                    disabled={this.state.btnDelete}
                                    //onClick={this.submitDeleteForm}
                                    onClick={this.deleteConfirmation}
                                />
                                <PrimaryButton
                                    btntext={this.state.btnVerifyText}
                                    variant={"contained"}
                                    disabled={this.state.btnVerify}
                                    onClick={this.submitVerifyForm}
                                />
                                <PrimaryButton
                                    btntext={this.state.btnMoveToMainText}
                                    variant={"contained"}
                                    disabled={this.state.btnMoveToMain}
                                    //onClick={this.submitMoveToMainForm}
                                    onClick={this.moveToMainConfirmation}
                                />
                            </div>
                        </form>
                    </div>
                    <ConfirmDelete
                        open={this.state.confirmMsgModal}
                        handleModalClose={this.closeConfirmModal}
                        isVerifyMoveToMain={this.state.deleteMoveToMainCallback}
                        moveToMainForm={this.submitMoveToMainForm}
                        deleteForm={this.submitDeleteForm}
                    />
                </Card>

                {this.state.isVerifyDataShow ?
                    <div style={{display: 'table', tableLayout: 'fixed', width: '100%'}}>
                        <div className={' mt-12'}></div>
                        <Card className="overflow-hidden customStyle" classes={{root: classes.card}}>
                            <div className="font-semibold mb-3 ml-6 w-3/12">Record</div>
                            <div className={clsx("w-full dataTableContainer")}>
                                <DataTable
                                    noHeader={true}
                                    wrap={false}
                                    responsive={true}
                                    columns={this.state.module == 'traffic' ? trafficColumn() : columns()}
                                    data={this.state.data}
                                    progressPending={this.state.loading}
                                    persistTableHead
                                    conditionalRowStyles={conditionalRowStyles}
                                />
                            </div>
                        </Card>
                    </div>
                    : null
                }
            </>
        );
    }
}

export default withStyles(useStyles)(connect(null)(Verify))