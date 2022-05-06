import React from "react";
import Tooltip from "@material-ui/core/Tooltip";
export default function CellTooltip (props) {
        return (
            <Tooltip title={props.title} placement={props.placement} arrow>
                <span>{
                    props.title.length>props.limit?
                    
                    props.title.substr(0,props.limit)+"..."
                    
                    :
                    props.title
                }</span>
            </Tooltip>
        );
    
}
