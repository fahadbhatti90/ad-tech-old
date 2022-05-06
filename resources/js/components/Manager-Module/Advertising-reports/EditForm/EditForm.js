import React, {Component} from 'react';
import {Grid} from "@material-ui/core";
import TextFieldInput from "./../../../../general-components/Textfield";
import SingleSelect from "./../../../../general-components/Select";
import {
    getAdReportsData,
    getReportTypesApi,
    getReportMetrics,
    createSchedule,
    getEditFormData,
    filterMetrics
} from "./../apicalls";
import HMultiSelect from "./../../../../general-components/HMultiSelect/HMultiSelect";
import {ShowSuccessMsg} from "./../../../../general-components/successDailog/actions";
import {ShowFailureMsg} from "./../../../../general-components/failureDailog/actions";
import {connect} from "react-redux";
import {
    TimePicker,
    MuiPickersUtilsProvider,
} from '@material-ui/pickers';
import DateFnsUtils from '@date-io/date-fns';
import SelectDay from "./../AddForm/SelectDay";
import CcEmail from "./../../../../general-components/EmailCC/EmailChips";
import "./../styles.scss";
import Metrics from "./../AddForm/Metrics";
import PrimaryButton from "./../../../../general-components/PrimaryButton";
import TextButton from "./../../../../general-components/TextButton";
import moment from "moment";
import {
    stringRequiredValidationHelper,
    objectRequiredValidationHelper,
    arrayRequiredValidationHelper
} from './../../../../helper/yupHelpers';
import * as Yup from 'yup';
import MultiSelect from "./../../../../general-components/MultiSelect";
import {checkArrayStates} from "./../../../../helper/helper";
import {hideLoader, showLoader} from "./../../../../general-components/loader/action";
import CheckBox from "./../../../../general-components/CheckBox";
import LinearProgress from '@material-ui/core/LinearProgress';
var preSelected = {
    selectedDays: {
        F: false,
        M: true,
        SA: true,
        SU: true,
        T: true,
        TH: false,
        W: false
    }
}
var GranularityOptions = [
    {label: "Daily", value: "Daily"},
    {label: "Weekly", value: "Weekly"},
    {label: "Monthly", value: "Monthly"}
    // more options...
];
export function TimeFrame(selectedValue) {

    if (selectedValue == 'Daily') {
        let TimeFrameOptions = [];
        for (let i = 1; i <= 365; i++) {
            if (i == 1) {
                var days = 'Day';
            } else {
                var days = 'Days';
            }
            TimeFrameOptions.push({label: "Last " + i + " " + days, value: i});
        }
        return TimeFrameOptions;
    }
    if (selectedValue == 'Weekly') {
        let TimeFrameOptions = [];
        for (let i = 1; i <= 52; i++) {

            if (i == 1) {
                var weeks = 'Week';
            } else {
                var weeks = 'Weeks';
            }

            TimeFrameOptions.push({label: "Last " + i + " " + weeks, value: i});
        }
        return TimeFrameOptions;
    }
    if (selectedValue == 'Monthly') {
        let TimeFrameOptions = [];
        for (let i = 1; i <= 12; i++) {
            if (i == 1) {
                var months = 'Months';
            } else {
                var months = 'Months';
            }
            TimeFrameOptions.push({label: "Last " + i + " " + months, value: i});
        }
        return TimeFrameOptions;

    }
}

class EditForm extends Component {
    constructor(props) {
        super(props);
        this.state = {
            id:0,
            reportName: '',
            brand: null,
            reportType: null,
            oldSponsordType: null,
            oldSlctdRptType: null,
            sponsordType: null,
            granularity: {label: "Daily", value: "Daily"},
            timeFrame: null,
            timeFrameOptions: [],
            time: new Date(),
            amsProfiles: [],
            sponsordTypes: [],
            sponsordReports: [],
            metricsCbData: [],
            ccEmails: [],
            selectedDays: [],
            metrixSelected: "",
            allMetricCheck:false,
            M: false,
            T: false,
            W: false,
            TH: false,
            F: false,
            SA: false,
            SU: false,
            isLoaded:false,
            errors: {
                reportN: "",
                timeF: "",
                sponsordT: "",
                brandE: "",
                reportT: "",
                granularityE: "",
                timeE: "",
                selectedD: "",
                selectMetrix: ""
            }
        }
    }

