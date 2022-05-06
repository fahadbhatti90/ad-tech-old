import {ACTION_SET_IS_ADMIN} from "./sideBarConstants";

export const SetIsAdmin= (status = false) => dispatch => {
    dispatch({
        type: ACTION_SET_IS_ADMIN,
        payload: {
            status
        }
    });
};
