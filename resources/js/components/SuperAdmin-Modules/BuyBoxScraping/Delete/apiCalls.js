
export const DELETE_SCHEDULE = baseUrl+'/buybox/deletebatch/';

export function DeleteScheduleApiCall(params, cb, ecb){
    axios.delete(
        DELETE_SCHEDULE+params.id
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
