import React from 'react';
import clsx from 'clsx';
import ExpandMore from '@material-ui/icons/ExpandMore';

const AppBarDateTime = props =>{
    return (
    <div className="appBarDateTime flex items-center mb-2 mt-2 text-xs">
        <span className="block border border-solid border-gray-500 h-0 line mr-4 w-12"></span>
        <span className="font-medium infoLabel mr-1 text-gray-500">Show:</span>
        <span className="date font-medium mr-3">Today, 21 April 2020</span>
        <span className="border-2 border-gray-500 border-gray-700 border-solid dropDownIcon flex flex-col h-5 items-center justify-center p-1 rounded-full text-gray-700 w-5">
              <ExpandMore />
        </span>
    </div>
  )};

  export default AppBarDateTime;