import React from "react";
import ActionBtns from "./ActionBtns";
import "./../styles.scss"
import CellTooltip from "./../../../../general-components/dataTableToolTip/CellTooltip";

export const columns =(deleteUserCall,editBrand,infoBtn)=>[
    {
        name: 'Sr.#',
        selector: 'serial',
        sortable: true,
        maxWidth:"100px"
    },
    {
        name: 'Name',
        selector: 'name',
        sortable: true,
        maxWidth:"200px",
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
        sortable: true,
        wrap:true
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
        cell:row => <ActionBtns 
            row={row}
            deleteUserCall={deleteUserCall}
            editBrand = {editBrand}
            UserInfo = {infoBtn}
            />,
        ignoreRowClick: true,
        allowOverflow: true,
        button: true,
        minWidth:"200px"
      },
];