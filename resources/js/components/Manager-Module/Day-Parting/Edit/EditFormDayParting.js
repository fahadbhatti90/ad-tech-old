import React, {Component} from 'react';
import Card from '@material-ui/core/Card';
import Typography from '@material-ui/core/Typography';
import {withStyles} from "@material-ui/core/styles";
import {styles} from "../styles";
import {getCampaigns, showEditPartingForm, storeEditDayPartingForm} from '../apiCalls'
import moment from "moment";
import {connect} from 'react-redux';
import MomentUtils from "@date-io/moment";
import {Grid} from "@material-ui/core";
import TextFieldInput from "./../../../../general-components/Textfield";
import SingleSelect from "./../../../../general-components/Select";
import MultiSelect from "./../../../../general-components/MultiSelect";
import CheckBox from "./../../../../general-components/CheckBox";
import {MuiPickersUtilsProvider, TimePicker} from "@material-ui/pickers";
import CcEmail from "./../../../../general-components/EmailCC/EmailChips";
import Button from "@material-ui/core/Button";
import {breakProfileId, requiredValidationHelper, breakCampaignName} from "./../../../../helper/helper";
import DayPartingModal from "../DayPartingModal";
import PortfolioCampaignRemove from "./DeleteEditScheduleOptions";
import * as Yup from "yup";
import {ShowSuccessMsg} from "./../../../../general-components/successDailog/actions";
import {ShowFailureMsg} from "./../../../../general-components/failureDailog/actions";
import TextButton from "./../../../../general-components/TextButton";
import PrimaryButton from "./../../../../general-components/PrimaryButton";
import {objectRequiredValidationHelper, stringRequiredValidationHelper} from "./../../../../helper/yupHelpers";

const list = (errorsData) => {

    return (
        <>
            {errorsData.map(item => (
                <li className="list-none"> {item} </li>
            ))}
        </>
    )
};

class EditFormDayParting extends Component {
    constructor(props) {
        super(props);
        this.state = {
            id: '',
            genericShowError: false,
            scheduleName: "",
            scheduleNameError: '',
            selectedProfile: null,
            selectedPfCampaign: null,
            tempSelectedPfCampaign: '',
            selectedPfCampaignError: '',
            oldPortfolioCampaignType: null,
            selectedPfCampaignOption: [],
            selectedPortfoliosCampaigns: null,
            selectedPortfoliosCampaignsError: '',
            PortfoliosCampaignsOptions: [],
            tempPortfoliosCampaignsOptions: [],
            tempPortfoliosCampaigns: [],
            removePortfoliosCampaigns: [],
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
            modalTitle: "",
            modalBody: "",
            maxWidth: "sm",
            callback: 0,
            preSelectEmail: [],
            scheduleCountPopup: 1,
            campaignOptionSelected: 1,
            errors: {
                scheduleN: "",
                selectedPfCp: "",
                selectedPfsCps: "",
                stTime: "",
                edTime: "",
            }
        }
    }

