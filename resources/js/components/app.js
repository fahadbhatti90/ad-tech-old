import React, {lazy, Suspense} from 'react';
import clsx from 'clsx';
import ReactDOM from 'react-dom';
import {theme as Theme} from "./../app-resources/theme-overrides/app-theme-overrides";
import {MuiThemeProvider, withStyles} from "@material-ui/core/styles";
import {Provider, connect} from 'react-redux'
import store from './../store/configureStore';
import CssBaseline from '@material-ui/core/CssBaseline';
import ProgressLoader from "./../general-components/loader/component";
import CustomizedSnackbars from "./../general-components/snackBar/component";
import {HashRouter, Switch, Route, Redirect} from 'react-router-dom';
import history from "./../history";
import FailureDailog from "./../general-components/failureDailog/component";
import SuccessDailog from "./../general-components/successDailog/component";
import "./styles.scss";
const LoginScreen = lazy(()=> import('./Login/LoginScreen'))
const EventsDataTable = lazy(()=> import('./Manager-Module/Manager/Events/EventsDataTable'))
const CustomDatatable = lazy(()=> import('./Manager-Module/ProductTable/CustomDatatable'))
const AsinVisuals = lazy(()=> import("./Manager-Module/Asin-Visuals/container"))
const AdVisuals = lazy(()=> import("./Manager-Module/Advertising-Visuals/container"))


import NavigationBars from './../components/Manager-Module/Main/Layout/NavigationBars';
import { MainApp } from './MainApp';
import LinearBuffer from './../Loader';
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
            showLoaderState:false,
        }
        console.log(React.version)
    }
    hideSideBar = (visibility) => {
        this.setState({
            hideSideBar:visibility
        })
    }
    // static getDerivedStateFromProps(nextProps, prevState) {
      
    // }
    render() {
        const {classes} = this.props;
        return (
            <>
                <HashRouter history={history}>
                     {/* {
                            this.state.showLoaderState ?
                            <LinearBuffer/>
                            : null
                     } */}
                     <div className={clsx(classes.root, "MainLayout")}>
                        {
                            !this.state.hideSideBar ?
                            <NavigationBars/>
                            : null
                        } 
                        <CssBaseline/>
                        <MuiThemeProvider theme={Theme}>
                            <ProgressLoader/>
                            <CustomizedSnackbars/>
                            <SuccessDailog/>
                            <FailureDailog/>
                            <Suspense fallback={<LinearBuffer />}>
                                <Switch>
                                    {/* <Route exact path={`/reload`}
                                        component={props => <SwitchingBrand {...props} />}/> */}
                                    {/* Login Route */}
                                    <Route path='/login' exact component={props => <LoginScreen 
                                    hideSidebar={this.hideSideBar} 
                                    isSideBarHidden={this.state.hideSideBar} 
                                    {...props} />}/>
                                    
                                    <Route exact path={`/`}
                                        component={props => MainApp(<CustomDatatable/>, props, "Dashboard", this, this.state, this.showLoader)}/>

                                    <Route exact path={`/events`}
                                        component={props => MainApp(<EventsDataTable/>, props, "Events", this, this.state, this.showLoader)}/>
                                    <Route exact path="/adVisuals"
                                       component={props => MainApp(<AdVisuals/>, props, "Advertising Visuals", this, this.state, this.showLoader)}/>
                                    <Route exact path="/asinVisuals"
                                       component={props => MainApp(<AsinVisuals/>, props, "Asin Performance", this, this.state, this.showLoader)}/>
                                </Switch>
                            </Suspense>
                        </MuiThemeProvider>
                    </div>
                </HashRouter>

            </>
        );
    }
}

let App = connect(null)(RootApp);
let AppNew = withStyles(styles)(App);
if (document.getElementById('root')) {
    ReactDOM.render(
        <Provider store={store}>
            <AppNew/>
        </Provider>,
        document.getElementById('root'));
}
