import React from 'react';
import clsx from 'clsx';
import ListItem from '@material-ui/core/ListItem';
import ListItemAvatar from '@material-ui/core/ListItemAvatar';
import ListItemText from '@material-ui/core/ListItemText';
import Avatar from '@material-ui/core/Avatar';
import SvgLoader from '../../../../general-components/SvgLoader';

const NotificaitonTitle = (props)=>(
    <div className="flex items-center justify-between NotificaitonTitle">
        <span className="text-sm text-black">
            {props.notificaitonTitle+" "}  
            <span className="time text-gray-500">
                 ( {props.notificaitonTime} )
            </span>
        </span> 
        {
            props.isNewMessage ?
            <>
                <div className={clsx("error newNotificationIcon smallBtn rounded-full", props.isMarkingAllRead ? "hidden" : "")}></div>
                <SvgLoader customClasses={clsx("markAllLoader", !props.isMarkingAllRead ? "hidden" : "")} src={"/images/NotiPreloader.gif"} alt="loader" height="12px"/>
            </>
            :null
        }
    </div>
);//
const NotificaitonDescription = (props)=>(
    <span className="inline-block font-semibold NotificationDescription m-0">
        {props.description}
    </span>
);
const Notificaiton = (props)=>(
        <ListItem noti-id = {props.notiId} onClick={props.onClick} className={clsx("notificationListItem hover:bg-gray-300", props.isNew ? "bg-gray-200" : "")}>
            <ListItemAvatar>
            <Avatar className="NotificationIcon pl-1">
                {props.iconText}
            </Avatar>
            </ListItemAvatar>
            <ListItemText
            primary={<NotificaitonTitle notificaitonTitle={props.Title} notificaitonTime={props.Time} isNewMessage={props.isNew} isMarkingAllRead={props.isMarkingAllRead} />}
            secondary={<NotificaitonDescription  description={props.description}/>}
            />
        </ListItem>
);

export default Notificaiton;