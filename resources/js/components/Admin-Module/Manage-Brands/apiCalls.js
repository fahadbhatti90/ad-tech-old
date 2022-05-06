export const MANAGE_BRANDS_DATATABLE_URL = window.baseUrl+"/ht/brands";
export const GET_ASSOCIATED_USERS_URL = window.baseUrl+"/ht/brandsAddPopupData";
export const ADD_BRAND_URL = window.baseUrl +"/ht/manageClient";
export const DELETE_BRAND_URL = window.baseUrl +"/ht/";
export const EDIT_USERS_URL = window.baseUrl + "/ht/brandsEditPopupData";
export function getBrandsData(callback, errorcallback){
    axios.get(MANAGE_BRANDS_DATATABLE_URL)
    .then(res => {
      if(callback != null){
        let data = res.data.brands;
        for(let i=0;i<data.length;++i){
            data[i]["serial"] = i+1;
        }
        callback(data);
      }
    })
    .catch(err => {
      if(errorcallback != null){
          errorcallback(err);
      }
    })
}

export function getAssociatedUsers(callback, errorcallback){
  axios.get(GET_ASSOCIATED_USERS_URL)
    .then(res => {
      if(callback != null){
        let users = res.data.users;
          users = users.map((obj) => {
            return {
               label: obj.email,
               value: obj.id
            }
          })
        callback(users);
      }
    })
    .catch(err => {
      if(errorcallback != null){
          errorcallback(err);
      }
    })
}

export function addBrandCall(params,callback, errorcallback){
  axios.post(ADD_BRAND_URL,params)
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

export function deleteBrandCall(brandId,callback, errorcallback){
  axios.delete(`${DELETE_BRAND_URL}${brandId}/deleteClient`)
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

export function filterSelectedUsers(Id,callback, errorcallback){
  axios.post(EDIT_USERS_URL,
    {brandId: Id})
    .then(res => {
      if(callback != null){
        let users = res.data.users;
        let selectedUsers = [];
        users.forEach(obj =>{
              if(obj.isChecked == "true"){
                selectedUsers.push({label: obj.userEmail,
                  value: obj.id})
              }
              
          });
        callback(selectedUsers);
      }
    })
    .catch(err => {
      if(errorcallback != null){
          errorcallback(err);
      }
    })
}