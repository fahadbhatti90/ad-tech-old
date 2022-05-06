export const SC_EXPORT_DATA = window.baseUrl+"/mws/checkScHistory";
export const SC_GET_API_DATA = window.baseUrl+"/mws/apiconfig";
export const SC_ADD_API_DATA = window.baseUrl+"/mws/addConfig";
export const SC_EDIT_API_DATA = window.baseUrl+"/mws/editConfig";
export const SC_DELETE_API_DATA = window.baseUrl+"/mws/deleteApiConfig";

export function scExportData(params, callback, errorcallback) {
    axios.post(SC_EXPORT_DATA, params)
        .then((res) => {
            callback(res.data);
        }).catch((error) => {
        if (errorcallback != null) {
            errorcallback(error);
        }
    })
}
export function addScApiConfig(params, callback, errorcallback) {
    axios.post(SC_ADD_API_DATA, params)
        .then((res) => {
            callback(res.data);
        }).catch((error) => {
        if (errorcallback != null) {
            errorcallback(error);
        }
    })
}

export function editScApiConfig(params, callback, errorcallback) {
    axios.post(SC_EDIT_API_DATA, params)
        .then((res) => {
            callback(res.data);
        }).catch((error) => {
        if (errorcallback != null) {
            errorcallback(error);
        }
    })
}

export function getAllApiConfigData(callback, errorcallback){
    axios.get(SC_GET_API_DATA + '?_=' + new Date().getTime())
        .then(res => {
            if (callback != null) {
                callback(res.data);
            }
        })
        .catch(err => {
            if (errorcallback != null) {
                errorcallback(err);
            }
        })
}

export function deleteApiConfig(params, callback, errorcallback) {
    axios.post(SC_DELETE_API_DATA, {"id": params})
        .then((res) => {
            callback(res.data);
        }).catch((error) => {
        if (errorcallback != null) {
            errorcallback(error);
        }
    })
}