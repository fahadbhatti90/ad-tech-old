import React, { Component } from 'react';
import Card from "@material-ui/core/Card/Card";
import Typography from "@material-ui/core/Typography";
import PurchaseOrderUploadFile from "../PurchaseOrder/Upload/UploadFile";
import {withStyles} from "@material-ui/core";
import {styles} from "../styles";
import {Helmet} from "react-helmet";

class PurchaseOrder extends Component{
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
                            Upload Purchase Order CSV Files
                        </Typography>
                        <PurchaseOrderUploadFile/>
                    </Card>
                </div>
            </>
        );
    }
}

export default withStyles(styles)(PurchaseOrder);