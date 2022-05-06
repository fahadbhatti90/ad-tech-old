import React, {Component} from "react";
import Grid from "@material-ui/core/Grid";
import SvgLoader from "../SvgLoader";
import Tick from "./../../app-resources/svgs/Tick.svg";

class SuccessMessage extends Component {
  render() {
    return (
      <React.Fragment>
        <Grid item xs={12}>
        <div className="text-center">
            <div className="w-full">
              <SvgLoader src={Tick} height={"5rem"}/>
            </div>

            <h5 className="defaultModalHeading">{(
              <span>{this.props.render}</span>
            )}</h5>
            {this.props.messageSecondary === ""
                ?
                ''
                :
                <h5 className="defaultModalHeading">{this.props.messageSecondary}</h5>
            }

            <div style={{textAlign: 'center', marginBottom: '10px'}}>
              <label style={{
                textAlign: "center",
                fontSize: "13px"
              }}>{this.props.infoMsg}</label>
            </div>
          </div>
        </Grid>
      </React.Fragment>
    );
  }
}

export default SuccessMessage;
