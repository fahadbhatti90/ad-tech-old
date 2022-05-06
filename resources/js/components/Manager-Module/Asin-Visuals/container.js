import React, { Component } from 'react';
import Card from '@material-ui/core/Card';
import AsinFilters from "./AsinFilters";
import AsinComCards from "./AsinComCards";
import GraphChart from './../Advertising-Visuals/Graphs/container';
import Typography from '@material-ui/core/Typography';
import { withStyles } from "@material-ui/core/styles";
import {styles}  from "./styles";
import "./styles.scss";
import CustomizedDateRangePicker from "./../../../general-components/DateRangePicker/CustomizedDateRangePicker";
// import { DateRangePicker, DateRange } from "@matharumanpreet00/react-daterange-picker";
import moment from 'moment';
import {defaultOptions} from "./defaultState";
import ContainerLoader from "./../../../general-components/ProgressLoader/ContainerLoader";
import {showLoader,hideLoader} from "./../../../general-components/loader/action";
import { getProfiles,getCampaignsCall,
        getAsinsCall,getScoreCardCall,
        getPerfChartCall,getEffiChartCall,
        getAwarChartCall,getPerfPercentagesCall,
        getAwarPercentagesCall,getEffiPercentagesCall
    } from './apiCalls'
import { connect } from 'react-redux';
import {initialState} from "./initialState";
import {Helmet} from "react-helmet";

let filterObj = {
    selectedProfile: null,
    selectedCampaign:null,
    selectedAsin:null,
    selectedDate: "",
    startDate:"",
    endDate:""
};
class AsinVisuals extends Component {
    constructor(props){
        super(props);
        this.state={
        ...initialState    
        }
    }

    getLocalStorageFilters=(profileOptions)=>{
        let filterData = htk.getLocalStorageObjectDataById(htk.constants.ASIN_FILTERS);
        let activeRole = htk.getLocalStorageObjectDataById(htk.constants.LOGGED_IN_USER);
        let filters = null;
        for (let key in filterData) {
            if (filterData.hasOwnProperty(activeRole.id)) {
                if(key == activeRole.id){
                    filters = filterData[key];
                }
            }
        }

        let selectedProfile = filters["selectedProfile"];
        let found = false;
        if(selectedProfile){
            for(var i = 0; i < profileOptions.length; i++) {
                if (profileOptions[i].value == selectedProfile.value) {
                    found = true;
                    break;
                }
            }
        }

        if(filters  && found){
            let selectedCampaign = null;
            if(filters.selectedCampaign){
                selectedCampaign = filters.selectedCampaign.map(obj=>{
                    return {label: obj.label.props?obj.label.props.title:obj.label,
                            value: obj.value}
                })
            }
            filters["selectedCampaign"]=selectedCampaign;
            this.setState({
                ...filters
            },()=>{
                this.allApiCalls();
                if(filters.selectedProfile){
                    this.getCampaigns(filters.selectedProfile);
                }
                if(filters.selectedProfile && filters.selectedCampaign){
                    this.getAsins(filters.selectedProfile,filters.selectedCampaign);
                }
            })
        } else{
            this.setLocalStorage("selectedProfile",null);
            this.setLocalStorage("selectedCampaign",null);
            this.setLocalStorage("selectedAsin",null);
            this.setLocalStorage("selectedDate","");
            this.setLocalStorage("startDate","");
            this.setLocalStorage("endDate", "");
           
        }
    }
    /**
     * Life Cycle method to get the data for initial template call
     */
    componentDidMount=()=>{
        this.props.dispatch(showLoader());
        getProfiles((profileOptions) => {
            //success
            this.props.dispatch(hideLoader());
            this.setState({
                profileOptions
            })
            if(profileOptions.length > 0){
                this.getLocalStorageFilters(profileOptions);
                
             }
        },(err) => {
            //error
            // this.props.dispatch(showSnackBar());
        });
    }

    onProfileChange=(value)=>{
        if(value==null){
            let {profileOptions,selectedDate,startDate,endDate} = this.state;
            this.setState({
                ...initialState,
                profileOptions,selectedDate,startDate,endDate
            },()=>{
                this.setLocalStorage("selectedProfile",null);
                this.setLocalStorage("selectedAsin",null);
                this.setLocalStorage("selectedCampaign",null);
            })
        }else{      
            this.setState({
                selectedProfile: value
            },()=>{
            this.setState({
                selectedCampaign:null,
                campaignOptions:[],
                asinOptions:[],
                selectedAsin: null,
                showFilterLoader:true
            })
            this.getCampaigns(value);
            this.setLocalStorage("selectedProfile",value);
            this.setLocalStorage("selectedAsin",null);
            this.setLocalStorage("selectedCampaign",null);
        })
    }
    }

