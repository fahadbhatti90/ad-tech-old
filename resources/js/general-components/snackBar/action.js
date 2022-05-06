
import {SHOW_SNACK_BAR} from "../../config/AppConstants";

export const showSnackBar= (message, variant, callback = null) => dispatch => {
    dispatch({
        type: SHOW_SNACK_BAR,
        payload: {
            message,
            variant,
            callback
        }
    });
}