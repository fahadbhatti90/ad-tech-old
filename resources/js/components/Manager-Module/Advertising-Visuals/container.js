import React, {Component} from 'react';
import Card from '@material-ui/core/Card';
import AdFilters from "./Filters/AdFilters";
import Typography from '@material-ui/core/Typography';
import {withStyles} from "@material-ui/core/styles";
import {styles} from "./styles";
import AsinComCards from "./../Asin-Visuals/AsinComCards";
import ComCards from "./ComCards";
import GraphChart from './Graphs/container';
import {connect} from 'react-redux';
import moment from 'moment';
import {initialState} from "./initialState";
import ContainerLoader from "./../../../general-components/ProgressLoader/ContainerLoader";
import {showLoader, hideLoader} from "./../../../general-components/loader/action";
import AdVisualTables from "./DataTables/container";
import TopCampaignsTable from "./DataTables/TopCampaignsTable";
import $ from 'jquery';
import {
    getProfiles, getCampaignsCall, getScoreCardCall,
    getPerfChartCall, getEffiChartCall,
    getAwarChartCall, getPerfPercentagesCall,
    getAwarPercentagesCall, getEffiPercentagesCall,
    getMTDDataCall, getMTDPercCall,
    getWOWDataCall, getWOWPercCall,
    getDODDataCall, getDODPercCall,
    getYTDDataCall, getYTDPercCall,
    getWTDDataCall, getWTDPercCall,
    getAdTypeTable, getStrTypeTable,
    getCstTypeTable, getProTypeTable,
    getPreTypeTable, getPreYTDTypeTable,
    getTopCampaigns,getTagCampaigns

} from './apiCalls';
import {Helmet} from "react-helmet";


let filterObj = {
    selectedProfile: null,
    selectedCampaign: null,
    selectedProduct: null,
    selectedStrategy:null,
    selectedDate: "",
    startDate: "",
    endDate: "",
};

class AdVisuals extends Component {
    constructor(props) {
        super(props);
        this.state = {
            ...initialState
        }
    }

