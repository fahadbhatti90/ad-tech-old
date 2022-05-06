import React, {Component} from 'react';
import DataTable from 'react-data-table-component';
import {makeStyles, withStyles} from '@material-ui/core/styles';
import LinearProgress from '@material-ui/core/LinearProgress';
import Card from '@material-ui/core/Card';
import SearchIcon from '@material-ui/icons/Search';
import Button from '@material-ui/core/Button';
import Tooltip from '@material-ui/core/Tooltip';
import '../dayparting.scss';
import {getAllSchedules} from '../apiCalls';
import moment from 'moment';
import EditFormDayParting from "../Edit/EditFormDayParting";
import DayPartingModal from '../DayPartingModal'
import PortfolioCampaignRemove from "../DeleteScheduleOptions"
import StopScheduleComp from "./StopSchedule";
import StartScheduleComp from "./StartSchedule";
import {styles} from "../styles";
import {connect} from "react-redux";
import ActionBtns from "./ActionBtns";

const useStyles = makeStyles(theme => ({
    root: {
        width: '100%',
        '& > * + *': {
            marginTop: theme.spacing(2),
        },
    },
}));


const handleChange = (state) => {
    // You can use setState or dispatch with something like Redux so we can use the retrieved data
};

const LinearIndeterminate = () => {
    const classes = useStyles();
    return (
        <div className={classes.root}>
            <LinearProgress/>
        </div>
    );
};

class DayPartingDataTables extends Component {
    constructor(props) {
        super(props)
        this.state = {
            id: "",
            modalTitle: "",
            modalBody: "",
            maxWidth: "sm",
            data: [],
            orignalData: [],
            loading: false,
            openModal: false,
            openSMModal: false,
            totalRows: 0,
            perPage: 10,
            isDataTableReload: false,
            removeCampaigns: "1",
            columns: [
                {
                    name: 'Schedule Name',
                    selector: 'scheduleName',
                    sortable: true,
                    cell: (row) => {
                        return this.getScheduleName(row.scheduleName);
                    },
                    minWidth: '145px'
                },
                {
                    name: 'Campaign/Portfolio',
                    selector: 'portfolioCampaignType',
                    sortable: true,
                    cell: (row) => {
                        return row.portfolioCampaignType
                    },
                    minWidth: '140px',
                    maxWidth: '200px'
                },
                {
                    name: 'Include',
                    selector: 'Include',
                    sortable: false,
                    wrap: true,
                    cell: (row) => {
                        return this.getTooltipPortfolioCampaign(row);
                    },
                    minWidth: '140px',
                    maxWidth: '200px'
                },
                {
                    name: 'Monday (Start / End)',
                    selector: 'mon',
                    sortable: false,
                    wrap: false,
                    cell: (row) => {
                        return this.getDayPartingStartEndTime(row.mon, row.startTime, row.endTime)
                    },
                    minWidth: '115px',
                },
                {
                    name: 'Tuesday (Start / End)',
                    selector: 'tue',
                    sortable: false,
                    wrap: false,
                    cell: (row) => {
                        return this.getDayPartingStartEndTime(row.tue, row.startTime, row.endTime)
                    },
                    minWidth: '115px',
                },
                {
                    name: 'Wednesday (Start / End)',
                    selector: 'wed',
                    sortable: false,
                    wrap: false,
                    cell: (row) => {
                        return this.getDayPartingStartEndTime(row.wed, row.startTime, row.endTime)
                    },
                    minWidth: '115px',
                },
                {
                    name: 'Thursday (Start / End)',
                    selector: 'thu',
                    sortable: false,
                    wrap: false,
                    cell: (row) => {
                        return this.getDayPartingStartEndTime(row.thu, row.startTime, row.endTime)
                    },
                    minWidth: '115px',
                },
                {
                    name: 'Friday (Start / End)',
                    selector: 'fri',
                    sortable: false,
                    wrap: false,
                    cell: (row) => {
                        return this.getDayPartingStartEndTime(row.fri, row.startTime, row.endTime)
                    },
                    minWidth: '115px',
                },
                {
                    name: 'Saturday (Start / End)',
                    selector: 'sat',
                    sortable: false,
                    wrap: false,
                    cell: (row) => {
                        return this.getDayPartingStartEndTime(row.sat, row.startTime, row.endTime)
                    },
                    minWidth: '115px',
                },
                {
                    name: 'Sunday (Start / End)',
                    selector: 'sun',
                    sortable: false,
                    wrap: false,
                    cell: (row) => {
                        return this.getDayPartingStartEndTime(row.sun, row.startTime, row.endTime)
                    },
                    minWidth: '115px',
                },
                {
                    name: 'Action',
                    selector: 'id',
                    sortable: false,
                    cell: row  => <ActionBtns 
                        row={row}
                        deleteSchedule={this.handleRowClickEventDelete}
                        editSchedule = {this.handleRowClickEventEdit}
                        stopSchedule = {this.handleRowClickEventStop}
                        startSchedule = {this.handleRowClickEventStart}
                    />,
                        ignoreRowClick: true,
                        allowOverflow: true,
                        button: true,
                    },
            ]
        };
    }

