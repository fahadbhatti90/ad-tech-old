

import React from 'react';
import Tooltip from "@material-ui/core/Tooltip";
export const GET_ALL_EVENTS_DATA = baseUrl+"/client/eventsData";
export const GET_ACCOUNT_ASINS = baseUrl+"/client/getRequiredData";
export const GET_EVENT_LOG = baseUrl+"/client/events/logs/";
export const ADD_EVENT_LOGS = baseUrl+"/client/addEventLogs";

export function getEventLog(params,cb, ecb){
    axios.get(
        GET_EVENT_LOG+params.id
    ).then((response)=>{
        if(response.data.status){
            cb(response.data);
        }else{
            ecb(response.data.message);    
        }

    }).catch((error)=>{
        if(error.response)
        ecb(error.response.data.message)
        else{
            console.log(error)
            ecb("Some thing went worng in front end app, See Console")
        }
    });

}
export function getEventsData(cb, ecb){
    axios.get(
        GET_ALL_EVENTS_DATA
    ).then((response)=>{
        cb(response.data);
    }).catch((error)=>{
        ecb(error)
    });

}
export function addEventLogs(params,cb, ecb){
    axios.post(
        ADD_EVENT_LOGS,
            params
    ).then((response)=>{
        if(response.data.status){
            cb(response.data);
        }else{
            ecb(response.data.message);    
        }
    }).catch((error)=>{
        if(error.response)
        ecb(error.response.data.message)
        else{
            console.log(error)
            ecb("Some thing went worng in front end app, See Console")
        }
    });

}

export function getAccountAsins(params,cb, ecb){
    axios.get(
        GET_ACCOUNT_ASINS,{
            params: {
                accountId:params.accountId
            }
        }
    ).then((response)=>{
        let asinOptions = getProfileAsins(response.data.result);
        cb(asinOptions);
    }).catch((error)=>{
        ecb(error)
    });
}

function manageProfile(obj) {
    return obj.product_alias.length > 0 && obj.product_alias[0].overrideLabel ?  obj.product_alias[0].overrideLabel : obj.attr2;
}
export function getProfileAsins(data){
    return data.map((obj, idx) => {
        let productTitle = manageProfile(obj);
        return {
            label: "("+obj.attr1+") "+productTitle,
            orignalLable:productTitle,
            value: obj.attr1,
            key: idx
        }
    })
}
