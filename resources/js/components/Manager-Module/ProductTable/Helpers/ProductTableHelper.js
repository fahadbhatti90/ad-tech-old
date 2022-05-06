import React from 'react';

import { 
    getTableColumns,
 } from './PtColumnsHelper';
export const getNewData = (originalData, rowId, tagId, moduleType, type = 0) => {
    originalData.map((data)=> {
        if(data["Sr.#"] == rowId){
            let tag = data.tag;
            for (let index = 0; index < tag.length; index++) {
                const element = tag[index];
                if(element.fkTagId == tagId) {
                    if(moduleType == 1)
                        tag.splice(index, 1);
                    else if(parseInt(type) == element.type)
                        tag.splice(index,1);
                }
            }
            data.tag = tag;
        }
    })
    return originalData;
}
export const getFilterColumnNames = (currentPage, prePage, classes, itemsToShow, handleSelectAllCheckBoxClick, handleCheckBoxClick)=>{
    return getTableColumns(currentPage, prePage, classes, itemsToShow, handleSelectAllCheckBoxClick, handleCheckBoxClick).map(column=>column.selector);
}

export const handleSelectedCheckboxesStateChange = ({selectedArray}, handleIfAllRowsSelected) => {
    let RowTitle = $(".taggedDataTable .RowTitle");
    let intId = setInterval(() => {
        if(RowTitle.length > 0){
            $.each(RowTitle, (indexInArray, valueOfElement) => { 
                const parentTr = $(valueOfElement).parents(".rdt_TableRow"); 
                let asin = $(valueOfElement).attr("asin");
                if(selectedArray.includes(asin))
                    $(parentTr).addClass("activeTr");
                else
                    $(parentTr).removeClass("activeTr");
               
            });
            handleIfAllRowsSelected();
            clearInterval(intId);
        }
        RowTitle = $(".taggedDataTable .RowTitle");
    }, 1);
   
}
export const manageAllRowSelection = (isAllSelected, thisObj = null) => {
    const checkBox = thisObj ?? $(".taggedDataTable .rdt_TableHeadRow .selectContainer");
    const trs = $(".taggedDataTable .rdt_TableBody .rdt_TableRow:not(.activeTr) .selectContainer");
    const activetrs = $(".taggedDataTable .rdt_TableBody .rdt_TableRow.activeTr .selectContainer");
    if(isAllSelected){
        $(trs).trigger("click");
        $(checkBox).addClass("active");
    }
    else{
        $(checkBox).removeClass("active");
        $(activetrs).trigger("click");
    }
}