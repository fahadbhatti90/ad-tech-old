import React, {Component} from "react";
import ModalDialog from "../../../../../general-components/ModalDialog";
import FormScApi from './FormScApi';

export default class AddBrandModal extends Component {

    render() {
        return (
                <ModalDialog
                    open={this.props.open}
                    title={this.props.modalTitle}
                    handleClose ={this.props.handleModalClose}
                    component={<FormScApi
                        isEdit={this.props.isEdit}
                        row={this.props.row}
                        handleModalClose={this.props.handleModalClose}
                        reloadData={this.props.reloadData}
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