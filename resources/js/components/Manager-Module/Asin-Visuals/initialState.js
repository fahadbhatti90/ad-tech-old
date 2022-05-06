import moment from 'moment';
import React from 'react';

export const initialState ={
    profileOptions:[],
    campaignOptions:[],
    asinOptions:[],
    scoreCards:[],
    perfData:[],
    effiData:[],
    awareData:[],
    getPerformanceY2Min:null,
    selectedProfile: null,
    selectedCampaign:null,
    selectedAsin:null,
    selectedDate: "",
    startDate:"",
    endDate:"",
    dateRange: {},
    dateRangeObj:{
        startDate:new Date(),
        endDate:new Date(),
        key: 'selection',
    },
    showFilterLoader:false,
    showComcardsLoader:false,
    showPerfLoader:"",
    showEffiLoader:"",
    showAwarLoader:"",
    disableFilters:false,
    perfPercentagesData:[
        {
            prefix: "$",
            title:"Revenue",
            label:0,
            currency:0
            },
            {
            prefix: "$",
            title:"Cost",
            label:0,
            currency:0
            },
            {
            prefix: "%",
            title:"Acos",
            label:0,
            currency:0
            }
    ],
    effiPercentagesData:[
        {
            prefix: "$",
            title:"CPC",
            label:0,
            currency:0
            },
            {
            prefix: "$",
            title:"ROAS",
            label:0,
            currency:0
            },
            {
            prefix: "$",
            title:"CPA",
            label:0,
            currency:0
            }
    ],
    awarPercentagesData: [
        {
            prefix: "",
            title:"Impressions",
            label:0,
            currency:0
        },
        {
        prefix: "",
        title:"Clicks",
        label:0,
        currency:0
        },
        {
        prefix: "%",
        title:"CTR",
        label:0,
        currency:0
        }],
    // definedRanges: [
    //                 {   endDate:moment()._d,
    //                     label:"Last Week",
    //                     startDate:moment().subtract(6, 'days')._d
    //                 },
    //                 {   endDate:moment()._d,
    //                     label:"Last 2 Weeks",
    //                     startDate:moment().subtract(12, 'days')._d
    //                 },
    //                 {   endDate:moment().endOf('month')._d,
    //                     label:"This Month",
    //                     startDate:moment().startOf('month')._d
    //                 },
    //                 {   endDate:moment().subtract(1, 'month').endOf('month')._d,
    //                     label:"Last Month",
    //                     startDate:moment().subtract(1, 'month').startOf('month')._d
    //                 }
    //             ],
}