import React from 'react';
import FormControlLabel from '@material-ui/core/FormControlLabel';
import Switch from '@material-ui/core/Switch';
import { withStyles } from '@material-ui/core/styles';


const CustomSwitch = withStyles((theme) => ({
    root: {
      width: 30,
      height: 18,
      padding: 0,
      display: 'flex',
    },
    switchBase: {
      padding: 1,
      color: theme.palette.grey[500],
      '&$checked': {
        transform: 'translateX(12px)',
        color: theme.palette.common.white,
        '& + $track': {
          opacity: 1,
          backgroundColor: theme.palette.primary.lightest,
          borderColor: theme.palette.primary.main,
        },
      },
    },
    thumb: {
      width: 16,
      height: 16,
      color: theme.palette.primary.main,
      backgroundColor: theme.palette.primary.main,
      boxShadow: 'none',
    },
    track: {
      border: `1px solid ${theme.palette.grey[500]}`,
      borderRadius: 18 / 2,
      opacity: 1,
      backgroundColor: theme.palette.common.white,
    },
    checked: {},
  }))(Switch);
export default function Switcher(props) {
    return(
        <FormControlLabel
            control={<CustomSwitch checked={props.switchValue} onChange={props.handleChange} name={props.switchName} />}
            label={props.switchLabel}
      />
    );
}