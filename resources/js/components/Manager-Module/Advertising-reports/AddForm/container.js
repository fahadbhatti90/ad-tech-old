import React, { Component }  from 'react';
import ModalDialog from './../../../../general-components/ModalDialog';
import AddForm from "./AddForm";
import EditForm from "./../EditForm/EditForm";

class FormContent extends Component {
    render() {
        return (

            <ModalDialog
                open={this.props.open}
                title={this.props.modalTitle}
                component={this.props.modalBody}
                handleClose={this.props.handleModalClose}
                cancelEvent={this.props.handleModalClose}
                smallDialog={true}
                maxWidth={this.props.maxWidth}
                fullWidth={true}
                callback={this.props.callback}
                disable={this.props.hidePopUpBtn}
            >
            </ModalDialog>
        );
    }
}

export default FormContent;