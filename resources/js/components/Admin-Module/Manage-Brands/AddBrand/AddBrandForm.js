import React, { Component } from 'react';
import {Grid} from "@material-ui/core";
import TextFieldInput from "./../../../../general-components/Textfield";
import TextButton from "./../../../../general-components/TextButton";
import MultiSelect from "./../../../../general-components/MultiSelect";
import PrimaryButton from "./../../../../general-components/PrimaryButton";
import {connect} from "react-redux";
import {
    stringRequiredValidationHelper,
    objectRequiredValidationHelper
} from './../../../../helper/yupHelpers';
import {useStyles,customStyle} from "./../../Manage-Users/styles";
import { withStyles } from "@material-ui/core/styles";
import * as Yup from 'yup';
import {addBrandCall,getAssociatedUsers,filterSelectedUsers} from "../apiCalls"; 
import {ShowSuccessMsg} from "./../../../../general-components/successDailog/actions";
import {ShowFailureMsg} from "./../../../../general-components/failureDailog/actions";
import LinearProgress from '@material-ui/core/LinearProgress';


const ValidateSchemaObject = {
    name: stringRequiredValidationHelper("Name"),
    email: stringRequiredValidationHelper("Email").matches(
        /[\w\d\.-]+@[\w\d\.-]+\.[\w\d\.-]+/g,
        "InValid Email"
    ),
    users: objectRequiredValidationHelper("Select Users ")
};


const validationSchema = Yup.object().shape(ValidateSchemaObject);

class AddBrandForm extends Component {
    constructor(props) {
        super(props);
        this.state = {
            clientName:"",
            clientEmail:"",
            selectedUsers:null,
            opType: 1,
            id:null,
            userOptions:[],
            errors:{
                name:"",
                email:"",
                users:""
            },
            isLoading:false,
            isEdit: false,
            isProcessing: false
        }
    }

    componentDidMount(){
        this.getBrandAssociatedUsers();
    }

    getBrandAssociatedUsers=()=>{
        this.setState({
            isLoading:true
        },()=>{
            getAssociatedUsers((userOptions) => {
                this.setState({
                    userOptions,
                    isLoading:this.props.isEdit?true:false
                },()=>{
                    if(this.props.isEdit){
                        filterSelectedUsers(this.props.row.id,(selectedUsers) => {
                            this.setState({
                                selectedUsers,
                                isLoading:false
                            })
                        }, (err) => {
                            //error
                            // this.props.dispatch(showSnackBar());
                        });
                    }
                })
            }, (err) => {
                //error
                // this.props.dispatch(showSnackBar());
            });
        })
    }

    static getDerivedStateFromProps(nextProps, prevState){
        if(nextProps.isEdit && prevState.isEdit == false){
            return {
                clientName:nextProps.row.name,
                clientEmail:nextProps.row.email,
                id: nextProps.row.id,
                isEdit: nextProps.isEdit
            }
        }
        return null;
    }

    tfChangeHandler=(e)=>{
        this.setState({
            [e.target.name]: e.target.value
        })
    }

