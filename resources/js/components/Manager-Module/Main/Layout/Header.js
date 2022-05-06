import React, { Component } from 'react';
import clsx from 'clsx';
import {connect} from "react-redux";
import {getAllNavigationData} from './../GBS/apiCalls';
import {currentDate, getUserName} from "./../../../../helper/helper";
import {SetNotificationCount} from './../../../../general-components/Notification/actions'
import PageHeading from './PageHeading';
import SvgLoader from '../../../../general-components/SvgLoader';
import userIcon from "../../../../app-resources/svgs/manager/user.svg";
import AppBarScearchElement from './../Notifications/AppBarScearchElement';
import AppBar from '@material-ui/core/AppBar';
import IconButton from '@material-ui/core/IconButton';
import MenuIcon from '@material-ui/icons/Menu';
import Toolbar from '@material-ui/core/Toolbar';
import ExpandMore from '@material-ui/icons/ExpandMore';
import Menu from '@material-ui/core/Menu';
import MenuItem from '@material-ui/core/MenuItem';
import {logout} from './../../../Login/actions';
import {SetIsAdmin} from './../../../SideBars/redux/sideBarAction';
import {logoutFromBackend} from './apiCalls';
import {SetLoginStatus} from './../../../../general-components/HeaderRedux/actions';
import GBSModel from './../GBS/GBSModel';
import GBSwitcherControls from './../GBS/GBSwitcherControls';

class Header extends Component {
    constructor(props){
        super(props);
        this.state = {
            addGBS:false,
            parentBrands:{
                brands:[],
                selected:0,
                selectedBrandName:"No Brand Assigned",
            },
            default:{
                selected:0,
                selectedBrandName:"No Brand Assigned",
            },
            modal:{
                open:false,
                modalComponent:null,
                modalTitle:""
            },
            anchorEl:null,
            showPreloader:false,
        }
    }
    
    componentDidMount(){
        if(!htk.isUserLoggedIn() && htk.activeRole != 3 && htk.isSuperAdmin()){
            return;
        }
        this.getLatestNavigationInfo(parseInt(htk.activeRole));
    }
     
