import React, { Component } from 'react';
import clsx from 'clsx';
import SwipeableViews from 'react-swipeable-views';
import {withStyles} from '@material-ui/core/styles';
import AppBar from '@material-ui/core/AppBar';
import Tabs from '@material-ui/core/Tabs';
import Tab from '@material-ui/core/Tab';
import List from '@material-ui/core/List';
import Notificaiton from './../Notification';
import NotificationLoader from './../NotificationLoader';
import TabPanel from './TabPanel';
import TabTitle from './TabTitle';
import {connect} from 'react-redux';
import Skeleton from '@material-ui/lab/Skeleton';
import {getAllNotifications} from './apiCalls';
import {SetNotificationCount} from './../../../../../general-components/Notification/actions'
import {
    getTabsInfo,
    setActiveTabUnseenCount,
    shouldMarkAllBtnShow,
    helperOnMarkAllAsReadBtnClick,
    shouldMarkAllBtnShowOnIndexChange,
    handleDirectionAndActiveUnseenCount
} from './NotificationPopupHelpers';
import {updateNotificationId} from './../NotificationPreview/action';
import {resetReduxNotificaitonsState} from './../../../../../general-components/Notification/actions';
import MarkAllAsReadButton from './MarkAllAsReadButton';

function a11yProps(index) {
  return {
    id: `full-width-tab-${index}`,
    'aria-controls': `full-width-tabpanel-${index}`,
  };
}

const useStyles =(theme) => ({
  root: {
    backgroundColor: theme.palette.background.paper,
    width: 400,
  },
});

