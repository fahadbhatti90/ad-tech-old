import React, {Component} from 'react';
import clsx from 'clsx';
import {makeStyles , withStyles} from '@material-ui/core/styles';
import LinearProgress from '@material-ui/core/LinearProgress';
import DataTable from 'react-data-table-component';
import Card from '@material-ui/core/Card';
import SearchIcon from '@material-ui/icons/Search';
import PrimaryButton from "./../../../../general-components/PrimaryButton";
import "./SearchRank.scss"
import {
    getScheduleTableColumns,
} from './SearchRankScrapingHelpers';
import {
    getAllSchedule
} from './apiCalls';
import ScrapingModel from './ScrapingModel';
import AddSchedule from './AddSchedule';
import {Helmet} from "react-helmet";
import DeleteSchedule from './Delete/DeleteSchedule';
import ExportCsv from './../../../../general-components/ExportCsv';
import ScheduleTime from './ScheduleTime';
import ScheduleTimeLoader from './ScheduleTime/ScheduleTimeLoader';
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
class Container extends Component {
    constructor(props) {
        super(props)
        let _this = this;
        this.state = {
            data: [],
            orignalData:[],
            loading: false,
            totalRows: 0,
            perPage: 10,
            departments: [],
            scheduleTime: "",
            columns : _this.getTablColumns(),
            profiles:[],
            isDataTableLoaded:false,
            modal:{
                open:false,
                modalComponent:null,
                modalTitle:""
            }
        };
        
        this.wrapperRef = React.createRef();
    }
    componentDidMount() {
        if(!htk.isUserLoggedIn()){
            return;
        }
        
        const { perPage } = this.state;
    
        this.setState({ loading: true });
    
        getAllSchedule((response)=>{
            this.setState({
                data: response.data,
                orignalData: response.data,
                departments: response.departments,
                scheduleTime: response.scheduleTime,
                totalRows: response.data.length,
                loading: false,
                columns:this.getTablColumns(),
                modal:{
                    open:false,
                    modalComponent:<AddSchedule 
                    id={0} 
                    handleModalClose = {this.handleModalClose} 
                    heloperReloadDataTable = {this.helperReloadDataTable} 
                    departments={response.departments}
                    />,
                    modalTitle:"Add Schedule"
                }
            }); 
        },(error)=>{
            console.log(error);
            this.setState({ loading: false });
        })
    }
    getTablColumns = () => {
        return getScheduleTableColumns(this.props.classes, this.showDeleteConfirmationDialog);
    }
    helperReloadDataTable = (data) => {
        this.setState((prevState)=>({
            data: data,
            orignalData: data,
            totalRows: data.length,
            loading: false,
            modal:{
                ...prevState.modal,
                open:false
            }
        })); 
    }
    filterOrignalData =(value) =>{
        return this.state.orignalData.filter(row => {
            return row.id.toString().toLowerCase().includes(value.toLowerCase())||
            row.departmentName.toLowerCase().includes(value.toLowerCase())||
            row.sName.toString().toLowerCase().includes(value.toLowerCase()) ||
            row.frequency.toString().toLowerCase().includes(value.toLowerCase()) ||
            row.lastRun.toString().toLowerCase().includes(value.toLowerCase()) ||
            row.nextRun.toString().toLowerCase().includes(value.toLowerCase()) ||
            row.isRunning.toString().toLowerCase().includes(value.toLowerCase()) ||
            row.created_at.toString().toLowerCase().includes(value.toLowerCase())
        });
    }
    onDataTableSearch =(e)=>{ 
       this.setState({displayGraph:false});
        if(e.target.value.length >0){
            var result = this.filterOrignalData(e.target.value);
            this.setState({
                data:result,
                totalRows:result.length
            })
        }
        else{
            let data = this.state.orignalData;
            this.setState({
                data:data,
                totalRows:data.length
            })
        }
    }
    showDataTableLoader = (isLoading) => {
        this.setState({
            loading: isLoading,
        });
    }
    showDeleteConfirmationDialog = (id) => {
        this.setState({
            modal:{
                open:true,
                modalComponent:<DeleteSchedule 
                id={id}  
                handleModalClose = {this.handleModalClose} 
                heloperReloadDataTable = {this.helperReloadDataTable} 
                />,
                modalTitle:"Delete Schedule"
            }
        })
    }
    updateDataTable= (data)=>{
        this.setState({
            data: data,
            orignalData: data,
            totalRows: data.length,
            loading: false,
            columns:this.getTablColumns()
        },()=>{
            this.setState({
                isDataTableLoaded:false,
            })
        });
    }
    setIsDataTableLoaded = (isLoaded) =>{
        this.setState({
            isDataTableLoaded:isLoaded
        })
    }
    handleOnAddScheduleButtonClick = (e)=>{
        this.setState({
            modal:{
                open:true,
                modalComponent:<AddSchedule 
                id={0} 
                handleModalClose = {this.handleModalClose} 
                heloperReloadDataTable = {this.helperReloadDataTable} 
                departments={this.state.departments}
                />,
                modalTitle:"Add Schedule"
            }
        })
    }
    handleModalClose = ()=>{
        this.setState((prevState)=>({
            modal:{
                ...prevState.modal,
                open:false
            }
        }))
    }
    render() {
        const { loading, data, totalRows} = this.state;
        let element = 
        element = <b></b>
        return (
            <>
                <Helmet>
                    <title>Ad-Tech | Search Rank</title>
                </Helmet>
                <div style={{display: 'table', tableLayout:'fixed', width:'100%'}} className="searchRankSchedules">
                    <div className="flex justify-between scheduleControls w-full">
                        {this.state.scheduleTime != "" ? <ScheduleTime 
                            scheduleTime = {this.state.scheduleTime}
                            url = "/settings/SchedulerTime"
                        /> : <ScheduleTimeLoader />}
                       
                        <ExportCsv 
                            checkDataUrl = "/sr/checkHistory"
                            className="w-2/6"
                        />
                    </div>
                    <Card className="overflow-hidden">
                        <div className="flex p-5">
                            <div className="font-semibold w-3/12">Schedules</div>
                            <div className="searchDataTable w-9/12 flex justify-end">
                                <div className="border border-gray-300 border-solid flex inputGroup mr-4 px-3 py-1 rounded-full w-7/12 ml-auto">
                                    <input type="text"
                                    className="border-0 flex-1 focus:outline-none font-semibold outline-none px-2 text-xs" placeholder="Search" 
                                    onChange={this.onDataTableSearch}
                                    />
                                    <SearchIcon className="text-gray-300"/>
                                </div>
                                <PrimaryButton
                                btnlabel={"Add Schedule"}
                                variant={"contained"}
                                onClick={this.handleOnAddScheduleButtonClick}
                                />       
                            </div>
                        </div>
                        <div className={clsx("relative w-full dataTableContainer")} >
                            <DataTable
                                className="allASINS"
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
                                progressComponent={<LinearIndeterminate setDatatableLoaded={this.setIsDataTableLoaded}/>}
                                persistTableHead
                                // onRowClicked={this.handleRowClickEvent}
                                // onSort={this.handleOnSortDataTable}
                            />
                        </div>
                    </Card>
                </div>
                <ScrapingModel
                    open = {this.state.modal.open}
                    handleModalClose = {this.handleModalClose}
                    modalComponent ={this.state.modal.modalComponent}
                    modalTitle = {this.state.modal.modalTitle}
                />
            </>
        )
    }
}

export default withStyles(classStyles)(Container)