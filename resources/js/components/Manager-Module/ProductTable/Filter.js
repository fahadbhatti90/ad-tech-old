import React, { Component } from 'react'
import "./ProductTable.scss";
import MultiSelect from "./../../../general-components/MultiSelect";
import TextButton from "./../../../general-components/TextButton";
import PrimaryButton from "./../../../general-components/PrimaryButton";
import {primaryColor} from "./../../../app-resources/theme-overrides/global";
import {getTagsForFilter} from './apiCalls';
const customStyle ={
    menu: base => ({
    ...base,
    marginTop: 0
    }),
    control: (base, state) => ({
    background: '#fff',
    height: 30,
    border: "1px solid #c3bdbd8c",
    borderRadius: 20,
    display: 'flex', 
    border: "1px solid #c3bdbd8c", //${primaryColor}
    // This line disable the blue border
    boxShadow: 0,
    '&:hover': {
        border:  "1px solid #c3bdbd8c"
    },
    fontSize: '0.72rem'
    }),
    container: (provided, state) => ({
    ...provided,
    marginTop: 8
    }),
    valueContainer: (provided, state) => ({
    ...provided,
    padding: "0px 8px",
    overflowY: "auto",

    }),
    multiValue: (styles, { data }) => {
    return {
        ...styles,
        borderRadius:25
    };
    },
    multiValueRemove: (styles, { data }) => ({
    ...styles,
    color: data.color,
    ':hover': {
        backgroundColor: primaryColor,
        color: 'white',
        borderRadius: 25
    },
    }),
}
const selectedColumnOptions = [
    {label: "Fullfillment Channel", value: 3, className: 'custom-class'},
    {label: "Shipped Units", value: 4, className: 'awesome-class'},
    {label: "Tags", value: 5, className: 'awesome-class'},
    {label: "Segment", value: 6, className: 'awesome-class'}
];
const columnOptions = [
    {label: "Fullfillment Channel", value: 3, className: 'custom-class'},
    {label: "Shipped Units", value: 4, className: 'awesome-class'},
    {label: "Tags", value: 5, className: 'awesome-class'},
    {label: "Segment", value: 6, className: 'awesome-class'},
    {label: "Cost", value: 7, className: 'awesome-class'},
    {label: "Revenue", value: 8, className: 'awesome-class'},
    {label: "ACOS", value: 9, className: 'awesome-class'},
    {label: "Order Conversion", value: 10, className: 'awesome-class'},
    {label: "Order Units", value: 11, className: 'awesome-class'},
    {label: "QTD Shipped Units", value: 12, className: 'awesome-class'},
    {label: "YTD Shipped Units", value: 13, className: 'awesome-class'},
    {label: "MTD Shipped Units", value: 14, className: 'awesome-class'},
    {label: "WTD Shipped Units", value: 15, className: 'awesome-class'},
    {label: "Last Week Shipped Units", value: 16, className: 'awesome-class'},
    {label: "Price", value: 17, className: 'awesome-class'},
    {label: "Price Diff 30d", value: 18, className: 'awesome-class'},
    {label: "Sales Rank", value: 19, className: 'awesome-class'},
    {label: "PRSC Diff Salesrank Pre 30d", value: 20, className: 'awesome-class'},
    {label: "Sellable Inv Units", value: 21, className: 'awesome-class'},
    {label: "Unsellable Inv Units", value: 22, className: 'awesome-class'},
    {label: "PO Units", value: 23, className: 'awesome-class'},
    {label: "Review Score", value: 24, className: 'awesome-class'},
    {label: "Review Score 30d", value: 25, className: 'awesome-class'},
    {label: "Review Count", value: 26, className: 'awesome-class'},
    {label: "Review Count 30d", value: 27, className: 'awesome-class'},
    {label: "YTD PO Units", value: 28, className: 'awesome-class'},
    {label: "MTD PO Units", value: 29, className: 'awesome-class'},
    {label: "QTD PO Units", value: 30, className: 'awesome-class'},
    {label: "WTD PO Units", value: 31, className: 'awesome-class'},
    {label: "Last Week PO Units", value: 32, className: 'awesome-class'},
];

