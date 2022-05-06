import React, { Component } from 'react'
import ModalDialog from '../../../../general-components/ModalDialog';
import AddBrandForm from "./AddBrandForm";

class AddBrandModal extends Component {
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
                    title={this.props.modalTitle}
                    handleClose ={this.props.handleModalClose}
                    component={<AddBrandForm 
                        onMenuOpen = {this.onMenuOpen}
                        onMenuClose = {this.onMenuClose}
                        isEdit={this.props.isEdit}
                        row={this.props.row}
                        brandOptions={this.props.brandOptions}
                        handleModalClose={this.props.handleModalClose}
                        reloadData={this.props.reloadData}
                    />}
                    maxWidth={"xs"}
                    fullWidth={true}
                    disable={true}
                    cancel={true}
                    >
                </ModalDialog>
            </div>
        )
    }
}
export default AddBrandModal;