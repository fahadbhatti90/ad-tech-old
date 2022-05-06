import React, {Component} from 'react'
import clsx from 'clsx';
import './AddAccounts.scss';
import {connect} from "react-redux"
import Tooltip from "@material-ui/core/Tooltip";
import SingleSelect from "./../../../../general-components/Select";
import MultiSelect from "./../../../../general-components/MultiSelect";
import TextButton from "./../../../../general-components/TextButton";
import PrimaryButton from "./../../../../general-components/PrimaryButton";
import {ShowSuccessMsg} from "./../../../../general-components/successDailog/actions";
import {ShowFailureMsg} from "./../../../../general-components/failureDailog/actions";
import {withStyles} from "@material-ui/core/styles";
import {primaryColor, primaryColorLight} from "./../../../../app-resources/theme-overrides/global";
import TextFieldInput from "./../../../../general-components/Textfield";
import moment from "moment";
import LinearProgress from '@material-ui/core/LinearProgress';
import {associateAccounts} from './apiCalls';
import * as Yup from 'yup';
import {
    stringRequiredValidationHelper,
    objectRequiredValidationHelper,
    arrayRequiredValidationHelper,
} from './../../../../helper/yupHelpers';
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
    brand: objectRequiredValidationHelper("Brand"),
    amsProfile: objectRequiredValidationHelper("Profile"),
    mwsSeller: objectRequiredValidationHelper("Seller"),
    vcVendor: objectRequiredValidationHelper("VC Vendor"),
};

const validationSchema = Yup.object().shape(ValidateSchemaObject);

class AddAccounts extends Component {
    constructor(props) {
        super(props);
        this.state = {
            options: {
                brands: [],
                amsProfiles: [],
                mwsSellers: [],
                vcVendors: [],
            },
            selectedOptions:{
                brand: null,
                amsProfile: null,
                mwsSeller: null,
                vcVendor: null,
            },
            loaders: {
                isAsinLoading: false,
            },
            form: {
                isFormLoading: true,
                loadingText: "Loading..."
            },
            errors: {
                brand: "",
                amsProfile: "",
                mwsSeller: "",
                vcVendor: "",
            },
        }//end state
    }

    componentDidMount() {
        const brands = this.props.addFormData.brands.map( brand =>(
            {label: brand.name+" ("+brand.email+")", value: brand.id, className: 'custom-class'}
        ));
        const amsProfiles = this.props.addFormData.amsProfiles.map( amsProfile =>(
            {label: amsProfile.name, value: amsProfile.id, className: 'custom-class'}
        ));
        const mwsSellers = this.props.addFormData.mwsSellers.map( mwsSeller =>(
            {label: mwsSeller.merchant_name, value: mwsSeller.mws_config_id, className: 'custom-class'}
        ));
        const vcVendors = this.props.addFormData.vcVendors.map( vcVendor =>(
            {label: vcVendor.vendor_name, value: vcVendor.vendor_id, className: 'custom-class'}
        ));
        this.setState((prevState)=>{
            return {
                options:{
                    ...prevState.options,
                    brands,
                    amsProfiles,
                    mwsSellers,
                    vcVendors,
                },
                form: {
                    isFormLoading: false,
                    loadingText: "Loading..."
                },
            }
        })
        // document.addEventListener('click', this.handleClickOutside);
    }

    onChangeHandler = (value, element) => {
        const {errors} = this.state;
        const name = element.name;
        const errorName = $("input[name='" + name + "']").parents(".ThemeSelect").attr("id");
        errors[errorName] = "";
        this.setState((prevState)=>{
            return {
                selectedOptions:{
                    ...prevState.selectedOptions,
                    [name] : (value)
                },
                errors,
            }
        });
    }
   
