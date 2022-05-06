

import React from 'react';
import Tooltip from "@material-ui/core/Tooltip";

export const GET_ALL_INVENTORY = baseUrl+"/admin/labelOverride/data";
export const ADD_ALIAS = baseUrl+"/admin/labelOverride/addAlias";
export const UPLOAD_ALIAS_FILE = baseUrl+"/admin/labelOverride/uploadAlias";

export function getInventoryData(cb, ecb){
    axios.get(
        GET_ALL_INVENTORY,
    {
        params:{
            columsCustom:"all"
        }
    }
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

}//end function

export function getSingleInventoryData(params,cb, ecb){
    axios.get(
        GET_EVENT_LOG,
    {
        params:{
            columsCustom:params.columsCustom
        }
    }
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

}//end function
function manageProfile(obj) {
    return obj.product_alias.length > 0 && obj.product_alias[0].overrideLabel ?  obj.product_alias[0].overrideLabel : obj.attr2;
}


export function addOverrideLabel(params, cb, ecb){
    axios.post(
        ADD_ALIAS,
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

}//end function
export function uploadAliasFile(params, cb, ecb){
    axios.post(
        UPLOAD_ALIAS_FILE,
        params
    ).then((response)=>{
        if(response.data.status){
            cb(response.data);
        }else{
            ecb(response.data.message);    
        }

    }).catch((error)=>{
        console.log(error)
        if(error.response)
        ecb(error.response.data.message)
        else{
            console.log(error)
            ecb("Some thing went worng in front end app, See Console")
        }
    });

}//end function