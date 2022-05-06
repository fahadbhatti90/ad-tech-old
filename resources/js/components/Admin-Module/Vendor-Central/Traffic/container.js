import React, { Component } from 'react';
import Card from "@material-ui/core/Card/Card";
import Typography from "@material-ui/core/Typography";
import TrafficUploadFile from "../Traffic/Upload/UploadFile"
import {withStyles} from "@material-ui/core";
import {styles} from "../styles";
import {Helmet} from "react-helmet";

class Traffic extends Component{
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
                            Upload Traffic CSV File
                        </Typography>
                        <TrafficUploadFile/>
                    </Card>
                </div>
            </>
        );
    }
}

export default withStyles(styles)(Traffic);