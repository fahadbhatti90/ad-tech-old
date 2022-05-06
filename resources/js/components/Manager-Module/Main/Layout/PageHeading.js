import React from 'react';
import Typography from '@material-ui/core/Typography';
import {connect} from "react-redux";

function PageHeading(props) {
    return (
            <Typography variant="h6" className="PageTitle pt-3 themeBoldFontFamily"  noWrap>
                {props.pageHeader}
            </Typography>
        )
}

const mapStateToProps = state => {
    return {
      pageHeader : state.PAGE_HEADER.pageHeader
    }
  }
export default connect(mapStateToProps)(PageHeading);