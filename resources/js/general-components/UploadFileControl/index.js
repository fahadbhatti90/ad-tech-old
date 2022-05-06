import React, { Component } from 'react'
import clsx from 'clsx';
import {primaryColor, primaryColorLight} from "./../../app-resources/theme-overrides/global";
import TextFieldInput from "./../Textfield";
import {withStyles} from "@material-ui/core/styles";
import './UploadFileControl.scss';
import CircularProgress from '@material-ui/core/CircularProgress';
import {isValidFileType} from './../../helper/yupHelpers';
const useStyles = theme => ({
    root: {
        '& .MuiInputBase-root': {
            marginTop: 0,
            borderRadius: 20,
            border: "1px solid #c3bdbd8c",
            height: 35,
            cursor:"pointer",
            background: '#fff0'
        },
        "&:hover .MuiInputBase-root": {
            borderColor: primaryColorLight,
            borderRadius: "20px",
            cursor:"pointer",
        },
        '& .MuiInputBase-input': {
            margin: props => props.margin || 15,
            fontSize: '0.72rem',
            cursor:"pointer",
            padding: '7px 0 7px'
        }
    },
    focused: {
        border: "2px solid !important",
        borderColor: `${primaryColor} !important`,
    }
});
class UploadFileControl extends Component {
    constructor(props){
        super(props);
        this.state = {
            fileName: "",
            showProgress: false,
            value: null,
        }//end state
    }
    handleUploadLabelOverrideFileInputChage = (e) => {
        let files = $(e.target).get(0).files[0];
        this.inputElement.value = null;
        if(!isValidFileType(files))
        {
            this.props.setUploadedFile(null, "Unsupported File Format");
            this.setState({
                showProgress: false,
            })
            return;
        }
        let fileName = files.name.length > 50 ? files.name.substr(0, 35) + "..." : files.name;
        this.setState({
            fileName: fileName,
            value: null,
        }, ()=>{
            this.setState({
                showProgress: false,
            })
            this.props.setUploadedFile(files, "");
        })
    }
    handleInputClick = (e) => {
        this.inputElement.click();
        document.body.onfocus = this.handleOnBlur;
        this.setState({
            showProgress: true,
        })
    }
    handleOnBlur = (e) => {
        document.body.onfocus = null;
        this.setState({
            showProgress: false,
        })
    }
    render() {
        const {classes} = this.props;
        return (
            <div>
                <div className={clsx("relative cursor-pointer ThemeInput",this.props.className ? this.props.className : "")} onClick={this.handleInputClick}>
                    <TextFieldInput
                        placeholder="Choose File"
                        type="text"
                        fullWidth={true}
                        value={this.state.fileName}
                        classesstyle = {classes}
                        disabled
                    />
                    <button className="absolute browseFileButton text-gray-800 text-xs cursor-pointer"
                    style={
                        this.state.showProgress ? {paddingRight: "5px"} : {paddingRight: "15px"}
                    }
                    >
                        Browse            
                        { this.state.showProgress ? 
                        <CircularProgress/> : null}
                    </button>
                </div>
                <input 
                    ref={input => this.inputElement = input} 
                    type="file" 
                    accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" 
                    className="hidden" 
                    onChange={this.handleUploadLabelOverrideFileInputChage}
                />
            </div>
        )
    }
}

export default withStyles(useStyles)(UploadFileControl)