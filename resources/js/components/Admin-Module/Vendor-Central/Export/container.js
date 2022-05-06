import React, { Component } from 'react';
import Card from "@material-ui/core/Card/Card";
import Typography from "@material-ui/core/Typography";
import ExportData from "../Export/Export/Export"
import {withStyles} from "@material-ui/core";
import {styles} from "../styles";
import {Helmet} from "react-helmet";

class Export extends Component{
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
                    <Card classes={{root: classes.card}} className="relative">
                        <Typography variant="h6" className={`${classes.pageTitle}`} noWrap>
                            Export Data
                        </Typography>
                        <ExportData/>
                    </Card>
                </div>
            </>
        );
    }
}

export default withStyles(styles)(Export);