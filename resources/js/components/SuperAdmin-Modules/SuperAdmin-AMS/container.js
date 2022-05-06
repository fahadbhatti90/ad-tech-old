import React, { Component } from 'react';
import "./styles.scss";
import clsx from 'clsx';
import {Card,CardHeader,CardContent,Grid} from '@material-ui/core';
import {withStyles} from "@material-ui/core/styles";
import {Helmet} from "react-helmet";
import {useStyles,customStyle} from "./../../Admin-Module/Manage-Users/styles";
import SingleSelect from "./../../../general-components/Select";
import PrimaryButton from "./../../../general-components/PrimaryButton";
import RadioGroup from '@material-ui/core/RadioGroup';
import Radio from '@material-ui/core/Radio';
import FormControlLabel from '@material-ui/core/FormControlLabel';
import {listOptions,timeOptions,scheduledColumns} from "./data";
import LinearProgress from '@material-ui/core/LinearProgress';
import {
    stringRequiredValidationHelper
} from './../../../helper/yupHelpers';
import * as Yup from 'yup';
import {getScheduledCrons,scheduleCall} from "./apiCalls";
import DataTable from 'react-data-table-component';
import {ShowSuccessMsg} from "./../../../general-components/successDailog/actions";
import { connect } from 'react-redux';
import Divider from '@material-ui/core/Divider';

const ValidateSchemaObject = {
    list: stringRequiredValidationHelper("Report"),
    time: stringRequiredValidationHelper("Time"),
    radioCheck: stringRequiredValidationHelper("Status ")
};

const validationSchema = Yup.object().shape(ValidateSchemaObject);

class AmsScheduling extends Component {
    constructor(props){
        super(props);
        this.state={
            listValue:null,
            timeValue: null,
            timeOptions,
            listOptions,
            radioValue: null,
            isProcessing:true,
            errors:{
                list:"",
                time:"",
                radioCheck: ""
            }
        }
    }

    componentDidMount=()=>{
        this.reloadData();
    }

