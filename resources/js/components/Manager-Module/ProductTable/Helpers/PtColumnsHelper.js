import React from 'react'
import DoneIcon from '@material-ui/icons/Done';
import Chip from '@material-ui/core/Chip';
import {
    productTitleRowHandler
} from './PtToolTipHelpers';
import ProductTableTooltip from '../ProductTableTooltip';

export const SrCustomHead = (props)=>{
    return (
            <>
                <div className={`selectContainer`} onClick={props.handleClick}>
                    <div className="checkboxMiniContainer justify-center items-center">
                        <span className="flex justify-center items-center"><DoneIcon></DoneIcon></span>
                    </div>
                </div>
                Sr.#
            </> 
        )
}
const handlSegments = (row) => {
    if(row.segmentName && row.segmentName.length > 0){
        let segmentNamesArray = row.segmentName.split(",");
        if(segmentNamesArray.length > 0){
            return <ProductTableTooltip row={row} tooltipContent={row.segmentName.split(",").map((sn)=> <div>{sn}</div>)} tooltipTarget={<div className="productTableChip"><Chip size="small" label={row.segmentName.split(",")[0]} /> </div>}/>
        }
        else{
            return <div className="productTableChip"><Chip size="small" label={row.segmentName} /> </div>;
        }
    }
    return "None";
}
const getSr = (currentPage, perPage, index, handleCheckBoxClick) => {
    return <><div className="selectContainer" onClick={handleCheckBoxClick}>
                <div className="checkboxMiniContainer">
                    <span><DoneIcon></DoneIcon></span>
                </div>
            </div>{ (currentPage == 1 ? currentPage + (index) : ((currentPage-1) * perPage)  + (index + 1))}
            </> 
}
export const getTableColumns = (currentPage, perPage, classes, itemsToShow, handleSelectAllCheckBoxClick, handleCheckBoxClick, getTooltipTag)=>{
    let cols = [
        {
            name: <SrCustomHead handleClick = {handleSelectAllCheckBoxClick}/>,
            selector: 'Sr.#',
            sortable: false,
            cell:(row , index)=> getSr(currentPage, perPage , index, handleCheckBoxClick) ,
            maxWidth:"100px"
        },
        {
            name: 'ASIN',
            selector: 'ASIN',
            sortable: true,
            maxWidth:"150px",
            minWidth:"150px"
        },
        {
            name: 'Product Title',
            selector: 'product_title',
            sortable: true,
            wrap:false,	
            maxWidth:"300px",
            minWidth:"300px",
            cell:(row)=>productTitleRowHandler(row, classes)
        },
        {
            name: 'Fullfillment Channel',
            selector: 'fullfillment_channel',
            sortable: true,
            wrap:true,
        },
        {
            name: 'Shipped Units',
            selector: 'shipped_units',
            sortable: true,
            wrap:true,
        },
        {
            name: 'Tags',
            selector: 'tag',
            sortable: true,
            wrap:true,
            cell:getTooltipTag,
        },
        {
            name: 'Segment',
            selector: 'segmentName',
            sortable: true,
            wrap:true,
            cell:handlSegments,
        },
        {
            name: 'Cost',
            selector: 'cost',
            sortable: true,
            wrap:true,
        },
        {
            name: 'Revenue',
            selector: 'revenue',
            sortable: true,
            wrap:true,
        },
        {
            name: 'ACOS',
            selector: 'acos',
            sortable: true,
            wrap:true,
        },
        {
            name: 'Order Conversion',
            selector: 'order_conversion',
            sortable: true,
            wrap:true,
        },
        {
            name: 'Order Units',
            selector: 'order_units',
            sortable: true,
            wrap:true,
        },
        {
            name: 'QTD Shipped Units',
            selector: 'qtd_shipped_units',
            sortable: true,
            wrap:true,
        },
        {
            name: 'YTD Shipped Units',
            selector: 'ytd_shipped_units',
            sortable: true,
            wrap:true,
        },
        {
            name: 'MTD Shipped Units',
            selector: 'mtd_shipped_units',
            sortable: true,
            wrap:true,
        },
        {
            name: 'WTD Shipped Units',
            selector: 'wtd_shipped_units',
            sortable: true,
            wrap:true,
        },
        {
            name: 'Last Week Shipped Units',
            selector: 'last_week_shipped_units',
            sortable: true,
            wrap:true,
        },
        {
            name: 'Price',
            selector: 'price',
            sortable: true,
            wrap:true,
        },
        {
            name: 'Price Diff 30d',
            selector: 'price_diff_30d',
            sortable: true,
            wrap:true,
        },
        {
            name: 'Sales Rank',
            selector: 'salesrank',
            sortable: true,
            wrap:true,
        },
        {
            name: 'PRSC Diff Salesrank Pre 30d',
            selector: 'prsc_diff_salesrank_pre_30d',
            sortable: true,
            wrap:true,
        },
        {
            name: 'Sellable Inv Units',
            selector: 'sellable_inv_units',
            sortable: true,
            wrap:true,
        },
        {
            name: 'Unsellable Inv Units',
            selector: 'unsellable_inv_units',
            sortable: true,
            wrap:true,
        },
        {
            name: 'PO Units',
            selector: 'po_units',
            sortable: true,
            wrap:true,
        },
        {
            name: 'Review Score',
            selector: 'review_score',
            sortable: true,
            wrap:true,
        },
        {
            name: 'Review Score 30d',
            selector: 'review_score_30d',
            sortable: true,
            wrap:true,
        },
        {
            name: 'Review Count',
            selector: 'review_count',
            sortable: true,
            wrap:true,
        },
        {
            name: 'Review Count 30d',
            selector: 'review_count_30d',
            sortable: true,
            wrap:true,
        },
        {
            name: 'YTD PO Units',
            selector: 'ytd_po_units',
            sortable: true,
            wrap:true,
        },
        {
            name: 'MTD PO Units',
            selector: 'mtd_po_units',
            sortable: true,
            wrap:true,
        },
        {
            name: 'QTD PO Units',
            selector: 'qtd_po_units',
            sortable: true,
            wrap:true,
        },
        {
            name: 'WTD PO Units',
            selector: 'wtd_po_units',
            sortable: true,
            wrap:true,
        },
        {
            name: 'Last Week PO Units',
            selector: 'last_week_po_units',
            sortable: true,
            wrap:true,
        },
    ];
    let NewCols = [];
    NewCols.push(cols[0]);
    NewCols.push(cols[1]);
    NewCols.push(cols[2]);
    if(itemsToShow && itemsToShow.length > 0){
        cols.map((item,index)=>{
            if(itemsToShow.includes(index)) NewCols.push(item);
        })
    }
    return NewCols;
}