    componentDidMount = () => {
        let id = this.props.id;
        let editParams = {
            'scheduleId': id
        }

        showEditPartingForm(editParams, (response) => {

            let allPortfolios = response.allPortfolios;
            let allCampaigns = response.allCampaignListRecord;
            let selectedCampaigns = response.allScheduleData.campaigns;
            let selectedPortfolios = response.allScheduleData.portfolios;
            let scheduleData = response.allScheduleData;

            if (scheduleData.portfolioCampaignType == 'Campaign') {
                if (allCampaigns.length > 0) {
                    let campaigns = allCampaigns.map((obj, idx) => {
                        return {
                            value: obj.id + '|' + obj.name,
                            label: obj.name
                        }
                    })

                    this.setState({
                        PortfoliosCampaignsOptions: campaigns,
                        tempPortfoliosCampaignsOptions: campaigns,
                        selectedPfCampaign: scheduleData.portfolioCampaignType
                    })
                }

                if (selectedCampaigns.length > 0) {
                    let selectedCamp = selectedCampaigns.map((obj, idx) => {
                        return {
                            value: obj.id + '|' + obj.name,
                            label: obj.name
                        }
                    })

                    this.setState({
                        selectedPortfoliosCampaigns: selectedCamp,
                        tempPortfoliosCampaigns: selectedCamp
                    })
                }
            }

            if (scheduleData.portfolioCampaignType == 'Portfolio') {
                if (allPortfolios.length > 0) {

                    let portfolios = allPortfolios.map((obj, idx) => {
                        return {
                            value: obj.id + '|' + obj.name,
                            label: obj.name
                        }
                    })

                    this.setState({
                        PortfoliosCampaignsOptions: portfolios,
                        tempPortfoliosCampaignsOptions: portfolios,
                        selectedPfCampaign: scheduleData.portfolioCampaignType
                    })
                }

                if (selectedPortfolios.length > 0) {
                    let selectedPort = selectedPortfolios.map((obj, idx) => {
                        return {
                            value: obj.id + '|' + obj.name,
                            label: obj.name,
                            selected: 'selected'
                        }
                    })

                    this.setState({
                        selectedPortfoliosCampaigns: selectedPort,
                        tempPortfoliosCampaigns: selectedPort
                    })
                }
            }

            this.setState({
                id: id,
                scheduleName: scheduleData.scheduleName,
                selectedPfCampaign: {
                    value: scheduleData.portfolioCampaignType,
                    label: scheduleData.portfolioCampaignType,
                    selected: 'selected'
                },
                selectedPfCampaignOption: [
                    {
                        value: "Campaign", label: 'Campaign'
                    },
                    {value: "Portfolio", label: 'Portfolio'}
                ],
                tempSelectedPfCampaign: {
                    value: scheduleData.portfolioCampaignType,
                    label: scheduleData.portfolioCampaignType
                },
                oldPortfolioCampaignType: scheduleData.portfolioCampaignType,
                selectedProfile: scheduleData.fkProfileId,
                mon: (scheduleData.mon == 1),
                tue: (scheduleData.tue == 1),
                wed: (scheduleData.wed == 1),
                thu: (scheduleData.thu == 1),
                fri: (scheduleData.fri == 1),
                sat: (scheduleData.sat == 1),
                sun: (scheduleData.sun == 1),
                startTime: moment(scheduleData.startTime, [moment.ISO_8601, 'HH:mm']),
                endTime: moment(scheduleData.endTime, [moment.ISO_8601, 'HH:mm']),
                emailReceiptStart: (scheduleData.emailReceiptStart == 1),
                emailReceiptEnd: (scheduleData.emailReceiptEnd == 1),
                reccuringSchedule: (scheduleData.reccuringSchedule == 1),
                ccEmail: (scheduleData.ccEmails.length > 0) ? scheduleData.ccEmails.split(',') : [],
            })
        })
    }

