import React from "react";
import ActionBtn from "./ActionBtns";
import CellTooltip from "../../../../general-components/dataTableToolTip/CellTooltip";
//import '../../styles.scss';
export const columns =(editAgency,changePassword)=>[
    {
        name: 'Sr.#',
        selector: 'serial',
        sortable: true,
        maxWidth:"100px"
    },

    {
        name: 'Name',
        selector: 'name',
        sortable: false,
        wrap:true,
        cell:row => <CellTooltip
            title={row.name}
            placement="top"
            limit={14}
        />
    },
    {
        name: 'Email',
        selector: 'email',
        sortable: false,
        wrap:true,
        cell:row => <CellTooltip
            title={row.email}
            placement="top"
        />
    },
    {
        name: 'Created At',
        selector: 'created_at',
        cell:row => <div>{row.created_at.split(' ')[0]}</div>,
        sortable: true,
        wrap:true,
        maxWidth:"200px"
    },
    {
        name: "Actions",
        cell:row => <ActionBtn
            row={row}
            editAgency = {editAgency}
            changePassword={changePassword}

        />,
        ignoreRowClick: true,
        allowOverflow: true,
        button: true,
        minWidth:"200px"
    },
];