    componentDidMount = () => {
        this.getEditData();
    }

    getEditData = () => {

        getEditFormData(this.props.rowId, (data) => {

            this.setState({
                id:this.props.rowId,
                reportName: data.reportName,
                brand: data.fkProfileId,
                oldSponsordType: data.selectedSponsordTypeValueArray,
                oldSlctdRptType: data.selectedReportTypesArray,
                sponsordReports: data.reportsTypesArray,
                granularity: GranularityOptions.filter(row => {
                    return row.value == data.granularity
                })[0],
                allMetricCheck: (data.allMetricsCheck == 1),
                M: (data.mon == 1),
                T: (data.tue == 1),
                W: (data.wed == 1),
                TH: (data.thu == 1),
                F: (data.fri == 1),
                SA: (data.sat == 1),
                SU: (data.sun == 1),
                metricsCbData: filterMetrics(data),
                isLoaded:true,
                metrixSelected:'selected',
                time: moment(data.time, [moment.ISO_8601, 'HH:mm A']),
                ccEmails: (data.addCC.length > 0) ? data.addCC.split(',') : [],
            }, () => {
                let getTimeFrameOptions = this.getTimeFrameOptions();
                let timeFrame = getTimeFrameOptions.filter(row => {
                    return row.value == data.timeFrame
                })[0]
                this.setState({
                    timeFrame
                })


                this.getFormData();
            })
            //success
        }, (err) => {
            //error
            alert(err);
        });
    }

    resetErrors = (key) => {
        let {errors} = this.state;
        errors[key] = ""
        this.setState({
            ...errors
        })
    }

    onCheckBoxesChangeHandler = (e) => {
        this.setState({
            [e.target.name]: e.target.checked
        }, () => {
            this.resetErrors("selectedD");
        })
    }
    getFormData = () => {
        getAdReportsData((data) => {
            this.updateState(data);
            //success
        }, (err) => {
            //error
            alert(err);
        });
    }

    onChangeSponsordType = (values) => {
        this.setState({
                sponsordType: values,
                sponsordReports:[],
                metricsCbData:[],
                reportType:null
            },
            () => {
                let inputST = checkArrayStates(values)
                if (inputST.length > 0 ) {
                    this.resetErrors("sponsordT");
                    let selectedSponsoredTypes = this.state.sponsordType.map(item => {
                        return item.value;
                    });
                    getReportTypesApi(selectedSponsoredTypes, (data) => {
                        //success
                        this.setState({
                            sponsordReports: data
                        })
                    }, (err) => {
                        //error
                        alert(err);
                    })
                }else{
                    this.setState({
                        reportType: null,
                        sponsordReports: [],
                        metricsCbData: [],
                        metrixSelected: ""
                    })

                }
            })
    }

    onGranularityChange = (value) => {
        this.setState({
            granularity: value,
            timeFrame:null
        }, () => {
            this.resetErrors("granularityE");
            this.getTimeFrameOptions();
        })
    }

    getTimeFrameOptions = () => {
        if (this.state.granularity != null) {
            let timeFrameOptions = TimeFrame(this.state.granularity.value);
            this.setState({
                timeFrameOptions
            })
            return timeFrameOptions;
        }
    }
    onTimeFrameChange = (value) => {
        this.setState({
            timeFrame: value
        }, () => {
            this.resetErrors("timeF");
        })
    }
    onChangeReportType = (values) => {
        this.setState({
                reportType: values,
                metricsCbData: []
            },
            () => {
                if (this.state.reportType != null) {
                    this.resetErrors("reportT");
                    let selectedReportTypes = this.state.reportType.map(item => {
                        return item.value;
                    });
                    getReportMetrics(selectedReportTypes, (data) => {
                        //success
                        this.setState({
                            metricsCbData: data
                        })
                    }, (err) => {
                        //error
                        alert(err);
                    })
                }
            })
    }
    updateState = (data) => {

        let amsProfiles = data.amsProfiles.map((obj, idx) => {
            return {
                label: obj.accountName,
                value: obj.fkId,
                key: idx
            }
        });
        let brand = amsProfiles.filter(row => {
            return row.value == this.state.brand
        })[0]

        let sponsordTypes = data.sponsordTypes.map((obj, idx) => {
            return {
                label: obj.sponsordTypenName,
                value: obj.id,
                key: idx
            }
        });
        let preSelectSponsoredType = this.state.oldSponsordType;
        let sponsordType = this.genericPreSelection(sponsordTypes, preSelectSponsoredType);
        let reportOptions = this.state.sponsordReports;
        let selectedReport = this.state.oldSlctdRptType;
        let reportType = this.genericPreSelection(reportOptions, selectedReport);

        this.setState({
            brand,
            amsProfiles,
            sponsordType,
            sponsordTypes,
            reportType
        });
    }

