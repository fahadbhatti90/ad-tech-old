import {markAllAsRead} from "./apiCalls"

export const helperOnMarkAllAsReadBtnClick = (e, prevState, setState) => {
    
    let unseenLi = $(".notificationListItem.bg-gray-200");
    
    if(unseenLi.length <= 0) return false;
    let ids = [];
    $.each(unseenLi, function (indexInArray, valueOfElement) { 
         ids.push($(valueOfElement).attr("noti-id"))
    });
    markAllAsRead({ids},
    (response)=>{
        let selectedKey = prevState.selectedTabKey
        
        let data = prevState.notifications[selectedKey].data.map((noti, index) => {
            noti.status = 1;
            return noti;
        })
        setState({
            isMarkingAllRead:false,
            notifications:{
                ...prevState.notifications,
                [prevState.selectedTabKey]:{
                    unseen:0,
                    data
                },
                activeTabUnseenCount:0,
                totalUnseenCount:(prevState.notifications.totalUnseenCount - prevState.notifications[prevState.selectedTabKey].unseen)
            },
            showReadAllBtn:false,
        });
    },
    (error) => {
        console.log(error)
    })
}
export const shouldMarkAllBtnShow = (totalUnseenCount, buyboxUnseen, blacklistUnseen) => {
    if(totalUnseenCount <= 0) return false;

    switch (parseInt(htk.activeRole)) {
        case 2:
        case 3:
            return {
                activeUnSeenCount : buyboxUnseen,
                showMarkAll: (buyboxUnseen > 0),
            }
            break;
        default:
            return {
                activeUnSeenCount : blacklistUnseen,
                showMarkAll: (blacklistUnseen > 0),
            }
            break;
    }
}
export const shouldMarkAllBtnShowOnIndexChange = (index, {
    totalUnseenCount, buybox, blacklist, settings
}) => {
    if(totalUnseenCount <= 0) return false;

    switch (index) {
        case 0://buybox
            return htk.isSuperAdmin() ? (blacklist.unseen > 0) : buybox.unseen > 0;
            break;
        case 1://blacklist
            return htk.isSuperAdmin() ? (settings.unseen > 0) : blacklist.unseen > 0;
            break;
        default://settings
            return settings.unseen > 0
            break;
    }
}
export const handleDirectionAndActiveUnseenCount = (index,  {buybox, blacklist, settings})=>{
    switch (index) {
        case 0:
            return {
                direction:"ltr",
                activeUnseen: htk.isSuperAdmin() ? blacklist.unseen : buybox.unseen,
                selectedTabKey:htk.isSuperAdmin() ? "blacklist" : "buybox",
            };
            break;
        case 1:
            return {
                direction:"rtl",
                activeUnseen: htk.isSuperAdmin() ? settings.unseen : blacklist.unseen,
                selectedTabKey:htk.isSuperAdmin() ? "settings" : "blacklist",
            };
            break;
    
        default:
            return {
                direction:"rtl",
                activeUnseen: settings.unseen,
                selectedTabKey: "settings",
            };
            break;
    }
}

export const setActiveTabUnseenCount = (index, { buybox, blacklist,settings})=>{
    switch (index) {
        case 0:
            return (htk.isSuperAdmin() ? blacklist.unseen : buybox.unseen);
            break;
        case 1:
            return (htk.isSuperAdmin() ? settings.unseen : blacklist.unseen);
            break;
    
        default: 
            return settings.unseen;
            break;
    }
}
export const getTabsInfo = (state, notifications)=>{
    let tabsInfo = null;
    let tabsArray = [
        {
            tabTitle:"Buy Box", 
            key:"buybox",
            icon:"BB",
            data:state.buybox.data,
            unseenCount: (state.buybox.unseen)
        },
        {
            tabTitle:"Black List", 
            key:"blacklist",
            icon:"BL",
            data:state.blacklist.data,
            unseenCount: (state.blacklist.unseen)
        },
        {
            tabTitle:"Settings", 
            key:"settings",
            icon:"S",
            data:state.settings.data,
            unseenCount: (state.settings.unseen)
        }
    ];
    switch (parseInt(htk.activeRole)) {
        case 2:
            tabsInfo = [...tabsArray];
            break;
        case 3:
            tabsInfo = [tabsArray[0]];
            break;
        default:
            tabsInfo = [tabsArray[1], tabsArray[2]];
            break;
    }
    return tabsInfo;
}