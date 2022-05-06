import React from 'react';
import CheckBox from "./../../../../general-components/CheckBox";
import {Grid} from "@material-ui/core";

function MetCheckBoxes(props) {
    return (
        <div className="metricsDiv">
            {props.MetricsData.map((metricName, pIdx) => {
                return (
                    <>
                        <p className="text-center">{Object.keys(metricName)[0]}</p>
                        <Grid container spacing={2}>
                            {metricName[Object.keys(metricName)[0]].map((cb, idx) => {
                                return (
                                    <Grid item xs={6}>
                                        <CheckBox
                                            size="small"
                                            label={cb.metricName}
                                            checked={cb.isChecked}
                                            onChange={(e) => props.handleChange(e, Object.keys(metricName)[0], idx, pIdx)}
                                            name={cb.id.toString()}
                                        />
                                    </Grid>

                                )
                            })
                            }
                        </Grid>
                    </>
                )
            })}
        </div>
    );
}

export default MetCheckBoxes;