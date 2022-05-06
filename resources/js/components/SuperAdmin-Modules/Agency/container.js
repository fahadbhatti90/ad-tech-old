import React, { Component } from 'react';
import DataTable from 'react-data-table-component';
import clsx from 'clsx';
import {withStyles} from "@material-ui/core/styles";
import SearchIcon from '@material-ui/icons/Search';
import {connect} from "react-redux";
import {Helmet} from "react-helmet";
import {styles} from "../../Admin-Module/Manage-Users/styles";
import {getAgenciesApiData} from "./apiCalls";
import Card from "@material-ui/core/Card/Card";
import {columns} from "./TableContent/DataTablecolumns";
import {LinearIndeterminate} from "../../../general-components/DT-Linear-ProgressBar/DataTablePB";
import AddEditFormModel from "./AddAgency/AddAgencyModel";
import ChangePassword from "./TableContent/ChangePassword";
import moment from "moment";
import PrimaryButton from "../../../general-components/PrimaryButton";


class ManageAgency extends Component {
    constructor(props){
        super(props);
        this.state={
            loading: true,
            openAddModal:false,
            rowId: null,
            data: [],
            originalData: [],
            totalRows: 0,
            perPage: 10,
            isDataTableReload: false,
            isEdit:false,
            isDataMultiple:false,
            cpModal:false,
        }
    }

    componentDidMount(){
        this.getAgenciesData();
    }

    getAgenciesData= () => {
        // success
        getAgenciesApiData((data) => {
            this.setState({
                data,
                originalData: data,
                totalRows: data.length,
                loading: false,
            }, () => {
                if (!this.state.data.length > 0){
                    this.setState({
                        isDataMultiple:true
                    })
                }
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
    reloadData=()=>{
        this.setState({
            loading: true,
            openAddModal:false,
            isDataTableReload:false,
            cpModal:false,
            isDataMultiple:false,
        },()=>{
            this.getAgenciesData();
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
    editAgency=(row)=>{
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
    onDataTableSearch = (e) => {
        if (e.target.value.length > 0) {
            let result = this.state.originalData.filter(row => {
                console.log(
                    'row',
                    row
                )
                return row.name.toString().toLowerCase().includes(e.target.value.toLowerCase()) ||
                    row.email.toLowerCase().includes(e.target.value.toLowerCase()) ||
                    moment(row.created_at.toLowerCase()).format('YYYY-MM-DD').includes(e.target.value.toLowerCase())
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
    render() {
        const {classes} = this.props;
        return (
            <>
                <Helmet>
                    <title>Ad-Tech | Manage Agencies</title>
                </Helmet>
                <div style={{display: 'table', tableLayout: 'fixed', width: '100%'}} className="manageBrand">
                    <Card className="overflow-hidden" classes={{root: classes.card}}>
                        <div className="flex p-5">
                            <div className="font-semibold w-3/12">Agencies List</div>
                            <div className={!this.state.isDataMultiple ? "searchDataTable w-9/12" : "searchDataTable w-7/12"}>
                                <div
                                    className="border border-gray-300 border-solid flex inputGroup mr-4 px-3 py-1 rounded-full w-9/12 ml-auto"
                                >
                                    <input type="text"
                                           className="border-0 flex-1 focus:outline-none font-semibold outline-none px-2 text-xs"
                                           placeholder="Search"
                                           onChange={this.onDataTableSearch}
                                    />
                                    <SearchIcon className="text-gray-300"/>
                                </div>
                            </div>
                            {this.state.isDataMultiple ?
                                <div className="w-2/12">
                                    <PrimaryButton
                                        btnlabel={"Add Agency"}
                                        variant={"contained"}
                                        onClick={this.openAddForm}
                                    />
                                </div>
                                : null
                            }

                        </div>
                        <div className={clsx("w-full dataTableContainer")}>
                            <DataTable
                                Clicked
                                noHeader={true}
                                wrap={false}
                                responsive={true}
                                columns={columns(this.editAgency, this.openChangePasswordModal)}
                                data={this.state.data}
                                pagination
                                paginationTotalRows={this.state.totalRows}
                                progressPending={this.state.loading}
                                progressComponent={<LinearIndeterminate/>}
                                persistTableHead
                            />
                        </div>
                    </Card>
                    <AddEditFormModel
                        isEdit={this.state.isEdit}
                        row={this.state.row}
                        open = {this.state.openAddModal}
                        handleModalClose = {this.handleModalClose}
                        modalTitle = {this.state.isEdit?"Update Agency":"Add Agency"}
                        reloadData={this.reloadData}
                    />
                    <ChangePassword
                        rowId={this.state.rowId}
                        open={this.state.cpModal}
                        handleModalClose={this.closeChangePasswordModal}
                        reloadData={this.reloadData}
                    />
                </div>
            </>

        )
    }
}

export default withStyles(styles)(connect(null)(ManageAgency));