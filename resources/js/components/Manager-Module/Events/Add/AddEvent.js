import React, {Component} from 'react'
import clsx from 'clsx';
import './addEvent.scss';
import {connect} from "react-redux"
import SingleSelect from "./../../../../general-components/Select";
import TextButton from "./../../../../general-components/TextButton";
import PrimaryButton from "./../../../../general-components/PrimaryButton";
import {ShowSuccessMsg} from "./../../../../general-components/successDailog/actions";
import {ShowFailureMsg} from "./../../../../general-components/failureDailog/actions";
import {withStyles} from "@material-ui/core/styles";
import {primaryColor, primaryColorLight} from "./../../../../app-resources/theme-overrides/global";
import TextFieldInput from "./../../../../general-components/Textfield";
import moment from "moment";
import LinearProgress from '@material-ui/core/LinearProgress';
import {getAccountAsins, addEventLogs, getEventLog, getProfileAsins, getEventsData} from './apiCalls';
import * as Yup from 'yup';
import {
    stringRequiredValidationHelper,
    objectRequiredValidationHelper,
    arrayRequiredValidationHelper,
} from './../../../../helper/yupHelpers';
import EventsCheckBox from './../EventsCheckBox';
import CustomDateRangePicker from './../CustomDateRangePicker';
import SelectAsync from './../../../../general-components/SelectAsync';
import TextareaAutosize from '@material-ui/core/TextareaAutosize';

