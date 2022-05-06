import React, {Component} from "react";
import Grid from "@material-ui/core/Grid";
import SvgLoader from "../SvgLoader";
import Cross from "./../../app-resources/svgs/Cross.svg";
import "./styles.scss";

class FailureMessage extends Component {
  render(props) {
    return (
      <React.Fragment>
        <Grid item xs={12}>
          <div className="text-center">
            <div className="w-full">
              <SvgLoader src={Cross} height={"5rem"}/>
            </div>
            {this.props.render != "" ?
                <h5 className="defaultModalHeading">{(
                    <span>{this.props.render}</span>
                )}</h5> : null
            }
            {this.props.messageSecondary != "" ?
                <h5 className="defaultModalHeading">{this.props.messageSecondary}</h5> : null
            }
            {this.props.htmlList != null ?
                <div className="parentDiv w-full overflow-y-auto" >
                  <ul>
                    {this.props.htmlList}
                  </ul>
                </div> : null
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

export default FailureMessage;
