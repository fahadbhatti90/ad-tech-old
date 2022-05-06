import Tooltip from "@material-ui/core/Tooltip";
import React from 'react';
import {helperDateFunction,profilesMapping} from "./../../../helper/helper";


/**
 * ASIN PERFORMANCE API URLS
 */
export const ASIN_VISUALS_PROFILE_URL = window.baseUrl+"/manager/asinvisuals";
export const ASIN_VISUALS_CAMPAIGNS_URL = window.baseUrl+"/manager/visuals/getAsinPerformanceVisualsCampaigns";
export const ASIN_VISUALS_ASINS_URL = window.baseUrl+"/manager/visuals/getAsinPerformanceVisualsAsins";
export const ASIN_VISUALS_SCORECARD_URL = window.baseUrl+"/manager/visuals/asinLevelSpData?sp=spCalculateAMSScoreCardsAsinLevel";
export const ASIN_VISUALS_PCHART_URL = window.baseUrl+"/manager/visuals/asinLevelSpData?sp=spPopulateAsinPerformance";
export const ASIN_VISUALS_ECHART_URL = window.baseUrl+"/manager/visuals/asinLevelSpData?sp=spPopulateAsinLevelEfficiency";
export const ASIN_VISUALS_ACHART_URL = window.baseUrl+"/manager/visuals/asinLevelSpData?sp=spPopulateAMSAwarenessAsinLevel";
export const ASIN_VISUALS_PERF_CARD_URL = window.baseUrl+"/manager/visuals/asinLevelSpData?sp=spCalculateAsinLevelPreformancePrecentages,spCalculateAsinLevelPerformanceGrandTotal";
export const ASIN_VISUALS_EFFI_CARD_URL = window.baseUrl+"/manager/visuals/asinLevelSpData?sp=spCalculateAsinLevelEfficiencyPrecentages,spCalculateAsinLevelEfficiencyGrandTotal";
export const ASIN_VISUALS_AWAR_CARD_URL = window.baseUrl+"/manager/visuals/asinLevelSpData?sp=spCalculateAMSAwarenessAsinLevelPercentage,spPopulateAMSAwarenessAsinLevelGrandTotal";
export function getProfiles(callback, errorcallback){
  axios.get(ASIN_VISUALS_PROFILE_URL)
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
  axios.get(ASIN_VISUALS_CAMPAIGNS_URL,{
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
                              (obj.name.length>23?
                                obj.name.substr(0,23)+"..."
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
      if(campaignOptions.length != 0){
        campaignOptions.unshift({label: "Select All",value:"All"})
      }
      callback(campaignOptions);
    }
  })
  .catch(err => {
    if(errorcallback != null){
       errorcallback(err);
    }
  })
}

export function getAsinsCall(params,callback, errorcallback){
  axios.get(ASIN_VISUALS_ASINS_URL,{
    params:{
      profileId: params.profileId,
      campaignId: params.campaignIds.toString()
    }
  })
  .then(res => {
    if(callback != null){
      let asinOptions = res.data.asins.map((obj, idx) => {
        return {
           label: obj.asin,
           value: obj.asin,
           key: idx
        }
      })
      callback(asinOptions);
    }
  })
  .catch(err => {
    if(errorcallback != null){
       errorcallback(err);
    }
  })
}