const useStyles = theme => ({
    root: {
        '& .MuiInputBase-root': {
            marginTop: 8,
            borderRadius: 12,
            border: "1px solid #c3bdbd8c",
            height: 35,
            background: '#fff'
        },
        "&:hover .MuiInputBase-root": {
            borderColor: primaryColorLight,
            borderRadius: "12px",
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
    childBrand: objectRequiredValidationHelper("child Brand"),
    marketPlaceId: objectRequiredValidationHelper("Marketplace Id"),
    ASIN: objectRequiredValidationHelper("ASIN"),
    dateRange: stringRequiredValidationHelper("Date Range"),
    events: arrayRequiredValidationHelper("Events"),
};

const validationSchema = Yup.object().shape(ValidateSchemaObject);
const marketPlaceIDs = [
    {label: "US", value: "US", className: 'custom-class'},
];
const getProfileName = (account) => {
    return account.brand_alias.length > 0 && account.brand_alias[0].overrideLabel ? account.brand_alias[0].overrideLabel : account.attr2;
}

class AddEvent extends Component {
    constructor(props) {
        super(props);
        this.state = {
            childBrand: null,
            marketPlaceId: null,
            ASIN: null,
            dateRange: "",
            dateRangeObj: {
                startDate: new Date(),
                endDate: new Date(),
                key: 'selection',
            },
            showDRP: false,
            events: [],
            andon: false,
            crap: false,
            andonTextarea: false,
            crapTextarea: false,
            eventsAndonNote: "",
            eventsCrapNote: "",
            options: {
                childBrands: [],
                asins: [],
            },
            loaders: {
                isAsinLoading: false,
            },
            form: {
                isFormLoading: true,
                loadingText: "Loading..."
            },
            errors: {
                childBrand: "",
                marketPlaceId: "",
                ASIN: "",
                dateRange: "",
                events: "",
                andon: "",
                crap: "",
            },
        }//end state

        this.eventsTextareaRef = React.createRef();
    }

    componentDidMount() {
        if (this.props.id) {
            getEventLog({id: this.props.id}, (response) => {
                let asinOptions = getProfileAsins(response.asins);
                let selectedEventLog = response.data;
                this.setState((prevState) => ({
                    childBrand: response.accounts.filter(row => {
                        return row.value == selectedEventLog.fkAccountId
                    })[0],
                    marketPlaceId: marketPlaceIDs.filter(row => {
                        return row.value == "US"
                    })[0],
                    ASIN: asinOptions.filter(row => {
                        return row.value == selectedEventLog.asin
                    })[0],
                    dateRange: selectedEventLog.occurrenceDate,
                    events: [selectedEventLog.fkEventId],
                    eventsAndonNote: selectedEventLog.fkEventId == 4 ? selectedEventLog.notes == "NA" ? "" : selectedEventLog.notes : "",
                    eventsCrapNote: selectedEventLog.fkEventId == 5 ? selectedEventLog.notes == "NA" ? "" : selectedEventLog.notes : "",
                    andonTextarea: selectedEventLog.fkEventId == 4,
                    crapTextarea: selectedEventLog.fkEventId == 5,
                    options: {
                        options: {...prevState.options, childBrands:this.formateProfiles(response.accounts)},
                        asins: asinOptions,
                    },
                    andon: selectedEventLog.fkEventId == 4,
                    crap: selectedEventLog.fkEventId == 5,
                    dateRangeObj: {
                        startDate: new Date(),
                        endDate: new Date(),
                        key: 'selection',
                    },
                    form: {
                        isFormLoading: false,
                        loadingText: "Loading..."
                    },
                }))
            }, (error) => {
                console.log(error);
            });
        } else {
            getEventsData((response)=>{
                this.setState((prevState)=>({
                    options: {...prevState.options, childBrands:this.formateProfiles(response.accounts)},
                    form: {
                        isFormLoading: false,
                        loadingText: "Loading..."
                    },
                }));
            },(error)=>{
                console.log(error);
            })
        }
        document.addEventListener('click', this.handleClickOutside);
    }
    componentWillUnmount() {
        document.removeEventListener('click', this.handleClickOutside);
    }
    formateProfiles = (accounts) => {
        return accounts.map((profile) => {
            return {label: getProfileName(profile), value: profile.attr1, className: 'custom-class'}
        });
    }
    handleClickOutside = (event) => {
        if (this.eventsTextareaRef && !this.eventsTextareaRef.current.contains(event.target) && this.props.id == 0) {
            this.setState({
                andonTextarea: false,
                crapTextarea: false
            })
        }
    }
    helperGetEventsIds = (events, andon, crap, current) => {
        this.setState((prevState) => ({
            errors: {
                ...prevState.errors,
                events: ""
            },
            events,
            andon,
            crap,
        }));
        if (current == "andon" && andon) {
            this.setState({
                andonTextarea: true,
            })
        } else {
            this.setState({
                andonTextarea: false,
            })
        }
        if (current == "crap" && crap) {
            this.setState({
                crapTextarea: true,
            })
        } else {
            this.setState({
                crapTextarea: false,
            })
        }
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
            if (name == "childBrand" && this.state.childBrand) {
                this.setState({
                    loaders: {
                        isAsinLoading: true,
                    }
                }, () => {
                    this.handleChildeBrandChange();
                })
            } else if (name == "childBrand" && this.state.childBrand == null) {
                this.setState((prevState) => ({
                    options: {
                        ...prevState.options,
                        asins: [],
                    },
                    loaders: {
                        isAsinLoading: false,
                    }
                }));
            }
        });
    }
    handleChildeBrandChange = () => {
        let accountId = this.state.childBrand.value;

        getAccountAsins({accountId}, (asinOptions) => {
            this.setState((prevState) => ({
                options: {
                    ...prevState.options,
                    asins: asinOptions,
                },
                ASIN: null,
                loaders: {
                    isAsinLoading: false,
                }
            }));
        }, (error) => {
            console.log(error)
            this.setState({
                loaders: {
                    isAsinLoading: false,
                }
            });
        })

    }
    handleOnDateRangeClick = (e) => {
        this.setState({
            showDRP: true
        })
    }
    helperCloseDRP = (event) => {
        this.setState({
            showDRP: false
        })
    }
    getValue = (range) => {
        this.setState((prevState) => ({
            dateRangeObj: range,
            errors: {
                ...prevState.errors,
                dateRange: ""
            },
            dateRange: moment(range.startDate).format('YYYY-MM-DD') + " - " + moment(range.endDate).format('YYYY-MM-DD'),
            showDRP: false
        }))
    }
    setSingleDate = (date) => {
        this.setState((prevState) => ({
            errors: {
                ...prevState.errors,
                dateRange: ""
            },
            dateRange: moment(date).format('YYYY-MM-DD'),
            showDRP: false
        }))
    }
    helperSetValidationErrorState = (errors, allValiditionFrom) => {
        $.each(allValiditionFrom, function (indexInArray, valueOfElement) {
            errors[indexInArray] = valueOfElement;
        });
        this.setState((prevState) => ({
            errors
        }));
    }
    helperResetErrors = () => {
        const {errors} = this.state;
        $.each(this.state.errors, function (indexInArray, valueOfElement) {
            errors[indexInArray] = "";
        });
        return errors;
    }
    handleOnEventNotesChange = (e) => {
        if (e.target && e.target.value.length > 100) {
            this.setState((prevState) => ({
                errors: {
                    ...prevState.errors,
                    [event.target.name]: "Max 100 characters allowed",

                },
            }))
        } else {
            if (e.target.value.length <= 100 && this.state.errors[event.target.name] != "") {
                this.setState((prevState) => ({
                    errors: {
                        ...prevState.errors,
                        [event.target.name]: "",
                    },
                }))
            }//endif
        }
        this.setState((prevState) => ({
            [event.target.name == "andon" ? "eventsAndonNote" : "eventsCrapNote"]: event.target.value
        }));
    }
    handleAddEventFormSubmit = () => {
        if (this.props.id != 0) {
            if (this.state.errors.andon == "" && this.state.errors.crap == "") {
                let ajaxData = {
                    id: this.props.id,
                    fkEventIds: this.state.events.join(","),
                    eventsNotes: [this.state.eventsAndonNote.trim() == "" ? "NA" : this.state.eventsAndonNote, this.state.eventsCrapNote.trim() == "" ? "NA" : this.state.eventsCrapNote],
                    operationType: this.props.id != 0 ? "edit" : "add"
                }
                this.manageEevent(ajaxData);
            }
            return;
        } else {
            let dataToValidatebject = {
                childBrand: this.state.childBrand,
                marketPlaceId: this.state.marketPlaceId,
                ASIN: this.state.ASIN,
                dateRange: this.state.dateRange,
                events: this.state.events,
            };
            let allValiditionFrom = htk.validateAllFields(validationSchema, dataToValidatebject);
            if (Object.size(allValiditionFrom) > 0) {
                let resetErrors = this.helperResetErrors();
                this.helperSetValidationErrorState(resetErrors, allValiditionFrom);
            } else {
                if (this.state.errors.andon == "" && this.state.errors.crap == "") {
                    let start = this.state.dateRange.split(" - ")[0];
                    let end = this.state.dateRange.split(" - ")[1];
                    start = moment(start, "YYYY-MM-DD")
                    end = moment(end, "YYYY-MM-DD")
                    let diff = end.diff(start, "days");
                    let occurrenceDates = [];
                    occurrenceDates.push(start.format('YYYY-MM-DD'));
                    for (let index = 1; index <= diff; index++) {
                        let date = moment(start, "YYYY-MM-DD").add(index, 'days');
                        occurrenceDates.push(date.format('YYYY-MM-DD'));
                    }
                    let ajaxData = {
                        id: this.props.id,
                        childBrand: this.state.childBrand.value,
                        asin: this.state.ASIN.value,
                        occurrenceDates: occurrenceDates,
                        fkEventIds: this.state.events.join(","),
                        eventsNotes: [this.state.eventsAndonNote.trim() == "" ? "NA" : this.state.eventsAndonNote, this.state.eventsCrapNote.trim() == "" ? "NA" : this.state.eventsCrapNote],
                        operationType: "add"
                    }
                    this.manageEevent(ajaxData);
                }
            }
        }
    }
    manageEevent = (ajaxData) => {
        this.setState({
            form: {
                isFormLoading: true,
                loadingText: "Processing..."
            },
        });
        addEventLogs(
            ajaxData,
            (response) => {
                this.props.dispatch(ShowSuccessMsg("Successfull", response.message, true, "", this.props.heloperReloadDataTable()));
            }, (error) => {
                this.setState((prevState) => ({
                    form: {
                        isFormLoading: false,
                        loadingText: "Processing..."
                    },
                }));
                this.props.dispatch(ShowFailureMsg(error, "", true, ""));
            })
    }

    render() {
        const {classes} = this.props;
        return (
            <>
                <div className="graphLoader bg-white absolute h-full overflow-hidden w-full top-0 left-0 z-10"
                     style={this.state.form.isFormLoading ? {display: "block"} : {display: "none"}}>
                    <LinearProgress/>
                    <div
                        className="absolute flex font-bold font-mono h-full items-center justify-center overflow-hidden text-1rem text-sm w-full z-10">
                        {this.state.form.loadingText}
                    </div>
                </div>
                <div className="px-12 relative manageEventForm">
                    <div
                        className={clsx("ChildBrand", this.props.id != 0 ? "disabledElement" : "", this.state.errors.childBrand.length > 0 ? "errorCustom" : "")}>
                        <label className="text-xs font-normal ml-2">
                            Child Brand <span className="font-black text-red-500 text-sm">*</span>
                        </label>
                        <SingleSelect
                            placeholder="Please Select Child Brand"
                            name={"childBrand"}
                            value={this.state.childBrand}
                            onChangeHandler={this.onChangeHandler}
                            fullWidth={true}
                            Options={this.state.options.childBrands}
                            styles={customStyle}
                            customClassName="ThemeSelect"
                            id="childBrand"
                            isDisabled={this.props.id != 0}
                        />
                        <div className="error pl-3">{this.state.errors.childBrand}</div>

                    </div>
                    <div
                        className={clsx("MarketPlaceId pt-5", this.props.id != 0 ? "disabledElement" : "", this.state.errors.marketPlaceId.length > 0 ? "errorCustom" : "")}>
                        <label className="text-xs font-normal ml-2">
                            Marketplace Id <span className="font-black text-red-500 text-sm">*</span>
                        </label>
                        <SingleSelect
                            placeholder="MarketPlaceId"
                            name={"marketPlaceId"}
                            value={this.state.marketPlaceId}
                            onChangeHandler={this.onChangeHandler}
                            fullWidth={true}
                            Options={marketPlaceIDs}
                            styles={customStyle}
                            customClassName="ThemeSelect"
                            id="marketPlaceId"
                            isDisabled={this.props.id != 0}
                        />
                        <div className="error pl-3">{this.state.errors.marketPlaceId}</div>
                    </div>
                    <div
                        className={clsx("ASIN pt-5", this.props.id != 0 ? "disabledElement" : "", this.state.errors.ASIN.length > 0 ? "errorCustom" : "")}>
                        <label className="text-xs font-normal ml-2">
                            ASIN <span className="font-black text-red-500 text-sm">*</span>
                        </label>
                        <SelectAsync
                            placeholder="Select product"
                            name={"ASIN"}
                            value={this.state.ASIN}
                            onChangeHandler={this.onChangeHandler}
                            fullWidth={true}
                            Options={this.state.options.asins}
                            styles={customStyle}
                            labelLimit="30"
                            customClassName="ThemeSelect"
                            id="ASIN"
                            isDisabled={this.props.id != 0}
                            isLoading={this.state.loaders.isAsinLoading}
                        />
                        <div className="error pl-3">{this.state.errors.ASIN}</div>
                    </div>
                    <div
                        className={clsx("DateRange pt-5", this.props.id != 0 ? "disabledElement" : "", this.state.errors.dateRange.length > 0 ? "errorCustom" : "")}>
                        <label className="text-xs font-normal ml-2">
                            Date {this.props.id == 0 ? "Range" : null} <span
                            className="font-black text-red-500 text-sm">*</span>
                        </label>
                        <div className="ThemeInput dateRange"
                             onClick={this.props.id == 0 ? this.handleOnDateRangeClick : null}>
                            <TextFieldInput
                                placeholder="Select Date Range"
                                type="text"
                                id="dateRange"
                                name={"dateRange"}
                                value={this.state.dateRange}
                                fullWidth={true}
                                classesstyle={classes}
                                disabled
                            />
                            <div className="error pl-3">{this.state.errors.dateRange}</div>
                        </div>
                        {this.state.showDRP ?
                            <CustomDateRangePicker 
                                range={this.state.dateRangeObj}
                                helperCloseDRP={this.helperCloseDRP}
                                setSingleDate={this.setSingleDate}
                                date={this.state.dateRange}
                                getValue={this.getValue} 
                                direction="vertical"
                                isDateRange={this.props.id == 0}
                            />
                            : null}
                    </div>
                    <div className={clsx("EventsCheckbox pt-5", this.props.id != 0 ? "disabledElement" : "", this.state.errors.events.length > 0 ? "errorCustom" : "")}>
                        <div className="ThemeInput relative" ref={this.eventsTextareaRef}>
                            {
                                this.props.id != 0 ?
                                    <label className="text-xs font-normal ml-2 mb-2 block">Notes</label> : null
                            }
                            <div
                                className={clsx("bg-white left-0 notesAndonContainer mb-4 overflow-hidden top-0 z-10", this.props.id == 0 ? "absolute shadow-lg" : "mx-1")}
                                style={this.state.andonTextarea ? {display: "block"} : {display: "none"}}>
                                <TextareaAutosize onChange={this.handleOnEventNotesChange}
                                                  className="andonTextArea px-2 text-gray-700 w-full" name="andon"
                                                  value={this.state.eventsAndonNote} aria-label="minimum height"
                                                  rowsMin={3} placeholder="Minimum 100 Characters"/>
                                <div className="error px-2 smallSizeError text-center">{this.state.errors.andon}</div>
                            </div>
                            <div
                                className={clsx("notesCrapContainer mb-4 overflow-hidden top-0 right-0 bg-white z-10", this.props.id == 0 ? "absolute shadow-lg" : "mx-1")}
                                style={this.state.crapTextarea ? {display: "block"} : {display: "none"}}>
                                <TextareaAutosize onChange={this.handleOnEventNotesChange}
                                                  className="px-2 crapTextArea text-gray-700 w-full"
                                                  aria-label="minimum height" name="crap" rowsMin={3}
                                                  value={this.state.eventsCrapNote}
                                                  placeholder="Minimum 100 Characters"/>
                                <div className="error px-2 smallSizeError text-center">{this.state.errors.crap}</div>
                            </div>
                            <EventsCheckBox
                                disabled={this.props.id != 0} helperGetEventsIds={this.helperGetEventsIds}
                                andon={this.state.andon} crap={this.state.crap}/>
                            <div className="error text-center">{this.state.errors.events}</div>
                        </div>
                    </div>
                    <div className="flex float-right items-center justify-center my-5 w-full">
                        <div className="mr-3">
                            <TextButton
                                BtnLabel={"Cancel"}
                                color="primary"
                                onClick={this.props.handleModalClose}/>
                        </div>
                        <PrimaryButton
                            btnlabel={this.props.id == 0 ? "Save" : "Update"}
                            variant={"contained"}
                            onClick={this.handleAddEventFormSubmit}
                        />
                    </div>
                </div>
            </>
        )
    }
}

export default withStyles(useStyles)(connect(null)(AddEvent))
