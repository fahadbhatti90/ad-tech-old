

import React from 'react';
export const ASSOCIATE_ACCOUNTS = baseUrl+"/accounts/manageaccount";

export function associateAccounts(params,cb, ecb){
    axios.post(
        ASSOCIATE_ACCOUNTS,
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
