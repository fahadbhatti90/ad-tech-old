import React, { Component } from "react";
import SuccessMessage from "./SuccessMessage";
import { withStyles } from "@material-ui/core/styles";
import { styles } from "./styles";
// import Button from "@material-ui/core/Button";
import { Grid } from "@material-ui/core";
import Dialog from "@material-ui/core/Dialog";
import { connect } from "react-redux";
import {closeSuccessMsg} from "./actions";
import PrimaryButton from "./../PrimaryButton";

class SuccessDailog extends Component {
  constructor(props) {
    super(props);
    this.state = {
      showSuccessMsg:0
    };
  }

static getDerivedStateFromProps(nextProps, prevState) {
    if (nextProps.showSuccessMsg > prevState.showSuccessMsg) {
        return ({message: nextProps.message,
        open: true });
    }
    return ({message: nextProps.message,
        open: nextProps.open});
    }

  handleCloseModal = () => {
    this.props.dispatch(closeSuccessMsg());
    if (!!this.props.callback) this.props.callback();
    // this.setState(
    //   {
    //     open: false
    //   },
    //   () => {
    //     if (!!this.props.callback) this.props.callback();
    //   }
    // );
  };

  render(props) {
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
            <SuccessMessage
              render={this.props.message}
              infoMsg={this.props.infoMsg}
              htmlList={this.props.htmlList}
              messageSecondary={
                this.props.hasOwnProperty("messageSecondary")
                    ? this.props.messageSecondary
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
    message: state.SHOW_SUCCESS_MSG.UISettings.message,
    showSuccessMsg: state.SHOW_SUCCESS_MSG.UISettings.showSuccessMsg,
    open:state.SHOW_SUCCESS_MSG.UISettings.open,
    messageSecondary: state.SHOW_SUCCESS_MSG.UISettings.secondaryMessage,
    infoMsg: state.SHOW_SUCCESS_MSG.UISettings.infoMsg,
    callback: state.SHOW_SUCCESS_MSG.UISettings.callback
  };
}

export default withStyles(styles)(
  connect(
    mapStateToProps,
  )(SuccessDailog)
);
