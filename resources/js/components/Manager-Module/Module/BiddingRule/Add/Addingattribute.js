import React, {useState, Component} from "react";
import SingleSelect from "./../../../../../general-components/Select";
import MultiSelect from "./../../../../../general-components/MultiSelect";
import {Grid} from '@material-ui/core';
import TextFieldInput from "./../../../../../general-components/Textfield";
import Radio from '@material-ui/core/Radio';
import RadioGroup from '@material-ui/core/RadioGroup';
import FormControlLabel from '@material-ui/core/FormControlLabel';
import FormControl from '@material-ui/core/FormControl';
import { withStyles } from "@material-ui/core/styles";
import PrimaryButton from "./../../../../../general-components/PrimaryButton";
import Tooltip from "@material-ui/core/Tooltip";
import {primaryColor, primaryColorLight} from "../../../../../app-resources/theme-overrides/global";
import CcEmail from "./../../../../../general-components/EmailCC/EmailChips";
import {connect} from "react-redux"
import {ShowSuccessMsg} from "./../../../../../general-components/successDailog/actions";
import {ShowFailureMsg} from "./../../../../../general-components/failureDailog/actions";
import {styles} from "./../styles";
import {
    stringRequiredValidationHelper, 
    objectRequiredValidationHelper,
    poitiveIntegerValidationHelper,
    numberValidationHelper,
} from './../../../../../helper/yupHelpers';

import * as Yup from 'yup';

