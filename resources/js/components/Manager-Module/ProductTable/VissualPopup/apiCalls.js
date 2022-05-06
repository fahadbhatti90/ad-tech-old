export const GET_ALL_EVENTS = baseUrl+"/getEvents";
export function getVissualPopupData(params, cb, ecb){
    axios.get(
        GET_ALL_EVENTS,
        {
            params
        }
    ).then((response)=>{
        cb(response.data);
    }).catch((error)=>{
        if(error.response)
        ecb(error.response.data.message)
        else{
            console.log(error)
            ecb("Some thing went worng in front end app, See Console")
        }
    });

}