    onMultiSelectChangeHandler = (value, name) => {
        const {errors} = this.state;
        errors[name] = "";
        this.setState((prevState)=>{
            return {
                selectedOptions:{
                    ...prevState.selectedOptions,
                    [name] : (value)
                },
                errors,
            }
        });
        
        if($("."+name+" .select__value-container"))
        $("."+name+" .select__value-container").clearQueue().animate({
            scrollTop: $("."+name+" .select__value-container").get(0).scrollHeight
        });
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
    handleAddAccountFormSubmit = () => {
        
            let dataToValidatebject = {
                brand: this.state.selectedOptions.brand,
                amsProfile: this.state.selectedOptions.amsProfile,
                mwsSeller: this.state.selectedOptions.mwsSeller,
                vcVendor: this.state.selectedOptions.vcVendor,
            };
            let isAnyAccountSelected = this.state.selectedOptions.amsProfile ||
            this.state.selectedOptions.mwsSeller|| this.state.selectedOptions.vcVendor;
            let allValiditionFrom = htk.validateAllFields(validationSchema, dataToValidatebject);
            if ((Object.size(allValiditionFrom) > 0 && 
                allValiditionFrom.brand) || !isAnyAccountSelected) {
                let resetErrors = this.helperResetErrors();
                this.helperSetValidationErrorState(resetErrors, allValiditionFrom);
            } else {
                let ajaxData = {
                    clientId: this.state.selectedOptions.brand.value,
                    amsProfile: this.state.selectedOptions.amsProfile ? this.state.selectedOptions.amsProfile.map(profile => profile.value).join(",") : "",
                    sellerId: this.state.selectedOptions.mwsSeller ? this.state.selectedOptions.mwsSeller.map(seller => seller.value).join(",") : "",
                    vendorId: this.state.selectedOptions.vcVendor ? this.state.selectedOptions.vcVendor.map(vendor => vendor.value).join(",") : "",
                }
                console.log("ajaxData",ajaxData);
                this.manageEevent(ajaxData);
            }
    }
    manageEevent = (ajaxData) => {
        this.setState({
            form: {
                isFormLoading: true,
                loadingText: "Processing..."
            },
        });
        associateAccounts(
            ajaxData,
            (response) => {
                this.props.dispatch(ShowSuccessMsg("Successfull", response.message, true, "", this.props.heloperReloadDataTable(response.tableData)));
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

    formatOptionLabel = ({ value, label }) => {
        let labelLimit =  35;
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
                    <div className={clsx("brand", this.state.errors.brand.length > 0 ? "errorCustom" : "")}>
                        <label className="text-xs font-normal ml-2">
                        Select Brand <span className="font-black text-red-500 text-sm">*</span>
                        </label>
                        <SingleSelect
                            placeholder="Please Select Brand"
                            name={"brand"}
                            value={this.state.selectedOptions.brand}
                            onChangeHandler={this.onChangeHandler}
                            formatOptionLabel={this.formatOptionLabel}
                            fullWidth={true}
                            Options={this.state.options.brands}
                            styles={customStyle}
                            menuPlacement="auto"
                            maxMenuHeight={190}
                            customClassName="ThemeSelect"
                            id="brand"
                        />
                        <div className="error pl-3">{this.state.errors.brand}</div>

                    </div>
                    <div className={clsx("amsProfile pt-5", this.state.errors.amsProfile.length > 0 ? "errorCustom" : "")}>
                        <label className="text-xs font-normal ml-2">
                        Select AMS Profile <span className="font-black text-red-500 text-sm">*</span>
                        </label>
                        <MultiSelect
                            placeholder="Select Campaigns / Portfolios"
                            name={"amsProfile"}
                            id="amsProfile"
                            value={this.state.selectedOptions.amsProfile}
                            onChangeHandler = {this.onMultiSelectChangeHandler}
                            fullWidth={true}
                            Options={this.state.options.amsProfiles}
                            styles={customStyle}
                            maxMenuHeight={190}
                            menuPlacement="auto"
                            customClassName="ThemeSelect"
                            formatOptionLabel={this.formatOptionLabel}
                        />
                        <div className="error pl-3">{this.state.errors.amsProfile}</div>
                    </div>
                    
                    <div className={clsx("mwsProfile pt-5", this.state.errors.mwsSeller.length > 0 ? "errorCustom" : "")}>
                        <label className="text-xs font-normal ml-2">
                        Select MWS Seller <span className="font-black text-red-500 text-sm">*</span>
                        </label>
                        <MultiSelect
                            placeholder="Select MWS Seller"
                            name={"mwsSeller"}
                            value={this.state.selectedOptions.mwsSeller}
                            onChangeHandler = {this.onMultiSelectChangeHandler}
                            formatOptionLabel={this.formatOptionLabel}
                            fullWidth={true}
                            Options={this.state.options.mwsSellers}
                            styles={customStyle}
                            maxMenuHeight={150}
                            menuPlacement="auto"
                            customClassName="ThemeSelect"
                            id="mwsSeller"
                        />
                        <div className="error pl-3">{this.state.errors.mwsSeller}</div>
                    </div>
                    <div className={clsx("vcVendor pt-5", this.state.errors.vcVendor.length > 0 ? "errorCustom" : "")}>
                        <label className="text-xs font-normal ml-2">
                        Select Vendor <span className="font-black text-red-500 text-sm">*</span>
                        </label>
                        <MultiSelect
                            placeholder="Select Vendor"
                            name={"vcVendor"}
                            value={this.state.selectedOptions.vcVendor}
                            onChangeHandler = {this.onMultiSelectChangeHandler}
                            formatOptionLabel={this.formatOptionLabel}
                            fullWidth={true}
                            onMenuOpen={this.props.onMenuOpen} 
                            onMenuClose = {this.props.onMenuClose}
                            maxMenuHeight={150}
                            menuPlacement="auto"
                            Options={this.state.options.vcVendors}
                            styles={customStyle}
                            customClassName="ThemeSelect"
                            id="vcVendor"
                        />
                        <div className="error pl-3">{this.state.errors.vcVendor}</div>
                    </div>
                    
                    <div className="flex float-right items-center justify-center my-5 w-full">
                        <div className="mr-3">
                            <TextButton
                                BtnLabel={"Cancel"}
                                color="primary"
                                onClick={this.props.handleModalClose}/>
                        </div>
                        <PrimaryButton
                            btnlabel={"Save"}
                            variant={"contained"}
                            onClick={this.handleAddAccountFormSubmit}
                        />
                    </div>
                </div>
            </>
        )
    }
}

export default withStyles(useStyles)(connect(null)(AddAccounts))
