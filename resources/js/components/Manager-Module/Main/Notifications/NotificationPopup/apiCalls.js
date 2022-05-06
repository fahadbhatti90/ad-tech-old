
export const CLIENT_MARK_ALL_AS_READ = baseUrl+"/client/notifications/readAll";
export const ADMIN_MARK_ALL_AS_READ = baseUrl+"/notifications/readAll";
export const SUPER_ADMIN_MARK_ALL_AS_READ = baseUrl+"/superadmin/notifications/readAll";

export function getAllNotifications(params,cb, ecb){
    axios.get(
        params.url,
        {
            params:{
                userType : params.activeRole
            }
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
export function markAllAsRead(params,cb, ecb){//ids
    axios.post(
        htk.isManager() ? CLIENT_MARK_ALL_AS_READ : htk.isAdmin() ? ADMIN_MARK_ALL_AS_READ : SUPER_ADMIN_MARK_ALL_AS_READ,
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
}