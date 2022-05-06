import {UPDATE_PARENT_BRAND} from './../../../../config/AppConstants';


export const updateParentBrand = (data) => dispatch => {
    dispatch({
        type:UPDATE_PARENT_BRAND,
        payload: data
    })
}