import Tooltip from "@material-ui/core/Tooltip";
import React from 'react';
import {helperDateFunction,profilesMapping,comCardMappings,AddRows} from "./../../../helper/helper";


export const ADVERTISING_VISUALS_PROFILES_URL = window.baseUrl+"/manager/visuals"; 
export const ADVERTISING_VISUALS_CAMPAIGNS_URL = window.baseUrl+"/manager/visuals/getCampaigns"; 
export const ADVERTISING_VISUALS_TAG_CAMPAIGNS_URL = window.baseUrl+"/manager/visuals/getTagCampaigns"; 



export const ADVERTISING_VISUALS_TOP_CAMPAIGNS_URL = window.baseUrl+"/manager/visuals/spPopulatePresentationTopCampiagnTable";
export const ADVERTISING_VISUALS_SCORE_CARD_URL = window.baseUrl+"/manager/visuals/spCalculateAMSScoreCards";
export const ADVERTISING_VISUALS_PERFORMANCE_URL = window.baseUrl+"/manager/visuals/spPopulateCampaignPerformance";
export const ADVERTISING_VISUALS_EFFICIENCY_URL = window.baseUrl+"/manager/visuals/spPopulateCampaignEfficiency";
export const ADVERTISING_VISUALS_AWARENESS_URL = window.baseUrl+"/manager/visuals/spPopulateCampaignAwareness";
export const ADVERTISING_VISUALS_MTD_CARD_URL = window.baseUrl+"/manager/visuals/spPopulateCampaignMTD";
export const ADVERTISING_VISUALS_MTD_PERC_URL = window.baseUrl+"/manager/visuals/spCalculateMTDPercentages";


export const ADVERTISING_VISUALS_WOW_CARD_URL = window.baseUrl+"/manager/visuals/spPopulatePresentationWowTable";
export const ADVERTISING_VISUALS_WOW_PERC_URL = window.baseUrl+"/manager/visuals/spCalculateWowPercentages";


export const ADVERTISING_VISUALS_DOD_CARD_URL = window.baseUrl+"/manager/visuals/spPopulatePresentationDODTable";
export const ADVERTISING_VISUALS_DOD_PERC_URL = window.baseUrl+"/manager/visuals/spCalculateDODPrecentages";


export const ADVERTISING_VISUALS_YTD_CARD_URL = window.baseUrl+"/manager/visuals/spPopulatePresentationCpgYTDTable";
export const ADVERTISING_VISUALS_YTD_PERC_URL = window.baseUrl+"/manager/visuals/spCalculateYTDPercentages";


export const ADVERTISING_VISUALS_WTD_CARD_URL = window.baseUrl+"/manager/visuals/spPopulatePresentationWTDTable";
export const ADVERTISING_VISUALS_WTD_PERC_URL = window.baseUrl+"/manager/visuals/spCalculateWTDPercentages";


export const ADVERTISING_VISUALS_AD_TYPE_URL = window.baseUrl+"/manager/visuals/spPopulatePresentationAdType";
export const ADVERTISING_VISUALS_STR_TYPE_URL = window.baseUrl+"/manager/visuals/spCalculateStragTypeCampTagingVisual";
export const ADVERTISING_VISUALS_CST_TYPE_URL = window.baseUrl+"/manager/visuals/spCalculateCustomCampTagingVisual";
export const ADVERTISING_VISUALS_PROD_TYPE_URL = window.baseUrl+"/manager/visuals/spCalculateProdTypeCampTagingVisual";
export const ADVERTISING_VISUALS_PRE_TDAYS_URL = window.baseUrl+"/manager/visuals/spPerformancePre30Day";
export const ADVERTISING_VISUALS_PERF_YTD_URL = window.baseUrl+"/manager/visuals/spPerformanceytd";

export const ADVERTISING_VISUALS_PERCNT_AWARE_URL = window.baseUrl+"/manager/visuals/spCalculateAwarenessPrecentages";
export const ADVERTISING_VISUALS_PERCNT_PERF_URL = window.baseUrl+"/manager/visuals/spCalculatePreformancePrecentages";
export const ADVERTISING_VISUALS_PERCNT_EFFI_URL = window.baseUrl+"/manager/visuals/spCalculateEfficiencyPrecentages";



