import React, { Component } from 'react';
import Card from '@material-ui/core/Card';
import Typography from '@material-ui/core/Typography';
import Editingattribute from "./editingattribute";
import { withStyles } from "@material-ui/core/styles";
import LinearProgress from '@material-ui/core/LinearProgress';
import {styles}  from "../styles";
import { getProfiles,getCampaignsCall } from './apiCalls'
class BiddingRuleEdit extends Component {
    constructor(props){
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
               <div className="graphLoader bg-white absolute h-full overflow-hidden w-full top-0 left-0 z-10" style={this.state.isProcessing?{display:"block"}:{display:"none"}} >
                    <LinearProgress />
                    <div className="absolute flex font-bold font-mono h-full items-center justify-center overflow-hidden text-1rem text-sm w-full z-10">
                        Processing...
                    </div>
                </div>
                <Card  classes={{ root: classes.card }} className="biddingRule biddingRulEdit">
                
                    <Editingattribute
                    id={this.props.id}
                    handleProgressBar = {this.handleProgressBar}
                    handleModalClose={this.props.handleModalClose}
                    handleClose={this.props.handleClose}
                    />
                </Card>

            </>
        );
    }
}

export default withStyles(styles)(BiddingRuleEdit);