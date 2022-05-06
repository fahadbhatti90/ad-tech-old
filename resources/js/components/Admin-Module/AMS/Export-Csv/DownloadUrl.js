import React from 'react';
import SvgLoader from "./../../../../general-components/SvgLoader";
import Cross from "./../../../../app-resources/svgs/Cross.svg";
import Tick from "./../../../../app-resources/svgs/Tick.svg";
import PrimaryButton from "./../../../../general-components/PrimaryButton";
import Divider from '@material-ui/core/Divider';
import {Link} from "react-router-dom";

const DownloadUrlDailog = (props) => {
    const handleClick = () => {
        window.open(props.url,"_blank");
        props.handleModalClose();
      }
    return (
        <>
            <div className="text-center">
                {props.status?
                    <div className="w-full">
                    <SvgLoader src={Tick} height={"5rem"}/>
                    </div>:
                    <div className="w-full">
                    <SvgLoader src={Cross} height={"5rem"}/>
                    </div>            
                }
                <h2>{props.title}</h2>
                <h5 className="defaultModalHeading">{props.message}</h5>
                {props.status?
                    <>
                    <Divider className="mBottom"/>
                    <div onClick={handleClick} className="text-blue-700 hover:underline cursor-pointer">Click Here</div>
                    </>
                :""}
            </div>
            {props.status?
                "":<div className="text-center">
                    <PrimaryButton
                        btnlabel={"Ok"}
                        variant={"contained"}
                        onClick={props.handleModalClose}
                    />   
                </div>}
        </>
    );
};

export default DownloadUrlDailog;