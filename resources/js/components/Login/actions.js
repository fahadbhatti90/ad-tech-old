import {IS_LOGGED_IN,IS_LOGGED_OUT} from "./../../config/AppConstants";


export const loggedIn = () => dispatch => {
    dispatch({
        type: IS_LOGGED_IN,
        payload: "logged"
    });
}
export const logout = () => dispatch => {
    dispatch({
        type: IS_LOGGED_OUT,
        payload: true
    });
}