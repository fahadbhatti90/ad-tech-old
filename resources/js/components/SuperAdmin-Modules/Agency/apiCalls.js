import {ADD_MANAGER_URL} from "../../Admin-Module/Manage-Users/apiCalls";

export const FETCH_AGENCY_DATA_URL = window.baseUrl + "/ht/admins";
export const ADD_UPDATE_AGENCY_URL = window.baseUrl + "/ht/adminOperations";

export function getAgenciesApiData(callback, errorcallback) {
    axios.get(FETCH_AGENCY_DATA_URL)
        .then(res => {
            if (callback != null) {
                let data = res.data.agencies;
                for (let i = 0; i < data.length; ++i) {
                    data[i]["serial"] = i + 1;
                }
                callback(data);
            }
        })
        .catch(err => {
            if (errorcallback != null) {
                errorcallback(err);
            }
        })
}

export function addUpdateAgency(params, callback, errorcallback) {
    axios.post(ADD_UPDATE_AGENCY_URL, params)
        .then(res => {
            if (callback != null) {
                let data = res.data;
                callback(data);
            }
        })
        .catch(err => {
            if (errorcallback != null) {
                errorcallback(err);
            }
        })
}