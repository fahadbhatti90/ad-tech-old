import React, { Component } from 'react';
import clsx from 'clsx';
import DataTable from 'react-data-table-component';
import Card from "@material-ui/core/Card/Card";
import SearchIcon from "@material-ui/core/SvgIcon/SvgIcon";
import PrimaryButton from "./../../../general-components/PrimaryButton";
import {columns} from "./TableContent/DataTablecolumns";
import {LinearIndeterminate} from "./../../../general-components/DT-Linear-ProgressBar/DataTablePB";
import {withStyles} from "@material-ui/core";
import {styles} from "./styles";
import {connect} from "react-redux";
import {getApiConfig, deleteApiConfig} from './apiCalls';
import ConfirmDelete from "./../Manage-Users/TableContent/ConfirmDelete";
import {ShowSuccessMsg} from "./../../../general-components/successDailog/actions";
import {ShowFailureMsg} from "./../../../general-components/failureDailog/actions";
import {Helmet} from "react-helmet";
import AddApiModal from './Api-Config/AddApi/AddApiModal';

class ScApiConfig extends Component {
    constructor(props) {
        super(props);
        this.state={
            loading: true,
            openAddModal:false,
            confirmMsgModal:false,
            rowId: null,
            originalData: [],
            data: [],
            totalRows: 0,
            perPage: 10,
            isDataTableReload: false,
            isEdit:false
        }
    }

    componentDidMount(){
        this.getApiData();
    }

    getApiData = () => {
        //Api function call
        getApiConfig((data) => {
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
        })
    }
    reloadData=()=>{
        this.setState({
            loading: true,
            openAddModal:false,
            isDataTableReload:true
        },()=>{
            this.getApiData();
        })
    }
    openAddForm= () => {
        this.setState({
            openAddModal: true,
            isEdit:false
        })
    }

    editApiConfig=(row)=>{
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
        },()=>{
            this.reloadData()
        })
    }

    deleteApi=(e)=>{
        e.preventDefault();
        deleteApiConfig(this.state.rowId, (message) => {
            this.props.dispatch(ShowSuccessMsg(message, "", true, "", this.closeConfirmModal()));
        }, (errorMessage) => {
            this.props.dispatch(ShowFailureMsg(errorMessage, "", true, ""));
        })
    }

    updateAfterSubmit = () => {
        this.getApiConfigCall();
    }
    onDataTableSearch = (e) => {
        if (e.target.value.length > 0) {
            let result = this.state.originalData.filter(row => {
                return row.merchant_name.toString().toLowerCase().includes(e.target.value.toLowerCase()) ||
                    row.mws_access_key_id.toLowerCase().includes(e.target.value.toLowerCase()) ||
                    row.mws_authtoken.toLowerCase().includes(e.target.value.toLowerCase()) ||
                    row.mws_secret_key.toString().toLowerCase().includes(e.target.value.toLowerCase())||
                    row.seller_id.toString().toLowerCase().includes(e.target.value.toLowerCase())
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
    render() {
        const {classes} = this.props;
        return (
            <>
                <Helmet>
                    <title>Ad-Tech | Seller Central</title>
                </Helmet>
                <div className="flex justify-end">
                    {/*<PrimaryButton*/}
                    {/*    btnlabel={"Account Setup"}*/}
                    {/*    variant={"contained"}*/}
                    {/*    onClick={() => {*/}
                    {/*        console.log("clicked")*/}
                    {/*    }}*/}
                    {/*/>*/}
                    {/*<div className="ml-5">*/}
                    {/*    <div className="ml-5">*/}
                    {/*        <a className="btn-icon btn bg-gradient-warning waves-effect waves-light"*/}
                    {/*           href="https://www.amazon.com/ap/oa?client_id=amzn1.application-oa2-client.5b2c41d7822440dc8628610ffafc2488&scope=cpc_advertising:campaign_management&response_type=code&redirect_uri=https://devapi-adtech.diginc.pk/#/apiConfig">*/}
                    {/*            <i class=" feather icon-plus"></i> Login with Amazon*/}
                    {/*        </a>*/}
                    {/*    </div>*/}

                    {/*</div>*/}
                </div>
             
                <div style={{display: 'table', tableLayout: 'fixed', width: '100%'}} className="manageBrand">
                   
                    <Card className="overflow-hidden" classes={{root: classes.card}}>
                        <div className="flex p-5">
                            <div className="font-semibold w-3/12">AMS API Credentials</div>
                            <div className="searchDataTable w-7/12">
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
                                    btnlabel={"Add API Parameter"}
                                    variant={"contained"}
                                    onClick={this.openAddForm}
                                />
                            </div>

                        </div>
                        <div className={clsx("w-full dataTableContainer")}>
                            <DataTable
                                Clicked
                                className="scrollableDatatable"
                                noHeader={true}
                                wrap={false}
                                responsive={true}
                                columns={columns(this.openConfirmation, this.editApiConfig)}
                                data={this.state.data}
                                pagination
                                paginationTotalRows={this.state.totalRows}
                                progressPending={this.state.loading}
                                progressComponent={<LinearIndeterminate/>}
                                persistTableHead
                            />
                        </div>
                    </Card>
                    <AddApiModal
                        isEdit={this.state.isEdit}
                        row={this.state.row}
                        open = {this.state.openAddModal}
                        handleModalClose = {this.handleModalClose}
                        modalTitle = {this.state.isEdit?"Edit API Parameters":"Add API Parameters"}
                        reloadData={this.reloadData}
                        handleModalClose={this.handleModalClose}
                        updateAfterSubmit={this.reloadData}
                    />

                    <ConfirmDelete
                        open={this.state.confirmMsgModal}
                        handleModalClose={this.closeConfirmModal}
                        deleteCallback = {this.deleteApi}/>
                </div>
            </>
        );
    }
}

export default withStyles(styles)(connect(null)(ScApiConfig));