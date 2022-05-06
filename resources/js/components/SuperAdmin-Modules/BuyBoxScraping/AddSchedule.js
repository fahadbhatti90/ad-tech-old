import React, {Component} from 'react'
import clsx from 'clsx';
import './BuyBox.scss';
import {connect} from "react-redux"
import TextButton from "./../../../general-components/TextButton";
import PrimaryButton from "./../../../general-components/PrimaryButton";
import {ShowSuccessMsg} from "./../../../general-components/successDailog/actions";
import {ShowFailureMsg} from "./../../../general-components/failureDailog/actions";
import {withStyles} from "@material-ui/core/styles";
import {primaryColor, primaryColorLight} from "./../../../app-resources/theme-overrides/global";
import TextFieldInput from "./../../../general-components/Textfield";
import LinearProgress from '@material-ui/core/LinearProgress';
import SingleSelect from "./../../../general-components/Select";
import Tooltip from "@material-ui/core/Tooltip";
import { addSchedule } from './apiCalls';
import * as Yup from 'yup';
import {
    objectRequiredValidationHelper,
    stringRequiredEmailValidationHelper,
    stringMaxLengthAlphaNumericValidationHelper,
} from './../../../helper/yupHelpers';
import UploadFileControl from './../../../general-components/UploadFileControl';


const customStyle = {
    menu: base => ({
        ...base,
        marginTop: 0,
        zIndex: 3
    }),
    control: (base, state) => ({
        background: '#fff',
        height: 35,
        border: "1px solid #c3bdbd8c",
        borderRadius: 12,
        display: 'flex',
        border: state.isFocused ? "2px solid " + primaryColor : "1px solid #c3bdbd8c", //${primaryColor}
        // This line disable the blue border
        boxShadow: state.isFocused ? 0 : 0,
        '&:hover': {
            border: state.isFocused ? "2px solid " + primaryColor : "1px solid " + primaryColorLight
        },
        fontSize: '0.72rem'
    }),
    container: (provided, state) => ({
        ...provided,
        marginTop: 8
    }),
    valueContainer: (provided, state) => ({
        ...provided,
        padding: "0px 8px",
        overflowY: "auto",

    }),
    multiValue: (styles, {data}) => {
        return {
            ...styles,
            borderRadius: 12
        };
    },
    multiValueRemove: (styles, {data}) => ({
        ...styles,
        color: data.color,
        ':hover': {
            backgroundColor: primaryColor,
            color: 'white',
            borderRadius: 12
        },
    }),
}
const useStyles = theme => ({
    root: {
        '& .MuiInputBase-root': {
            marginTop: 8,
            borderRadius: 20,
            border: "1px solid #c3bdbd8c",
            height: 35,
            background: '#fff'
        },
        "&:hover .MuiInputBase-root": {
            borderColor: primaryColorLight,
            borderRadius: "20px",
        },
        '& .MuiInputBase-input': {
            margin: props => props.margin || 15,
            fontSize: '0.72rem',
            padding: '7px 0 7px'
        }
    },
    focused: {
        border: "2px solid !important",
        borderColor: `${primaryColor} !important`,
    }
});

const ValidateSchemaObject = {
    collectionName: stringMaxLengthAlphaNumericValidationHelper("Collection name", 99),
    email: stringRequiredEmailValidationHelper("Email"),
    frequency: objectRequiredValidationHelper("Frequency"),
    duration: objectRequiredValidationHelper("Duration"),
    asins: objectRequiredValidationHelper("ASIN's file"),
};

