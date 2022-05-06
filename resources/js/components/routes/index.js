import home from "./../../app-resources/svgs/manager/home.svg";
import products from "./../../app-resources/svgs/manager/products.svg";
import productNotes from "./../../app-resources/svgs/manager/Product-Notes.svg";
import campaignTagging from "./../../app-resources/svgs/manager/Campaign-Tagging.svg";
import asinW from "./../../app-resources/svgs/manager/AsinW.svg";
import adVisualsW from "./../../app-resources/svgs/manager/adVisualsW.svg";
import biddingRuleSvg from "./../../app-resources/svgs/manager/Biding-Rules.png.svg";
import reportsW from "./../../app-resources/svgs/manager/ReportsW.svg"
import dayPartingLogo from "../../app-resources/svgs/manager/Day-Parting.svg";


import manageUser from "./../../app-resources/svgs/admin/ManageUser.svg";
import LabelOverride from "./../../app-resources/svgs/admin/Label-Override.svg";
import dashboard from "./../../app-resources/svgs/admin/dashboard.svg";
import Brands from "./../../app-resources/svgs/admin/Brands.svg";
import VendorCentral from "./../../app-resources/svgs/admin/VC-White.svg";
import SellerCentral from "./../../app-resources/svgs/admin/SellerCentral.svg";
import AMS from "./../../app-resources/svgs/admin/AMS.svg";


import Agency from "./../../app-resources/svgs/superAdmin/Agencies.svg";
import ASIN from "./../../app-resources/svgs/superAdmin/ASIN.svg";
import SearchRank from "./../../app-resources/svgs/superAdmin/search rank.svg";
import BuyBox from "./../../app-resources/svgs/superAdmin/Buy Box.svg";

