import React, {Component} from 'react';
import DataTable from 'react-data-table-component';
import clsx from 'clsx';
import {withStyles} from "@material-ui/core/styles";
import {LinearIndeterminate} from "./../../../general-components/DT-Linear-ProgressBar/DataTablePB";
import PrimaryButton from "./../../../general-components/PrimaryButton";
import Card from '@material-ui/core/Card';
import {columns} from "./tableContent/DataTablecolumns";
import {getScheduleReportData, deleteSchedule} from "./apicalls";
import "./styles.scss";
import SearchIcon from '@material-ui/icons/Search';
import MetricsModal from './tableContent/MetricsModal';
import {connect} from "react-redux";
import {ShowSuccessMsg} from "./../../../general-components/successDailog/actions";
import {ShowFailureMsg} from "./../../../general-components/failureDailog/actions";
import FormContent from "./AddForm/container";
import AddForm from "./AddForm/AddForm";
import EditForm from "./EditForm/EditForm";
import {styles} from "./styles";
import {Helmet} from "react-helmet";

class AdReports extends Component {
    constructor(props) {
        super(props);
        this.state = {
            openMetricModal: false,
            openFormModal: false,
            loading: true,
            modalTitle:'',
            maxWidth: 'md',
            modalBody:'',
            originalData: [],
            data: [],
            totalRows: 0,
            perPage: 10,
            hidePopUpBtn: true,
            rowId: null,
            isDataTableReload: false,
        }
    }

    updateDataTable = () => {
        this.setState({
            isDataTableReload:false
        })
    }

    viewMetricsClickHandler = (row) => {
        this.setState({
            openMetricModal: true,
            rowId: row.id
        })
    }
    componentDidUpdate(prevProps, prevState, snapshot) {

        if (snapshot !== null) {
            if (this.props.isDataTableReload || this.state.isDataTableLoaded) {
                this.getScheduleReportDataCall();
            }
            return null;
        }
    }
    componentDidMount() {
        this.getScheduleReportDataCall();
    }

    getScheduleReportDataCall = () => {
        getScheduleReportData((data) => {
            if (this.props.isDataTableReload || this.state.isDataTableLoaded) {
                this.updateDataTable();
            }
            this.setState({
                data: data,
                orignalData: data,
                totalRows: data.length,
                loading: false,
            }).catch(e => {
                this.setState({
                    loading: true,
                });

            });
            //success
        }, (err) => {
            //error
            // this.props.dispatch(showSnackBar());
        });
    }

    onDataTableSearch = (e) => {
        if (e.target.value.length > 0) {
            let result = this.state.originalData.filter(row => {
                return row.serial.toString().toLowerCase().includes(e.target.value.toLowerCase()) ||
                    row.reportName.toLowerCase().includes(e.target.value.toLowerCase()) ||
                    row.schedule.toLowerCase().includes(e.target.value.toLowerCase()) ||
                    row.sponsored_type.toString().toLowerCase().includes(e.target.value.toLowerCase()) ||
                    row.report_type.toString().toLowerCase().includes(e.target.value.toLowerCase())
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

    handleModalClose = () => {
        this.setState({
            openMetricModal: false,
        })
    }

    openAddForm = () => {
        this.setState({
            modalTitle: 'Create Schedule',
            openFormModal:true,
            maxWidth: 'md',
            modalBody:<AddForm
                handleModalClose={this.handleFormClose}
                getScheduleReportDataCall={this.getScheduleReportDataCall}
            />
        })
    }
    handleFormClose = () => {

        this.setState({
            openFormModal: !this.state.openFormModal,
            hidePopUpBtn:true,
        })
    }

    deleteCallback = () => {
        this.getScheduleReportDataCall();
    }

    editSchedule = (rowId) => {
        this.setState({
            modalTitle: 'Edit Schedule',
            openFormModal:true,
            maxWidth: 'md',
            modalBody:<EditForm
                rowId={rowId}
                handleModalClose={this.handleFormClose}
                getScheduleReportDataCall={this.getScheduleReportDataCall}
            />
        })
    }

    deleteScheduleCall = (rowId) => {
        this.setState({
            rowId: rowId,
            callback:'deleteFinalSchedule',
            maxWidth: 'sm',
            modalTitle: 'Delete Advertising Schedule',
            modalBody: <p>Do you realy want to delete this record?</p>,
            openFormModal: true
        }, () => {
            this.setState({
                hidePopUpBtn:false
            })
        })
    }

    deleteFinalSchedule = () => {
        deleteSchedule(this.state.rowId, (data) => {
            //success
            this.handleFormClose();
            this.props.dispatch(ShowSuccessMsg(data.message, "", data.status, "", this.getScheduleReportDataCall()));
        }, (err) => {
            //error
            this.props.dispatch(ShowFailureMsg(data.message, "", data.status, "", null));
            // this.props.dispatch(showSnackBar());
        })
    }

    render() {
        const {classes} = this.props;

        return (
            <>
                <Helmet>
                    <title>Ad-Tech Advertising Report</title>
                </Helmet>
                <div style={{display: 'table', tableLayout: 'fixed', width: '100%'}} className="adReport">
                    <Card className="overflow-hidden" classes={{root: classes.card}}>
                        <div className="flex p-5">
                            <div className="font-semibold w-3/12">Scheduled Reports</div>
                            <div className="searchDataTable w-7/12">
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

                            <div className="w-2/12">
                                <PrimaryButton
                                    btnlabel={"Create Schedule"}
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
                                columns={columns(this.viewMetricsClickHandler, this.deleteScheduleCall, this.editSchedule)}
                                data={this.state.data}
                                pagination
                                paginationTotalRows={this.state.totalRows}
                                progressPending={this.state.loading}
                                progressComponent={<LinearIndeterminate/>}
                                persistTableHead
                            />
                        </div>
                    </Card>

                    <MetricsModal
                        open={this.state.openMetricModal}
                        handleModalClose={this.handleModalClose}
                        rowId={this.state.rowId}
                        modalBody={this.state.modalBody}
                        maxWidth={this.state.maxWidth}
                    />


                    <FormContent
                        rowId={this.state.rowId}
                        modalTitle={this.state.modalTitle}
                        open={this.state.openFormModal}
                        modalBody={this.state.modalBody}
                        maxWidth={this.state.maxWidth}
                        hidePopUpBtn={this.state.hidePopUpBtn}
                        handleModalClose={this.handleFormClose}
                        callback={this.deleteFinalSchedule}
                        getScheduleReportDataCall={this.getScheduleReportDataCall}
                    />
                </div>
            </>
        );
    }
}

export default withStyles(styles) (connect(null)(AdReports));