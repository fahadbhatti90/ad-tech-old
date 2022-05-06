import React, {Component} from 'react';
import Card from '@material-ui/core/Card';
import Typography from '@material-ui/core/Typography';
import {withStyles} from "@material-ui/core/styles";
import moment from 'moment';
import {connect} from "react-redux";
import {styles} from './styles';
import "./dayparting.scss"
import AddFormDayParting from './Add/AddFormDayParting';
import {Helmet} from "react-helmet";

import DayPartingDataTables from "./Datatables/DayPartingDataTables";


class DayParting extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isDataTableReload:false
        }
    }

    updateDataTable = () => {
        this.setState({
            isDataTableReload:false
        })
    }

    updateDataTableAfteSubmit = () => {
        this.setState({
            isDataTableReload:true
        })
    }
    render() {
        const {classes} = this.props;

        return (
            <>
                <Helmet>
                    <title>Ad-Tech Day Parting</title>
                </Helmet>
                <div className="dayPartingModule">
                    <Card classes={{root: classes.card}}>
                        <Typography variant="h6" className={`${classes.pageTitle} dayPartingHeading`} noWrap>
                            Day Parting Schedule
                        </Typography>
                        <AddFormDayParting
                            updateDataTableAfteSubmit={this.updateDataTableAfteSubmit}
                        />
                    </Card>
                    <div className={' mt-12'}></div>
                    <Card classes={{root: classes.tableCard}}>
                        <DayPartingDataTables
                            isDataTableReload={this.state.isDataTableReload}
                            updateDataTable={this.updateDataTable}
                        />
                    </Card>
                </div>
            </>
        );
    }
}

export default withStyles(styles)(connect(null)(DayParting));