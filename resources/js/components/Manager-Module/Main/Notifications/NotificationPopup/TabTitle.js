import React from 'react';
import Badge from '@material-ui/core/Badge';

export default function TabTitle(props) {
    return (
        <div className="tabTitle" >
            <Badge badgeContent={props.newMessages} color="default">{props.tabTitle}</Badge>
        </div>
    )
}
