import React,{Component} from "react";
import DeleteIcon from '@material-ui/icons/Delete';
import EditIcon from '@material-ui/icons/Edit';
import LockIcon from '@material-ui/icons/Lock';
import InfoIcon from '@material-ui/icons/Info';
import {createMuiTheme, MuiThemeProvider } from '@material-ui/core/styles';
import "./styles.scss";

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
        this.props.deleteUserCall(rowId);
    }
    render(){
        return(
            <>
            <MuiThemeProvider theme={InputTheme}>
                <button 
                    className="btnHover unAssociateAccountButton"
                    onClick={this.onClickDelete}
                    >
                    <DeleteIcon className="text-base"/>
                </button>
            </MuiThemeProvider>
        </>
        )
    }
}