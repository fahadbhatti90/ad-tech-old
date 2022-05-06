import React from 'react';
import {connect} from "react-redux";
// import SearchIcon from "../../../app-resources/svgs/manager/Search.svg";
import SearchIcon from '@material-ui/icons/Search';
import SvgLoader from './../../../../general-components/SvgLoader';
import NotificationPopup from './NotificationPopup/NotificationPopup';
import Badge from '@material-ui/core/Badge';
import NotificationsNoneIcon from '@material-ui/icons/NotificationsNone';

function AppBarScearchElement (props) {
  const [isMarkingAllRead, setIsMarkingAllRead] = React.useState(false);
  const handleNotificationIconClick = (event) => {
    if(isMarkingAllRead) return;
    props.setOpenNotificationPopup(!props.openNotificationPopup);
  };
  return (
  <div className="hidden lg:flex items-end mainControls w-1/2 items-center">
      <div className="flex items-center justify-end min-w-full searchAndNoti">
          {/* <div className="bg-white flex inputGroup mr-4 px-3 py-2 rounded-full w-64 shadow">
              <input type="text" className="border-0 flex-1 focus:outline-none font-semibold outline-none px-2 text-right text-xs" placeholder="Search" />
              <SearchIcon className=" text-gray-300"/>
              {/* <SvgLoader customClasses="searchIcon" src={SearchIcon} alt="Search Icon"/> */}
          {/* </div> */} 
          {/* <span className="bg-gray-500 block border border-solid border-gray-500 h-0 line mr-4 text-gray-900 w-10"></span> */}
          <div className="relative cursor-pointer z-10">
            <div onClick={handleNotificationIconClick}>
              <Badge color="error" badgeContent={props.totalNewNotification}  anchorOrigin={{
                vertical: 'top',
                horizontal: 'left',
              }}>
                <NotificationsNoneIcon  className="notificationBell"/>
              </Badge>
            </div>
              { props.openNotificationPopup ? <NotificationPopup 
                openNotificationPopup = {props.openNotificationPopup}
                setOpenNotificationPopup = {props.setOpenNotificationPopup}
                setIsMarkingAllRead = {setIsMarkingAllRead}
              />:null
              }
          </div>
      </div>
  </div>
)};

const mapStateToProps = state => {
  return {
    totalNewNotification : state.NEW_NOTIFICATION.totalNewNotification 
  }
}
export default connect(mapStateToProps)(AppBarScearchElement);