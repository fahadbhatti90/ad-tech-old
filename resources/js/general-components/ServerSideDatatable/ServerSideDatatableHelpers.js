
import React, {Component} from 'react';
import Tooltip from '@material-ui/core/Tooltip';
const getLimitedValue = (orignal,alias) => {
    return orignal && alias == null ? orignal.length > 30 
    ? orignal.slice(0,27)+"..." 
    : orignal : alias ? alias.length > 30 
    ? alias.slice(0,27)+"..." 
    : alias : "None";
}
const ToolTipContent = (props) => {
    return <div>
        <div>
            <b>Orignal:</b>
            {props.orignal}
        </div>
        <div>
            <b>Alias:</b>
            {props.alias}
        </div>
    </div>
}
const CustomTooltip = (props) =>{
    let classes = props.classes;
    let TooltipTarget = props.tooltipTarget;
    let ToolTipContent = props.tooltipContent;
    return <Tooltip classes={{
        popper:classes.mainClass,
        popperInteractive:classes.productTable,
        tooltip:classes.ptTooltip,
        arrow:classes.ptArrow,
       }} className="newClass" placement="top" title={ToolTipContent} arrow interactive>
                            {TooltipTarget}
                        </Tooltip>
}
export const productTitleRowHandler = (row, classes) =>{
    if(row.ASIN == "N/A") return row.product_title;
    let newProductTitle =  getLimitedValue(row.product_title, row.overrideLabelProduct);
    let tooltipTarget = <div
    fk-account-id={row.fk_account_id}
    attr={row.ASIN}
    orignalattritbute={row.product_title}
    alias={row.overrideLabelProduct}
    type="2"
    className="cursor-pointer"
    >
        {newProductTitle}
    </div>
    return newProductTitle != "None" ? <CustomTooltip 
    classes= {classes}
    row = {row}
    tooltipTarget = {tooltipTarget}
    tooltipContent = {<ToolTipContent orignal={row.product_title}  alias={row.overrideLabelProduct} />}
    /> : newProductTitle;
}
export const categoryRowHandler = (row, classes)=>{
    if(row.ASIN == "N/A") return row.category_name;
    let newCategory = getLimitedValue(row.category_name, row.overrideLabelCategory);
    let tooltipTarget = <div
    type="3" 
    attr = {row.category_id} 
    orignalattritbute={row.category_name}
    alias={row.overrideLabelCategory}
    className="cursor-pointer"
    >
        {newCategory}
    </div>
    return newCategory != "None" ? <CustomTooltip 
    classes= {classes}
    row = {row}
    tooltipTarget = {tooltipTarget}
    tooltipContent = {<ToolTipContent orignal={row.category_name}  alias={row.overrideLabelCategory} />}
    /> : newCategory
}
export const subCategoryRowHandler = (row, classes)=>{
    if(row.ASIN == "N/A") return row.subcategory_name;
    let newSubCategory = getLimitedValue(row.subcategory_name, row.overrideLabelSubCategory);
    let tooltipTarget = <div
    type="4" 
    attr = {row.subcategory_id} 
    orignalattritbute={row.subcategory_name} 
    alias={row.overrideLabelSubCategory}
    className="cursor-pointer"
    >
        {newSubCategory}
    </div>
    return newSubCategory != "None" ? <CustomTooltip 
    classes= {classes}
    row = {row}
    tooltipTarget = {tooltipTarget}
    tooltipContent = {<ToolTipContent orignal={row.subcategory_name}  alias={row.overrideLabelSubCategory} />}
    /> : newSubCategory
}
export const brandRowHandler = (row, classes)=>{
    let newBrand = getLimitedValue(row.accountName, row.overrideLabelBrand);
    let tooltipTarget = <div
    type="1" 
    attr = {row.fkAccountId} 
    orignalattritbute={row.accountName} 
    alias={row.overrideLabelBrand}
    className="cursor-pointer"
    >
        {newBrand}
    </div>
    return <CustomTooltip 
    classes= {classes}
    row = {row}
    tooltipTarget = {tooltipTarget}
    tooltipContent = {<ToolTipContent orignal={row.accountName}  alias={row.overrideLabelBrand} />}
    />
}

export const getTableColumns = (itemsToShow, classes) => {
    let cols = [
        {
            name: 'ASIN',
            selector: 'ASIN',
            sortable: true,
            isMulti:false,
            cell: row => row.ASIN ? row.ASIN : "None"
        }, 
        {
            name: 'Product Title',
            selector: 'product_title',
            sortable: true,
            wrap: true,
            isMulti:true,
            secondColumn:"overrideLabelProduct",
            cell: row => productTitleRowHandler(row, classes)
        }, 
        {
            name: 'Subcategory Name',
            selector: 'subcategory_name',
            sortable: true,
            wrap: true,
            isMulti:true,
            secondColumn:"overrideLabelSubCategory",
            cell: row => subCategoryRowHandler(row, classes)
        }, 
        {
            name: 'Category Name',
            selector: 'category_name',
            sortable: true,
            wrap: true,
            isMulti:true,
            secondColumn:"overrideLabelCategory",
            cell: row => categoryRowHandler(row, classes)
        }, 
        {
            name: 'Brand Name',
            selector: 'accountName',
            sortable: true,
            wrap: true,
            isMulti:true,
            secondColumn:"overrideLabelBrand",
            cell: row => brandRowHandler(row, classes)
        }
    ]
    let NewCols = [];
    if(itemsToShow && itemsToShow.length > 0){
        cols.map((item,index)=>{
            if(itemsToShow.includes(index)) NewCols.push(item);
        })
    }
    return NewCols;
}