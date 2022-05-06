import React from 'react'
import { makeStyles } from '@material-ui/core/styles';
import Tooltip from '@material-ui/core/Tooltip';
const useStyles = makeStyles(theme => ({
    mainClass:{

    },
    productTable: {
        
    },
    ptTooltip:{
        color: "#000",
        backgroundColor: "rgb(255 255 255 / 90%)",
        boxShadow: "1px 1px 10px #0000003b",
        overflow: "hidden",
    },
    ptArrow:{
        color: "#fff"
    },
}));

export default function ProductTableTooltip(props) {
    const classes = useStyles();
    return (
        <Tooltip classes={{
            popper:classes.mainClass,
            popperInteractive:classes.productTable,
            tooltip:classes.ptTooltip,
            arrow:classes.ptArrow,
           }} className="newClass" placement="top" title={props.tooltipContent} arrow interactive>
            {props.tooltipTarget}
        </Tooltip>
    )
}
