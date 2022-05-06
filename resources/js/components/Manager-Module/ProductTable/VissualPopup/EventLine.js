import React, {useEffect} from 'react'
import clsx from 'clsx';
export default function EventLine({width, eventNumber, eventId, eventBg, positonFromTop, positionFromLeft, eventGradient,showGradient, delay}) {
    const [isShowAnimation, setShowAnimation] = React.useState(false);
    useEffect(() => {
        setTimeout(() => {
            setShowAnimation(true);
        }, delay);
    }, [])
    return (
            <div className={clsx("event event"+eventId+" eventPosition"+eventNumber+"", !isShowAnimation ? "left100" : "")} style={{width:width=="0px" ? "10px":width,background:eventBg,top:positonFromTop+"px",left:positionFromLeft+"px"}}>
                <div className="eventGradientContainer">
                    <div className="eventGradient" style={showGradient?{backgroundImage:eventGradient,display:"block"}:{backgroundImage:eventGradient}}></div>
                </div>
            </div>
        )
}
