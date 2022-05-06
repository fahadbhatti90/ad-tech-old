import React, {Component} from 'react';
import {Grid, withStyles} from "@material-ui/core";
import TextFieldInput from "../../../../../general-components/Textfield";
//import {styles} from "../../styles";
import {customStyle, useStyles} from "../../../Manage-Users/styles";
import {connect} from "react-redux";
import SingleSelect from "../../../../../general-components/Select";
import {getAllVendors, uploadPurchaseOrder} from "../../apiCalls"
import {ShowSuccessMsg} from "../../../../../general-components/successDailog/actions";
import {ShowFailureMsg} from "../../../../../general-components/failureDailog/actions";
import * as Yup from "yup";
import {
    objectRequiredValidationHelper,
    stringRequiredValidationHelper
} from "../../../../../helper/yupHelpers";
import PrimaryButton from "../../../../../general-components/PrimaryButton";

const list = (errorsData) => {
    return (
        <>
            {errorsData.map((item, idx) => (
                <li key={idx} className="list-none"> {item} </li>
            ))}
        </>
    )
}

class UploadFile extends Component {
    constructor(props) {
        super(props);
        this.state = {
            selectedVendor: null,
            openAgg:'',
            openNonAgg:'',
            closeAgg:'',
            closeNonAgg:'',
            vendorOptions: [],
            btnText:'Submit',
            isBtnEnableDisable:false,
            errors:{
                selectedVendorE:'',
                openAggE:'',
                openNonAggE:'',
                closeAggE:'',
                closeNonAggE:''
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
        let inputVal = e.target.name;
        this.setState({
            [e.target.name]: e.target.files[0]
        }, () =>{
            this.resetErrors(inputVal + 'E')
        })
    }

    formSubmissionPurchaseOrder = (e) => {
        e.preventDefault();
        var event = e.target;

        let validationSchema = Yup.object().shape({
            selectedVendorE: objectRequiredValidationHelper("vendor"),
            //openAggE:stringRequiredValidationHelper('open AGG file'),
            //openNonAggE:stringRequiredValidationHelper('open Non AGG file'),
            //closeAggE:stringRequiredValidationHelper('close AGG file'),
            //closeNonAggE:stringRequiredValidationHelper('close Non AGG file')
        });

        let dataToValidateObject = {
            selectedVendorE: this.state.selectedVendor,
            //openAggE:this.state.openAgg,
            //openNonAggE:this.state.openNonAgg,
            //closeAggE:this.state.closeAgg,
            //closeNonAggE:this.state.closeNonAgg
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
                btnText:'Submitting...',
                isBtnEnableDisable:true
            })
            let params = new FormData();
            params.append('vendor', this.state.selectedVendor.value)
            params.append('open_agg_file', this.state.openAgg)
            params.append('open_nonagg_file', this.state.openNonAgg)
            params.append('close_agg_file', this.state.closeAgg)
            params.append('close_nonagg_file', this.state.closeNonAgg)

            if (params != null) {
                uploadPurchaseOrder(params, (data) => {
                    this.setState({
                        btnText:'Submit',
                        isBtnEnableDisable:false
                    })
                    if (data.ajax_status != false) {
                        event.reset();
                        this.setState({
                            selectedVendor: null,
                            openAgg:'',
                            openNonAgg:'',
                            closeAgg:'',
                            closeNonAgg:'',
                            btnText:'Submit',
                            isBtnEnableDisable:false
                        })
                        this.props.dispatch(ShowSuccessMsg("Purchase order files has been uploaded!", "Successfully", true, ""));

                    } else {

                        this.props.dispatch(ShowFailureMsg("", "", true, "", list(data.error)))
                    }
                })
            } else {

            }
        }
    }

    render() {
        return (
            <div className="p-5 rounded-lg mb-10">
                <form onSubmit={this.formSubmissionPurchaseOrder}>
                    <Grid container justify="center" spacing={3}>
                        <Grid item xs={12} sm={12} md={12} lg={12} className="">
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
                        </Grid>

                        <Grid item xs={12} sm={6} md={6} lg={6}
                              className="vendorCentralGridElement">
                            <label className="inline-block mb-2 ml-2 text-sm">
                                Open AGG <span className="required-asterisk">*</span>
                            </label>

                            <TextFieldInput
                                type="file"
                                className="vendorCentralTextField rounded-full bg-white vendorCentralFileUpload"
                                name="openAgg"
                                onChange={this.onChangeHandler}
                                fullWidth={true}
                                inputProps={{ accept: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel, .csv' }}
                            />
                            <div className="error pl-2">{this.state.errors.openAggE}</div>
                        </Grid>
                        <Grid item xs={12} sm={6} md={6} lg={6}
                              className="vendorCentralGridElement">
                            <label className="inline-block mb-2 ml-2 text-sm">
                                Open Non AGG <span className="required-asterisk">*</span>
                            </label>

                            <TextFieldInput
                                type="file"
                                className="vendorCentralTextField rounded-full bg-white vendorCentralFileUpload"
                                name="openNonAgg"
                                onChange={this.onChangeHandler}
                                fullWidth={true}
                                inputProps={{ accept: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel, .csv' }}
                            />
                            <div className="error pl-2">{this.state.errors.openNonAggE}</div>
                        </Grid>
                        <Grid item xs={12} sm={6} md={6} lg={6}
                              className="vendorCentralGridElement">
                            <label className="inline-block mb-2 ml-2 text-sm">
                                Close AGG <span className="required-asterisk">*</span>
                            </label>

                            <TextFieldInput
                                type="file"
                                className="vendorCentralTextField rounded-full bg-white vendorCentralFileUpload"
                                name="closeAgg"
                                onChange={this.onChangeHandler}
                                fullWidth={true}
                                inputProps={{ accept: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel, .csv' }}
                            />
                            <div className="error pl-2">{this.state.errors.closeAggE}</div>
                        </Grid>
                        <Grid item xs={12} sm={6} md={6} lg={6}
                              className="vendorCentralGridElement">
                            <label className="inline-block mb-2 ml-2 text-sm">
                                Close Non AGG <span className="required-asterisk">*</span>
                            </label>

                            <TextFieldInput
                                type="file"
                                className="vendorCentralTextField rounded-full bg-white vendorCentralFileUpload"
                                name="closeNonAgg"
                                onChange={this.onChangeHandler}
                                fullWidth={true}
                                inputProps={{ accept: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel, .csv' }}
                            />
                            <div className="error pl-2">{this.state.errors.closeNonAggE}</div>
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