
import React from 'react';
import Tooltip from '@material-ui/core/Tooltip';
import DeleteIcon from '@material-ui/icons/Delete';
import EditIcon from '@material-ui/icons/Edit';

const notesRowHandler = (row, classes) => {
    const childBrand = <Tooltip classes={{
        popper:classes.mainClass,
        popperInteractive:classes.events,
        tooltip:classes.eTooltip,
        arrow:classes.eArrow,
       }} className="newClass" placement="top" title={row.notes} arrow interactive>
                            <div>
                                {row.notes.substr(0,50)+"..."}
                            </div>
                        </Tooltip>
    return row.notes.length > 47 ? childBrand : row.notes;
}
const actionHandler = (row, classes, handleOnDeleteEventButtonClick, handleOnEditEventButtonClick) =>{
    return row.eventId == 4 || row.eventId == 5 ? <div el-id={row.orignalId} className="eventLogsActions">
        <Tooltip classes={{
            popper:classes.mainClass,
            popperInteractive:classes.events,
            tooltip:classes.eTooltip,
            arrow:classes.eArrow,
        }} placement="top" title={"Delete"} arrow interactive>
                <DeleteIcon className="themeTextColor"  onClick={handleOnDeleteEventButtonClick}/>  
        </Tooltip>
        <Tooltip classes={{
            popper:classes.mainClass,
            popperInteractive:classes.events,
            tooltip:classes.eTooltip,
            arrow:classes.eArrow,
        }} placement="top" title={"Edit"} arrow interactive>
                <EditIcon className="themeTextColor" onClick={handleOnEditEventButtonClick}/>
        </Tooltip>
    </div>:<div el-id={row.orignalId} className="eventLogsActions">
        <Tooltip classes={{
            popper:classes.mainClass,
            popperInteractive:classes.events,
            tooltip:classes.eTooltip,
            arrow:classes.eArrow,
        }} placement="top" title={"Automated Event Cannot Delete"} arrow interactive>
                <DeleteIcon className="themeTextColor" style={{color:"#a0a0a0", opacity:"0.3" }}/>  
        </Tooltip>
        <Tooltip classes={{
            popper:classes.mainClass,
            popperInteractive:classes.events,
            tooltip:classes.eTooltip,
            arrow:classes.eArrow,
        }} placement="top" title={"Automated Event Cannot Edit"} arrow interactive>
                <EditIcon className="themeTextColor"  style={{color:"#a0a0a0", opacity:"0.3"}}/>
        </Tooltip>
    </div>
}
const getAccountColumnValue = (row, classes) => {
    let childBrandName = row.accountName ?? "None" ;
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
export const getTableColumns = (classes, handleOnDeleteEventButtonClick, handleOnEditEventButtonClick) =>{
    return [
        {
            name: "Sr.#",
            selector: 'id',
            sortable: false,
            maxWidth:"50px"
        }, 
        {
            name: 'ASIN',
            selector: 'asin',
            sortable: true,
            maxWidth:"120px",
        },
        {
            name: 'Child Brand',
            selector: 'accountName',
            sortable: true,
            wrap: true,
            cell: (row)=>getAccountColumnValue(row, classes)
        },
        {
            name: 'Event',
            selector: 'eventName',
            sortable: true,
            wrap:true,
        },
        {
            name: 'Notes',
            selector: 'notes',
            sortable: true,
            wrap:true,
            maxWidth:"300px",
            cell: (row)=>notesRowHandler(row, classes)
        },
        {
            name: 'Occurrence Date',
            selector: 'occurrenceDate',
            sortable: true,
            wrap:true,
            maxWidth:"120px",
        },
        {
            name: 'Actions',
            selector: 'id',
            sortable: false,
            wrap:true,
            maxWidth:"100px",
            cell: (row)=> actionHandler(row, classes, handleOnDeleteEventButtonClick, handleOnEditEventButtonClick),
        },
    ];
}