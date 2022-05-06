import React, { Component } from 'react';
import Card from "@material-ui/core/Card/Card";
import Typography from "@material-ui/core/Typography";
import VerifyRecordData from "../VerifyRecord/Verify/Verify";
import {withStyles} from "@material-ui/core";
import {styles} from "../styles";
import {Helmet} from "react-helmet";

class VerifyRecord extends Component{
    constructor(props) {
        super(props);
    }
    render() {
        const {classes} = this.props;
        return (
            <>
                <Helmet>
                    <title>Ad-Tech | VC</title>
                </Helmet>
                <div className="vendorCentral">
                        <VerifyRecordData/>
                </div>
            </>
        );
    }
}

export default withStyles(styles)(VerifyRecord);