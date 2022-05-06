import React from 'react';
import clsx from 'clsx';
import ExpandMore from '@material-ui/icons/ExpandMore';
import SvgLoader from './../../../../../general-components/SvgLoader';
import userIcon from "./../../../../../app-resources/svgs/manager/user.svg";

const AppBarUserElement = props => {
    console.log("AppBarUserElement")
   return (
    <div className="flex flex-1 items-center justify-end userInfoSection">
        <span className="bg-indigo-800 border-0 h-10 overflow-hidden pt-1 rounded-full userIconContainer w-10">
            <SvgLoader customClasses="userIcon" src={userIcon} alt="User Icon"/>
        </span>
        <span className="flex flex-col ml-4 mr-8 userDetails">
            <span className="text-xl userName">
                Ad-Tech
            </span> 
            <span className="font-semibold text-gray-500 text-xs userRole">
                Super Admin
            </span>
        </span>
        <span className="border-2 border-gray-500 border-gray-700 border-solid dropDownIcon flex flex-col h-5 items-center justify-center p-1 rounded-full text-gray-700 w-5">
              <ExpandMore />
        </span>
    </div>
  )};
  export default AppBarUserElement;