    componentDidUpdate(prevProps, prevState, snapshot) {

        if (snapshot !== null) {
            if (this.props.isDataTableReload || this.state.isDataTableLoaded) {
                this.getAllSchedulesFromDb();
            }
            return null;
        }
    }

    getScheduleName = (scheduleName) => {
        const name = scheduleName;
        if (name && name.length > 0) {
            if (name.length > 10) {
                const shortName = name.slice(0, 10) + "...";
                return <Tooltip title={name} placement="top" arrow>
                    <span>{shortName}</span>
                </Tooltip>
            } else {
                return name;
            }
        } else {
            return "NA";
        }
    }

    getTooltipPortfolioCampaign = (row) => {
        switch (row.portfolioCampaignType) {
            case 'Campaign': {
                let listValue = row.campaigns;
                let listItems = listValue.map(
                    (obj, idx) => <li className='list-disc' key={idx}>{obj.name}</li>
                );

                let heading = <div className="font-semibold">Campaign</div>
                let ulList = <ul className='m-1 mr-5 pl-5 pr-3'>{listItems}</ul>
                let allData = <div className={ulList.length>0?"h-32 overflow-auto":""}>
                    {heading}
                    {ulList}
                </div>
                return <>
                    <Tooltip title={allData} placement="top" arrow
                             interactive>
                        <Button>List</Button>
                    </Tooltip>
                </>
                break;
            }

            default: {
                let listPortfolios = row.portfolios;
                let heading = <div className="font-semibold mb-1">Portfolio</div>
                let returnListPf = listPortfolios.map((obj1, idx1) => {
                    let portfolioNames = <div key={idx1} className="ml-3 font-semibold">{obj1.name}</div>
                    const listItems = row.campaigns.map(
                        (obj, idx) => {
                            if (obj.portfolioId === obj1.portfolioId) {
                                return <li className='list-disc' key={idx}>{obj.name}</li>
                            }
                        }
                    );
                    return <>
                        {portfolioNames}
                        <ul key={obj1.id} className="mt-1"> {listItems} </ul>
                    </>
                })
                let allData = <div className={returnListPf.length>0?"h-32 overflow-auto":""}>
                    {heading}
                    {returnListPf.length>0?returnListPf:""}
                </div>
                return <Tooltip title={allData} placement="auto"
                                arrow interactive>
                    <Button>List</Button>
                </Tooltip>
                break;
            }
        }
    }

    getDayPartingStartEndTime = (rowName, startTime, endTime) => {
        let conversionStartTime = moment(startTime, 'HH:mm').format('hh:mm A');
        let conversionEndTime = moment(endTime, 'HH:mm').format('hh:mm A');
        if (rowName == 1) {
            return conversionStartTime + " / " + conversionEndTime;
        }
        return;
    }

