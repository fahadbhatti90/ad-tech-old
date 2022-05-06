import React, {Component} from 'react';
import {
    TimePicker,
    MuiPickersUtilsProvider,
} from '@material-ui/pickers';
import MomentUtils from '@date-io/moment';
import SingleSelect from "./../../../../general-components/Select";
import MultiSelect from "./../../../../general-components/MultiSelect";
import CheckBox from "./../../../../general-components/CheckBox";
import CcEmail from "./../../../../general-components/EmailCC/EmailChips";
import Button from "@material-ui/core/Button";
import {Grid, withStyles} from '@material-ui/core/index';
import TextFieldInput from "./../../../../general-components/Textfield";
import "./../dayparting.scss"
import {getCampaigns, getProfiles, storeDayPartingForm} from "../apiCalls";
import {connect} from "react-redux";
import moment from "moment";
import {breakProfileId, requiredValidationHelper, showErrors} from "./../../../../helper/helper";
import {ShowSuccessMsg} from "./../../../../general-components/successDailog/actions";
import {ShowFailureMsg} from "./../../../../general-components/failureDailog/actions";
import * as Yup from 'yup';
import {styles} from "../styles";
import {hideLoader, showLoader} from "./../../../../general-components/loader/action";
import {objectRequiredValidationHelper, stringRequiredValidationHelper, arrayRequiredValidationHelper} from "./../../../../helper/yupHelpers";
import PrimaryButton from "../../../../general-components/PrimaryButton";

const list = (errorsData) => {
    return (
        <>
            {errorsData.map(item => (
                <li className="list-none"> {item} </li>
            ))}
        </>
    )
};

class AddFormDayParting extends Component {
    constructor(props) {
        super(props);
        this.state = {
            scheduleName: "",
            selectedProfile: null,
            profileOptions: [],
            selectedPfCampaign: null,
            selectedPfCampaignOption: [],
            selectedPortfoliosCampaigns: null,
            PortfoliosCampaignsOptions: [],
            mon: false,
            tue: false,
            wed: false,
            thu: false,
            fri: false,
            sat: false,
            sun: false,
            reccuringSchedule: false,
            startTime: new Date(),
            endTime: new Date(),
            emailReceiptStart: false,
            emailReceiptEnd: false,
            ccEmail: [],
            items: [],
            reset: false,
            errors: {
                scheduleN: "",
                selectedP: "",
                selectedPfCp: "",
                selectedPfsCps: "",
                stTime: "",
                edTime: "",
            }
        }
    }

    componentDidMount = () => {
        this.props.dispatch(showLoader());
        getProfiles((profileOptions) => {
            //success
            this.setState({
                profileOptions
            })
        }, (err) => {
            //error
            // this.props.dispatch(showSnackBar());
        });

        this.setState({
            selectedPfCampaignOption: [
                {value: "Campaign", label: 'Campaign'},
                {value: "Portfolio", label: 'Portfolio'}
            ]
        })
        this.props.dispatch(hideLoader());
    }
    onPortfoliosCampaignsChange = (value) => {
        if(value && value.length == 0){
            value = null
        }
        let tempPc = this.state.selectedPortfoliosCampaigns;
        this.setState({
            selectedPortfoliosCampaigns: value
        }, () => {

            if (tempPc && value && tempPc.length <= value.length) {
                $(".autoScrl .select__value-container").animate({
                    scrollTop: $('.autoScrl .select__value-container').get(0).scrollHeight
                });
            }
            this.resetErrors("selectedPfsCps");
        })
    }

    // Child Brand On Change Event
    onProfileChange = (value) => {
        //success
        this.setState({
            selectedProfile: value,
        }, () => {
            this.resetErrors("selectedP");
            this.setState({
                selectedPortfoliosCampaigns: null,
                PortfoliosCampaignsOptions: null,
            })
            this.callPortfolioCampaign();

        })
    }
    // Get Campaigns on the basis of profile
    getPortfolioCampaigns = () => {
        let pf = this.state.selectedProfile.value;
        let cpt = this.state.selectedPfCampaign.value;

        getCampaigns(pf, cpt, '', '', (data) => {

            if (data.length > 0) {
                //success
                this.setState({
                    PortfoliosCampaignsOptions: data
                })
            } else {
                this.props.dispatch(ShowFailureMsg("No Data Found ", "", true, ""));
            }
        }, (err) => {
            //error
            // this.props.dispatch(showSnackBar());
        });
    }

