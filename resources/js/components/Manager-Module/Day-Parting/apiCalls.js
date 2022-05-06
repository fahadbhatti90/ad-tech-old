import axios from 'axios';
import React from 'react';
import {profilesMapping, breakProfileId} from "./../../../helper/helper";


export const DAY_PARTING_PROFILE_URL = window.baseUrl + "/dayParting/day-parting-profile";
export const DAY_PARTING_PORTFOLIO_CAMPAIGN_URL = window.baseUrl + "/dayParting/getCampaignPortfolioData";
export const DAY_PARTING_ADD_FETCH_SCHEDULE_URL = window.baseUrl + "/dayParting/schedule";
export const DAY_PARTING_FETCH_SCHEDULE_URL = window.baseUrl + "/dayParting/scheduleList";
export const DAY_PARTING_EDIT_SCHEDULE__URL = window.baseUrl + "/dayParting/editSchedule";
export const DAY_PARTING_EDIT_SUBMIT_SCHEDULE_URL = window.baseUrl + "/dayParting/editScheduleSubmit";
export const DAY_PARTING_DELETE_SCHEDULE_URL = window.baseUrl + "/dayParting/deleteSchedule";
export const DAY_PARTING_STOP_SCHEDULE_URL = window.baseUrl + "/dayParting/stopSchedule";
export const DAY_PARTING_START_SCHEDULE_URL = window.baseUrl + "/dayParting/startSchedule";
export const DAY_PARTING_GET_HISTORY_SCHEDULE_URL = window.baseUrl + "/dayParting/getHistorySchedule";

export function getProfiles(callback, errorcallback) {
    // Get All Profiles Associated with master brand
    axios.get(DAY_PARTING_PROFILE_URL + '?_=' + new Date().getTime())
        .then(res => {
            if (callback != null) {
                let data = profilesMapping(res.data);
                callback(data);
            }
        })
        .catch(err => {
            if (errorcallback != null) {
                errorcallback(err);
            }
        })
}

export function getHistorySchedule(profileId, callback, errorcallback) {
    // Get All Profiles Associated with master brand
    axios.post(DAY_PARTING_GET_HISTORY_SCHEDULE_URL + '?_=' + new Date().getTime(),{
        "fkProfileId": breakProfileId(profileId),
    })
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

export function getCampaigns(pf, pct, oldPft='', addEdit='', callback, errorcallback) {

    let profileId = pf;
    if (addEdit == ''){
        let profileId = breakProfileId(pf);
    }
    // Get All Profiles Associated with master brand
    axios.post(DAY_PARTING_PORTFOLIO_CAMPAIGN_URL + '?_=' + new Date().getTime(),
        {
            "portfolioCampaignType": pct,
            "fkProfileId": profileId,
            "oldPortfolioCampaignType":oldPft
        })
        .then(res => {
            let data = res.data.text;
            if (data.length > 0) {
                data = data.map((obj, idx) => {
                    return {
                        value: obj.id + '|' + obj.name,
                        label: obj.name
                    }
                })
            }
            if (callback != null) {
                callback(data);
            }

        })
        .catch(err => {
            if (errorcallback != null) {
                errorcallback(err);
            }
        })
}

/**
 * This APi is used for storing Day parting Data
 * @param params
 * @param errorcallback
 */
export function storeDayPartingForm(params, callback, errorcallback) {
    axios.post(DAY_PARTING_ADD_FETCH_SCHEDULE_URL + '?_=' + new Date().getTime(), params)
        .then((res) => {
            callback(res.data);
    }).catch((error) => {
        if (errorcallback != null) {
            errorcallback(err);
        }
    })
}

/**
 * This APi is used for storing Day parting Data
 * @param params
 * @param errorcallback
 */
export function storeEditDayPartingForm(params, callback, errorcallback) {
    axios.post(DAY_PARTING_EDIT_SUBMIT_SCHEDULE_URL + '?_=' + new Date().getTime(), params)
        .then((res) => {
            callback(res.data);
        }).catch((error) => {
        if (errorcallback != null) {
            errorcallback(err);
        }
    })
}

export function showEditPartingForm(params, callback, errorcallback) {
    axios.post(DAY_PARTING_EDIT_SCHEDULE__URL + '?_=' + new Date().getTime(), params)
        .then((res) => {
            callback(res.data);
        }).catch((error) => {
        if (errorcallback != null) {
            errorcallback(err);
        }
    })
}

export function deleteDayPartingSchedule(params, callback, errorcallback){
    axios.post(DAY_PARTING_DELETE_SCHEDULE_URL + '?_=' + new Date().getTime(), params)
        .then((res) => {
            callback(res.data);
        }).catch((error) => {
        if (errorcallback != null) {
            errorcallback(err);
        }
    })
}

export function stopDayPartingSchedule(params, callback, errorcallback){
    axios.post(DAY_PARTING_STOP_SCHEDULE_URL + '?_=' + new Date().getTime(), params)
        .then((res) => {
            callback(res.data);
        }).catch((error) => {
        if (errorcallback != null) {
            errorcallback(err);
        }
    })
}
export function startDayPartingSchedule(params, callback, errorcallback){
    axios.post(DAY_PARTING_START_SCHEDULE_URL + '?_=' + new Date().getTime(), params)
        .then((res) => {
            callback(res.data);
        }).catch((error) => {
        if (errorcallback != null) {
            errorcallback(err);
        }
    })
}

export function getAllSchedules(callback, errorcallback) {
    axios.get(DAY_PARTING_FETCH_SCHEDULE_URL + '?_=' + new Date().getTime())
        .then((res) => {
            callback(res.data);
        })
        .catch(err => {
            if (errorcallback != null) {
                errorcallback(err);
            }
        })
}