    setLocalStorage=(key,value)=>{
        let filterData = htk.getLocalStorageObjectDataById(htk.constants.ASIN_FILTERS);
        if(filterData){            
            filterObj[key] = value;
            let activeRole = htk.getLocalStorageObjectDataById(htk.constants.LOGGED_IN_USER);
            filterData[activeRole.id]= filterObj;
            localStorage.setItem(htk.constants.ASIN_FILTERS,JSON.stringify(filterData));
        } else{
            filterObj[key] = value;
            let activeRole = htk.getLocalStorageObjectDataById(htk.constants.LOGGED_IN_USER);
            let updatedObj = {[activeRole.id]: filterObj}    
            localStorage.setItem(htk.constants.ASIN_FILTERS,JSON.stringify(updatedObj));
        }

    }
    
    getCampaigns=(profile)=>{
        if(profile!=null){
            getCampaignsCall(profile.value,(campaignOptions) => {
                //success
                this.setState({
                    campaignOptions,
                    showFilterLoader:false
                })
            },(err) => {
                //error
                alert(err);
            });
        }else {
            this.setState({
                showFilterLoader:false
            })
        }

    }
 
    getAsins=(profile,campaigns)=>{
        if(campaigns!=null){
            let profileId = profile.value;
            let campaignIds = campaigns.map(campaign=>{
                return campaign.value;
            })
            getAsinsCall({profileId,campaignIds},(asinOptions) => {
                //success
                this.setState({
                    asinOptions,
                    showFilterLoader:false
                })
            },(err) => {
                //error
                alert(err);
            });
        } else{
            this.setState({
                showFilterLoader:false
            })
        }

    }

    getScoreCards=()=>{
        let profileId = this.state.selectedProfile.value;
        let campaignIds = this.state.selectedCampaign.map(campaign=>{
            return campaign.value;
        })
        let asin = this.state.selectedAsin.value;
        let startDate=moment(this.state.startDate).format('YYYYMMDD');
        let endDate=moment(this.state.endDate).format('YYYYMMDD');

        getScoreCardCall({profileId,campaignIds,asin,startDate,endDate},(scoreCards) => {
            //success
            this.setState({
                scoreCards,
                showComcardsLoader:false
            })
        },(err) => {
            //error
            alert(err);
        });
    }
    onCampaignChange=(value)=>{
        if(value && value.length > 0){
            let checkAll = value.some(el => el.value === "All");
            if(checkAll){
                value=[{label:"Select All",
                        value: "All"
                        }]
            }
            
            this.setState({
                selectedCampaign: value,
                showFilterLoader:true
            },()=>{
                $(".autoScrl .select__value-container").animate({
                    scrollTop: $('.autoScrl .select__value-container').get(0).scrollHeight - 35
                });
                this.setState({
                    selectedAsin: null,
                    asinOptions:[],
                })
                this.setLocalStorage("selectedAsin",null);
                this.setLocalStorage("selectedCampaign",value);
                this.getAsins(this.state.selectedProfile,value);
            })
        } else{
            this.backToDefaultState();
            let {profileOptions,selectedProfile,campaignOptions,selectedDate,startDate,endDate} = this.state;
            this.setState({
                ...initialState,
                campaignOptions,
                selectedProfile,
                profileOptions,selectedDate,startDate,endDate
            },()=>{
                    this.setLocalStorage("selectedCampaign",null);
                    this.setLocalStorage("selectedAsin",null);
            })
        }

    }

    onAsinChange=(value)=>{
        this.setState({
            selectedAsin: value,
        },()=>{
                this.allApiCalls();
                this.setLocalStorage("selectedAsin",value);
        })
    }

    backToDefaultState=()=>{
        this.setState({
            ...defaultOptions
        })
    }

    allApiCalls=()=>{
        this.backToDefaultState();
        if( this.state.selectedProfile &&
            this.state.selectedCampaign &&
            this.state.selectedAsin &&
            this.state.selectedDate !="" ){
                this.setState({
                    disableFilters:true,
                    showPerfLoader:"Performance",
                    showEffiLoader:"Efficiency",
                    showAwarLoader:"Awareness",
                    showComcardsLoader:true,
                },()=>{
                    this.getScoreCards();
                    this.getPerformanceChartCall();
                    this.getPerformancePercentages();
                    this.getEfficiencyChartCall();
                    this.getEfficiencyPercentages();
                    this.getAwarenessChartCall();
                    this.getAwarenessPercentages();
                })

        }
   }

