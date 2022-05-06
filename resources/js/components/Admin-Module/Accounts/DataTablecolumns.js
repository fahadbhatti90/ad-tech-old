import React from "react";
import ActionBtns from "./ActionBtns";
import "./styles.scss"
import Tooltip from '@material-ui/core/Tooltip';
import moment from "moment";
htk.moment = moment;
const getAccountColumnValue = (row, classes) => {
    let childBrandName = row.brandName;
    const childBrand = <Tooltip
                classes={{
                    popper:classes.mainClass,
                    popperInteractive:classes.events,
                    tooltip:classes.eTooltip,
                    arrow:classes.eArrow,
                }}
                className="newClass" placement="top"
                                title={childBrandName}
                                arrow interactive>
                    <span>
                        { 
                            childBrandName.length > 30
                            ? childBrandName.slice(0, 27) + "..."
                            : childBrandName
                        }
                    </span>
    </Tooltip>
    return childBrand;
}
export const columns =(deleteUserCall, classes)=>[
    {
        name: "Sr.#",
        selector: 'sr',
        sortable: true,
        maxWidth:"100px"
    }, 
    {
        name: 'Account Name',
        selector: 'accountName',
        sortable: true,
    },
    {
        name: 'Account Type',
        selector: 'accountType',
        sortable: true,
    },
    {
        name: 'Brand Name',
        selector: 'brandName',
        sortable: true,
        wrap: true,
        cell: (row)=>getAccountColumnValue(row, classes)
    },
    {
        name: 'Created At',
        selector: 'created_at',
        // cell:row => <div>{row.created_at.split(' ')[0]}</div>,
        sortable: true,
        wrap:true,
        maxWidth:"150px"
    },
    {
        name: "Actions",
        cell:row => <ActionBtns 
            row={row}
            deleteUserCall={deleteUserCall}
            />,
        ignoreRowClick: true,
        allowOverflow: true,
        button: true,
        minWidth:"200px"
      },
];