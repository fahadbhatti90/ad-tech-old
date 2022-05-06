import React, { Component } from 'react';
import "./Events.scss";
import { withStyles } from '@material-ui/core/styles';
import PrimaryButton from "./../../../../general-components/PrimaryButton";
import {GET_ALL_EVENTS} from './apiCalls';
import EventsModal from './EventsModal';
import AddEvent from './Add/AddEvent';
import DeleteEvent from './Delete/DeleteEvent';
import { Helmet } from 'react-helmet';
import { getTableColumns } from './ManualEventsHelper';
import ServerSideDatatable from './../../../../general-components/ServerSideDatatable/ServerSideDatatable';
const classStyles = theme => ({
    mainClass:{

    },
    events: {
    
    },
    eTooltip:{
        color: "#000",
        backgroundColor: "rgb(255 255 255 / 90%)",
        boxShadow: "1px 1px 10px #0000003b",
    },
    eArrow:{
        color: "#fff"
    },
});

class EventsDataTable extends Component {
    constructor(props) {
        super(props)
        this.state = {
            data: [],
            orignalData:[],
            loading: false,
            totalRows: 0,
            perPage: 10,
            columns : [],
            profiles:[],
            isDataTableLoaded:false,
            modal:{
                open:false,
                modalComponent:null,
                modalTitle:""
            }
        };
        
        this.wrapperRef = React.createRef();
        this.dataTableRef = React.createRef();
    }
    componentDidMount() {
        if(!htk.isUserLoggedIn()){
            return;
        }
        const { perPage } = this.state;
    
        this.setState({ loading: true });
    }
    helperReloadDataTable = () => {
        this.dataTableRef.current.helperReloadDataTable();
        this.handleModalClose();
    }
    showDataTableLoader = (isLoading) => {
        this.setState({
            loading: isLoading,
        });
    }
    handleOnAddEventButtonClick = (e)=>{
        this.setState({
            modal:{
                open:true,
                modalComponent:<AddEvent id={0} handleModalClose = {this.handleModalClose} heloperReloadDataTable = {this.helperReloadDataTable} accounts={this.state.profiles}/>,
                modalTitle:"Create Event"
            }
        })
    }
    handleOnDeleteEventButtonClick = (e)=>{
        let id = $(e.target).parents(".eventLogsActions").attr("el-id")
        this.setState({
            modal:{
                open:true,
                modalComponent:<DeleteEvent handleModalClose = {this.handleModalClose} heloperReloadDataTable = {this.helperReloadDataTable} id={id}/>,
                modalTitle:"Delete Event"
            }
        })
    }
    handleOnEditEventButtonClick = (e)=>{
        let id = $(e.target).parents(".eventLogsActions").attr("el-id")
        this.setState({
            modal:{
                open:true,
                modalComponent:<AddEvent  handleModalClose = {this.handleModalClose} heloperReloadDataTable = {this.helperReloadDataTable} accounts={this.state.profiles} id = {id}/>,
                modalTitle:"Edit Event"
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
                    <title>Ad-Tech | Events</title>
                </Helmet>
                <ServerSideDatatable 
                        ref = {this.dataTableRef}
                        url = {GET_ALL_EVENTS}
                        title="Event Logs"
                        showButtons
                        buttons = {
                            <>
                               <PrimaryButton
                                btnlabel={"Add Event"}
                                variant={"contained"}
                                onClick={this.handleOnAddEventButtonClick}
                                /> 
                            </>
                        }
                        columns={getTableColumns(this.props.classes, this.handleOnDeleteEventButtonClick, this.handleOnEditEventButtonClick)}
                        setReloadDataTableState = {this.showDataTableLoader}
                        reloadTable = {this.state.loading}
                        // getResponseData = {this.getResponseData}
                    />
                            {/* <DataTable
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
                                progressComponent={<LinearIndeterminate setDatatableLoaded={this.setIsDataTableLoaded}/>}
                                persistTableHead
                                // onRowClicked={this.handleRowClickEvent}
                                // onSort={this.handleOnSortDataTable}
                            /> */}
                <EventsModal
                    open = {this.state.modal.open}
                    handleModalClose = {this.handleModalClose}
                    modalComponent ={this.state.modal.modalComponent}
                    modalTitle = {this.state.modal.modalTitle}
                />
            </>
        )
    }
};

export default withStyles(classStyles)(EventsDataTable)