import React from 'react';
import "./styles.scss";

function timepicker(props) {
    return (
        <div>
            <TimePicker
                className="timepicker"
                name="startTime"
                fullWidth={true}
                InputProps={{
                    disableUnderline: true,
                    }}
                value={props.time}
                onChange={props.handleTimeChange} />
        </div>
    );
}

export default timepicker;