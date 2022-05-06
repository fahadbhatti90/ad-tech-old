import React, {useEffect} from 'react';
import clsx from 'clsx';
import ReactDOM from 'react-dom';
import {theme as Theme} from "./../app-resources/theme-overrides/app-theme-overrides";
import {MuiThemeProvider, withStyles} from "@material-ui/core/styles";
import {Provider, connect} from 'react-redux'
import store from './../store/configureStore';
import CssBaseline from '@material-ui/core/CssBaseline';
import Layout from './Manager-Module/Main/Layout';
import LoginScreen from './Login/LoginScreen';
import ProgressLoader from "./../general-components/loader/component";
import CustomizedSnackbars from "./../general-components/snackBar/component";
import {HashRouter, Switch, Route, Redirect} from 'react-router-dom';
import AsinVisuals from "./Manager-Module/Asin-Visuals/container";
import AdVisuals from "./Manager-Module/Advertising-Visuals/container";
import DayParting from "./Manager-Module/Day-Parting/container"
import DayPartingHistory from "./Manager-Module/Day-Parting/History/container";
import ManageUser from "./Admin-Module/Manage-Users/container";
import ManageBrand from "./Admin-Module/Manage-Brands/container";
import AdminDashboard from "./Admin-Module/Admin-Dashboard/container";
import SuperAdminDashboard from "./SuperAdmin-Modules/SuperAdmin-Dashboard/container";
import AmsScheduling from "./SuperAdmin-Modules/SuperAdmin-AMS/container";
import SellerCentralSA from "./SuperAdmin-Modules/SuperAdmin-SellerCentral/container";
import Agencies from './SuperAdmin-Modules/Agency/container';
import history from "./../history";
import AdReports from './Manager-Module/Advertising-reports/container';
import FailureDailog from "./../general-components/failureDailog/component";
import SuccessDailog from "./../general-components/successDailog/component";
import BiddingRule from "./Manager-Module/Module/BiddingRule/BiddingRuleMain";
import CustomDatatable from './Manager-Module/ProductTable/CustomDatatable';
import CampaignTaggingContainer from './Manager-Module/CampaignTagging/Container';
import "./styles.scss";
import NotificationPreview from './Manager-Module/Main/Notifications/NotificationPreview/NotificationPreview';
import EventsDataTable from './Manager-Module/Events/EventsDataTable';
import Vendor from './Admin-Module/Vendor-Central/Vendor/container';
import DailySales from './Admin-Module/Vendor-Central/DailySales/container';
import PurchaseOrder from './Admin-Module/Vendor-Central/PurchaseOrder/container';
import DailyInventory from './Admin-Module/Vendor-Central/DailyInventory/container';
import Forecast from './Admin-Module/Vendor-Central/Forecast/container';
import Catalog from './Admin-Module/Vendor-Central/Catalog/container';
import Traffic from './Admin-Module/Vendor-Central/Traffic/container';
import LabelOverride from './Admin-Module/LabelOverride/LabelOverride';
import ExportData from './Admin-Module/Vendor-Central/Export/container';
import ExportSCData from './Admin-Module/Seller-Central/Export/container';
import AddApiConfig from './Admin-Module/Seller-Central/Api-Config/container';
import VerifyData from './Admin-Module/Vendor-Central/VerifyRecord/container';
// import ApiConfig from "./Admin-Module/AMS/Api-Config/container";
import ApiConfig from "./Admin-Module/AMS";
import ExportCsv from "./Admin-Module/AMS/Export-Csv/container";
import Accounts from './Admin-Module/Accounts/Container';
import AsinCollections from './SuperAdmin-Modules/AsinScraping/ScrapingCollection/Container';
import AsinSchedules from './SuperAdmin-Modules/AsinScraping/ScheduleScraping/Container';
import SearchRankSchedules from './SuperAdmin-Modules/SearchRankScraping/ScheduleScraping/Container';
import BuyBoxSchedules from './SuperAdmin-Modules/BuyBoxScraping/Container';
import ServerSideDatatableContainer from '../general-components/ServerSideDatatable/ServerSideDatatableContainer';
import NavigationBars from './../components/Manager-Module/Main/Layout/NavigationBars';
import SwitchingBrand from './SwitchingBrand';
import PageNotFound from './404PageNotFound';
import { MainApp } from './MainApp';
const styles = (theme) => ({
    root: {
        display: 'flex',
        padding: '0 !important',
      },
});

