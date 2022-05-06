import React,{Component} from "react";
import DeleteIcon from '@material-ui/icons/Delete';
import {createMuiTheme, MuiThemeProvider } from '@material-ui/core/styles';
import "./BuyBox.scss";

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
        this.props.deleteHelperCall(rowId);
    }
    render(){
        return(
            <>
            <MuiThemeProvider theme={InputTheme}>
                <button 
                    className="btnHover deleteScheduleButton"
                    onClick={this.onClickDelete}
                    >
                    <DeleteIcon className="text-base"/>
                </button>
            </MuiThemeProvider>
        </>
        )
    }
}