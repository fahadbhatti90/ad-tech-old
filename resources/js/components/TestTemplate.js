import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import TextFieldInput from "./../general-components/Textfield";
import SingleSelect from "./../general-components/Select";
import MultiSelect from "./../general-components/MultiSelect";
import CheckBox from "./../general-components/CheckBox";
import SvgLoader from "./../general-components/SvgLoader";
import Header from "./../general-components/Header";
import TextButton from "./../general-components/TextButton";
import PrimaryButton from "./../general-components/PrimaryButton";
import Switcher from "./../general-components/Switcher";
import { theme as Theme } from "./../app-resources/theme-overrides/app-theme-overrides";
import { MuiThemeProvider } from "@material-ui/core/styles";
import ProgressLoader from "./../general-components/loader/component";
import CustomizedSnackbars from "./../general-components/snackBar/component";
import { Provider,connect } from 'react-redux'
import IconBtn from "./../general-components/IconBtn";
import store from './../store/configureStore'
import { showLoader,hideLoader } from './../general-components/loader/action';
import { showSnackBar } from './../general-components/snackBar/action';
import { ShowSuccessMsg } from '../general-components/successDailog/actions';
import {ShowFailureMsg} from "./../general-components/failureDailog/actions";
import SuccessDailog from "./../general-components/successDailog/component";
import FailureDailog from "./../general-components/failureDailog/component";
import AsinW from "./../app-resources/svgs/manager/AsinW.svg";
import ModalDialog from '../general-components/ModalDialog';
import FullScreenDialog from '../general-components/FullScreenDialog';
import CssBaseline from '@material-ui/core/CssBaseline';
import EmailChips from "../general-components/EmailCC/EmailChips";

var k = 0;
const Options = [
    {label: "Campaign Value Selection", value: 1, className: 'custom-class'},
    {label: "Profile Value Selection", value: 2, className: 'awesome-class'}
    // more options...
];

export default class TestTemplate extends React.Component {
    constructor(props){
        super(props)
        this.state={
            campagin: "",
            check: false,
            label:"Check Box",
            name:"cb",
            radioValue:"",
            open:true,
            openD: false,
            openModal: false,
            items:[]
        }
    }

    handleClose=()=>{
        this.setState({
            openD: false
        })
    }

    handleOpen=()=>{
        this.setState({
            openD: true
        },()=> {this.props.dispatch(showLoader())})
    }

    handleModalOpen=()=>{
        this.setState({
            openModal: true
        })
    }

    handleModalClose=()=>{
        this.setState({
            openModal: false
        })
    }

    handleDrawerClose=()=>{
        this.props.dispatch(showSnackBar("Demo Success..! ","error"));
        this.props.dispatch(showLoader());
        setTimeout(()=>{
            this.props.dispatch(hideLoader());
            this.props.dispatch(ShowSuccessMsg("Action SuccessFully Done","Successfully Added",true,"Confirmation Message"))
        },1000)
    }

    
    onChangeHandler=(e)=>{
        console.log(e);
        this.setState({
            campagin: e
        },()=>{
            console.log("Updated Value:",this.state.campagin);
        })
    }

    handleChange=(e)=>{
        console.log(e);
       if(k==0){
            this.props.dispatch(showSnackBar("Hmara Msg","info"));
            k +=1
       } else if(k==1){
            this.props.dispatch(showSnackBar("Success","success"));
            k +=1
        }else if(k==2){
            this.props.dispatch(showSnackBar("Warning","warning"));
            k +=1
        }else{
            this.props.dispatch(showSnackBar("Error","error"));
            k=0;
       }
    }

