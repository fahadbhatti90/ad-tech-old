import {FAILURE_MODAL_OPEN,FAILURE_MODAL_CLOSE} from "../../config/AppConstants";

const initialState ={
    UISettings: {
        showFailureMsg: 0,
        message: "",
        infoMsg:"",
        htmlList:null,
        secondaryMessage:null,
        open:false
    }

}
export const showFailureMsgReducer = (state = initialState, action)=> {
    switch (action.type) {
        case FAILURE_MODAL_OPEN: {
            return { ...state, 
                UISettings: {
                    showFailureMsg: state.UISettings.showFailureMsg + 1,
                    message: action.payload.message,
                    open: action.payload.open,
                    htmlList: action.payload.htmlList,
                    secondaryMessage: action.payload.secondaryMessage,
                    infoMsg: action.payload.infoMsg,
                    callback: action.payload.callback
                }
            };
        }
        case FAILURE_MODAL_CLOSE:{
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