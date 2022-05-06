import React, { Component, useEffect, useState } from 'react';
import ReactDOM from 'react-dom';
import clsx from 'clsx';
import { withStyles } from '@material-ui/core/styles';
import "./ProductTable.scss"
import TagManager from './../../../general-components/TagManager/TagManager';
import VisualPopup from './VissualPopup';
import {unAssignSingleTag} from './apiCalls';
import SvgLoader from "./../../../general-components/SvgLoader";
import productTableFilter from './../../../app-resources/svgs/manager/productTableFilter.svg';
import IconBtn from "./../../../general-components/IconBtn";
import Filter from './Filter';
import { Helmet } from 'react-helmet';
import { 
    getFilterColumnNames,
    manageAllRowSelection,
    handleSelectedCheckboxesStateChange,
} from './Helpers/ProductTableHelper';
 
import { 
    getTaggedTooltipContent,
    getTaggedTooltipTarget
} from './Helpers/PtToolTipHelpers';
 
import { 
    getTableColumns,
 } from './Helpers/PtColumnsHelper';
import {
    GET_PRODUCT_TABLE_DATA
} from './apiCalls';
import ProductTableTooltip from './ProductTableTooltip';
import ServerSideDatatable from '../../../general-components/ServerSideDatatable/ServerSideDatatable';
import useFilter from './CustomHooks/useFilter';
import useGraphHandler from './CustomHooks/useGraphHandler';

const classStyles = theme => ({
    mainClass:{

    },
    productTable: {
        
    },
    ptTooltip:{
        color: "#000",
        backgroundColor: "rgb(255 255 255 / 90%)",
        boxShadow: "1px 1px 10px #0000003b",
        overflow: "hidden",
    },
    ptArrow:{
        color: "#fff"
    },
});