export function getProfiles(callback, errorcallback){
    axios.get(ADVERTISING_VISUALS_PROFILES_URL)
    .then(res => {
      if(callback != null){
        let data = profilesMapping(res.data);
        callback(data);
      }
    })
    .catch(err => {
      if(errorcallback != null){
          errorcallback(err);
      }
    })
  }
  
export function getCampaignsCall(params,callback, errorcallback){
    axios.get(ADVERTISING_VISUALS_CAMPAIGNS_URL,{
      params: {
        profileId: params
      }
    })
    .then(res => {
        
      if(callback != null){
        let campaignOptions = res.data.campaigns.map((obj, idx) => {
          return {
             label: ( <Tooltip placement="top" title={obj.name} arrow>
                        <span>{                     
                                (obj.name.length>30?
                                  obj.name.substr(0,30)+"..."
                                  :
                                  obj.name)
                              }
                        </span>
                      </Tooltip>
                       ),
             value: obj.campaignId,
             key: idx
          }
        })

        let strategyOptions = res.data.stretagyType.map((obj, idx) => {
            return {
               label: obj.tag,
               value: obj.fkTagId,
               key: obj.type
            }
          })
        
        let ProductOptions = res.data.productType.map((obj, idx) => {
            return {
               label: obj.tag,
               value: obj.fkTagId,
               key: obj.type
            }
          })
        campaignOptions.unshift({label: "Select All",value:"All"})
        callback(campaignOptions,strategyOptions,ProductOptions);
      }
    })
    .catch(err => {
      if(errorcallback != null){
         errorcallback(err);
      }
    })
  }

export function getTagCampaigns(params,callback, errorcallback){
    axios.get(ADVERTISING_VISUALS_TAG_CAMPAIGNS_URL,{
        params: {
            fkTagIdS: params.fkTagIdS,
            fkTagIdP: params.fkTagIdP,
            strategyType: params.strategyType,
            productType: params.productType,
            profileId: params.profileId
        }
    })
    .then(res => {
        if(callback != null){
            if(res.data.campaigns.length !=0){
                let campaignOptions = res.data.campaigns.map((obj, idx) => {
                    return {
                       label: ( <Tooltip placement="top" title={obj.campaignName} arrow>
                                  <span>{                     
                                          (obj.campaignName.length>30?
                                            obj.campaignName.substr(0,30)+"..."
                                            :
                                            obj.campaignName)
                                        }
                                  </span>
                                </Tooltip>
                                 ),
                       value: obj.campaignId,
                       key: idx
                    }
                  })
                callback(campaignOptions);
            }
        }
        })
    .catch(err => {
        if(errorcallback != null){
            errorcallback(err);
        }
    })
}

export function getScoreCardCall(params,callback, errorcallback){
axios.get(ADVERTISING_VISUALS_SCORE_CARD_URL,
    {
    params:{
        profileId: params.profileId,
        campaignId: params.campaignIds.toString(),
        startDate: params.startDate,
        endDate: params.endDate,
    }
    })
.then(res => {
    
    if(callback != null){
    if(res.data.length!=0){
        callback(res.data);
    } else{
        callback([]);
    }
    }
})
.catch(err => {
    if(errorcallback != null){
        errorcallback(err);
    }
})
}

