import React, { Component } from 'react'
import ModalDialog from './../../../../../general-components/ModalDialog';
import AddApiForm from "./AddApiForm";

export default class AddApiModal extends Component {
   
    render() {
        return (
                <ModalDialog
                    open={this.props.open}
                    title={this.props.modalTitle}
                    handleClose ={this.props.handleModalClose}
                    component={<AddApiForm
                        isEdit={this.props.isEdit}
                        row={this.props.row}
                        handleModalClose={this.props.handleModalClose}
                        reloadData={this.props.reloadData}
                        handleModalClose={this.props.handleModalClose}
                        updateAfterSubmit = {this.props.updateAfterSubmit}
                    />}
                    maxWidth={"xs"}
                    fullWidth={true}
                    disable={true}
                    cancel={true}
                >
                </ModalDialog>
        )
    }
}
