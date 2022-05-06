import {ACTION_SET_HEADER,ACTION_SET_LOGIN_STATUS} from "./HeaderConstants";

export const SetPageHeader= (pageHeader = "") => dispatch => {
    dispatch({
        type: ACTION_SET_HEADER,
        payload: {
            pageHeader:pageHeader
        }
    });
};
export const SetLoginStatus= (status = false) => dispatch => {
    dispatch({
        type: ACTION_SET_LOGIN_STATUS,
        payload: {
            loginStatus:status
        }
    });
};
