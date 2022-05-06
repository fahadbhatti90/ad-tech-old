import React, {Component} from 'react';
import {Grid, withStyles} from "@material-ui/core";
import TextFieldInput from "../../../../../general-components/Textfield";
//import {styles} from '../../styles'
import {customStyle, useStyles} from "../../../Manage-Users/styles";
import * as Yup from "yup";
import {
    capitalizeFirstLetter,
    stringRequiredValidationHelper
} from "../../../../../helper/yupHelpers";
import {vendorAddSubmission} from '../../apiCalls';
import {ShowSuccessMsg} from "../../../../../general-components/successDailog/actions";
import {ShowFailureMsg} from "../../../../../general-components/failureDailog/actions";
import {connect} from "react-redux";
import PrimaryButton from "../../../../../general-components/PrimaryButton";

const list = (errorsData) => {
    return (
        <>
            {errorsData.map(item => (
                <li className="list-none"> {item} </li>
            ))}
        </>
    )
};

class AddVendor extends Component{
    constructor(props) {
        super(props);
        this.state = {
            vendorName:'',
            domain:'',
            tier:'',
            btnText:'Submit',
            isBtnEnableDisable:false,
            errors:{
                vendorNameE:'',
                domainE:'',
                tierE:'',
            }
        }
    }

    onChangeText = (e) => {
        let fieldName = e.target.name;
        this.setState({
            [e.target.name]: e.target.value
        }, () => {
            this.resetErrors(fieldName + 'E')
        })
    }

    resetErrors = (key) => {
        let {errors} = this.state;
        errors[key] = ""
        this.setState({
            ...errors
        })
    }

    formSubmissionDayParting = () =>{
        let validationSchema = Yup.object().shape({
            vendorNameE: stringRequiredValidationHelper("vendor name")
                .max(40, capitalizeFirstLetter("vendor name") + " length must not be greater than " +40),
            domainE: stringRequiredValidationHelper("domain").max(10, capitalizeFirstLetter("domain") + " length not be greater than " +10),
            tierE: stringRequiredValidationHelper("tier").max(10, capitalizeFirstLetter("tier") + " length not be greater than " +10),
        });

        let dataToValidateObject = {
            vendorNameE: this.state.vendorName,
            domainE: this.state.domain,
            tierE: this.state.tier
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
            let params = {
                'vendor_name':this.state.vendorName,
                'domain':this.state.domain,
                'tier':this.state.tier,
            }

            if (params != null) {
                vendorAddSubmission(params, (data) => {
                    this.setState({
                        btnText:'Submit',
                        isBtnEnableDisable:false
                    })
                    if (data.ajax_status != false) {
                        this.setState({
                            vendorName:'',
                            domain:'',
                            tier:''
                        })
                        this.props.dispatch(ShowSuccessMsg(data.success, "Successfully", true, ""));

                    } else {

                        this.props.dispatch(ShowFailureMsg("", "", true, "", list(data.error)))
                    }
                })
            }
        }

    }

    render() {
        const {classes} = this.props;
        return (
            <div className="p-5 rounded-lg mb-10">
                <form>
                    <Grid container justify="center" spacing={3}>
                        <Grid item xs={12} sm={6} md={6} lg={6}
                              className="vendorCentralGridElement">
                            <label className="inline-block mb-2 ml-2 text-sm">
                                Vendor <span className="required-asterisk">*</span>
                            </label>

                            <TextFieldInput
                                placeholder="Enter Vendor e.g Xyz"
                                id="dr"
                                type="text"
                                className="vendorCentralTextField rounded-full bg-white"
                                name="vendorName"
                                value={this.state.vendorName}
                                onChange={this.onChangeText}
                                fullWidth={true}
                                classesstyle={classes}
                            />
                            <div className="error pl-2">{this.state.errors.vendorNameE}</div>
                        </Grid>
                        <Grid item xs={12} sm={6} md={6} lg={6}
                              className="vendorCentralGridElement">
                            <label className="inline-block mb-2 ml-2 text-sm">
                                Domain <span className="required-asterisk">*</span>
                            </label>

                            <TextFieldInput
                                placeholder="Enter Domain e.g US"
                                id="dr"
                                type="text"
                                className="vendorCentralTextField rounded-full bg-white"
                                name="domain"
                                value={this.state.domain}
                                onChange={this.onChangeText}
                                fullWidth={true}
                                classesstyle={classes}
                            />
                            <div className="error pl-2">{this.state.errors.domainE}</div>
                        </Grid>
                        <Grid item xs={12} sm={12} md={12} lg={12} >
                            <Grid item xs={12} sm={6} md={6} lg={6}
                                  className="vendorCentralGridElement">
                                <label className="inline-block mb-2 ml-2 text-sm">
                                    Tier <span className="required-asterisk">*</span>
                                </label>

                                <TextFieldInput
                                    placeholder="Enter Tier e.g Platinum"
                                    id="dr"
                                    type="text"
                                    className="vendorCentralTextField rounded-full bg-white"
                                    name="tier"
                                    value={this.state.tier}
                                    onChange={this.onChangeText}
                                    fullWidth={true}
                                    classesstyle={classes}
                                />
                                <div className="error pl-2">{this.state.errors.tierE}</div>
                            </Grid>
                        </Grid>
                    </Grid>
                    <div className="flex mt-8 justify-end">
                        <PrimaryButton
                            btntext={this.state.btnText}
                            variant={"contained"}
                            disabled={this.state.isBtnEnableDisable}
                            onClick={this.formSubmissionDayParting}
                        />
                    </div>
                </form>
            </div>
        );
    }
}

export default withStyles(useStyles)(connect(null)(AddVendor))