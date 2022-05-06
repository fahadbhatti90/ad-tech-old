import React, {Component} from 'react';
import DataTable from 'react-data-table-component';
import {makeStyles , withStyles} from '@material-ui/core/styles';
import LinearProgress from '@material-ui/core/LinearProgress';
import Card from '@material-ui/core/Card';
import SearchIcon from '@material-ui/icons/Search';
import "./LabelOverride.scss"
import {
    getTableColumns,
} from './LabelOverrideHelpers';
import {
    GET_ALL_INVENTORY
} from './apiCalls';
import LabelOverrideIO from './LabelOverrideIO/LabelOverrideIO';
import LabelOverrideModel from './LabelOverrideModel';
import AddLabelOverride from './AddLabelOverride';
import LabelOverrideFilter from './LabelOverrideFilter/LabelOverrideFilter';
import {Helmet} from "react-helmet";
import ServerSideDatatable from './../../../general-components/ServerSideDatatable/ServerSideDatatable';
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
class LabelOverride extends Component {
    constructor(props) {
        super(props)
        this.state = {
            id: "",
            data: [],
            orignalData: [],
            loading: false,
            reloadTable: false,
            openModal: false,
            openSMModal: false,
            modal:{
                open:false,
                modalComponent:null,
                modalTitle:""
            },
            selectColName:"all",
            totalRows: 0,
            perPage: 10,
            columns:[],
            selectedCols: [0,1,2,3,4]
        };
            
        this.dataTableRef = React.createRef();
    }

    componentDidMount() {
        this.setState({
            columns:getTableColumns(this.state.selectedCols, this.handleOnColumnClick, this.props.classes),
        })
    }
    helperReloadDataTable = (data) => {
        
        this.dataTableRef.current.helperReloadDataTable();
        // return;
        // let result = data;
        // if(this.state.columns.length < 3){
        //     let cols = this.getFilterColumnNames();
        //     let colName = cols.length > 1 ? "ASIN" : cols[0];
        //     result = this.filterDistinctData(data, colName);
        // }
        
        this.setState((prevState)=>({
            // data: result,
            // orignalData: data,
            // totalRows: result.length,
            loading: false,
            modal:{
                ...prevState.modal,
                open:false
            }
        })); 
    }
    handleOnColumnClick = e => {
        $(e.target).parent().addClass("selectedColumn");
        let attr = $(e.target).attr("attr");
        let type = $(e.target).attr("type");
        let labelOverride = $(e.target).attr('orignalattritbute')
        let alias = $(e.target).attr('alias')
        this.setState({
            modal:{
                open:true,
                modalComponent:<AddLabelOverride id={0} handleModalClose = {this.handleModalClose} heloperReloadDataTable = {this.helperReloadDataTable} ajaxData = {{fkId:attr, type, labelOverride, alias}}/>,
                modalTitle:"Label Override"
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
    getFilterColumnNames = ()=>{
        return this.state.columns.map(column=>column.selector);
    }
    handleOnFilterSelect = (colToShow, selectColName) => {
        this.setState({ 
            selectedCols: colToShow,
            selectColName: selectColName,
        },()=>{
            this.setState({
                columns: getTableColumns(colToShow, this.handleOnColumnClick, this.props.classes)
            })
            this.dataTableRef.current.helperReloadDataTable();
        });
    }
    setReloadDataTableState = (status) => {
        this.setState({
            reloadTable:status
        })
    }
    render() {
        const {selectColName} = this.state;
        return (
            <>
            
                <Helmet>
                    <title>Ad-Tech | Label Override</title>
                </Helmet>
                <LabelOverrideIO helperReloadDataTable = {this.helperReloadDataTable}/>
                <ServerSideDatatable 
                    ref = {this.dataTableRef}
                    url = {GET_ALL_INVENTORY}
                    dataForAjax = {
                        {
                            columnName : selectColName,
                        }
                    }
                    title="Label Override"
                    showButtons
                    buttons = {
                        <>
                            <LabelOverrideFilter 
                                handleOnFilterSelect={this.handleOnFilterSelect}
                            />
                        </>
                    }
                    columns={this.state.columns}
                    setReloadDataTableState = {this.setReloadDataTableState}
                    reloadTable = {this.state.reloadTable}
                    // getResponseData = {this.getResponseData}
                />
                <LabelOverrideModel
                    open = {this.state.modal.open}
                    handleModalClose = {this.handleModalClose}
                    modalComponent ={this.state.modal.modalComponent}
                    modalTitle = {this.state.modal.modalTitle}
                />
            </>
        )
    }
}

export default withStyles(classStyles)(LabelOverride)