import React, {Component} from 'react';
import clsx from 'clsx';
import {makeStyles , withStyles} from '@material-ui/core/styles';
import LinearProgress from '@material-ui/core/LinearProgress';
import DataTable from 'react-data-table-component';
import Card from '@material-ui/core/Card';
import SearchIcon from '@material-ui/icons/Search';
import Tooltip from '@material-ui/core/Tooltip';
import DataTableLoadingCheck from './../DataTableLoadingCheck';
import SvgLoader from "./../../../../general-components/SvgLoader";
import DeleteIcon from '@material-ui/icons/Delete';
import EditIcon from '@material-ui/icons/Edit';
import PrimaryButton from "./../../../../general-components/PrimaryButton";
import "./../Collection.scss"
import {
    getCollectionTableColumns,
} from '../AsinScrapingHelpers';
import {
    getAllCollections
} from './../apiCalls';
import ScrapingModel from './../ScrapingModel';
import AddCollection from './AddCollection';
import {Helmet} from "react-helmet";
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
const colsObj = {
    "ASIN":[
        "ASIN",
        "product_title",
        "overrideLabelProduct",
    ],
    "category_name":[
        "category_name",
        "overrideLabelCategory",
    ],
    "subcategory_name":[
        "subcategory_name",
        "overrideLabelSubCategory",
    ],
    "accountName":[
        "accountName",
        "overrideLabelBrand",
    ],
    "all":[
        "ASIN",
        "product_title",
        "overrideLabelProduct",
        "category_name",
        "overrideLabelCategory",
        "subcategory_name",
        "overrideLabelSubCategory",
        "accountName",
        "overrideLabelBrand",
    ]
}
class Container extends Component {
    constructor(props) {
        super(props)
        this.state = {
            data: [],
            orignalData:[],
            loading: false,
            totalRows: 0,
            perPage: 10,
            columns : getCollectionTableColumns(),
            profiles:[],
            isDataTableLoaded:false,
            modal:{
                open:false,
                modalComponent:null,
                modalTitle:""
            }
        };
        
        this.wrapperRef = React.createRef();
    }
    componentDidMount() {
        if(!htk.isUserLoggedIn()){
            return;
        }
        
        const { perPage } = this.state;
    
        this.setState({ loading: true });
    
        getAllCollections((response)=>{
            this.setState({
                data: response.data,
                orignalData: response.data,
                totalRows: response.data.length,
                loading: false,
                columns:getCollectionTableColumns(),
                modal:{
                    open:false,
                    modalComponent:<AddCollection  id={0} handleModalClose = {this.handleModalClose} heloperReloadDataTable = {this.helperReloadDataTable} accounts={this.state.profiles}/>,
                    modalTitle:"Add Collection"
                }
            }); 
        },(error)=>{

        })
    }
    helperReloadDataTable = (data) => {
        this.setState((prevState)=>({
            data: data,
            orignalData: data,
            totalRows: data.length,
            loading: false,
            modal:{
                ...prevState.modal,
                open:false
            }
        })); 
    }
    filterOrignalData =(value) =>{
        return this.state.orignalData.filter(row => {
            return row.c_name.toLowerCase().includes(value.toLowerCase())||
            row.c_type.toLowerCase().includes(value.toLowerCase())||
            row.asinCount.toString().toLowerCase().includes(value.toLowerCase()) ||
            row.created_at.toString().toLowerCase().includes(value.toLowerCase())
        });
    }
    onDataTableSearch =(e)=>{ 
       this.setState({displayGraph:false});
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
        this.setState({
            data: data,
            orignalData: data,
            totalRows: data.length,
            loading: false,
            columns:getCollectionTableColumns()
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
    handleOnAddCollectionButtonClick = (e)=>{
        this.setState({
            modal:{
                open:true,
                modalComponent:<AddCollection id={0} handleModalClose = {this.handleModalClose} heloperReloadDataTable = {this.helperReloadDataTable} accounts={this.state.profiles}/>,
                modalTitle:"Add Collection"
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
                    <title>Ad-Tech | Asin Scraping</title>
                </Helmet>
                <div style={{display: 'table', tableLayout:'fixed', width:'100%'}} className="ASINCollections">
                    
                    <Card className="overflow-hidden">
                        <div className="flex p-5">
                            <div className="font-semibold w-3/12">Collections</div>
                            <div className="searchDataTable w-9/12 flex justify-end">
                                <div className="border border-gray-300 border-solid flex inputGroup mr-4 px-3 py-1 rounded-full w-7/12 ml-auto">
                                    <input type="text"
                                    className="border-0 flex-1 focus:outline-none font-semibold outline-none px-2 text-xs" placeholder="Search" 
                                    onChange={this.onDataTableSearch}
                                    />
                                    <SearchIcon className="text-gray-300"/>
                                </div>
                                <PrimaryButton
                                btnlabel={"Add Collection"}
                                variant={"contained"}
                                onClick={this.handleOnAddCollectionButtonClick}
                                />       
                            </div>
                        </div>
                        <div className={clsx("relative w-full dataTableContainer")} >
                            <DataTable
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
                            />
                        </div>
                    </Card>
                </div>
                <ScrapingModel
                    open = {this.state.modal.open}
                    handleModalClose = {this.handleModalClose}
                    modalComponent ={this.state.modal.modalComponent}
                    modalTitle = {this.state.modal.modalTitle}
                />
            </>
        )
    }
}

export default withStyles(classStyles)(Container)