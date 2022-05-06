export const ADMIN_PREVIEW_NOTIFICATION = baseUrl+"/notifications/";
export const SUPER_ADMIN_PREVIEW_NOTIFICATION = baseUrl+"/superadmin/notifications/";
export const CLIENT_PREVIEW_NOTIFICATION = baseUrl+"/client/notifications/";
//1/preview
export function getNotificationUrl(){
    return  htk.isManager() ?  CLIENT_PREVIEW_NOTIFICATION : (htk.isAdmin() ? ADMIN_PREVIEW_NOTIFICATION : SUPER_ADMIN_PREVIEW_NOTIFICATION);
}

export function getNotificationData(params, cb, ecb){
    let NotificaitonStart = getNotificationUrl();
    axios.get(
        NotificaitonStart+params.notiId+"/preview",
    ).then((response)=>{
        cb(response)
    }).catch((error)=>{
        ecb(error)
    });

}