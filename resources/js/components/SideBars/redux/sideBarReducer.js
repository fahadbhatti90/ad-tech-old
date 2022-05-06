import {ACTION_SET_IS_ADMIN} from "./sideBarConstants";

const initialState ={
    isAdmin:false,
}
export const SideBarReducer = (state = initialState, action)=> {
    switch (action.type) {
        case ACTION_SET_IS_ADMIN: {
            return { 
                ...state, 
                isAdmin: !state.isAdmin,
            };
        }
        default: {
            return {
                ...state
            };
        }
    }
}