export default class Filter extends Component {
    constructor(props){
        super(props);
        this.state = {
            segments:null,
            columns:null,
            tags:null,
            segmentsOptions:[],
            tagsOptions:[],
            columnsOptions:[],
            loaders:{
                showSegmentsFilterLoader:true,
                showTagsFilterLoader:true,
                showColumnsFilterLoader:true,
            },
            toggleGroup:false,
        }
    }
    componentDidMount(){
        getTagsForFilter((response)=>{
            let tagsOptions = response.data.tags.map((obj, idx) => {
                return {
                    label: (obj.tag.length > 50 ? obj.tag.substr(0, 50) + "..." : obj.tag),
                    value: obj.id,
                    key: idx
                }
            });
            let segmentsG = response.data.segmentsG.map((obj, idx) => {
                return {label:  this.getGroupHeadingComponent(obj.groupName), options: obj.segments.map((segment)=>{
                   return {label: segment.segmentName, value: segment.id, className: 'custom-class'}
                })}
            });
            
            let segmentsI = response.data.segmentsI.map((segment, idx) => {
                return {label: segment.segmentName, value: segment.id, className: 'custom-class'}
            });
            
            let segmentsOptions = [...segmentsG, ...segmentsI];
            let selectedSegments = [];
            let selectedTags = [];
            let selectedColumns = selectedColumnOptions;
            if(this.props.filter){
                    selectedTags = tagsOptions.filter((tagsOption)=>{
                        return this.props.filter.tagIds.includes(tagsOption.value);
                    });
                            
                    segmentsG.map((segmentsGOption)=>{
                        segmentsGOption.options.map((segementOpt)=>{
                            if(this.props.filter.segmentIds.includes(segementOpt.value))
                            {
                                selectedSegments.push(segementOpt);
                            }
                        });
                    });
                    segmentsI.map((segmentIOption)=>{
                        if(this.props.filter.segmentIds.includes(segmentIOption.value))
                            {
                                selectedSegments.push(segmentIOption);
                            }
                    });
                    selectedColumns = columnOptions.filter((columnOption)=>{
                        return this.props.filter.itemsToShow.includes(columnOption.value);
                    });
            }
            this.setState({
                tagsOptions,
                segmentsOptions:segmentsOptions,
                segments:selectedSegments,
                tags: selectedTags,
                columns: selectedColumns,
                columnsOptions:columnOptions,
                loaders:{
                    showSegmentsFilterLoader:false,
                    showTagsFilterLoader:false,
                    showColumnsFilterLoader:false,
                }
            },()=>{
                $(".segments .select__value-container").animate({
                    scrollTop: $('.segments .select__value-container').get(0).scrollHeight
                });
                $(".tags .select__value-container").animate({
                    scrollTop: $('.tags .select__value-container').get(0).scrollHeight
                });
                $(".columns .select__value-container").animate({
                    scrollTop: $('.columns .select__value-container').get(0).scrollHeight
                });
            })
        }, (error)=>{
            console.log(error)
        });
    }
    handleGroupHeadingClick = (e)=>{
        $(e.target).parent().next().slideToggle("fast");
        $(e.target).toggleClass("toggleGroup");
    }
    getGroupHeadingComponent = (groupName) => <div onClick={this.handleGroupHeadingClick} > {groupName} </div>
    onSegmentChangeHandler = (value) => {
        this.setState({
            segments: value
        });
        $(".segments .select__value-container").clearQueue().animate({
            scrollTop: $('.segments .select__value-container').get(0).scrollHeight
        });
    }
    onTagChangeHandler = (value) => {
        this.setState({
            tags: value
        });
        $(".tags .select__value-container").clearQueue().animate({
            scrollTop: $('.tags .select__value-container').get(0).scrollHeight
        });
    }
    onColumnSelectorChangeHandler = (value) => {
        this.setState({
            columns: value
        });
        $(".columns .select__value-container").clearQueue().animate({
            scrollTop: $('.columns .select__value-container').get(0).scrollHeight
        });
    }
    handleApplyFilterButtonClick = (e) =>{
        let tagIds = this.state.tags ? this.state.tags.map((tag)=>parseInt(tag.value)) : [];
        let segmentIds = this.state.segments ? this.state.segments.map((segment)=>parseInt(segment.value)) : [];
        let itemsToShow = this.state.columns ? this.state.columns.map((column)=>parseInt(column.value)): [];
        this.props.applyFilterOnTable({
            tagIds,
            segmentIds,
            itemsToShow
        })
    }
    handleClearFilter = (e) =>{
        this.setState({
            segments:[],
            tags:[],
            columns:selectedColumnOptions
        },()=>{
            this.props.relaodDatatable()
            this.props.applyFilterOnTable({
                tagIds:[],
                segmentIds:[],
                itemsToShow:[3, 4, 5, 6]
            })
        })  
    }
    render() {
        return (
            <div className="flex pr-10 h-56 productTableFilter px-10 py-5 md:pr-24">
                <div className="w-4/12 md:pr-10 pr-5 segments">
                    <div>
                        <label className="text-xs font-normal ml-2">
                        Product Segments
                        </label>
                        <MultiSelect
                            placeholder="Segments"
                            name="segments"
                            id="segments"
                            value={this.state.segments}
                            onChangeHandler = {this.onSegmentChangeHandler}
                            fullWidth={true}
                            Options={this.state.segmentsOptions}
                            styles={customStyle}
                            customClassName="mr-5 ThemeSelect"
                            isLoading={this.state.loaders.showSegmentsFilterLoader}
                            menuIsOpen
                        />
                    </div>
                </div>
                <div className="w-4/12 md:pr-10 pr-5 tags">
                    <div>
                        <label className="text-xs font-normal ml-2">
                        Product Tags
                        </label>
                        <MultiSelect
                            placeholder="tags"
                            name="tags"
                            id="tags"
                            value={this.state.tags}
                            onChangeHandler = {this.onTagChangeHandler}
                            fullWidth={true}
                            Options={this.state.tagsOptions}
                            styles={customStyle}
                            customClassName="mr-5 ThemeSelect"
                            isLoading={this.state.loaders.showTagsFilterLoader}
                            menuIsOpen
                        />
                    </div>
                </div>
                <div className="w-4/12 flex columns">
                    <div className="w-9/12">
                        <label className="text-xs font-normal ml-2">
                            Add/Remove Column
                        </label>
                        <MultiSelect
                            placeholder="Columns"
                            name="columns"
                            id="columns"
                            value={this.state.columns}
                            onChangeHandler = {this.onColumnSelectorChangeHandler}
                            fullWidth={true}
                            Options={this.state.columnsOptions}
                            styles={customStyle}
                            customClassName="mr-5 ThemeSelect"
                            isLoading={this.state.loaders.showColumnsFilterLoader}
                            menuIsOpen
                        />
                    </div>
                    <div className="flex flex-col items-center justify-end pb-5 w-3/12">
                        <TextButton
                        BtnLabel={"Reset all"}
                        color="primary"
                        styles={{paddingRight:0, paddingLeft:0, outline:"none", width:"100%"}}
                        onClick={this.handleClearFilter}
                        ></TextButton>
                        <PrimaryButton
                            btnlabel={"Apply"}
                            variant={"contained"}
                            onClick={this.handleApplyFilterButtonClick}/> 
                    </div>
                </div>
            </div>
        )
    }
}