    checkIfDataIsAvailable = (temp, actualValue) => {
        let getRemovedValues = [];
        //if (temp && actualValue && actualValue.length > 0) {
        if (temp) {
            getRemovedValues = temp.filter(function (el) {
                let isExists = false;
                $.each(actualValue, function (indexInArray, valueOfElement) {
                    if (isExists) return;
                    isExists = el.value == valueOfElement.value;
                });
                return !isExists;
            });
        }
        return getRemovedValues;
    }
    makeRemovableCampaigns = (temp, actualValue) => {

        if (temp) {
            let getRemovedValues = temp.filter(function (el) {
                let isExists = false;
                $.each(actualValue, function (indexInArray, valueOfElement) {
                    if (isExists) return;
                    isExists = el.value == valueOfElement.value;
                });
                return !isExists;
            });

            this.setState({
                removePortfoliosCampaigns: getRemovedValues
            })
        }
    }
    // This functions calls when Add or Remove Portfolio and campaign
    onPortfoliosCampaignsChange = (value) => {
        if(value && value.length == 0){
            value = null
        }
        let result = this.checkIfDataIsAvailable(this.state.tempPortfoliosCampaigns, value);
        if (result.length > 0 && this.state.scheduleCountPopup == 1) {
            this.setState({
                scheduleCountPopup: this.state.scheduleCountPopup + 1,
                modalTitle: 'Day Parting Schedule',
                maxWidth: 'sm',
                modalBody: <PortfolioCampaignRemove handleModalClose={this.handleModalClose}
                                                    onRadioChange={this.onRadioChange}
                                                    campaignOptionSelected={this.state.campaignOptionSelected}
                                                    removeCampaignsAfterConfirmation={this.removeCampaignsAfterConfirmation}
                />,
                openModal: true,
            }, () => {
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
                    this.makeRemovableCampaigns(this.state.tempPortfoliosCampaigns, value)
                })

            })
        } else {
            this.setState({
                selectedPortfoliosCampaigns: value
            }, () => {
                this.resetErrors("selectedPfsCps");
                this.makeRemovableCampaigns(this.state.tempPortfoliosCampaigns, value)
            })
        }
    }
    onRadioChange = (value) => {
        this.setState({
            campaignOptionSelected: value
        }, () => {

        })
    }
    // Get Campaigns on the basis of profile
    getPortfolioCampaigns = () => {
        let pf = this.state.selectedProfile;
        let cpt = this.state.selectedPfCampaign.value;
        let oldCpt = this.state.oldPortfolioCampaignType.value;

        getCampaigns(pf, cpt, oldCpt, 'edit', (data) => {
            if (data.length > 0) {
                //success
                this.setState({
                    PortfoliosCampaignsOptions: data,
                    tempPortfoliosCampaigns: [],
                })
            } else {
                //this.props.dispatch(ShowFailureMsg("No Data Found ", "Failed", true, ""));
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

    checkIfTypePreSelect = (temp, inputValue) => {
        let isReturn = false;
        if ((inputValue) && inputValue.value == temp) {
            isReturn = true;
        }
        return isReturn;
    }

    onPfCampaignChange = (value) => {

        let returnResult = this.checkIfTypePreSelect(this.state.oldPortfolioCampaignType, value)

        if (!returnResult) {
            // three options pop up will come here
            this.setState({
                modalTitle: 'Day Parting Schedule',
                maxWidth: 'sm',
                modalBody: <PortfolioCampaignRemove handleModalClose={this.handleModalClose}
                                                    onRadioChange={this.onRadioChange}
                                                    campaignOptionSelected={this.state.campaignOptionSelected}
                                                    removeCampaignsAfterConfirmation={this.removeCampaignsAfterConfirmation}/>,
                openModal: true
            }, () => {

                this.setState({
                    selectedPfCampaign: value,

                }, () => {
                    this.resetErrors("selectedPfCp");
                    this.setState({
                        selectedPortfoliosCampaigns: null,
                        PortfoliosCampaignsOptions: null,
                        removePortfoliosCampaigns: [],
                    })
                    this.callPortfolioCampaign();
                })
            })
        } else {
            this.setState({
                selectedPfCampaign: value,
            }, () => {
                this.resetErrors("selectedPfCp");
                this.setState({
                    selectedPortfoliosCampaigns: null,
                    PortfoliosCampaignsOptions: null,
                    removePortfoliosCampaigns: [],
                })
                this.callPortfolioCampaign();
            })
        }
    }
    removeCampaignsAfterConfirmation = () => {
        this.handlePopUpPfCampaignClose();
    }
    callPortfolioCampaign = () => {
        if (this.state.selectedPfCampaign) {
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
        })
    };

    onEndDateChangeHandler = (endTime) => {
        this.setState({
            endTime: endTime
        })
    };

    getUpdatedItems = (items) => {
        this.setState({
            ccEmail: items
        })
    }

    handlePopUpPfCampaignClose = () => {
        this.setState({
            openModal: false,
            modalBody: '',
            maxWidth: 'md'
        })
    }

    handleModalClose = () => {
        this.setState({
            openModal: false,
            modalBody: '',
            maxWidth: 'md',
            scheduleCountPopup: 1,
            selectedPortfoliosCampaigns: this.state.tempPortfoliosCampaigns,
            selectedPfCampaign: this.state.tempSelectedPfCampaign,
            PortfoliosCampaignsOptions: this.state.tempPortfoliosCampaignsOptions
        })
    }

    formSubmissionDayParting = event => {
        event.preventDefault();
        let validationSchema = Yup.object().shape({
            scheduleN: stringRequiredValidationHelper("schedule name")
                .matches(
                    /^[a-zA-Z0-9:_-]+$/,
                    "The name can only consist of alphabetical,number,underscore,hyphen and colon"
                ),
            selectedPfCp: stringRequiredValidationHelper("portfolio/campaign"),
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

        if (Object.size(validationFormData) > 0) {
            const {errors} = this.state;
            $.each(validationFormData, function (indexInArray, valueOfElement) {
                errors[indexInArray] = valueOfElement;
            });
            this.setState((prevState) => ({
                errors: errors
            }));
        } else {

            var removeCampaigns;
            if (this.state.removePortfoliosCampaigns.length > 0) {
                let tempRemove = this.state.removePortfoliosCampaigns.map(item => {
                    return breakCampaignName(item.value);
                });
                removeCampaigns = tempRemove.join(',');
            }
            let id = this.state.id;
            let scheduleName = this.state.scheduleName;
            let profileId = this.state.selectedProfile;
            let selectedPfCampaign = this.state.selectedPfCampaign;
            let oldPortfolioCampaignType = this.state.oldPortfolioCampaignType;
            let selectedPortfoliosCampaigns = this.state.selectedPortfoliosCampaigns;
            let tempPortfoliosCampaigns = this.state.tempPortfoliosCampaigns;


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
            let userSelectionStatus = this.state.campaignOptionSelected;
            let reccuringSchedule = this.state.reccuringSchedule;
            let params = {
                'scheduleId': id,
                'scheduleName': scheduleName,
                'fkProfileId': profileId,
                'portfolioCampaignType': selectedPfCampaign.value,
                'portfolioCampaignEditTypeOldValue': oldPortfolioCampaignType,
                'pfCampaigns': selectedPortfoliosCampaigns.map(item => {
                    return item.value;
                }),
                'removeCampaigns': (removeCampaigns) ? removeCampaigns : null,
                'mon': mon,
                'tue': tue,
                'wed': wed,
                'thu': thu,
                'fri': fri,
                'sat': sat,
                'sun': sun,
                'startTime': startTime,
                'endTime': endTime,
                'campaignOptionSelected': userSelectionStatus,
                'ccEmails': ccEmails,
                'emailReceiptStart': emailReceiptStart,
                'emailReceiptEnd': emailReceiptEnd,
                'reccuringSchedule': reccuringSchedule
            }

            if (params != null) {
                storeEditDayPartingForm(params, (data) => {

                        if (data.ajax_status != false) {

                            this.props.getAllSchedulesFromDb();
                            this.props.handleModalClose();
                            this.props.dispatch(ShowSuccessMsg(data.success, "Successfully", true, ""));

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
        const {classes} = this.props;
        return (
            <MuiPickersUtilsProvider utils={MomentUtils}>
                <div className="bg-gray-100 dayPartingModule pl-5 pr-10 rounded-lg">
                    <form>
                        <Grid container spacing={3}>
                            <Grid className="dayPartingGridElement" item xs={12} sm={6} md={6} lg={6}>
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
                            <Grid className="dayPartingGridElement" item xs={12} sm={6} md={6} lg={6}>
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
                            <Grid className="dayPartingGridElement" item xs={12} sm={6} md={6} lg={6}>
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
                                    name="mon"
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
                            <Grid item xs={12} sm={6} md={6} lg={6}>
                                <label className="text-sm  ml-2">
                                    Start Time <span className="required-asterisk">*</span><sup className="font-mono">UTC
                                    -7</sup>
                                </label>

                                <TimePicker
                                    className="timePickerElement"
                                    name="startTime"
                                    variant="outlined"
                                    fullWidth={true}
                                    value={this.state.startTime}
                                    onChange={this.onStartDateChangeHandler}/>
                                <div className="error pl-3">{this.state.errors.stTime}</div>
                            </Grid>
                            <Grid item xs={12} sm={6} md={6} lg={6}>
                                <label className="text-sm  ml-2">
                                    End Time <span className="required-asterisk">*</span><sup className="font-mono">UTC
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
                                <CcEmail items={this.state.ccEmail} getUpdatedItems={this.getUpdatedItems}>

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
                                            btnlabel={"  Submit  "}
                                            variant={"contained"}
                                            onClick={this.formSubmissionDayParting}/>

                                    </Grid>
                                </Grid>
                            </Grid>

                        </Grid>
                    </form>
                </div>
                <DayPartingModal
                    openModal={this.state.openModal}
                    modalTitle={this.state.modalTitle}
                    callback={this.showThreeOptions}
                    id={this.state.id}
                    handleClose={this.handleModalClose}
                    modalBody={this.state.modalBody}
                    maxWidth={this.state.maxWidth}
                    cancelEvent={this.handleModalClose}
                    fullWidth={true}
                />
            </MuiPickersUtilsProvider>
        );
    }
}

export default withStyles(styles)(connect(null)(EditFormDayParting));