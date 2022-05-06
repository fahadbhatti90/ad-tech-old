import React from 'react';
import RadioGroup from '@material-ui/core/RadioGroup';
import Radio from '@material-ui/core/Radio';
import { FormControl } from '@material-ui/core';
import FormLabel from '@material-ui/core/FormLabel';
import FormControlLabel from '@material-ui/core/FormControlLabel';

export default function RadioBtnGroup(props) {
    return (
        <FormControl component="fieldset">
            <FormLabel component="legend">This will permanently remove these campaigns from the schedule!</FormLabel>
            <RadioGroup aria-label="gender" name="gender1" value={props.value} onChange={props.onRadioChange}>
                <FormControlLabel value="1" control={<Radio />} label="Run today's schedule, then pause" />
                <FormControlLabel value="2" control={<Radio />} label="Pause Campaign immediately" />
                <FormControlLabel value="3" control={<Radio />} label="Campaigns enabled permanently" />
            </RadioGroup>
        </FormControl>
    );
}


