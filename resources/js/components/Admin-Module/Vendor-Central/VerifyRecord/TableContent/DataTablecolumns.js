import React from "react";
import '../../styles.scss';
export const columns =()=>[

    {
        name: 'Vendor ID',
        selector: 'fk_vendor_id',
        sortable: false,
        wrap:true
    },

    {
        name: 'Date',
        selector: 'date_column',
        sortable: false,
        wrap:true
    },
    {
        name: 'Count',
        selector: 'Row_Count',
        sortable: false,
        wrap:true
    },
    {
        name: 'Duplication',
        selector: 'dup_count',
        sortable: false,
        wrap:true
    }
];

export const trafficColumn =()=>[

    {
        name: 'Vendor ID',
        selector: 'fk_vendor_id',
        sortable: false,
        wrap:true
    },
    {
        name: 'Start Date',
        selector: 'start_date_column',
        sortable: false,
        wrap:true
    },
    {
        name: 'End Date',
        selector: 'end_date_column',
        sortable: false,
        wrap:true
    },
    {
        name: 'Count',
        selector: 'Row_Count',
        sortable: false,
        wrap:true
    },
    {
        name: 'Duplication',
        selector: 'dup_count',
        sortable: false,
        wrap:true
    }
];