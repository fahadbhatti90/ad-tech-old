import React, { Component } from 'react';
import {Grid} from "@material-ui/core";
import {withStyles } from "@material-ui/core/styles";
import {styles} from "../styles";
import TextButton from "./../../../../general-components/TextButton";
import PrimaryButton from "./../../../../general-components/PrimaryButton";
import {connect} from "react-redux";
import {startDayPartingSchedule} from "../apiCalls";
import {ShowSuccessMsg} from "../../../../general-components/successDailog/actions";
class StartSchedule extends Component {
    constructor(props) {
        super(props);
    }

    startScheduleDone = () => {
        console.log('this.props', this.props)
            let params = {
                'scheduleId': this.props.id
            }
        startDayPartingSchedule(params, (response) => {
                if (response.status != false) {
                    this.props.handleModalClose();
                    this.props.getAllSchedulesFromDb();
                    this.props.dispatch(ShowSuccessMsg(response.message, "Successfully", true, "" ));
                }
            });
            return;
    }
    render() {
        const {classes} = this.props;
        return (
            <div>
                <div className="dayPartingModule pl-5 pr-5 rounded-lg">
                    <form>
                        <p>Do you really want to Start this schedule?</p>
                        <Grid container justify="center" spacing={3}>
                            <div className="flex float-right items-center justify-center my-5 w-full">
                                <div className="mr-3">
                                    <PrimaryButton
                                        btnlabel={"Yes, Proceed"}
                                        variant={"contained"}
                                        customclasses={"ml-1 rounded"}
                                        onClick={this.startScheduleDone}/>
                                </div>
                                <TextButton
                                    BtnLabel={"Cancel"}
                                    color="default"
                                    onClick={this.props.handleModalClose}/>
                            </div>
                        </Grid>
                    </form>
                </div>
            </div>
        )
    }
}
export default withStyles(styles)(connect(null)(StartSchedule));