import React, {Component} from 'react';
import DataTable from 'react-data-table-component';
import Checkbox from '@material-ui/core/Checkbox';
import {makeStyles} from '@material-ui/core/styles';
import LinearProgress from '@material-ui/core/LinearProgress';
import Card from '@material-ui/core/Card';
import SearchIcon from '@material-ui/icons/Search';
import "./biddingRuleDatatable.scss"
import Button from '@material-ui/core/Button';
import DeleteIcon from '@material-ui/icons/Delete';
import EditOutlinedIcon from '@material-ui/icons/EditOutlined';
import Tooltip from '@material-ui/core/Tooltip';
import ModalDialog from "./../../../../../general-components/ModalDialog";
import BiddingRuleEditComponent from "../Edit/BiddingRuleEdit.js";
import BiddingRuleModal from "./../Edit/Modal";
import BiddingRuleAdd from './../Add/BiddingRuleAdd';
import ActionBtns from "./ActionBtns";

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

export default class BiddingRuleDatatables extends Component {
    constructor(props) {
        super(props)
        this.state = {
            id: "",
            modalTitle: "",
            modalBody: "",
            maxWidth: "sm",
            callback: "",
            data: [],
            orignalData: [],
            loading: false,
            openModal: false,
            openSMModal: false,
            totalRows: 0,
            perPage: 10,
            columns: [
                {
                    name: 'Rule Name',
                    selector: 'ruleName',
                    sortable: true,
                    cell: (row) => {
                        const name = row.ruleName;
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
                }, {
                    name: 'Campaign/Portfolio',
                    selector: 'type',
                    sortable: true,
                    wrap: true,
                }, {
                    name: 'Include',
                    selector: 'list',
                    sortable: true,
                    wrap: true,
                    cell: (row) => {
                        let listValue = row.list;
                        let listItems = listValue.map((value,i) => <li key={i}>{value}</li>);
                        return <Tooltip title={<ul className='list-disc mr-5'>{listItems}</ul>} placement="top" arrow>
                            <Button>List</Button>
                        </Tooltip>
                    }
                }, {
                    name: 'Rule',
                    selector: 'presetName',
                    sortable: true,
                    wrap: true,
                    cell: (row) => {
                        return row.presetName ? row.presetName : "NA"
                    }
                }, {
                    name: 'Frequency',
                    selector: 'frequency',
                    sortable: true,
                    wrap: true,
                }, {
                    name: 'Statement',
                    selector: 'statement',
                    sortable: true,
                    cell: (row) => {
                        return row.statement ? row.statement : "NA"
                    }
                }, {
                    name: 'Action',
                    selector: 'id',
                    sortable: true,
                    cell: row  => <ActionBtns 
                                    row={row}
                                    deleteSchedule={this.handleRowClickEventDelete}
                                    editSchedule = {this.handleRowClickEventEdit}/>,
                    ignoreRowClick: true,
                    allowOverflow: true,
                    button: true,
                    // cell: (row) => {
                    //     return <>
                    //         <Tooltip title="Delete" placement="top" arrow>
                    //             <button onClick={() => this.handleRowClickEventDelete(row.id)}><DeleteIcon/></button>
                    //         </Tooltip>
                    //         <Tooltip title="Edit" placement="top" arrow>
                    //             <button onClick={() => this.handleRowClickEventEdit(row.id)}><EditOutlinedIcon/></button>
                    //         </Tooltip>
                    //     </>
                    // }
                },
            ]
        };
    }

    async componentDidMount() {
        const {perPage} = this.state;
        this.setState({loading: true});
        let fetchUrl = `${baseUrl}/bidding-rule/get-bidding-rule-list`
        const response = await axios.get(fetchUrl)
            .then(response => {
                this.setState({
                    data: response.data,
                    orignalData: response.data,
                    totalRows: response.data.length,
                    loading: false,
                });
            })
            .catch(e => {
                this.setState({
                    loading: true,
                });
                // console.error(e);
            });
    }

    /**
     * This fucntion is used to handle closing modal functionality
     */
    handleModalClose = () => {
        this.setState({
            openModal: false,
            modalBody: '',
            maxWidth: '',
        })
    }

    /**
     * This function is used to open modal
     * @param id
     */
    handleRowClickEventDelete = (id) => {
        this.setState({
            id: id,
            callback: 'deleteApiCall',
            modalTitle: 'Delete Bidding Rule',
            maxWidth: 'xs',
            modalBody: <p style={{textAlign: "center", marginTop: "3px"}}>Do you realy want to delete this record?</p>,
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
            callback: 'editApiCall',
            modalTitle: 'Edit Bidding Rule',
            maxWidth: 'md',
            modalBody: <BiddingRuleEditComponent id={id} handleModalClose={this.handledDataTableUpdate} handleClose={this.handleModalClose}/>,
            openModal: true
        })
    }

    /**
     * This function is used to handle search function on datatable data
     * @param e
     */
    onDataTableSearch = (e) => {
        this.setState({displayGraph: false});
        if (e.target.value.length > 0) {
            var result = this.state.orignalData.filter(row => {
                return row.ruleName.toString().toLowerCase().includes(e.target.value.toLowerCase()) ||
                    row.lookBackPeriod.toLowerCase().includes(e.target.value.toLowerCase()) ||
                    row.statement.toLowerCase().includes(e.target.value.toLowerCase())
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
    handledDataTableUpdate = (data) => {
        console.log(data)
        this.setState({
            data: data,
            orignalData: data,
            totalRows: data.length,
            loading: false,
            openModal: false,
            modalBody: '',
            maxWidth: '',
        });
    }
    render() {
        const {loading, data, totalRows} = this.state;
        return (
            <>
            
                <BiddingRuleAdd 
                    handleModalClose={this.handledDataTableUpdate}/>
               <div className={' mt-12'}></div>
                <div style={{display: 'table', tableLayout: 'fixed', width: '100%'}} className="biddingRuleDatatable ">
                    <Card className="overflow-hidden">
                        <div className="flex p-5">
                            <div className="font-semibold w-3/12">Bidding Rule History</div>
                            <div className="searchDataTable w-9/12">
                                <div className="border border-gray-300 border-solid flex inputGroup mr-4 px-3 py-1 rounded-full w-7/12 ml-auto">
                                    <input type="text"
                                           className="border-0 flex-1 focus:outline-none font-semibold outline-none px-2 text-xs" placeholder="Search"
                                           onChange={this.onDataTableSearch}
                                    />
                                    <SearchIcon className="text-gray-300"/>
                                </div>

                            </div>
                        </div>
                        <div className=" w-full ">
                            <div className="h-full pl-20 w-full"></div>
                            <DataTable
                                className=""
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
                </div>
                <BiddingRuleModal
                    openModal={this.state.openModal}
                    modalTitle={this.state.modalTitle}
                    callback={this.state.callback}
                    id={this.state.id}
                    handleClose={this.handleModalClose}
                    modalBody={this.state.modalBody}
                    maxWidth={this.state.maxWidth}
                    fullWidth={true}
                    handleModalClose={this.handledDataTableUpdate}
                />
            </>
        )
    }
}