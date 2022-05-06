import React, { Component } from 'react';
import {Helmet} from "react-helmet";
import ComingSoon from './../../../app-resources/svgs/ComingSoon76.png'
import SvgLoader from "./../../../general-components/SvgLoader";
class AdminDashboard extends Component {
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

export default AdminDashboard;