const validationSchema = Yup.object().shape(ValidateSchemaObject);
const durations = [
    {label: "One time in a day", value: "1", className: 'custom-class'},
];
const frequencies = [
    {label: "1 week", value: "1", className: 'custom-class'},
    {label: "2 week", value: "2", className: 'custom-class'},
    {label: "3 week", value: "3", className: 'custom-class'},
    {label: "Monthly", value: "4", className: 'custom-class'},
];
class AddSchedule extends Component {
    constructor(props){
        super(props);
        this.state = {
            collectionName: "",
            email: "",
            duration: null,
            frequency: null,
            asins: null,
            form:{
                isFormLoading: false,
                loadingText: "Loading..."
            },
            errors: {
                collectionName: "",
                duration: "",
                frequency: "",
                asins: "",
                email:"",
            },
        }//end state
    }
    componentDidMount(){
      
    }
    onChangeHandler = (value, element) => {
        const {errors} = this.state;
        const name = element.name;
        const errorName = $("input[name='" + name + "']").parents(".ThemeSelect").attr("id");
        errors[errorName] = "";
        this.setState({
            [name]: value,
            errors
        });
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
    handleAddScheduleFormSubmit = () => {
        let dataToValidatebject = {
            frequency: this.state.frequency,
            duration: this.state.duration,
            collectionName: this.state.collectionName.trim(),
            asins: this.state.asins,
            email: this.state.email,
        };

        let allValiditionFrom = htk.validateAllFields(validationSchema, dataToValidatebject);
        if (Object.size(allValiditionFrom) > 0 ) {
            let resetErrors = this.helperResetErrors();
            this.helperSetValidationErrorState(resetErrors, allValiditionFrom);
        } else {
            var DatatoUpload = new FormData();
            if( window.FormData === undefined )
            {
                return;
            }
            DatatoUpload.append("frequency",  this.state.frequency.value);
            DatatoUpload.append("c_name_buybox", this.state.collectionName.trim());
            DatatoUpload.append("buybox_email", this.state.email);
            DatatoUpload.append("asinfiles", this.state.asins);
            DatatoUpload.append("duration", this.state.duration.value);
            this.manageEevent(DatatoUpload);
        }
    }
    manageEevent = (ajaxData) => {
        this.setState({
            form:{
                isFormLoading:true,
                loadingText:"Processing..."
            },
        });
        addSchedule(
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
    setUploadedFile = (asins, error)  => {
        this.setState(prevState => ({
            errors: {
                ...prevState.errors,
                asins: error
            },
            asins
        }));
    }
    onInputChangeHandler = (e) => {
        const {errors} = this.state;
        const errorName = e.target.name;
        
        let allValiditionFrom = htk.validateAllFields(validationSchema, {[errorName]: e.target.value.trim()});
        errors[errorName] = Object.size(allValiditionFrom) > 0 ? allValiditionFrom[errorName] : "";

        this.setState({
            [e.target.name]: e.target.value,
            errors
        })
    }
    formatOptionLabel = ({ value, label }) => {
        let labelLimit =  35;
        let option =label.length > labelLimit ? (
          <Tooltip placement="top" title={label} arrow>
              <span>
                  {
                    ( label.substr(0, labelLimit) + "...")
                  }
              </span>
          </Tooltip>
        ) : label;
        return option;
    };
    render() {
        const {classes} = this.props;
        return (
            <>
            <div className="addScheduleForm px-10">
                <div className="graphLoader bg-white absolute h-full overflow-hidden w-full top-0 left-0 z-10" style={this.state.form.isFormLoading?{display:"block"}:{display:"none"}} >
                    <LinearProgress />
                    <div className="absolute flex font-bold font-mono h-full items-center justify-center overflow-hidden text-1rem text-sm w-full z-10">
                        {this.state.form.loadingText}
                    </div>
                </div>
                <div className={clsx("collectionName", this.state.errors.collectionName.length > 0 ? "errorCustom" : "")}>
                    <label className="text-xs font-normal ml-2">
                        Collection Name <span className="font-black text-red-500 text-sm">*</span>
                    </label>
                    <div className="ThemeInput">
                        <TextFieldInput
                            placeholder="Enter Collection Name"
                            type="text"
                            id="collectionName"
                            name={"collectionName"}
                            value={this.state.collectionName}
                            fullWidth={true}
                            onChange={this.onInputChangeHandler}
                            classesstyle = {classes}
                        />
                    </div>
                    <div className="error pl-3">{this.state.errors.collectionName}</div>
                </div>
                <div className={clsx("email pt-5 ", this.state.errors.email.length > 0 ? "errorCustom" : "")}>
                    <label className="text-xs font-normal ml-2">
                        Email Address <span className="font-black text-red-500 text-sm">*</span>
                    </label>
                    <div className="ThemeInput">
                        <TextFieldInput
                            placeholder="Enter email"
                            type="text"
                            id="email"
                            name={"email"}
                            value={this.state.email}
                            fullWidth={true}
                            onChange={this.onInputChangeHandler}
                            classesstyle = {classes}
                        />
                    </div>
                    <div className="error pl-3">{this.state.errors.email}</div>
                </div>
                
                <div className={clsx("scheduleDuration pt-5",this.state.errors.duration.length > 0 ? "errorCustom" : "")}>
                    <label className="text-xs font-normal ml-2">
                        Frequency <span className="font-black text-red-500 text-sm">*</span>
                    </label>
                    <SingleSelect
                        placeholder="Select Frequency"
                        name={"duration"}
                        value={this.state.duration}
                        onChangeHandler={this.onChangeHandler}
                        formatOptionLabel={this.formatOptionLabel}
                        fullWidth={true}
                        Options={durations}
                        styles={customStyle}
                        customClassName="ThemeSelect"
                        id="duration"
                    />
                    <div className="error pl-3">{this.state.errors.duration}</div>
                </div>
                <div className={clsx("scheduleDuration pt-5", this.state.errors.frequency.length > 0 ? "errorCustom" : "")}>
                    <label className="text-xs font-normal ml-2">
                        Duration <span className="font-black text-red-500 text-sm">*</span>
                    </label>
                    <SingleSelect
                        placeholder="Select Duration"
                        name={"frequency"}
                        value={this.state.frequency}
                        onChangeHandler={this.onChangeHandler}
                        formatOptionLabel={this.formatOptionLabel}
                        fullWidth={true}
                        Options={frequencies}
                        styles={customStyle}
                        customClassName="ThemeSelect"
                        id="frequency"
                    />
                    <div className="error pl-3">{this.state.errors.frequency}</div>
                </div>
                <div className={clsx("scheduleAsins pt-5",this.state.errors.asins.length > 0 ? "errorCustom" : "")}>
                    <label className="text-xs font-normal ml-2">
                        Asin File <span className="font-black text-red-500 text-sm">*</span>
                    </label>
                    <UploadFileControl
                        setUploadedFile = {this.setUploadedFile}
                    />
                    <div className="error pl-3">{this.state.errors.asins}</div>
                </div>
                <div className="flex float-right items-center justify-center my-5 mt-10 w-full">
                        <div className="mr-3">
                            <TextButton
                            BtnLabel={"Cancel"}
                            color="primary"
                            onClick={this.props.handleModalClose}/>
                        </div>
                        <PrimaryButton
                        btnlabel={"Save"}
                        variant={"contained"}
                        type="submit"
                        onClick={this.handleAddScheduleFormSubmit}
                        />     
                </div>
            </div>
            </>
        )
    }
}

export default withStyles(useStyles)(connect(null)(AddSchedule))
