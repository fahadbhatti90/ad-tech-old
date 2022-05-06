import React,{Component} from "react";
import DeleteIcon from '@material-ui/icons/Delete';
import EditIcon from '@material-ui/icons/Edit';
import {createMuiTheme, MuiThemeProvider } from '@material-ui/core/styles';
import "./biddingRuleDatatable.scss";

const InputTheme = createMuiTheme({
    overrides: {
        MuiSvgIcon: {
            root:{
                fontSize: '1.1rem',
            }
        },
    }
});

export default class ActionBtns extends Component{
    onClickDelete=()=>{
        let rowId = this.props.row.id;
        this.props.deleteSchedule(rowId);
    }

    onClickEdit=()=>{
        let rowId = this.props.row.id;
        this.props.editSchedule(rowId);
    }
   
    render(){
        return(
            <>
            <MuiThemeProvider theme={InputTheme}>
                <button 
                    className="btnHover"
                    onClick={this.onClickDelete}
                    >
                    <DeleteIcon />
                </button>
    
                <button 
                    className="btnHover"
                    onClick={this.onClickEdit}
                    >
                    <EditIcon />
                </button>
            </MuiThemeProvider>
        </>
        )
    }
}