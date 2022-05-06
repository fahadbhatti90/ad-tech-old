import React,{Component} from "react";
import DeleteIcon from '@material-ui/icons/Delete';
import EditIcon from '@material-ui/icons/Edit';
import LockIcon from '@material-ui/icons/Lock';
import InfoIcon from '@material-ui/icons/Info';
import {createMuiTheme, MuiThemeProvider } from '@material-ui/core/styles';
import "../../../Admin-Module/Manage-Users/styles.scss";

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

    onClickEdit=()=>{
        let row= this.props.row;
        this.props.editAgency(row);
    }
   
    onClickLock=()=>{
        let rowId = this.props.row.id;
        this.props.changePassword(rowId);
    }

    render(){
        return(
            <>
            <MuiThemeProvider theme={InputTheme}>
                <button 
                    className="btnHover"
                    onClick={this.onClickEdit}
                    >
                    <EditIcon className="text-base"/>
                </button>

                <button 
                    className="btnHover"
                    onClick={this.onClickLock}
                    >
                    <LockIcon className="text-base"/>
                </button>

            </MuiThemeProvider>
        </>
        )
    }
}