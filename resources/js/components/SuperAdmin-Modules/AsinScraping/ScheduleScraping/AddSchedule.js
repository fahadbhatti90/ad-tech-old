import React, {Component} from 'react'
import clsx from 'clsx';
import './../Collection.scss';
import {connect} from "react-redux"
import TextButton from "./../../../../general-components/TextButton";
import PrimaryButton from "./../../../../general-components/PrimaryButton";
import {ShowSuccessMsg} from "./../../../../general-components/successDailog/actions";
import {ShowFailureMsg} from "./../../../../general-components/failureDailog/actions";
import {withStyles} from "@material-ui/core/styles";
import {primaryColor, primaryColorLight} from "./../../../../app-resources/theme-overrides/global";
import TextFieldInput from "./../../../../general-components/Textfield";
import LinearProgress from '@material-ui/core/LinearProgress';
import RadioGroup from '@material-ui/core/RadioGroup';
import FormControlLabel from '@material-ui/core/FormControlLabel';
import FormControl from '@material-ui/core/FormControl';
import SingleSelect from "./../../../../general-components/Select";
import FormLabel from '@material-ui/core/FormLabel';
import ThemeRadioButtons from './../../../../general-components/AsinCollectionRadioButton';
import Tooltip from "@material-ui/core/Tooltip";
import { addAsinSchedule } from './apiCalls';
import * as Yup from 'yup';
import {
    objectRequiredValidationHelper,
    stringMaxLengthValidationHelper,
} from './../../../../helper/yupHelpers';


const customStyle = {
    menu: base => ({
        ...base,
        marginTop: 0
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
const ValidateSchemaObject = {
    collection: objectRequiredValidationHelper("Collection Name"),
    duration: objectRequiredValidationHelper("Duration"),
};

const validationSchema = Yup.object().shape(ValidateSchemaObject);
const durations = [
    {label: "Daily", value: "0000-00-00", className: 'custom-class'},
    {label: "1 week", value: "1w", className: 'custom-class'},
    {label: "2 week", value: "2w", className: 'custom-class'},
    {label: "3 week", value: "3w", className: 'custom-class'},
    {label: "1 month", value: "1m", className: 'custom-class'},
];
class AddSchedule extends Component {
    constructor(props){
        super(props);
        this.state = {
            collection: null,
            duration: null,
            status: "stop",
            options: {
                collections: [],
                durations: [],
            },
            form:{
                isFormLoading: false,
                loadingText: "Loading..."
            },
            errors: {
                collection: "",
                duration: "",
            },
        }//end state
    }
    componentDidMount(){
        let collectionOptions = this.props.collections.map((collection) => {
            return {label: collection.name + "_" + collection.id, value: collection.id, className: 'custom-class'}
        })
        this.setState((prevState)=>({
            options: {
                ...prevState.options,
                collections: collectionOptions,
                durations,
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
        }, () => {
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
                duration: this.state.duration,
                collection: this.state.collection,
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
                DatatoUpload.append("cronduration",  this.state.duration.value);
                DatatoUpload.append("cronstatus",  this.state.status);
                DatatoUpload.append("collectionId", this.state.collection.value);
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
        addAsinSchedule(
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
    setUploadedFile = (collection) => {
        this.setState(prevState => ({
            errors: {
                ...prevState.errors,
                collection: ""
            },
            collection
        }));
    }
    handleOnStatusChange = (e) => {
        let val = e.target.value;
        this.setState((prevState) => ({
            status: val,
        }));
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
                <div className={clsx("scheduleCollection",this.state.errors.collection.length > 0 ? "errorCustom" : "")}>
                    <label className="text-xs font-normal ml-2">
                        Collections <span className="font-black text-red-500 text-sm">*</span>
                    </label>
                    <SingleSelect
                        placeholder="Select Collection"
                        name={"collection"}
                        value={this.state.collection}
                        onChangeHandler={this.onChangeHandler}
                        formatOptionLabel={this.formatOptionLabel}
                        fullWidth={true}
                        Options={this.state.options.collections}
                        styles={customStyle}
                        customClassName="ThemeSelect"
                        id="collection"
                    />
                    <div className="error pl-3">{this.state.errors.collection}</div>
                </div>
                <div className={clsx("scheduleDuration  pt-5", this.state.errors.duration.length > 0 ? "errorCustom" : "")}>
                    <label className="text-xs font-normal ml-2">
                        Duration <span className="font-black text-red-500 text-sm">*</span>
                    </label>
                    <SingleSelect
                        placeholder="Select Duration"
                        name={"duration"}
                        value={this.state.duration}
                        onChangeHandler={this.onChangeHandler}
                        formatOptionLabel={this.formatOptionLabel}
                        fullWidth={true}
                        Options={this.state.options.durations}
                        styles={customStyle}
                        customClassName="ThemeSelect"
                        id="duration"
                    />
                    <div className="error pl-3">{this.state.errors.duration}</div>
                </div>
                
                <div className={clsx("asinsScrapingRadioButtons pt-5")}>
                    <FormControl component="fieldset">
                        <RadioGroup row aria-label="status" name="status" value={this.state.status} onChange={this.handleOnStatusChange}>
                            <FormControlLabel value="stop" control={<ThemeRadioButtons />} label="Stop" />
                            <FormControlLabel value="run" control={<ThemeRadioButtons />} label="Run" />
                        </RadioGroup>
                    </FormControl>
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

export default connect(null)(AddSchedule)