    onUserChange=(value)=>{
        this.setState({
            selectedUsers: value
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
            name: this.state.clientName.trim(),
            email: this.state.clientEmail.trim(),
            users: this.state.selectedUsers
        };
        let allValiditionFrom = htk.validateAllFields(validationSchema, dataToValidatebject);
        let resetErrors = this.helperResetErrors();
        if (Object.size(allValiditionFrom) > 0) {
            this.helperSetValidationErrorState(resetErrors, allValiditionFrom);
        } else {
            let {clientName,clientEmail,selectedUsers,opType} = this.state;
            if(selectedUsers !=  null){
                selectedUsers = selectedUsers.map(item => {
                    return item.value;
                })
                selectedUsers = selectedUsers.toString()
            } else{
                selectedUsers = ""
            }
            let formData = {
                clientName,
                clientEmail,
                selectedUsers,
                opType
            }
            this.setState({
                isProcessing:true
            })
            addBrandCall(formData,(data) => {
                this.setState({
                    isProcessing:false
                },()=>{
                    if(data.status){
                        this.props.dispatch(ShowSuccessMsg(data.message, "", true, "",this.props.reloadData()));
                    } else{
                        this.props.dispatch(ShowFailureMsg(data.message, "", true, ""));
                    }
                })
            }, (err) => {
                //error
                this.setState({
                    isProcessing:false
                })
                this.props.dispatch(ShowFailureMsg(err.message, "", true, ""));
            });
        }   
    }

    handleUpdate=()=>{
        let dataToValidatebject = {
            name: this.state.clientName.trim(),
            email: this.state.clientEmail.trim(),
            users: this.state.selectedUsers && this.state.selectedUsers.length>0?this.state.selectedUsers:null
        };
        let allValiditionFrom = htk.validateAllFields(validationSchema, dataToValidatebject);
        let resetErrors = this.helperResetErrors();
        if (Object.size(allValiditionFrom) > 0) {
            this.helperSetValidationErrorState(resetErrors, allValiditionFrom);
        } else {
            let {clientName,clientEmail,id,selectedUsers} = this.state;
            if(selectedUsers !=  null){
                selectedUsers = selectedUsers.map(item => {
                    return item.value;
                })
                selectedUsers = selectedUsers.toString()
            } else{
                selectedUsers = ""
            }
            let formData = {
                clientName,
                clientEmail,
                selectedUsers,
                opType:2,
                id
            }
            this.setState({
                isProcessing:true
            })
            addBrandCall(formData,(data) => {
                this.setState({
                    isProcessing:false
                },()=>{
                    if(data.status){
                        this.props.dispatch(ShowSuccessMsg(data.message, "", true, "",this.props.reloadData()));
                    } else{
                        this.props.dispatch(ShowFailureMsg(data.message, "", true, ""));
                    }
                })               
            }, (err) => {
                //error
                this.setState({
                    isProcessing:false
                })
                this.props.dispatch(ShowFailureMsg(err.message, "", true, ""));
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
                <form className="visible">
                    <Grid container spacing={2}>
                        <Grid item xs={12} className={this.state.errors.name.length > 0 ? "errorCustom px-2" : "px-2"}>
                            <label className="text-xs font-normal ml-2">
                                Name <span className="required-asterisk">*</span>
                            </label>
                            <div className="ThemeInput">
                                <TextFieldInput
                                    placeholder="Enter User Name"
                                    id="name"
                                    name="clientName"
                                    type="text"
                                    value={this.state.clientName}
                                    onChange={this.tfChangeHandler}
                                    fullWidth={true}
                                    classesstyle = {classes}
                                />
                                <div className="error pl-3">{this.state.errors.name}</div>
                            </div>
                        </Grid>
                        <Grid item xs={12} className={this.state.errors.email.length > 0 ? "errorCustom px-2" : "px-2"}>
                            <label className="text-xs font-normal ml-2">
                                Email <span className="required-asterisk">*</span>
                            </label>
                            <div className="ThemeInput">
                                <TextFieldInput
                                    placeholder="Enter Email Address"
                                    id="email"
                                    name="clientEmail"
                                    type="text"
                                    value={this.state.clientEmail}
                                    onChange={this.tfChangeHandler}
                                    fullWidth={true}
                                    classesstyle = {classes}
                                />
                                <div className="error pl-3">{this.state.errors.email}</div>
                            </div>
                        </Grid>
                        <Grid item xs={12} className={this.state.errors.users.length > 0 ? "errorCustom px-2" : "px-2"}>
                            <label className="text-xs font-normal ml-2">
                                Associate Users 
                            </label>
                            <div className="ThemeInput">
                            <MultiSelect
                                isDisabled={this.state.disableFilters}
                                placeholder="Choose"
                                id="sc"
                                name="onUserChange" 
                                onMenuOpen={this.props.onMenuOpen} 
                                onMenuClose = {this.props.onMenuClose}     
                                value={this.state.selectedUsers}
                                onChangeHandler = {this.onUserChange}
                                Options={this.state.userOptions}
                                styles={customStyle}
                                isLoading={this.state.isLoading}
                                />
                            </div>
                            <div className="error pl-3">{this.state.errors.users}</div>
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
                        btnlabel={this.props.isEdit?"Update":"Save"}
                        variant={"contained"}
                        onClick={this.props.isEdit?this.handleUpdate:this.handleSubmit}
                    />     
                </div>
                </form>
            </div>
        );
    }
}

export default withStyles(useStyles) (connect(null)(AddBrandForm));