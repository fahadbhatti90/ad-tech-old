import React, { Component } from 'react'
import ModalDialog from './../../../general-components/ModalDialog';

export default class AccountsModal extends Component {
    render() {
        return (
            <div className="modelClass">
                <ModalDialog
                    overflowIssue={this.props.overflowIssue}
                    open={this.props.open}
                    title={this.props.modalTitle}
                    handleClose ={this.props.handleModalClose}
                    component={this.props.modalComponent}
                    maxWidth={"xs"}
                    fullWidth={true}
                    disable={true}
                    modelClass={this.props.modalTitle == "Associate Account"? "addModel":"unAssociateModel"}
                    cancel={true}
                    >
                </ModalDialog>
            </div>
        )
    }
}
