export const GET_API_CONFIG_URL = window.baseUrl+"/ams-apiconfig";
export const ADD_APICONFIG_URL = window.baseUrl+"/ams-addConfig";
export const EDIT_APICONFIG_URL = window.baseUrl+"/ams-editConfig";
export const DELETE_APICONFIG_URL = window.baseUrl+"/ams-deleteConfig";
export const CHECK_HISTORY_URL = window.baseUrl+"/ams-checkHistory";

export function getApiConfig(callback, errorcallback){
    axios.get(GET_API_CONFIG_URL)
    .then(res => {
      if(callback != null){
        let data = res.data.api_parameter;
        callback(data);
      }
    })
    .catch(err => {
      if(errorcallback != null){
          errorcallback(err);
      }
    })
}

export function addApiCall(params, callback, validationCallBack, errorcallback){
    axios.post(params.isEditing ? EDIT_APICONFIG_URL : ADD_APICONFIG_URL,params)
      .then((res)=>res.data)
      .then(res => {
        if(res.status)
          callback(res.message);
        else if(res.validationStatus)
          validationCallBack(res.errors);
        else 
          errorcallback(res.message);
      })
      .catch(error => {
        if(error.response)
          errorcallback(error.response.data.message)
        else{
            console.log(error)
            errorcallback("Some thing went worng in front end app, See Console")
        }
      })
  }

  export function deleteApiConfig(params, callback, errorcallback) {
    axios.post(DELETE_APICONFIG_URL, {"id": params})
        .then((res)=>res.data)
        .then((res) => {
            if(res.status)
              callback(res.message);
            else
              errorcallback(res.message);
            
        }).catch((error) => {
          if(error.response)
          errorcallback(error.response.data.message)
          else{
              console.log(error)
              ecb("Some thing went worng in front end app, See Console")
          }
        })
  }
  export function checkHistory(params,callback, errorcallback){
    axios.post(CHECK_HISTORY_URL,params)
      .then(res => {
        if(callback != null){
          let data = res.data;
          callback(data);
        }
      })
      .catch(err => {
        if(errorcallback != null){
            errorcallback(err);
        }
      })
  }
  