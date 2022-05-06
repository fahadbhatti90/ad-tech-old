
export const GET_ALL_COLLECTIONS = baseUrl+"/asin/uploadASIN";
export const ADD_COLLECTION = baseUrl+"/asin/upload";

export function getAllCollections(cb, ecb){
    axios.get(
        GET_ALL_COLLECTIONS
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
export function addAsinCollection(params, cb, ecb){
    axios.post(
        ADD_COLLECTION,
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