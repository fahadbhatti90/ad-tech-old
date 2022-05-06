import React, { Component } from 'react';
import Card from "@material-ui/core/Card/Card";
import {styles} from '../styles'
import Typography from "@material-ui/core/Typography";
import AddVendorForm from '../../Vendor-Central/Vendor/Add/AddVendor';
import {withStyles} from "@material-ui/core";
import './../styles.scss';
import {Helmet} from "react-helmet";

class Vendor extends Component{
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
                            Add Vendor
                        </Typography>
                        <AddVendorForm/>
                    </Card>
                </div>
            </>
        );
    }
}

export default withStyles(styles)(Vendor);

