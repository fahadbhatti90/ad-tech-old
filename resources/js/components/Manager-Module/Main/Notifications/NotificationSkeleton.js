import React from 'react';
import ListItem from '@material-ui/core/ListItem';
import ListItemAvatar from '@material-ui/core/ListItemAvatar';
import ListItemText from '@material-ui/core/ListItemText';
import Skeleton from '@material-ui/lab/Skeleton';

const NotificaitonTitle = (props)=>(
    <div className="flex items-center justify-between NotificaitonTitle pr-5">
         <Skeleton animation="wave" height={12} width="40%" style={{ marginBottom: 6 }} />
         <Skeleton animation="wave" variant="circle" width={9} height={9}  style={{ marginRight: "-1px" }}/>
    </div>
);//
const NotificaitonDescription = (props)=>{
    return (
        <>
            <Skeleton animation="wave"  height={10} width="80%" style={{ marginBottom: 2 }} />
            <Skeleton animation="wave"  height={10} width="50%" />
        </>
    );
};
const NotificaitonSkeleton = (props)=>(
        <ListItem  style={{ paddingTop: 5, paddingBottom: 5 }} >
            <ListItemAvatar>
            <Skeleton animation="wave" variant="circle" width={40} height={40} />
           
            </ListItemAvatar>
            <ListItemText
                primary={<NotificaitonTitle isNewMessage={props.isNew}/>}
                secondary={<NotificaitonDescription />}
            />  
        </ListItem>
);

export default NotificaitonSkeleton;