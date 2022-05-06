export const VENDOR_ADD_POST = window.baseUrl+"/ht/vendors";
export const FETCH_ALL_VENDOR = window.baseUrl+"/ht/getAllVendors";
export const UPLOAD_DAILY_SALES_FORM = window.baseUrl+"/ht/dailySales";
export const UPLOAD_DAILY_INVENTORY_FORM = window.baseUrl+"/ht/dailyInventory";
export const UPLOAD_FORECAST_FORM = window.baseUrl+"/ht/forecast";
export const UPLOAD_CATALOG_FORM = window.baseUrl+"/ht/catalog";
export const UPLOAD_PURCHASE_ORDER_FORM = window.baseUrl+"/ht/purchaseOrder";
export const UPLOAD_TRAFFIC_FORM = window.baseUrl+"/ht/traffic";
export const EXPORT_HISTORY_DATA = window.baseUrl+"/ht/history";
export const VERIFY_DELETE_DATA = window.baseUrl+"/ht/vc/delete";
export const VERIFY_DATA = window.baseUrl+"/ht/vc/verify";
export const VERIFY_MOVE_TO_MAIN_DATA = window.baseUrl+"/ht/vc/move";

export function vendorAddSubmission(params, callback, errorcallback) {
    axios.post(VENDOR_ADD_POST, params)
        .then((res) => {
            callback(res.data);
        }).catch((error) => {
        if (errorcallback != null) {
            errorcallback(error);
        }
    })
}

export function getAllVendors(callback, errorcallback) {
    axios.get(FETCH_ALL_VENDOR + '?_=' + new Date().getTime())
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

export function uploadDailySales(params, callback, errorcallback) {

    axios.post(UPLOAD_DAILY_SALES_FORM , params,{
    }).then((res) =>{
            callback(res.data);
        }).catch((error)=>{
        if (errorcallback != null) {
            errorcallback(error);
        }
    })
}

export function uploadDailyInventory(params, callback, errorcallback) {

    axios.post(UPLOAD_DAILY_INVENTORY_FORM , params,{
    }).then((res) =>{
        callback(res.data);
    }).catch((error)=>{
        if (errorcallback != null) {
            errorcallback(error);
        }
    })
}
export function uploadForecast(params, callback, errorcallback) {

    axios.post(UPLOAD_FORECAST_FORM , params,{
    }).then((res) =>{
        callback(res.data);
    }).catch((error)=>{
        if (errorcallback != null) {
            errorcallback(error);
        }
    })
}

export function uploadCatalog(params, callback, errorcallback) {

    axios.post(UPLOAD_CATALOG_FORM , params,{
    }).then((res) =>{
        callback(res.data);
    }).catch((error)=>{
        if (errorcallback != null) {
            errorcallback(error);
        }
    })
}

export function uploadPurchaseOrder(params, callback, errorcallback) {

    axios.post(UPLOAD_PURCHASE_ORDER_FORM , params,{
    }).then((res) =>{
        callback(res.data);
    }).catch((error)=>{
        if (errorcallback != null) {
            errorcallback(error);
        }
    })
}

export function uploadTraffic(params, callback, errorcallback) {

    axios.post(UPLOAD_TRAFFIC_FORM , params,{
    }).then((res) =>{
        callback(res.data);
    }).catch((error)=>{
        if (errorcallback != null) {
            errorcallback(error);
        }
    })
}
export function exportData(params, callback, errorcallback) {

    axios.post(EXPORT_HISTORY_DATA , params,{
    }).then((res) =>{
        callback(res.data);
    }).catch((error)=>{
        if (errorcallback != null) {
            errorcallback(error);
        }
    })
}

export function deleteData(params, callback, errorcallback) {

    axios.post(VERIFY_DELETE_DATA , params,{
    }).then((res) =>{
        callback(res.data);
    }).catch((error)=>{
        if (errorcallback != null) {
            errorcallback(error);
        }
    })
}
export function verifyData(params, callback, errorcallback) {

    axios.post(VERIFY_DATA , params,{
    }).then((res) =>{
        callback(res.data);
    }).catch((error)=>{
        if (errorcallback != null) {
            errorcallback(error);
        }
    })
}
export function moveToMainData(params, callback, errorcallback) {

    axios.post(VERIFY_MOVE_TO_MAIN_DATA , params,{
    }).then((res) =>{
        callback(res.data);
    }).catch((error)=>{
        if (errorcallback != null) {
            errorcallback(error);
        }
    })
}
