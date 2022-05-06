import React from 'react';
import GroupChildren from "./../MultiComCards/GroupChildren";
import Graph from './Graph';
import "./../MultiComCards/styles.scss";
import { Grid ,Card} from '@material-ui/core';
import {useStyles} from "./../cardStyles";
import CardHeader from "./../../../../general-components/cardHeader";
import ContainerLoader from "./../../../../general-components/ProgressLoader/ContainerLoader";

function GraphChart(props) {
    const classes = useStyles();
    return (
        <>
            <Card classes={{ root: classes.card }} className={`mt-6 relative ${props.customClass?props.customClass:""}`}>  
                {props.showLoader?
                    <ContainerLoader height={30}/>
                :""}   
                <CardHeader 
                    heading={props.heading}
                    subHeading={props.subHeading}
                />
                <Grid container>
                    <Grid item xs={1}>
                        <GroupChildren cardData={props.cardData} tooltip={props.tooltip}/>
                    </Grid>
                    <Grid item xs={11}>
                        <Graph 
                            visual={props.visual}
                            unload={props.unload}
                            axes={props.axes}
                            dataChart={props.dataChart}
                            types={props.types}
                            colors={props.colors}
                            yOneText={props.y1}
                            yTwoText={props.y2}
                            getY2Min={props.getPerformanceY2Min}
                            customClass={`.${props.customClass}`}
                        />
                    </Grid>
                </Grid>
            </Card>
           
        </>
    );
}

export default GraphChart;