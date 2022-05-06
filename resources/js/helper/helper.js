import React from "react";
import * as d3 from 'd3';
import moment from 'moment';
import {USER} from "./../config/localStorageKeys";
import * as Yup from "yup";
import store from './../store/configureStore';
import {showSnackBar} from "./../general-components/snackBar/action";
import {alertNewNotification} from './../general-components/Notification/actions';


/**
 * Number Comma Separator
 */
export function commaSeparator(x) {
    let formatComma = d3.format(","), formatSuffixDecimal2 = d3.format(".5s");
    if (x > 10000) {
        return formatSuffixDecimal2(x);
    } else {
        return formatComma(x);
    }
}

/**
 * Check if object exist in array or not
 * @param {object and list array} x 
 */
export function containsObject(obj, list){
    let i;
    for (i = 0; i < list.length; i++) {
        if (list[i] === obj) {
            return true;
        }
    }
    return false;
  }
/**
 *
 * @param {*} x
 * comma format for a big number
 */
export function commaFormat(x) {
    let formatComma = d3.format(",");
    return formatComma(x);
}

/**
 *
 * @param {*} x
 * revert value to original value
 */
export function showOrginialValue(x) {
    let originalFormat = d3.format("");
    return originalFormat(x);
}

/**
 * helper Date conversion function
 */
export function helperDateFunction(date) {
    var dateFormat = new Date(Date.parse(date));
    const ye = new Intl.DateTimeFormat('en', {year: 'numeric'}).format(dateFormat)
    const mo = new Intl.DateTimeFormat('en', {month: 'short'}).format(dateFormat)
    const da = new Intl.DateTimeFormat('en', {day: '2-digit'}).format(dateFormat)
    return `${mo} ${da},${ye}`;
}

/**
 * Email Validation function
 */
export function validateEmail(email) {
    const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}

/**
 * Get Current Date
 */
export function currentDate() {
    return moment().format("dddd, MMMM Do YYYY");
}

/**
 * Get Formated Date
 */
 export function formateData(date) { 
    return moment(date).format("dddd, MMMM Do YYYY hh:mm:ss");
}
/**
 * profiles Select Data mapping
 */
export function profilesMapping(data) {
    
    let profileData = data[0].profiles.map((obj, idx) => {
        return {
            value: obj.id + "|" + obj.profileId,
            label: obj.name
        }
    })

    let accounts = data[0].profiles.map(item => {
        return item.accounts;
    })

    let names = accounts.map((arr, idx) => {
        return arr.brand_alias;
    })

    let overRideLab = names.map((arr, idx) => {
        if (Array.isArray(arr) && arr.length > 0) {
            return arr[0].overrideLabel;
        }
    })

    for (let i = 0; i < profileData.length; i++) {
        if (overRideLab[i]) {
            profileData[i].label = overRideLab[i];
        }
    }
    return profileData;
}

export function profilesMappingBhatti(profiles) {

    let profileData = profiles.map((obj, idx) => {
        return {
            value: obj.id + "|" + obj.profileId,
            label: obj.name
        }
    })

    let accounts = profiles.map(item => {
        return item.accounts;
    })

    let names = accounts.map((arr, idx) => {
        return arr.brand_alias;
    })

    let overRideLab = names.map((arr, idx) => {
        if (Array.isArray(arr) && arr.length > 0) {
            return arr[0].overrideLabel;
        }
    })

    for (let i = 0; i < profileData.length; i++) {
        if (overRideLab[i]) {
            profileData[i].label = overRideLab[i];
        }
    }

    return profileData;
}

export function getUserName() {
    let userData = JSON.parse(localStorage.getItem(USER));
    if (userData) {
        return userData.name;
    } else {
        return "Guest";
    }
}

