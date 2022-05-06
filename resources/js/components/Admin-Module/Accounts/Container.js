import React, { Component } from 'react';
import clsx from 'clsx';
import {connect} from "react-redux"
import {ShowSuccessMsg} from "./../../../general-components/successDailog/actions";
import {ShowFailureMsg} from "./../../../general-components/failureDailog/actions";
import DataTable from 'react-data-table-component';
import { makeStyles, withStyles } from '@material-ui/core/styles';
import LinearProgress from '@material-ui/core/LinearProgress';
import Card from '@material-ui/core/Card';
import SearchIcon from '@material-ui/icons/Search';
import Tooltip from '@material-ui/core/Tooltip';
import DataTableLoadingCheck from './DataTableLoadingCheck';
// import SvgLoader from "./../../../general-components/SvgLoader";
import DeleteIcon from '@material-ui/icons/Delete';
import EditIcon from '@material-ui/icons/Edit';
import PrimaryButton from "./../../../general-components/PrimaryButton";
import {getAccounts} from './apiCalls';
import {columns} from './DataTablecolumns';
import AccountsModel from './AccountsModel';
import AddAccounts from './Add/AddAccounts';
import UnAssociateAccount from './Delete/UnAssociateAccount';
import { Helmet } from 'react-helmet';

const useStyles = makeStyles(theme => ({
root: {
    width: '100%',
    '& > * + *': {
    marginTop: theme.spacing(2),
    },
}
}));
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
const LinearIndeterminate = (props) => {
    const classes = useStyles();

    return (
    <div className={classes.root}>
        <LinearProgress />
        <DataTableLoadingCheck setDatatableLoaded ={props.setDatatableLoaded} />
    </div>
    );
};
class Accounts extends Component {
    constructor(props) {
        super(props)
        this.state = {
            overflowIssue:false,
            data: [],
            orignalData:[],
            loading: false,
            totalRows: 0,
            perPage: 10,
            columns : [],
            addFormData:{
                brands:[],
                mwsSellers:[],
                amsProfiles:[],
                vcVendors:[],
            },
            isDataTableLoaded:false,
            modal:{
                open:false,
                modalComponent:null,
                modalTitle:"Add Account"
            }
        };
        
    }

    onMenuOpen=()=>{
        this.setState({
             overflowIssue:true
        })
    }
     
    onMenuClose=()=>{
         this.setState({
             overflowIssue:false
         })
     }

    componentDidMount() {
        if(!htk.isUserLoggedIn()){
            return;
        }
        
        const { perPage } = this.state;
    
        this.setState({ loading: true });
    
        getAccounts((response)=>{
            this.setState({
                data: response.accounts,
                orignalData: response.accounts,
                totalRows: response.accounts.length,
                loading: false,
                addFormData:{
                    brands:response.brands,
                    amsProfiles:response.amsProfile,
                    mwsSellers:response.mwsSeller,
                    vcVendors:response.vcVendor,
                },
            }); 
        },(error)=>{
            this.props.dispatch(ShowFailureMsg(error, "", true, ""));
        })
    }
    helperReloadDataTable = (data) => {
        this.setState((prevState)=>({
            data: data.accounts,
            orignalData: data.accounts,
            totalRows: data.accounts.length,
            loading: false,
            addFormData:{
                brands:data.brands,
                amsProfiles:data.amsProfile,
                mwsSellers:data.mwsSeller,
                vcVendors:data.vcVendor,
            },
            modal:{
                ...prevState.modal,
                open:false
            }
        })); 
    }
    filterOrignalData =(value) =>{
        return this.state.orignalData.filter(row => {
            return row.accountName.toString().toLowerCase().includes(value.toLowerCase()) ||
            row.accountType.toString().toLowerCase().includes(value.toLowerCase()) ||
            row.brandName.toString().toLowerCase().includes(value.toLowerCase()) ||
            row.created_at.toLowerCase().includes(value.toLowerCase())
        });
    }
    onDataTableSearch =(e)=>{ 
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
    updateDataTable= (data)=>{
        let newData = data.sort(function(a, b) {
            return a["Sr.#"] - b["Sr.#"];
        });
        this.setState({
            data: newData,
            orignalData: newData,
            totalRows: newData.length,
            loading: false,
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
    handleOnAddEventButtonClick = (e)=>{
        this.setState({
            modal:{
                open:true,
                modalComponent:<AddAccounts id={0} addFormData={this.state.addFormData} onMenuOpen = {this.onMenuOpen}
                onMenuClose = {this.onMenuClose} handleModalClose = {this.handleModalClose} heloperReloadDataTable = {this.helperReloadDataTable} accounts={this.state.profiles}/>,
                modalTitle:"Associate Account"
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
    showDeleteConfirmationDialog = (id) => {
        this.setState({
            modal:{
                open:true,
                modalComponent:<UnAssociateAccount 
                id={id}  
                handleModalClose = {this.handleModalClose} 
                heloperReloadDataTable = {this.helperReloadDataTable} 
                />,
                modalTitle:"Un-Associate Account"
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
                    <title>Ad-Tech | Accounts</title>
                </Helmet>
                <div style={{display: 'table', tableLayout:'fixed', width:'100%'}} className="productTable ">
                    
                    <Card className="overflow-hidden">
                        <div className="flex p-5">
                            <div className="font-semibold w-3/12">Associated Accounts List</div>
                            <div className="searchDataTable w-9/12 flex justify-end">
                                <div className="border border-gray-300 border-solid flex inputGroup mr-4 px-3 py-1 rounded-full w-7/12 ml-auto">
                                    <input type="text"
                                    className="border-0 flex-1 focus:outline-none font-semibold outline-none px-2 text-xs" placeholder="Search" 
                                    onChange={this.onDataTableSearch}
                                    />
                                    <SearchIcon className="text-gray-300"/>
                                </div>
                                <PrimaryButton
                                btnlabel={"Associate Account"}
                                variant={"contained"}
                                onClick={this.handleOnAddEventButtonClick}
                                />       
                            </div>
                        </div>
                        <div className={clsx("relative w-full dataTableContainer")} >
                            <DataTable
                                className=""
                                Clicked
                                noHeader={true}
                                wrap={false}
                                responsive={true}
                                onChangePage={this.handleOnChangeRowsPerPage}
                                columns={columns(this.showDeleteConfirmationDialog,this.props.classes)}
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
                <AccountsModel
                    open = {this.state.modal.open}
                    overflowIssue={this.state.overflowIssue}
                    handleModalClose = {this.handleModalClose}
                    modalComponent ={this.state.modal.modalComponent}
                    modalTitle = {this.state.modal.modalTitle}
                />
            </>
        )
    }
};

export default withStyles(classStyles)(connect(null)(Accounts))