function generate(element) {
    return [0, 1, 2, 3].map((value) =>
      React.cloneElement(element, {
        key: value,
      }),
    );
}
class NotificationPopup extends Component {
    constructor(props){
        super(props);
        this.state = {
            isStateSet:false,
            selectedTab:0,
            selectedTabKey: htk.isSuperAdmin() ? "blacklist" : "buybox",
            direction:"ltr",
            notifications:{
                blacklist:{
                    unseen:0,
                    data:[]
                },
                buybox:{
                    unseen:0,
                    data:[]
                },
                settings:{
                    unseen:0,
                    data:[]
                },
                activeTabUnseenCount:0,
                totalUnseenCount:0
            },
            isMarkingAllRead:false,
            showReadAllBtn:false
        };
        this.notificationPopupRef = React.createRef();
    }
    async componentDidMount(){
        if(!htk.isUserLoggedIn()){
            return;
        }
        let notificationUrl = "";
        switch (htk.activeRole) {
            case 1:
                notificationUrl = `${baseUrl}/superadmin/getNotifications`;
                break;
            case 2:
                notificationUrl = `${baseUrl}/getNotifications`;
                break;
        
            default:
                notificationUrl = `${baseUrl}/client/getNotifications`;
                break;
        }
        this.props.dispatch(resetReduxNotificaitonsState());
        getAllNotifications({
            url:notificationUrl,
            activeRole:htk.activeRole
        },
        (response) => {
            var buybox = response.BuyBoxNotifications;
            var blacklist = response.BlackListNotifications;
            var settings = response.SettingsNotifications;

            let buyboxUnseen = buybox.unseenCount || 0;
            let blacklistUnseen = blacklist.unseenCount || 0;
            let settingsUnseen = settings.unseenCount || 0;
            let totalUnseenCount = (buyboxUnseen + blacklistUnseen + settingsUnseen)
            let unseenInfo  = shouldMarkAllBtnShow(totalUnseenCount, buyboxUnseen, blacklistUnseen);

            this.setState({
                isStateSet:true,
                notifications:{
                    buybox:{
                        data:buybox.data || [],
                        unseen:buyboxUnseen,
                    },
                    blacklist:{
                        data:blacklist.data || [],
                        unseen:blacklistUnseen,
                    },
                    settings:{
                        data:settings.data || [],
                        unseen:settingsUnseen,
                    },
                    activeTabUnseenCount:unseenInfo.activeUnSeenCount,
                    totalUnseenCount,
                },
                showReadAllBtn: unseenInfo.showMarkAll
            });
            this.props.dispatch(SetNotificationCount(totalUnseenCount));
        },
        (error)=>{
        })
       
        document.addEventListener('click', this.handleClickOutside);
    }
    componentWillUnmount(){
        document.removeEventListener('click', this.handleClickOutside);
    }
    static getDerivedStateFromProps(nextProps, prevState) {
        
        if (nextProps.propsNotifications && nextProps.propsNotifications.key) {
            let stateNoti = prevState.notifications[nextProps.propsNotifications.key];
            let propNoti = nextProps.propsNotifications[nextProps.propsNotifications.key];
            const newStateOnPropsRecive = {
                notifications:{
                    ...prevState.notifications,
                    [nextProps.propsNotifications.key] : {
                        data: [...propNoti.data, ...stateNoti.data],
                        unseen: stateNoti.unseen + 1
                    },
                    totalUnseenCount: prevState.notifications.totalUnseenCount + 1,
                },
                showReadAllBtn: (nextProps.propsNotifications.index == $(".NotificationTabs button.Mui-selected").index() || prevState.showReadAllBtn)
            };
            
            nextProps.dispatch(resetReduxNotificaitonsState());
            nextProps.dispatch(SetNotificationCount(prevState.notifications.totalUnseenCount + 1));
            return (newStateOnPropsRecive);
        }
        return null;
    }
    handleClickOutside = (event) => {
        if (this.notificationPopupRef && !this.notificationPopupRef.current.contains(event.target) && !this.state.isMarkingAllRead) {
            this.props.setOpenNotificationPopup(false);
        }
    }
    handleChange = (event, index) => {
        this.handleIndexChange(index)
    }
    handleChangeIndex = (index) => {
        this.handleIndexChange(index)
    }
    handleIndexChange = (index) => {
        if(this.state.isMarkingAllRead) return;
        const {notifications} = this.state;
        var result = handleDirectionAndActiveUnseenCount(index, notifications);
        notifications.activeTabUnseenCount = result.activeUnseen; 
        this.setState({
            selectedTab:index,
            selectedTabKey:result.selectedTabKey,
            direction:result.direction,
            notifications:notifications,
            showReadAllBtn:shouldMarkAllBtnShowOnIndexChange(index, notifications)
        });
    }
    handleNotificationClick = (e) => {
        if(this.state.isMarkingAllRead) return;
        let notiId = $(e.target).attr("noti-id");
        notiId = typeof notiId == "undefined" ? $(e.target).parents("li").attr("noti-id") : notiId;
        this.props.setOpenNotificationPopup(false);
        this.props.dispatch(SetNotificationCount(this.state.notifications.totalUnseenCount >0 ? (this.state.notifications.totalUnseenCount - 1): this.state.notifications.totalUnseenCount));
        this.props.dispatch(updateNotificationId(parseInt(notiId)))
        htk.history.push("/notification/"+notiId);
    }
    handleOnMarkAllAsReadBtnClick = (e) => {
        this.setState({
            isMarkingAllRead: true
        },()=> {
            this.props.setIsMarkingAllRead(true)
            helperOnMarkAllAsReadBtnClick(e, this.state, this.helperSetState);
        })
    }
    helperSetState = (state) => {
        this.props.dispatch(SetNotificationCount(state.notifications.totalUnseenCount));
        this.setState(state,()=>{
            this.props.setIsMarkingAllRead(false)
        });
    }
    render(){
        const { classes, propsNotifications } = this.props;
        const {selectedTab, direction} = this.state;
        let ActiveUnseenCount = setActiveTabUnseenCount(selectedTab, this.state.notifications);
        let tabsInfo = getTabsInfo(this.state.notifications, propsNotifications);
        return (
        <div className={clsx(classes.root, "NotificationPopup themePopupsShadows absolute top-full right-0")} ref={this.notificationPopupRef}>
            <AppBar position="static" color="default" className="NotificationPopupHeader">
            <Tabs
                value={selectedTab}
                onChange={this.handleChange}
                indicatorColor="primary"
                textColor="primary"
                variant="fullWidth"
                aria-label="full width tabs example"
                className={clsx("NotificationTabs", htk.isManager() ? "managersNotification":"")}
            >
                {
                    tabsInfo.map((tabHeader, index)=>{
                        return <Tab key={index} label={<TabTitle tabTitle={tabHeader.tabTitle} newMessages={tabHeader.unseenCount} className={`tabsButton ${tabHeader.key}`}/>} {...a11yProps(index)} />
                    })
                }
            </Tabs>
            </AppBar>
            <div className={clsx("border-0 border-b border-gray-200 border-solid flex items-center justify-between markAllAsRead px-8 py-2 cursor-default", this.state.showReadAllBtn ? "" : "")}>
                {
                    this.state.isStateSet ?
                    <div className="text-gray-600 text-xs ">
                        Mark all as read
                    </div>
                    :
                    <Skeleton animation="wave"  height={20} width="35%"/>
                }
                {
                   
                    this.state.isStateSet ?
                    this.state.showReadAllBtn ?
                      <MarkAllAsReadButton 
                      isMarkingAllRead = {this.state.isMarkingAllRead}
                      onClickHandler = {this.handleOnMarkAllAsReadBtnClick}
                      />
                    : null
                    : <Skeleton animation="wave" variant="circle" width={10} height={10} />
                    
                }
            </div>
            <SwipeableViews
            axis={direction === 'rtl' ? 'x-reverse' : 'x'}
            index={selectedTab}
            onChangeIndex={this.handleChangeIndex}
            className="h-64"
            >
                { 
                    this.state.isStateSet && this.props.openNotificationPopup && tabsInfo ?
                    tabsInfo.map((tabPanel, tabPanelIndex)=>{
                        return  <TabPanel key={tabPanelIndex} value={selectedTab} index={tabPanelIndex} dir={"ltr"} className={`notificationTab ${tabPanel.key}`}>
                            {
                                tabPanel.data.length > 0 ? 
                                <List dense={false}>
                                    {
                                        tabPanel.data.map((notificaiton, index) =>
                                            <Notificaiton
                                            onClick={this.handleNotificationClick} 
                                            key ={index}
                                            iconText={tabPanel.icon}
                                            notiId={notificaiton.id}
                                            Title={notificaiton.title} 
                                            description={notificaiton.message}
                                            isMarkingAllRead={this.state.isMarkingAllRead}
                                            Time={notificaiton.created_at}
                                            isNew={!notificaiton.status}/>
                                        )
                                    }
                                </List>:
                                <div className="noNotiFoundText flex justify-center items-center text-gray-400">No Notification Found</div>

                            }
                        </TabPanel>
                    }) : <NotificationLoader />
                }
            </SwipeableViews>
            <div className="NotificationFooter border-0 border-gray-300 border-solid border-t flex items-center justify-center py-4 text-center text-gray-500 text-xs">
                {
                    this.state.isStateSet ?
                    ActiveUnseenCount + " New Notification"
                    :
                    <Skeleton animation="wave"  height={10} width="30%" />
                }
                </div>
        </div>
        );
    }
}

function mapStateToProps(state) {
    return { propsNotifications: state.NEW_NOTIFICATION.notifications };
} 
  
let NotiPopup = connect(mapStateToProps)(NotificationPopup);
export default withStyles(useStyles)(NotiPopup);
