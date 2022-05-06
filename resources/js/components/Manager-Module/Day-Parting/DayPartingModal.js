import React, {Component} from 'react';
import {withStyles} from '@material-ui/core/styles';
import Button from '@material-ui/core/Button';
import Dialog from '@material-ui/core/Dialog';
import MuiDialogTitle from '@material-ui/core/DialogTitle';
import MuiDialogContent from '@material-ui/core/DialogContent';
import MuiDialogActions from '@material-ui/core/DialogActions';
import IconButton from '@material-ui/core/IconButton';
import CloseIcon from '@material-ui/icons/Close';
import Typography from '@material-ui/core/Typography';
import PrimaryButton from "./../../../general-components/PrimaryButton";
import ModalDialog from "./../../../general-components/ModalDialog";
import {showLoader} from "./../../../general-components/loader/action";
import {connect} from 'react-redux';
const styles = (theme) => ({
    root: {
        margin: 0,
        padding: theme.spacing(2),
    },
    closeButton: {
        position: 'absolute',
        right: theme.spacing(1),
        top: theme.spacing(1),
        color: theme.palette.grey[500],
    },
});


const DialogContent = withStyles((theme) => ({
    root: {
        padding: theme.spacing(2),
    },
}))(MuiDialogContent);

const DialogActions = withStyles((theme) => ({
    root: {
        margin: 0,
        padding: theme.spacing(1),
    },
}))(MuiDialogActions);


export default class DayPartingDeleteModal extends Component {

    constructor(props){
        super(props)
    }

    render() {
        return (
            <div>
                <ModalDialog
                    open={this.props.openModal}
                    title={this.props.modalTitle}
                    id={this.props.id}
                    handleClose={this.props.handleClose}
                    component={this.props.modalBody}
                    maxWidth={this.props.maxWidth}
                    fullWidth={true}
                    cancelEvent={this.props.handleClose}
                    disable
                />
            </div>
        );
    }

}