export const managerLinks = [
    {
        "linkNo":0,
        "isDropDown": false,
        "text": "Home",
        "icon": home,
        "to": "/",
    },
    {
        "isDropDown": true,
        "text": "Products",
        "icon": products,
        "dropDownIndex":0,
        "dropDown": [
            {
                "linkNo":1,
                "text": "Events",
                "hasIcon": true,
                "icon": productNotes,
                "to": "/events",
            }
        ]
    },
    {
        "isDropDown": true,
        "text": "Advertising",
        "icon": reportsW,
        "dropDownIndex":1,
        "dropDown": [
            {
                "linkNo":2,
                "text": "Advertising Visuals",
                "hasIcon": true,
                "icon": adVisualsW,
                "to": "/adVisuals",
            },
            {
                "linkNo":3,
                "text": "ASIN Performance",
                "hasIcon": true,
                "icon": asinW,
                "to": "/asinVisuals",
            },
            {
                "linkNo":4,
                "text": "Campaign Tagging",
                "hasIcon": true,
                "icon": campaignTagging,
                "to": "/compaignTagging",
            },
            {
                "linkNo":5,
                "text": "Day Parting",
                "hasIcon": true,
                "icon": dayPartingLogo,
                "to": "/dayParting",
            },
            {
                "linkNo":6,
                "text": "Day Parting History",
                "hasIcon": true,
                "icon": dayPartingLogo,
                "to": "/DayPartingHistory",
            },
            {
                "linkNo":7,
                "text": "Advertising Report",
                "hasIcon": true,
                "icon": reportsW,
                "to": "/emailSchedule",
            },
            {
                "linkNo":8,
                "text": "Bidding Rule",
                "hasIcon": true,
                "icon": biddingRuleSvg,
                "to": "/biddingRule",
            },
        ]
    },
];
export const adminLinks = [
    {
        "linkNo":0,
        "isDropDown": false,
        "text": "Dashboard",
        "icon": dashboard,
        "to": "/admin",
    },
    {
        "linkNo":1,
        "isDropDown": false,
        "text": "Manage Users",
        "icon": manageUser,
        "to": "/manageUser",
    },
    {
        "isDropDown": true,
        "text": "Brands",
        "icon": Brands,
        "dropDownIndex":0,
        "dropDown": [
            {
                "linkNo":2,
                "text": "Manage Brands",
                "hasIcon": false,
                "to": "/manageBrands",
            },
            {
                "linkNo":3,
                "text": "Manage Accounts",
                "hasIcon": false,
                "to": "/manageAccounts",
            },
        ]
    },
    {
        "linkNo":4,
        "isDropDown": false,
        "text": "Label Override",
        "icon": LabelOverride,
        "to": "/labelOverride",
    },
    {
        "isDropDown": true,
        "text": "Vendor Central",
        "icon": VendorCentral,
        "dropDownIndex":1,
        "dropDown": [
            {
                "linkNo":5,
                "text": "Daily Sales",
                "hasIcon": false,
                "to": "/dailySales",
            },
            {
                "linkNo":6,
                "text": "Purchase Order",
                "hasIcon": false,
                "to": "/purchaseOrder",
            },
            {
                "linkNo":7,
                "text": "Daily Inventory",
                "hasIcon": false,
                "to": "/dailyInventory",
            },
            {
                "linkNo":8,
                "text": "Traffic",
                "hasIcon": false,
                "to": "/traffic",
            },
            {
                "linkNo":9,
                "text": "Forecast",
                "hasIcon": false,
                "to": "/forecast",
            },
            {
                "linkNo":10,
                "text": "Catalog",
                "hasIcon": false,
                "to": "/catalog",
            },
            {
                "linkNo":11,
                "text": "Vendors",
                "hasIcon": false,
                "to": "/vendors",
            },
            {
                "linkNo":12,
                "text": "Export CSV",
                "hasIcon": false,
                "to": "/VcHistory",
            },
            {
                "linkNo":13,
                "text": "Verify Record",
                "hasIcon": false,
                "to": "/VcDelete",
            },
        ]
    },
    {
        "isDropDown": true,
        "text": "AMS",
        "icon": AMS,
        "dropDownIndex":2,
        "dropDown": [
            {
                "linkNo":14,
                "text": "API Config",
                "hasIcon": false,
                "to": "/apiConfig",
            },
        ]
    },
    {
        "isDropDown": true,
        "text": "Seller Central",
        "icon": SellerCentral,
        "dropDownIndex":3,
        "dropDown": [
            {
                "linkNo":15,
                "text": "API Config",
                "hasIcon": false,
                "to": "/ScApiConfig",
            },
            {
                "linkNo":16,
                "text": "Export CSV",
                "hasIcon": false,
                "to": "/ScExport",
            },
        ]
    },
];
export const superAdminLinks = [
    
    {
        "linkNo":0,
        "isDropDown": false,
        "text": "Dashboard",
        "icon": dashboard,
        "to": "/superAdmin",
    },
    {
        "isDropDown": true,
        "text": "AMS",
        "icon": AMS,
        "dropDownIndex":0,
        "dropDown": [
            {
                "linkNo":1,
                "text": "Scheduling",
                "hasIcon": false,
                "to": "/amsScheduling",
            },
        ]
    },
    {
        "isDropDown": true,
        "text": "Seller Central",
        "icon": SellerCentral,
        "dropDownIndex":1,
        "dropDown": [
            {
                "linkNo":2,
                "text": "Scheduling",
                "hasIcon": false,
                "to": "/sellerCentral",
            },
        ]
    },
    {
        "isDropDown": true,
        "text": "Agency",
        "icon": Agency,
        "dropDownIndex":2,
        "dropDown": [
            {
                "linkNo":3,
                "text": "Manage Agencies",
                "hasIcon": false,
                "to": "/agencies",
            },
        ]
    },
    {
        "isDropDown": true,
        "text": "Asins Scraping",
        "icon": ASIN,
        "dropDownIndex":3,
        "dropDown": [
            {
                "linkNo":4,
                "text": "Manage Collections",
                "hasIcon": false,
                "to": "/asin/collections",
            },
            {
                "linkNo":5,
                "text": "Manage schedules",
                "hasIcon": false,
                "to": "/asin/schedules",
            },
        ]
    },
    {
        "linkNo":6,
        "isDropDown": false,
        "text": "Search Rank",
        "icon": SearchRank,
        "to": "/sr/schedules",
    },
    {
        "linkNo":7,
        "isDropDown": false,
        "text": "Buy Box",
        "icon": BuyBox,
        "to": "/buybox/scheduling",
    },
];