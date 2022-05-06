import axios from 'axios';
import Tooltip from "@material-ui/core/Tooltip";
import React from 'react';
import {helperDateFunction, profilesMappingBhatti} from "./../../../../../helper/helper";

const profileUrl = window.baseUrl + "/bidding-rule/bidding-profile";
const presetRuleUrl = window.baseUrl + "/bidding-rule/preset-rule";
const SAVE_PRESET_RULE = window.baseUrl + "/bidding-rule/only-store-rules";
const storeRuleUrl = window.baseUrl + "/bidding-rule/only-store-rules";
const storeBiddingRuleUrl = window.baseUrl + "/bidding-rule/store-rules";
const portfolioCampaignUrl = window.baseUrl + "/bidding-rule/campaign-portfolio-list";
const presetUrl = window.baseUrl + "/bidding-rule/preset-rule-list";

function presetRulesMaping(presetRules){
    return presetRules.map((obj, idx) => {
        return {
            label: (<Tooltip placement="top" title={obj.presetName} arrow>
              <span>{
                  (obj.presetName.length > 23 ?
                      obj.presetName.substr(0, 23) + "..." : obj.presetName)
              }
              </span>
                </Tooltip>
            ),
            value: obj.id,
            key: idx
        }
    })
}

/**
 * This function is used to get All Profile
 * @param callback
 * @param errorcallback
 */
export function getProfiles(params,cb, errorcallback) {
    axios.get(profileUrl+"/"+params.id)
        .then(res => {
            if (cb != null) {
                let profileOptions = profilesMappingBhatti(res.data.profiles);
                let presetOptions = presetRulesMaping(res.data.presetRules);
                let SelectedBidRule = res.data.SelectedBidRule;
                let pfCampaigns = getPfCampaingOptions(res.data.pfCampaings);
                cb({profileOptions, presetOptions, SelectedBidRule, pfCampaigns});
            }
        })
        .catch(err => {
            if (errorcallback != null) {
                errorcallback(err);
            }
        })
}

/**
 * This function is used to get Campaign/Portfolio List
 * @param profileId
 * @param adType
 * @param portfolioCampaign
 * @param callback
 * @param errorcallback
 */
export function getCampaignsPortfolioCall(profileId, adType, portfolioCampaign, callback, errorcallback) {
    axios.get(portfolioCampaignUrl, {
        params: {
            profile_fk_id: profileId,
            sponsored_type: adType,
            portfolio_campaign_type: portfolioCampaign,
        }
    }).then(res => {
        if (callback != null) {
            callback(getPfCampaingOptions(res.data.data));
        }
    }).catch(err => {
        if (errorcallback != null) {
            errorcallback(err);
        }
    })
}
function getPfCampaingOptions(data){
    return data.map((obj, idx) => {
        return {
            label: (<Tooltip placement="top" title={obj.name} arrow>
              <span>{
                  (obj.name.length > 50 ?
                      obj.name.substr(0, 50) + "..."
                      :
                      obj.name)
              }
              </span>
                </Tooltip>
            ),
            value: obj.id,
            key: idx
        }
    })
}
/**
 * This function is used to get preset rules list
 * @param callback
 * @param errorcallback
 */
export function getPreset(callback, errorcallback) {
    axios.post(presetUrl).then(res => {
        if (callback != null) {
            
            callback(presetOptions);
        }
    }).catch(err => {
        if (errorcallback != null) {
            errorcallback(err.responseText);
        }
    })
}

/**
 * This function is used to store bidding rule data
 * @param params
 * @param errorcallback
 */
export function addBiddingData(params,callBack, errorcallback) {
    axios.post(storeBiddingRuleUrl, params).then((response) => {
        if(response.data.status){
            callBack(response.data)
        }
        else{
            errorcallback(response.data.message)
        }
    }).catch((error) => {
        errorcallback(error.message)
    })
}

/**
 * This function is used to store only Rule
 * @param callback
 * @param errorcallback
 */
export function addBiddingRule(callback, errorcallback) {
    axios.post(storeRuleUrl).then(res => {
        if (callback != null) {
            let presetOptions = res.data.map((obj, idx) => {
                return {

                    label: (<Tooltip placement="top" title={obj.presetName} arrow>
                      <span>{
                          (obj.presetName.length > 23 ?
                              obj.presetName.substr(0, 23) + "..." : obj.presetName)
                      }
                      </span>
                        </Tooltip>
                    ),
                    value: obj.id,
                    key: idx
                }
            })
            callback(presetOptions);
        }
    }).catch(err => {
        if (errorcallback != null) {
            errorcallback(err);
        }
    })
}
/**
 * This function is used to store only Rule
 * @param callback
 * @param errorcallback
 */
 export function savePresetRule(params,callback, errorcallback) {
    axios.post(SAVE_PRESET_RULE,params).then(res => {
        if (res.data.status) {
            callback(res.data.message);
        }
        else{
            errorcallback(res.data.message);
        }
    }).catch(err => {
        if (errorcallback != null) {
            errorcallback(err);
        }
    })
}

/**
 * This function is used to get Specific Preset Rule
 * @param callback
 * @param errorcallback
 */
export function getPesetRuleValue(params, callback, errorcallback) {
    axios.get(presetRuleUrl, {
        params: {
            id:params.value
        }
    })
        .then(res => {
            if(res.data.status){
                callback(res.data.data[0]);
            }
            else{
                errorcallback(err);    
            }
        })
        .catch(err => {
            errorcallback(err);
        })
}