    genericPreSelection = (availableOptions, preSelectOption) => {
        let getSelectedValues = availableOptions.filter(function (el) {
            let isDataExist = false;
            $.each(preSelectOption, function (indexInArray, valueOfElement) {
                if (isDataExist) return;
                isDataExist = el.value == valueOfElement;
            });
            return isDataExist;
        });

        return getSelectedValues;
    }


    onBrandChange = (value) => {
        this.setState({
            brand: value
        }, () => {
            this.resetErrors("brandE");
        })
    }
    onReportChange = (e) => {
        this.setState({
            reportName: e.target.value
        }, () => {
            this.resetErrors("reportN");
        })
    }

    handleTimeChange = (time) => {
        this.setState({
            time
        }, () => {
            this.resetErrors("timeE");
        })
    }

    getUpdatedItems = (items) => {
        console.log('items selcte email ', items)
        this.setState({
            ccEmails: items
        })
    }


    filterIds = (data, key) => {
        let selectedIds = [];
        data.map(x => {
            if (Object.keys(x) == key) {
                let keys = [];
                x[Object.keys(x)[0]].map(obj => {
                    if (obj.isChecked) {
                        keys.push(obj.id);
                    }
                })
                return selectedIds.push(keys.join(','));
            }
        });
        return selectedIds.toString();
    }

    getKeyByValue = (object, value) => {
        return Object.keys(object).filter(key => object[`${key}`] === value);
    }