export function getPerfChartCall(params,callback, errorcallback){
axios.get(ADVERTISING_VISUALS_PERFORMANCE_URL,
    {
    params:{
        profileId: params.profileId,
        campaignId: params.campaignIds.toString(),
        startDate: params.startDate,
        endDate: params.endDate,
    }
    })
.then(res => {
    if(callback != null){
        
    if(res.data.length!=0){
        let cost = res.data.map(function(obj){
        return +obj.cost;
    });
    let rev = res.data.map(function(obj){
        return +obj.revenue;
    });
    let acos = res.data.map(function(obj){
        return +obj.acos;
    });
    let datekey = res.data.map(function(obj){
        return helperDateFunction(obj.date_key);
    });

    datekey.unshift("x");
    cost.unshift("Cost");
    rev.unshift("Rev");
    acos.unshift("ACOS");

    let getPY2Min = Math.min.apply(Math, rev)
    callback([datekey,rev,cost,acos],getPY2Min);
    } else{
        callback([],null);
    }
    }
})
.catch(err => {
    if(errorcallback != null){
        errorcallback(err);
    }
})
}
export function getEffiChartCall(params,callback, errorcallback){
    axios.get(ADVERTISING_VISUALS_EFFICIENCY_URL,
    {
        params:{
        profileId: params.profileId,
        campaignId: params.campaignIds.toString(),
        startDate: params.startDate,
        endDate: params.endDate,
        }
    })
    .then(res => {
    if(callback != null){
        
        if(res.data.length!=0){
            let roas = res.data.map(function(obj){
                return +obj.roas;
            });
            let cpa = res.data.map(function(obj){
                return +obj.cpa;
            });
            let cpc = res.data.map(function(obj){
                return +obj.cpc;
            });
            let dateKeyE = res.data.map(function(obj){
                return helperDateFunction(obj.date_key);
            });
            
            dateKeyE.unshift("x");
            roas.unshift("ROAS");
            cpa.unshift("CPA");
            cpc.unshift("CPC");
            callback([dateKeyE,roas,cpa,cpc]);
        } else{
            callback([]);
        }
    }
    })
    .catch(err => {
    if(errorcallback != null){
        errorcallback(err);
    }
    })

}

export function getAwarChartCall(params,callback, errorcallback){
axios.get(ADVERTISING_VISUALS_AWARENESS_URL,
    {
    params:{
        profileId: params.profileId,
        campaignId: params.campaignIds.toString(),
        startDate: params.startDate,
        endDate: params.endDate,
    }
    })
.then(res => {
    if(callback != null){
    if(res.data.length!=0){
        let impr = res.data.map(function(obj){
        return +obj.impressions;
        });
        let ctr = res.data.map(function(obj){
            return +obj.CTR;
        });
        let clicks = res.data.map(function(obj){
            return +obj.clicks;
        });
        let date_Akey = res.data.map(function(obj){
            return helperDateFunction(obj.date_key);
        });
        date_Akey.unshift("x");
        impr.unshift("Impressions");
        ctr.unshift("CTR");
        clicks.unshift("Clicks");
        callback([date_Akey,ctr,impr,clicks]);
    } else{
        callback([]);
    }
    }
})
.catch(err => {
    if(errorcallback != null){
        errorcallback(err);
    }
})

}

export function getPerfPercentagesCall (params,callback, errorcallback){
axios.get(ADVERTISING_VISUALS_PERCNT_PERF_URL,
    {
    params:{
        profileId: params.profileId,
        campaignId: params.campaignIds.toString(),
        startDate: params.startDate,
        endDate: params.endDate,
    }
    })
.then(res => {
    if(callback != null){
    let percentages = res.data.spCalculatePreformancePrecentages.map(item=>{
        return item;
    })
    let currencies = res.data.spCalculateCampaignPerformanceGrandTotal.map(item=>{
        return item;
        })
    
    let cardData = [
        {
        prefix: "$",
        title:"Revenue",
        label:percentages[0].revenue_perc,
        currency:currencies[0].revenue
        },
        {
        prefix: "$",
        title:"Cost",
        label:percentages[0].cost_perc,
        currency:currencies[0].cost
        },
        {
        prefix: "%",
        title:"Acos",
        label:percentages[0].acos_perc,
        currency:currencies[0].acos_
        }
    ]

    callback(cardData);
    }
})
.catch(err => {
    if(errorcallback != null){
        errorcallback(err);
    }
})

}

export function getEffiPercentagesCall(params,callback, errorcallback){
axios.get(ADVERTISING_VISUALS_PERCNT_EFFI_URL,
    {
    params:{
        profileId: params.profileId,
        campaignId: params.campaignIds.toString(),
        startDate: params.startDate,
        endDate: params.endDate,
    }
    })
.then(res => {
    if(callback != null){
        
    let percentages = res.data.spCalculateEfficiencyPrecentages.map(item=>{
        return item;
        })
        let currencies = res.data.spCalculateCampaignEfficiencyGrandTotal.map(item=>{
        return item;
        })
        
        let cardData = [
        {
        prefix: "$",
        title:"CPC",
        label:percentages[0].prct_cpc,
        currency:currencies[0].cpc
        },
        {
        prefix: "$",
        title:"ROAS",
        label:percentages[0].prct_roas,
        currency:currencies[0].roas
        },
        {
        prefix: "$",
        title:"CPA",
        label:percentages[0].prct_cpa,
        currency:currencies[0].cpa
        }
        ]
    callback(cardData);
    }
})
.catch(err => {
    if(errorcallback != null){
        errorcallback(err);
    }
})

}

