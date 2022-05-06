import React, { Component } from 'react';
import { withStyles } from "@material-ui/core/styles";
import {FormControl, Grid} from "@material-ui/core";
import FormLabel from "@material-ui/core/FormLabel";
import RadioGroup from "@material-ui/core/RadioGroup/RadioGroup";
import FormControlLabel from "@material-ui/core/FormControlLabel/FormControlLabel";
import Radio from "@material-ui/core/Radio/Radio";
import {primaryColor} from "./../../../../app-resources/theme-overrides/global";
import {styles} from "../styles";
import Button from "@material-ui/core/Button";
import {deleteDayPartingSchedule} from "../apiCalls";
import {ShowSuccessMsg} from "./../../../../general-components/successDailog/actions";
import {connect} from "react-redux";
import TextButton from "./../../../../general-components/TextButton";
import PrimaryButton from "../../../../general-components/PrimaryButton";

const GreenRadio = withStyles({
    root: {
        color: primaryColor,
        '&$checked': {
            color: primaryColor,
        },
    },
    checked: {},
})((props) => <Radio color="default" {...props} />);

class DeleteEditScheduleOptions extends Component {
    constructor(props) {
        super(props);
        this.state = {
            campaignOptionSelected:"1"
        }
    }

    onRadioChange = (e) => {
        let value = e.target.value;
        this.setState({
            campaignOptionSelected: e.target.value
        }, () => {
            this.props.onRadioChange(value)
        })
    }
    render() {
        const {classes} = this.props;

        return (
            <div>
                <div className="dayPartingModule pl-5 pr-5 rounded-lg">
                    <form>
                        <p>This will permanently remove these campaigns from the schedule!</p>
                        <Grid container justify="center" spacing={3}>
                            <Grid className="dayPartingGridElement" item xs={12} sm={12} md={12} lg={12}>
                                <FormControl component="fieldset" fullWidth={true} className="bg-white">
                                    <FormLabel component="legend"></FormLabel>
                                    <RadioGroup aria-label="gender" name="status" value={this.state.campaignOptionSelected} onChange={this.onRadioChange}>
                                        <FormControlLabel value="1" control={<GreenRadio />} label="Run today's schedule, then pause" />
                                        <FormControlLabel value="2" control={<GreenRadio />} label="Pause Campaign immediately" />
                                        <FormControlLabel value="3" control={<GreenRadio />} label="Campaigns enabled permanently" />
                                    </RadioGroup>
                                </FormControl>

                            </Grid>
                            <div className="flex float-right items-center justify-center my-5 w-full">
                                <div className="mr-3">
                                    <PrimaryButton
                                        btnlabel={"Yes, Proceed"}
                                        variant={"contained"}
                                        customclasses={"ml-1 rounded"}
                                        onClick={this.props.removeCampaignsAfterConfirmation}
                                    />
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

export default withStyles(styles)(connect(null)(DeleteEditScheduleOptions));
