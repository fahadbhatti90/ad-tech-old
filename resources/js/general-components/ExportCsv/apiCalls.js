

import React from 'react';

export function getDownloadLink( url, params, cb, ecb){
    axios.get(
        baseUrl+url,
        {
            params:params
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

}