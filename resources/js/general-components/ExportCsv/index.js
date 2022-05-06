import React, {Component} from 'react';
import PropTypes from 'prop-types'; 
import clsx from 'clsx';
import './exportCsv.scss'
import {connect} from "react-redux"
import {ShowSuccessMsg} from "../successDailog/actions";
import {ShowFailureMsg} from "../failureDailog/actions";
import {primaryColor, primaryColorLight} from "../../app-resources/theme-overrides/global";
import {withStyles} from "@material-ui/core/styles";
import TextFieldInput from "../Textfield";
import moment from "moment";
import LinearProgress from '@material-ui/core/LinearProgress';
import {getDownloadLink} from './apiCalls';
import CustomDateRangePicker from '../../components/SuperAdmin-Modules/SearchRankScraping/ScheduleScraping/CustomDateRangePicker';
const useStyles = theme => ({
    root: {
        '& .MuiInputBase-root': {
            marginTop: 8,
            borderRadius: 20,
            border: "1px solid #c3bdbd8c",
            height: 30,
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
class ExportCsv extends Component {
    constructor(props) {
        super(props);
        this.state = {
            dateRange: "",
            dateRangeObj: {
                startDate: new Date(),
                endDate: new Date(),
                key: 'selection',
            },
            showDRP: false,
            isLoading:false,
        }//end state

    }
    handleOnDateRangeClick = (e) => {
        this.setState({
            showDRP: true
        })
    }
    helperCloseDRP = (event) => {
        this.setState({
            showDRP: false,
        });
    }
    getValue = (range) => {
        this.setState({
            dateRangeObj: range,
            dateRange: moment(range.startDate).format('YYYY-MM-DD') + " - " + moment(range.endDate).format('YYYY-MM-DD'),
            showDRP: false,
            isLoading:true,
        }, () => {
            if(this.props.checkDataUrl) {
                this.fetchUrlFromBackendAfterDataExistVerification();
            }
            else{
                window.open( this.props.fetchDataUrl, '_blank');
            }
        
        })
    }
    fetchUrlFromBackendAfterDataExistVerification = () => {
        let startDate = moment(this.state.dateRangeObj.startDate).format('YYYY-MM-DD');
        let endDate = moment(this.state.dateRangeObj.endDate).format('YYYY-MM-DD');
        getDownloadLink( this.props.checkDataUrl, {
            startDate,
            endDate
        },
        (response)=>{
            let urlToDownload = response.url;
            this.setState({
                isLoading:false,
            }, ()=>{
                window.open( urlToDownload, '_blank');
            })
            
        },(error)=>{
            console.log(error);
            this.setState({
                isLoading:false,
            }, ()=>{
                this.props.dispatch(ShowFailureMsg(error, "", true, ""));
            })
        })
    }
    render() {
        const {classes} = this.props;
        return (
            <>
                <div className={clsx("px-1 relative exportCsvMain",this.props.className ? this.props.className : "")}>
                    <div className={clsx(this.props.className ? "DateRange mb-5 relative":"DateRange ml-auto mb-5 relative w-2/6")}>
                        <div className="graphLoader bg-white absolute h-full overflow-hidden w-full top-0 left-0 z-10"
                            style={(this.state.isLoading ? {display: "block", background:"#f7fafceb"} : {display: "none", background:"#f7fafceb"})}>
                            
                            <div
                                className="absolute flex font-normal h-full items-center justify-center overflow-hidden text-gray-600 text-xs tracking-widest w-full z-10">
                                Processing...
                            </div>
                        </div>
                        <label className="text-xs font-normal ml-2">
                            Export CSV 
                        </label>
                        <div className="ThemeInput dateRange " onClick={this.handleOnDateRangeClick}>
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
                        </div>
                        {this.state.showDRP ?
                                <CustomDateRangePicker 
                                    range={this.state.dateRangeObj}
                                    helperCloseDRP={this.helperCloseDRP}
                                    date={this.state.dateRange}
                                    getValue={this.getValue} 
                                    direction="horizontal"
                                    className="right-0 left-auto exportCSVDRP"
                                    isDateRange={true}
                                />
                        : null}
                    </div>
                </div>
            </>
        )
    }
}
ExportCsv.propTypes = {
    checkDataUrl: PropTypes.string,
    fetchDataUrl: PropTypes.string,
  };
export default withStyles(useStyles)(connect(null)(ExportCsv))
