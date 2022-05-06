import React, { Component } from 'react'
import './DeleteSchedule.scss';
import {connect} from "react-redux"
import {ShowSuccessMsg} from "./../../../../../general-components/successDailog/actions";
import {ShowFailureMsg} from "./../../../../../general-components/failureDailog/actions";
import TextButton from "./../../../../../general-components/TextButton";
import PrimaryButton from "./../../../../../general-components/PrimaryButton";
import LinearProgress from '@material-ui/core/LinearProgress';
import {DeleteScheduleApiCall} from './apiCalls';
class DeleteSchedule extends Component {
    constructor(props){
        super(props);
        this.state = {
            isProcessing:false
        }
    }
    handleDeleteScheduleSubmit = () => {
        this.setState({isProcessing:true})
        DeleteScheduleApiCall({
            id:this.props.id
        }, (response)=>{
            this.props.dispatch(ShowSuccessMsg("Successfull", 
            response.message, 
            true, 
            "",
            this.props.heloperReloadDataTable(response.tableData)));
        }, (error)=>{
            this.setState({isProcessing:false})
            this.props.dispatch(ShowFailureMsg(error, "", true, ""));
        });
    }
    render() {
        const {classes} = this.props;
        return (
            <>
            <div className="graphLoader bg-white absolute h-full overflow-hidden w-full top-0 left-0 z-10" style={this.state.isProcessing ? {display:"block"} : {display:"none"}} >
                <LinearProgress />
                <div className="absolute flex font-bold font-mono h-full items-center justify-center overflow-hidden text-1rem text-sm w-full z-10">
                    Processing...
                </div>
            </div>
            <div className="relative">
                <div className="flex float-right items-center justify-center w-full">
                    Do you really want to delete schedule?
                </div>
                <div className="flex float-right items-center justify-center my-5 mt-8 w-full">
                        <div className="mr-3">
                            <TextButton
                            BtnLabel={"Cancel"}
                            color="primary"
                            onClick={this.props.handleModalClose}/>
                        </div>
                        <PrimaryButton
                            btnlabel={"Confirm"}
                            variant={"contained"}
                            onClick={this.handleDeleteScheduleSubmit}
                        />     
                </div>
            </div>
            </>
        )
    }
}

export default connect(null)(DeleteSchedule)
