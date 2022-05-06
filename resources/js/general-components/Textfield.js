// import ReactTooltip from "react-tooltip";
import React from 'react';
import clsx from 'clsx';
import TextField from '@material-ui/core/TextField';
import { makeStyles } from "@material-ui/core/styles";
import {primaryColor,primaryColorLight, primaryColorLighter} from "../app-resources/theme-overrides/global"
import InputAdornment from '@material-ui/core/InputAdornment';

const useStyles = makeStyles({
    root: {
      '& .MuiInputBase-root':{
        marginTop: 8,        
        borderRadius: 25,
        border: '1px solid #c3bdbd',
        height: 30,
        background: '#fafafa'
      },
      "&:hover .MuiInputBase-root": {
        borderColor: primaryColorLight,
        borderRadius: "25px !important"
      },
      '& .MuiInputBase-input':{
        margin: props=>props.margin || 15
      }
    },
    focused:{
      border: "2px solid !important",
      borderColor: `${primaryColor} !important`,
    }
  });

export default function TextFieldInput(props) {
    const classes = useStyles(props);
    return (
         <>
        <TextField
          className={clsx(props.classesstyle ? props.classesstyle.root : classes.root)}
          id={props.id}
          autoComplete={"off"}
          type={props.type}
          name={props.name}
          placeholder={props.placeholder}
          onChange={props.onChange}
          value={props.value}
          fullWidth={props.fullWidth}
          InputProps={{ disableUnderline: true,
            classes:{
              focused: classes.focused,
            }}}
            style={props.styles}
            {...props}
        />
    </>
    );
}