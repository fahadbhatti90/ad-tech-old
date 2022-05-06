export const GET_ALL_EVENTS = baseUrl+"/client/events";
export function getEvents(cb, ecb){
    axios.get(
        GET_ALL_EVENTS
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
