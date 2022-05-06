import { combineReducers } from "redux";
import {loaderReducer} from "./general-components/loader/loaderReducer";
import {snackBarReducer} from "./general-components/snackBar/snackBarReducer";
import {showSuccessMsgReducer} from "./general-components/successDailog/successMsgReducer";
import {loginReducer} from "./components/Login/loginReducer";
import {SideBarReducer} from "./components/SideBars/redux/sideBarReducer";
import {parentBrandReducer} from "./components/Manager-Module/Main/GBS/GBSreducer";
import {notificationIdReducer} from "./components/Manager-Module/Main/Notifications/NotificationPreview/NotificationPreviewReducer";
import {showFailureMsgReducer} from "./general-components/failureDailog/failureMsgReducer";
import {HandleNewNotificaitonReducer} from "./general-components/Notification/notificaitonReducer";
import {PageHeaderReducer} from "./general-components/HeaderRedux/headerReducer";
const rootReducer = combineReducers({
    LOADING_SPINNER: loaderReducer,
    SHOW_SNACKBAR: snackBarReducer,
    SHOW_SUCCESS_MSG: showSuccessMsgReducer,
    IS_LOGGED_IN : loginReducer,
    SHOW_FAILURE_MSG: showFailureMsgReducer,
    ACTIVE_PARENT_BRAND: parentBrandReducer,
    NOTIFICATION_ID: notificationIdReducer,
    NEW_NOTIFICATION:HandleNewNotificaitonReducer,
    PAGE_HEADER:PageHeaderReducer,
    SIDE_BAR_STATUS:SideBarReducer,
});

export default rootReducer;
