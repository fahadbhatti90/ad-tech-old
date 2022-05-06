import React, {Component} from 'react';
import clsx from 'clsx';
import DataTable from 'react-data-table-component';
import Checkbox from '@material-ui/core/Checkbox';
import {makeStyles, withStyles } from '@material-ui/core/styles';
import LinearProgress from '@material-ui/core/LinearProgress';
import Card from '@material-ui/core/Card';
import "./CampaignTaggingTable.scss"
import SearchIcon from '@material-ui/icons/Search';
import TagManager from './../../../general-components/TagManager/TagManager';
import DoneIcon from '@material-ui/icons/Done';
import Tooltip from '@material-ui/core/Tooltip';
import CloseIcon from '@material-ui/icons/Close';
import DataTableLoadingCheck from './DataTableLoadingCheck';
import {getCampaignsData,unAssignSingleTag} from './apiCalls';

import { 
    getNewData
 } from './../ProductTable/Helpers/ProductTableHelper';



const campaignNameRowHandler = (row,classes) => {
    let campaignName = row.campaignName && row.campaignName.length > 0
        ? row.campaignName.length > 70
            ? row.campaignName.slice(0, 67) + "..."
            : row.campaignName
        : "NA"
    let campaignNameFull = row.campaignName && row.campaignName.length > 0 ? row.campaignName : "NA"
    // return 
    return <div
        fk-account-id={row.fkAccountId}
        campaign-id={row.campaignId}
        className="RowTitle"
    >
        <Tooltip
        classes={{
            popper:classes.mainClass,
            popperInteractive:classes.campaignTaggingTable,
            tooltip:classes.ctTooltip,
            arrow:classes.ctArrow,
        }}
        className="campaignName newClass" placement="top" title={campaignNameFull} arrow interactive>
            <div
                fk-account-id={row.fkAccountId}
                campaign-id={row.campaignId}
                className="RowTitle"
            >
                {campaignName}
            </div>
        </Tooltip>
    </div>;
}
var incrementedId = 0;


const useStyles = makeStyles(theme => ({
    root: {
        width: '100%',
        '& > * + *': {
            marginTop: theme.spacing(2),
        },
    },
}));
const classStyles = theme => ({
    mainClass:{

    },
    campaignTaggingTable: {
     
    },
    ctTooltip:{
        color: "#000",
        backgroundColor: "rgb(255 255 255 / 90%)",
        boxShadow: "1px 1px 10px #0000003b",
        // overflow: "hidden",
    },
    ctArrow:{
        color: "#fff"
    },
});
const LinearIndeterminate = (props) => {
    const classes = useStyles();

    return (
        <div className={classes.root}>
            <LinearProgress/>
            <DataTableLoadingCheck setDatatableLoaded={props.setDatatableLoaded}/>
        </div>
    );
};
const SrCustomHead = (props) => {
    return (
        <>
            <div className="selectContainer" onClick={props.handleClick}>
                <div className="checkboxMiniContainer justify-center items-center">
                    <span className="flex justify-center items-center"><DoneIcon></DoneIcon></span>
                </div>
            </div>
            Sr.#
        </>
    )
}
class CampaignTaggingDataTable extends Component {
    constructor(props) {
        super(props)
        this.state = {
            data: [],
            orignalData: [],
            loading: false,
            totalRows: 0,
            perPage: 10,
            toggledClearRows: false,
            displayGraph: false,
            showTagPopUp: false,
            totalSelectedRows: 0,
            asin: "",
            isAllSelected: false,
            selectedArray: [],
            selectedObject: {},
            columns: [],
            isDataTableLoaded: false,
            showSingleTagLoader: false,
        };
    }

    componentDidMount() {
        const {perPage} = this.state;
        let _this = this;
        this.setState({loading: true});
        getCampaignsData(
            (response) => {
                let newData = response.data.sort(function(a, b) {
                    return a["Sr.#"] - b["Sr.#"];
                  });
                _this.setState({
                    data: newData,
                    orignalData: newData,
                    totalRows: newData.length,
                    loading: false,
                    columns: _this.getTableColumns()
                });
            },
            (error) => {
                _this.setState({loading: false});
                console.log(error)
            }
        );

    }

