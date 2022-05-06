import {UPDATE_NOTIFICATION_PREVIEW} from './../../../../../config/AppConstants';


export const updateNotificationId = (notiId) => dispatch => {
    dispatch({
        type:UPDATE_NOTIFICATION_PREVIEW,
        payload:notiId
    });
}