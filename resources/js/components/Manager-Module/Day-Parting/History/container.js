import React, {Component} from 'react';
import Card from '@material-ui/core/Card';
import Typography from '@material-ui/core/Typography';
import {withStyles} from "@material-ui/core/styles";
import {styles} from '../styles';
import FullCalendar from '@fullcalendar/react'
import dayGridPlugin from '@fullcalendar/daygrid'
import {getProfiles, getHistorySchedule} from "../apiCalls";
import {breakProfileId} from "./../../../../helper/helper";
import Tooltip from "@material-ui/core/Tooltip/Tooltip";
import SingleSelect from "./../../../../general-components/Select";
import {Grid} from "@material-ui/core";
import "../dayparting.scss"
import {hideLoader, showLoader} from "./../../../../general-components/loader/action";
import {connect} from "react-redux";
const defaultProfile = {
    'value' : '0|12345',
    'lable' : 'default'
}
class DayPartingHistory extends Component {
    constructor(props) {
        super(props);
        this.state = {
            data: [],
            selectedProfile: '',
            profileOptions: [],
        }
    }

    componentDidMount = () => {
        this.props.dispatch(showLoader());
        getProfiles((profileOptions) => {
            //success
            this.setState({
                profileOptions
            }, () => {
                let profileCount = profileOptions.length
                if (profileCount > 0) {
                    let doPreSelectProfile = profileOptions[profileCount - 1];
                    this.setState({
                        selectedProfile: {
                            value: doPreSelectProfile.value,
                            label: doPreSelectProfile.label
                        }
                    })
                    this.getAllHistorySchedules(doPreSelectProfile);
                }
            })
        }, (err) => {
            //error
        });
        this.props.dispatch(hideLoader());
    }

    getAllHistorySchedules = (profileId) => {
        if (profileId) {
            let pf = profileId.value;
            getHistorySchedule(pf, (response) => {
                //success
                this.setState({data: response});
            }, (err) => {
                //error
                // this.props.dispatch(showSnackBar());
            });
        }
    }
    onProfileChange = (value) => {
        this.props.dispatch(showLoader());
       // if (value){
            this.setState({
                selectedProfile: value
            }, () => {
                this.getAllHistorySchedules(value);
            })
        // }else{
        //     this.setState({
        //         selectedProfile: value
        //     }, () => {
        //         this.getAllHistorySchedules(defaultProfile);
        //     })
        // }
        this.props.dispatch(hideLoader());
    }

    renderEventContent = eventInfo => {
        return (
            <>
                <Tooltip title={eventInfo.event.extendedProps.description} placement="top" arrow>
                    <span>{eventInfo.event.extendedProps.description.slice(0, 15) + "..."}</span>
                </Tooltip>
            </>
        )
    }

    render() {
        const {classes} = this.props;
        return (
            <>
                <div className="dayPartingHistory">
                    <Grid className="dayPartingGridElement flex items-center" item xs={12} sm={5} md={5} lg={5}>
                        <label className="float-left ml-2 mr-4 mt-1 text-sm">
                            Child Brand
                        </label>
                        <div className="w-9/12">
                            <SingleSelect
                                placeholder=""
                                id="cbn"
                                name="selectedProfile"
                                className="bg-white historyProfile"
                                value={this.state.selectedProfile}
                                onChangeHandler={this.onProfileChange}
                                fullWidth={true}
                                Options={this.state.profileOptions}
                                isClearable={false}
                                menuPlacement="auto"
                                maxMenuHeight={190}
                            />
                        </div>
                    </Grid>
                    <Card className="historyElement" classes={{root: classes.card}}>
                        <Typography variant="h6" className={`${classes.pageTitle} dayPartingHeading`} noWrap>
                            Day Parting History
                        </Typography>
                        <FullCalendar
                            plugins={[dayGridPlugin]}
                            initialView="dayGridMonth"
                            headerToolbar={{
                                right: 'prevYear,prev,next,nextYear today',
                                center: 'title',
                                left: ''
                            }}
                            selectable={true}
                            dayMaxEvents={true}
                            events={this.state.data}
                            eventContent={this.renderEventContent}
                        />
                    </Card>
                </div>
            </>
        );
    }
}

export default withStyles(styles)(connect(null)(DayPartingHistory));