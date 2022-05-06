import React from 'react';
import Button from '@material-ui/core/Button';

export default function TextButton(props) {
    const styles = {
        outline:"none",
        minWidth: 112,
        height: 33,
        background: '#201e1e38',
        color: '#6e6b6b',
        fontWeight: '600 !important',
        fontSize: 14,
        textTransform:"capitalize"
    }
    return (
        <Button
            variant={props.variant}
            style={props.styles ? props.styles : styles}
            color={props.color}
            onClick={props.onClick}
            {...props}
        >
            {props.BtnLabel ? props.BtnLabel : props.btntext }
        </Button>
    );
  }