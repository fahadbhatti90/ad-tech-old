import React from 'react'
import clsx from 'clsx';
import List from '@material-ui/core/List';
import ListItem from '@material-ui/core/ListItem';
import ListItemIcon from '@material-ui/core/ListItemIcon';
import ListItemText from '@material-ui/core/ListItemText';
import Collapse from '@material-ui/core/Collapse';
import ExpandLess from '@material-ui/icons/ExpandLess';
import ExpandMore from '@material-ui/icons/ExpandMore';
import { Link } from "react-router-dom";
import SvgLoader from './../../general-components/SvgLoader';

export function NormalLink(props) {
    return (
        <Link to={props.link.to} className="no-underline text-white sideBarLink" linkkey = {props.link.linkNo} onClick={props.handleOnLinkClick}>
            <ListItem button key={props.link.linkNo} className={clsx("listItem", props.links[props.link.linkNo] ? "active" : "")}>
                <ListItemIcon>
                    <SvgLoader customClasses="sideBarIcon" src={props.link.icon}/>
                </ListItemIcon>
                <ListItemText primary={props.link.text} />
            </ListItem>
        </Link>
    )
}
export function DropDown(props) {
    return (
        <>
            <ListItem button onClick={props.handleOnDropDownCollapse} collapsekey={props.link.dropDownIndex} className="listItem dropDownItem">
                <ListItemIcon>
                    <SvgLoader customClasses="sideBarIcon" src={props.link.icon}/>
                </ListItemIcon>
                <ListItemText primary={props.link.text}  className="themeNormalFontFamily"/>
                {props.dropDowns[props.link.dropDownIndex] ? <ExpandLess /> : <ExpandMore />}
            </ListItem>
            <Collapse in={props.dropDowns[props.link.dropDownIndex]} collapsekey={props.link.dropDownIndex} timeout="auto" className="orignalDropDown">
                <List component="div" disablePadding>
                    {
                        props.link.dropDown.map((sublink, sublinkIndex)=>{
                            return <Link to={sublink.to} key={sublinkIndex} className="no-underline text-white sideBarLink" linkkey = {sublink.linkNo} onClick={props.handleOnLinkClick}>
                                <ListItem button className={clsx(props.classes.nested, "listItem", props.links[sublink.linkNo] ? "active" : "")} >
                                    <ListItemIcon className="iconContainer">
                                        {sublink.hasIcon ? <SvgLoader customClasses="sideBarIcon" src={sublink.icon}/>:null}
                                    </ListItemIcon>
                                    <ListItemText primary={sublink.text}  className="themeNormalFontFamily"/>
                                </ListItem>
                            </Link>
                        })
                    }
                </List>
            </Collapse>
        </>
    )
}
