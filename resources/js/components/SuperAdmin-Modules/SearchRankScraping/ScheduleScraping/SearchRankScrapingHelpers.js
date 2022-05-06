
import React, {Component} from 'react';
import Tooltip from '@material-ui/core/Tooltip';
import ActionBtns from './ActionBtns';
const getLimitedValue = (value) => {
    return value ? value.length > 30 
    ? value.slice(0,27)+"..." 
    : value
    : "None";
}
const ToolTipContent = (props) => {
    return <div>
        {props.tooltipContent}
    </div>
}
const CustomTooltip = (props) =>{
    let classes = props.classes;
    let TooltipTarget = props.tooltipTarget;
    let ToolTipContent = props.tooltipContent;
    return <Tooltip classes={{
        popper:classes.mainClass,
        popperInteractive:classes.productTable,
        tooltip:classes.ptTooltip,
        arrow:classes.ptArrow,
       }} className="newClass" placement="top" title={ToolTipContent} arrow interactive>
                            {TooltipTarget}
                        </Tooltip>
}
export const addTooltTip = (value, classes) =>{
    let newValue = getLimitedValue(value);
    let tooltipTarget = <div> {newValue} </div>
    return newValue != "None" ? <CustomTooltip 
    classes= {classes}
    tooltipTarget = {tooltipTarget}
    tooltipContent = {<ToolTipContent tooltipContent={value}  />}
    /> : newValue;
}

export const getScheduleTableColumns = (classes, deleteHelperCall) => {
    return [
        {
            name: 'Sr.#',
            selector: 'sr',
            sortable: true,
            maxWidth:"50px",
            minWidth:"50px"
        }, 
        {
            name: 'Department',
            selector: 'departmentName',
            sortable: true,
            wrap: true,
            minWidth:"150px",
            maxWidth:"150px",
            cell: row => addTooltTip( row.departmentName, classes )
        },
        {
            name: 'Name',
            selector: 'sName',
            sortable: true,
            wrap: true,
            minWidth:"200px",
            maxWidth:"300px",
            cell: row => addTooltTip(row.id + "_" + row.sName, classes)
        },
        {
            name: 'Frequency',
            selector: 'frequency',
            sortable: true,
            wrap: true,
            minWidth:"100px",
            maxWidth:"100px",
        }, 
        {
            name: 'Last Run',
            selector: 'lastRun',
            sortable: true,
            wrap: true,
            minWidth:"110px",
            maxWidth:"110px",
            cell: row => row.lastRun == 0 ? "Not ran yet" : row.lastRun
        }, 
        {
            name: 'Next Run',
            selector: 'nextRun',
            sortable: true,
            wrap: true,
            minWidth:"110px",
            maxWidth:"110px",
        },
        {
            name: 'Status',
            selector: 'isRunning',
            sortable: true,
            wrap: true,
            maxWidth:"70px"
        },
        {
            name: 'Created At',
            selector: 'created_at',
            sortable: true,
            wrap: true,
            minWidth:"125px"
        },
        {
            name: "Actions",
            cell:row => <ActionBtns 
                row={row}
                deleteHelperCall={deleteHelperCall}
                />,
            ignoreRowClick: true,
            allowOverflow: true,
            button: true,
            maxWidth:"100px"
        },
    ];
}