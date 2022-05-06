import React, {Component} from "react";
import DeleteIcon from '@material-ui/icons/Delete';
import EditIcon from '@material-ui/icons/Edit';
import StopIcon from '@material-ui/icons/Stop';
import PlayArrowIcon from '@material-ui/icons/PlayArrow';
import {createMuiTheme, MuiThemeProvider} from '@material-ui/core/styles';
import "./../dayparting.scss";

const InputTheme = createMuiTheme({
    overrides: {
        MuiSvgIcon: {
            root: {
                fontSize: '1.1rem',
            }
        },
    }
});

export default class ActionBtns extends Component {

    onClickDelete = () => {
        let rowId = this.props.row.id;
        this.props.deleteSchedule(rowId);
    }

    onClickEdit = () => {
        let rowId = this.props.row.id;
        this.props.editSchedule(rowId);
    }

    onClickStop = () => {
        let rowId = this.props.row.id;
        this.props.stopSchedule(rowId);
    }

    onClickStart = () => {
        let rowId = this.props.row.id;
        this.props.startSchedule(rowId);
    }

    render() {
        return (
            <>
                <MuiThemeProvider theme={InputTheme}>
                    {
                        this.props.row.isScheduleExpired != 1 ?
                        <div>
                            {
                                this.props.row.stopScheduleDate == null ?
                                    <button className="btnHover" onClick={this.onClickStop}><StopIcon/></button>
                                    :
                                    <button className="btnHover" onClick={this.onClickStart}><PlayArrowIcon/></button>
                            }
                            <button className="btnHover" onClick={this.onClickDelete}><DeleteIcon/></button>
                            <button className="btnHover" onClick={this.onClickEdit}><EditIcon/></button>
                        </div>
                        : <div>
                            <button disabled={true}><DeleteIcon/></button>
                            <button disabled={true}><EditIcon/></button>
                        </div>
                    }
                </MuiThemeProvider>
            </>
        )
    }
}
