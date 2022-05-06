import React from 'react';
import GroupChildren from './GroupChildren';
import Typography from '@material-ui/core/Typography';
import "./styles.scss";
import { Grid, Card } from '@material-ui/core';
import Divider from '@material-ui/core/Divider';
import RefreshIcon from "@material-ui/icons/Refresh";
import {useStyles} from "./../cardStyles";
import ContainerLoader from "./../../../../general-components/ProgressLoader/ContainerLoader";
import CardHeader from "./../../../../general-components/cardHeader";

export default function ComCardMulti(props) {
    const classes = useStyles();
    return (
            <Card classes={{ root: classes.card }} className="mt-1 relative">
                 {props.dataType==props.showLoader?
                    <ContainerLoader height={30}/>
                :""} 
                <CardHeader 
                    heading={props.heading}
                    subHeading={props.subHeading}
                    reloadApiCall={props.reloadApiCall}
                    name={props.dataType}
                />  
                <Grid container>
                    <Grid item xs={6}><GroupChildren tooltip={false} commaSep={true} cardData={props.cardDataLeft}/></Grid>
                    <Grid item xs={6}><GroupChildren tooltip={false} commaSep={true} cardData={props.cardDataRight}/></Grid>
                </Grid>
            </Card>);
    }