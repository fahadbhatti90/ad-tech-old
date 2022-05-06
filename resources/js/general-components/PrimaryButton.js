import React from 'react';
import Button from '@material-ui/core/Button';
import { makeStyles } from '@material-ui/core/styles';
import { primaryColorOrange } from './../app-resources/theme-overrides/global';

const useStyles = makeStyles((theme) => ({
    button: {
      minWidth:112,
      backgroundColor: primaryColorOrange,
      color: "white",
      "&:hover": {
        backgroundColor: primaryColorOrange
      },
      textTransform:"capitalize"
    },
  }));

export default function PrimaryButton(props) {
    const classes = useStyles();
    return (
        <Button
            className ={`${classes.button} ${props.customclasses}`}
            variant={props.variant}
            style={{outline:"none"}}
            onClick={props.onClick}
            {...props}
            >
              {props.btnlabel ? props.btnlabel : props.btntext }
        </Button>
    );
  }