    getTableColumns = () => {
        return [
            {
                name: <SrCustomHead handleClick={this.handleSelectAllCheckBoxClick}/>,
                selector: 'Sr.#',
                sortable: false,
                cell: (row) => <>
                    <div className="selectContainer" onClick={this.handleCheckBoxClick}>
                        <div className="checkboxMiniContainer">
                            <span><DoneIcon></DoneIcon></span>
                        </div>
                    </div>
                    {row['Sr.#']}
                </>,
                maxWidth: "100px"
            },
            {
                name: 'Campaign Name',
                selector: 'campaignName',
                sortable: true,
                wrap: true,
                cell: (row)=>campaignNameRowHandler(row,this.props.classes)
            },
            {
                name: 'Child Brand',
                selector: 'accounts',
                sortable: true,
                wrap: true,
                cell: (row)=>this.getAccountColumnValue(row,this.props.classes)
            },
            {
                name: 'Tags',
                selector: 'tag',
                sortable: true,
                wrap: true,
                cell: this.getTooltipTag,
            },

        ];
    }
    getAccountColumnValue = (row, classes) => {
        let childBrandName = row.accounts;
        let newChildBrand = childBrandName.length > 30
            ? childBrandName.slice(0, 27) + "..."
            : childBrandName;
        const childBrand = <Tooltip
                    classes={{
                        popper:classes.mainClass,
                        popperInteractive:classes.campaignTaggingTable,
                        tooltip:classes.ctTooltip,
                        arrow:classes.ctArrow,
                    }}
                    className="newClass" placement="top"
                                    title={childBrandName}
                                    arrow interactive>
                        <span>
                            {newChildBrand}
                        </span>
        </Tooltip>
        return childBrand;
    }
    getTooltipTag = (row) => {
        if (row.tag.length > 0) {
            const tags = <Tooltip
            classes={{
                popper:this.props.classes.mainClass,
                popperInteractive:this.props.classes.campaignTaggingTable,
                tooltip:this.props.classes.ctTooltip,
                arrow:this.props.classes.ctArrow,
            }}
            id="campaignTaggingTooltip" className="newClass" placement="top" title={this.getTooltipContent(row)} arrow
                                  interactive>
                            <span>
                                {row.tag[0].tag} {this.getTagTypeName(row.tag[0].type)}...
                            </span>
                        </Tooltip>
            return tags;
        } else {
            return "None"
        }
    }
    getTagTypeName = (type) => {
        switch (type) {
            case 1:
                return "(Product Type)"
                break;
            case 2:
                return "(Strategy Type)"
                break;
            default:
                return "(Custom Type)";
                break;
        }
    }
    getTooltipContent = (row) => {
        const tags = row.tag.map((tag,index)=>{
            return  <div key = {(tag.tag+index+tag.type+tag.fkTagId)} className="flex items-center singleTagDelete">
                        <span>{tag.tag} {this.getTagTypeName(tag.type)} </span>
                        <CloseIcon
                        campaign-id={row.campaignId} 
                        account-id ={row.fkAccountId} 
                        tag-id ={tag.fkTagId} 
                        tag-type ={tag.type} 
                        rowid = {row["Sr.#"]} 
                        className="cursor-pointer" 
                        onClick={this.handleSingleTagUnAssignment}
                        />
                    </div>
        });//end map funciton
        return <div className="CampaignTaggingTooltip">
                    <div className="singleTagLoader absolute left-0 top-0 w-full hidden">
                        <LinearProgress />
                    </div>
                    {tags}
                </div>
    }
    
