import {
    ACTION_RESET_REDUX_NOTIFICATION_STATE, 
    ACTION_ON_NEW_NOTIFICATION,
    ACTION_SET_NOTIFICATION_COUNT} from "./NotificationConstants";

export const alertNewNotification = (data = null) => dispatch => {
    dispatch({
        type: ACTION_ON_NEW_NOTIFICATION,
        payload: data
    });
};
export const resetReduxNotificaitonsState= (data = null) => dispatch => {
    dispatch({
        type: ACTION_RESET_REDUX_NOTIFICATION_STATE,
    });
};
export const SetNotificationCount= (notiCount = null) => dispatch => {
    dispatch({
        type: ACTION_SET_NOTIFICATION_COUNT,
        payload: {
            notiCount
        }
    });
};
