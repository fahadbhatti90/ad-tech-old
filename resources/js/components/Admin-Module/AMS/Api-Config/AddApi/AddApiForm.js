import React, { Component } from 'react';
import {Grid} from "@material-ui/core";
import TextFieldInput from "./../../../../../general-components/Textfield";
import TextButton from "./../../../../../general-components/TextButton";
import PrimaryButton from "./../../../../../general-components/PrimaryButton";
import {connect} from "react-redux";
import {
    stringRequiredValidationHelper,
    stringMinLengthValidationHelper
} from './../../../../../helper/yupHelpers';
import {useStyles} from "./../../../Manage-Users/styles";
import { withStyles } from "@material-ui/core/styles";
import * as Yup from 'yup';
import {addApiCall} from "./../../apiCalls"; 
import {ShowSuccessMsg} from "./../../../../../general-components/successDailog/actions";
import {ShowFailureMsg} from "./../../../../../general-components/failureDailog/actions";
import LinearProgress from '@material-ui/core/LinearProgress';
const ValidateSchemaObject = {
    client_id:  stringMinLengthValidationHelper("Client ID",31).matches(
        /amzn1\.application-oa2-client\.[a-z0-9]{32}$/i,
        "InValid Parameter Value for Client ID"),
    client_secret: stringMinLengthValidationHelper("Client Secret",64).matches(
    /^[a-z0-9]{64}$/i,
    "InValid Parameter Value for Client Secret"),
    refresh_token: stringMinLengthValidationHelper("Refresh Token", 65)
};


const validationSchema = Yup.object().shape(ValidateSchemaObject);

class AddApiForm extends Component {
    constructor(props) {
        super(props);
        this.state = {
            clientId:"",
            clientSecret:"",
            refreshToken:"",
            isProcessing:false,
            errors:{
                client_id:"",
                client_secret:"",
                refresh_token:""
            }
        }
    }
    componentDidMount(){
        if(this.props.isEdit) {
            this.setState({
                clientId:this.props.row.client_id,
                clientSecret:this.props.row.client_secret,
                refreshToken:this.props.row.refresh_token
            })
        }
        else {
            this.setState({
                clientId:"",
                clientSecret:"",
                refreshToken:""
            })
        }
    }
    tfChangeHandler=(e)=>{
        this.setState({
            [e.target.name]: e.target.value
        })
    }

    helperResetErrors = () =>{
        const {errors} = this.state;
        $.each(this.state.errors, function (indexInArray, valueOfElement) { 
            errors[indexInArray] = "";
        });
        return errors;
    }

    helperSetValidationErrorState = (errors, allValiditionFrom) => {
        $.each(allValiditionFrom, function (indexInArray, valueOfElement) { 
             errors[indexInArray] = typeof valueOfElement == "string" ? valueOfElement : valueOfElement[0];
        });
        this.setState((prevState)=>({
            errors
        }));
    }

    handleSubmit=()=>{
        let dataToValidatebject = {
            client_id: this.state.clientId,
            client_secret: this.state.clientSecret,
            refresh_token: this.state.refreshToken
        };
        let allValiditionFrom = htk.validateAllFields(validationSchema, dataToValidatebject);
        let resetErrors = this.helperResetErrors();
        if (Object.size(allValiditionFrom) > 0) {
            this.helperSetValidationErrorState(resetErrors, allValiditionFrom);
        } else {
            let {clientId, clientSecret, refreshToken} = this.state;
            let formData = {
                isEditing:this.props.isEdit,
                id: this.props.isEdit ? this.props.row.id : null,
                client_id:clientId,
                client_secret:clientSecret,
                refreshToken
            }
            this.setState({
                isProcessing:true
            })
            addApiCall(formData, (message) => {
                this.setState({
                    isProcessing:false
                })
                this.props.dispatch(ShowSuccessMsg(message, "", true, "",this.props.updateAfterSubmit));
            }, (errors) => {
                //backend validation errors
                this.setState({
                    isProcessing:false
                })
                let resetErrors = this.helperResetErrors();
                this.helperSetValidationErrorState(resetErrors, errors);
            },(err) => {
                //error
                this.setState({
                    isProcessing:false
                })
                this.props.dispatch(ShowFailureMsg(message, "", true, "",this.props.updateAfterSubmit));
            });
        }   
    }

    render() {
        const {classes} = this.props;
        return (
            <div className="px-8">
                <div className="graphLoader bg-white absolute h-full overflow-hidden w-full top-0 left-0 z-10" style={this.state.isProcessing?{display:"block"}:{display:"none"}} >
                    <LinearProgress />
                    <div className="absolute flex font-bold font-mono h-full items-center justify-center overflow-hidden text-1rem text-sm w-full z-10">
                        Processing...
                    </div>
                </div>
                <form>
                    <Grid container spacing={2}>
                        <Grid item xs={12} className={this.state.errors.client_id.length > 0 ? "errorCustom px-2" : "px-2"}>
                            <label className="text-xs font-normal ml-2">
                                Client ID <span className="required-asterisk">*</span>
                            </label>
                            <div className="ThemeInput">
                                <TextFieldInput
                                    placeholder="amzn1.application-oa2-client.a8358a60…"
                                    id="id"
                                    name="clientId"
                                    type="text"
                                    value={this.state.clientId}
                                    onChange={this.tfChangeHandler}
                                    fullWidth={true}
                                    classesstyle = {classes}
                                />
                                <div className="error pl-3">{this.state.errors.client_id}</div>
                            </div>
                        </Grid>
                        <Grid item xs={12} className={this.state.errors.client_secret.length > 0 ? "errorCustom px-2" : "px-2"}>
                            <label className="text-xs font-normal ml-2">
                                Client Secret <span className="required-asterisk">*</span>
                            </label>
                            <div className="ThemeInput">
                                <TextFieldInput
                                    placeholder="208257577110975193121591895857093449424"
                                    id="clientSecret"
                                    name="clientSecret"
                                    type="text"
                                    value={this.state.clientSecret}
                                    onChange={this.tfChangeHandler}
                                    fullWidth={true}
                                    classesstyle = {classes}
                                />
                                <div className="error pl-3">{this.state.errors.client_secret}</div>
                            </div>
                        </Grid>       
                        <Grid item xs={12} className={this.state.errors.refresh_token.length > 0 ? "errorCustom px-2" : "px-2"}>
                            <label className="text-xs font-normal ml-2">
                                Refresh Token <span className="required-asterisk">*</span>
                            </label>
                            <div className="ThemeInput">
                                <TextFieldInput
                                    placeholder="Atzr|IwEBINe7Jvf1FrgMlQypnICjRSqpueThtDg..."
                                    id="refreshToken"
                                    name="refreshToken"
                                    type="text"
                                    value={this.state.refreshToken}
                                    onChange={this.tfChangeHandler}
                                    fullWidth={true}
                                    classesstyle = {classes}
                                />
                                <div className="error pl-3">{this.state.errors.refresh_token}</div>
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
                        btnlabel={"Save"}
                        variant={"contained"}
                        onClick={this.handleSubmit}
                    />     
                </div>
                </form>
            </div>
        );
    }
}

export default withStyles(useStyles) (connect(null)(AddApiForm));