export function getLocalStorageDataById(key){
    let data = JSON.parse(localStorage.getItem(key));
    if (data) {
        return data; 
    } else{
        return null;
    }
}
export function comCardMappings(cardData, response) {

    cardData.forEach(function (item, index) {
        if (item.title == "Impressions") {
            cardData[index] = {
                ...item,
                label: response.impressions_perc
            }
        }
        if (item.title == "Cost") {
            cardData[index] = {
                ...item,
                label: response.cost_perc
            }
        }
        if (item.title == "Rev") {
            cardData[index] = {
                ...item,
                label: response.revenue_perc
            }
        }
        if (item.title == "ACOS") {
            cardData[index] = {
                ...item,
                label: response.acos_perc
            }
        }
        if (item.title == "CPC") {
            cardData[index] = {
                ...item,
                label: response.cpc_perc
            }
        }
        if (item.title == "ROAS") {
            cardData[index] = {
                ...item,
                label: response.roas_perc
            }
        }
    });
    return cardData;
}

export function breakProfileId(profileId) {
    let Id = profileId.split('|');
    return (+Id[0]);
}
export function AddRows(AdtableLength){
    let rowsToAdd = 0;
        if(AdtableLength > 0){
            let rowResult = (AdtableLength % 5)
            if(rowResult != 0){
                rowsToAdd = 5-rowResult;
            }
        }else{
            rowsToAdd = 5;
        }
    return rowsToAdd;
}

/**
 * Replicate your elements
 * @param {} element 
 * @param {*} elements 
 */
export function generate(element,elements) {
    return Array.from({ length: elements }, (_, idx) => `${++idx}`).map((value,index)=>{
        return React.cloneElement(element, {
            key: value,
        })
    });
}//end function

export function breakCampaignName(campaignName) {
    let Id = campaignName.split('|');
    return (+Id[0]);
}

export function requiredValidationHelper(){
    return Yup.string().min(1, "Required");
}

export function checkArrayStates(inputVal){
    return (Array.isArray(inputVal) && inputVal.length != 0 && inputVal != null) ? 'true' : '';
}
export function getChannelName(id, type){
    return type == 3 ? "hello-tec-channel"+id : "hello-tec-channel" 
}
export function getOnPageReloadChannelName(id){
    return htk.isManager() ? "hello-tec-channel"+id : "hello-tec-channel" 
}
export function validateHost(data){
    if (!('host' in data) || data.host == "404" ){
        // alert("no HOst Found in incoming message");
        return false;
    }
    
    var CurrentHost = htk.host;
    if (CurrentHost == "404") {
        store.dispatch(showSnackBar("No host found in settings table please contact your service provider till then notifications module will not display live notfication", "error"));
        return false;
    } else {
        if(!CurrentHost.includes(data.host)) return false;
    }
    return true;
}
export function setUpUserTypeSpecificLiveNotification(channelName){
    //Notifier decleared in bootstrap.js at the end
    
    let channel = notifier.subscribe(channelName);

    channel.bind('sendNotification', function(data) {
        if (!validateHost(data)) return;
        if((htk.isManager() && data.type != 1) || 
        (htk.isSuperAdmin() && data.type == 1)) return;
        var key = "";
        switch (parseInt(data.type)) {
            case 2:
                key = "blacklist";
                break;
            case 3:
                key = "settings";
                break;
            default:
                key = "buybox";
                break;
        }

        let Notification = {};
        Notification.id= data.id;
        Notification.title= data.title;
        Notification.message= data.message;
        Notification.created_at= data.created_at;
        
        // switch (parseInt(data.type)) {
        //     case 2:
        //         break;
        //     case 3:
        //             break;
        //     default:
        //         break;
        // }
        store.dispatch(showSnackBar("You have a new notification", "info"));
        store.dispatch(alertNewNotification({data, key}))
    });
}

// export function autoScroll(temp, value, className) {
//     if (temp && value && temp.length <= value.length){
//         $("."+ className + ".select__value-container").animate({
//             scrollTop: $("."+ className + ".select__value-container").get(0).scrollHeight
//         });
//     }
// }