export default class RootApp extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            // isLoggedIn: false,
            hideSideBar:false,
        }
    }
    hideSideBar = (visibility) => {
        this.setState({
            hideSideBar:visibility
        })
    }
    getSideBarState = () => {
        return this.state.hideSideBar;
    }
    render() {
        const {classes} = this.props;
        return (
            <>
                <HashRouter history={history}>
                    <div className={clsx(classes.root, "MainLayout")}>
                        {
                            !this.state.hideSideBar ?
                            <NavigationBars />
                            : null
                        } 
                        <CssBaseline/>
                        <MuiThemeProvider theme={Theme}>
                            <ProgressLoader/>
                            <CustomizedSnackbars/>
                            <SuccessDailog/>
                            <FailureDailog/>
                            <Switch>
                                <Route exact path={`/reload`}
                                    component={props => <SwitchingBrand {...props} />}/>
                                {/* Login Route */}
                                <Route path='/login' exact component={props => <LoginScreen 
                                hideSidebar={this.hideSideBar} 
                                isSideBarHidden={this.state.hideSideBar} 
                                {...props} />}/>
                                
                                <Route exact path={`/`}
                                    component={props => MainApp(<CustomDatatable/>, props, "Dashboard", this, this.state)}/>

                                <Route exact path={`/events`}
                                    component={props => MainApp(<EventsDataTable/>, props, "Events", this, this.state)}/>
                                <Route exact path={`/asin/collections`}
                                    component={props => MainApp(<AsinCollections/>, props, "ASIN Collections", this, this.state)}/>
                                <Route exact path={`/asin/schedules`}
                                    component={props => MainApp(<AsinSchedules />, props, "ASIN Schedules", this, this.state)}/>
                                <Route exact path={`/sr/schedules`}
                                    component={props => MainApp(<SearchRankSchedules />, props, "Search Rank", this, this.state)}/>
                             
                               <Route exact path={`/admin`}
                                       component={props => MainApp(<AdminDashboard />, props, "Dashboard", this, this.state)}/>

                                <Route exact path={`/superAdmin`}
                                       component={props => MainApp(<SuperAdminDashboard />, props, "Dashboard", this, this.state)}/>

                                <Route exact path={`/amsScheduling`}
                                       component={props => MainApp(<AmsScheduling/>, props, "AMS", this, this.state)}/>
                                
                                <Route exact path={`/sellerCentral`}
                                       component={props => MainApp(<SellerCentralSA/>, props, "Seller Central", this, this.state)}/>
                                <Route exact path={`/buybox/scheduling`}
                                    component={props => MainApp(<BuyBoxSchedules />, props, "Buy Box", this, this.state)}/>
                                <Route exact path={`/agencies`}
                                       component={props => MainApp(<Agencies/>, props, "Manage Agencies", this, this.state)}/>
                                       
                                <Route exact path={`/manageUser`}
                                       component={props => MainApp(<ManageUser/>, props, "Manage Users", this, this.state)}/>
                                <Route exact path={`/manageAccounts`}
                                       component={props => MainApp(<Accounts/>, props, "Manage Accounts", this, this.state)}/>
                                <Route exact path={`/dailySales`}
                                       component={props => MainApp(<DailySales/>, props, "Vendor Central", this, this.state)}/>
                                <Route exact path={`/purchaseOrder`}
                                       component={props => MainApp(<PurchaseOrder/>, props, "Vendor Central", this, this.state)}/>
                                <Route exact path={`/dailyInventory`}
                                       component={props => MainApp(<DailyInventory/>, props, "Vendor Central", this, this.state)}/>
                                <Route exact path={`/traffic`}
                                       component={props => MainApp(<Traffic/>, props, "Vendor Central", this, this.state)}/>
                                <Route exact path={`/forecast`}
                                       component={props => MainApp(<Forecast/>, props, "Vendor Central", this, this.state)}/>
                                <Route exact path={`/catalog`}
                                       component={props => MainApp(<Catalog/>, props, "Vendor Central", this, this.state)}/>
                                <Route exact path={`/vendors`}
                                       component={props => MainApp(<Vendor/>, props, "Vendor Central", this, this.state)}/>
                                <Route exact path={`/VcHistory`}
                                       component={props => MainApp(<ExportData/>, props, "Vendor Central", this, this.state)}/>
                                <Route exact path={`/VcDelete`}
                                       component={props => MainApp(<VerifyData/>, props, "Vendor Central", this, this.state)}/>

                                <Route exact path={`/manageBrands`}
                                       component={props => MainApp(<ManageBrand/>, props, "Manage Brands", this, this.state)}/>
                                <Route exact path={`/labeloverride`}
                                       component={props => MainApp(<LabelOverride />, props, "Label Override", this, this.state)}/>
                                
                                <Route exact path={`/apiConfig`}
                                       component={props => MainApp(<ApiConfig/>, props, "AMS", this, this.state)}/>

                                <Route exact path={`/exportCsv`}
                                       component={props => MainApp(<ExportCsv/>, props, "AMS", this, this.state)}/>
                                <Route exact path={`/ScExport`}
                                       component={props => MainApp(<ExportSCData/>, props, "Seller Central", this, this.state)}/>
                                <Route exact path={`/ScApiConfig`}
                                       component={props => MainApp(<AddApiConfig/>, props, "Seller Central", this, this.state)}/>
                                <Route exact path="/compaignTagging" component={props => MainApp(
                                    <CampaignTaggingContainer/>, props, "Campaign Tagging", this, this.state)}/>
                                <Route exact path="/adVisuals"
                                       component={props => MainApp(<AdVisuals/>, props, "Advertising Visuals", this, this.state)}/>
                                <Route exact path="/asinVisuals"
                                       component={props => MainApp(<AsinVisuals/>, props, "Asin Performance", this, this.state)}/>
                                <Route exact path="/emailSchedule"
                                       component={props => MainApp(<AdReports/>, props, "Advertising Reports", this, this.state)}/>
                                <Route exact path="/biddingRule"
                                       component={props => MainApp(<BiddingRule/>, props, "BiddingRule", this, this.state)}/>
                                <Route exact path="/dayParting"
                                       component={props => MainApp(<DayParting/>, props, "Day Parting", this, this.state)}/>
                                <Route exact path="/dayPartingHistory" component={props => MainApp(
                                    <DayPartingHistory/>, props, "Day Parting History", this, this.state)}/>
                                <Route exact path="/notification/:notiId" component={props => MainApp(
                                    <NotificationPreview {...props}/>, props, "Notification Preview", this, this.state)}/>
                                    
                                <Route path='*' exact={true} component={props => MainApp(<PageNotFound/>, props, "404 Not Found", this, this.state)} />
                            </Switch>
                        </MuiThemeProvider>
                    </div>
                </HashRouter>

            </>
        );
    }
}

// const mapStateToProps = state => ({
//     isLoggedIn: state.IS_LOGGED_IN.isLoggedIn,
// })

let App = connect(null)(RootApp);
let AppNew = withStyles(styles)(App);
if (document.getElementById('root')) {
    ReactDOM.render(
        <Provider store={store}>
            <AppNew/>
        </Provider>,
        document.getElementById('root'));
}
