import React, {Component} from 'react';
import {Grid} from "@material-ui/core";
import TextFieldInput from "../../../../general-components/Textfield";
import TextButton from "../../../../general-components/TextButton";
import PrimaryButton from "../../../../general-components/PrimaryButton";
import {connect} from "react-redux";
import {withStyles} from "@material-ui/core/styles";
import * as Yup from 'yup';
import {ShowSuccessMsg} from "../../../../general-components/successDailog/actions";
import {ShowFailureMsg} from "../../../../general-components/failureDailog/actions";
import LinearProgress from '@material-ui/core/LinearProgress';
import {addUpdateAgency} from '../apiCalls';
import {
    stringRequiredValidationHelper,
    stringMinLengthValidationHelper, capitalizeFirstLetter
} from '../../../../helper/yupHelpers';

import {useStyles, customStyle} from "../styles";

const ValidateSchemaObject = {
    agencyNameE: stringRequiredValidationHelper("Name")
        .max(200, "Name must be less than 200 Characters")
        .matches(
            /^\S[a-zA-Z0-9_ ]+$/,
            "The  name can only consist of alphabetical, number and underscore"
        ),
    agencyEmailE: stringRequiredValidationHelper("Email")
        .max(200, "Email must be less than 200 Characters")
        .matches(
        /[\w\d\.-]+@[\w\d\.-]+\.[\w\d\.-]+/g,
        "InValid Email"
    ),
    agencyPasswordE: stringMinLengthValidationHelper("Password", 7)
};


const validationSchema = Yup.object().shape(ValidateSchemaObject);

class AddAgencyForm extends Component {
    constructor(props) {
        super(props);
        this.state = {
            agencyName: "",
            agencyEmail: "",
            agencyPassword: "",
            opType: 1,
            id: null,
            errors: {
                agencyNameE: "",
                agencyEmailE: "",
                agencyPasswordE: ""
            },
            isLoading: false,
            isEdit: false,
            isProcessing: false
        }
    }

    static getDerivedStateFromProps(nextProps, prevState) {
        if (nextProps.isEdit && prevState.isEdit == false) {
            return {
                agencyName: nextProps.row.name,
                agencyEmail: nextProps.row.email,
                id: nextProps.row.id,
                isEdit: nextProps.isEdit
            }
        }

        return null;
    }

