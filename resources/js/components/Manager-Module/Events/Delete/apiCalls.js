
export const GET_ALL_EVENTS = baseUrl+"/client/events/delete/";

export function deleteEventLog(params,cb, ecb){
    axios.get(
        GET_ALL_EVENTS+params.id
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