export function getAwarPercentagesCall(params,callback, errorcallback){
axios.get(ADVERTISING_VISUALS_PERCNT_AWARE_URL,
    {
    params:{
        profileId: params.profileId,
        campaignId: params.campaignIds.toString(),
        startDate: params.startDate,
        endDate: params.endDate,
    }
    })
.then(res => {
    if(callback != null){
        
    let percentages = res.data.spCalculateAwarenessPrecentages.map(item=>{
        return item;
        })
    let currencies = res.data.spCalculateCampaignAwarenessGrandTotal.map(item=>{
        return item;
        })
        
    let cardData = [
        {
            prefix: "",
        title:"Impressions",
        label:percentages[0].impressions_perc,
        currency:currencies[0].impressions
        },
        {
        prefix: "",
        title:"Clicks",
        label:percentages[0].clicks_perc,
        currency:currencies[0].clicks
        },
        {
        prefix: "%",
        title:"CTR",
        label:percentages[0].ctr_perc,
        currency:currencies[0].ctr
        }
        ]
    callback(cardData);
    }
})
.catch(err => {
    if(errorcallback != null){
        errorcallback(err);
    }
})

}

export function getMTDDataCall(params,callback, errorcallback){
    axios.get(ADVERTISING_VISUALS_MTD_CARD_URL,
        {
        params:{
            profileId: params.profileId,
            campaignId: params.campaignIds.toString(),
            startDate: params.startDate,
            endDate: params.endDate,
        }
        })
    .then(res => {
        let data = res.data[0];
        if(callback != null){            
            let cardData = [
                {
                    prefix: "",
                    title:"Impressions",
                    label: "",
                    currency:data.impressions
                },
                {
                    prefix: "$",
                    title:"Cost",
                    label:"",
                    currency:data.cost
                },
                {
                    prefix: "$",
                    title:"Rev",
                    label:"",
                    currency:data.revenue
                },
                {
                    prefix: "%",
                    title:"ACOS",
                    label:"",
                    currency:data.acos_
                },
                {
                    prefix: "$",
                    title:"CPC",
                    label:"",
                    currency:data.CPC
                },
                {
                    prefix: "$",
                    title:"ROAS",
                    label:"",
                    currency:data.ROAS
                }
            ]
        callback(cardData);
        }
    })
    .catch(err => {
        if(errorcallback != null){
            errorcallback(err);
        }
    })
    
    }

export function getMTDPercCall(params,callback, errorcallback){
    axios.get(ADVERTISING_VISUALS_MTD_PERC_URL,
        {
        params:{
            profileId: params.profileId,
            campaignId: params.campaignIds.toString(),
            startDate: params.startDate,
            endDate: params.endDate,
        }
        })
    .then(res => {
        if(callback != null){
        let data = res.data[0];           
        let cardData = comCardMappings(params.cardData,data);
        let partOne = [];
        let partTwo = [];
        cardData.map((item,idx)=>{
            if(idx<3){
                partOne.push(item)
            } else{
                partTwo.push(item)
            }

        })
        callback(partOne,partTwo);
        }
    })
    .catch(err => {
        if(errorcallback != null){
            errorcallback(err);
        }
    })
}


