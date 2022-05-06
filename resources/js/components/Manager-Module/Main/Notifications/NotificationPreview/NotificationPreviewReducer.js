import { UPDATE_NOTIFICATION_PREVIEW } from "./../../../../../config/AppConstants";

const initialState = {
    notiId : 0
}
export const notificationIdReducer = (state=initialState,action) =>{
    switch (action.type) {
        case UPDATE_NOTIFICATION_PREVIEW:
            return {
                ...state,
                notiId:action.payload
            }
    
        default:
            return {...state};
    }
} 