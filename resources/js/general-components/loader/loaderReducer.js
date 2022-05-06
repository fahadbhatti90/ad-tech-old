import { SHOW_LOADER_IS } from "./initialState";
import {SHOW_LOADER,HIDE_LOADER} from "../../config/AppConstants"


export const loaderReducer = (state = SHOW_LOADER_IS, action)=> {
    switch (action.type) {
        case SHOW_LOADER:{
            return {
                ...state,
                UISettings: {
                    showLoader: {
                        open: action.payload
                    }
                }
            }
        }
        case HIDE_LOADER: {
            return {
                ...state,
                UISettings: {
                    showLoader: {
                        open: action.payload
                    }
                }
            }
        }
        default: {
            return {
                ...state
            };
        }
    }
}