    getAllSchedulesFromDb = () => {
        getAllSchedules((data) => {
            if (this.props.isDataTableReload || this.state.isDataTableLoaded) {
                this.props.updateDataTable();
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
        });
    }

    componentDidMount() {

        const {perPage} = this.state;
        this.setState({loading: true});
        this.getAllSchedulesFromDb();
    }

    onDataTableSearch = (e) => {
        if (e.target.value.length > 0) {
            var result = this.state.orignalData.filter(row => {
                return row.scheduleName.toString().toLowerCase().includes(e.target.value.toLowerCase())
                    || row.portfolioCampaignType.toLowerCase().includes(e.target.value.toLowerCase())
                    || row.portfolioCampaignType.toLowerCase().includes(e.target.value.toLowerCase());
            });
            this.setState({
                data: result,
                totalRows: result.length
            })
        } else {
            this.setState({
                data: this.state.orignalData,
                totalRows: this.state.orignalData.length
            })
        }
    }

    /**
     * This function is used to open modal
     * @param id
     */
    handleRowClickEventDelete = (id) => {
        this.setState({
            id: id,
            modalTitle: 'Delete Day Parting Schedule',
            maxWidth: 'sm',
            modalBody: <PortfolioCampaignRemove
                id={id}
                getAllSchedulesFromDb={this.getAllSchedulesFromDb}
                handleModalClose={this.handleModalClose}
            />,
            openModal: true
        })
    }

    handleRowClickEventStart = (id) => {
        this.setState({
            id: id,
            modalTitle: 'Start Day Parting Schedule',
            maxWidth: 'xs',
            modalBody: <StartScheduleComp
                id={id}
                getAllSchedulesFromDb={this.getAllSchedulesFromDb}
                handleModalClose={this.handleModalClose}
            />,
            openModal: true
        })
    }

    handleRowClickEventStop = (id) => {
        this.setState({
            id: id,
            modalTitle: 'Stop Day Parting Schedule',
            maxWidth: 'xs',
            modalBody: <StopScheduleComp
                id={id}
                getAllSchedulesFromDb={this.getAllSchedulesFromDb}
                handleModalClose={this.handleModalClose}
            />,
            openModal: true
        })
    }

    /**
     * This fucntion is used to open Edit Modal
     * @param id
     */
    handleRowClickEventEdit = (id) => {
        this.setState({
            id: id,
            modalTitle: 'Edit Day Parting',
            maxWidth: 'md',
            modalBody: <EditFormDayParting id={id} getAllSchedulesFromDb={this.getAllSchedulesFromDb}
                                           handleModalClose={this.handleModalClose}

            />,
            openModal: true
        })
    }

    handleModalClose = () => {
        this.setState({
            openModal: false,
            modalBody: '',
            maxWidth: 'md',
        })
    }

    render() {
        const {loading, data, totalRows} = this.state;
        return (
            <>
                <div style={{display: 'table', tableLayout: 'fixed', width: '100%'}} className="dayPartingDatatable scrollableDatatable">
                    <Card className="overflow-hidden">
                        <div className="flex p-5">
                            <div className="font-semibold w-3/12">Active Schedules</div>
                            <div className="searchDataTable w-9/12">
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
                        </div>
                        <div className=" w-full ">
                            <div className="h-full pl-20 w-full"></div>
                            <DataTable
                                className="scrollableDatatable"
                                Clicked
                                noHeader={true}
                                wrap={false}
                                responsive={true}
                                columns={this.state.columns}
                                data={data}
                                pagination
                                paginationTotalRows={totalRows}
                                progressPending={loading}
                                progressComponent={<LinearIndeterminate/>}
                                persistTableHead
                            />
                        </div>
                    </Card>
                    <DayPartingModal
                        openModal={this.state.openModal}
                        modalTitle={this.state.modalTitle}
                        id={this.state.id}
                        handleClose={this.handleModalClose}
                        modalBody={this.state.modalBody}
                        maxWidth={this.state.maxWidth}
                        cancelEvent={this.handleModalClose}
                        callback={this.state.callback}
                        fullWidth={true}
                    />
                </div>
            </>
        )
    }
}

export default withStyles(styles)(connect(null)(DayPartingDataTables));