export function getWOWDataCall(params,callback, errorcallback){
    axios.get(ADVERTISING_VISUALS_WOW_CARD_URL,
        {
        params:{
            profileId: params.profileId,
            campaignId: params.campaignIds.toString(),
            startDate: params.startDate,
            endDate: params.endDate,
        }
        })
    .then(res => {
        let data = res.data[0];
        if(callback != null){            
            let cardData = [
                {
                    prefix: "",
                    title:"Impressions",
                    label: "",
                    currency:data.impressions
                },
                {
                    prefix: "$",
                    title:"Cost",
                    label:"",
                    currency:data.cost
                },
                {
                    prefix: "$",
                    title:"Rev",
                    label:"",
                    currency:data.revenue
                },
                {
                    prefix: "%",
                    title:"ACOS",
                    label:"",
                    currency:data.acos_
                },
                {
                    prefix: "$",
                    title:"CPC",
                    label:"",
                    currency:data.CPC
                },
                {
                    prefix: "$",
                    title:"ROAS",
                    label:"",
                    currency:data.ROAS
                }
            ]
        callback(cardData);
        }
    })
    .catch(err => {
        if(errorcallback != null){
            errorcallback(err);
        }
    })
    
    }

export function getWOWPercCall(params,callback, errorcallback){
    axios.get(ADVERTISING_VISUALS_WOW_PERC_URL,
        {
        params:{
            profileId: params.profileId,
            campaignId: params.campaignIds.toString(),
            startDate: params.startDate,
            endDate: params.endDate,
        }
        })
    .then(res => {
        if(callback != null){
        let data = res.data[0];           
        let cardData = comCardMappings(params.cardData,data);
        let partOne = [];
        let partTwo = [];
        cardData.map((item,idx)=>{
            if(idx<3){
                partOne.push(item)
            } else{
                partTwo.push(item)
            }

        })
        callback(partOne,partTwo);
        }
    })
    .catch(err => {
        if(errorcallback != null){
            errorcallback(err);
        }
    })
}

export function getDODDataCall(params,callback, errorcallback){
    axios.get(ADVERTISING_VISUALS_DOD_CARD_URL,
        {
        params:{
            profileId: params.profileId,
            campaignId: params.campaignIds.toString(),
            startDate: params.startDate,
            endDate: params.endDate,
        }
        })
    .then(res => {
        let data = res.data[0];
        if(callback != null){            
            let cardData = [
                {
                    prefix: "",
                    title:"Impressions",
                    label: "",
                    currency:data.impressions
                },
                {
                    prefix: "$",
                    title:"Cost",
                    label:"",
                    currency:data.cost
                },
                {
                    prefix: "$",
                    title:"Rev",
                    label:"",
                    currency:data.revenue
                },
                {
                    prefix: "%",
                    title:"ACOS",
                    label:"",
                    currency:data.acos_
                },
                {
                    prefix: "$",
                    title:"CPC",
                    label:"",
                    currency:data.CPC
                },
                {
                    prefix: "$",
                    title:"ROAS",
                    label:"",
                    currency:data.ROAS
                }
            ]
        callback(cardData);
        }
    })
    .catch(err => {
        if(errorcallback != null){
            errorcallback(err);
        }
    })
    
    }

export function getDODPercCall(params,callback, errorcallback){
    axios.get(ADVERTISING_VISUALS_DOD_PERC_URL,
        {
        params:{
            profileId: params.profileId,
            campaignId: params.campaignIds.toString(),
            startDate: params.startDate,
            endDate: params.endDate,
        }
        })
    .then(res => {
        if(callback != null){
        let data = res.data[0];           
        let cardData = comCardMappings(params.cardData,data);
        let partOne = [];
        let partTwo = [];
        cardData.map((item,idx)=>{
            if(idx<3){
                partOne.push(item)
            } else{
                partTwo.push(item)
            }

        })
        callback(partOne,partTwo);
        }
    })
    .catch(err => {
        if(errorcallback != null){
            errorcallback(err);
        }
    })
}

export function getYTDDataCall(params,callback, errorcallback){
    axios.get(ADVERTISING_VISUALS_YTD_CARD_URL,
        {
        params:{
            profileId: params.profileId,
            campaignId: params.campaignIds.toString(),
            startDate: params.startDate,
            endDate: params.endDate,
        }
        })
    .then(res => {
        let data = res.data[0];
        if(callback != null){            
            let cardData = [
                {
                    prefix: "",
                    title:"Impressions",
                    label: "",
                    currency:data.impressions
                },
                {
                    prefix: "$",
                    title:"Cost",
                    label:"",
                    currency:data.cost
                },
                {
                    prefix: "$",
                    title:"Rev",
                    label:"",
                    currency:data.revenue
                },
                {
                    prefix: "%",
                    title:"ACOS",
                    label:"",
                    currency:data.acos_
                },
                {
                    prefix: "$",
                    title:"CPC",
                    label:"",
                    currency:data.CPC
                },
                {
                    prefix: "$",
                    title:"ROAS",
                    label:"",
                    currency:data.ROAS
                }
            ]
        callback(cardData);
        }
    })
    .catch(err => {
        if(errorcallback != null){
            errorcallback(err);
        }
    })
    
    }

