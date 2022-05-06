
import React, {Component} from 'react';
import Tooltip from '@material-ui/core/Tooltip';
import ActionBtns from './ScheduleScraping/ActionBtns';
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
export const collectionNameRowHandler = (row, classes) =>{
   let newCName =  getLimitedValue(row.c_name + "_" + row.id);
    let tooltipTarget = <div> {newCName} </div>
    return newCName != "None" ? <CustomTooltip 
    classes= {classes}
    row = {row}
    tooltipTarget = {tooltipTarget}
    tooltipContent = {<ToolTipContent tooltipContent={newCName}  />}
    /> : newCName;
}

export const getCollectionTableColumns = () => {
    return [
        {
            name: 'Sr.#',
            selector: 'sr',
            sortable: true,
            maxWidth:"50px"
        }, 
        {
            name: 'Collection Name',
            selector: 'c_name',
            sortable: true,
            wrap: true,
            // cell: row => productTitleRowHandler(row, handleOnColumnClick, classes)
        }, 
        {
            name: 'Collection Type',
            selector: 'c_type',
            sortable: true,
            wrap: true,
        }, 
        {
            name: 'Total Asins',
            selector: 'asinCount',
            sortable: true,
            wrap: true,
        },
        {
            name: 'Created At',
            selector: 'created_at',
            sortable: true,
            wrap: true,
        }
    ];
}

export const getScheduleTableColumns = (classes, deleteUserCall) => {
    return [
        {
            name: 'Sr.#',
            selector: 'sr',
            sortable: true,
            maxWidth:"50px"
        }, 
        {
            name: 'Collection Name',
            selector: 'c_name',
            sortable: true,
            wrap: true,
            minWidth:"200px",
            maxWidth:"300px",
            cell: row => collectionNameRowHandler(row, classes)
        },
        {
            name: 'Status',
            selector: 'cronStatus',
            sortable: true,
            wrap: true,
            maxWidth:"70px"
        }, 
        {
            name: 'Last Run',
            selector: 'lastRun',
            sortable: true,
            wrap: true,
        }, 
        {
            name: 'Duration',
            selector: 'cronDuration',
            sortable: true,
            wrap: true,
        },
        {
            name: 'Running',
            selector: 'isRunning',
            sortable: true,
            wrap: false,
            maxWidth:"70px"
        },
        {
            name: 'Created At',
            selector: 'created_at',
            sortable: true,
            wrap: true,
            maxWidth:"170px"
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
            maxWidth:"100px"
        },
    ];
}