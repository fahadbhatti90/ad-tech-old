import React, {Component} from 'react'
import clsx from 'clsx';
import './SearchRank.scss';
import {connect} from "react-redux"
import TextButton from "./../../../../general-components/TextButton";
import PrimaryButton from "./../../../../general-components/PrimaryButton";
import {ShowSuccessMsg} from "./../../../../general-components/successDailog/actions";
import {ShowFailureMsg} from "./../../../../general-components/failureDailog/actions";
import {withStyles} from "@material-ui/core/styles";
import {primaryColor, primaryColorLight} from "./../../../../app-resources/theme-overrides/global";
import TextFieldInput from "./../../../../general-components/Textfield";
import LinearProgress from '@material-ui/core/LinearProgress';
import SingleSelect from "./../../../../general-components/Select";
import Tooltip from "@material-ui/core/Tooltip";
import { addSchedule } from './apiCalls';
import * as Yup from 'yup';
import {
    objectRequiredValidationHelper,
    stringMaxLengthAlphaNumericValidationHelper,
} from './../../../../helper/yupHelpers';
import UploadFileControl from './../../../../general-components/UploadFileControl';


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
        borderRadius: 20,
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
            borderRadius: 20
        };
    },
    multiValueRemove: (styles, {data}) => ({
        ...styles,
        color: data.color,
        ':hover': {
            backgroundColor: primaryColor,
            color: 'white',
            borderRadius: 20
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
    scheduleName: stringMaxLengthAlphaNumericValidationHelper("Schedule name", 99),
    department: objectRequiredValidationHelper("Department name"),
    frequency: objectRequiredValidationHelper("Duration"),
    searchTerms: objectRequiredValidationHelper("Search term file"),
};

const validationSchema = Yup.object().shape(ValidateSchemaObject);
const frequencies = [
    {label: "Daily", value: "1", className: 'custom-class'},
    {label: "1 week", value: "7", className: 'custom-class'},
    {label: "2 week", value: "14", className: 'custom-class'},
    {label: "3 week", value: "21", className: 'custom-class'},
    {label: "1 month", value: "30", className: 'custom-class'},
];
class AddSchedule extends Component {
    constructor(props){
        super(props);
        this.state = {
            scheduleName: "",
            department: null,
            frequency: null,
            searchTerms: null,
            options: {
                departments: [],
                frequencies: [],
            },
            form:{
                isFormLoading: false,
                loadingText: "Loading..."
            },
            errors: {
                scheduleName: "",
                department: "",
                frequency: "",
                searchTerms: "",
            },
        }//end state
    }
    componentDidMount(){
        let departmentOptions = this.props.departments.map((department) => {
            return {label: department.d_name + "_" + department.id, value: department.id, className: 'custom-class'}
        })
        this.setState((prevState)=>({
            options: {
                ...prevState.options,
                departments: departmentOptions,
                frequencies,
            }
        }));
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
            department: this.state.department,
            scheduleName: this.state.scheduleName.trim(),
            searchTerms: this.state.searchTerms,
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
            DatatoUpload.append("crawlName", this.state.scheduleName.trim());
            DatatoUpload.append("searchTerm", this.state.searchTerms);
            DatatoUpload.append("d_id", this.state.department.value);
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
    setUploadedFile = (searchTerms, error)  => {
        this.setState(prevState => ({
            errors: {
                ...prevState.errors,
                searchTerms: error
            },
            searchTerms
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
                <div className={clsx("ScheduleName pt-5 ", this.state.errors.scheduleName.length > 0 ? "errorCustom" : "")}>
                    <label className="text-xs font-normal ml-2">
                        Schedule Name <span className="font-black text-red-500 text-sm">*</span>
                    </label>
                    <div className="ThemeInput">
                        <TextFieldInput
                            placeholder="Enter Schedule Name"
                            type="text"
                            id="scheduleName"
                            name={"scheduleName"}
                            value={this.state.scheduleName}
                            fullWidth={true}
                            onChange={this.onInputChangeHandler}
                            classesstyle = {classes}
                        />
                    </div>
                    <div className="error pl-3">{this.state.errors.scheduleName}</div>
                </div>
                
                <div className={clsx("scheduleDepartments pt-5",this.state.errors.department.length > 0 ? "errorCustom" : "")}>
                    <label className="text-xs font-normal ml-2">
                        Departments <span className="font-black text-red-500 text-sm">*</span>
                    </label>
                    <SingleSelect
                        placeholder="Select Department"
                        name={"department"}
                        value={this.state.department}
                        onChangeHandler={this.onChangeHandler}
                        formatOptionLabel={this.formatOptionLabel}
                        fullWidth={true}
                        Options={this.state.options.departments}
                        styles={customStyle}
                        customClassName="ThemeSelect"
                        id="department"
                    />
                    <div className="error pl-3">{this.state.errors.department}</div>
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
                        Options={this.state.options.frequencies}
                        styles={customStyle}
                        customClassName="ThemeSelect"
                        id="frequency"
                    />
                    <div className="error pl-3">{this.state.errors.frequency}</div>
                </div>
                <div className={clsx("scheduleSearchTerms pt-5",this.state.errors.searchTerms.length > 0 ? "errorCustom" : "")}>
                    <label className="text-xs font-normal ml-2">
                        Search Terms <span className="font-black text-red-500 text-sm">*</span>
                    </label>
                    <UploadFileControl
                        setUploadedFile = {this.setUploadedFile}
                    />
                    <div className="error pl-3">{this.state.errors.searchTerms}</div>
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
