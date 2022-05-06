import React from 'react';
import moment from 'moment';

let firstPart = [{
    prefix: "",
    title: "Impressions",
    label: "0",
    currency: "0"
},
    {
        prefix: "$",
        title: "Cost",
        label: "0",
        currency: "0"
    },
    {
        prefix: "$",
        title: "Rev",
        label: "0",
        currency: "0"
    }]
let secondPart = [{
    prefix: "%",
    title: "ACOS",
    label: "0",
    currency: "0"
},
    {
        prefix: "$",
        title: "CPC",
        label: "0",
        currency: "0"
    },
    {
        prefix: "$",
        title: "ROAS",
        label: "0",
        currency: "0"
    }]
export const initialState = {
    closeMenuOnSelect:true,
    disableFilters:false,
    profileOptions: [],
    campaignOptions: [],
    productOptions: [],
    TopCampaignsData: [],
    strategyOptions: [],
    showHidden:false,
    selectedStrategy: null,
    TopXCampaigns: 5,
    AdData: [],
    rowsToAdd:5,
    StrrowsToAdd:5,
    ProrowsToAdd:5,
    PrerowsToAdd:5,
    CstrowsToAdd:5,
    PreYTDrowsToAdd:5,
    AdGrands: [],
    StrData: [],
    StrGrands: [],
    CstData: [],
    CstGrands: [],
    ProData: [],
    ProGrands: [],
    PreData: [],
    PreGrands: [],
    PreYTDData: [],
    PreYTDGrands: [],
    scoreCards: [],
    perfData: [],
    effiData: [],
    awareData: [],
    getPerformanceY2Min: null,
    selectedProfile: null,
    selectedCampaign: null,
    selectedProduct: null,
    selectedDate: "",
    startDate: "",
    endDate: "",
    showDRP: false,
    dateRange: {},
    dateRangeObj:{
        startDate:new Date(),
        endDate:new Date(),
        key: 'selection',
    },
    showFilterLoader: false,
    showComcardsLoader: false,
    showPerfLoader: false,
    showEffiLoader: false,
    showAwarLoader: false,
    showMOMLoader: false,
    showWOWLoader: false,
    showDODLoader: false,
    showYTDLoader: false,
    showWTDLoader: false,
    showADLoader: false,
    showStrLoader: false,
    showCstLoader: false,
    showProLoader: false,
    showPreLoader: false,
    showPreYTDLoader: false,
    mtdDataLeft: firstPart,
    mtdDataRight: secondPart,

    wowDataLeft: firstPart,
    wowDataRight: secondPart,

    dodDataLeft: firstPart,
    dodDataRight: secondPart,

    ytdDataLeft: firstPart,
    ytdDataRight: secondPart,

    wtdDataLeft: firstPart,
    wtdDataRight: secondPart,


    perfPercentagesData: [
        {
            prefix: "$",
            title: "Revenue",
            label: 0,
            currency: 0
        },
        {
            prefix: "$",
            title: "Cost",
            label: 0,
            currency: 0
        },
        {
            prefix: "%",
            title: "Acos",
            label: 0,
            currency: 0
        }
    ],
    effiPercentagesData: [
        {
            prefix: "$",
            title: "CPC",
            label: 0,
            currency: 0
        },
        {
            prefix: "$",
            title: "ROAS",
            label: 0,
            currency: 0
        },
        {
            prefix: "$",
            title: "CPA",
            label: 0,
            currency: 0
        }
    ],
    awarPercentagesData: [
        {
            prefix: "",
            title: "Impressions",
            label: 0,
            currency: 0
        },
        {
            prefix: "",
            title: "Clicks",
            label: 0,
            currency: 0
        },
        {
            prefix: "%",
            title: "CTR",
            label: 0,
            currency: 0
        }],
    // definedRanges: [
    //     {
    //         endDate: moment()._d,
    //         label: "Last Week",
    //         startDate: moment().subtract(6, 'days')._d
    //     },
    //     {
    //         endDate: moment()._d,
    //         label: "Last 2 Weeks",
    //         startDate: moment().subtract(12, 'days')._d
    //     },
    //     {
    //         endDate: moment().endOf('month')._d,
    //         label: "This Month",
    //         startDate: moment().startOf('month')._d
    //     },
    //     {
    //         endDate: moment().subtract(1, 'month').endOf('month')._d,
    //         label: "Last Month",
    //         startDate: moment().subtract(1, 'month').startOf('month')._d
    //     }
    // ],

}