    getLocalStorageFilters=(profileOptions)=>{
        let filterData = htk.getLocalStorageObjectDataById(htk.constants.AD_FILTERS);
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
        if(filters && found){
            let selectedCampaign = null;
            if(filters.selectedCampaign) {
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
                    this.callTagCampaign();
                }
                this.callTopCampaignsLoader();
            });
        } else{
            this.setLocalStorage("selectedProfile",null);
            this.setLocalStorage("selectedProduct",null);
            this.setLocalStorage("selectedStrategy",null);
            this.setLocalStorage("selectedCampaign",null);
            this.setLocalStorage("selectedDate","");
            this.setLocalStorage("startDate","");
            this.setLocalStorage("endDate", "");
           
        }
    }

    componentDidMount() {
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
        }, (err) => {
            //error
            // this.props.dispatch(showSnackBar());
        });
    }

    setLocalStorage=(key,value)=>{
        let filterData = htk.getLocalStorageObjectDataById(htk.constants.AD_FILTERS);
        if(filterData){            
            filterObj[key] = value;
            let activeRole = htk.getLocalStorageObjectDataById(htk.constants.LOGGED_IN_USER);
            filterData[activeRole.id]= filterObj;
            localStorage.setItem(htk.constants.AD_FILTERS,JSON.stringify(filterData));
        } else{
            filterObj[key] = value;
            let activeRole = htk.getLocalStorageObjectDataById(htk.constants.LOGGED_IN_USER);
            let updatedObj = {[activeRole.id]: filterObj}    
            localStorage.setItem(htk.constants.AD_FILTERS,JSON.stringify(updatedObj));
        }

    }

    onStrategyChange = (value) => {
        let {profileOptions,productOptions,selectedProduct,strategyOptions,selectedProfile,TopCampaignsData,selectedDate,startDate,endDate} = this.state;
            this.setState({
                ...initialState,
                profileOptions,
                selectedProfile,
                selectedDate,
                startDate,
                endDate,
                selectedProduct,
                productOptions,
                strategyOptions,
                TopCampaignsData,
                selectedStrategy: value,
                campaignOptions:[],
                selectedCampaign: null
            },()=>{
            this.setLocalStorage("selectedStrategy",value);
            this.setLocalStorage("selectedCampaign",null);
            this.getTagCampaignsPS();
        })
    }

    onProductChange = (value) => {
        this.setState({
            selectedProduct: value,
            campaignOptions:[],
            selectedCampaign: null

        },()=>{
            this.setLocalStorage("selectedProduct",value);
            this.setLocalStorage("selectedCampaign",null);
            this.getTagCampaignsPS();
        })
    }

    getTagCampaignsPS=()=>{
        if(this.state.selectedStrategy == null && this.state.selectedProduct == null ){
            if(this.state.selectedProfile){
                this.getCampaigns(this.state.selectedProfile);
                this.callTopCampaignsLoader();
            }
        } else{
            this.callTagCampaign();
        }
    }

    callTagCampaign=()=>{
        let productType = 0;
        let strategyType = 0;
        let fkTagIdS = 0;
        let fkTagIdP = 0;
        if(this.state.selectedProduct){
            productType = 1;
            fkTagIdP = this.state.selectedProduct.value
        } 
        if(this.state.selectedStrategy){
            strategyType = 2;   
            fkTagIdS = this.state.selectedStrategy.value
        }

        if((this.state.selectedProduct || this.state.selectedStrategy) && this.state.selectedProfile){
            
            let profileId = this.state.selectedProfile.value;
            getTagCampaigns({fkTagIdS,fkTagIdP,productType,strategyType,profileId}, (campaignOptions) => {
                //success
                this.setState({
                    campaignOptions,
                    showFilterLoader: false
                })
            }, (err) => {
                //error
                alert(err);
            });
        }

    }

    onProfileChange = (value) => {
        if(value==null){
            let {profileOptions,selectedDate,startDate,endDate} = this.state;
            this.setState({
                ...initialState,
                profileOptions,selectedDate,startDate,endDate

            },()=>{
                this.setLocalStorage("selectedProduct",null);
                this.setLocalStorage("selectedStrategy",null);
                this.setLocalStorage("selectedCampaign",null);
                this.setLocalStorage("selectedProfile",null);
            })
        }else{
            this.setState({
                selectedProfile: value
            }, () => {
                this.setState({
                    selectedCampaign: null,
                    selectedStrategy: null,
                    selectedProduct: null,
                    campaignOptions:[],
                    showFilterLoader: true
                })
                this.setLocalStorage("selectedProfile",value);
                this.setLocalStorage("selectedProduct",null);
                this.setLocalStorage("selectedStrategy",null);
                this.setLocalStorage("selectedCampaign",null);
                this.getCampaigns(value);
                this.callTopCampaignsLoader();
            })
        }
      
    }

    onTopXCampaignChange = (e) => {
        this.setState({
            TopXCampaigns: e.target.value
        },()=>{
            
            this.callTopCampaignsLoader();
            }
        )}

    callTopCampaignsLoader=()=>{
        if (this.state.selectedProfile &&
            this.state.selectedDate != "") {
            this.setState({TopXCampaignsLoader:12})
            this.getTopCampaignsCall();
        }
    }
        
    onCampaignChange = (value) => {
        if (value && value.length>0) {
            let checkAll = value.some(el => el.value === "All");
            if (checkAll) {
                value = [{
                    label: "Select All",
                    value: "All"
                }]
            }
            this.setState({
                selectedCampaign: value,
            }, () => {
                $(".autoScrl .select__value-container").animate({
                    scrollTop: $('.autoScrl .select__value-container').clearQueue().get(0).scrollHeight - 35
                });
                this.setLocalStorage("selectedCampaign",value);
                this.allApiCalls();
            })
        
        } else{
                let {profileOptions,selectedProduct,selectedStrategy,productOptions,strategyOptions,selectedProfile,TopCampaignsData,selectedDate,startDate,endDate,campaignOptions} = this.state;
                this.setState({
                    ...initialState,
                    profileOptions,
                    selectedProfile,
                    selectedDate,
                    startDate,
                    endDate,
                    selectedProduct,
                    selectedStrategy,
                    campaignOptions,
                    productOptions,
                    strategyOptions,
                    TopCampaignsData
                },()=>{
                    // this.setLocalStorage("selectedProduct",null);
                    // this.setLocalStorage("selectedStrategy",null);
                    this.setLocalStorage("selectedCampaign",null);                    
                })
        }
    }

    getCampaigns = (profile) => {
        if (profile != null) {
            getCampaignsCall(profile.value, (campaignOptions, strategyOptions, productOptions) => {
                //success
                this.setState({
                    campaignOptions,
                    strategyOptions,
                    productOptions,
                    showFilterLoader: false
                })
            }, (err) => {
                //error
                alert(err);
            });
        } else {
            this.setState({
                showFilterLoader: false
            })
        }

    }

    getScoreCards = () => {
        let profileId = this.state.selectedProfile.value;
        let campaignIds = this.state.selectedCampaign.map(campaign => {
            return campaign.value;
        })
        let startDate = moment(this.state.startDate).format('YYYYMMDD');
        let endDate = moment(this.state.endDate).format('YYYYMMDD');

        getScoreCardCall({profileId, campaignIds, startDate, endDate}, (scoreCards) => {
            //success
            this.setState({
                scoreCards,
                showComcardsLoader: false
            })
        }, (err) => {
            //error
            alert(err);
        });
    }

    allApiCalls = () => {
        if (this.state.selectedProfile &&
            this.state.selectedCampaign &&
            this.state.selectedDate != "") {
            this.setState({
                disableFilters:true,
                showHidden:true,
                showComcardsLoader: true,
                showPerfLoader: "Performance",
                showEffiLoader: "Efficiency",
                showAwarLoader: "Awareness",
                showMOMLoader: 1,
                showWOWLoader: 2,
                showDODLoader: 3,
                showYTDLoader: 4,
                showWTDLoader: 5,
                showADLoader: 6,
                showStrLoader: 7,
                showProLoader: 8,
                showCstLoader: 9,
                showPreLoader: 10,
                showPreYTDLoader: 11,
            }, () => {
                this.getScoreCards();
                this.getPerformanceChartCall();
                this.getPerformancePercentages();
                this.getEfficiencyChartCall();
                this.getEfficiencyPercentages();
                this.getAwarenessChartCall();
                this.getAwarenessPercentages();
                this.getMTDCall();
                this.getWOWCall();
                this.getDODCall();
                this.getYTDCall();
                this.getWTDCall();
                this.getAdTypeCall();
                this.getStrTypeCall();
                this.getCstTypeCall();
                this.getProTypeCall();
                this.getPreTypeCall();
                this.getPreYTDTypeCall();
            })

        }
    }

    getTopCampaignsCall = () => {
        let profileId = this.state.selectedProfile.value;
        let startDate = moment(this.state.startDate).format('YYYYMMDD');
        let endDate = moment(this.state.endDate).format('YYYYMMDD');
        let TopXCampaigns = this.state.TopXCampaigns;
        getTopCampaigns({profileId, startDate, endDate, TopXCampaigns}, (TopCampaignsData) => {
            //success
            this.setState({
                TopCampaignsData,
                TopXCampaignsLoader: false
            })
        }, (err) => {
            //error
            alert(err);
        });
    }

    getPerformanceChartCall = () => {
        let profileId = this.state.selectedProfile.value;
        let campaignIds = this.state.selectedCampaign.map(campaign => {
            return campaign.value;
        })
        let startDate = moment(this.state.startDate).format('YYYYMMDD');
        let endDate = moment(this.state.endDate).format('YYYYMMDD');
        getPerfChartCall({profileId, campaignIds, startDate, endDate}, (perfData, getPerformanceY2Min) => {
            //success
            this.setState({
                perfData,
                getPerformanceY2Min,
            })
        }, (err) => {
            //error
            alert(err);
        });
    }

    getPerformancePercentages = () => {
        let profileId = this.state.selectedProfile.value;
        let campaignIds = this.state.selectedCampaign.map(campaign => {
            return campaign.value;
        })
        let startDate = moment(this.state.startDate).format('YYYYMMDD');
        let endDate = moment(this.state.endDate).format('YYYYMMDD');
        getPerfPercentagesCall({profileId, campaignIds, startDate, endDate}, (perfPercentagesData) => {
            //success
            this.setState({
                perfPercentagesData,
                showPerfLoader: ""
            })
        }, (err) => {
            //error
            alert(err);
        });
    }

    getEfficiencyChartCall = () => {
        let profileId = this.state.selectedProfile.value;
        let campaignIds = this.state.selectedCampaign.map(campaign => {
            return campaign.value;
        })
        let startDate = moment(this.state.startDate).format('YYYYMMDD');
        let endDate = moment(this.state.endDate).format('YYYYMMDD');

        getEffiChartCall({profileId, campaignIds, startDate, endDate}, (effiData) => {
            //success
            this.setState({
                effiData,
                showEffiLoader: ""
            })
        }, (err) => {
            //error
            alert(err);
        });
    }

    getAwarenessChartCall = () => {
        let profileId = this.state.selectedProfile.value;
        let campaignIds = this.state.selectedCampaign.map(campaign => {
            return campaign.value;
        })

        let startDate = moment(this.state.startDate).format('YYYYMMDD');
        let endDate = moment(this.state.endDate).format('YYYYMMDD');
        getAwarChartCall({profileId, campaignIds, startDate, endDate}, (awareData) => {
            //success
            this.setState({
                awareData,
                showAwarLoader: ""
            })
        }, (err) => {
            //error
            alert(err);
        });
    }

    getEfficiencyPercentages = () => {
        let profileId = this.state.selectedProfile.value;
        let campaignIds = this.state.selectedCampaign.map(campaign => {
            return campaign.value;
        })

        let startDate = moment(this.state.startDate).format('YYYYMMDD');
        let endDate = moment(this.state.endDate).format('YYYYMMDD');
        getEffiPercentagesCall({profileId, campaignIds, startDate, endDate}, (effiPercentagesData) => {
            //success
            this.setState({
                effiPercentagesData,
                showPerfLoader: ""
            })
        }, (err) => {
            //error
            alert(err);
        });
    }

    getAwarenessPercentages = () => {
        let profileId = this.state.selectedProfile.value;
        let campaignIds = this.state.selectedCampaign.map(campaign => {
            return campaign.value;
        })
        let startDate = moment(this.state.startDate).format('YYYYMMDD');
        let endDate = moment(this.state.endDate).format('YYYYMMDD');
        getAwarPercentagesCall({profileId, campaignIds, startDate, endDate}, (awarPercentagesData) => {
            //success
            this.setState({
                awarPercentagesData,
                showPerfLoader: ""
            })
        }, (err) => {
            //error
            alert(err);
        });
    }

    getAdTypeCall = () => {
        let profileId = this.state.selectedProfile.value;
        let campaignIds = this.state.selectedCampaign.map(campaign => {
            return campaign.value;
        })
        let startDate = moment(this.state.startDate).format('YYYYMMDD');
        let endDate = moment(this.state.endDate).format('YYYYMMDD');
        getAdTypeTable({profileId, campaignIds, startDate, endDate}, (AdData, AdGrands,rowsToAdd) => {
            //success
            this.setState({
                AdData,
                AdGrands,
                rowsToAdd,
                showADLoader: ""
            })
        }, (err) => {
            //error
            alert(err);
        });
    }

    getStrTypeCall = () => {
        let profileId = this.state.selectedProfile.value;
        let campaignIds = this.state.selectedCampaign.map(campaign => {
            return campaign.value;
        })
        let startDate = moment(this.state.startDate).format('YYYYMMDD');
        let endDate = moment(this.state.endDate).format('YYYYMMDD');
        getStrTypeTable({profileId, campaignIds, startDate, endDate}, (StrData, StrGrands,StrrowsToAdd) => {
            //success

            this.setState({
                StrData,
                StrGrands,
                StrrowsToAdd,
                showStrLoader: ""
            })
        }, (err) => {
            //error
            alert(err);
        });
    }

    getCstTypeCall = () => {
        let profileId = this.state.selectedProfile.value;
        let campaignIds = this.state.selectedCampaign.map(campaign => {
            return campaign.value;
        })
        let startDate = moment(this.state.startDate).format('YYYYMMDD');
        let endDate = moment(this.state.endDate).format('YYYYMMDD');
        getCstTypeTable({profileId, campaignIds, startDate, endDate}, (CstData, CstGrands,CstrowsToAdd) => {
            //success

            this.setState({
                CstData,
                CstGrands,
                CstrowsToAdd,
                showCstLoader: ""
            })
        }, (err) => {
            //error
            alert(err);
        });
    }

    getProTypeCall = () => {
        let profileId = this.state.selectedProfile.value;
        let campaignIds = this.state.selectedCampaign.map(campaign => {
            return campaign.value;
        })
        let startDate = moment(this.state.startDate).format('YYYYMMDD');
        let endDate = moment(this.state.endDate).format('YYYYMMDD');
        getProTypeTable({profileId, campaignIds, startDate, endDate}, (ProData, ProGrands,ProrowsToAdd) => {
            //success

            this.setState({
                ProData,
                ProGrands,
                ProrowsToAdd,
                showProLoader: ""
            })
        }, (err) => {
            //error
            alert(err);
        });
    }

    getPreTypeCall = () => {
        let profileId = this.state.selectedProfile.value;
        let campaignIds = this.state.selectedCampaign.map(campaign => {
            return campaign.value;
        })
        let startDate = moment(this.state.startDate).format('YYYYMMDD');
        let endDate = moment(this.state.endDate).format('YYYYMMDD');
        getPreTypeTable({profileId, campaignIds, startDate, endDate}, (PreData, PreGrands,PrerowsToAdd) => {
            //success

            this.setState({
                PreData,
                PreGrands,
                PrerowsToAdd,
                showPreLoader: ""
            })
        }, (err) => {
            //error
            alert(err);
        });
    }

    getPreYTDTypeCall = () => {
        let profileId = this.state.selectedProfile.value;
        let campaignIds = this.state.selectedCampaign.map(campaign => {
            return campaign.value;
        })
        let startDate = moment(this.state.startDate).format('YYYYMMDD');
        let endDate = moment(this.state.endDate).format('YYYYMMDD');
        getPreYTDTypeTable({profileId, campaignIds, startDate, endDate}, (PreYTDData, PreYTDGrands,PreYTDrowsToAdd) => {
            //success
            this.setState({
                PreYTDData,
                PreYTDGrands,
                PreYTDrowsToAdd,
                showPreYTDLoader: "",
                disableFilters:false
            })
        }, (err) => {
            //error
            this.setState({
                disableFilters:false
            })
        });
    }

    getMTDCall = () => {
        let profileId = this.state.selectedProfile.value;
        let campaignIds = this.state.selectedCampaign.map(campaign => {
            return campaign.value;
        })
        let startDate = moment(this.state.startDate).format('YYYYMMDD');
        let endDate = moment(this.state.endDate).format('YYYYMMDD');
        getMTDDataCall({profileId, campaignIds, startDate, endDate}, (mtdData) => {
            //success
            this.getMTDPercentageCall(mtdData);
        }, (err) => {
            //error
            alert(err);
        });
    }
    getMTDPercentageCall = (cardData) => {
        let profileId = this.state.selectedProfile.value;
        let campaignIds = this.state.selectedCampaign.map(campaign => {
            return campaign.value;
        })
        let startDate = moment(this.state.startDate).format('YYYYMMDD');
        let endDate = moment(this.state.endDate).format('YYYYMMDD');
        getMTDPercCall({profileId, campaignIds, startDate, endDate, cardData}, (mtdDataLeft, mtdDataRight) => {
            //success
            this.setState({
                mtdDataLeft,
                mtdDataRight,
                showMOMLoader: "",
            })
        }, (err) => {
            //error
            alert(err);
        });
    }

    getWOWCall = () => {
        let profileId = this.state.selectedProfile.value;
        let campaignIds = this.state.selectedCampaign.map(campaign => {
            return campaign.value;
        })
        let startDate = moment(this.state.startDate).format('YYYYMMDD');
        let endDate = moment(this.state.endDate).format('YYYYMMDD');
        getWOWDataCall({profileId, campaignIds, startDate, endDate}, (mtdData) => {
            //success
            this.getWOWPercentageCall(mtdData);
        }, (err) => {
            //error
            alert(err);
        });
    }
    getWOWPercentageCall = (cardData) => {
        let profileId = this.state.selectedProfile.value;
        let campaignIds = this.state.selectedCampaign.map(campaign => {
            return campaign.value;
        })
        let startDate = moment(this.state.startDate).format('YYYYMMDD');
        let endDate = moment(this.state.endDate).format('YYYYMMDD');
        getWOWPercCall({profileId, campaignIds, startDate, endDate, cardData}, (wowDataLeft, wowDataRight) => {
            //success
            this.setState({
                wowDataLeft,
                wowDataRight,
                showWOWLoader: ""
            })
        }, (err) => {
            //error
            alert(err);
        });
    }

    getDODCall = () => {
        let profileId = this.state.selectedProfile.value;
        let campaignIds = this.state.selectedCampaign.map(campaign => {
            return campaign.value;
        })
        let startDate = moment(this.state.startDate).format('YYYYMMDD');
        let endDate = moment(this.state.endDate).format('YYYYMMDD');
        getDODDataCall({profileId, campaignIds, startDate, endDate}, (mtdData) => {
            //success
            this.getDODPercentageCall(mtdData);
        }, (err) => {
            //error
            alert(err);
        });
    }
    getDODPercentageCall = (cardData) => {
        let profileId = this.state.selectedProfile.value;
        let campaignIds = this.state.selectedCampaign.map(campaign => {
            return campaign.value;
        })
        let startDate = moment(this.state.startDate).format('YYYYMMDD');
        let endDate = moment(this.state.endDate).format('YYYYMMDD');
        getDODPercCall({profileId, campaignIds, startDate, endDate, cardData}, (dodDataLeft, dodDataRight) => {
            //success
            this.setState({
                dodDataLeft,
                dodDataRight,
                showDODLoader: ""
            })
        }, (err) => {
            //error
            alert(err);
        });
    }

    getWTDCall = () => {
        let profileId = this.state.selectedProfile.value;
        let campaignIds = this.state.selectedCampaign.map(campaign => {
            return campaign.value;
        })
        let startDate = moment(this.state.startDate).format('YYYYMMDD');
        let endDate = moment(this.state.endDate).format('YYYYMMDD');
        getWTDDataCall({profileId, campaignIds, startDate, endDate}, (mtdData) => {
            //success
            this.getWTDPercentageCall(mtdData);
        }, (err) => {
            //error
            alert(err);
        });
    }
    getWTDPercentageCall = (cardData) => {
        let profileId = this.state.selectedProfile.value;
        let campaignIds = this.state.selectedCampaign.map(campaign => {
            return campaign.value;
        })
        let startDate = moment(this.state.startDate).format('YYYYMMDD');
        let endDate = moment(this.state.endDate).format('YYYYMMDD');
        getWTDPercCall({profileId, campaignIds, startDate, endDate, cardData}, (wtdDataLeft, wtdDataRight) => {
            //success
            this.setState({
                wtdDataLeft,
                wtdDataRight,
                showWTDLoader:""
            })
        }, (err) => {
            //error
            alert(err);
        });
    }

    getYTDCall = () => {
        let profileId = this.state.selectedProfile.value;
        let campaignIds = this.state.selectedCampaign.map(campaign => {
            return campaign.value;
        })
        let startDate = moment(this.state.startDate).format('YYYYMMDD');
        let endDate = moment(this.state.endDate).format('YYYYMMDD');
        getYTDDataCall({profileId, campaignIds, startDate, endDate}, (mtdData) => {
            //success
            this.getYTDPercentageCall(mtdData);
        }, (err) => {
            //error
            alert(err);
        });
    }
    getYTDPercentageCall = (cardData) => {
        let profileId = this.state.selectedProfile.value;
        let campaignIds = this.state.selectedCampaign.map(campaign => {
            return campaign.value;
        })
        let startDate = moment(this.state.startDate).format('YYYYMMDD');
        let endDate = moment(this.state.endDate).format('YYYYMMDD');
        getYTDPercCall({profileId, campaignIds, startDate, endDate, cardData}, (ytdDataLeft, ytdDataRight) => {
            //success
            this.setState({
                ytdDataLeft,
                ytdDataRight,
                showYTDLoader: ""
            })
        }, (err) => {
            //error
            alert(err);
        });
    }
    onDateChange = (range) => {
        let startDate = moment(range.startDate).format('l');
        let endDate = moment(range.endDate).format('l');
        this.setState({
            startDate: startDate,
            endDate: endDate,
            selectedDate: startDate + " - " + endDate
        }, () => {
            this.setLocalStorage("selectedDate",(startDate + " - " + endDate));
            this.setLocalStorage("startDate",startDate);
            this.setLocalStorage("endDate", endDate);
            this.openCalender();
            this.allApiCalls();
        })
    }

    openCalender = () => {
        this.setState({
            open: !this.state.open
        }, () => {
            this.callTopCampaignsLoader();
        })
    }

    reloadData = (name) => {
        if (this.state.selectedProfile &&
            this.state.selectedCampaign &&
            this.state.selectedDate != "") {
            if (name == "Performance") {
                this.setState({showPerfLoader: name}, () => {
                    this.getPerformanceChartCall();
                    this.getPerformancePercentages();
                })
            }
            if (name == "Efficiency") {
                this.setState({showEffiLoader: name}, () => {
                    this.getEfficiencyChartCall();
                    this.getEfficiencyPercentages();
                })
            }
            
            if (name == "Awareness") {
                this.setState({showAwarLoader: name}, () => {
                    this.getAwarenessChartCall();
                    this.getAwarenessPercentages();
                })
            }
            
            if (name == 1) {
                this.setState({showMOMLoader: 1}, () => {
                    this.getMTDCall();
                })
            }
            
            if (name == 2) {
                this.setState({showWOWLoader: 2}, () => {
                    this.getWOWCall();
                })
            }
            
            if (name == 3) {
                this.setState({showDODLoader: 3}, () => {
                    this.getDODCall();
                })
            }
            if (name == 4) {
                this.setState({showYTDLoader: 4}, () => {
                    this.getYTDCall();
                })
            }
            
            if (name == 5) {
                this.setState({showWTDLoader: 5}, () => {
                    this.getWTDCall();
                })
            }
            
            if (name == 6) {
                this.setState({showADLoader: 6}, () => {
                    this.getAdTypeCall();
                })
            }
            
            if (name == 7) {
                this.setState({showStrLoader: 7}, () => {
                    this.getStrTypeCall();
                })
            }
            
            if (name == 9) {
                this.setState({showCstLoader: 9}, () => {
                    this.getCstTypeCall();
                })
            }
            
            if (name == 8) {
                this.setState({showProLoader: 8}, () => {
                    this.getProTypeCall();
                })
            }
            
            if (name == 10) {
                this.setState({showPreLoader: 10}, () => {
                    this.getPreTypeCall();
                })
            }
            
            if (name == 11) {
                this.setState({showPreYTDLoader: 11}, () => {
                    this.getPreYTDTypeCall();
                })
            }            
        } 
        if (name == "TOP CAMPAIGNS"){
                this.callTopCampaignsLoader();
            }     
    }

    /**
     * clear campaigns
     */
    clearCampaigns=()=>{
        this.setState({
            selectedCampaign:null,
            selectedProduct: null,
            selectedStrategy: null,
        })
    }

    render() {
        const {classes} = this.props;
        return (
            <>
                <Helmet>
                    <title>Ad-Tech Advertising Visuals</title>
                </Helmet>
                <Card classes={{root: classes.card}}>
                    <Typography variant="h6" className={`${classes.pageTitle}`} noWrap>
                        Advertising Visuals
                    </Typography>
                    <div className="relative">
                        {this.state.showFilterLoader ?
                            <ContainerLoader
                                height={30}
                                classStyles={"mt-1"}/>
                            :
                            ""}
                        <AdFilters
                            disableFilters={this.state.disableFilters}
                            profileOptions={this.state.profileOptions}
                            campaignsOptions={this.state.campaignOptions}
                            strategyOptions={this.state.strategyOptions}
                            selectedStrategy={this.state.selectedStrategy}
                            productOptions={this.state.productOptions}
                            selectedProfile={this.state.selectedProfile}
                            selectedCampaign={this.state.selectedCampaign}
                            onProductChange={this.onProductChange}
                            selectedProduct={this.state.selectedProduct}
                            selectedDate={this.state.selectedDate}
                            onProfileChange={this.onProfileChange}
                            onCampaignChange={this.onCampaignChange}
                            onStrategyChange={this.onStrategyChange}
                            closeMenuOnSelect={this.state.closeMenuOnSelect}

                            dateRangeObj={this.state.dateRangeObj}
                            getValue={this.onDateChange}
                            datepickerClass = {classes.datepickerClass}
                        />
                    </div>
                    <AsinComCards
                        scoreCards={this.state.scoreCards}
                        showComcardsLoader={this.state.showComcardsLoader}
                    />

                    {this.state.showHidden?
                    <>
                    {/* Performance Graph Chart */}
                    <GraphChart
                        customClass={"performance"}
                        cardData={this.state.perfPercentagesData}
                        dataChart={this.state.perfData}
                        heading={"Performance"}
                        types={["bar", "spline", "spline"]}
                        colors={['#21bf73', '#4a47a3', '#ffc107']}
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
                        cardData={this.state.effiPercentagesData}
                        dataChart={this.state.effiData}
                        heading={"Efficiency"}
                        types={["bar", "spline", "spline"]}
                        colors={['#ce93d8', '#ffc107', '#000000']}
                        y1={"CPA/CPC"}
                        y2={"ROAS"}
                        tooltip={true}
                        reloadApiCall={this.reloadData}
                        showLoader={this.state.showEffiLoader}
                    />
                    {/* Awareness Chart */}
                    <GraphChart
                        customClass={"awareness"}
                        cardData={this.state.awarPercentagesData}
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

                    <ComCards
                        mtdData={this.state.mtdData}
                        mtdDataLeft={this.state.mtdDataLeft}
                        mtdDataRight={this.state.mtdDataRight}
                        reloadData={this.reloadData}

                        wowDataLeft={this.state.wowDataLeft}
                        wowDataRight={this.state.wowDataRight}

                        dodDataLeft={this.state.dodDataLeft}
                        dodDataRight={this.state.dodDataRight}

                        ytdDataLeft={this.state.ytdDataLeft}
                        ytdDataRight={this.state.ytdDataRight}

                        wtdDataLeft={this.state.wtdDataLeft}
                        wtdDataRight={this.state.wtdDataRight}

                        showMOMLoader={this.state.showMOMLoader}
                        showWOWLoader={this.state.showWOWLoader}
                        showDODLoader={this.state.showDODLoader}
                        showYTDLoader={this.state.showYTDLoader}
                        showWTDLoader={this.state.showWTDLoader}
                    />

                    <AdVisualTables
                        AdData={this.state.AdData}
                        AdGrands={this.state.AdGrands}
                        reloadData={this.reloadData}
                        showADLoader={this.state.showADLoader}
                        rowsToAdd={this.state.rowsToAdd}
                        StrrowsToAdd={this.state.StrrowsToAdd}
                        ProrowsToAdd={this.state.ProrowsToAdd}
                        PrerowsToAdd={this.state.PrerowsToAdd}
                        CstrowsToAdd={this.state.CstrowsToAdd}
                        PreYTDrowsToAdd={this.state.PreYTDrowsToAdd}
                        StrData={this.state.StrData}
                        StrGrands={this.state.StrGrands}
                        reloadData={this.reloadData}
                        showStrLoader={this.state.showStrLoader}


                        CstData={this.state.CstData}
                        CstGrands={this.state.CstGrands}
                        reloadData={this.reloadData}
                        showCstLoader={this.state.showCstLoader}

                        ProData={this.state.ProData}
                        ProGrands={this.state.ProGrands}
                        reloadData={this.reloadData}
                        showProLoader={this.state.showProLoader}

                        PreData={this.state.PreData}
                        PreGrands={this.state.PreGrands}
                        reloadData={this.reloadData}
                        showPreLoader={this.state.showPreLoader}

                        PreYTDData={this.state.PreYTDData}
                        PreYTDGrands={this.state.PreYTDGrands}
                        reloadData={this.reloadData}
                        showPreYTDLoader={this.state.showPreYTDLoader}
                    />
                    </>
                    :""}

                    <div className="pt-5">
                        <TopCampaignsTable
                            onTopXCampaignChange={this.onTopXCampaignChange}
                            campaignsData = {this.state.TopCampaignsData}
                            topXValue = {this.state.TopXCampaigns}
                            dataType={12}
                            showLoader={this.state.TopXCampaignsLoader}
                            reloadApiCall={this.reloadData}
                        />
                    </div>
                </Card>
            </>
        );
    }
}

export default withStyles(styles)(connect(null)(AdVisuals));