    submitForm = (e) => {
        e.preventDefault();

        let ValidateSchemaObject = {
            reportN: stringRequiredValidationHelper("Report Name").matches(
                /^[a-zA-Z0-9 ]+$/,
                "Only Alpha Numeric allowed"
            ),
            timeF: objectRequiredValidationHelper("TimeFrame"),
            sponsordT: objectRequiredValidationHelper("Ad Type"),
            brandE: objectRequiredValidationHelper("Child Brand"),
            reportT: objectRequiredValidationHelper("Report Type"),
            granularityE: objectRequiredValidationHelper("Granularity"),
            timeE: stringRequiredValidationHelper("Time"),
            selectedD: stringRequiredValidationHelper("Days"),
            selectMetrix: stringRequiredValidationHelper("Metrics Selection of each group"),
        };

        let validationSchema = Yup.object().shape(ValidateSchemaObject);
        console.log("this.state.time", this.state.time)
        let time = moment(this.state.time).format('hh:mm A');
        console.log("time",time)
        let {
            F,
            M,
            SA,
            SU,
            T,
            TH,
            W,
            reportName,
            brand,
            granularity,
            timeFrame,
            ccEmails,
            metricsCbData,
            sponsordType,
            reportType,
            metrixSelected
        } = this.state;
        let selectedcampaignMetricsCheckBox = this.filterIds(metricsCbData, "Campaign");
        let selectedadGroupMetricsCheckBox = this.filterIds(metricsCbData, 'Ad Group');
        let selectedProductAdsMetricsCheckBox = this.filterIds(metricsCbData, 'Product Ads');
        let selectedkeywordMetricsCheckBox = this.filterIds(metricsCbData, "Keyword");
        let selectedAsinMetricsCheckBox = this.filterIds(metricsCbData, "ASINS");
        let selectedDays = (M || F || SA || SU || T || TH || W) ? 'true' : '';


        let dataToValidatebject = {
            reportN: reportName,
            timeF: timeFrame,
            sponsordT: sponsordType,
            brandE: brand,
            reportT: reportType,
            granularityE: granularity,
            timeE: time,
            selectedD: selectedDays,
            selectMetrix: metrixSelected,
        };

        let allValiditionFrom = htk.validateAllFields(validationSchema, dataToValidatebject);

        if (Object.size(allValiditionFrom) > 0) {
            const {errors} = this.state;
            $.each(allValiditionFrom, function (indexInArray, valueOfElement) {
                errors[indexInArray] = valueOfElement;
            });
            this.setState((prevState) => ({
                errors: errors
            }));
        } else {

            let sponsordType = this.state.sponsordType ? this.state.sponsordType.map(item => {
                    return item.value;
                })
                : null

            let reportType = this.state.reportType ? this.state.reportType.map(item => {
                    return item.value;
                })
                : null

            let formData = {
                id:this.state.id,
                reportName,
                timeFrame: timeFrame.value,
                sponsordType: sponsordType.toString(),
                brand: brand.value,
                selectAllMetrics:this.state.allMetricCheck,
                reportType: reportType.toString(),
                granularity: granularity.value,
                ccEmails,
                time,
                selectedDays: selectedDays.toString(),
                selectedcampaignMetricsCheckBox,
                selectedadGroupMetricsCheckBox,
                selectedProductAdsMetricsCheckBox,
                selectedkeywordMetricsCheckBox,
                selectedAsinMetricsCheckBox,
                opType: 2,
                M,
                F,
                SU,
                SA,
                TH,
                W,
                T
            }
            createSchedule(formData, (data) => {
                //success
                if (data.status) {
                    this.props.getScheduleReportDataCall();
                    this.props.handleModalClose();
                    this.props.dispatch(ShowSuccessMsg(data.message, "Successfully", true, ""));
                } else {
                    // message,infoMsg,open,htmlList,secondaryMessage,callback
                    this.props.dispatch(ShowFailureMsg("", "", true, data.message));
                }

                //success
            }, (err) => {
                //error
                alert(err);
            })
        }
    }

    updateMetrix = (value) => {

        this.setState({
            metrixSelected: value
        }, () => {
            if (this.state.metrixSelected == "selected") {
                this.resetErrors("selectMetrix");
            }
        })
    }
    updateMetricsData = (MetricsData) => {
        this.setState({
            metricsCbData: MetricsData
        })
    }
    updateCheckBox = (value) => {
        this.setState({
            allMetricCheck:value
        })
    }
    updateSelectedDays = (selectedDays) => {

        //console.log('selected days', selectedDays)
        this.setState({
            ...selectedDays
        }, () => {
            this.resetErrors("selectedD");
        })
    }

