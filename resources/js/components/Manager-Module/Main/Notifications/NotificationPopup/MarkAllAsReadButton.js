import React from 'react';
import clsx from 'clsx';
import SvgLoader from './../../../../../general-components/SvgLoader';

export default function MarkAllAsReadButton(props) {
    return (
        <>
            <div className={clsx("error markAllButton largeBtn rounded-full cursor-pointer", props.isMarkingAllRead ? "hidden" : "")} onClick={props.onClickHandler}></div>
            <SvgLoader customClasses={clsx("markAllLoader", !props.isMarkingAllRead ? "hidden" : "")} src={"/images/NotiPreloader.gif"} alt="loader" height="14px"/>
        </>
    )
}
