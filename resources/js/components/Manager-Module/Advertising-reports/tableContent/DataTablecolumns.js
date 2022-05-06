import React from "react";
import ActionBtns from "./ActionBtns";
import "./../styles.scss"
import CellTooltip from "./../../../../general-components/dataTableToolTip/CellTooltip";

function ViewMetricButton(props){
    return(
            <label className="viewMetrics" onClick={()=>props.onMetricClickHandler(props.row)}>View Metrics</label>
    );
}

export const columns =(onMetricHandler,deleteSchedule,editSchedule)=>[
    {
        name: 'Sr.#',
        selector: 'serial',
        sortable: true,
        maxWidth:"100px"
    },
    {
        name: 'Report Name',
        selector: 'reportName',
        sortable: true,
        maxWidth:"200px"
    },
    {
        name: 'Delivery Day and Time',
        selector: 'schedule',
        sortable: true,
        wrap:true,	
        cell:row => <CellTooltip 
                        title={row.schedule} 
                        placement="top"
                        limit={14}
                    />
    },
    {
        name: 'Sponsored Type',
        selector: 'sponsored_type',
        cell:row => <CellTooltip 
                        title={row.sponsored_type} 
                        placement="top"
                        limit={17}
                    />,
        sortable: true,
        wrap:true,
    },
    {
        name: 'Report Type',
        selector: 'report_type',
        cell:row => <CellTooltip 
                        title={row.report_type} 
                        placement="top"
                        limit={12}
                    />,
        sortable: true,
        wrap:true,
        
    },
    {
        name: 'Metrics',
        cell:(row)=><ViewMetricButton 
                    onMetricClickHandler={onMetricHandler}
                    row={row}
                     />,
        ignoreRowClick: true,
        button: true
    },
    {
        name: "Actions",
        cell:row => <ActionBtns 
            row={row}
            deleteSchedule={deleteSchedule}
            editSchedule = {editSchedule}/>,
        ignoreRowClick: true,
        allowOverflow: true,
        button: true,
      },
];