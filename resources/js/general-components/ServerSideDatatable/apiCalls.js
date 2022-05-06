

import React from 'react';
import Tooltip from "@material-ui/core/Tooltip";

export const GET_ALL_INVENTORY = baseUrl+"/admin/labelOverride/data";

export function getTableData(url, params, cb, ecb){
    axios.get(
        url,
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

}//end function