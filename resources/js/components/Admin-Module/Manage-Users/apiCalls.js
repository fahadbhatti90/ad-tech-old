export const MANAGE_USERS_DATATABLE_URL = window.baseUrl+"/ht/managers";
export const ADD_MANAGER_URL = window.baseUrl+"/ht/managerOperations";
export const DELETE_MANAGER_URL = window.baseUrl +"/ht/";
export const GET_USERS_URL = window.baseUrl + "/ht/getUsersByType";
export const CHECK_USER_BRANDS_URL = window.baseUrl + "/ht/checkUserBrands";
export const GET_EDIT_MANAGER_URL = window.baseUrl + "/ht/getEditManagerData";
export const REASSIGN_BRAND_MANAGER_URL = window.baseUrl + "/ht/addBrandManagers";

export function getUsersData(callback, errorcallback){
    axios.get(MANAGE_USERS_DATATABLE_URL)
    .then(res => {
      if(callback != null){
        let data = res.data.clients;
        let brands = res.data.brands;
        brands = brands.map((obj) => {
          return {
             label: obj.email,
             value: obj.id
          }
        })
        for(let i=0;i<data.length;++i){
            data[i]["serial"] = i+1;
        }
        callback(data,brands);
      }
    })
    .catch(err => {
      if(errorcallback != null){
          errorcallback(err);
      }
    })
}

export function addManagerCall(params,callback, errorcallback){
  axios.post(ADD_MANAGER_URL,params)
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

export function getUsersBasedOnType(type,callback, errorcallback){
  axios.post(GET_USERS_URL,type)
  .then(res => {
    if(callback != null){
      let data = res.data.usersArray;
      let arrOptions = []
      for (const [key, value] of Object.entries(data)) {
        arrOptions.push(
          {   label: value,
              value: key});
      }
      callback(arrOptions);
    }
  })
  .catch(err => {
    if(errorcallback != null){
        errorcallback(err);
    }
  })
}

export function deleteManagerCall(managerId,callback, errorcallback){
  axios.delete(`${DELETE_MANAGER_URL}${managerId}/deleteManager`)
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

export function checkUserBrands(Id,callback, errorcallback){
  axios.post(CHECK_USER_BRANDS_URL,
    {userId:Id})
    .then(res => {
      if(callback != null){
        let status = res.data.status;
        let arrOptions = []
        if(res.data.notAssignedBrandsNames){
          for (const [key, value] of Object.entries(res.data.notAssignedBrandsNames)) {
            arrOptions.push(
              {   label: value,
                  value: key});
          }
        }
        callback(status,arrOptions);
      }
    })
    .catch(err => {
      if(errorcallback != null){
          errorcallback(err);
      }
    })
}

export function reassignBrandandDelete(params,callback, errorcallback){
  axios.post(REASSIGN_BRAND_MANAGER_URL,
    params)
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

export function disableBrandsCall(Id,callback, errorcallback){
  axios.post(GET_EDIT_MANAGER_URL,
    {userId: Id})
    .then(res => {
      if(callback != null){
        let brands = res.data.brands;
        brands = brands.map((obj) => {
          return {
             label: obj.metricName,
             value: obj.id,
             isDisabled: (obj.isChecked === 'true')
          }
        })
        callback(brands);
      }
    })
    .catch(err => {
      if(errorcallback != null){
          errorcallback(err);
      }
    })
}