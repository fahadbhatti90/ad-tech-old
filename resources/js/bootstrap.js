import store from './store/configureStore';
import {SetLoginStatus} from './general-components/HeaderRedux/actions';

window._ = require('lodash');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */
try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');
    window.baseUrl = $("body").attr("baseUrl");
    window.assetUrl = $("body").attr("assetUrl");
    jQuery.fn.visible = function() {
        return this.css('visibility', 'visible');
    };
    jQuery.fn.invisible = function() {
        return this.css('visibility', 'hidden');
    };
    jQuery.fn.visibilityToggle = function() {
        return this.css('visibility', function(i, visibility) {
            return (visibility == 'visible') ? 'hidden' : 'visible';
        });
    };
    //Removes item from an arrray on the Bases of value
    Array.prototype.remove = function(v) {
        this.splice(this.indexOf(v) == -1 ? this.length : this.indexOf(v), 1);
    };
    //Removes item from an arrray on the Bases of Key
    Array.prototype.removeOnKey = function(v) {
      this.splice(v, 1);
    };
    Object.size = function(obj) {
        var size = 0, key;
        for (key in obj) {
            if (obj.hasOwnProperty(key)) size++;
        }
        return size;
    };

    window.htk= window.htk?window.htk:{};
    // window.htk={};
    htk.constants={
        IS_ADMIN: "isAdmin",
        AD_FILTERS: "adFilters",
        ASIN_FILTERS: "asinFilters",
        ACTIVE_ROLE:"activeRole",
        LOGGED_IN_USER:"user",
        LOG_IN_STATUS : "isLoggedIn",
        PRIMARY_COLOR : "#571986",
        PRIMARY_COLOR_LIGHTLight : "#9935c3",
        PRIMARY_COLOR_ORANGE : "#e1a23b",
        PRIMARY_COLOR_LIGHTER : "#D790F5",
        SECONDARY_COLOR : "#f96332",
        SHADE_GRAY_COLOR : "#2c2c2c",
        GRAY_COLOR : "#666666",
        GRAY_COLOR_LIGHT : "#979797",
        MD_GRAY_COLOR : "#eaeaea",
        SUCCESS : "#8cc34b",
        FAILURE : "#f68484",
        BACKGROUND : "#f5f6f7",
        BACKGROUND_GRADIENT : "linear-gradient(180deg, #571986 10%, #571986 100%)"
    }
    htk.host = $("body").attr("host");
    htk.validateAllFields = function(schema,dataObject) {
        var errors = {};
        $.each(dataObject, function (key, valueOfElement) { 
            try {
              schema.validateSync({[key]:valueOfElement});
            } catch (e) {
              errors[key] = e.type=="typeError" ?  key+" must be a valid type (type error)": e.errors[0];
            }
        });
        return errors;
    }//end function

    htk.getLocalStorageObjectDataById = function(key){
        let data = JSON.parse(localStorage.getItem(key));
        if (data) {
            return (data);
        } else{
            return null;
        }
    }
    htk.getLocalStorageDataById = function(key){
        let data = (localStorage.getItem(key));
        if (data) {
            return (data);
        } else{
            return null;
        }
    }
    htk.portalActiveUserIsAdmin = function() {
        return htk.getLocalStorageObjectDataById(htk.constants.IS_ADMIN);
    }
    htk.isUserLoggedIn = function(){
        return htk.getLocalStorageDataById(htk.constants.LOG_IN_STATUS) ? true : false;
    }
    htk.activeRole = htk.getLocalStorageDataById(htk.constants.ACTIVE_ROLE);
    htk.isManager =  function(){
        let data = htk.getLocalStorageDataById(htk.constants.ACTIVE_ROLE);
        return data && data == 3;
    }
    htk.isAdmin =  function(){
        let data = htk.getLocalStorageDataById(htk.constants.ACTIVE_ROLE);
        return data && data == 2;
    }
    htk.isSuperAdmin =  function(){
        let data = htk.getLocalStorageDataById(htk.constants.ACTIVE_ROLE);
        return data && data == 1;
    }

} catch (e) {}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

let token = document.head.querySelector('meta[name="csrf-token"]');
window.csrf = token.content;

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}
let intercepterCalled = false;
// const CancelToken = axios.CancelToken;
// let cancel;

// axios.interceptors.request.use((config) => {

//   if (cancel && intercepterCalled) {
//     cancel(); // cancel request
//   }
//   config.cancelToken =  new CancelToken(function executor(c)
//     {
//       cancel = c;
//     })

//   return config

// }, function (error) {
//   return Promise.reject(error)
// });
window.axios.interceptors.response.use(function (response) {
   // Any status code that lie within the range of 2xx cause this function to trigger
    // Do something with response data
    //   throw new axios.Cancel('Login Session Expired');
        const csrf = response.data && response.data.csrf ? response.data.csrf : window.csrf 
        if(csrf && response.data.isLogged == false){
            window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrf;
            $('meta[name="csrf-token"]').attr("content",csrf);  
            $('body').attr("csrf",csrf);  
        }
        return response;
  }, function (error) {
    // Any status codes that falls outside the range of 2xx cause this function to trigger
    const status = error.response ? error.response.status : null
    
    if(status == 401 && !intercepterCalled && error.response.data.activeRole == 0){
      let csrf = error.response.data.csrf;
      window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrf;
      $('meta[name="csrf-token"]').attr("content",csrf);  
      $('body').attr("csrf",csrf);  
      intercepterCalled = true;
      localStorage.removeItem("isLoggedIn");
      window.htk.history.push("/login");
      throw new axios.Cancel('Login Session Expired');
    } else if(status == 403){
        window.htk.history.push("/login");
    } else{
      intercepterCalled = false;
      return Promise.reject(error);
    }
    // Do something with response error
  });
/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo'

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     encrypted: true
// });

window.Pusher = require('pusher-js');

window.notifier = new Pusher('f9d8bddf802745d82a74', {
    cluster: 'ap2',
    forceTLS: true,
    //authEndpoint: '/broadcasting/auth',
    auth: {
      headers: { 'X-CSRF-Token': csrf }
    }
  });