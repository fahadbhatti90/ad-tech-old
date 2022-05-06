import React, { Component } from 'react'
import ModalDialog from './../../../../general-components/ModalDialog';

export default class AddAsinsModel extends Component {
   
    render() {
        return (
            <div className="modelClass">
                <ModalDialog
                    open={this.props.open}
                    title={this.props.modalTitle}
                    handleClose ={this.props.handleModalClose}
                    component={this.props.modalComponent}
                    maxWidth={"xs"}
                    modelClass="AddSearchRankScheduleModel"
                    fullWidth={true}
                    disable={true}
                    cancel={true}
                    >
                </ModalDialog>
            </div>
        )
    }
}
