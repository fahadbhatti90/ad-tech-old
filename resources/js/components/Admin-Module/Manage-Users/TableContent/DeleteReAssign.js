import React, { Component } from 'react'
import ModalDialog from './../../../../general-components/ModalDialog';
import ReAssignForm from './ReAssignForm';

export default class DeleteReAssign extends Component {
    constructor(props){
        super(props);
        this.state = {
             overflowIssue:false
        }
    }

    onMenuOpen=()=>{
        this.setState({
             overflowIssue:true
        })
    }
     
    onMenuClose=()=>{
         this.setState({
             overflowIssue:false
         })
     }

    render() {
        return (
            <div className="modelClass">
                <ModalDialog
                    overflowIssue={this.state.overflowIssue}
                    open={this.props.open}
                    title={"Reassign Brand and Delete User"}
                    handleClose ={this.props.handleModalClose}
                    component={
                        <ReAssignForm 
                            onMenuOpen = {this.onMenuOpen}
                            onMenuClose = {this.onMenuClose}
                            callback = {this.props.reAssignCallback}
                            handleClose ={this.props.handleModalClose}
                            brandsNames = {this.props.brandsNames}
                            rowId = {this.props.rowId}
                            reloadData={this.props.reloadData}
                            />
                    }
                    maxWidth={"sm"}
                    fullWidth={true}
                    disable={true}
                    >
                </ModalDialog>
            </div>
        )
    }
}
