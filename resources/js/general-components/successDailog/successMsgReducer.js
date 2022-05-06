import {SUCCESS_MODAL_OPEN,SUCCESS_MODAL_CLOSE} from "./../../config/AppConstants";

const initialState ={
    UISettings: {
        showSuccessMsg: 0,
        message: "",
        infoMsg:"",
        secondaryMessage:null,
        open:false,
    }

}
export const showSuccessMsgReducer = (state = initialState, action)=> {
    switch (action.type) {
        case SUCCESS_MODAL_OPEN: {
            return { ...state, 
                UISettings: {
                    showSuccessMsg: state.UISettings.showSuccessMsg + 1,
                    message: action.payload.message,
                    open: action.payload.open,
                    secondaryMessage: action.payload.secondaryMessage,
                    infoMsg: action.payload.infoMsg,
                    callback: action.payload.callback
                }
            };
        }
        case SUCCESS_MODAL_CLOSE:{
            return {
                ...state,
                    ...initialState
            }
        }
        default: {
            return {
                ...state
            };
        }
    }
}