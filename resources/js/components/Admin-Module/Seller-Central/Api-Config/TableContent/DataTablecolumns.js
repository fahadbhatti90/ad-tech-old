import React from "react";
import ActionBtn from "./ActionBtns";
//import "../../styles.scss";

export const columns = (deleteApiCall, editApiConfig) => [
    {
        name: 'Seller Name',
        selector: 'merchant_name',
        sortable: true,
    },
    {
        name: 'Seller Id',
        selector: 'seller_id',
        sortable: true,
    },
    {
        name: 'Access Key Id',
        selector: 'mws_access_key_id',
        sortable: true,
        wrap: true
    },
    {
        name: 'Auth Token',
        selector: 'mws_authtoken',
        sortable: true,
        wrap: true
    },
    {
        name: 'Secret Key',
        selector: 'mws_secret_key',
        sortable: true,
        wrap: true,
    },
    {
        name: "Actions",
        cell: row => <ActionBtn
            row={row}
            deleteApiConfig={deleteApiCall}
            editApiConfig={editApiConfig}
        />,
        ignoreRowClick: true,
        allowOverflow: true,
        button: true,
        minWidth: "200px"
    },
];