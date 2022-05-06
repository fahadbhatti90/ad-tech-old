import React from 'react';
import "./styles.scss";
import {Divider,Typography} from '@material-ui/core';
import RefreshIcon from "@material-ui/icons/Refresh";
import IconButton from '@material-ui/core/IconButton';

export default function CardHeader(props) {
    return (
        <>
        <Typography component={"span"} className="heading">
        {props.heading} <Typography component={"span"} className="heading subHeading">{props.subHeading}</Typography>
            <IconButton 
                className="iconBtn" 
                aria-label="refresh" 
                size="small"
                onClick={()=>props.reloadApiCall(props.name)}
                >
                <RefreshIcon className="icon" />
            </IconButton>

        </Typography>
        <Divider light className={props.customClass?props.customClass:"divider"}/>
        </>
    );
}