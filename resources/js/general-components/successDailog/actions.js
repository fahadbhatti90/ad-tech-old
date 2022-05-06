import {SUCCESS_MODAL_OPEN,SUCCESS_MODAL_CLOSE} from "../../config/AppConstants";

export const ShowSuccessMsg= (message,infoMsg,open,secondaryMessage,callback = null) => dispatch => {
    dispatch({
        type: SUCCESS_MODAL_OPEN,
        payload: {
            message,
            infoMsg,
            open,
            secondaryMessage,
            callback
        }
    });
};

export const closeSuccessMsg=()=>dispatch=>{
    dispatch({
        type: SUCCESS_MODAL_CLOSE
    });
}