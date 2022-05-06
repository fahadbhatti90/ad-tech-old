import React, {Component} from 'react';
import {Grid} from "@material-ui/core";
import TextFieldInput from "./../../../../../general-components/Textfield";
import TextButton from "./../../../../../general-components/TextButton";
import PrimaryButton from "./../../../../../general-components/PrimaryButton";
import {connect} from "react-redux";
import {
    capitalizeFirstLetter,
    stringRequiredValidationHelper,
} from './../../../../../helper/yupHelpers';
import {useStyles} from "./../../../Manage-Users/styles";
import {withStyles} from "@material-ui/core/styles";
import * as Yup from 'yup';
import {ShowSuccessMsg} from "./../../../../../general-components/successDailog/actions";
import {ShowFailureMsg} from "./../../../../../general-components/failureDailog/actions";
import LinearProgress from '@material-ui/core/LinearProgress';
import {addScApiConfig, editScApiConfig} from '../../apiCalls';


const ValidateSchemaObject = {
    sellerNameE: stringRequiredValidationHelper("seller name")
        .max(50, "Seller name length must not be greater than " + 40)
        .matches(
            /^(?!\s*$).+/,
            "Seller Name Is required"
        ),
    sellerIdE: stringRequiredValidationHelper("seller Id")
        .max(14, "Seller Id length must not be greater than " + 40)
        .matches(
            /^(?!\s*$).+/,
            "Seller Id is required"
        ),
    accessKeyIdE: stringRequiredValidationHelper("Access key")
        .min(20, "Access key must be 20 characters long")
        .max(20, "Access key must be 20 characters long")
        .matches(
            /^(?!\s*$).+/,
            capitalizeFirstLetter("Access key") + "is required"
        ),
    authTokenE: stringRequiredValidationHelper("auth token")
        .matches(
            /^amzn\.mws\.[a-z0-9]{8}\-[a-z0-9]{4}\-[a-z0-9]{4}\-[a-z0-9]{4}\-[a-z0-9]{12}$/i,
        capitalizeFirstLetter("Invalid parameter value")
    ),
    secretTokenE: stringRequiredValidationHelper("secret key")
        .min(40, capitalizeFirstLetter("secret key") + " must be 40 characters long")
        .max(40, capitalizeFirstLetter("secret key") + " must be 40 characters long")
        .matches(
            /^(?!\s*$).+/,
            capitalizeFirstLetter("secret key") + "is required"
        ),
};

const validationSchema = Yup.object().shape(ValidateSchemaObject);

class FormScApi extends Component {
    constructor(props) {
        super(props);
        this.state = {
            sellerName: "",
            secretToken: "",
            sellerId: "",
            accessKeyId: "",
            authToken: "",
            id: null,
            errors: {
                sellerNameE: "",
                secretTokenE: "",
                sellerIdE: "",
                accessKeyIdE: "",
                authTokenE: "",
            },
            isLoading: false,
            isEdit: false,
            isProcessing: false
        }
    }

    static getDerivedStateFromProps(nextProps, prevState) {
        if (nextProps.isEdit && prevState.isEdit == false) {
            return {
                sellerName: nextProps.row.merchant_name,
                secretToken: nextProps.row.mws_secret_key,
                sellerId: nextProps.row.seller_id,
                accessKeyId: nextProps.row.mws_access_key_id,
                authToken: nextProps.row.mws_authtoken,
                id: nextProps.row.mws_config_id,
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
        }, () =>{
            this.resetErrors(fieldName + "E")
        })
    }


    handleSubmit = () => {
        let dataToValidatebject = {
            sellerNameE: this.state.sellerName,
            secretTokenE: this.state.secretToken,
            sellerIdE: this.state.sellerId,
            accessKeyIdE: this.state.accessKeyId,
            authTokenE: this.state.authToken
        };
        let allValidationFrom = htk.validateAllFields(validationSchema, dataToValidatebject);

        if (Object.size(allValidationFrom) > 0) {
            this.showValidationErrorOnSubmit(allValidationFrom);
        } else {
            let {sellerName, secretToken, sellerId, accessKeyId, authToken} = this.state;

            let formData = {
                merchant_name: sellerName,
                mws_secret_key: secretToken,
                seller_id: sellerId,
                mws_access_key_id: accessKeyId,
                mws_authtoken: authToken,
            }
            this.setState({
                isProcessing: true
            })
            addScApiConfig(formData, (data) => {
                this.setState({
                    isProcessing: false
                })
                if (data.status != false){
                    this.props.dispatch(ShowSuccessMsg(data.title, "", true, "", this.props.reloadData()));
                } else{
                    this.props.dispatch(ShowFailureMsg(data.title, "", true, ""));
                }

            }, (err) => {
                //error
                this.setState({
                    isProcessing: false
                })
                this.props.dispatch(ShowFailureMsg(data.title, "", true, ""));
            });
        }
    }