    handleRadioChange=(e)=>{
        const list =()=> (
                <>
                    <li>kia bat hai</li>
                    <li>kia bat hai</li>
                    <li>kia bat hai</li>
                    <li>kia bat hai</li>
                    <li>kia bat hai</li>
                    <li>kia bat hai</li>
                    <li>kia bat hai</li>
                    <li>kia bat hai</li>
                    <li>kia bat hai</li>
                </>
        )
        this.props.dispatch(ShowFailureMsg("Couldn't load the specific Asins ","",true,"",list()))
        this.setState({
            radioValue: e.target.value 
        })
    }
    render() {
        return (
            <div className="container" style={{marginTop: '100px'}}>
                <Header data={{open:true, anchor: "left"}}/>
                <CssBaseline/>
                <ProgressLoader />
                <CustomizedSnackbars/>
                <SuccessDailog/>
                <FailureDailog/>
                <MuiThemeProvider theme={Theme}>
                    {/* <TextFieldInput
                        label="Enter Campaigns"
                        id="campaigns"
                        type="text"
                        value={this.state.value}
                        onChangeHandler = {this.onChangeHandler}
                        fullWidth={true}
                        />
                    <br/> */}
                   <SingleSelect
                        placeholder="Select Campaign"
                        id="select"
                        name="text"
                        value={this.state.campagin}
                        onChangeHandler = {this.onChangeHandler}
                        fullWidth={true}
                        Options={Options}
                        />
                    <br/>

                    <MultiSelect
                        placeholder="Select Campaigns"
                        id="select"
                        name="text"
                        value={this.state.value}
                        onChangeHandler = {this.onChangeHandler}
                        fullWidth={true}
                        Options={Options}
                        />
                    
                    <CheckBox
                        label={this.state.label}
                        checked={this.state.check}
                        onChange={this.handleChange}
                        name={this.state.name}
                        />
                    <br/>
                    <br/>
                    <Switcher
                         selectedValue={this.state.radioValue}
                         handleChange={this.handleRadioChange}
                         label="Male"
                       
                    />
                    <br/>
                    <br/>

                    <EmailChips
                        items={["bilalkhan@waqarJanu.com","saqib@gmail.com"]}
                        getUpdatedItems={(items)=>{this.setState({
                            items
                        })}}
                     />
                        {/* Text Button */}
                        <TextButton
                           BtnLabel={"Cancel"}
                           color="primary"
                           onClick={()=>{alert("Text Button")}}/>

                    <br/>
                    <br/>
                        {/* Contained Default Button */}
                       <TextButton
                           BtnLabel={"Save As Draft"}
                           variant={"contained"}
                           onClick={()=>{alert("Contained Button ")}}/> 
                        {/* Contained Primary Button */}
                    
                    <br/>
                    <br/>
                        <PrimaryButton
                           btnlabel={"launch Campagins"}
                           variant={"contained"}
                           onClick={()=>{alert("Contained Button ")}}/> 
                    <br/>
                    <br/>
                    <IconBtn
                        BtnLabel={"Filter"}
                        variant={"contained"}
                        icon={<SvgLoader
                            src={AsinW}/>}
                        onClick={this.handleDrawerClose}/> 

                    <br/>
                    <br/>
                    <IconBtn
                        BtnLabel={"Open Full Screen Dialog"}
                        variant={"contained"}
                        icon={<SvgLoader
                            src={AsinW}/>}
                        onClick={this.handleOpen}/> 


                    <IconBtn
                        BtnLabel={"Open Modal"}
                        variant={"contained"}
                        icon={<SvgLoader
                            src={AsinW}/>}
                        onClick={this.handleModalOpen}/>  
                
                </MuiThemeProvider>
                {/* Loader */}
                <br/>
                {/* <br/>

                <ModalDialog 
                    open={this.state.openModal}
                    title="My Modal"
                    handleClose ={this.handleModalClose}
                    component={<p> Horr Sunao </p>}
                    maxWidth={"md"}
                    fullWidth={true}
                />
                
                <FullScreenDialog 
                    open={this.state.openD}
                    title="My Dialog"
                    handleClose ={this.handleClose}
                    component={<p> Horr Sunao </p>}
                /> */}


            </div>
        );
    }
}

const mapStateToProps = state =>({})
let App = connect(mapStateToProps)(TestTemplate);
if (document.getElementById('root')) {
    ReactDOM.render( 
    <Provider store={store}>
        <MuiThemeProvider theme={Theme}>
            <App />
        </MuiThemeProvider>
    </Provider>, document.getElementById('root'));
}
