import React, {Component} from 'react';
import DataTable from 'react-data-table-component';
import {makeStyles , withStyles} from '@material-ui/core/styles';
import LinearProgress from '@material-ui/core/LinearProgress';
import Card from '@material-ui/core/Card';
import SearchIcon from '@material-ui/icons/Search';
import "./ServerSideDatatable.scss"
import {
    getTableData
} from './apiCalls';
const useStyles = makeStyles(theme => ({
    root: {
        width: '100%',
        '& > * + *': {
            marginTop: theme.spacing(2),
        },
    },
}));

const LinearIndeterminate = () => {
    const classes = useStyles();
    return (
        <div className={classes.root}>
            <LinearProgress/>
        </div>
    );
};
const classStyles = theme => ({
    mainClass:{

    },
    productTable: {
     
    },
    ptTooltip:{
        color: "#000",
        backgroundColor: "rgb(255 255 255 / 90%)",
        boxShadow: "1px 1px 10px #0000003b",
    },
    ptArrow:{
        color: "#fff"
    },
  });
class ServerSideDatatable extends Component {
    constructor(props) {
        super(props)
        this.state = {
            isDataTableSearhing:false,
            data: [],
            orignalData: [],
            loading: false,
            isServerSideLoading: false,
            serverSideData:{
                pageNumber:1,
                perPage:10,
                sort:{
                    isSorting:false,
                    isMultiColumn:false,
                    column1:"ASIN",
                    column2:"",
                    direction:"desc"
                },
                search:{
                    isSearching:false,
                    query:""
                }
            }
        };
    }
    componentDidMount() {
        this.setState({
            isServerSideLoading: true,
        });
        this.fetchTableData();
    }
    fetchTableData = (successCallBack = null) => {
        let params = this.props.dataForAjax ? {
            options: this.state.serverSideData,
            ...this.props.dataForAjax
        } :  {
            options: this.state.serverSideData
        };
        getTableData(this.props.url, params, (response)=>{
            if(this.props.getResponseData)
            this.props.getResponseData(response);

            this.setState({
                data: response.data,
                orignalData: response.data,
                totalRows: response.total,
                loading: false,
                columns:this.props.columns
            },()=>{
                this.setDataTableLoader(false);
                if(successCallBack){
                    successCallBack();
                }
            });
            
        }, (error)=>{
            this.setDataTableLoader(false);
            this.setState({
                loading: false,
            });
        })
    }
    handleOnSortDataTable = (column, sortDirection, event) =>{
        this.setDataTableLoader(true);
        this.setState((prevState)=>({
            serverSideData:{
                ...prevState.serverSideData,
                sort:{
                    isSorting:true,
                    isMultiColumn:column.isMulti,
                    column1:column.selector,
                    column2:column.isMulti ? column.secondColumn : "",
                    direction:sortDirection
                },
            }
        }),()=>{
            this.fetchTableData(()=>{
                if(this.props.callBackOnSortDataTable)
                this.props.callBackOnSortDataTable(column, sortDirection, event)
            });
        })

    }
    handleOnChangeRowsPerPage = (currentRowsPerPage, currentPage) => {
        this.setDataTableLoader(true);
        this.setState((prevState)=>({
            serverSideData:{
                ...prevState.serverSideData,
                perPage:currentRowsPerPage,
            }
        }),()=>{
            this.fetchTableData(()=>{
                if(this.props.callBackOnChangeRowsPerPage)
                this.props.callBackOnChangeRowsPerPage(currentRowsPerPage, currentPage)
            });
        })
    }
    handleOnChangePage = (page, totalRows) => {
        this.setDataTableLoader(true);
        this.setState((prevState)=>({
            serverSideData:{
                ...prevState.serverSideData,
                pageNumber:page,
            }
        }),()=>{
            this.fetchTableData(()=>{
                if(this.props.callBackOnChangePage)
                this.props.callBackOnChangePage(page, totalRows)
            });
            
        })

    }
    handleRowClickEvent = (row)=>{
        //row.ASIN
        if(this.props.handleRowClickEvent)
            this.props.handleRowClickEvent(row);
    }
    helperReloadDataTable = (callBack = null) => {
        this.setDataTableLoader(true);
        this.fetchTableData(callBack);
    }
    onDataTableSearchInputChange = (e) => {
        let value = e.target.value;
        let shouldLoadAllData = (this.state.serverSideData.search.isSearching && value.length <= 0);
        this.setState((prevState)=>({
            serverSideData:{
                ...prevState.serverSideData,
                pageNumber:1,
                search:{
                    isSearching:value.trim().length > 0,
                    query:value.trim()
                },
            }
        }), ()=>{
            if(shouldLoadAllData && this.state.isDataTableSearhing){
                this.setDataTableLoader(true);
                this.setState({
                    isDataTableSearhing:false,
                })
                this.fetchTableData();
            }
        });
    }
    handleDataTableSearchInputOnKeyUp = (e)=>{
        if (e.keyCode == 13) {//if Enter Press
            if(this.state.isServerSideLoading || !this.state.serverSideData.search.isSearching){
                return;
            }
            
            if(this.props.onDataTableSearch)
            this.props.onDataTableSearch();
            
            this.setState({
                isDataTableSearhing:true,
            })
            this.setDataTableLoader(true);
            this.fetchTableData();
        } //end if
    }
    setDataTableLoader = (status)=>{
        this.setState({
            isServerSideLoading: status,
        })
    }
    render() {
        const {columns} = this.props;
        const {loading, data, totalRows, isServerSideLoading} = this.state;
        return (
            <>
                <div style={{display: 'table', tableLayout: 'fixed', width: '100%'}} className={`serverSideDataTable  ${this.props.customClass ?? ""}`}>
                    <Card className="overflow-hidden">
                        <div className="flex p-5">
                            <div className="font-semibold w-3/12">{this.props.title ? this.props.title : "No Card Title"}</div>
                            <div className="searchDataTable w-9/12 flex justify-end">
                                <div className="border border-gray-300 border-solid flex inputGroup mr-4 px-3 py-1 rounded-full w-7/12 ml-auto">
                                    <input type="text"
                                           className="border-0 flex-1 focus:outline-none font-semibold outline-none px-2 text-xs" placeholder="Press ENTER to search"
                                           onChange={this.onDataTableSearchInputChange}
                                           onKeyUp= {this.handleDataTableSearchInputOnKeyUp}
                                    />
                                    <SearchIcon className="text-gray-300"/>
                                </div>
                                {
                                    this.props.showButtons ? 
                                    <div className="flex items-center serverDTSideButtons">
                                       {this.props.buttons}
                                    </div> : null
                                }
                                
                            </div>
                        </div>
                        {
                            this.props.otherSection ? this.props.otherSection : null
                        }
                        <div className=" w-full relative allASINS taggedDataTable">
                            <div className="h-full pl-20 w-full"></div>
                            {/* <div className="graphLoader bg-white absolute h-full overflow-hidden w-full top-0 left-0 z-10"
                                style={this.state.loading ? {display: "block", background: "#ffffffb0"} : {display: "none", background: "#ffffffb0"}}>
                                <LinearProgress/>
                                <div
                                    className="absolute flex font-bold font-mono h-full items-center justify-center overflow-hidden text-1rem text-sm w-full z-10">
                                    Loading...
                                </div>
                            </div> */}
                            
                            <DataTable
                                className={`scrollableDatatable`}
                                Clicked
                                noHeader={true}
                                wrap={false}
                                responsive={true}
                                columns={columns}
                                data={data}
                                pagination
                                paginationServer
                                paginationTotalRows={totalRows}
                                progressPending={isServerSideLoading}
                                progressComponent={<LinearIndeterminate/>}
                                persistTableHead
                                onRowClicked={this.props.handleRowClickEvent}
                                sortServer
                                onSort={this.handleOnSortDataTable}
                                onChangePage={this.handleOnChangePage}
                                onChangeRowsPerPage={this.handleOnChangeRowsPerPage}
                            />
                        </div>
                    </Card>
                </div>
            </>
        )
    }
}

export default withStyles(classStyles)(ServerSideDatatable)