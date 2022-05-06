import React from 'react'
import Typography from '@material-ui/core/Typography';
import Skeleton from '@material-ui/lab/Skeleton';

export default function ScheduleTimeLoader() {
    return (
        <div className="ml-4 mt-5 scheduleTimeLoader w-32">
            <Typography variant="body1">
                <Skeleton height={35}/>
            </Typography>
        </div>
    )
}
