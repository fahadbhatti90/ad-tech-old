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
import FormLabel from '@material-ui/core/FormLabel';
import ThemeRadioButtons from './../../../../general-components/AsinCollectionRadioButton';
import { addAsinCollection } from './../apiCalls';
import * as Yup from 'yup';
import {
    objectRequiredValidationHelper,
    stringMaxLengthAlphaNumericValidationHelper,
} from './../../../../helper/yupHelpers';
import UploadFileControl from './../../../../general-components/UploadFileControl';

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
    collectionFile: objectRequiredValidationHelper("Collection File"),
    collectionName: stringMaxLengthAlphaNumericValidationHelper("Collection Name", 99),
};

const validationSchema = Yup.object().shape(ValidateSchemaObject);

class AddCollection extends Component {
    constructor(props){
        super(props);
        this.state = {
            collectionFile: null,
            collectionName: "",
            collectionType: "d",
            form:{
                isFormLoading: false,
                loadingText: "Loading..."
            },
            errors: {
                collectionFile: "",
                collectionName: "",
            },
        }//end state
    }
    componentDidMount(){
    }
    onChangeHandler = (e) => {
        const {errors} = this.state;
        const errorName = e.target.name;
        
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
                collectionName: this.state.collectionName.trim(),
                collectionFile: this.state.collectionFile,
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
                DatatoUpload.append("collectionName", this.state.collectionName.trim());
                DatatoUpload.append("collectionFile", this.state.collectionFile);
                DatatoUpload.append("collectionType", this.state.collectionType);
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
        addAsinCollection(
        ajaxData,
        (response)=>{
            let cId = response.collection_type;
            this.props.dispatch(ShowSuccessMsg("Successfull", 
            response.message, 
            true, 
            "", 
            ()=> {
                // if(cId)
                // startInstantAsinScraping({ c_id: cId }, (response)=>{ }, (error)=>{ });
                this.props.heloperReloadDataTable(response.tableData);
            }
            ));
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
    setUploadedFile = (collectionFile, error) => {
        this.setState(prevState => ({
            errors: {
                ...prevState.errors,
                collectionFile: error
            },
            collectionFile
        }));
    }
    handleOnCollectionTypeChange = (e) => {
        let val = e.target.value;
        this.setState((prevState) => ({
            collectionType: val,
        }));
    }
    render() {
        const {classes} = this.props;
        return (
            <>
            <div className="asinCollectionFrom px-10">
                <div className="graphLoader bg-white absolute h-full overflow-hidden w-full top-0 left-0 z-10" style={this.state.form.isFormLoading?{display:"block"}:{display:"none"}} >
                    <LinearProgress />
                    <div className="absolute flex font-bold font-mono h-full items-center justify-center overflow-hidden text-1rem text-sm w-full z-10">
                        {this.state.form.loadingText}
                    </div>
                </div>
                <div className={clsx("collectionFile ", this.state.errors.collectionFile.length > 0 ? "errorCustom" : "")}>
                    <label className="text-xs font-normal ml-2 block mb-2">
                        Asin File  <span className="font-black text-red-500 text-sm">*</span>
                    </label>
                    <UploadFileControl setUploadedFile={this.setUploadedFile}/>
                    <div className="error pl-3">{this.state.errors.collectionFile}</div>
                </div>
                <div className={clsx("collectionName pt-5",this.state.errors.collectionName.length > 0 ? "errorCustom" : "")}>
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
                            onChange={this.onChangeHandler}
                            classesstyle = {classes}
                        />
                    </div>
                    <div className="error pl-3">{this.state.errors.collectionName}</div>
                </div>
                <div className={clsx("asinsScrapingRadioButtons pt-5")}>
                    <FormControl component="fieldset">
                        <RadioGroup row aria-label="collectionType" name="collectionType" value={this.state.collectionType} onChange={this.handleOnCollectionTypeChange}>
                            <FormControlLabel value="d" control={<ThemeRadioButtons />} label="Daily" />
                            <FormControlLabel value="i" control={<ThemeRadioButtons />} label="Instant" />
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
                        onClick={this.handleAddEventFormSubmit}
                        />     
                </div>
            </div>
            </>
        )
    }
}

export default withStyles(useStyles)(connect(null)(AddCollection))