    reloadData=()=>{
        getScheduledCrons((data)=>{
            this.setState({
                listValue:null,
                timeValue: null,
                radioValue: null,
                data,
                isProcessing: false,
            })
        },(err)=>{
            this.setState({
                isProcessing: false
            })
        })
    }
    onListChangeHandler=(value)=>{
        this.setState({
            listValue: value
        });
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

    onTimeChangeHandler=(value)=>{
        this.setState({
            timeValue: value
        });
    }

    handleChange=(e)=>{           
        this.setState({
            radioValue: e.target.value
        })
    }

    onHandleSubmit=(e)=>{
        e.preventDefault();
        let dataToValidatebject = {
            list: this.state.listValue?this.state.listValue.value:"",
            time: this.state.timeValue?this.state.timeValue.value:"",
            radioCheck: this.state.radioValue?this.state.radioValue:""
        };
        let allValiditionFrom = htk.validateAllFields(validationSchema, dataToValidatebject);
        let resetErrors = this.helperResetErrors();
        if (Object.size(allValiditionFrom) > 0) {
            this.helperSetValidationErrorState(resetErrors, allValiditionFrom);
        } else {
            let crontype = this.state.listValue.value;
            let crontime = this.state.timeValue.value;
            let cronstatus = this.state.radioValue;
            let params = {
                crontype,
                crontime,
                cronstatus
            }

            this.setState({
                isProcessing: true
            })
            scheduleCall(params,(data)=>{
                this.setState({
                    isProcessing: false,
                },()=>{
                    this.props.dispatch(ShowSuccessMsg(data.message, "", true, "",this.reloadData()));
                })
            },(err)=>{
                this.setState({
                    isProcessing: false
                })
                this.props.dispatch(ShowFailureMsg(err.message, "", true, ""));
            })
        }
    }

    render() {
        const {classes} = this.props;
        return (
            <>
            <Helmet>
                <title>Ad-Tech | AMS</title>
            </Helmet>
            <div className="amsSchedule">
                
                <Card className="relative" classes={{root: classes.cardWithContent}}>
                    
                    <CardHeader 
                        title="List" 
                        titleTypographyProps={{variant:'h6' }} />
                       
                    <div className="flex overflow-hidden ">
                        <div className=" rounded graphLoader bg-white absolute h-full overflow-hidden w-full top-1 left-0 z-10" style={this.state.isProcessing?{display:"block"}:{display:"none"}} >
                            <LinearProgress />
                            <div className="absolute flex font-bold font-mono h-full items-center justify-center overflow-hidden text-1rem text-sm w-full z-10">
                                Processing...
                            </div>
                        </div>
                        
                    </div>               
                    <Divider />
                    <CardContent  className={classes.cardContent}>
                        <Grid container spacing={2}>
                            <Grid item xs={12} sm={12} md={6} lg={6}>
                                <div className="flex py-5 px-2">
                                    <div className="font-semibold w-12/12">Scheduling List</div>
                                </div>
                                <form onSubmit={this.onHandleSubmit}> 
                                    <Grid container spacing={2}  justify="flex-start">
                                        <Grid item xs={10} className={this.state.errors.list.length > 0 ? "errorCustom" : ""}>
                                            <label className="text-xs font-normal ml-2">
                                                List <span className="font-black text-red-500 text-sm">*</span>
                                            </label>
                                            <SingleSelect
                                                placeholder="Choose..."
                                                name={"list"}
                                                value={this.state.listValue}
                                                onChangeHandler={this.onListChangeHandler}
                                                fullWidth={true}
                                                maxMenuHeight={190}
                                                Options={this.state.listOptions}
                                                styles={customStyle}
                                                customClassName="ThemeSelect"
                                                id="list"
                                            />
                                            <div className="error pl-3">{this.state.errors.list}</div>
                                        </Grid>
                                        <Grid item xs={10} className={this.state.errors.time.length > 0 ? "errorCustom" : ""}>
                                            <label className="text-xs font-normal ml-2">
                                                    Time List <span className="font-black text-red-500 text-sm">*</span>
                                                </label>
                                                <SingleSelect
                                                    placeholder="Choose..."
                                                    name={"time"}
                                                    value={this.state.timeValue}
                                                    onChangeHandler={this.onTimeChangeHandler}
                                                    fullWidth={true}
                                                    maxMenuHeight={190}
                                                    Options={this.state.timeOptions}
                                                    styles={customStyle}
                                                    customClassName="ThemeSelect"
                                                    id="time"
                                                />
                                                <div className="error pl-3">{this.state.errors.time}</div>
                                        </Grid>
                                        <Grid item xs={10}>
                                            <div className="flex space-x-12">
                                                <label className="text-xs font-normal ml-2">
                                                    Status <span className="font-black text-red-500 text-sm">*</span>
                                                </label>
                                                <div>

                                                    <RadioGroup row aria-label="position" name="position" defaultValue="top">
                                                        <FormControlLabel
                                                            value="top"
                                                            control={<Radio 
                                                                        checked={this.state.radioValue === 'stop'}
                                                                        onChange={this.handleChange}
                                                                        value={"stop"}
                                                                        name="stop"
                                                                        size="small" />}
                                                            label="Stop"
                                                        />
                                                    <div className="ml-5">
                                                        <FormControlLabel
                                                            value="top"
                                                            control={<Radio 
                                                                        checked={this.state.radioValue === 'run'}
                                                                        onChange={this.handleChange}
                                                                        value={"run"}
                                                                        name="run"
                                                                        size="small" />}
                                                            label="Run"
                                                        />
                                                    </div>
                                                    </RadioGroup>
                                                </div>
                                            </div>
                                            <div className="error pl-3">{this.state.errors.radioCheck}</div>
                                        </Grid>
                                        <Grid item xs={10}>
                                            <div className="flex justify-end">
                                                <PrimaryButton
                                                    btnlabel={"Save"}
                                                    variant={"contained"}
                                                    type="submit"
                                                    customclasses={"rounded"}
                                                />
                                            </div>
                                        </Grid>
                                    </Grid>
                                </form>
                            </Grid>
                            <Grid item xs={12} sm={12} md={6} lg={6}>
                                <div className="flex py-5 px-2">
                                    <div className="font-semibold w-12/12">Scheduled List</div>
                                </div>
                                <Card classes={{ root: classes.customDataCard }}>
                                    <div className={clsx("w-full dataTableContainer")} style={{display: 'table', tableLayout: 'fixed', width: '100%'}}>
                                        <DataTable
                                                Clicked
                                                noHeader={true}
                                                wrap={false}
                                                responsive={true}
                                                columns={scheduledColumns}
                                                data={this.state.data}
                                                />
                                    </div>
                                </Card>
                            </Grid>
                        </Grid>    
                    </CardContent>
                </Card>
            </div>
        </>
        );
    }
}

export default withStyles(useStyles) (connect(null)(AmsScheduling));