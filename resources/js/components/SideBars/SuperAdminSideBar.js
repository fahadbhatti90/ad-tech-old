import React, {useEffect}  from 'react';
import {sideBarStyles} from "./../../app-resources/theme-overrides/sideBarStyles";
import { superAdminLinks } from './../routes';
import { helperLinkHandler, helperDropDownHandler, getAllLinks, setLastActiveLinkOnPageLoad } from './SideBarHelpers';


export default function SuperAdminSideBar(props) {
    const classes = sideBarStyles();

    const [links, setLinks] = React.useState([]);
    const [dropDowns, setDropDowns] = React.useState([]);
    useEffect(() => {
        setLastActiveLinkOnPageLoad(setLinks, setDropDowns);
    }, []);
    const handleOnDropDownCollapse = (e) => {
        helperDropDownHandler(e, dropDowns, setDropDowns)
    }
    const handleOnLinkClick = (e)=>{ 
        helperLinkHandler(e, setLinks, dropDowns, setDropDowns)
    }
    return (
        <>
            {
                getAllLinks(
                    superAdminLinks, 
                    dropDowns, 
                    links, 
                    classes, 
                    handleOnDropDownCollapse, 
                    handleOnLinkClick
                )
            }
        </>
    );
}
