import React, {Component, useState} from 'react';
import Card from '@material-ui/core/Card';
import Addingattribute from "./Addingattribute";
import {withStyles} from "@material-ui/core/styles";
import {styles} from "../styles";
import LinearProgress from '@material-ui/core/LinearProgress';
import "./../biddingRule.scss"


class BiddingRuleAdd extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isProcessing :false
        }
    }
    handleProgressBar = (isProcessing) =>{
        this.setState({
            isProcessing
        });
    }
    render() {
        const {classes} = this.props;
        return (
            <>
                <Card classes={{root: classes.card}} className="biddingRule relative">
                    <div className="graphLoader bg-white absolute h-full overflow-hidden w-full top-0 left-0 z-10" style={this.state.isProcessing?{display:"block"}:{display:"none"}} >
                        <LinearProgress />
                        <div className="absolute flex font-bold font-mono h-full items-center justify-center overflow-hidden text-1rem text-sm w-full z-10">
                            Processing...
                        </div>
                    </div>
                    <div> Bidding Rule</div>
                    <Addingattribute
                    handleProgressBar = {this.handleProgressBar}
                    handleModalClose={this.props.handleModalClose}
                    />
                </Card>
            </>
        );
    }
}

export default withStyles(styles)(BiddingRuleAdd);




