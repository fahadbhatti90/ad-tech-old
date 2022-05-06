export const GET_ALL_ACCOIATED_ACCOUNTS = baseUrl+"/accounts/getAccounts";
export function getAccounts(cb, ecb){
    axios.get(
        GET_ALL_ACCOIATED_ACCOUNTS
    ).then((response)=>{
        console.log(response);
        cb(response.data);
    }).catch((error)=>{
        ecb(error)
    });

}
