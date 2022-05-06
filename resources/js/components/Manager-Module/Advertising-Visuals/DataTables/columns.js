import React from "react";
import "./../styles.scss"
import {commaSeparator} from "./../../../../helper/helper";
import Tooltip from "@material-ui/core/Tooltip";

export const AdTypecolumns =[
        {  label: 'Ad Type' },
        { label: 'Imp' },
        { label: 'Rev.($)' },
        { label: 'ACOS(%)' },
        { label: 'CTR(%)' },
        { label: 'Cost($)' }
    ];

export const StrategyTypecolumns =[
    { label: 'Strategy Type' },
    { label: 'Imp' },
    { label: 'Rev.($)' },
    { label: 'ACOS(%)' },
    { label: 'CTR(%)' },
    {label: 'Cost($)' }
];

export const CustomTypecolumns =[
    {  label: 'Custom Type' },
    {  label: 'Imp' },
    { label: 'Rev.($)' },
    { label: 'ACOS(%)' },
    { label: 'CTR(%)' },
    { label: 'Cost($)' }
];

export const ProductTypecolumns =[
    {label: 'Product Type' },
    {label: 'Imp' },
    {label: 'Rev.($)' },
    {label: 'ACOS(%)' },
    {label: 'CTR(%)' },
    {label: 'Cost($)' }
];

export const PerfPreTypecolumns =[
    { label: 'Account Name' },
    {label: 'Rev.($)' },
    { label: 'ACOS(%)' },
    { label: 'Cost($)' }
];

export const PerfYtdTypecolumns =[
    {label: 'Account Name' },
    { label: 'Rev.($)' },
    { label: 'ACOS(%)' },
    { label: 'Cost($)' }
];

export const TopCampaignsColumns=[
    {
        name: 'Campaign Name',
        selector: 'campaign_name',
        sortable: true,
        maxWidth:"350px"
    },
    {
        name: 'Spend($)',
        selector: 'spend',
        sortable: true,
        maxWidth:"210px",
        cell:row => <Tooltip placement="top" title={"$"+row.spend} arrow>
                        <span>{"$"+commaSeparator(row.spend)}</span>  
                    </Tooltip>
    },
    {
        name: 'Revenue($)',
        selector: 'revenue',
        sortable: true,
        maxWidth:"210px",
        cell:row => <Tooltip placement="top" title={"$"+row.revenue} arrow>
                        <span>{"$"+commaSeparator(row.revenue)}</span>  
                    </Tooltip>
    },
    {
        name: 'ACOS(%)',
        selector: 'acos_',
        sortable: true,
        maxWidth:"210px"
    }
]