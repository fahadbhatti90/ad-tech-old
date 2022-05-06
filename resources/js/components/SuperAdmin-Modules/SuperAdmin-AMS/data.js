import CellTooltip from "./../../../general-components/dataTableToolTip/CellTooltip";
import React from "react";

export const listOptions =[
    {
        label:"Advertising Campaign Report",
        value: "Advertising_Campaign_Reports"
    },
    {
        label:"Ad Group Report",
        value: "Ad_Group_Reports"
    },
    {
        label:"Keyword Report",
        value: "Keyword_Reports"
    },
    {
        label:"Product Ads Report",
        value: "Product_Ads_Report"
    },
    {
        label:"ASINs Report",
        value: "ASINs_Report"
    },
    {
        label:"Product Attribute Targeting Report",
        value: "Product_Attribute_Targeting_Reports"
    },
    {
        label:"Sponsored Brand Keyword Report",
        value: "Sponsored_Brand_Reports"
    },
    {
        label:"Sponsored Brand Campaigns Report",
        value: "Sponsored_Brand_Campaigns"
    },
    {
        label:"Sponsored Display Campaigns Report",
        value: "Sponsored_Display_Campaigns"
    },
    {
        label:"Sponsored Display ProductAds Report",
        value: "Sponsored_Display_ProductAds"
    },
    {
        label:"Sponsored Display Adgroup Report",
        value: "Sponsored_Display_Adgroup"
    },
    {
        label:"Sponsored Brand Adgroup Report",
        value: "Sponsored_Brand_Adgroup"
    },
    {
        label:"Sponsored Brand Targeting Report",
        value: "Sponsored_Brand_Targeting"
    }
]

export const timeOptions = [
    {
        label:"16:00",
        value:"16:00"
    },
    {
        label:"17:00",
        value:"17:00"
    },
    {
        label:"18:00",
        value:"18:00"
    },
    {
        label:"19:00",
        value:"19:00"
    },
    {
        label:"20:00",
        value:"20:00"
    },
    {
        label:"21:00",
        value:"21:00"
    },
    {
        label:"22:00",
        value:"22:00"
    },
    {
        label:"23:00",
        value:"23:00"
    },
    {
        label:"00:00",
        value:"00:00"
    },
    {
        label:"01:00",
        value:"01:00"
    },
    {
        label:"02:00",
        value:"02:00"
    },
    {
        label:"03:00",
        value:"03:00"
    },
    {
        label:"04:00",
        value:"04:00"
    },
    {
        label:"05:00",
        value:"05:00"
    },
    {
        label:"06:00",
        value:"06:00"
    },
    {
        label:"07:00",
        value:"07:00"
    },
    {
        label:"08:00",
        value:"08:00"
    },
    {
        label:"09:00",
        value:"09:00"
    },
    {
        label:"10:00",
        value:"10:00"
    },
    {
        label:"11:00",
        value:"11:00"
    },
    {
        label:"12:00",
        value:"12:00"
    },
    {
        label:"13:00",
        value:"13:00"
    },
    {
        label:"14:00",
        value:"14:00"
    },
    {
        label:"15:00",
        value:"15:00"
    },
]

export const scheduledColumns = [
    {
        name: 'Name',
        selector: 'cronType',
        sortable: true,
        maxWidth:"80%",	
        cell:row => <CellTooltip 
                        title={row.cronType} 
                        placement="top"
                        limit={30}
                    />
    },
    {
        name: 'Time',
        selector: 'cronTime',
        sortable: true,
        maxWidth:"10%"
    },
    {
        name: 'Status',
        selector: 'cronStatus',
        sortable: true,
        maxWidth:"10%",
        cell:row =><span className="capitalize">{row.cronStatus}</span>
    }
]