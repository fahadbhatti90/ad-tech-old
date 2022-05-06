import React, { Component } from 'react';
import {customStyle,useStyles} from "./../../Manage-Users/styles";
import {Grid,Card,Typography,withStyles} from "@material-ui/core";
import {checkHistory} from "./../apiCalls";
import SingleSelect from "./../../../../general-components/Select";
import TextFieldInput from "./../../../../general-components/Textfield";
import CustomizedDateRangePicker from "./../../../../general-components/DateRangePicker/CustomizedDateRangePicker";
import moment from 'moment';
import PrimaryButton from "./../../../../general-components/PrimaryButton";
import {ShowSuccessMsg} from "./../../../../general-components/successDailog/actions";
import {ShowFailureMsg} from "./../../../../general-components/failureDailog/actions";
import {connect} from "react-redux";
import * as Yup from 'yup';
import LinearProgress from '@material-ui/core/LinearProgress';
import DownloadUrlModal from './DownloadUrlModal';
import {
    stringRequiredValidationHelper
} from './../../../../helper/yupHelpers';
import {Helmet} from "react-helmet";

const options = [{value:"Advertising_Campaign_Reports",label:"Advertising Campaign Reports"},
                { value:"Ad_Group_Reports" ,label: "Ad Group Reports"},
                { value:"Keyword_Reports" ,label: "Keyword Reports"},
                { value:"Product_Ads_Report" ,label: "Product Ads Report"},
                { value:"ASINs_Report" ,label: "ASINs Report"},
                { value:"Product_Attribute_Targeting_Reports" ,label: "Product  Attribute Targeting Reports"},
                { value:"Sponsored_Brand_Reports" ,label: "Sponsored Brand Reports"},
                {value: "Sponsored_Brand_Campaigns",label: "Sponsored Brand Campaigns"},
                {value:"Sponsored_Display_Campaigns",label :"Sponsored Display Campaigns" },                         
                {value:"Sponsored_Display_ProductAds",label:"Sponsored Display ProductAds"},
                {value:"Sponsored_Display_Adgroup",label: "Sponsored Display Adgroup"},
                {value:"Sponsored_Brand_Adgroup",label: "Sponsored Brand Adgroup Report"},
                {value:"Sponsored_Brand_Targeting",label: "Sponsored Brand Targeting Report"}
            ];

const ValidateSchemaObject = {
    report: stringRequiredValidationHelper("Report"),
    date: stringRequiredValidationHelper("Date Range")
    };

const validationSchema = Yup.object().shape(ValidateSchemaObject);

class ExportCsv extends Component {
    constructor(props){
        super(props);
        this.state={
            reportValue: null,
            reportValueOptions:options,
            selectedDate:"",
            showDRP:false,
            startDate:"",
            endDate:"",
            dateRange: {},
            dateRangeObj:{
                startDate:new Date(),
                endDate:new Date(),
                key: 'selection',
            },
            errors:{
                report:"",
                date:""
            },
            isProcessing: false,
            openDailog: false,
            status: false,
            url: null
        }
    }

