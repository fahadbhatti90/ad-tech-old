import React, { Component } from 'react'
import clsx from 'clsx';
import {connect} from "react-redux"
import TextButton from "./../../../general-components/TextButton";
import PrimaryButton from "./../../../general-components/PrimaryButton";
import {ShowSuccessMsg} from "./../../../general-components/successDailog/actions";
import {ShowFailureMsg} from "./../../../general-components/failureDailog/actions";
import { withStyles } from "@material-ui/core/styles";
import {primaryColor, primaryColorLight} from "./../../../app-resources/theme-overrides/global";
import TextFieldInput from "./../../../general-components/Textfield";
import {
    addOverrideLabel
} from './apiCalls';
import LinearProgress from '@material-ui/core/LinearProgress';
import * as Yup from 'yup';
import {
    stringRequiredValidationHelper, 
} from './../../../helper/yupHelpers';
import { data } from 'jquery';

const useStyles = theme => ({
    root: {
      '& .MuiInputBase-root':{
        marginTop: 8,        
        borderRadius: 12,
        border: "1px solid #c3bdbd8c",
        height: 35,
        background: '#fff'
      },
      "&:hover .MuiInputBase-root": {
        borderColor: primaryColorLight,
        borderRadius: "12px",
      },
      '& .MuiInputBase-input':{
        margin: props=>props.margin || 15,
        fontSize:'0.72rem',
        padding: '7px 0 7px'
      }
    },
    focused:{
      border: "2px solid !important",
      borderColor: `${primaryColor} !important`,
    }
});
const ValidateSchemaObject = {
    overrideLabel: stringRequiredValidationHelper("Override Label"),
};

const validationSchema = Yup.object().shape(ValidateSchemaObject);
class AddLabelOverride extends Component {
    constructor(props){
        super(props);
        this.state = {
            orignalLabel:"",
            overrideLabel:"",
            form:{
                isFormLoading:false,
                loadingText:"Loading..."
            },
            errors: {
                orignalLabel:"",
                overrideLabel:"",
            },
        }//end state
    }
    componentDidMount(){
        this.setState({
            overrideLabel:this.props.ajaxData.alias ? this.props.ajaxData.alias : ""
        })
    }
    onChangeHandler = (e) => {
        const {errors} = this.state;
        const name = e.target.name;
        const errorName = $("input[name='"+name+"']").attr("id");
        
        let allValiditionFrom = htk.validateAllFields(validationSchema, {[errorName]: e.target.value.trim()});
        errors[errorName] = Object.size(allValiditionFrom) > 0 ? allValiditionFrom[errorName] : "";
        this.setState({
            [e.target.name]: e.target.value,
            errors
        })
    }
    helperSetValidationErrorState = (errors,allValiditionFrom) => {
        $.each(allValiditionFrom, function (indexInArray, valueOfElement) { 
             errors[indexInArray] = valueOfElement;
        });
        this.setState((prevState)=>({
            errors
        }));
    }
    helperResetErrors = () =>{
        const {errors} = this.state;
        $.each(this.state.errors, function (indexInArray, valueOfElement) { 
            errors[indexInArray] = "";
        });
        return errors;
    }
    handleAddEventFormSubmit = () => {
        
            let dataToValidatebject = {
                overrideLabel: this.state.overrideLabel.trim(),
            };
            let allValiditionFrom = htk.validateAllFields(validationSchema, dataToValidatebject);
            if (Object.size(allValiditionFrom) > 0 ) {
                let resetErrors = this.helperResetErrors();
                this.helperSetValidationErrorState(resetErrors, allValiditionFrom);
            } else {
                let ajaxData = {
                    fkId:this.props.ajaxData.fkId,
                    type:this.props.ajaxData.type,
                    overrideLabel: this.state.overrideLabel,
                }
                this.manageEevent(ajaxData);
            }
    }
    manageEevent = (ajaxData) => {
        this.setState({
            form:{
                isFormLoading:true,
                loadingText:"Processing..."
            },
        });
        addOverrideLabel(
        ajaxData,
        (response)=>{
            this.props.dispatch(ShowSuccessMsg("Successfull", response.message, true, "",this.props.heloperReloadDataTable(response.tableData)));
        },(error)=>{
            this.setState((prevState)=>({
                    form:{
                        isFormLoading:false,
                        loadingText:"Processing..."
                    },
            }));
            this.props.dispatch(ShowFailureMsg(error, "", true, ""));
        })
    }
    validatePastEvent = (e) => {
        // access the clipboard using the api
        var pastedData = e.clipboardData.getData("text");
        e.preventDefault();
        console.log(pastedData,pastedData.length > 99);
        
        setTimeout(function() {
          if (pastedData.length > 99) {
            var orignal = pastedData.substring(0, 99);
            this.setState({
                overrideLabel: (orignal)
            });
          }
        }, 100);
    }
    handleTagKeyPress = (e) =>{
        if (e.target.value.length > 99) e.preventDefault();
    }
    render() {
        const {classes} = this.props;
        return (
            <>
            <div className="labelOverrideForm px-5">
                <div className="graphLoader bg-white absolute h-full overflow-hidden w-full top-0 left-0 z-10" style={this.state.form.isFormLoading?{display:"block"}:{display:"none"}} >
                    <LinearProgress />
                    <div className="absolute flex font-bold font-mono h-full items-center justify-center overflow-hidden text-1rem text-sm w-full z-10">
                        {this.state.form.loadingText}
                    </div>
                </div>
                <div className={clsx("orignalLabel pt-5 disabledElement", this.state.errors.orignalLabel.length > 0 ? "errorCustom" : "")}>
                    <label className="text-xs font-normal ml-2">
                        Orignal Product Title:
                    </label>
                    <TextFieldInput
                        placeholder="Orignal Product Title"
                        type="text"
                        id="orignalLabel"
                        name={"orignalLabel"}
                        value={this.props.ajaxData.labelOverride}
                        fullWidth={true}
                        classesstyle = {classes}
                        disabled
                    />
                    <div className="error pl-3">{this.state.errors.orignalLabel}</div>
                </div>
                <div className={clsx("overrideLabel pt-5",this.state.errors.overrideLabel.length > 0 ? "errorCustom" : "")}>
                    <label className="text-xs font-normal ml-2">
                        Alias
                    </label>
                    <TextFieldInput
                        placeholder="Enter Alias"
                        type="text"
                        id="overrideLabel"
                        name={"overrideLabel"}
                        value={this.state.overrideLabel}
                        fullWidth={true}
                        onChange={this.onChangeHandler}
                        onKeyPress={this.handleTagKeyPress} 
                        onPaste={this.validatePastEvent} 
                        classesstyle = {classes}
                    />
                    <div className="error pl-3">{this.state.errors.overrideLabel}</div>
                </div>
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
                        onClick={this.handleAddEventFormSubmit}
                        />     
                </div>
            </div>
            </>
        )
    }
}

export default withStyles(useStyles)(connect(null)(AddLabelOverride))