export function getScoreCardCall(params,callback, errorcallback){
  axios.get(ASIN_VISUALS_SCORECARD_URL,
    {
      params:{
        profileId: params.profileId,
        campaignId: params.campaignIds.toString(),
        startDate: params.startDate,
        endDate: params.endDate,
        ASIN: params.asin,
      }
    })
  .then(res => {
    if(callback != null){
      if(res.data.spCalculateAMSScoreCardsAsinLevel.length!=0){
        callback(res.data.spCalculateAMSScoreCardsAsinLevel);
      }else{
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
  axios.get(ASIN_VISUALS_PCHART_URL,
    {
      params:{
        profileId: params.profileId,
        campaignId: params.campaignIds.toString(),
        startDate: params.startDate,
        endDate: params.endDate,
        ASIN: params.asin,
      }
    })
  .then(res => {
    if(callback != null){
      if(res.data.spPopulateAsinPerformance.length!=0){
        let cost = res.data.spPopulateAsinPerformance.map(function(obj){
          return +obj.cost;
      });
      let rev = res.data.spPopulateAsinPerformance.map(function(obj){
          return +obj.revenue;
      });
      let acos = res.data.spPopulateAsinPerformance.map(function(obj){
          return +obj.acos;
      });
      let datekey = res.data.spPopulateAsinPerformance.map(function(obj){
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
    axios.get(ASIN_VISUALS_ECHART_URL,
      {
        params:{
          profileId: params.profileId,
          campaignId: params.campaignIds.toString(),
          startDate: params.startDate,
          endDate: params.endDate,
          ASIN: params.asin,
        }
      })
    .then(res => {
      if(callback != null){
        if(res.data.spPopulateAsinLevelEfficiency.length!=0){
          let roas = res.data.spPopulateAsinLevelEfficiency.map(function(obj){
            return +obj.roas;
        });
        let cpa = res.data.spPopulateAsinLevelEfficiency.map(function(obj){
            return +obj.cpa;
        });
        let cpc = res.data.spPopulateAsinLevelEfficiency.map(function(obj){
            return +obj.cpc;
        });
        let dateKeyE = res.data.spPopulateAsinLevelEfficiency.map(function(obj){
            return helperDateFunction(obj.date_key);
        });
		
        dateKeyE.unshift("x");
        roas.unshift("ROAS");
        cpa.unshift("CPA");
        cpc.unshift("CPC");
        callback([dateKeyE,roas,cpa,cpc]);
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
  axios.get(ASIN_VISUALS_ACHART_URL,
    {
      params:{
        profileId: params.profileId,
        campaignId: params.campaignIds.toString(),
        startDate: params.startDate,
        endDate: params.endDate,
        ASIN: params.asin,
      }
    })
  .then(res => {
    if(callback != null){
      if(res.data.spPopulateAMSAwarenessAsinLevel.length!=0){
        let impr = res.data.spPopulateAMSAwarenessAsinLevel.map(function(obj){
          return +obj.impressions;
      });
        let ctr = res.data.spPopulateAMSAwarenessAsinLevel.map(function(obj){
            return +obj.CTR;
        });
        let clicks = res.data.spPopulateAMSAwarenessAsinLevel.map(function(obj){
            return +obj.clicks;
        });
        let date_Akey = res.data.spPopulateAMSAwarenessAsinLevel.map(function(obj){
            return helperDateFunction(obj.date_key);
        });
        date_Akey.unshift("x");
        impr.unshift("Impressions");
        ctr.unshift("CTR");
        clicks.unshift("Clicks");

      callback([date_Akey,ctr,impr,clicks]);
      }
    }
  })
  .catch(err => {
    if(errorcallback != null){
       errorcallback(err);
    }
  })

}

export function getPerfPercentagesCall(params,callback, errorcallback){
  axios.get(ASIN_VISUALS_PERF_CARD_URL,
    {
      params:{
        profileId: params.profileId,
        campaignId: params.campaignIds.toString(),
        startDate: params.startDate,
        endDate: params.endDate,
        ASIN: params.asin,
      }
    })
  .then(res => {
    if(callback != null){
      let percentages = res.data.spCalculateAsinLevelPreformancePrecentages.map(item=>{
       return item;
      })
      let currencies = res.data.spCalculateAsinLevelPerformanceGrandTotal.map(item=>{
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
  axios.get(ASIN_VISUALS_EFFI_CARD_URL,
    {
      params:{
        profileId: params.profileId,
        campaignId: params.campaignIds.toString(),
        startDate: params.startDate,
        endDate: params.endDate,
        ASIN: params.asin,
      }
    })
  .then(res => {
    if(callback != null){
      let percentages = res.data.spCalculateAsinLevelEfficiencyPrecentages.map(item=>{
        return item;
       })
       let currencies = res.data.spCalculateAsinLevelEfficiencyGrandTotal.map(item=>{
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
  axios.get(ASIN_VISUALS_AWAR_CARD_URL,
    {
      params:{
        profileId: params.profileId,
        campaignId: params.campaignIds.toString(),
        startDate: params.startDate,
        endDate: params.endDate,
        ASIN: params.asin,
      }
    })
  .then(res => {
    if(callback != null){
      let percentages = res.data.spCalculateAMSAwarenessAsinLevelPercentage.map(item=>{
        return item;
       })
       let currencies = res.data.spPopulateAMSAwarenessAsinLevelGrandTotal.map(item=>{
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