import React, { Component } from "react";
import FailureMessage from "./FailureMessage";
import { withStyles } from "@material-ui/core/styles";
import { styles } from "./styles";
import { Grid } from "@material-ui/core";
import Dialog from "@material-ui/core/Dialog";
import { connect } from "react-redux";
import {closeFailureMsg} from "./actions";
import PrimaryButton from "./../PrimaryButton";

class FailureDailog extends Component {
  constructor(props) {
    super(props);
    this.state = {
      showFailureMsg:0
    };
  }

static getDerivedStateFromProps(nextProps, prevState) {
    if (nextProps.showFailureMsg > prevState.showFailureMsg) {
        return ({message: nextProps.message,
        open: true });
    }
    return ({message: nextProps.message,
        open: nextProps.open});
    }

  handleCloseModal = () => {
    this.props.dispatch(closeFailureMsg());
    if (!!this.props.callback) this.props.callback();
  };

  render() {
    const { classes } = this.props;
    return (
      <Dialog
        open={this.props.open}
        onClose={this.handleCloseModal}
        fullWidth={false}
        maxWidth="xs"
        classes={{ paper: classes.modalCnt }}
      >
        <div>
          <Grid container justify="center">
            <FailureMessage
              render={this.props.message}
              infoMsg={this.props.infoMsg}
              htmlList={this.props.htmlList}
              messageSecondary={
                this.props.hasOwnProperty("secondaryMessage")
                  ? this.props.secondaryMessage
                  : ""
              }
            />
          </Grid>
          <Grid container justify="center">
            <PrimaryButton
              btnlabel={"OK"}
              variant="contained"
              type="submit"
              disableTouchRipple={true}
              color="primary"
              className={classes.defaultBtn}
              onClick={this.handleCloseModal}
            />
          </Grid>
        </div>
      </Dialog>
    );
  }
}

function mapStateToProps(state) {
  return {
    message: state.SHOW_FAILURE_MSG.UISettings.message,
    showFailureMsg: state.SHOW_FAILURE_MSG.UISettings.showFailureMsg,
    open:state.SHOW_FAILURE_MSG.UISettings.open,
    messageSecondary: state.SHOW_FAILURE_MSG.UISettings.secondaryMessage,
    infoMsg: state.SHOW_FAILURE_MSG.UISettings.infoMsg,
    htmlList: state.SHOW_FAILURE_MSG.UISettings.htmlList,
    callback: state.SHOW_FAILURE_MSG.UISettings.callback
  };
}

export default withStyles(styles)(
  connect(
    mapStateToProps,
  )(FailureDailog)
);