    onReportValueChange=(reportValue)=>{
        this.setState({
            reportValue
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

    setShowDRP=()=>{
        let showDRP = !this.state.showDRP;
        this.setState({
            showDRP
        })
    }

    onDateChange=(range)=>{
        let startDate = moment(range.startDate).format('YYYY-MM-DD');
        let endDate = moment(range.endDate).format('YYYY-MM-DD');
        this.setState({
            showDRP:false,
            dateRangeObj:range,
            startDate:startDate,
            endDate: endDate,
            selectedDate: startDate+" - "+endDate,
        })
    }

    handleSubmit=()=>{
        let dataToValidatebject = {
            report: this.state.reportValue?this.state.reportValue:"",
            date: this.state.selectedDate
        };
        let allValiditionFrom = htk.validateAllFields(validationSchema, dataToValidatebject);
        let resetErrors = this.helperResetErrors();
        if (Object.size(allValiditionFrom) > 0) {
            this.helperSetValidationErrorState(resetErrors, allValiditionFrom);
        } else {
            let reporttype = this.state.reportValue;
            let daterange = this.state.selectedDate;
            let params = {
                reporttype:reporttype.value,
                daterange
            }
       
            this.setState({
                isProcessing: true
            })
            checkHistory(params,(res)=>{
                this.setState({
                    isProcessing: false,
                    openDailog: true,
                    status: res.status,
                    message: res.message,
                    url: res.url
                })
            },(err)=>{
                this.setState({
                    isProcessing: false
                })
                this.props.dispatch(ShowFailureMsg(data.message, "", true, ""));
            })
            }
    }

    handleModalClose = ()=>{
        this.setState({
            openDailog: false
        })
    }

    render() {
        const {classes} = this.props;
        return (
            <>
                <Helmet>
                    <title>Ad-Tech AMS</title>
                </Helmet>
                <Card  classes={{ root: classes.card }} className="relative">
                    <div className="graphLoader bg-white absolute h-full overflow-hidden w-full top-0 left-0 z-10" style={this.state.isProcessing?{display:"block"}:{display:"none"}} >
                        <LinearProgress />
                        <div className="absolute flex font-bold font-mono h-full items-center justify-center overflow-hidden text-1rem text-sm w-full z-10">
                            Processing...
                        </div>
                    </div>
                    {/* Header of the Module */}
                    <Typography variant="h6" className={`${classes.pageTitle}`} noWrap>
                        Export Data
                    </Typography>

                    <div className="mt-5">
                        <Grid container spacing={2} className={this.state.errors.report.length > 0 ? "errorCustom" : ""}>
                            <Grid item xs={6}>
                                <label className="text-sm ml-1">
                                    List <span className="required-asterisk">*</span>
                                </label>
                                <div className="ThemeInput">
                                    <SingleSelect
                                        placeholder="Choose..."
                                        id="reportValue"
                                        isClearable={false}
                                        name={"reportValue"}
                                        value={this.state.reportValue}
                                        onChangeHandler={this.onReportValueChange}
                                        fullWidth={true}
                                        Options={this.state.reportValueOptions}
                                        customClassName="ThemeSelect"
                                        styles={customStyle}
                                    />
                                </div>
                                <div className="error pl-3">{this.state.errors.report}</div>
                            </Grid>

                            <Grid item xs={6} className={this.state.errors.date.length > 0 ? "errorCustom px-2" : "px-2"}>  
                                <label className="text-sm ml-1">
                                    Select Date Range <span className="required-asterisk">*</span>
                                </label>
                                
                                <div onClick={this.setShowDRP}>
                                    <div className="ThemeInput">
                                        <TextFieldInput
                                            placeholder="Date Range"
                                            id="dr"
                                            type="text"
                                            name={"selectedDate"}
                                            value={this.state.selectedDate}
                                            fullWidth={true}
                                            classesstyle={classes}
                                        />
                                    </div>
                                </div>
                                <div className="error pl-3">{this.state.errors.date}</div>

                                {/* ${props.datepickerClass} */}
                                <div className={`absolute right-0  ${classes.datepickerClass}`}> 
                                    {this.state.showDRP ? <CustomizedDateRangePicker range={this.state.dateRangeObj} helperCloseDRP = {this.setShowDRP} getValue = {this.onDateChange} direction="horizontal"/>:null}
                                </div> 

                            </Grid>
                        </Grid>
                        <div className="flex mt-8 justify-end">
                            <PrimaryButton
                                btnlabel={"Submit"}
                                variant={"contained"}
                                onClick={this.handleSubmit}
                            />  
                        </div>   
                    </div>
                </Card>
            
            <DownloadUrlModal
                handleModalClose={this.handleModalClose}
                status ={this.state.status}
                open={this.state.openDailog}
                modalTitle = {"Download Report"}
                message = {this.state.message}
                title={this.state.reportValue?this.state.reportValue.label:""}
                url = {this.state.url}
            />
            </>
        );
    }
}

export default withStyles(useStyles) (ExportCsv);