export const MANAGE_PARENT_BRANDS = baseUrl +  '/manager/brands/';
export const GET_MANAGERS_NAVIGATION_DATA = baseUrl +  '/client/getNavigationData/';
export const GET_SUPER_ADMIN_NAVIGATION_DATA = baseUrl +  '/superadmin/getNavigationData/';
export const GET_NAVIGATION_DATA = baseUrl +  '/getNavigationData/';

export function getNotificationUrl(switchingPortalTo){
    return  htk.portalActiveUserIsAdmin() ? 
    switchingPortalTo == 2 ? GET_NAVIGATION_DATA : GET_MANAGERS_NAVIGATION_DATA 
: htk.isManager() ? GET_MANAGERS_NAVIGATION_DATA : GET_SUPER_ADMIN_NAVIGATION_DATA;
}

export function getAllNavigationData(params, cb, ecb){
    let url = getNotificationUrl(params.switchingPortalTo)
    axios.get(url,
        {
            params:{
                userType : params.switchingPortalTo
            }
        }
        ).
        then((response) => {
            cb(response)
        })
        .catch((e) => {
            ecb(e)
        });
}//END
export function changeActiveParentBrand(params,cb,ecb){
    axios.get(MANAGE_PARENT_BRANDS+params.parentBrandId).
        then((response) => {
            if(response.data.status){
                cb(response.data)
            }
            else{
                ecb("Some thing went wrong");
            }
        })
        .catch((e) => {
            ecb(e)
        });
}//END