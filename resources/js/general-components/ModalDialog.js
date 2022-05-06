import React from 'react';
import clsx from 'clsx';
import { withStyles,makeStyles } from '@material-ui/core/styles';
import Button from '@material-ui/core/Button';
import Dialog from '@material-ui/core/Dialog';
import MuiDialogTitle from '@material-ui/core/DialogTitle';
import MuiDialogContent from '@material-ui/core/DialogContent';
import MuiDialogActions from '@material-ui/core/DialogActions';
import IconButton from '@material-ui/core/IconButton';
import CloseIcon from '@material-ui/icons/Close';
import Typography from '@material-ui/core/Typography';
import PrimaryButton from "./PrimaryButton";
import TextButton from "./TextButton";
import "./styles.scss";

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

const smallDailog = makeStyles(() => ({
  paper: { minWidth: "800px" },
}));

const DialogTitle = withStyles(styles)((props) => {
    const {children, classes, onClose, ...other} = props;
    return (
        <MuiDialogTitle disableTypography className={classes.root} {...other}>
            <Typography className="dailog-header">{children}</Typography>
            {onClose ? (
                <IconButton aria-label="close" className={classes.closeButton} onClick={onClose}>
                    <CloseIcon/>
                </IconButton>
            ) : null}
        </MuiDialogTitle>
    );
});

const DialogContent = withStyles((theme) => ({
    root: {
        padding: theme.spacing(2),
    },
}))(MuiDialogContent);

const DialogActions = withStyles((theme) => ({
    root: {
        margin: 0,
        justifyContent: 'center',
        padding: theme.spacing(1),
    },
}))(MuiDialogActions);

export default function CustomizedDialogs(props) {
  const classes = smallDailog();
  return (
    <div>
      <Dialog 
        PaperProps={props.overflowIssue ? { style: {overflowY: 'visible'} } : {}}
        classes= {props.smallDailog ? { paper: classes.paper }:null}
        onClose={props.handleClose} 
        aria-labelledby="customized-dialog-title" 
        open={props.open}
        fullWidth={props.fullWidth}
        className={props.modelClass}
        maxWidth={props.maxWidth}>
        {props.title==""?"":
        <DialogTitle id="customized-dialog-title" onClose={props.handleClose}>
          {props.title}
        </DialogTitle>
        }
        <DialogContent dividers style={{ overflowY: 'visible' }}>
         {props.component}
        </DialogContent>
      
        {!props.disable?        
            <DialogActions>      
              {
              !props.cancel?         
              <TextButton
                BtnLabel={"Cancel"}
                color="primary"
                onClick={props.cancelEvent}/>
                :""}

              <PrimaryButton
                btnlabel={"Confirm"}
                variant={"contained"}
                onClick={props.callback}/>            
            </DialogActions>:
            ""}
      </Dialog>
    </div>
  );
}