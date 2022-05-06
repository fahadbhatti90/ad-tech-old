import React from 'react';
import {ACTIVE_LINK} from "./../../config/localStorageKeys";
import { NormalLink, DropDown } from './../routes/Links';
export const setLastActiveLinkOnPageLoad = (setLinks, setDropDowns) => {
    if(htk.history){
        let sideBarLinks = $(".sideBarDesktop .sideBarLink");
        let tempArray = getResetArray(sideBarLinks.length)
        let tempDropDownArray = getResetArray($(".sideBarDesktop .dropDownItem").length)
        sideBarLinks.map((index, url)=>{
            if($(url).attr("href") == "#"+(htk.history.location.pathname)) {
                tempArray[$(url).attr("linkkey")] = true;
                if($(url).parents(".orignalDropDown")){
                    tempDropDownArray[$(url).parents(".orignalDropDown").attr("collapsekey")] = true;
                    setDropDowns(tempDropDownArray);
                }
                setLinks(tempArray);
            }
        })
    }
    
}
export const helperLinkHandler = (e, setLinks, dropDowns, setDropDowns) => {
    helperCloseAllDropDownsIfLinkClickedWasNotFromDrowDown(e, dropDowns, setDropDowns);
    let tempArray = getResetArray($(".sideBarDesktop .sideBarLink").length)
    let linkIndex = helperGetLinkIndex(e, "linkkey", ".sideBarLink");
    tempArray[linkIndex] = true;
    setLinks(tempArray);
}
export const helperDropDownHandler =  (e, dropDowns, setDropDowns) => {
    let dropDownIndex = helperGetLinkIndex(e, "collapsekey", ".dropDownItem")
    let tempArray = getResetArray($(".sideBarDesktop .dropDownItem").length)
    tempArray[dropDownIndex] = !(dropDowns.indexOf(true) == dropDownIndex);
    setDropDowns(tempArray);
}
export const getAllLinks = (sideBarLinks, dropDowns, links, classes, handleOnDropDownCollapse, handleOnLinkClick) => {
    return sideBarLinks.map((link, index)=>{
        if(link.isDropDown){
            return <DropDown 
                key={index}
                handleOnDropDownCollapse = {handleOnDropDownCollapse}
                handleOnLinkClick = {handleOnLinkClick}
                link={link}
                links={links}
                dropDowns={dropDowns}
                classes = {classes}
            />
        }
        return <NormalLink  
            key={index}
            handleOnLinkClick = {handleOnLinkClick}
            link={link}
            links={links}
        />
    })
}
export const getResetArray = (total) => {
    let tempArray = [];
    for (let index = 0; index < total; index++) { tempArray[index] = false; }
    return tempArray; 
}
export const helperGetLinkIndex = (e, key, parent) => {
    let linkIndex = $(e.target).attr(key);
    return typeof linkIndex == "undefined" ? $(e.target).parents(parent).attr(key) : linkIndex;
}
export const helperCloseAllDropDownsIfLinkClickedWasNotFromDrowDown = (e, dropDowns, setDropDowns) => {
    if(dropDowns.indexOf(true) != -1 && $(e.target).parents(".orignalDropDown").length <= 0) 
    setDropDowns(getResetArray($(".sideBarDesktop .dropDownItem").length));
}