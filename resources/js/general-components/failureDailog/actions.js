import {FAILURE_MODAL_OPEN,FAILURE_MODAL_CLOSE} from "../../config/AppConstants";

export const ShowFailureMsg= (message,infoMsg,open,secondaryMessage,htmlList,callback = null) => dispatch => {
    dispatch({
        type: FAILURE_MODAL_OPEN,
        payload: {
            message,
            infoMsg,
            open,
            htmlList,
            secondaryMessage,
            callback
        }
    });
};

export const closeFailureMsg=()=>dispatch=>{
    dispatch({
        type: FAILURE_MODAL_CLOSE
    });
}