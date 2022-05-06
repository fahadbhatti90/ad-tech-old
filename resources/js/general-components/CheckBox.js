import React from 'react';
import Checkbox from '@material-ui/core/Checkbox';
import { FormControl } from '@material-ui/core';
import FormControlLabel from '@material-ui/core/FormControlLabel';
import {primaryColor,primaryColorLight} from "../app-resources/theme-overrides/global"

export default function CheckBox(props) {
    return (
        <FormControl component="fieldset">
            <FormControlLabel
            control={
                <Checkbox
                  color="primary"
                  inputProps={{ 'aria-label': 'secondary checkbox' }}
                  {...props}
              />}
            label={props.label}
          />    
      </FormControl>
    );
}