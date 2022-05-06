import React, { Component } from 'react'
import { withStyles } from '@material-ui/core/styles';
import {primaryColor, primaryColorLight} from "../app-resources/theme-overrides/global";
import Radio from '@material-ui/core/Radio';

 const ThemeRadioButtons = withStyles({
    root: {
      color: primaryColor,
      '&$checked': {
        color: primaryColor,
      },
    },
    checked: {},
  })((props) => <Radio {...props} size={"small"}/>);    

  export default ThemeRadioButtons;