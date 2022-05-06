import { UPDATE_PARENT_BRAND } from "./../../../../config/AppConstants";

const initialState = {
    parentId : {},
}
export const parentBrandReducer = (state=initialState,action) =>{
    switch (action.type) {
        case UPDATE_PARENT_BRAND:
            return {
                ...state,
                parentId:action.payload,
            }
    
        default:
            return {...state};
    }
} 