import React, { Component } from 'react';
import DataTable from 'react-data-table-component';
import clsx from 'clsx';
import PrimaryButton from "./../../../general-components/PrimaryButton";
import { showSnackBar } from './../../../general-components/snackBar/action';
import Card from '@material-ui/core/Card';
import {withStyles} from "@material-ui/core/styles";
import "./styles.scss";
import {styles} from "./styles";
import SearchIcon from '@material-ui/icons/Search';
import {columns} from "./TableContent/DataTablecolumns";
import {LinearIndeterminate} from "./../../../general-components/DT-Linear-ProgressBar/DataTablePB"; 
import {ShowSuccessMsg} from "./../../../general-components/successDailog/actions";
import {getUsersData,deleteManagerCall,checkUserBrands} from "./apiCalls";
import AddUserModal from './AddManager/AddUserModal';
import {connect} from "react-redux";
import ConfirmDelete from "./TableContent/ConfirmDelete";
import BrandsInfo from "./TableContent/BrandsInfo";
import ChangePassword from "./TableContent/ChangePassword";
import DeleteReAssign from "./TableContent/DeleteReAssign";
import {Helmet} from "react-helmet";

class ManageUser extends Component {
    constructor(props) {
        super(props);
        this.state = {
            openAddModal: false,
            confirmMsgModal:false,
            ReAssignMsgModal:false,
            cpModal: false,
            infoModal:false,
            isEdit: false,
            loading: true,
            originalData: [],
            data: [],
            brandOptions:null,
            assignedBrands:[],
            totalRows: 0,
            perPage: 10,
            rowId: null,
            row: null,
            isDataTableReload: false,
            unassignedBrands:null,
            processDelete:false
        }
    }

    reloadData=()=>{
        this.setState({
            loading: true,
            openAddModal:false,
            ReAssignMsgModal:false,
            cpModal:false,
            confirmMsgModal:false,
            isDataTableReload:true,
            processDelete:false
        },()=>{
            this.getUsersDataCall();
        })  
    }

    handleModalClose = () => {
        this.setState({
            openAddModal: false,
            isEdit: false
        })
    }

    openInfoModal=(assignedBrands)=>{
        this.setState({
            infoModal:true,
            assignedBrands
        })
    }

    openChangePasswordModal=(id)=>{
        this.setState({
            rowId: id,
            cpModal:true
        })
    }
    closeChangePasswordModal=()=>{
        this.setState({
            cpModal:false
        })
    }

    closeReAssignModal=()=>{
        this.setState({
            ReAssignMsgModal:false
        })
    }
    
    openReAssignModal=(Id,unassignedBrands)=>{
        this.setState({
            ReAssignMsgModal:true,
            rowId: Id,
            unassignedBrands
        })
    }
    
    closeInfoModal=()=>{
        this.setState({
            infoModal:false
        })
    }

    openAddForm= () => {
        this.setState({
            openAddModal: true,
        })
    }

    editManager=(row)=>{
        this.setState({
            isEdit: true,
            openAddModal: true,
            row: row
        })
    }

    onDataTableSearch = (e) => {
        if (e.target.value.length > 0) {
            let result = this.state.originalData.filter(row => {
                return row.serial.toString().toLowerCase().includes(e.target.value.toLowerCase()) ||
                    row.name.toLowerCase().includes(e.target.value.toLowerCase()) ||
                    row.email.toLowerCase().includes(e.target.value.toLowerCase()) ||
                    row.created_at.toString().toLowerCase().includes(e.target.value.toLowerCase())
            });
            this.setState({
                data: result,
                totalRows: result.length
            })
        } else {
            this.setState({
                data: this.state.originalData,
                totalRows: this.state.originalData.length
            })
        }
    }

    componentDidMount(){
        this.getUsersDataCall();
    }
    /**
     * Manager DataTable data api call
     */
    getUsersDataCall = () => {
        //success
        getUsersData((data,brandOptions) => {
            this.setState({
                brandOptions,
                data,
                originalData: data,
                totalRows: data.length,
                loading: false,
            }).catch(e => {
                this.setState({
                    loading: false,
                });
            });
        }, (err) => {
            //error
            // this.props.dispatch(showSnackBar());
        });
    }

