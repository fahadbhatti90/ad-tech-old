import React, { Component } from 'react';
import Card from "@material-ui/core/Card/Card";
import Typography from "@material-ui/core/Typography";
import ForecastUploadFile from "../Forecast/Upload/UploadFile"
import {withStyles} from "@material-ui/core";
import {styles} from "../styles";
import {Helmet} from "react-helmet";

class Forecast extends Component{
    constructor(props) {
        super(props);
    }
    render() {
        const {classes} = this.props;
        return (
            <>
                <Helmet>
                    <title>Ad-Tech VC</title>
                </Helmet>
                <div className="vendorCentral">
                    <Card classes={{root: classes.card}}>
                        <Typography variant="h6" className={`${classes.pageTitle}`} noWrap>
                            Upload Forecast CSV File
                        </Typography>
                        <ForecastUploadFile/>
                    </Card>
                </div>
            </>
        );
    }
}

export default withStyles(styles)(Forecast);