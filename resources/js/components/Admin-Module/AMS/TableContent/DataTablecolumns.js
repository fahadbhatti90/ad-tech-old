import React from "react";
import ActionBtn from "./ActionBtns";
import AMSTooltip from './../AMSTooltip';
const getCellData = (row, column, limit = 50) => {
    return row && row[column].length > limit?
    <AMSTooltip row={row} tooltipContent={row[column]} tooltipTarget={<div className="tooltipText"> {row[column]} </div>}/>
    : row[column];
}
export const columns = (deleteApiCall, editApiConfig) => [
    {
        name: 'Sr.#',
        selector: 'id',
        sortable: false,
        maxWidth: "100px"
    },
    {
        name: 'Grant Type',
        selector: 'grant_type',
        sortable: true,
        maxWidth: "150px"
    },
    {
        name: 'Refresh Token',
        selector: 'refresh_token',
        sortable: true,
        maxWidth: "200px",
        cell: (row)=> getCellData(row, "refresh_token")
    },
    {
        name: 'Client Id',
        selector: 'client_id',
        sortable: true,
        maxWidth: "200px",
        cell: (row)=> getCellData(row, "client_id")
    },
    {
        name: 'Client Secret',
        selector: 'client_secret',
        sortable: true,
        wrap: true,
        maxWidth: "200px",
        cell: (row)=> getCellData(row, "client_secret")
    },
    {
        name: 'Created At',
        selector: 'created_at',
        sortable: true,
        wrap: true,
        maxWidth: "200px"
    },
    // {
    //     name: "Actions",
    //     cell: row => <ActionBtn
    //         row={row}
    //         deleteApiConfig={deleteApiCall}
    //         editApiConfig={editApiConfig}
    //     />,
    //     ignoreRowClick: true,
    //     allowOverflow: true,
    //     button: true,
    //     minWidth: "100px"
    // },
];