    getPerformanceChartCall=()=>{
        let profileId = this.state.selectedProfile.value;
        let campaignIds = this.state.selectedCampaign.map(campaign=>{
            return campaign.value;
        })
        let asin = this.state.selectedAsin.value;
        let startDate=moment(this.state.startDate).format('YYYYMMDD');
        let endDate=moment(this.state.endDate).format('YYYYMMDD');
        getPerfChartCall({profileId,campaignIds,asin,startDate,endDate},(perfData,getPerformanceY2Min) => {
            //success
            this.setState({
                perfData,
                getPerformanceY2Min,
            })
        },(err) => {
            //error
            alert(err);
        });
    }
    getPerformancePercentages=()=>{
        let profileId = this.state.selectedProfile.value;
        let campaignIds = this.state.selectedCampaign.map(campaign=>{
            return campaign.value;
        })
        let asin = this.state.selectedAsin.value;
        let startDate=moment(this.state.startDate).format('YYYYMMDD');
        let endDate=moment(this.state.endDate).format('YYYYMMDD');
        getPerfPercentagesCall({profileId,campaignIds,asin,startDate,endDate},(perfPercentagesData) => {
            //success
            this.setState({
                perfPercentagesData,
                showPerfLoader:""
            })
        },(err) => {
            //error
            alert(err);
        });
    }

    getEfficiencyChartCall=()=>{
        let profileId = this.state.selectedProfile.value;
        let campaignIds = this.state.selectedCampaign.map(campaign=>{
            return campaign.value;
        })
        let asin = this.state.selectedAsin.value;
        let startDate=moment(this.state.startDate).format('YYYYMMDD');
        let endDate=moment(this.state.endDate).format('YYYYMMDD');

        getEffiChartCall({profileId,campaignIds,asin,startDate,endDate},(effiData) => {
            //success
            this.setState({
                effiData,
                showEffiLoader:""
            })
        },(err) => {
            //error
            alert(err);
        });
    }

    getAwarenessChartCall=()=>{
        let profileId = this.state.selectedProfile.value;
        let campaignIds = this.state.selectedCampaign.map(campaign=>{
            return campaign.value;
        })
        let asin = this.state.selectedAsin.value;
        let startDate=moment(this.state.startDate).format('YYYYMMDD');
        let endDate=moment(this.state.endDate).format('YYYYMMDD');
        getAwarChartCall({profileId,campaignIds,asin,startDate,endDate},(awareData) => {
            //success
            this.setState({
                awareData,
                showAwarLoader:""
            })
        },(err) => {
            //error
            alert(err);
        });
    }

    getEfficiencyPercentages=()=>{
        let profileId = this.state.selectedProfile.value;
        let campaignIds = this.state.selectedCampaign.map(campaign=>{
            return campaign.value;
        })
        let asin = this.state.selectedAsin.value;
        let startDate=moment(this.state.startDate).format('YYYYMMDD');
        let endDate=moment(this.state.endDate).format('YYYYMMDD');
        getEffiPercentagesCall({profileId,campaignIds,asin,startDate,endDate},(effiPercentagesData) => {
            //success
            this.setState({
                effiPercentagesData,
                showEffiLoader:""
            })
        },(err) => {
            //error
            alert(err);
        });
    }