    render() {
        return (
            <>
                <div className="graphLoader bg-white absolute h-full overflow-hidden w-full top-0 left-0 z-10" style={!this.state.isLoaded?{display:"block"}:{display:"none"}} >
                    <LinearProgress />
                    <div className="absolute flex font-bold font-mono h-full items-center justify-center overflow-hidden text-1rem text-sm w-full z-10">
                        Loading...
                    </div>
                </div>
                <div className="formMargin">
                    <form>
                        <MuiPickersUtilsProvider utils={DateFnsUtils}>
                            <Grid container spacing={2}>
                                <Grid item xs={12} md={6} lg={6}
                                      className={this.state.errors.reportN.length > 0 ? "errorCustom" : ""}>
                                    <label className="text-sm ml-2">
                                        Report Name <span className="required-asterisk">*</span>
                                    </label>
                                    <div className="ThemeInput">
                                        <TextFieldInput
                                            placeholder="Enter Report Name"
                                            id="report"
                                            type="text"
                                            value={this.state.reportName}
                                            onChange={this.onReportChange}
                                            fullWidth={true}
                                        />
                                        <div className="error pl-3">{this.state.errors.reportN}</div>
                                    </div>
                                </Grid>
                                <Grid item xs={12} md={6} lg={6}
                                      className={this.state.errors.brandE.length > 0 ? "errorCustom" : ""}>
                                    <label className="text-sm ml-2">
                                        Child Brand <span className="required-asterisk">*</span>
                                    </label>
                                    <SingleSelect
                                        placeholder="Child Brand"
                                        id="childbrand"
                                        isClearable={false}
                                        name={"selectedProfile"}
                                        value={this.state.brand}
                                        onChangeHandler={this.onBrandChange}
                                        fullWidth={true}
                                        Options={this.state.amsProfiles}
                                        customClassName="ThemeSelect"
                                    />
                                    <div className="error pl-3">{this.state.errors.brandE}</div>
                                </Grid>

                                <Grid item xs={12} md={6} lg={6}
                                      className={this.state.errors.sponsordT.length > 0 ? "errorCustom" : ""}>
                                    <label className=" text-sm ml-2">
                                        Ad Type <span className="required-asterisk">*</span>
                                    </label>
                                    <MultiSelect
                                        placeholder="Select Ad Type"
                                        id="adType"
                                        className="bg-white"
                                        name="adType"
                                        value={this.state.sponsordType}
                                        onChangeHandler={this.onChangeSponsordType}
                                        Options={this.state.sponsordTypes}
                                        reset={this.unSelectSponsoredTypes}
                                    />
                                    <div className="error pl-3">{this.state.errors.sponsordT}</div>
                                </Grid>

                                <Grid item xs={12} md={6} lg={6}
                                      className={this.state.errors.reportT.length > 0 ? "errorCustom" : ""}>
                                    <label className=" text-sm ml-2">
                                        Report Type <span className="required-asterisk">*</span>
                                    </label>
                                    <MultiSelect
                                        placeholder="Select Report Type"
                                        id="reportType"
                                        className="bg-white"
                                        name="reportType"
                                        value={this.state.reportType}
                                        onChangeHandler={this.onChangeReportType}
                                        Options={this.state.sponsordReports}
                                        reset={this.unSelectReportTypes}
                                    />
                                    <div className="error pl-3">{this.state.errors.reportT}</div>
                                </Grid>

                                <Grid item xs={12} md={6} lg={6}>
                                    <div className="overflow-y-auto">
                                        {
                                            this.state.isLoaded ? <Metrics
                                            errors={this.state.errors}
                                            metrixSelected={this.state.metrixSelected}
                                            updateMetrix={this.updateMetrix}
                                            metricsCbData={this.state.metricsCbData}
                                            updateMetricsData={this.updateMetricsData}
                                            updateCheckBox={this.updateCheckBox}
                                            isAllCheckBoxSelected={this.state.allMetricCheck}
                                        /> : null
                                        }

                                    </div>
                                    <fieldset className="border">
                                        <legend className=" ml-2">Time Frame <span
                                            className="required-asterisk">*</span></legend>
                                        <div className={this.state.errors.timeF.length > 0 ? "errorCustom" : ""}>
                                            <label className="text-sm ml-2">
                                                Select Days <span className="required-asterisk">*</span>
                                            </label>
                                            <SingleSelect
                                                placeholder="select Time Frame"
                                                isClearable={false}
                                                id="timeFrame"
                                                name={"timeFrame"}
                                                value={this.state.timeFrame}
                                                onChangeHandler={this.onTimeFrameChange}
                                                fullWidth={true}
                                                Options={this.state.timeFrameOptions}
                                                customClassName="mr-5 ThemeSelect"
                                            />
                                            <div className="error pl-3">{this.state.errors.timeF}</div>
                                        </div>
                                    </fieldset>
                                </Grid>

                                <Grid item xs={12} md={6} lg={6}
                                      className={this.state.errors.granularityE.length > 0 ? "errorCustom" : ""}>
                                    <div className="mt-1">
                                        <label className="text-sm ml-2">
                                            Granularity <span className="required-asterisk">*</span>
                                        </label>
                                        <SingleSelect
                                            placeholder=""
                                            id="granularity"
                                            isClearable={false}
                                            name={"granularity"}
                                            value={this.state.granularity}
                                            onChangeHandler={this.onGranularityChange}
                                            fullWidth={true}
                                            Options={GranularityOptions}
                                            customClassName="ThemeSelect"
                                        />
                                        <div className="error pl-3">{this.state.errors.granularityE}</div>
                                    </div>

                                    <fieldset className="mt-4 border hfield">
                                        <legend className="ml-2">Schedule <span className="required-asterisk">*</span>
                                        </legend>

                                        <label className=" text-sm ml-2 mt-2">
                                            Select Days <span className="required-asterisk">*</span>
                                        </label>

                                        <div className={this.state.errors.selectedD.length > 0 ? "error ml-3 mt-2" : "ml-3 mt-2"} style={{minHeight: 76}}>
                                            <CheckBox
                                                label="M"
                                                size="small"
                                                name={"M"}
                                                checked={this.state.M}
                                                onChange={this.onCheckBoxesChangeHandler}
                                            />
                                            <CheckBox
                                                label="T"
                                                name={"T"}
                                                checked={this.state.T}
                                                size="small"
                                                onChange={this.onCheckBoxesChangeHandler}
                                            />
                                            <CheckBox
                                                label="W"
                                                name={"W"}
                                                size="small"
                                                checked={this.state.W}
                                                onChange={this.onCheckBoxesChangeHandler}
                                            />
                                            <CheckBox
                                                label="TH"
                                                name={"TH"}
                                                size="small"
                                                checked={this.state.TH}
                                                onChange={this.onCheckBoxesChangeHandler}
                                            />
                                            <CheckBox
                                                label="F"
                                                name={"F"}
                                                size="small"
                                                checked={this.state.F}
                                                onChange={this.onCheckBoxesChangeHandler}
                                            />
                                            <CheckBox
                                                label="SA"
                                                name={"SA"}
                                                size="small"
                                                checked={this.state.SA}
                                                onChange={this.onCheckBoxesChangeHandler}
                                            />
                                            <CheckBox
                                                label="SU"
                                                name={"SU"}
                                                size="small"
                                                checked={this.state.SU}
                                                onChange={this.onCheckBoxesChangeHandler}
                                            />
                                            <div className="error pl-3">{this.state.errors.selectedD}</div>
                                        </div>
                                        <div className={this.state.errors.timeE.length > 0 ? "errorCustom" : ""}>
                                            <label className="text-sm ml-2 mt-5">
                                                Time <span className="required-asterisk">*</span>
                                            </label>
                                            <div className={"mr-5 ThemeSelect"}>
                                                <TimePicker
                                                    className={"timepicker"}
                                                    name="startTime"
                                                    fullWidth={true}
                                                    InputProps={{
                                                        disableUnderline: true,
                                                    }}
                                                    value={this.state.time}
                                                    onChange={this.handleTimeChange}/>
                                                <div className="error pl-3">{this.state.errors.timeE}</div>
                                            </div>
                                        </div>
                                    </fieldset>

                                </Grid>
                                <Grid item xs={12} md={12} lg={12}>
                                    <label className="text-sm ml-2">
                                        Cc Email
                                    </label>
                                    <CcEmail
                                        items={this.state.ccEmails}
                                        getUpdatedItems={this.getUpdatedItems}
                                        classListItem={"listItemsGray"}
                                    />
                                </Grid>


                                <Grid item xs={12} md={12} lg={12} className="text-center">
                                    <Grid container justify="center" spacing={2}>
                                        <Grid item xs={2} md={2} lg={2}>
                                            <TextButton
                                                BtnLabel={" Cancel "}
                                                color="primary"
                                                onClick={this.props.handleModalClose}/>

                                        </Grid>
                                        <Grid item xs={2} md={2} lg={2}>
                                            <PrimaryButton
                                                btnlabel={"  Update  "}
                                                variant={"contained"}
                                                onClick={this.submitForm}/>

                                        </Grid>
                                    </Grid>
                                </Grid>

                            </Grid>
                        </MuiPickersUtilsProvider>
                    </form>
                </div>
            </>
        );
    }

}

export default connect(null)(EditForm)