

import React from 'react';
import Tooltip from "@material-ui/core/Tooltip";
export const GET_ALL_SCHEDULE = baseUrl+"/asin/scheduling";
export const ADD_SCHEDULE = baseUrl+"/asin/addScheduling";

export function getAllSchedule(cb, ecb){
    axios.get(
        GET_ALL_SCHEDULE
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
export function addAsinSchedule(params, cb, ecb){
    axios.post(
        ADD_SCHEDULE,
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