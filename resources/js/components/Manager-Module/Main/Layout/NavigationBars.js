import React, { PureComponent } from 'react'
import TopNavigationBar from './TopNavigationBar'
import {connect} from "react-redux";

class NavigationBars extends PureComponent {
    componentDidMount(){
    }
    render() {
        return (
            <>
               {    
                // this.props.isUserLoggedIn || htk.isUserLoggedIn() ? 
                    <>
                        <TopNavigationBar reloadSideBar = {this.props.isAdmin}/>
                    </>
                    // : null/
                }
            </>
        )
    }
}



const mapStateToProps = state => {
    return {
        isAdmin : state.SIDE_BAR_STATUS.isAdmin 
    }
  }
export default connect(mapStateToProps)(NavigationBars);