import {
    getProfiles,
    getCampaignsPortfolioCall, 
    getPesetRuleValue,
    savePresetRule,
    addBiddingData
} from './ApiCalls'
    const adTypeOptions = [
        {label: "Brand", value: 'sponsoredBrands', className: 'custom-class'},
        {label: "Product", value: 'sponsoredProducts', className: 'awesome-class'},
        {label: "Display", value: 'sponsoredDisplay', className: 'awesome-class'}
    ];
    const portfolioCampaignTypeOptions = [
        {label: "Campaign", value: 'Campaign', className: 'custom-class'},
        {label: "Portfolio", value: 'Portfolio', className: 'awesome-class'}
    ];
    const periodsOptions = [
        {label: "Last 7 days", value: '7d', className: 'custom-class'},
        {label: "Last 14 days", value: '14d', className: 'awesome-class'},
        {label: "Last 21 days", value: '21d', className: 'awesome-class'},
        {label: "Last 1 Month", value: '1m', className: 'awesome-class'},
    ];

    const frequencyOptions = [
        {label: "Once per day", value: 'once_per_day', className: 'custom-class'},
        {label: "Every other day", value: 'every_day', className: 'awesome-class'},
        {label: "Once per week", value: 'w', className: 'awesome-class'},
        {label: "Once per month", value: 'm', className: 'awesome-class'},
    ];
    const metricOptions = [
        {label: "Impression", value: 'impression', className: 'custom-class'},
        {label: "Clicks", value: 'clicks', className: 'awesome-class'},
        {label: "Cost", value: 'cost', className: 'awesome-class'},
        {label: "Revenue", value: 'revenue', className: 'awesome-class'},
        {label: "ROAS", value: 'roas', className: 'custom-class'},
        {label: "ACOS", value: 'acos', className: 'awesome-class'},
        {label: "CPC", value: 'cpc', className: 'awesome-class'},
        {label: "CPA", value: 'cpa', className: 'awesome-class'},
    ];
    const conditionOptions = [
        {label: "Greater than", value: 'greater', className: 'custom-class'},
        {label: "Less than" , value: 'less', className: 'awesome-class'},
    ];
    const thenClauseOptions = [
        {label: "Raise", value: 'raise', className: 'custom-class'},
        {label: "Lower", value: 'lower', className: 'awesome-class'},
    ];
    const customStyle ={
        menu: base => ({
        ...base,
        marginTop: 0
        }),
        control: (base, state) => ({
        background: '#fff',
        height: 30,
        border: "1px solid #c3bdbd8c",
        borderRadius: 20,
        display: 'flex', 
        border: state.isFocused ? "2px solid "+primaryColor : "1px solid #c3bdbd8c", //${primaryColor}
        // This line disable the blue border
        boxShadow: state.isFocused ? 0 : 0,
        '&:hover': {
            border:  state.isFocused ?"2px solid "+primaryColor:"1px solid "+primaryColorLight
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
        multiValue: (styles, { data }) => {
        return {
            ...styles,
            borderRadius:25
        };
        },
        multiValueRemove: (styles, { data }) => ({
        ...styles,
        color: data.color,
        ':hover': {
            backgroundColor: primaryColor,
            color: 'white',
            borderRadius: 25
        },
        }),
    }
    
const useStyles = theme => ({
    root: {
      '& .MuiInputBase-root':{
        marginTop: 8,        
        borderRadius: 20,
        border: "1px solid #c3bdbd8c",
        height: 30,
        background: '#fff'
      },
      "&:hover .MuiInputBase-root": {
        borderColor: primaryColorLight,
        borderRadius: "25px",
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
const Options = [
    {label: "Campaign Value Selection", value: 1, className: 'custom-class'},
    {label: "Profile Value Selection", value: 2, className: 'awesome-class'}
    // more options...
];
const ThemeRadio = withStyles({
    root: {
      color: primaryColor,
      '&$checked': {
        color: primaryColor,
      },
    },
    checked: {},
  })((props) => <Radio color="default" {...props} />);
const ValidateSchemaObject = {
    Bidby: poitiveIntegerValidationHelper("Bid By"),
    Rulename: stringRequiredValidationHelper("Rule name").matches(
        /^[a-zA-Z0-9 ]+$/,
        "Only Alpha Numeric allowed"
    ),
    Profile: objectRequiredValidationHelper("Profile"),
    Preset: objectRequiredValidationHelper("Preset"),
    AdType: objectRequiredValidationHelper("Ad-Type"),
    PortfolioCampaignType: objectRequiredValidationHelper("Portfolio Campaign Type"),
    Campaign: objectRequiredValidationHelper("Pf Campaign"),
    Periods: objectRequiredValidationHelper("Periods"),
    ConditionValue: objectRequiredValidationHelper("Condition Value"),
    ConditionValue1: objectRequiredValidationHelper("Condition Value"),
    MetricValue: objectRequiredValidationHelper("Metric Value"),
    MetricValue1: objectRequiredValidationHelper("Metric Value"),
    firstValue: numberValidationHelper("First Value"),
    secondValue: numberValidationHelper("Second Value"),
    Frequency: objectRequiredValidationHelper("Frequency"),
    ThenClause: objectRequiredValidationHelper("Then clasue"),
};

const validationSchema = Yup.object().shape(ValidateSchemaObject);


class Addingattribute extends Component {
    
    defaultState = {};
    constructor(props){
        super(props);
        this.state = {
            inputList: [{selectedMetric: "", selectedCondition: "", integerValues: ""}],
            profileOptions: [],
            adTypeOptions: adTypeOptions,
            portfolioCampaignTypeOptions: portfolioCampaignTypeOptions,
            campaignsOptions: [],
            presetOptions: [],
            metricOptions: metricOptions,
            periodsOptions: periodsOptions,
            frequencyOptions: frequencyOptions,
            conditionOptions: conditionOptions,
            thenClauseOptions: thenClauseOptions,
            selectedRulename: "",
            selectedRulenameError: false,
            selectedProfile: null,
            selectedAdType: null,
            selectedPortfolioCampaignType: null,
            selectedCampaign: null,
            selectedPreset: null,
            selectedPeriods: null,
            selectedFrequency: null,
            selectedMetric: [],
            selectedCondition: [],
            selectedIntegerValues: [],
            statement:{
                selectedConditionValue: null,
                selectedMetricValue: null,
                selectedInteger: "",
                selectedConditionValue1: null,
                selectedMetricValue1: null,
                selectedInteger1: "",
            },
            andOr: "",
            selectedThenClause: null,
            selectedBidby: "",
            ccEmails: [],
            errors: {
                Rulename:"",
                Profile:"",
                AdType:"",
                PortfolioCampaignType:"",
                Campaign:"",
                Preset:"",
                Periods:"",
                Frequency:"",
                ThenClause:"",
                Bidby:"",
                ConditionValue: "",
                MetricValue: "",
                firstValue: "",
                ConditionValue1: "",
                MetricValue1: "",
                secondValue: "",
            },
            loaders:{
                presetLoaders:false,
            },
            reset:false,
            model:{
                showModel:false,
                modelTitle:"",
            }
        }
        this.defaultState = this.state;
    }
    componentDidMount = () => {
        getProfiles(({profileOptions, presetOptions}) => {
            this.setState({
                profileOptions,
                presetOptions
            })
        }, (err) => {
        });
    }
    handleRemoveClick = index => {
        const list = [...this.state.inputList];
        list.splice(index, 1);
        this.setState({
            andOr:"",
            inputList:list
        })
        // setInputList(list);
    };
    handleAddClick = () => {
        this.setState((prevState)=>{
            return {
                andOr:"and",
                inputList:[
                    ...prevState.inputList,
                    { selectedMetric: "",selectedCondition: "", integerValues: "" }
                ]
            }
        });
    };
    restState = ()=>{
        this.defaultState.profileOptions = this.state.profileOptions;
        this.defaultState.adTypeOptions = this.state.adTypeOptions;
        this.defaultState.portfolioCampaignTypeOptions = this.state.portfolioCampaignTypeOptions;
        this.defaultState.presetOptions = this.state.presetOptions;
        this.defaultState.reset = true; 
        this.defaultState.ccEmails = []; 
        this.setState(this.defaultState,()=>{
            this.setState({
                reset: false 
            })
        })
    }
    getCampaigns = () => {
        
        let profile = this.state.selectedProfile;
        let adType = this.state.selectedAdType;
        let portfolioCampaign = this.state.selectedPortfolioCampaignType;
        if (profile != null && adType != null && portfolioCampaign != null) {
            this.props.handleProgressBar(true);
            getCampaignsPortfolioCall(profile.value, adType.value, portfolioCampaign.value, (campaignsOptions) => {
                //success
                this.setState({
                    selectedCampaign:null,
                    campaignsOptions,
                    showFilterLoader: false
                })
                this.props.handleProgressBar(false);
            }, (err) => {
                //error
                this.props.handleProgressBar(false);
                alert(err);
            });
        } else {
            this.setState({
                showFilterLoader: false
            })
        }
    }
    getPreset = () => {
        let selectedPreset = this.state.selectedPreset;
        if (selectedPreset != null ) {
            this.props.handleProgressBar(true);
            getPesetRuleValue(selectedPreset, (response) => {
                //success
                let inputList = [];
                if(response.andOr == "NA"){
                    inputList.push({ selectedMetric: "",selectedCondition: "", integerValues: "" }) 
                }
                else{
                    inputList.push({ selectedMetric: "",selectedCondition: "", integerValues: "" }) 
                    inputList.push({ selectedMetric: "",selectedCondition: "", integerValues: "" }) 
                }
                console.log(inputList)
                this.setState((prevState)=>({
                    // selectedRulename: response.presetName,
                    inputList,
                    selectedPeriods: periodsOptions.filter(row =>{ return row.value == response.lookBackPeriod })[0],
                    selectedFrequency: frequencyOptions.filter(row =>{ return row.value == response.frequency})[0],
                    selectedMetric: response.metric.split(","),
                    selectedCondition: response.condition.split(","),
                    selectedIntegerValues: response.integerValues.split(","),
                    statement:{
                        selectedMetricValue: metricOptions.filter(row =>{ return row.value == response.metric.split(",")[0] })[0],
                        selectedConditionValue: conditionOptions.filter(row =>{ return row.value == response.condition.split(",")[0] })[0],
                        selectedInteger: response.integerValues.split(",")[0],
                        selectedMetricValue1:  response.andOr != "NA" ? metricOptions.filter(row =>{ return row.value == response.metric.split(",")[1] })[0]:"",
                        selectedConditionValue1: response.andOr != "NA" ? conditionOptions.filter(row =>{ return row.value == response.condition.split(",")[1] })[0]:"",
                        selectedInteger1: response.andOr != "NA" ? response.integerValues.split(",")[1] : "",
                    },
                    errors: {
                        ...prevState.errors,
                        Rulename:"",
                        Preset:"",
                        Periods:"",
                        Frequency:"",
                        ThenClause:"",
                        Bidby:"",
                        ConditionValue: "",
                        MetricValue: "",
                        firstValue: "",
                        ConditionValue1: "",
                        MetricValue1: "",
                        secondValue: "",
                    },
                    loaders:{
                        ...prevState.loaders,
                        presetLoaders:!this.state.loaders.presetLoaders
                    },
                    andOr: response.andOr == "NA" ? "" : response.andOr,
                    selectedThenClause: thenClauseOptions.filter(row =>{ return row.value == response.thenClause})[0],
                    selectedBidby: response.bidBy,
                }));
                this.props.handleProgressBar(false);
            }, (err) => {
                //error
                this.props.handleProgressBar(false);
                alert(err);
            });
        } else {
            this.setState({
                showFilterLoader: false
            })
        }
    }
    onMultiSelectChangeHandler = (value) => {
        const {errors} = this.state;
        errors["Campaign"] = "";
        this.setState({
            errors,
            selectedCampaign: value
        });
    }
    onInputChangeHandler = (e) =>{
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
    onChangeHandler = (value,element) => {
        const {errors} = this.state;
        const name = element.name;
        const errorName = $("input[name='"+name+"']").parents(".ThemeSelect").attr("id");
        errors[errorName] = "";
        this.setState({
            [name]: value,
            errors
        },()=>{
            if( (name == "selectedProfile" ||
                name == "selectedAdType" ||
                name == "selectedPortfolioCampaignType" ) &&
                this.state.selectedProfile &&
                this.state.selectedAdType &&
                this.state.selectedPortfolioCampaignType){
                    this.setState({
                        campaignsOptions:[],
                        showFilterLoader:!this.state.showFilterLoader
                    })
                    this.getCampaigns();
                }//endif
            if(name == "selectedPreset" && this.state.selectedPreset){
                this.setState((prevState)=>({
                    loaders:{
                        ...prevState.loaders,
                        presetLoaders:!this.state.loaders.presetLoaders
                    }
                }));
                this.getPreset();
            }
            if(name == "selectedPreset" && this.state.selectedPreset == null)
            {
                this.setState((prevState)=>({
                    selectedPeriods: null,
                    selectedFrequency: null,
                    selectedMetric: [],
                    selectedCondition: [],
                    selectedIntegerValues: [],
                    selectedThenClause: null,
                    selectedBidby: "",
                    statement:{
                        selectedMetricValue: null,
                        selectedConditionValue: null,
                        selectedInteger: "",
                        selectedMetricValue1: null,
                        selectedConditionValue1: null ,
                        selectedInteger1: "",
                    },
                    inputList: [{selectedMetric: "", selectedCondition: "", integerValues: ""}],
                    andOr:"",
                }));
            }
        });
    }
    onMetricSelectChange = (value,e) => {
        const {errors} = this.state;
        let index = parseInt(e.name.split("-")[1]);
        const name = e.name;
        const errorName = $("input[name='"+name+"']").parents(".ThemeSelect").attr("id");
        errors[errorName] = "";
        this.setState((prevState)=>{
            return {
                statement:{
                    ...prevState.statement,
                    [(index == 0 ? "selectedMetricValue":"selectedMetricValue1")] : (value)
                },
                errors,
                selectedMetric :(value ?this.helperGetArrayData(this.state.selectedMetric, e.name, value.value): [])
            }
        });
    }
    onConditionSelectChange = (value,e) => {
        let index = parseInt(e.name.split("-")[1]);
        const {errors} = this.state;
        const name = e.name;
        const errorName = $("input[name='"+name+"']").parents(".ThemeSelect").attr("id");
        errors[errorName] = "";
            this.setState((prevState)=>{
                return {
                    statement:{
                        ...prevState.statement,
                        [(index == 0 ? "selectedConditionValue" : "selectedConditionValue1")] :  value 

                    },
                    errors,
                    selectedCondition: value ? this.helperGetArrayData(this.state.selectedCondition, e.name, value.value) : [],
                }
            });
    }
    onIntegerSelectChange = (e) => {
            let index = parseInt(e.target.name.split("-")[1]);
            let val = e.target.value;
            if(val.length > 0 && !(/^\d+(?:\.\d{1,2})?$/.test(val))){
                return;
            }
            let name = e.target.name;
            const {errors} = this.state;
            const errorName = $("input[name='"+name+"']").attr("id");
            
            let allValiditionFrom = htk.validateAllFields(validationSchema, {[errorName]: val});
            errors[errorName] = Object.size(allValiditionFrom) > 0 ? allValiditionFrom[errorName] : "";
            this.setState((prevState)=>{
                return {
                    statement:{
                        ...prevState.statement,
                        [(index == 0 ? "selectedInteger":"selectedInteger1")] : val

                    },
                    errors,
                    selectedIntegerValues: this.helperGetArrayData(this.state.selectedIntegerValues, name, val),
                }
            });
    }
    helperGetArrayData = (selectedArray,name,value)=>{
        let selectedMatrix = name;
        let index = parseInt(selectedMatrix.split("-")[1]);
        selectedArray[index] = value;
        return selectedArray;
    }
    getUpdatedItems = (e) => {
        this.setState({
            ccEmails: e
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
    onSubmit = (e) => {
        e.preventDefault();
        
        let dataToValidatebject = {
            Rulename: this.state.selectedRulename.trim(),
            Profile: this.state.selectedProfile,
            AdType: this.state.selectedAdType,
            PortfolioCampaignType: this.state.selectedPortfolioCampaignType,
            Campaign: this.state.selectedCampaign,
            Periods: this.state.selectedPeriods,
            Frequency: this.state.selectedFrequency,
            ConditionValue: this.state.statement.selectedConditionValue,
            MetricValue: this.state.statement.selectedMetricValue,
            firstValue: this.state.statement.selectedInteger,
            ThenClause: this.state.selectedThenClause,
            Bidby: this.state.selectedBidby,
        };
        if(this.state.andOr != ""){
            dataToValidatebject.ConditionValue1= this.state.statement.selectedConditionValue1;
            dataToValidatebject.MetricValue1= this.state.statement.selectedMetricValue1;
            dataToValidatebject.secondValue= this.state.statement.selectedInteger1;
        }
        let allValiditionFrom = htk.validateAllFields(validationSchema, dataToValidatebject);
        console.log(allValiditionFrom);
        if (Object.size(allValiditionFrom) > 0) {
            let resetErrors = this.helperResetErrors();
            this.helperSetValidationErrorState(resetErrors, allValiditionFrom);
        } else {
            
            this.props.handleProgressBar(true);
            let params = {
                'formType': 'add',
                'bidBy':this.state.selectedBidby,
                'andOr': this.state.andOr,
                'ruleName': this.state.selectedRulename,
                'profileFkId': this.state.selectedProfile.value,
                'fKPreSetRule':  this.state.selectedPreset ? this.state.selectedPreset.value : this.state.selectedPreset,
                'ccEmails': this.state.ccEmails,
                'sponsoredType': this.state.selectedAdType.value,
                'selectedPreset':  this.state.selectedPreset ? this.state.selectedPreset.value : this.state.selectedPreset,
                'type': this.state.selectedPortfolioCampaignType.value,
                'pfCampaigns': this.state.selectedCampaign.map(campaign=>{return campaign.value;}),
                'lookBackPeriod': this.state.selectedPeriods.value,
                'metric': this.state.selectedMetric,
                'integerValues': this.state.selectedIntegerValues,
                'frequency': this.state.selectedFrequency.value,
                'condition': this.state.selectedCondition,
                'thenClause': this.state.selectedThenClause.value,
            }
            addBiddingData(params, (response) => {
                // alert(message)
                this.props.dispatch(ShowSuccessMsg(response.message, "Successfully", true, "",this.props.handleModalClose(response.tableData)));
                this.restState();
                this.props.handleProgressBar(false);
            },
                (err) => {
                this.props.handleProgressBar(false);
                // alert(err);
                this.props.dispatch(ShowFailureMsg(err, "", true, ""));
                
            });
        }
    }
    onSaveAsRule = (e)=>{
        let dataToValidatebject = {
            Rulename: this.state.selectedRulename.trim(),
            Periods: this.state.selectedPeriods,
            Frequency: this.state.selectedFrequency,
            ConditionValue: this.state.statement.selectedConditionValue,
            MetricValue: this.state.statement.selectedMetricValue,
            firstValue: this.state.statement.selectedInteger,
            ThenClause: this.state.selectedThenClause,
            Bidby: this.state.selectedBidby,
        };
        if(this.state.andOr != ""){
            dataToValidatebject.ConditionValue1= this.state.statement.selectedConditionValue1;
            dataToValidatebject.MetricValue1= this.state.statement.selectedMetricValue1;
            dataToValidatebject.secondValue= this.state.statement.selectedInteger1;
        }
        let allValiditionFrom = htk.validateAllFields(validationSchema, dataToValidatebject);
        if (Object.size(allValiditionFrom) > 0) {
            let resetErrors = this.helperResetErrors();
            this.helperSetValidationErrorState(resetErrors, allValiditionFrom);
        }
        else
        {
            this.props.handleProgressBar(true);
            let params = {
                'bidBy':this.state.selectedBidby,
                'andOr': this.state.andOr,
                'ruleName': this.state.selectedRulename,
                'lookBackPeriod': this.state.selectedPeriods.value,
                'metric': this.state.selectedMetric,
                'integerValues': this.state.selectedIntegerValues,
                'frequency': this.state.selectedFrequency.value,
                'condition': this.state.selectedCondition,
                'thenClause': this.state.selectedThenClause.value,
            }
            savePresetRule(params, (message,presetOptions) => {
                this.props.handleProgressBar(false);
                this.setState({
                    presetOptions,
                    statement:{
                        selectedConditionValue: null,
                        selectedMetricValue: null,
                        selectedInteger: "",
                        selectedConditionValue1: null,
                        selectedMetricValue1: null,
                        selectedInteger1: "",
                    },
                    selectedMetric:[],
                    selectedCondition:[],
                    selectedIntegerValues:[],
                    selectedPeriods:null,
                    selectedThenClause:null,
                    selectedBidby:"",
                    andOr:"",
                })
                this.props.dispatch(ShowSuccessMsg(message, "Successfully", true, ""));
            },
                (err) => {
                //error
                this.props.handleProgressBar(false);
                this.props.dispatch(ShowFailureMsg(err, "Failed", true, ""));
            });
        }
    }
    formatOptionLabel = ({ value, label }) => {
        // let labelLimit = this.props.labelLimit ? this.props.labelLimit  : 25;
        let labelLimit =  50;
        return (
          <Tooltip placement="top" title={label} arrow>
              <span>
                  {
                    (label.length > labelLimit ? label.substr(0, labelLimit) + "..." : label)
                  }
              </span>
          </Tooltip>
        )
    };
    render(){
        const {classes} = this.props;
        return (
            <div className="pt-8 mb-12">
                <form>
                    <Grid container spacing={1}>
                        <Grid container spacing={1} alignItems="flex-start" className="px-2 py-0 rounded-lg">
                            <Grid item xs={12} sm={6} md={6} lg={4} className={this.state.errors.Rulename.length > 0 ? "errorCustom" : ""}>
                                    <label className="text-xs font-normal ml-2">
                                        Rule Name <span className="font-black text-red-500 text-sm">*</span>
                                    </label>
                                    <div className="ThemeInput mr-5">
                                        <TextFieldInput
                                            placeholder="Enter Rule"
                                            type="text"
                                            id="Rulename"
                                            name={"selectedRulename"}
                                            value={this.state.selectedRulename}
                                            onChange={this.onInputChangeHandler}
                                            fullWidth={true}
                                            classesstyle = {classes}
                                        />
                                        <div className="error pl-3">{this.state.errors.Rulename}</div>
                                    </div>
                            </Grid>
                            <Grid item xs={12} sm={6} md={6} lg={4} className={this.state.errors.Profile.length>0 ? "errorCustom" : ""}>
                                    <label className="text-xs font-normal ml-2">
                                        Select Child Brand  <span className="font-black text-red-500 text-sm">*</span>
                                    </label>
                                    <SingleSelect
                                        placeholder="Child Brand"
                                        name={"selectedProfile"}
                                        value={this.state.selectedProfile}
                                        onChangeHandler={this.onChangeHandler}
                                        fullWidth={true}
                                        Options={this.state.profileOptions}
                                        styles={customStyle}
                                        customClassName="mr-5 ThemeSelect"
                                        id="Profile"
                                    />
                                    <div className="error pl-3">{this.state.errors.Profile}</div>
                            </Grid>
                            <Grid item xs={12} sm={6} md={6} lg={4} className={this.state.errors.AdType.length>0 ? "errorCustom" : ""}>
                                <label className="text-xs font-normal ml-2">
                                    Select AdType  <span className="font-black text-red-500 text-sm">*</span>
                                </label>
                                <SingleSelect
                                    placeholder="AdType"
                                    id="AdType"
                                    name={"selectedAdType"}
                                    value={this.state.selectedAdType}
                                    onChangeHandler={this.onChangeHandler}
                                    Options={this.state.adTypeOptions}
                                    styles={customStyle}
                                    customClassName="mr-5 ThemeSelect"
                                />
                                <div className="error pl-3">{this.state.errors.AdType}</div>
                            </Grid>
                            <Grid item xs={12} sm={6} md={6} lg={6} className={this.state.errors.PortfolioCampaignType.length>0 ? "errorCustom" : ""}>
                                <label className="text-xs font-normal ml-2">
                                Select Type <span className="font-black text-red-500 text-sm">*</span>
                                </label>
                                <SingleSelect
                                    placeholder="Campaigns / Portfolio"
                                    id="PortfolioCampaignType"
                                    name={"selectedPortfolioCampaignType"}
                                    value={this.state.selectedPortfolioCampaignType}
                                    onChangeHandler={this.onChangeHandler}
                                    Options={this.state.portfolioCampaignTypeOptions}
                                    styles={customStyle}
                                    customClassName="mr-5 ThemeSelect"
                                />
                                <div className="error pl-3">{this.state.errors.PortfolioCampaignType}</div>
                            </Grid>
                            <Grid item xs={12} sm={6} md={6} lg={6} className={this.state.errors.Campaign.length>0 ? "errorCustom" : ""}>
                                <label className="text-xs font-normal ml-2">
                                Campaigns / Portfolios  <span className="font-black text-red-500 text-sm">*</span>
                                </label>
                                <MultiSelect
                                    placeholder="Select Campaigns / Portfolios"
                                    name="selectedCampaign"
                                    id="Campaign"
                                    value={this.state.selectedCampaign}
                                    onChangeHandler = {this.onMultiSelectChangeHandler}
                                    fullWidth={true}
                                    Options={this.state.campaignsOptions}
                                    styles={customStyle}
                                    customClassName="mr-5 ThemeSelect"
                                    formatOptionLabel={this.formatOptionLabel}
                                    isLoading={this.state.showFilterLoader}
                                />
                                <div className="error pl-3">{this.state.errors.Campaign}</div>
                            </Grid>
                        </Grid>
                        <Grid container spacing={1} alignItems="flex-start" className="mbi-5 mti-5 bg-gray-100 p-3 rounded-lg">
                            <Grid item xs={12} sm={6} md={4} lg={4} className={this.state.errors.Preset.length>0 ? "errorCustom" : ""}>
                                <div className="sm:mb-0 mb-5">
                                    <label className="text-xs font-normal ml-2">
                                        Pre-set Rules
                                    </label>
                                    <SingleSelect
                                        placeholder="Select Pre-set Rules"
                                        name={"selectedPreset"}
                                        id="Preset"
                                        value={this.state.selectedPreset}
                                        onChangeHandler={this.onChangeHandler}
                                        Options={this.state.presetOptions}
                                        styles={customStyle}
                                        customClassName="mr-5 ThemeSelect"
                                    />
                                    <div className="error pl-3">{this.state.errors.Preset}</div>
                                </div>
                            </Grid>
                            <Grid item xs={12} sm={6} md={4} lg={4} className={this.state.errors.Periods.length>0 ? "errorCustom" : ""}>
                                <div className="sm:mb-0 mb-5">
                                    <label className="text-xs font-normal ml-2">
                                        Look Back Periods  <span className="font-black text-red-500 text-sm">*</span>
                                    </label>
                                    <SingleSelect
                                        placeholder="Select Periods"
                                        id="Periods"
                                        name={"selectedPeriods"}
                                        closeMenuOnSelect={true}
                                        value={this.state.selectedPeriods}
                                        onChangeHandler={this.onChangeHandler}
                                        Options={this.state.periodsOptions}
                                        styles={customStyle}
                                        isLoading={this.state.loaders.presetLoaders}
                                        customClassName="mr-5 ThemeSelect"
                                    />
                                    <div className="error pl-3">{this.state.errors.Periods}</div>
                                </div>
                            </Grid>
                            <Grid item xs={12} sm={12} md={4} lg={4} className={this.state.errors.Frequency.length>0 ? "errorCustom" : ""}>
                                <div className="sm:mb-0 mb-5">
                                    <label className="text-xs font-normal ml-2">
                                        Frequency  <span className="font-black text-red-500 text-sm">*</span>
                                    </label>
                                    <SingleSelect
                                        placeholder="Select Frequency"
                                        id="Frequency"
                                        name={"selectedFrequency"}
                                        closeMenuOnSelect={true}
                                        value={this.state.selectedFrequency}
                                        onChangeHandler={this.onChangeHandler}
                                        Options={this.state.frequencyOptions}
                                        styles={customStyle}
                                        className="mr-5 ThemeSelect" 
                                        isLoading={this.state.loaders.presetLoaders}
                                    />
                                    <div className="error pl-3">{this.state.errors.Frequency}</div>
                                </div>
                            </Grid>
                            <Grid item xs={12} sm={12} md={12} lg={12} className="mti-5" >
                            </Grid>
                            {this.state.inputList.map((x, i) => {
                                return (
                                    <>
                                        {i !== 0 &&
                                        <Grid  item xs={12} sm={12} md={12} lg={12} className="flex items-center andOrGrid">
                                            <label className="text-xs font-semibold ">
                                                Select
                                            </label>
                                            <FormControl component="div">
                                                <RadioGroup aria-label="andOr1" className="andOr" name="andOr" value={this.state.andOr} onChange={this.onInputChangeHandler}>
                                                    <FormControlLabel value="and" control={<ThemeRadio size="small" />} label="AND" labelPlacement="start"/>
                                                    <FormControlLabel value="or" control={<ThemeRadio size="small" />} label="OR" labelPlacement="start"/>
                                                </RadioGroup>
                                            </FormControl>
                                        </Grid>
                                        }
                                        <Grid  item xs={12} sm={6} md={3} lg={4}  className={i==0 ? this.state.errors.MetricValue.length>0 ? "errorCustom" : "":this.state.errors.MetricValue1.length>0 ? "errorCustom" : ""}>
                                            {/* Statement */}
                                            <div className="flex condition">
                                                <label className="flex items-center justify-center w-2/12 text-xs font-normal conditionLabel">if</label>
                                                <SingleSelect
                                                    placeholder="Select"
                                                    id={i==0 ? "MetricValue" : "MetricValue1"}
                                                    name={"selectedMetric-"+i}
                                                    closeMenuOnSelect={true}
                                                    isLoading={this.state.loaders.presetLoaders}
                                                    value={i==0 ? this.state.statement.selectedMetricValue : this.state.statement.selectedMetricValue1}
                                                    onChangeHandler={this.onMetricSelectChange}
                                                    Options={this.state.metricOptions}
                                                    styles={customStyle}
                                                    customClassName="w-10/12 mr-5 ThemeSelect"
                                                />
                                            </div>
                                            <div className="error pl-3">{i==0 ? this.state.errors.MetricValue : this.state.errors.MetricValue1}</div>
                                        </Grid>
                                        <Grid item xs={12} sm={6} md={3} lg={4}  className={i==0 ? this.state.errors.ConditionValue.length>0 ? "errorCustom" : "":this.state.errors.ConditionValue1.length>0 ? "errorCustom" : ""}>
                                            <div className="flex condition">
                                                    <label className="flex items-center justify-center w-2/12 text-xs font-normal conditionLabel"> is</label>
                                                <SingleSelect
                                                    placeholder="Select"
                                                    id={i==0 ? "ConditionValue" : "ConditionValue1"}
                                                    name={"selectedCondition-"+i}
                                                    closeMenuOnSelect={true}
                                                    value={i==0 ? this.state.statement.selectedConditionValue : this.state.statement.selectedConditionValue1}
                                                    onChangeHandler={this.onConditionSelectChange}
                                                    Options={this.state.conditionOptions}
                                                    styles={customStyle}
                                                    isLoading={this.state.loaders.presetLoaders}
                                                    customClassName="w-10/12 mr-5 ThemeSelect"
                                                />
                                            </div>
                                            <div className="error pl-3">{i==0 ? this.state.errors.ConditionValue : this.state.errors.ConditionValue1}</div>
                                        </Grid>
                                        <Grid item xs={12} sm={12} md={4} lg={3} className={i==0 ? this.state.errors.firstValue.length>0 ? "errorCustom" : "":this.state.errors.secondValue.length>0 ? "errorCustom" : ""}>
                                            <div className="flex mt-0 condition">
                                                <label className="flex items-center justify-center w-3/12 text-xs font-normal conditionLabel">Enter</label>
                                                <div className="ThemeInput w-10/12 mr-5">
                                                    <TextFieldInput
                                                        name={"integerValue-"+i}
                                                        id={i==0 ? "firstValue" : "secondValue"}
                                                        placeholder="value"
                                                        fullWidth={true}
                                                        type="number"
                                                        value={i == 0 ? this.state.statement.selectedInteger : this.state.statement.selectedInteger1}
                                                        onChange={this.onIntegerSelectChange}
                                                        classesstyle = {classes}
                                                    />
                                                </div>

                                            </div>
                                            <div className="error pl-3">{i == 0 ? this.state.errors.firstValue : this.state.errors.secondValue}</div>
                                        </Grid>
                                        <Grid item xs={12} sm={12} md={2} lg={1}  className="sm:text-center">
                                            <div className="btn-box py-2">
                                                {i !== 0 &&
                                                <span className="cursor-pointer md:text-xs md:whitespace-no-wrap themePrimaryColor" onClick={() => this.handleRemoveClick(i)}>Remove</span>
                                                }
                                                {this.state.inputList.length - 1 === 0 &&
                                                <span onClick={this.handleAddClick} className="cursor-pointer md:text-xs md:whitespace-no-wrap themePrimaryColor">Add More</span>
                                                }
                                            </div>
                                        </Grid>
                                    </>
                                );
                            })}

                            <Grid item xs={12} sm={6} md={6} lg={6}  className={this.state.errors.ThenClause.length>0 ? "error mti-5" : "mti-5"}>
                                <div className="flex condition">
                                    <label className="flex items-center justify-center w-2/12 text-xs font-normal conditionLabel">
                                        Then
                                    </label>
                                    <SingleSelect
                                        placeholder="Select"
                                        id="ThenClause"
                                        name={"selectedThenClause"}
                                        closeMenuOnSelect={true}
                                        value={this.state.selectedThenClause}
                                        onChangeHandler={this.onChangeHandler}
                                        Options={this.state.thenClauseOptions}
                                        styles={customStyle}
                                        customClassName="w-10/12 mr-5 ThemeSelect"
                                        isLoading={this.state.loaders.presetLoaders}
                                    />
                                </div>
                                <div className="error pl-3">{this.state.errors.ThenClause}</div>
                            </Grid>
                            <Grid item xs={12} sm={6} md={6} lg={6}  className={this.state.errors.Bidby.length>0 ? "error mti-5" : "mti-5"}>
                                <div className="flex condition">
                                    <label className="conditionLabel flex font-normal items-center justify-center sm:w-3/12 text-xs w-2/12">
                                        Bid By (%)
                                    </label>
                                    <div className="ThemeInput w-10/12 mr-5">
                                        <TextFieldInput
                                            placeholder="Enter Bid By"
                                            id="Bidby"
                                            type="number"
                                            name={"selectedBidby"}
                                            value={this.state.selectedBidby}
                                            onChange={this.onInputChangeHandler}
                                            fullWidth={true}
                                            classesstyle = {classes}
                                            
                                        />
                                    </div>
                                </div>
                                <div className="error pl-3">{this.state.errors.Bidby}</div>
                            </Grid>
                        
                        </Grid>
                        <Grid container spacing={1} alignItems="flex-start" className="mbi-5">
                            <Grid className="cssEmail" item xs={12} sm={12} md={12} lg={12}>
                                <label className="font-normal inline-block mb-2 ml-2 text-xs">
                                    Cc Email
                                </label>
                                <CcEmail items={this.state.ccEmails} isReset={this.state.reset} getUpdatedItems={this.getUpdatedItems}>
    
                                </CcEmail>
    
                            </Grid>
                        </Grid>    
                    </Grid>
                    <div className="flex float-right items-center justify-around p-4 w-3/12">
                        <div className="mr-3">
                            <PrimaryButton
                                btnlabel={"Save As Rule"}
                                customclasses="whitespace-no-wrap"
                                variant={"contained"}
                                onClick={this.onSaveAsRule}
                            />   
                        </div>
                        <PrimaryButton
                        btnlabel={"Save"}
                        variant={"contained"}
                        onClick={this.onSubmit}
                        />   
                    </div>
                </form>
              
            </div>
        );
    }
}

export default withStyles(useStyles)(connect(null)(Addingattribute))
