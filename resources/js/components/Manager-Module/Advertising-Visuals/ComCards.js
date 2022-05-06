import React, { Component } from 'react';
import { Grid } from '@material-ui/core';
import ComCardMulti from "./MultiComCards/container";

class ComCards extends Component {
    render() {
        return (
            <div className="pt-5">
                <Grid container justify="center" spacing={1}>
                    <Grid item xs={12} sm={6} md={6} lg={4}>
                         {/* Month Over Month Com Card */}
                        <ComCardMulti 
                            dataType={1}
                            heading={"Month Over Month"}
                            subHeading = {"​(mtd vs last mtd)"}
                            cardDataLeft={this.props.mtdDataLeft}
                            cardDataRight={this.props.mtdDataRight}
                            showLoader={this.props.showMOMLoader}
                            reloadApiCall={this.props.reloadData}
                        />      
                    </Grid>

                    <Grid item xs={12} sm={6} md={6} lg={4}>
                         {/* WEEK OVER WEEK​ Com Card */}
                        <ComCardMulti   
                            dataType={2}
                            heading='Week Over Week​'
                            subHeading = {"​(last 7 days vs previous)"}
                            cardDataLeft={this.props.wowDataLeft}
                            cardDataRight={this.props.wowDataRight}
                            showLoader={this.props.showWOWLoader}
                            reloadApiCall={this.props.reloadData}
                        />      
                    </Grid>

                    <Grid item xs={12} sm={6} md={6} lg={4}>
                         {/* DAY Over DAY Com Card */}
                        <ComCardMulti 
                            dataType={3}
                            heading={"Day Over Day"}
                            subHeading = {"​(yesterday vs prior day)"}
                            cardDataLeft={this.props.dodDataLeft}
                            cardDataRight={this.props.dodDataRight}
                            showLoader={this.props.showDODLoader}
                            reloadApiCall={this.props.reloadData}
                        />      
                    </Grid>
                    
                    <Grid item xs={12} sm={6} md={6} lg={4}>
                         {/* YTD Com Card */}
                        <ComCardMulti   
                            dataType={4}
                            heading={"YTD​​"}
                            subHeading = {""}
                            cardDataLeft={this.props.ytdDataLeft}
                            cardDataRight={this.props.ytdDataRight}
                            showLoader={this.props.showYTDLoader}
                            reloadApiCall={this.props.reloadData}
                        />      
                    </Grid>

                    <Grid item xs={12} sm={6} md={6} lg={4}>
                         {/* WTD Com Card */}
                        <ComCardMulti 
                            dataType={5}
                            heading={"WTD"}
                            subHeading = {"​"}
                            cardDataLeft={this.props.wtdDataLeft}
                            cardDataRight={this.props.wtdDataRight}
                            showLoader={this.props.showWTDLoader}
                            reloadApiCall={this.props.reloadData}
                        />      
                    </Grid>
                </Grid>
            </div>
        );
    }
}

export default ComCards;