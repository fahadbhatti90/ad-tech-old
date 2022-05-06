import React, { Component } from 'react';
import SingleComCard from "./../Advertising-Visuals/SingleComCards/SingleComCard";
import { Grid } from '@material-ui/core';
import {commaSeparator} from "./../../../helper/helper";
import ContainerLoader from "./../../../general-components/ProgressLoader/ContainerLoader";

class AsinComCards extends Component {
    render() {
        let {scoreCards} = this.props;
        return (
            <div className="pt-5 relative">
                {this.props.showComcardsLoader?
                            <ContainerLoader 
                                height={30} 
                                classStyles={"mt-1"}/>
                            :
                            ""}
                <Grid container justify="center" spacing={2}>
                    <Grid  item xs={12}>
                        <Grid container justify="center" spacing={2}>
                            <Grid item xs={6} sm={4} md={2} lg={true}>
                                <SingleComCard 
                                    toolTip={true} title={"Impressions"} prefix={""} 
                                    tooltipTitle={scoreCards.length > 0?scoreCards[0].Impressions : "0"}
                                    value={scoreCards.length > 0?commaSeparator(+scoreCards[0].Impressions) : "0"}/>
                            </Grid>
                            <Grid item xs={6} sm={4} md={2} lg={true}>
                                <SingleComCard 
                                    toolTip={true} title="Clicks" prefix={""} 
                                    tooltipTitle={scoreCards.length > 0?scoreCards[0].Clicks : "0"} 
                                    value={scoreCards.length > 0?commaSeparator(+scoreCards[0].Clicks) : "0"}/>
                            </Grid>
                            <Grid item xs={6} sm={4} md={2} lg={true}>
                                <SingleComCard 
                                    toolTip={true} title="CTR" prefix={"%"} 
                                    tooltipTitle={scoreCards.length > 0?scoreCards[0].CTR : "0"}
                                    value={scoreCards.length > 0?scoreCards[0].CTR : "0"}/>
                            </Grid>
                            <Grid item xs={6} sm={4} md={2} lg={true}>
                                <SingleComCard 
                                    toolTip={true} title="CPC" prefix={"$"} 
                                    tooltipTitle={scoreCards.length > 0?scoreCards[0].CPC : "0"}
                                    value={scoreCards.length > 0?commaSeparator(+scoreCards[0].CPC) : "0"}/>
                            </Grid>
                            <Grid item xs={6} sm={4} md={2} lg={true}>
                                <SingleComCard 
                                    toolTip={true} title="Conversions" prefix={""} 
                                    tooltipTitle={scoreCards.length > 0?scoreCards[0].order_conversion : "0"}
                                    value={scoreCards.length > 0?commaSeparator(+scoreCards[0].order_conversion) : "0"}/>
                            </Grid>
                        </Grid>
                    </Grid>
                   <Grid item xs={12}>
                       <Grid container justify="center" spacing={2}>
                            <Grid item xs={6} sm={4} md={2} lg={true}>
                                <SingleComCard 
                                    toolTip={true} title="CPA" prefix={""} 
                                    tooltipTitle={scoreCards.length > 0?scoreCards[0].CPA : "0"}
                                    value={scoreCards.length > 0?scoreCards[0].CPA : "0"}/>
                            </Grid>
                            <Grid item xs={6} sm={4} md={2} lg={true}>
                                <SingleComCard 
                                    toolTip={true} title="Spend" prefix={"$"} 
                                    tooltipTitle={scoreCards.length > 0?scoreCards[0].Cost : "0"}
                                    value={scoreCards.length > 0?commaSeparator(+scoreCards[0].Cost) : "0"}/>
                            </Grid>
                            <Grid item xs={6} sm={4} md={2} lg={true}>
                                <SingleComCard 
                                    toolTip={true} title="Sales" prefix={"$"} 
                                    tooltipTitle={scoreCards.length > 0?scoreCards[0].Revenue : "0"}
                                    value={scoreCards.length > 0?commaSeparator(+scoreCards[0].Revenue) : "0"}/>
                            </Grid>
                            <Grid item xs={6} sm={4} md={2} lg={true}>
                                <SingleComCard 
                                    toolTip={true} title="ACOS" prefix={"%"} 
                                    tooltipTitle={scoreCards.length > 0?scoreCards[0].ACOS : "0"}
                                    value={scoreCards.length > 0?scoreCards[0].ACOS : "0"}/>
                            </Grid>
                            <Grid item xs={6} sm={4} md={2} lg={true}>
                                <SingleComCard 
                                    toolTip={true} title="ROAS" prefix={"$"} 
                                    tooltipTitle={scoreCards.length > 0?scoreCards[0].ROAS : "0"}
                                    value={scoreCards.length > 0?commaSeparator(+scoreCards[0].ROAS) : "0"}/>
                            </Grid>
                        </Grid> 
                   </Grid>  
                </Grid>
        </div>
        );
    }
}

export default AsinComCards;