const CustomDatatable = (props) => {
    const wrapperRef = React.useRef();
    const dataTableRef = React.useRef();
 
    const {
        displayGraph,
        dataForGraph,
        onDataTableSearch,
        handleRowClickEvent,
        handleGraphOverLayClick
    } = useGraphHandler();

    const {
        filter,
        showFilter,
        applyFilterOnTable,
        helperLoadFilterAgain,
        handleApplyFilterButtonClick,
    } = useFilter(dataTableRef, resetSelectedAsinsState);

    const [state, setState] = useState({
        toggledClearRows: false,
        showTagPopUp:false,
        totalSelectedRows:0,
        isAllSelected:false,
        selectedArray:[],
        selectedObject:{},
        showSingleTagLoader:false,
        wasFilterOpen:false,
        isDataTableRowClick:false,
        currentPage:1,
        sortDirection:"",
        currentRowsPerPage:10,
    });
    
    const TableColumns = (itemsToShow) => {
        return getTableColumns(state.currentPage, state.currentRowsPerPage, props.classes, itemsToShow, handleSelectAllCheckBoxClick, handleCheckBoxClick, getTooltipTag)
    }
    useEffect(() => {     
        console.log("Reseting",$(".taggedDataTable .rdt_TableBody .rdt_TableRow.activeTr .selectContainer").length >= state.currentRowsPerPage)
        if($(".taggedDataTable .rdt_TableBody .rdt_TableRow.activeTr .selectContainer").length >= state.currentRowsPerPage)
        {
            $(".taggedDataTable .rdt_TableHeadRow .selectContainer").addClass("active")
        }
        else{
            $(".taggedDataTable .rdt_TableHeadRow .selectContainer").removeClass("active");
         
        }
    }, [state.isAllSelected, state.currentPage, state.sortDirection, state.currentRowsPerPage, displayGraph])
    const getTooltipTag = (row) => {
        if(row.tag && row.tag.length > 0)
        {
           let tagArray = row.tag.split(",").sort();
           if( tagArray.length > 0 )
                return <ProductTableTooltip row={row} tooltipContent={getTaggedTooltipContent(row,tagArray, handleSingleTagUnAssignment)} tooltipTarget={getTaggedTooltipTarget(tagArray)}/>

           return tags;
        }
        else return "None"; 
    }
    const handleSingleTagUnAssignment = (e) => {
        let singleTagAjaxData = {};
        if(state.showSingleTagLoader) return;

        setState((prevState)=>({
            ...prevState,
            showSingleTagLoader : !state.showSingleTagLoader
        }));
        let targetEl = typeof $(e.target).attr("asin") == "undefined" ? $(e.target).parents("svg") : e.target;
        $(targetEl).parents(".ProductTableTooltip").find(".singleTagLoader").show();
        singleTagAjaxData.asin = $(targetEl).attr("asin");
        singleTagAjaxData.accountId = $(targetEl).attr("account-id");
        singleTagAjaxData.tagId = $(targetEl).attr("tag-id");
        unAssignSingleTag(
            singleTagAjaxData,
            () =>{
                setState((prevState)=>({
                    ...prevState,
                    showSingleTagLoader : !state.showSingleTagLoader,
                }));
                showDataTableLoader();
                $(targetEl).parents(".ProductTableTooltip").find(".singleTagLoader").hide();
            },
            (error) =>{
                console.log(error);
                setState((prevState)=>({
                    ...prevState,
                    showSingleTagLoader : !state.showSingleTagLoader
                }));
                $(targetEl).parents(".ProductTableTooltip").find(".singleTagLoader").hide();
            }
        );
    }
    const handleOnSortDataTable = (column, sortDirection, event) => {
        setState((prevState)=>({
            ...prevState,
            sortDirection,
        }))
        handleSelectedCheckboxesStateChange(state, handleIfAllRowsSelected);
    }
    const handleOnChangePage = (currentPage, totalRows) => {
        setState((prevState)=>({
            ...prevState, currentPage 
        }));
        handleSelectedCheckboxesStateChange( state, handleIfAllRowsSelected);
    }
    const handleCheckBoxClick = (e) => {
        const checkBox = e.target;
        const tr = $(checkBox).parents(".rdt_TableRow");
        const rowTitle = $(tr).find(".RowTitle");
        const {selectedArray} = state;

        let asin = $(rowTitle).attr("asin");
        let ffc = $(rowTitle).attr("ffc");
        let fkAccountId = $(rowTitle).attr("fk-account-id");
        
        $(tr).toggleClass("activeTr");

        if($(tr).hasClass("activeTr")){
            selectedArray.push(asin);
            setState((prevState)=>({
                ...prevState,
                selectedArray,
                selectedObject : {
                    ...prevState.selectedObject,
                    [asin]:{
                        ffm: ffc,
                        accountId: fkAccountId
                    }
                  }
            }));
        }
        else{
            selectedArray.remove(asin);
            const {selectedObject} = state;
            delete selectedObject[asin];
            setState((prevState)=>({
                ...prevState,
                selectedArray,
                selectedObject
            }))
        }
        handleIfAllRowsSelected();
    }
    const handleIfAllRowsSelected = () => {
        // handleing if all checkbox selected.
        const headerCheckBox = $(".taggedDataTable .rdt_TableHeadRow .selectContainer");
        const trs = $(".taggedDataTable .rdt_TableBody .rdt_TableRow");
        if(trs.length == $(".taggedDataTable .rdt_TableBody .rdt_TableRow.activeTr").length){
            setState((prevState)=>{   
                $(headerCheckBox).addClass("active");
                return {
                    ...prevState,
                    isAllSelected:true   
                }
            })
        }
        else{
            setState((prevState)=>{  
                $(headerCheckBox).removeClass("active");
                return {
                    ...prevState,
                    isAllSelected:false   
                }
            })
        }
        handleTagPopUp();
    }
    const handleSelectAllCheckBoxClick = (e) => {
        e.stopPropagation();
        e.preventDefault();
        manageAllRowSelection(!state.isAllSelected);
        
        setState((prevState)=>({
            ...prevState,
            isAllSelected:!state.isAllSelected   
        }))
    }
    const handleTagPopUp = ()=>{
        if(state.selectedArray.length > 0 && state.showTagPopUp) return;
        setState((prevState)=>({
            ...prevState,
            showTagPopUp:state.selectedArray.length > 0
        }))
    }
    const onTagPopupCloseButtonClicked = (close)=>{
        if(close) {
            resetSelectedAsinsState();
        }
    }
    useEffect(() => {
        if(state.selectedArray.length <=0 ){
            console.log("SellectedArray called")
           manageAllRowSelection(false);
           handleTagPopUp();
        }
    }, [state.selectedArray, state.isAllSelected])
    const resetSelectedAsinsState = ()=>{
        setState((prevState)=>{
           return {
                ...prevState,
                selectedArray: [],
                selectedObject: {},
                isAllSelected: false,
                showTagPopUp:false,
                showSingleTagLoader:false,
            }
        })
    }
    const showDataTableLoader = (shouldReset = true) => {
        dataTableRef.current.helperReloadDataTable(()=>{
            
            if(!shouldReset){  
                setState((prevState)=>{
                    return {
                         ...prevState,
                         isAllSelected: false,
                     }
                 })
                handleSelectedCheckboxesStateChange(state, handleIfAllRowsSelected);
            }
            else
            {
                resetSelectedAsinsState();
            }
        });
    }
    const relaodDatatable = ()=>{
        resetSelectedAsinsState();
    }
    const handleOnDataTableSearch = () => {
        onDataTableSearch();
        console.log("Reseting")
        resetSelectedAsinsState();
    }
    const { showTagPopUp } = state;
        return (
            <>
            <Helmet>
                <title>Ad-Tech | Product Table</title>
            </Helmet>
            <div className="datTableProduct">
                <ServerSideDatatable 
                    ref = {dataTableRef}
                    url = {GET_PRODUCT_TABLE_DATA}
                    dataForAjax = {
                        {
                            columnsToSearch:  getFilterColumnNames(state.currentPage, state.currentRowsPerPage, props.classes,  filter ? filter.itemsToShow:[3,4,5,6], handleSelectAllCheckBoxClick, handleCheckBoxClick, getTooltipTag),
                            segmentsIds: filter.segmentIds,
                            tagIds: filter.tagIds,
                        }
                    }
                    title="Product Table"
                    customClass="productTable"
                    showButtons
                    buttons = {
                        <>
                        <IconBtn
                                BtnLabel={"Filter"}
                                variant={"contained"}
                                icon={<SvgLoader
                                    src={productTableFilter}/>}
                                    style={{background:"#e1a23b"}}
                                onClick={handleApplyFilterButtonClick}
                            /> 
                        </>
                    }
                    otherSection={
                        <>
                        {
                            showFilter ? 
                            <Filter 
                                filter={filter} 
                                applyFilterOnTable={applyFilterOnTable}  
                                relaodDatatable={relaodDatatable}
                            ></Filter> 
                            : null
                        }
                        <div className={clsx("relative w-full dataTableContainer", displayGraph ? "show":"")} >
                            <div className="overlay absolute h-full pl-20 w-full" onClick={handleGraphOverLayClick}></div>
                            <div className={clsx("ProductNarativeGraph absolute pl-20 w-full z-20")} onClick={handleGraphOverLayClick}>
                            {displayGraph ?<VisualPopup  dataForGraph = {dataForGraph}/> : null}
                            </div>
                        </div>
                        </>   
                    }
                    columns={TableColumns(filter ? filter.itemsToShow: [3,4,5,6])}
                    handleRowClickEvent = {handleRowClickEvent}
                    callBackOnChangePage = {handleOnChangePage}
                    callBackOnSortDataTable = {handleOnSortDataTable}
                    onDataTableSearch={handleOnDataTableSearch}
                    callBackOnChangeRowsPerPage={(currentRowsPerPage, currentPage)=>{
                            setState((prevState)=>({
                                ...prevState, currentRowsPerPage 
                            }));
                            handleSelectedCheckboxesStateChange(state, handleIfAllRowsSelected)
                        }
                    }
                />
            </div>
            {/* <div style={{display: 'table', tableLayout:'fixed', width:'100%'}} className="productTable ">
                <Card className="overflow-hidden">
                   
                </Card>
            </div> */}
            
            {showTagPopUp ?
                <TagManager 
                    dots={state.selectedArray.length} 
                    selectedObject = {state.selectedObject}
                    onTagPopupClose = {onTagPopupCloseButtonClicked} 
                    showDataTableLoader = {showDataTableLoader} 
                    helperLoadFilterAgain = {helperLoadFilterAgain}
                    showFilter={showFilter}
                    type="1"
                />
                :null} 
            </>
        )
};

export default withStyles(classStyles)(CustomDatatable)