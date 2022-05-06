import React from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';
import CheckCircleIcon from '@material-ui/icons/CheckCircle';
import ErrorIcon from '@material-ui/icons/Error';
import InfoIcon from '@material-ui/icons/Info';
import CloseIcon from '@material-ui/icons/Close';
import IconButton from '@material-ui/core/IconButton';
import Snackbar from '@material-ui/core/Snackbar';
import SnackbarContent from '@material-ui/core/SnackbarContent';
import WarningIcon from '@material-ui/icons/Warning';
import {connect} from "react-redux";
import {withStyles} from '@material-ui/core/styles';

const variantIcon = {
  success: CheckCircleIcon,
  warning: WarningIcon,
  error: ErrorIcon,
  info: InfoIcon,
};

const styles1 = theme => ({
  success: {
    backgroundColor: theme.palette.success.main,
  },
  error: {
    backgroundColor: theme.palette.error.main,
  },
  info: {
    backgroundColor: theme.palette.info.main,
  },
  warning: {
    backgroundColor: theme.palette.warning.main,
  },
  icon: {
    fontSize: 20,
  },
  iconVariant: {
    opacity: 0.9,
    marginRight: theme.spacing(2),
  },
  message: {
    display: 'flex',
    alignItems: 'center',
  },
  close: {
    borderRadius: '4px',
    backgroundColor: 'inherit'
  }
});

function MySnackbarContent(props) {
  const {classes, className, message, onClose, variant, ...other} = props;
  const Icon = variantIcon[variant];

  return (
    <SnackbarContent
      className={classNames(classes[variant], className)}
      aria-describedby="client-snackbar"
      message={
        <span id="client-snackbar" className={classes.message}>
          <Icon className={classNames(classes.icon, classes.iconVariant)}/>
          {message}
        </span>
      }
      action={[
        <IconButton
          key="close"
          aria-label="Close"
          color="inherit"
          className={classes.close}
          onClick={onClose}
        >
          <CloseIcon className={classes.icon}/>
        </IconButton>,
      ]}
      {...other}
    />
  );
}

MySnackbarContent.propTypes = {
  classes: PropTypes.object.isRequired,
  className: PropTypes.string,
  message: PropTypes.node,
  onClose: PropTypes.func,
  variant: PropTypes.oneOf(['success', 'warning', 'error', 'info']).isRequired,
};

const MySnackbarContentWrapper = withStyles(styles1)(MySnackbarContent);

class CustomizedSnackbars extends React.Component {
  state = {
    open: false,
    snakBarCount: 0
  };

  static getDerivedStateFromProps(nextProps, prevState) {
    
    if (nextProps.snakBarCount > prevState.snakBarCount) {
      return ({snakBarCount:prevState.snakBarCount+1,open: true });
    }
    return null;
  }

  handleClose = (event, reason) => {
    if (reason === 'clickaway') {
      return;
    }

    this.setState({open: false});
  };

  render() {
    return (
      <div>

        <Snackbar
          anchorOrigin={{
            vertical: 'bottom',
            horizontal: 'right',
          }}
          open={this.state.open}
          autoHideDuration={4000}
          onClose={this.handleClose}
        >
          <MySnackbarContentWrapper
            onClose={this.handleClose}
            variant={this.props.variant || "info"}
            message={this.props.message || "Ok"}
          />
        </Snackbar>
      </div>
    );
  }
}

function mapStateToProps(state) {
  return {
    snakBarCount: state.SHOW_SNACKBAR.snakBarCount,
    variant: state.SHOW_SNACKBAR.snakBarContent.variant,
    message: state.SHOW_SNACKBAR.snakBarContent.message
  };
}

export default (
  connect(
    mapStateToProps,
    null,null
  )(CustomizedSnackbars)
);