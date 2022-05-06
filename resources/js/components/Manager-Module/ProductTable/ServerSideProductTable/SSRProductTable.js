import DataTable from 'react-data-table-component';
import {makeStyles , withStyles} from '@material-ui/core/styles';
import LinearProgress from '@material-ui/core/LinearProgress';
import Card from '@material-ui/core/Card';
import SearchIcon from '@material-ui/icons/Search';
import "./ServerSideDatatable.scss"
import {
    getTableData
} from './apiCalls';
// const {columns} = props;
// const {loading, data, totalRows} = state;
export default function SSRProductTable(props) {
    
    const [isDataTableSearhing, SetIsDataTableSearhing] = useState(false);
    const [
        {data, totalRows}, 
        isDataTableLoading, 
        fetchTableData
    ] = SSRApi();
    const [{serverSideData}, setServerSideData] = useState({
        serverSideData:{
            pageNumber:1,
            perPage:10,
            sort:{
                isSorting:false,
                isMultiColumn:false,
                column1:"",
                column2:"",
                direction:"desc"
            },
            search:{
                isSearching:false,
                query:""
            }
        }
    });

    useEffect(()=>{
        fetchTableData(props.dataForAjax, serverSideData);
    }, []);

    const handleOnSortDataTable = (column, sortDirection, event) => {
        let ssr = {
            ...serverSideData,
            sort:{
                isSorting:true,
                isMultiColumn:column.isMulti,
                column1:column.selector,
                column2:column.isMulti ? column.secondColumn : "",
                direction:sortDirection
            },
        }
        setServerSideData({
            serverSideData:ssr
        })
        fetchTableData(props.dataForAjax, ssr);

        if(props.callBackOnSortDataTable)
        props.callBackOnSortDataTable(column, sortDirection, event)

    }
    const handleOnChangeRowsPerPage = (currentRowsPerPage, currentPage) => {
        let ssr = {
            ...serverSideData,
            perPage:currentRowsPerPage,
        }
        setServerSideData({
            serverSideData:ssr
        })
        fetchTableData(props.dataForAjax, serverSideData);
        if(props.callBackOnChangeRowsPerPage)
        props.callBackOnChangeRowsPerPage(currentRowsPerPage, currentPage)
    }
    const handleOnChangePage = (page, totalRows) => {x
        let ssr = {
            ...serverSideData,
            pageNumber:page,
        }
        setServerSideData({
            serverSideData:ssr
        })
        fetchTableData(props.dataForAjax, serverSideData);
        if(props.callBackOnChangePage)
        props.callBackOnChangePage(page, totalRows)

    }
    const handleRowClickEvent = (row)=>{
        //row.ASIN
        if(props.handleRowClickEvent)
            props.handleRowClickEvent(row);
    }
    const onDataTableSearchInputChange = (e) => {
        let value = e.target.value;
        let shouldLoadAllData = (serverSideData.search.isSearching && value.length <= 0);
        let ssr = {
            ...serverSideData,
            pageNumber:1,
            search:{
                isSearching:value.trim().length > 0,
                query:value.trim()
            },
        }
        setServerSideData({
            serverSideData:ssr
        })
        if(shouldLoadAllData && isDataTableSearhing){
            SetIsDataTableSearhing(false)
            fetchTableData(props.dataForAjax, serverSideData);
        }
        
    }
    const handleDataTableSearchInputOnKeyUp = (e)=>{
        if (e.keyCode == 13) {//if Enter Press
            if(isDataTableLoading || !serverSideData.search.isSearching) return;

            SetIsDataTableSearhing(true);
            fetchTableData(props.dataForAjax, serverSideData);
        } //end if
    }
        return (
            <>
                <div style={{display: 'table', tableLayout: 'fixed', width: '100%'}} className="serverSideDataTable">
                    <Card className="overflow-hidden">
                        <div className="flex p-5">
                            <div className="font-semibold w-3/12">{props.title ? props.title : "No Card Title"}</div>
                            <div className="searchDataTable w-9/12 flex justify-end">
                                <div className="border border-gray-300 border-solid flex inputGroup mr-4 px-3 py-1 rounded-full w-7/12 ml-auto">
                                    <input type="text"
                                           className="border-0 flex-1 focus:outline-none font-semibold outline-none px-2 text-xs" placeholder="Press enter to search"
                                           onChange={onDataTableSearchInputChange}
                                           onKeyUp= {handleDataTableSearchInputOnKeyUp}
                                    />
                                    <SearchIcon className="text-gray-300"/>
                                </div>
                                {
                                    props.showButtons ? 
                                    <div className="flex items-center serverDTSideButtons">
                                       {props.buttons}
                                    </div> : null
                                }
                                
                            </div>
                        </div>
                        <div className=" w-full relative">
                            <div className="h-full pl-20 w-full"></div>
                            <div className="graphLoader bg-white absolute h-full overflow-hidden w-full top-0 left-0 z-10"
                                style={isDataTableLoading ? {display: "block", background: "#ffffffb0"} : {display: "none", background: "#ffffffb0"}}>
                                <LinearProgress/>
                                <div
                                    className="absolute flex font-bold font-mono h-full items-center justify-center overflow-hidden text-1rem text-sm w-full z-10">
                                    Loading...
                                </div>
                            </div>
                            <DataTable
                                className=""
                                Clicked
                                noHeader={true}
                                wrap={false}
                                responsive={true}
                                columns={columns}
                                data={data}
                                pagination
                                paginationServer
                                paginationTotalRows={totalRows}
                                progressPending={loading}
                                progressComponent={<LinearIndeterminate/>}
                                persistTableHead
                                onRowClicked={handleRowClickEvent}
                                sortServer
                                onSort={handleOnSortDataTable}
                                onChangePage={handleOnChangePage}
                                onChangeRowsPerPage={handleOnChangeRowsPerPage}
                            />
                        </div>
                    </Card>
                </div>
            </>
        )
}
