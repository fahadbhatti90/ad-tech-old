
export const UN_ASSOCIATED_ACCOUNT = baseUrl+'/accounts/';

export function UnAssociateAccountApiCall(params, cb, ecb){
    axios.get(
        UN_ASSOCIATED_ACCOUNT+params.id+'/deleteAccount/'
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