    onChangeText = (e) => {
        this.setState({
            scheduleName: e.target.value
        }, () => {
            this.resetErrors("scheduleN")
        })

    }

    onPfCampaignChange = (value) => {

        this.setState({
            selectedPfCampaign: value,
        }, () => {
            this.resetErrors("selectedPfCp");
            this.setState({
                selectedPortfoliosCampaigns: null,
                PortfoliosCampaignsOptions: [],
            })
            this.callPortfolioCampaign();
        })
    }

    callPortfolioCampaign = () => {
        if (this.state.selectedProfile && this.state.selectedPfCampaign) {
            this.getPortfolioCampaigns();
        }
    }

    onCheckBoxesChangeHandler = (e) => {
        this.setState({
            [e.target.name]: e.target.checked
        }, () => {
        })
    }

    onStartDateChangeHandler = (startTime) => {
        this.setState({
            startTime: startTime,
            endTime: startTime
        }, () => {
            this.resetErrors("stTime")
            this.resetErrors("edTime")
        })
    };

    onEndDateChangeHandler = (endTime) => {
        this.setState({
            endTime: endTime
        }, () => {
            this.resetErrors("edTime")
        })
    };

    getUpdatedItems = (items) => {
        this.setState({
            ccEmail: items
        })
    }

    formSubmissionDayParting = event => {
        event.preventDefault();
        var isTimeLess = false;
        let startTime = moment(this.state.startTime).format('HH:mm');
        let endTime = moment(this.state.endTime).format('HH:mm');

        if (endTime < startTime || endTime == startTime){

            this.setState({
                errors:{
                    edTime:"end time is less than start time"
                }
            }, () => {
            })
            isTimeLess = true
        }
        let validationSchema = Yup.object().shape({
            scheduleN: stringRequiredValidationHelper("schedule name")
                .matches(
                    /^[a-zA-Z0-9:_-]+$/,
                    "The name can only consist of alphabetical,number,underscore,hyphen and colon"
                ),
            selectedP: objectRequiredValidationHelper("child profile"),
            selectedPfCp: objectRequiredValidationHelper("portfolio/campaign"),
            selectedPfsCps: objectRequiredValidationHelper("portfolios/campaigns"),
            stTime: stringRequiredValidationHelper("start time"),
            edTime: stringRequiredValidationHelper("end time"),
        });

        let dataToValidateObject = {
            scheduleN: this.state.scheduleName,
            selectedP: this.state.selectedProfile,
            selectedPfCp: this.state.selectedPfCampaign,
            selectedPfsCps: this.state.selectedPortfoliosCampaigns,
            stTime: this.state.startTime,
            edTime: this.state.endTime,
        };

        let validationFormData = htk.validateAllFields(validationSchema, dataToValidateObject);

        if (Object.size(validationFormData) > 0 || isTimeLess) {
            const {errors} = this.state;
            $.each(validationFormData, function (indexInArray, valueOfElement) {
                errors[indexInArray] = valueOfElement;
            });
            this.setState((prevState) => ({
                errors: errors
            }));
        } else {

            let scheduleName = this.state.scheduleName;
            let profile = this.state.selectedProfile;
            let selectedPfCampaign = this.state.selectedPfCampaign;
            let selectedPortfoliosCampaigns = this.state.selectedPortfoliosCampaigns;
            let mon = this.state.mon;
            let tue = this.state.tue;
            let wed = this.state.wed;
            let thu = this.state.thu;
            let fri = this.state.fri;
            let sat = this.state.sat;
            let sun = this.state.sun;
            let startTime = moment(this.state.startTime).format('hh:mm A');
            let endTime = moment(this.state.endTime).format('hh:mm A');
            let ccEmails = this.state.ccEmail;
            let emailReceiptStart = this.state.emailReceiptStart;
            let emailReceiptEnd = this.state.emailReceiptEnd;
            let reccuringSchedule = this.state.reccuringSchedule;

            let params = {
                'scheduleName': scheduleName,
                'fkProfileId': breakProfileId(profile.value),
                'portfolioCampaignType': selectedPfCampaign.value,
                'pfCampaigns': selectedPortfoliosCampaigns.map(item => {
                    return item.value;
                }),
                'mon': mon,
                'tue': tue,
                'wed': wed,
                'thu': thu,
                'fri': fri,
                'sat': sat,
                'sun': sun,
                'startTime': startTime,
                'endTime': endTime,
                'ccEmails': ccEmails,
                'emailReceiptStart': emailReceiptStart,
                'emailReceiptEnd': emailReceiptEnd,
                'reccuringSchedule': reccuringSchedule
            }

            if (params != null) {
                storeDayPartingForm(params, (data) => {

                        if (data.ajax_status != false) {

                            this.setState({
                                scheduleName: "",
                                selectedProfile: null,
                                selectedPfCampaign: null,
                                selectedPortfoliosCampaigns: null,
                                PortfoliosCampaignsOptions: [],
                                mon: false,
                                tue: false,
                                wed: false,
                                thu: false,
                                fri: false,
                                sat: false,
                                sun: false,
                                startTime: new Date(),
                                endTime: new Date(),
                                emailReceiptStart: false,
                                emailReceiptEnd: false,
                                reccuringSchedule: false,
                                ccEmail: [],
                                items: [],
                                isDataTableReload: true,
                                reset: true
                            }, () => {
                                this.setState({
                                    reset: false
                                })
                            });
                            this.props.dispatch(ShowSuccessMsg(data.success, "Successfully", true, "", this.props.updateDataTableAfteSubmit()));

                        } else {

                            this.props.dispatch(ShowFailureMsg("", "", true, "", list(data.error)))
                        }
                    }
                )
            }
        }
    }

