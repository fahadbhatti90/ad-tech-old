import React, { Component } from 'react';

import MultiSelect from "./../../../../general-components/MultiSelect";
import SingleSelect from "./../../../../general-components/Select";
import TextButton from "./../../../../general-components/TextButton";
import PrimaryButton from "./../../../../general-components/PrimaryButton";
import {customStyle} from "./../styles";
import {getUsersBasedOnType,reassignBrandandDelete} from "./../apiCalls";
import {
    objectRequiredValidationHelper
} from './../../../../helper/yupHelpers';
import * as Yup from 'yup';
import {connect} from "react-redux";
import {ShowSuccessMsg} from "./../../../../general-components/successDailog/actions";
import {ShowFailureMsg} from "./../../../../general-components/failureDailog/actions";
import LinearProgress from '@material-ui/core/LinearProgress';

const ValidateSchemaObject = {
    users: objectRequiredValidationHelper("Select Users ")
};

const validationSchema = Yup.object().shape(ValidateSchemaObject);

class ReAssignForm extends Component {
    constructor(props){
        super(props);
        this.state={
            userTypeOptions:[
                {label: "Admin", value:2},
                {label: "Manager", value:3},
            ],
            selectedUserType:{label: "Admin", value:2},
            selectedUser:null,
            userOptions:[],
            errors:{
                users:""
            },
            isProcessing:false
        }
    }

    componentDidMount(){
        this.getUserOptions(this.state.selectedUserType.label);
    }
    onUserTypeChange=(selectedUserType)=>{
        this.setState({
            selectedUserType
        },()=>{
            this.getUserOptions(selectedUserType.label);
        });   
    }

    getUserOptions =(selectedUserType)=>{
        let params = {assignWithUserType:selectedUserType}
        getUsersBasedOnType(params,(userOptions) => {
            let updatedOptions = [];
            if(selectedUserType == "Manager"){
                updatedOptions = userOptions.filter((obj)=>obj.value != this.props.rowId)
            } else{
                updatedOptions = userOptions;
            }
            if(selectedUserType)
            this.setState({
             userOptions:updatedOptions,
             selectedUser:null
            })
         }, (err) => {
             //error
             console.log("Error loading Users..!");
         });
    }
    onSelectedUserChange=(selectedUser)=>{
        this.setState({
            selectedUser
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

    handleSubmit=()=>{
        let dataToValidatebject = {
            users: this.state.selectedUser
        };
        let allValiditionFrom = htk.validateAllFields(validationSchema, dataToValidatebject);
        let resetErrors = this.helperResetErrors();
        if (Object.size(allValiditionFrom) > 0) {
            this.helperSetValidationErrorState(resetErrors, allValiditionFrom);
        } else {
            let {errors} = this.state; 
            this.setState({
                ...errors,
                users:""
            });
            let selectedUsers = null;
            let assignedBrandToOtherIds = null;
            if(this.state.selectedUser !=  null){
                selectedUsers = this.state.selectedUser.map((item)=>item.value);
                selectedUsers = selectedUsers.toString()
            }
            if(this.props.brandsNames !=  null){
                assignedBrandToOtherIds = this.props.brandsNames.map((item)=> item.value);
                assignedBrandToOtherIds = assignedBrandToOtherIds.toString();
            }
            let form = {
                deleteUserId: this.props.rowId,
                assignedBrandToOtherIds,
                selectedUsers,
                opType: 1
            }
            this.setState({
                isProcessing:true
            })
            reassignBrandandDelete(form,(data) => {
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
        return (
        <>
            <div className="graphLoader bg-white absolute h-full overflow-hidden w-full top-0 left-0 z-10" style={this.state.isProcessing?{display:"block"}:{display:"none"}} >
                    <LinearProgress />
                    <div className="absolute flex font-bold font-mono h-full items-center justify-center overflow-hidden text-1rem text-sm w-full z-10">
                        Processing...
                    </div>
                </div>
            <form className="px-10">
                <div className="p-2">
                    Assign following brands to any user before you delete this.
                </div>
                <ul>
                    {this.props.brandsNames?
                        this.props.brandsNames.map((item,i) => <li key={i}>{item.label}</li>)
                        :
                        ""}
                </ul>

                <div className="mt-1">
                    <label className="text-sm ml-1">
                        Select User Type
                    </label>
                    <SingleSelect
                        placeholder="Select User Type"
                        id="selectedUser"
                        isClearable={false}
                        name={"selectedUserType"}
                        value={this.state.selectedUserType}
                        onChangeHandler={this.onUserTypeChange}
                        fullWidth={true}
                        Options={this.state.userTypeOptions}
                        customClassName="ThemeSelect"
                        styles={customStyle}
                    />
                </div>

                <div className={this.state.errors.users.length > 0 ? "errorCustom mt-1" : "mt-1"}>
                    <label className="text-xs font-normal ml-1">
                        Select Users 
                    </label>
                    <div className="ThemeInput">
                        <MultiSelect
                            isDisabled={this.state.disableFilters}
                            placeholder="Select User"
                            id="su"
                            name="selectedUser"       
                            value={this.state.selectedUser}
                            onChangeHandler = {this.onSelectedUserChange}
                            Options={this.state.userOptions}
                            styles={customStyle}
                            menuPlacement="auto"
                            onMenuOpen = {this.props.onMenuOpen}
                            onMenuClose = {this.props.onMenuClose}
                            onMenuOpen={this.props.onMenuOpen} 
                            />
                        <div className="error pl-3">{this.state.errors.users}</div>
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
                        onClick={this.handleSubmit}
                    />     
                </div>
            </form>
        </>
        );
    }
}

export default (connect(null)(ReAssignForm));