    handleSingleTagUnAssignment = (e) => {
        let singleTagAjaxData = {};
        
        if(this.state.showSingleTagLoader) return;

        this.setState({
            showSingleTagLoader : !this.state.showSingleTagLoader
        });
        let targetEl = typeof $(e.target).attr("campaign-id") == "undefined" ? $(e.target).parents("svg") : e.target;
        $(targetEl).parents(".CampaignTaggingTooltip").find(".singleTagLoader").show();
        singleTagAjaxData.campaignId = $(targetEl).attr("campaign-id");
        singleTagAjaxData.accountId = $(targetEl).attr("account-id");
        singleTagAjaxData.tagType = $(targetEl).attr("tag-type");
        singleTagAjaxData.tagId = $(targetEl).attr("tag-id");
        let rowId = $(targetEl).attr("rowid");
        unAssignSingleTag(
            singleTagAjaxData,
            (response) =>{
                this.setState({
                    showSingleTagLoader : !this.state.showSingleTagLoader,
                });
                this.showDataTableLoader(true);
                this.updateDataTable(getNewData(this.state.orignalData, rowId, singleTagAjaxData.tagId, 2, singleTagAjaxData.tagType));
                $(targetEl).parents(".CampaignTaggingTooltip").find(".singleTagLoader").hide();
            },
            (error) => {
                console.log(error);
                this.setState({
                    showSingleTagLoader : !this.state.showSingleTagLoader
                });
                
                $(targetEl).parents(".CampaignTaggingTooltip").find(".singleTagLoader").hide();
            }
        );
    }
    onDataTableSearch = (e) => {
        this.setState({displayGraph: false});
        if (e.target.value.length > 0) {

            var result = this.state.orignalData.filter(row => {
                let tempTagString = "";
                row.tag.map(tag => {
                    tempTagString = tempTagString + tag.tag
                })
                return (row.campaignName.toString().toLowerCase().includes(e.target.value.toLowerCase()) ||
                    row.accounts.toLowerCase().includes(e.target.value.toLowerCase()) ||
                    tempTagString.toString().toLowerCase().includes(e.target.value.toLowerCase()))
            });
            this.setState({
                data: result,
                totalRows: result.length
            })
        } else {
            this.setState({
                data: this.state.orignalData,
                totalRows: this.state.orignalData.length
            })
        }
    }
    handleOnSortDataTable = (column, sortDirection, event) =>{
        const RowTitle = $(".taggedDataTable .RowTitle");

        const {selectedArray} = this.state;
        let _this = this;
        $.each(RowTitle, function (indexInArray, valueOfElement) {

            const parentTr = $(valueOfElement).parents(".rdt_TableRow");
            let campaignId = $(valueOfElement).attr("campaign-id");

            if (selectedArray.includes(campaignId)) {
                $(parentTr).addClass("activeTr");
            } else {
                $(parentTr).removeClass("activeTr");
            }

        });
        _this.handleIfAllRowsSelected();
    }
    handleOnChangeRowsPerPage = (currentRowsPerPage, currentPage) => {
        const RowTitle = $(".taggedDataTable .RowTitle");

        const {selectedArray} = this.state;
        let _this = this;
        $.each(RowTitle, function (indexInArray, valueOfElement) {

            const parentTr = $(valueOfElement).parents(".rdt_TableRow");
            let campaignId = $(valueOfElement).attr("campaign-id");

            if (selectedArray.includes(campaignId)) {
                $(parentTr).addClass("activeTr");
            } else {
                $(parentTr).removeClass("activeTr");
            }

        });
        _this.handleIfAllRowsSelected();
    }
    handleCheckBoxClick = (e) => {
        const checkBox = e.target;

        const trs = $(".taggedDataTable .rdt_TableBody .rdt_TableRow");
        const tr = $(checkBox).parents(".rdt_TableRow");
        const rowTitle = $(tr).find(".RowTitle");
        const {selectedArray} = this.state;

        let campaignId = $(rowTitle).attr("campaign-id");
        let fkAccountId = $(rowTitle).attr("fk-account-id");

        $(tr).toggleClass("activeTr");

        if ($(tr).hasClass("activeTr")) {
            selectedArray.push(campaignId);
            this.setState((prevState) => ({
                selectedArray,
                selectedObject: {
                    ...prevState.selectedObject,
                    [campaignId]: {
                        accountId: fkAccountId
                    }
                }
            }));
        } else {
            selectedArray.remove(campaignId);
            const {selectedObject} = this.state;
            delete selectedObject[campaignId];
            this.setState({
                selectedArray,
                selectedObject
            })
        }
        this.handleIfAllRowsSelected();
    }
    handleIfAllRowsSelected = () => {
        // handleing if all checkbox selected.
        const headerCheckBox = $(".taggedDataTable .rdt_TableHeadRow .selectContainer");
        const trs = $(".taggedDataTable .rdt_TableBody .rdt_TableRow");
        if (trs.length == $(".taggedDataTable .rdt_TableBody .rdt_TableRow.activeTr").length) {
            this.setState({
                isAllSelected: true
            }, () => {
                $(headerCheckBox).addClass("active");
            })
        } else {
            this.setState({
                isAllSelected: false
            }, () => {
                $(headerCheckBox).removeClass("active");
            })
        }
        this.handleTagPopUp();
    }
    handleSelectAllCheckBoxClick = (e) => {
        this.setState({
            isAllSelected: !this.state.isAllSelected
        }, () => {
            this.manageAllRowSelection();
        })
    }
    manageAllRowSelection = () => {
        const checkBox = $(".taggedDataTable .rdt_TableHeadRow .selectContainer");
        const trs = $(".taggedDataTable .rdt_TableBody .rdt_TableRow:not(.activeTr) .selectContainer");
        const activetrs = $(".taggedDataTable .rdt_TableBody .rdt_TableRow.activeTr .selectContainer");
        if (this.state.isAllSelected) {
            $(checkBox).addClass("active");
            $(trs).click();
        } else {
            $(checkBox).removeClass("active");
            $(activetrs).click();
        }
    }
    handleTagPopUp = () => {
        if (this.state.selectedArray.length > 0 && this.state.showTagPopUp) {
            return;
        }
        this.setState({
            showTagPopUp: this.state.selectedArray.length > 0
        })
    }
    onTagPopupCloseButtonClicked = (close) => {
        if (close) {
            this.resetSelectedAsinsState();
        }
    }
    resetSelectedAsinsState = () => {
        this.setState({
            selectedArray: [],
            selectedObject: {},
            isAllSelected: false,
        }, () => {
            this.manageAllRowSelection();
            this.handleTagPopUp();
        })
    }
    showDataTableLoader = (isLoading) => {
        this.setState({
            loading: isLoading
        });
    }
    updateDataTable = (data) => {
        let newData = data.sort(function(a, b) {
            return a["Sr.#"] - b["Sr.#"];
        });
        this.setState({
            data: newData,
            orignalData: newData,
            totalRows: newData.length,
            loading: false,
        }, () => {
            this.setState({
                isDataTableLoaded: false,
            })
        });
    }
    setIsDataTableLoaded = (isLoaded) => {
        this.setState({
            isDataTableLoaded: isLoaded
        }, () => {
            const RowTitle = $(".taggedDataTable .RowTitle");
            const {selectedArray} = this.state;
            let _this = this;
            $.each(RowTitle, function (indexInArray, valueOfElement) {

                const parentTr = $(valueOfElement).parents(".rdt_TableRow");
                let campaignId = $(valueOfElement).attr("campaign-id");

                if (selectedArray.includes(campaignId)) {
                    $(parentTr).addClass("activeTr");
                } else {
                    $(parentTr).removeClass("activeTr");
                }

            });
            _this.handleIfAllRowsSelected();
        })
    }

