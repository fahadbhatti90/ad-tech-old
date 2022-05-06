import React, { Component } from 'react';
import {Helmet} from "react-helmet";
import ComingSoon from './../../../app-resources/svgs/ComingSoon76.png'
import Card from '@material-ui/core/Card';
import SvgLoader from "./../../../general-components/SvgLoader";
class SuperAdminDashboard extends Component {
    render() {
        return (
            <>
            <Helmet>
                <title>Ad-Tech | Dashboard</title>
            </Helmet> 
            <div className="flex justify-center items-center h-full py-32">
                <SvgLoader customClasses="sideBarIcon" src={ComingSoon} height="auto"/>
             </div>
            </>    
        );
    }
}

export default SuperAdminDashboard;