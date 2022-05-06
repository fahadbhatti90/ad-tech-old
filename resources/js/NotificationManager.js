import {
    validateHost,
    getChannelName,
    getOnPageReloadChannelName,
    setUpUserTypeSpecificLiveNotification
} from './helper/helper';
/**
 * Notification code for login and logout
 */
let loginChannel = notifier.subscribe("hello-tec-login-status");
loginChannel.bind('SendClientLoginStatus', function(data) {

    if (!validateHost(data) || (data.status && htk.isUserLoggedIn())) return;

    let channelName = getChannelName(data.id, data.type);

    if(data.status) setUpUserTypeSpecificLiveNotification(channelName);
    else notifier.unsubscribe(channelName);
});
if(htk.isUserLoggedIn()){
    let channelName = getOnPageReloadChannelName(htk.getLocalStorageObjectDataById(htk.constants.LOGGED_IN_USER).id);
    setUpUserTypeSpecificLiveNotification(channelName);
}

// store.subscribe(()=>console.log(store.getState()))