    getAwarenessPercentages=()=>{
        let profileId = this.state.selectedProfile.value;
        let campaignIds = this.state.selectedCampaign.map(campaign=>{
            return campaign.value;
        })
        let asin = this.state.selectedAsin.value;
        let startDate=moment(this.state.startDate).format('YYYYMMDD');
        let endDate=moment(this.state.endDate).format('YYYYMMDD');
        getAwarPercentagesCall({profileId,campaignIds,asin,startDate,endDate},(awarPercentagesData) => {
            //success
            this.setState({
                disableFilters:false,
                awarPercentagesData,
                showAwarLoader:"",
            })
        },(err) => {
            //error
            this.setState({
                disableFilters:false
            })
        });
    }
    onDateChange=(range)=>{
        let startDate = moment(range.startDate).format('l');
        let endDate = moment(range.endDate).format('l');
        this.setState({
            dateRangeObj:range,
            startDate:startDate,
            endDate: endDate,
            selectedDate: startDate+" - "+endDate,
        },()=>{
            this.setLocalStorage("startDate",startDate);
            this.setLocalStorage("endDate",endDate);
            this.setLocalStorage("selectedDate",(startDate+" - "+endDate));
            this.allApiCalls();
        })
    }
  
   
    reloadData=(name)=>{
        if( this.state.selectedProfile &&
            this.state.selectedCampaign &&
            this.state.selectedAsin &&
            this.state.selectedDate !="" ){
            if(name == "Performance"){
                this.setState({showPerfLoader:name},()=>{
                    this.getPerformanceChartCall();
                    this.getPerformancePercentages();
                })
            } else if(name == "Efficiency"){
                this.setState({showEffiLoader:name},()=>{
                    this.getEfficiencyChartCall();
                    this.getEfficiencyPercentages();
                })
            } else if(name == "Awareness"){
                this.setState({showAwarLoader:name},()=>{
                    this.getAwarenessChartCall();
                    this.getAwarenessPercentages();
                })
            }
        }
    }

    render() {
        const {classes} = this.props;
        return (
            <>
                <Helmet>
                    <title>Ad-Tech Asin Visuals</title>
                </Helmet>
            <div style={{display: 'table', tableLayout:'fixed', width:'100%'}}>
                <Card  classes={{ root: classes.card }}>
                    {/* Header of the Module */}
                    <Typography variant="h6" className={`${classes.pageTitle}`} noWrap>
                        Asin Performance
                    </Typography>
                    {/* Filters of the Module */}
                    <div className="relative">
                        {this.state.showFilterLoader?
                            <ContainerLoader
                                height={30}
                                classStyles={"mt-1"}/>
                            :
                            ""}
                            <AsinFilters
                                disableFilters={this.state.disableFilters}
                                selectedDate={this.state.selectedDate}
                                selectedProfile={this.state.selectedProfile}
                                selectedCampaign={this.state.selectedCampaign}
                                selectedAsin={this.state.selectedAsin}
                                profileOptions={this.state.profileOptions}
                                campaignOptions={this.state.campaignOptions}
                                asinOptions={this.state.asinOptions}
                                onProfileChangeHandler={this.onProfileChange}
                                onCampaignChangeHandler={this.onCampaignChange}
                                onAsinChangeHandler={this.onAsinChange}
                                
                                dateRangeObj={this.state.dateRangeObj}
                                getValue={this.onDateChange}
                                datepickerClass = {classes.datepickerClass}
                                />
                    </div>
                    {/* Single ComCards of the Module */}
                    <AsinComCards
                        showComcardsLoader={this.state.showComcardsLoader}
                        scoreCards={this.state.scoreCards}
                        />
                    {/* Performance Graph Chart */}
                    <GraphChart
                        customClass={"performance"}
                        cardData = {this.state.perfPercentagesData}
                        dataChart={this.state.perfData}
                        heading={"Performance"}
                        types={["bar","spline","spline"]}
                        colors={['#21bf73','#4a47a3','#ffc107']}
                        y1={"Cost/ACOS"}
                        y2={"Rev"}
                        getPerformanceY2Min={this.state.getPerformanceY2Min}
                        tooltip={true}
                        reloadApiCall={this.reloadData}
                        showLoader={this.state.showPerfLoader}
                        />
                    {/* Efficiency Chart */}
                    <GraphChart
                        customClass={"efficiency"}
                        cardData = {this.state.effiPercentagesData}
                        dataChart={this.state.effiData}
                        heading={"Efficiency"}
                        types={["bar","spline","spline"]}
                        colors={['#ce93d8','#ffc107','#000000']}
                        y1={"CPA/CPC"}
                        y2={"ROAS"}
                        tooltip={true}
                        reloadApiCall={this.reloadData}
                        showLoader={this.state.showEffiLoader}
                        />
                    {/* Awareness Chart */}
                    <GraphChart
                        customClass={"awareness"}
                        cardData = {this.state.awarPercentagesData}
                        dataChart={this.state.awareData}
                        heading={"Awareness"}
                        axes={true}
                        types={["spline","bar","spline"]}
                        colors={['#059656','#08bdda','#6a1b9a']}
                        y1={'Impressions/Clicks'}
                        y2={"CTR"}
                        tooltip={true}
                        reloadApiCall={this.reloadData}
                        showLoader={this.state.showAwarLoader}
                        />
                </Card>
            </div>
        </>
    );
}
}
export default withStyles(styles)(connect(null)(AsinVisuals));