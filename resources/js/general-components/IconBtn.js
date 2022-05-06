import React from 'react';
import clsx from 'clsx';
import Button from '@material-ui/core/Button';
import { makeStyles } from '@material-ui/core/styles';
import { primaryColorOrange, primaryColorOrangeHover } from '../app-resources/theme-overrides/global';

const useStyles = makeStyles((theme) => ({
    button: {
      background: primaryColorOrange,
      color: "white",
      "&:hover": {
        backgroundColor: primaryColorOrangeHover
      },
    },
  }));

export default function IconBtn(props) {
    const classes = useStyles();
    return(
    <Button
        variant="contained"
        onClick={props.onClick}
        className={clsx(classes.button,props.className)}
        startIcon={props.icon}
        style={props.style ? props.style : {outline:"none"}}
        >
     {props.BtnLabel}
    </Button>
    );
}