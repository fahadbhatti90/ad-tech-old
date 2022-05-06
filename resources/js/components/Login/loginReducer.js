import {IS_LOGGED_IN,IS_LOGGED_OUT} from "./../../config/AppConstants";
import {IS_LOGGED_IN_STATUS,USER} from "./../../config/localStorageKeys";

const initialState = {
    isLoggedIn: false,
    isLoggedOut:true,
};

export const loginReducer = (state = initialState, action)=> {
    switch (action.type) {
        case IS_LOGGED_IN:{
            return {
                ...state,
                isLoggedIn: action.payload,
                isLoggedOut:!action.payload,
            }
        }
        case IS_LOGGED_OUT:{
            
            localStorage.removeItem(IS_LOGGED_IN_STATUS);
            localStorage.removeItem(USER);
            return {
                ...state,
                isLoggedIn: !action.payload,
                isLoggedOut: action.payload
            }
        }
        default: {
            return {
                ...state
            };
        }
    }
}