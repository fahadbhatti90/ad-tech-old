import {ACTION_SET_HEADER,ACTION_SET_LOGIN_STATUS} from "./HeaderConstants";

const initialState ={
    pageHeader:"",
    isUserLoggedIn:true,
}
export const PageHeaderReducer = (state = initialState, action)=> {
    switch (action.type) {
        case ACTION_SET_HEADER: {
            return { 
                ...state, 
                pageHeader: action.payload.pageHeader,
                
            };
        }
        case ACTION_SET_LOGIN_STATUS: {
            return { 
                ...state, 
                isUserLoggedIn: action.payload.loginStatus,
                
            };
        }
        default: {
            return {
                ...state
            };
        }
    }
}