    /**
     * Manager Delete Api Call
     */
    deleteUserCall=(e)=>{
        e.preventDefault();
        this.setState({
            processDelete: true
        })
        deleteManagerCall(this.state.rowId,(data) => {
            this.setState({
                processDelete: false
            })
            this.props.dispatch(ShowSuccessMsg(data.message, "", true, "",this.reloadData()));
        }, (err) => {
            //error
            // this.props.dispatch(showSnackBar());
        })
    }

     /**
     * open confirm Delete Modal
     */
    openConfirmation=(Id)=>{
        checkUserBrands(Id,(status,unassignedBrands) => {
            if(status){
                this.openReAssignModal(Id,unassignedBrands);
            } else{
                this.setState({
                    confirmMsgModal:true,
                    rowId: Id
                })
            }
        }, (err) => {
            //error
            this.props.dispatch(showSnackBar("Internal Server Error ","error"));
        });        
    }

    closeConfirmModal=()=>{
        this.setState({
            confirmMsgModal: false
        })
    }

    render() {
        const {classes} = this.props;
        return (
            <>
            <Helmet>
                <title>Ad-Tech | Manage Users</title>
            </Helmet>
            <div style={{display: 'table', tableLayout: 'fixed', width: '100%'}} className="managerUser">
                <Card className="overflow-hidden" classes={{root: classes.card}}>
                    <div className="flex p-5">
                        <div className="font-semibold w-3/12">Users List</div>
                        <div className="searchDataTable w-9/12">
                            <div
                                className="border border-gray-300 border-solid flex inputGroup mr-4 px-3 py-1 rounded-full w-9/12 ml-auto">
                                <input type="text"
                                       className="border-0 flex-1 focus:outline-none font-semibold outline-none px-2 text-xs"
                                       placeholder="Search"
                                       onChange={this.onDataTableSearch}
                                />
                                <SearchIcon className="text-gray-300"/>
                            </div>
                        </div>

                        <div className="w-2/12">
                            <PrimaryButton
                                btnlabel={"Add Manager"}
                                variant={"contained"}
                                onClick={this.openAddForm}
                            />
                        </div>

                    </div>
                    <div className={clsx("w-full dataTableContainer")}>
                        <DataTable
                            Clicked
                            noHeader={true}
                            wrap={false}
                            responsive={true}
                            columns={columns(this.openConfirmation, this.editManager, this.openChangePasswordModal,this.openInfoModal)}
                            data={this.state.data}
                            pagination
                            paginationTotalRows={this.state.totalRows}
                            progressPending={this.state.loading}
                            progressComponent={<LinearIndeterminate/>}
                            persistTableHead
                        />
                    </div>
                </Card>
                <AddUserModal
                    isEdit={this.state.isEdit}
                    row={this.state.row}
                    open = {this.state.openAddModal}
                    handleModalClose = {this.handleModalClose}
                    modalTitle = {this.state.isEdit?"Update Manager":"Add Manager"}
                    brandOptions={this.state.brandOptions}
                    reloadData={this.reloadData}
                    />
                
                <DeleteReAssign 
                    open={this.state.ReAssignMsgModal}
                    handleModalClose={this.closeReAssignModal}
                    reAssignCallback = {this.ReAssignUserCall}
                    deleteCallback = {this.deleteUserCall}
                    brandsNames = {this.state.unassignedBrands}
                    rowId = {this.state.rowId}
                    reloadData={this.reloadData}
                 />

                <ConfirmDelete
                    open={this.state.confirmMsgModal}
                    handleModalClose={this.closeConfirmModal}
                    deleteCallback = {this.deleteUserCall}
                    isProcessing={this.state.processDelete}/>

                <BrandsInfo
                    title = {"Associated Brands"}
                    open={this.state.infoModal}
                    handleModalClose={this.closeInfoModal}
                    brands = {this.state.assignedBrands}
                />
                 
                <ChangePassword
                    rowId={this.state.rowId}
                    open={this.state.cpModal}
                    handleModalClose={this.closeChangePasswordModal}
                    reloadData={this.reloadData}
                />
            </div>
        </>
        );
    }
}

export default withStyles(styles) (connect(null)(ManageUser));