export function getYTDPercCall(params,callback, errorcallback){
    axios.get(ADVERTISING_VISUALS_YTD_PERC_URL,
        {
        params:{
            profileId: params.profileId,
            campaignId: params.campaignIds.toString(),
            startDate: params.startDate,
            endDate: params.endDate,
        }
        })
    .then(res => {
        if(callback != null){
        let data = res.data[0];           
        let cardData = comCardMappings(params.cardData,data);
        let partOne = [];
        let partTwo = [];
        cardData.map((item,idx)=>{
            if(idx<3){
                partOne.push(item)
            } else{
                partTwo.push(item)
            }

        })
        callback(partOne,partTwo);
        }
    })
    .catch(err => {
        if(errorcallback != null){
            errorcallback(err);
        }
    })
}

export function getWTDDataCall(params,callback, errorcallback){
    axios.get(ADVERTISING_VISUALS_WTD_CARD_URL,
        {
        params:{
            profileId: params.profileId,
            campaignId: params.campaignIds.toString(),
            startDate: params.startDate,
            endDate: params.endDate,
        }
        })
    .then(res => {
        let data = res.data[0];
        if(callback != null){            
            let cardData = [
                {
                    prefix: "",
                    title:"Impressions",
                    label: "",
                    currency:data.impressions
                },
                {
                    prefix: "$",
                    title:"Cost",
                    label:"",
                    currency:data.cost
                },
                {
                    prefix: "$",
                    title:"Rev",
                    label:"",
                    currency:data.revenue
                },
                {
                    prefix: "%",
                    title:"ACOS",
                    label:"",
                    currency:data.acos_
                },
                {
                    prefix: "$",
                    title:"CPC",
                    label:"",
                    currency:data.CPC
                },
                {
                    prefix: "$",
                    title:"ROAS",
                    label:"",
                    currency:data.ROAS
                }
            ]
        callback(cardData);
        }
    })
    .catch(err => {
        if(errorcallback != null){
            errorcallback(err);
        }
    })
    
    }

export function getWTDPercCall(params,callback, errorcallback){
    axios.get(ADVERTISING_VISUALS_WTD_PERC_URL,
        {
        params:{
            profileId: params.profileId,
            campaignId: params.campaignIds.toString(),
            startDate: params.startDate,
            endDate: params.endDate,
        }
        })
    .then(res => {
        if(callback != null){
        let data = res.data[0];           
        let cardData = comCardMappings(params.cardData,data);
        let partOne = [];
        let partTwo = [];
        cardData.map((item,idx)=>{
            if(idx<3){
                partOne.push(item)
            } else{
                partTwo.push(item)
            }

        })
        callback(partOne,partTwo);
        }
    })
    .catch(err => {
        if(errorcallback != null){
            errorcallback(err);
        }
    })
}

export function getAdTypeTable(params,callback, errorcallback){
    axios.get(ADVERTISING_VISUALS_AD_TYPE_URL,
        {
        params:{
            profileId: params.profileId,
            campaignId: params.campaignIds.toString(),
            startDate: params.startDate,
            endDate: params.endDate,
        }
        })
    .then(res => {
        let data = res.data;
        if(callback != null){            
        let Adgrand = data.grandTotals.length >0?data.grandTotals:[];
        let Adtable = data.tableData.length >0?data.tableData:[];
        
        let rowsToAdd = AddRows(Adtable.length);
        callback(Adtable,Adgrand,rowsToAdd);
        }
    })
    .catch(err => {
        if(errorcallback != null){
            errorcallback(err);
        }
    })
    
}

