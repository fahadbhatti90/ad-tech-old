export const LOGOUT = baseUrl+"/logout/";
//1/preview


export function logoutFromBackend(history, cb, ecb){
    // let url = params.type
    axios.get(
        LOGOUT,
    ).then((response)=>{
        cb();
        localStorage.removeItem(htk.constants.LOG_IN_STATUS);
        history.replace("/login");
    }).catch((error)=>{
        ecb(error)
    });

}