    handleParentBrandButtonClick = (e) =>{
        this.setState((prevState)=>({
            modal:{
                ...prevState.modal,
                open:true,
                modalTitle:"Parent Brand Switcher"
            },
        }));
        
        getAllNavigationData(
            {
                switchingPortalTo: parseInt(htk.activeRole)
            },
            (response)=>{
                let res = response.data;
                this.props.dispatch(SetNotificationCount(res.notiCount));
                if (res.status && res.data.length > 0) {
                    let parentBrands = {
                        brands: res.data
                    }
                    this.setState((prevState)=>({
                        modal:{
                            ...prevState.modal,
                            modalComponent:<GBSwitcherControls 
                                id={0} 
                                handleModalClose = {this.handleModalClose} 
                                propHandlerForModelClosing = {this.propHandlerForModelClosing} 
                                parentBrands={parentBrands} 
                                selectedBrandId={res.selected}
                                selectedBrandName={res.selectedBrandName}
                                setSelectedBrand={this.setSelectedBrand}
                            />,
                        },
                        parentBrands: {
                            ...prevState.parentBrands,
                            brands:res.data,
                            selected:res.selected,
                            selectedBrandName:res.selectedBrandName,
                        },
                        default:{
                            selected: res.selected,
                            selectedBrandName: res.selectedBrandName,
                        }
                    }));
                }     
                this.setState({
                    showPreloader:false,
                });
            },
            (error)=>{
                this.setState({
                    showPreloader:false,
                });
                console.log("Error While Fetching Parent Brands => ",error)
            }
        );
    }
    handleSetAnchorEl = (event) => {
      this.setState({
        anchorEl: event ? event.currentTarget : event
      });
    };
    setSelectedBrand = ({id, name}) => {
        this.setState(prevState => ({
            parentBrands:{
                ...prevState.parentBrands,
                selected: id,
                selectedBrandName: name,
            },
        }));
    }
    propHandlerForModelClosing = () => {
        this.setState(prevState => ({
            default:{
                selected: this.state.parentBrands.selected,
                selectedBrandName: this.state.parentBrands.selectedBrandName,
            }
        }));
    }
    handleModalClose = (e)=>{
        this.setState((prevState)=>({
            modal:{
                ...prevState.modal,
                open:false,
                modalComponent:null,
            }
        }))
    }
    handleClose = (name) => {
        this.handleSetAnchorEl(null);
        if(name == "logout"){
            localStorage.removeItem(htk.constants.IS_ADMIN);
            logoutFromBackend(htk.history,(response)=>{
                this.props.dispatch(logout());
                this.props.dispatch(SetLoginStatus(false));
            },(error)=>{
                console.log(error)
            }); 
        }  else if(name == "portal") {
            if(htk.activeRole == 2){
                htk.activeRole = 3;
                localStorage.setItem(htk.constants.ACTIVE_ROLE,3);
            } else if(htk.activeRole == 3){
                htk.activeRole = 2;
                localStorage.setItem(htk.constants.ACTIVE_ROLE,2);
            }
            this.getLatestNavigationInfo(parseInt(htk.activeRole), true);
            
        }
    }
    getLatestNavigationInfo = (switchingPortalTo = null, isSwitchingPortal = false) => {
        getAllNavigationData(
            {
                switchingPortalTo,
            },
            (response)=>{
                let res = response.data;
                this.props.dispatch(SetNotificationCount(res.notiCount));
                if(switchingPortalTo == 3)
                this.setBrandAndNotiCount(res);
                if(isSwitchingPortal)
                this.portalSwitchingStuff();
            },
            (error)=>{
                console.log("Error While Fetching Parent Brands => ",error)
            }
        );
    }
    portalSwitchingStuff = ()=>{
        htk.history.replace(htk.activeRole == 2 ? "/" : "/admin");
        this.props.dispatch(SetIsAdmin());
    }
    setBrandAndNotiCount = (res) => {
        if (res.status && res.data.length > 0) {
            this.setState((prevState)=>({
                parentBrands: {
                    ...prevState.parentBrands,
                    brands:res.data,
                    selected:res.selected,
                    selectedBrandName:res.selectedBrandName,
                },
                default:{
                    selected: res.selected,
                    selectedBrandName: res.selectedBrandName,
                }
            }));
        }        
    }
    render() {
        const {
            classes, 
            handleDrawerToggle,
            openNotificationPopup,
            setOpenNotificationPopup,
         } = this.props;
            
        let isAdmin = htk.getLocalStorageObjectDataById(htk.constants.IS_ADMIN);
        return (
            <>
                <AppBar position="absolute" className={clsx(classes.appBar, "appBar")}>
                    <div className="flex flex-row content-around">
                        <Toolbar className="flex-1 PageTitleContainer">
                            <IconButton
                                color="inherit"
                                aria-label="open drawer"
                                edge="start"
                                onClick={handleDrawerToggle}
                                className={classes.menuButton}
                            >
                                <MenuIcon />
                            </IconButton>
                            <PageHeading />
                        </Toolbar>
                        <AppBarScearchElement
                            openNotificationPopup = {openNotificationPopup}
                            setOpenNotificationPopup = {setOpenNotificationPopup}
                        />
                        <div className="flex flex-1 items-center justify-end userInfoSection">
                            <span className="bg-indigo-800 border-0 h-10 overflow-hidden pt-1 rounded-full userIconContainer w-10">
                                <SvgLoader customClasses="userIcon" src={userIcon} alt="User Icon"/>
                            </span>
                            <span className="flex flex-col ml-4 mr-8 userDetails">
                                <span className="text-xl userName themeNormalFontFamily">
                                    Ad-Tech
                                </span>
                                <span className="font-semibold text-gray-500 text-xs userRole whitespace-no-wrap">
                                    { getUserName() }
                                </span>
                            </span>
                            
                            <span className="border-2 border-gray-500 border-gray-700 border-solid dropDownIcon flex flex-col h-5 items-center justify-center p-1 rounded-full text-gray-700 w-5 cursor-pointer"  aria-controls="simple-menu" aria-haspopup="true" onClick={this.handleSetAnchorEl}>
                                <ExpandMore />
                            </span>
                            <Menu
                                id="simple-menu"
                                anchorEl={this.state.anchorEl}
                                keepMounted
                                open={Boolean(this.state.anchorEl)}
                                onClose={this.handleClose}
                                
                                anchorOrigin={{
                                    vertical: "bottom",
                                    horizontal: "left"
                                }}
                                transformOrigin={{
                                    vertical: "top",
                                    horizontal: "left"
                                }}
                                getContentAnchorEl={null}
                                
                            >
                                {/* <MenuItem onClick={this.handleClose}>Profile</MenuItem>
                                <MenuItem onClick={this.handleClose}>My account</MenuItem> */}
                                {isAdmin?
                                <MenuItem onClick={()=>this.handleClose("portal")}>{htk.activeRole == 2?"Brand Portal":"Admin Portal"}</MenuItem>
                                :""}
                                <MenuItem onClick={()=>this.handleClose("logout")}>Logout</MenuItem>
                            </Menu>
                        </div>
                    </div>
                    <div className="flex items-center justify-between w-full">
                        <div className="appBarDateTime flex items-center mb-2 mt-2 text-xs">
                            <span className="block border border-solid border-gray-500 h-0 line mr-4 w-12"></span>
                            <span className="font-medium infoLabel mr-1 text-gray-500">Show:</span>
                            <span className="date font-bold mr-3">{currentDate()}</span>
                        </div>
                        { htk.activeRole == "3"?
                            <div className="appBarGBS flex items-center mb-2 mt-2 text-xs">
                            <span className="date font-bold mr-3 cursor-pointer" onClick={this.handleParentBrandButtonClick}>
                                {this.state.default.selectedBrandName}
                            </span>
                            <span className="block border border-solid border-gray-500 h-0 line mr-4 w-12"></span> 
                            </div>:""
                        }
                    </div>

                </AppBar>
                <GBSModel
                    open = {this.state.modal.open}
                    handleModalClose = {this.handleModalClose}
                    modalComponent ={this.state.modal.modalComponent}
                    modalTitle = {this.state.modal.modalTitle}
                />
            </>
        )
    }
}

export default connect(null)(Header)