import {SHOW_SNACK_BAR} from "./../../config/AppConstants";

const initialState ={
    snakBarCount: 0,
	snakBarContent: {
        message:"Ok",
        variant:"info"
    },
}
export const snackBarReducer = (state = initialState, action)=> {
    switch (action.type) {
        case SHOW_SNACK_BAR: {
            return Object.assign({}, state, {
                snakBarCount: state.snakBarCount + 1,
                snakBarContent: action.payload
            });
        }
        default: {
            return {
                ...state
            };
        }
    }
}