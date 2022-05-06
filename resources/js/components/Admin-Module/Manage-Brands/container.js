import React, { Component } from 'react';
import DataTable from 'react-data-table-component';
import clsx from 'clsx';
import PrimaryButton from "./../../../general-components/PrimaryButton";
import Card from '@material-ui/core/Card';
import {withStyles} from "@material-ui/core/styles";
import "./styles.scss";
import {getBrandsData,deleteBrandCall} from "./apiCalls"
import {styles} from "./../Manage-Users/styles";
import SearchIcon from '@material-ui/icons/Search';
import {columns} from "./TableContent/DataTablecolumns";
import {LinearIndeterminate} from "./../../../general-components/DT-Linear-ProgressBar/DataTablePB"; 
import {connect} from "react-redux";
import AddBrandModal from "./AddBrand/AddBrandModal";
import BrandsInfo  from "./../Manage-Users/TableContent/BrandsInfo";
import ConfirmDelete from "./../Manage-Users/TableContent/ConfirmDelete";
import {ShowSuccessMsg} from "./../../../general-components/successDailog/actions";
import {Helmet} from "react-helmet";

class ManageBrand extends Component {
    constructor(props){
        super(props);
        this.state={
            loading: true,
            openAddModal:false,
            infoModal:false,
            confirmMsgModal:false,
            rowId: null,
            assignedUsers:[],
            originalData: [],
            data: [],
            totalRows: 0,
            perPage: 10,
            isDataTableReload: false,
            isEdit:false
        }
    }


    componentDidMount(){
        this.getBrandsDataCall();
    }

    reloadData=()=>{
        this.setState({
            loading: true,
            openAddModal:false,
            confirmMsgModal:false,
            isDataTableReload:true
        },()=>{
            this.getBrandsDataCall();
        })  
    }

    getBrandsDataCall= () => {
        // success
        getBrandsData((data) => {
            this.setState({
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

    openInfoModal=(assignedUsers)=>{
        this.setState({
            infoModal:true,
            assignedUsers
        })
    }
    closeInfoModal=()=>{
        this.setState({
            infoModal:false
        })
    }

    /**
     * open confirm Delete Modal
     */
    openConfirmation=(Id)=>{
        this.setState({
            confirmMsgModal:true,
            rowId: Id
        })      
    }

    closeConfirmModal=()=>{
        this.setState({
            confirmMsgModal: false
        })
    }
    /**
     * Brand Delete Api Call
     */
    deleteBrand=()=>{
        deleteBrandCall(this.state.rowId,(data) => {
            this.props.dispatch(ShowSuccessMsg(data.message, "", true, "",this.reloadData()));
        }, (err) => {
            //error
            // this.props.dispatch(showSnackBar());
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

    openAddForm= () => {
        this.setState({
            openAddModal: true,
        })
    }
    
    editBrand=(row)=>{
        this.setState({
            isEdit: true,
            openAddModal: true,
            row: row
        })
    }

    handleModalClose = () => {
        this.setState({
            openAddModal: false,
            isEdit: false
        })
    }

    render() {
        const {classes} = this.props;
        return (
            <>
            <Helmet>
                <title>Ad-Tech | Manage Brands</title>
            </Helmet>
            <div style={{display: 'table', tableLayout: 'fixed', width: '100%'}} className="manageBrand">
                <Card className="overflow-hidden" classes={{root: classes.card}}>
                    <div className="flex p-5">
                        <div className="font-semibold w-3/12">Manage Brands List</div>
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
                                btnlabel={"Add Brand"}
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
                            columns={columns(this.openConfirmation, this.editBrand, this.openInfoModal)}
                            data={this.state.data}
                            pagination
                            paginationTotalRows={this.state.totalRows}
                            progressPending={this.state.loading}
                            progressComponent={<LinearIndeterminate/>}
                            persistTableHead
                        />
                    </div>
                </Card>
                <AddBrandModal
                    isEdit={this.state.isEdit}
                    row={this.state.row}
                    open = {this.state.openAddModal}
                    handleModalClose = {this.handleModalClose}
                    modalTitle = {this.state.isEdit?"Update Brand":"Add Brand"}
                    brandOptions={this.state.brandOptions}
                    reloadData={this.reloadData}
                    />
                
                {/* <DeleteReAssign 
                    open={this.state.ReAssignMsgModal}
                    handleModalClose={this.closeReAssignModal}
                    reAssignCallback = {this.ReAssignUserCall}
                    deleteCallback = {this.deleteUserCall}
                    brandsNames = {this.state.unassignedBrands}
                    rowId = {this.state.rowId}
                    reloadData={this.reloadData}
                 /> */}

                <ConfirmDelete
                    open={this.state.confirmMsgModal}
                    handleModalClose={this.closeConfirmModal}
                    deleteCallback = {this.deleteBrand}/>

                <BrandsInfo
                    open={this.state.infoModal}
                    handleModalClose={this.closeInfoModal}
                    brands = {this.state.assignedUsers}
                    isUser={true}
                    title={"Assigned Users"}
                />
            </div>
        </>
        );
    }
}

export default withStyles(styles)(connect(null)(ManageBrand));