    handleUpdate = () => {
        let dataToValidatebject = {
            sellerNameE: this.state.sellerName,
            secretTokenE: this.state.secretToken,
            sellerIdE: this.state.sellerId,
            accessKeyIdE: this.state.accessKeyId,
            authTokenE: this.state.authToken
        };
        let allValidationFrom = htk.validateAllFields(validationSchema, dataToValidatebject);

        if (Object.size(allValidationFrom) > 0) {
            this.showValidationErrorOnSubmit(allValidationFrom);
        } else {
            let {id, sellerName, secretToken, sellerId, accessKeyId, authToken} = this.state;

            let formData = {
                merchant_name: sellerName,
                mws_secret_key: secretToken,
                seller_id: sellerId,
                mws_access_key_id: accessKeyId,
                mws_authtoken: authToken,
                mws_config_id:id
            }
            this.setState({
                isProcessing: true
            })
            editScApiConfig(formData, (data) => {
                this.setState({
                    isProcessing: false
                })
                if (data.status != false) {
                    this.props.dispatch(ShowSuccessMsg(data.title, "", true, "", this.props.reloadData()));
                } else {
                    this.props.dispatch(ShowFailureMsg(data.title, "", true, ""));
                }
            }, (err) => {
                //error
                this.setState({
                    isProcessing: false
                })
                this.props.dispatch(ShowFailureMsg(data.title, "", true, ""));
            });
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
                              className={this.state.errors.sellerNameE.length > 0 ? "errorCustom px-2" : "px-2"}>
                            <label className="text-xs font-normal ml-2">
                                Seller Name <span className="required-asterisk">*</span>
                            </label>
                            <div className="ThemeInput">
                                <TextFieldInput
                                    placeholder="Seller Name"
                                    name="sellerName"
                                    type="text"
                                    value={this.state.sellerName}
                                    onChange={this.tfChangeHandler}
                                    fullWidth={true}
                                    classesstyle={classes}
                                />
                                <div className="error pl-3">{this.state.errors.sellerNameE}</div>
                            </div>
                        </Grid>
                        <Grid item xs={12}
                              className={this.state.errors.sellerIdE.length > 0 ? "errorCustom px-2" : "px-2"}>
                            <label className="text-xs font-normal ml-2">
                                Seller Id <span className="required-asterisk">*</span>
                            </label>
                            <div className="ThemeInput">
                                <TextFieldInput
                                    placeholder="Seller Id"
                                    name="sellerId"
                                    type="text"
                                    value={this.state.sellerId}
                                    onChange={this.tfChangeHandler}
                                    fullWidth={true}
                                    classesstyle={classes}
                                />
                                <div className="error pl-3">{this.state.errors.sellerIdE}</div>
                            </div>
                        </Grid>
                        <Grid item xs={12}
                              className={this.state.errors.accessKeyIdE.length > 0 ? "errorCustom px-2" : "px-2"}>
                            <label className="text-xs font-normal ml-2">
                                Access Key Id <span className="required-asterisk">*</span>
                            </label>
                            <div className="ThemeInput">
                                <TextFieldInput
                                    placeholder="CGFAT4VW7BAGJLM72M5B"
                                    name="accessKeyId"
                                    type="text"
                                    value={this.state.accessKeyId}
                                    onChange={this.tfChangeHandler}
                                    fullWidth={true}
                                    classesstyle={classes}
                                />
                                <div className="error pl-3">{this.state.errors.accessKeyIdE}</div>
                            </div>
                        </Grid>
                        <Grid item xs={12}
                              className={this.state.errors.authTokenE.length > 0 ? "errorCustom px-2" : "px-2"}>
                            <label className="text-xs font-normal ml-2">
                                Auth Token <span className="required-asterisk">*</span>
                            </label>
                            <div className="ThemeInput">
                                <TextFieldInput
                                    placeholder="amzn.mws.19j14cdh-gccg-2049-6d4h-5f5mf07f4ccd"
                                    name="authToken"
                                    type="text"
                                    value={this.state.authToken}
                                    onChange={this.tfChangeHandler}
                                    fullWidth={true}
                                    classesstyle={classes}
                                />
                                <div className="error pl-3">{this.state.errors.authTokenE}</div>
                            </div>
                        </Grid>
                        <Grid item xs={12}
                              className={this.state.errors.secretTokenE.length > 0 ? "errorCustom px-2" : "px-2"}>
                            <label className="text-xs font-normal ml-2">
                                Secret Key <span className="required-asterisk">*</span>
                            </label>
                            <div className="ThemeInput">
                                <TextFieldInput
                                    placeholder="amzn.mws.19j14cdh-gccg-2049-6d4h-5f5mf07f4ccd"
                                    name="secretToken"
                                    type="text"
                                    value={this.state.secretToken}
                                    onChange={this.tfChangeHandler}
                                    fullWidth={true}
                                    classesstyle={classes}
                                />
                                <div className="error pl-3">{this.state.errors.secretTokenE}</div>
                            </div>
                        </Grid>
                    </Grid>
                    <div className="flex float-right items-center justify-center my-5 w-full">
                        <div className="mr-3">
                            <TextButton
                                BtnLabel={"Cancel"}
                                color="primary"
                                onClick={this.props.handleModalClose}/>
                        </div>
                        <PrimaryButton
                            btnlabel={this.props.isEdit ? "Update" : "Save"}
                            variant={"contained"}
                            onClick={this.props.isEdit ? this.handleUpdate : this.handleSubmit}
                        />
                    </div>
                </form>
            </div>
        );
    }
}

export default withStyles(useStyles)(connect(null)(FormScApi));