    resetErrors = (key) => {
        let {errors} = this.state;
        errors[key] = ""
        this.setState({
            ...errors
        })
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

    tfChangeHandler = (e) => {
        let fieldName = e.target.name
        this.setState({
            [e.target.name]: e.target.value
        }, () => {
            this.resetErrors(fieldName + "E")
        })
    }
    handleSubmit = () => {
        let dataToValidateObject = {
            agencyNameE: this.state.agencyName,
            agencyEmailE: this.state.agencyEmail,
            agencyPasswordE: this.state.agencyPassword
        };
        let allValidationFrom = htk.validateAllFields(validationSchema, dataToValidateObject);

        if (Object.size(allValidationFrom) > 0) {
            this.showValidationErrorOnSubmit(allValidationFrom);
        } else {
            let {agencyName, agencyEmail, agencyPassword} = this.state;

            let formData = {
                clientName: agencyName,
                clientEmail: agencyEmail,
                password: agencyPassword,
                opType: 1,
            }

            this.setState({
                isProcessing: true
            })
            addUpdateAgency(formData, (data) => {
                this.setState({
                    isProcessing: false
                })
                if (data.status != false) {
                    this.props.dispatch(ShowSuccessMsg(data.message, "", true, "", this.props.reloadData()));
                } else {
                    this.props.dispatch(ShowFailureMsg(data.message, "", true, ""));
                }
            })
        }
    }
    handleUpdate = () => {
        let dataToValidateObject = {
            agencyNameE: this.state.agencyName,
            agencyEmailE: this.state.agencyEmail
        };
        let allValidationFrom = htk.validateAllFields(validationSchema, dataToValidateObject);

        if (Object.size(allValidationFrom) > 0) {
            this.showValidationErrorOnSubmit(allValidationFrom);
        } else {
            let {id, agencyName, agencyEmail} = this.state;

            let formData = {
                id: id,
                clientName: agencyName,
                clientEmail: agencyEmail,
                opType: 2,
            }

            this.setState({
                isProcessing: true
            })
            addUpdateAgency(formData, (data) => {
                this.setState({
                    isProcessing: false
                })
                if (data.status != false) {
                    this.props.dispatch(ShowSuccessMsg(data.message, "", true, "", this.props.reloadData()));
                } else {
                    this.props.dispatch(ShowFailureMsg(data.message, "", true, ""));
                }
            })
        }
    }

    render() {
        const {classes} = this.props;
        return (
            <div className="px-8">
                <div className="graphLoader bg-white absolute h-full overflow-hidden w-full top-0 left-0 z-10"
                     style={this.state.isProcessing ? {display: "block"} : {display: "none"}}>
                    <LinearProgress/>
                    <div
                        className="absolute flex font-bold font-mono h-full items-center justify-center overflow-hidden text-1rem text-sm w-full z-10">
                        Processing...
                    </div>
                </div>
                <form>
                    <Grid container spacing={2}>
                        <Grid item xs={12}
                              className={this.state.errors.agencyNameE.length > 0 ? "errorCustom px-2" : "px-2"}>
                            <label className="text-xs font-normal ml-2">
                                Name <span className="required-asterisk">*</span>
                            </label>
                            <div className="ThemeInput">
                                <TextFieldInput
                                    placeholder="Enter Agency Name"
                                    id="name"
                                    name="agencyName"
                                    type="text"
                                    value={this.state.agencyName}
                                    onChange={this.tfChangeHandler}
                                    fullWidth={true}
                                    classesstyle={classes}
                                />
                                <div className="error pl-3">{this.state.errors.agencyNameE}</div>
                            </div>
                        </Grid>
                        <Grid item xs={12}
                              className={this.state.errors.agencyEmailE.length > 0 ? "errorCustom px-2" : "px-2"}>
                            <label className="text-xs font-normal ml-2">
                                Email <span className="required-asterisk">*</span>
                            </label>
                            <div className="ThemeInput">
                                <TextFieldInput
                                    placeholder="Enter Email Address"
                                    id="email"
                                    name="agencyEmail"
                                    type="text"
                                    value={this.state.agencyEmail}
                                    onChange={this.tfChangeHandler}
                                    fullWidth={true}
                                    classesstyle={classes}
                                />
                                <div className="error pl-3">{this.state.errors.agencyEmailE}</div>
                            </div>
                        </Grid>
                        {!this.props.isEdit ?
                            <Grid item xs={12}
                                  className={this.state.errors.agencyPasswordE.length > 0 ? "errorCustom px-2" : "px-2"}>
                                <label className="text-xs font-normal ml-2">
                                    Password <span className="required-asterisk">*</span>
                                </label>
                                <div className="ThemeInput">
                                    <TextFieldInput
                                        placeholder="Enter Password"
                                        id="password"
                                        name="agencyPassword"
                                        type="password"
                                        value={this.state.agencyPassword}
                                        onChange={this.tfChangeHandler}
                                        fullWidth={true}
                                        classesstyle={classes}
                                    />
                                    <div className="error pl-3">{this.state.errors.agencyPasswordE}</div>
                                </div>
                            </Grid>
                            : ""}
                    </Grid>
                    <div className="flex float-right items-center justify-center my-5 w-full">
                        <div className="mr-3">
                            <TextButton
                                btntext={"Cancel"}
                                color="primary"
                                onClick={this.props.handleModalClose}/>
                        </div>
                        <PrimaryButton
                            btntext={this.props.isEdit ? "Update" : "Save"}
                            variant={"contained"}
                            onClick={this.props.isEdit ? this.handleUpdate : this.handleSubmit}
                        />
                    </div>
                </form>
            </div>
        )
    }
}

export default withStyles(useStyles)(connect(null)(AddAgencyForm));