export function getStrTypeTable(params,callback, errorcallback){
    axios.get(ADVERTISING_VISUALS_STR_TYPE_URL,
        {
        params:{
            profileId: params.profileId,
            campaignId: params.campaignIds.toString(),
            startDate: params.startDate,
            endDate: params.endDate,
        }
        })
    .then(res => {
        let data = res.data;
        if(callback != null){            
        let Adgrand = data.grandTotals.length >0?data.grandTotals:[];
        let Adtable = data.tableData.length >0?data.tableData:[];
        let rowsToAdd = AddRows(Adtable.length);
        callback(Adtable,Adgrand,rowsToAdd);
        }
    })
    .catch(err => {
        if(errorcallback != null){
            errorcallback(err);
        }
    })
    
}

export function getCstTypeTable(params,callback, errorcallback){
    axios.get(ADVERTISING_VISUALS_CST_TYPE_URL,
        {
        params:{
            profileId: params.profileId,
            campaignId: params.campaignIds.toString(),
            startDate: params.startDate,
            endDate: params.endDate,
        }
        })
    .then(res => {
        let data = res.data;
        if(callback != null){            
        let Adgrand = data.grandTotals.length >0?data.grandTotals:[];
        let Adtable = data.tableData.length >0?data.tableData:[];
       
        let rowsToAdd = AddRows(Adtable.length)
        callback(Adtable,Adgrand,rowsToAdd);
        }
    })
    .catch(err => {
        if(errorcallback != null){
            errorcallback(err);
        }
    })
    
}

export function getProTypeTable(params,callback, errorcallback){
    axios.get(ADVERTISING_VISUALS_PROD_TYPE_URL,
        {
        params:{
            profileId: params.profileId,
            campaignId: params.campaignIds.toString(),
            startDate: params.startDate,
            endDate: params.endDate,
        }
        })
    .then(res => {
        let data = res.data;
        if(callback != null){            
        let Adgrand = data.grandTotals.length >0?data.grandTotals:[];
        let Adtable = data.tableData.length >0?data.tableData:[];
        let rowsToAdd = AddRows(Adtable.length)
        callback(Adtable,Adgrand,rowsToAdd);
        }
    })
    .catch(err => {
        if(errorcallback != null){
            errorcallback(err);
        }
    })
    
}

export function getPreTypeTable(params,callback, errorcallback){
    axios.get(ADVERTISING_VISUALS_PRE_TDAYS_URL,
        {
        params:{
            profileId: params.profileId,
            campaignId: params.campaignIds.toString(),
            startDate: params.startDate,
            endDate: params.endDate,
        }
        })
    .then(res => {
        let data = res.data;
        if(callback != null){            
        let Adgrand = data.grandTotals.length >0?data.grandTotals:[];
        let Adtable = data.tableData.length >0?data.tableData:[];
        let rowsToAdd = AddRows(Adtable.length)
        callback(Adtable,Adgrand,rowsToAdd);
        }
    })
    .catch(err => {
        if(errorcallback != null){
            errorcallback(err);
        }
    })
    
}


export function getPreYTDTypeTable(params,callback, errorcallback){
    axios.get(ADVERTISING_VISUALS_PERF_YTD_URL,
        {
        params:{
            profileId: params.profileId,
            campaignId: params.campaignIds.toString(),
            startDate: params.startDate,
            endDate: params.endDate,
        }
        })
    .then(res => {
        let data = res.data;
        if(callback != null){            
        let Adgrand = data.grandTotals.length >0?data.grandTotals:[];
        let Adtable = data.tableData.length >0?data.tableData:[];
        
        let rowsToAdd = AddRows(Adtable.length)
        callback(Adtable,Adgrand,rowsToAdd);
        }
    })
    .catch(err => {
        if(errorcallback != null){
            errorcallback(err);
        }
    })
    
}

export function getTopCampaigns(params,callback, errorcallback){
    axios.get(ADVERTISING_VISUALS_TOP_CAMPAIGNS_URL,
        {
        params:{
            profileId: params.profileId,
            startDate: params.startDate,
            endDate: params.endDate,
            TopXcampaign: params.TopXCampaigns
        }
        })
    .then(res => {
        let data = res.data;
        if(callback != null){            
        callback(data);
        }
    })
    .catch(err => {
        if(errorcallback != null){
            errorcallback(err);
        }
    })
}