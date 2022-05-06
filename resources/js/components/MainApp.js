import React, { useEffect } from 'react'
import Layout from './Manager-Module/Main/Layout';
import {SetPageHeader} from './../general-components/HeaderRedux/actions';
import {IS_LOGGED_IN_STATUS} from "./../config/localStorageKeys";

export const MainApp = (component, props, headerName, thisObj) => {
    let isLoggedIn = localStorage.getItem(IS_LOGGED_IN_STATUS);
    if(isLoggedIn != "logged"){
        thisObj.hideSideBar(true);
        props.history.replace("/login");
    }
    if(htk.activeRole == 3 && (props.history.location.pathname=="/admin" || props.history.location.pathname=="/superAdmin")){
        props.history.replace("/");
    } else if(htk.activeRole == 2 && (props.history.location.pathname=="/" || props.history.location.pathname=="/superAdmin")){
        props.history.replace("/admin");
    } else if(htk.activeRole == 1 && (props.history.location.pathname=="/" || props.history.location.pathname=="/admin")){
        props.history.replace("/superAdmin");   
    }
    
    if(thisObj.state.hideSideBar)
    thisObj.hideSideBar(false);
    
    window.htk.history = props.history;

    thisObj.props.dispatch(SetPageHeader(headerName));
    return (
        <React.Fragment>
            <Layout 
                mainComponent={component}
                headerName={headerName}
                history={props.history}
            />
        </React.Fragment>
    );
};
