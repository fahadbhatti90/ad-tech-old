import React, { Component } from 'react';
import ModalDialog from './../../../../general-components/ModalDialog';
import TextFieldInput from "./../../../../general-components/Textfield";
import TextButton from "./../../../../general-components/TextButton";
import PrimaryButton from "./../../../../general-components/PrimaryButton";
import "./../styles.scss";
import {useStyles} from "./../styles";
import { withStyles } from "@material-ui/core/styles";
import { 
    stringMinLengthValidationHelper
} from './../../../../helper/yupHelpers';
import * as Yup from 'yup';
import {ShowSuccessMsg} from "./../../../../general-components/successDailog/actions";
import {connect} from "react-redux";
import LinearProgress from '@material-ui/core/LinearProgress';
import {addManagerCall} from "./../apiCalls";

const ValidateSchemaObject = {
    password: stringMinLengthValidationHelper("Password",7)
};
const validationSchema = Yup.object().shape(ValidateSchemaObject);

class ChangePasswordForm extends Component {
    constructor(props){
        super(props);
        this.state={
            newPassword:"",
            errors:{
                password:""
            },
            isProcessing: false
        }
    }

    passwordChangeHandler=(e)=>{
        this.setState({
            newPassword: e.target.value
        })
    }

    helperResetErrors = () =>{
        const {errors} = this.state;
        $.each(this.state.errors, function (indexInArray, valueOfElement) { 
            errors[indexInArray] = "";
        });
        return errors;
    }

    helperSetValidationErrorState = (errors,allValiditionFrom) => {
        $.each(allValiditionFrom, function (indexInArray, valueOfElement) { 
             errors[indexInArray] = valueOfElement;
        });
        this.setState((prevState)=>({
            errors
        }));
    }
    
    handleSubmit=(e)=>{
        e.preventDefault();
        let dataToValidatebject = {
            password: this.state.newPassword,
        };
        let allValiditionFrom = htk.validateAllFields(validationSchema, dataToValidatebject);
        
        let resetErrors = this.helperResetErrors();
        if (Object.size(allValiditionFrom) > 0) {
            this.helperSetValidationErrorState(resetErrors, allValiditionFrom);
        } else {
            let {newPassword} = this.state;
            let formData = {
                clientName:"",
                clientEmail:"",
                selectedBrands:"",
                opType: 3,
                password: newPassword,
                id: this.props.rowId
            }
            this.setState({
                isProcessing:true
            })
            addManagerCall(formData,(data) => {
                this.setState({
                    isProcessing:false
                })
                this.props.dispatch(ShowSuccessMsg(data.message, "", true, "",this.props.reloadData()));
            }, (err) => {
                //error
                this.setState({
                    isProcessing:false
                })
                this.props.dispatch(ShowFailureMsg(data.message, "", true, ""));
            });
        }
    }
    render() {
        const {classes} = this.props;
        return (
            <div>
                <div className="graphLoader bg-white absolute h-full overflow-hidden w-full top-0 left-0 z-10" style={this.state.isProcessing?{display:"block"}:{display:"none"}} >
                    <LinearProgress />
                    <div className="absolute flex font-bold font-mono h-full items-center justify-center overflow-hidden text-1rem text-sm w-full z-10">
                        Processing...
                    </div>
                </div>
            <form onSubmit={this.handleSubmit}>
                <div className={this.state.errors.password.length > 0 ? "errorCustom px-2" : "px-2"}>
                    <label className="text-xs font-normal ml-2">
                        Password <span className="required-asterisk">*</span>
                    </label>
                    <div className="ThemeInput">
                        <TextFieldInput
                            placeholder="Enter Password"
                            id="password"
                            name="newPassword"
                            type="password"
                            value={this.state.newPassword}
                            onChange={this.passwordChangeHandler}
                            fullWidth={true}
                            classesstyle = {classes}
                        />
                        <div className="error pl-3">{this.state.errors.password}</div>
                    </div>
                </div>

                <div className="flex float-right items-center justify-center my-5 w-full">
                    <div className="mr-3">
                        <TextButton
                        BtnLabel={"Cancel"}
                        color="primary"
                        onClick={this.props.handleClose}/>
                    </div>
                    <PrimaryButton
                        btnlabel={"Continue"}
                        variant={"contained"}
                        type="submit"
                    />     
                </div>
            </form>
        </div>
        );
    }
}

const ChangePasswordFormR = withStyles(useStyles)(connect(null)(ChangePasswordForm));

const ChangePassword = (props) => {
    return (
        <ModalDialog
            open={props.open}
            title={"Change Password"}
            handleClose ={props.handleModalClose}
            cancelEvent ={props.handleModalClose}
            component={
                <ChangePasswordFormR 
                    ChangePasswordHandler={props.ChangePasswordHandler}
                    rowId = {props.rowId}
                    reloadData={props.reloadData}
                    handleClose ={props.handleModalClose}
            />}
            maxWidth={"xs"}
            fullWidth={true}
            disable={true}
            >
        </ModalDialog>
    );
};

export default ChangePassword;