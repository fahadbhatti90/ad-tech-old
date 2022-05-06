import React, {Component} from 'react';
import DataTable from 'react-data-table-component';
import {makeStyles , withStyles} from '@material-ui/core/styles';
import LinearProgress from '@material-ui/core/LinearProgress';
import Card from '@material-ui/core/Card';
import SearchIcon from '@material-ui/icons/Search';
import PrimaryButton from "./../PrimaryButton";
import "./ServerSideDatatable.scss"
import {
    getTableColumns
} from './ServerSideDatatableHelpers';
import {
    GET_ALL_INVENTORY
} from './apiCalls';
import ServerSideDatatable from './ServerSideDatatable';
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
class ServerSideDatatableContainer extends Component {
    constructor(props) {
        super(props)
        this.state = {
            data: [],
            orignalData: [],
            loading: false,
            isServerSideLoading: false,
            columns:[],
            selectedCols: [0,1,2,3,4],
        };
    }
    componentDidMount() {
        this.setState({
            loading: true,
        });
    }
    getResponseData = (response) => {
        
    }
    handleOnButton1Click = (e) => {

    }
    handleOnButton2Click = (e) => {

    }
    render() {
        const {columns} = this.state;
        return (
            <>
                <ServerSideDatatable 
                    url = {GET_ALL_INVENTORY}
                    dataForAjax = {
                        {
                            columsCustom : "one",
                            columnName : "ASIN",
                        }
                    }
                    title="Label Override"
                    // showButtons
                    buttons = {
                        <>
                            <PrimaryButton
                                btnlabel={"Add Events"}
                                variant={"contained"}
                                customclasses="whitespace-no-wrap"
                                onClick={this.handleOnButton1Click}
                            /><PrimaryButton
                            btnlabel={"Filter"}
                                variant={"contained"}
                                customclasses="whitespace-no-wrap"
                                onClick={this.handleOnButton2Click}
                            />
                        </>
                    }
                    columns={getTableColumns(this.state.selectedCols, this.props.classes)}
                    getResponseData = {this.getResponseData}
                />
            </>
        )
    }
}

export default withStyles(classStyles)(ServerSideDatatableContainer)