    resetErrors = (key) => {
        let {errors} = this.state;
        errors[key] = ""
        this.setState({
            ...errors
        })
    }

    render() {
        return (
            <MuiPickersUtilsProvider utils={MomentUtils}>
                <div className="p-5 rounded-lg">
                    <form>
                        <Grid container justify="center" spacing={3}>
                            <Grid item xs={12} sm={6} md={6} lg={6}
                                  className="dayPartingGridElement">
                                <label className="inline-block mb-2 ml-2 text-sm">
                                    Schedule Name <span className="required-asterisk">*</span>
                                </label>

                                <TextFieldInput
                                    placeholder="Schedule Name"
                                    id="dr"
                                    type="text"
                                    className="dayPartingTextField rounded-full bg-white"
                                    name="scheduleName"
                                    value={this.state.scheduleName}
                                    onChange={this.onChangeText}
                                    fullWidth={true}
                                />
                                <div className="error pl-2">{this.state.errors.scheduleN}</div>
                            </Grid>
                            <Grid item xs={12} sm={6} md={6} lg={6}
                                  className="dayPartingGridElement">
                                <label className="text-sm  ml-2">
                                    Child Brand <span className="required-asterisk">*</span>
                                </label>
                                <SingleSelect
                                    placeholder="Child Brand"
                                    id="cbn"
                                    name={"selectedProfile"}
                                    value={this.state.selectedProfile}
                                    onChangeHandler={this.onProfileChange}
                                    fullWidth={true}
                                    Options={this.state.profileOptions}
                                    isClearable={false}
                                />
                                <div className="error pl-2">{this.state.errors.selectedP}</div>
                            </Grid>
                            <Grid item xs={12} sm={6} md={6} lg={6}
                                  className="dayPartingGridElement">
                                <label className="text-sm  ml-2">
                                    Portfolio/Campaign <span className="required-asterisk">*</span>
                                </label>
                                <SingleSelect
                                    placeholder="Portfolio/Campaign"
                                    id="sc"
                                    name="selectedCampaign"
                                    value={this.state.selectedPfCampaign}
                                    onChangeHandler={this.onPfCampaignChange}
                                    Options={this.state.selectedPfCampaignOption}
                                    fullWidth={true}
                                    isClearable={false}
                                />
                                <div className="error pl-2">{this.state.errors.selectedPfCp}</div>
                            </Grid>
                            <Grid item xs={12} sm={6} md={6} lg={6}
                                  className="dayPartingGridElement autoScrl">
                                <label className="text-sm  ml-2">
                                    Portfolios/Campaigns <span className="required-asterisk">*</span>
                                </label>
                                <MultiSelect
                                    placeholder="Portfolios/Campaigns"
                                    id="pt"
                                    name="text"
                                    value={this.state.selectedPortfoliosCampaigns}
                                    onChangeHandler={this.onPortfoliosCampaignsChange}
                                    Options={this.state.PortfoliosCampaignsOptions}
                                />
                                <div className="error pl-2">{this.state.errors.selectedPfsCps}</div>
                            </Grid>
                            <Grid className="flex items-center dayPartingCheckBoxes" item xs={12} sm={6} md={4} lg={12}>
                                <label className="text-sm  ml-2 mr-10">
                                    Days of week
                                </label>

                                <CheckBox
                                    label="Mon"
                                    name={"mon"}
                                    checked={this.state.mon}
                                    onChange={this.onCheckBoxesChangeHandler}

                                />
                                <CheckBox
                                    label="Tue"
                                    checked={this.state.tue}
                                    onChange={this.onCheckBoxesChangeHandler}
                                    name="tue"
                                />
                                <CheckBox
                                    label="Wed"
                                    className="bg-white"
                                    checked={this.state.wed}
                                    onChange={this.onCheckBoxesChangeHandler}
                                    name="wed"
                                />
                                <CheckBox
                                    label="Thu"
                                    className="bg-white"
                                    checked={this.state.thu}
                                    onChange={this.onCheckBoxesChangeHandler}
                                    name="thu"
                                />
                                <CheckBox
                                    label="Fri"
                                    className="bg-white"
                                    checked={this.state.fri}
                                    onChange={this.onCheckBoxesChangeHandler}
                                    name="fri"
                                />
                                <CheckBox
                                    label="Sat"
                                    className="bg-white"
                                    checked={this.state.sat}
                                    onChange={this.onCheckBoxesChangeHandler}
                                    name="sat"
                                />
                                <CheckBox
                                    label="Sun"
                                    className="bg-white"
                                    checked={this.state.sun}
                                    onChange={this.onCheckBoxesChangeHandler}
                                    name="sun"
                                />
                            </Grid>
                            <Grid item xs={12} sm={6} md={6} lg={6}
                                  className={this.state.errors.stTime.length > 0 ? 'error' : ''}>
                                <label className="text-sm  ml-2">
                                    Start Time <span className="required-asterisk">*</span> <sup className="font-mono">UTC
                                    -7</sup>
                                </label>

                                <TimePicker
                                    className="timePickerElement"
                                    name="startTime"
                                    variant="outlined"
                                    fullWidth={true}
                                    value={this.state.startTime}
                                    onChange={this.onStartDateChangeHandler}
                                />
                                <div className="error pl-3">{this.state.errors.stTime}</div>
                            </Grid>
                            <Grid item xs={12} sm={6} md={6} lg={6}
                                  className={this.state.errors.edTime.length > 0 ? 'error' : ''}>
                                <label className="text-sm  ml-2">
                                    End Time <span className="required-asterisk">*</span> <sup className="font-mono">UTC
                                    -7</sup>
                                </label>
                                <TimePicker
                                    className="timePickerElement"
                                    name="endTime"
                                    inputVariant="standard"
                                    variant="outlined"
                                    fullWidth={true}
                                    value={this.state.endTime}
                                    onChange={this.onEndDateChangeHandler}
                                />
                                <div className="error pl-3">{this.state.errors.edTime}</div>
                            </Grid>
                            <Grid className="timePickerDayPartingElement" item xs={12} sm={12} md={12} lg={12}>
                                <label className="text-sm  ml-2 inline-block mb-2">
                                    Cc Email
                                </label>
                                <CcEmail items={[]} isReset={this.state.reset} getUpdatedItems={this.getUpdatedItems}>

                                </CcEmail>

                            </Grid>
                            <Grid container xs={12} >
                                <Grid item xs={6} sm={6} md={6} lg={6} className="flex items-center dayPartingCheckBoxes">
                                    <label className="text-sm  ml-2 mr-10">
                                        Email Receipts
                                    </label>
                                    <CheckBox
                                        label="Start"
                                        checked={this.state.emailReceiptStart}
                                        onChange={this.onCheckBoxesChangeHandler}
                                        name="emailReceiptStart"
                                    />
                                    <CheckBox
                                        label="End"
                                        checked={this.state.emailReceiptEnd}
                                        onChange={this.onCheckBoxesChangeHandler}
                                        name="emailReceiptEnd"
                                    />                                   
                                </Grid>
                                <Grid item xs={6} className="flex items-center dayPartingCheckBoxes">
                                    <label className="text-sm  ml-10 mr-10">
                                        Recurring Schedule
                                    </label>
                                    <CheckBox
                                        checked={this.state.reccuringSchedule}
                                        onChange={this.onCheckBoxesChangeHandler}
                                        name="reccuringSchedule"
                                    />
                                </Grid>
                            </Grid>
                        </Grid>
                        <div className="flex mt-8 justify-end">
                            <PrimaryButton
                                variant={"contained"}
                                btntext="submit"
                                onClick={this.formSubmissionDayParting}
                            />
                        </div>
                    </form>
                </div>
            </MuiPickersUtilsProvider>
        );
    }
}

export default withStyles(styles)(connect(null)(AddFormDayParting))