    render() {
        const {loading, data, totalRows, displayGraph, showTagPopUp, totalSelectedRows, asin} = this.state;
        let element =
            element = <b></b>
        return (
            <>
                <div style={{display: 'table', tableLayout: 'fixed', width: '100%'}} className="campaignTaggingTable ">
                    <Card className="overflow-hidden">
                        <div className="flex p-5">
                            <div className="font-semibold w-3/12">Campaign Tagging</div>
                            <div className="searchDataTable w-9/12">
                                <div
                                    className="border border-gray-300 border-solid flex inputGroup mr-4 px-3 py-1 rounded-full w-7/12 ml-auto">
                                    <input type="text"
                                           className="border-0 flex-1 focus:outline-none font-semibold outline-none px-2 text-xs"
                                           placeholder="Search"
                                           onChange={this.onDataTableSearch}
                                    />
                                    <SearchIcon className="text-gray-300"/>
                                </div>

                            </div>
                        </div>
                        <div className={clsx("relative w-full dataTableContainer", displayGraph ? "show" : "")}>
                            <DataTable
                                className="allASINS taggedDataTable"
                                Clicked
                                noHeader={true}
                                wrap={false}
                                responsive={true}
                                onChangePage={this.handleOnChangeRowsPerPage}
                                columns={this.state.columns}
                                data={data}
                                pagination
                                paginationTotalRows={totalRows}
                                progressPending={loading}
                                progressComponent={<LinearIndeterminate
                                setDatatableLoaded={this.setIsDataTableLoaded}/>}
                                persistTableHead
                                onRowClicked={this.handleRowClickEvent}
                                onSort={this.handleOnSortDataTable}
                            />

                        </div>

                    </Card>
                </div>

                {showTagPopUp ?
                    <TagManager
                        dots={this.state.selectedArray.length}
                        selectedObject={this.state.selectedObject}
                        onTagPopupClose={this.onTagPopupCloseButtonClicked}
                        showDataTableLoader={this.showDataTableLoader}
                        updateDataTable={this.updateDataTable}
                        orignalData = {this.state.orignalData} 
                        type="2"
                    />
                    : null}
            </>
        )
    }
};

export default withStyles(classStyles)(CampaignTaggingDataTable)