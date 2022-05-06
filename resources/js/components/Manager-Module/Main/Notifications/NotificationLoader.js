import React from 'react';
import NotificationSkeleton from './NotificationSkeleton';

export default function NotificationLoader() {
    return (
        <div>
            <NotificationSkeleton />
            <NotificationSkeleton />
            <NotificationSkeleton />
            <NotificationSkeleton />
        </div>
    )
}
