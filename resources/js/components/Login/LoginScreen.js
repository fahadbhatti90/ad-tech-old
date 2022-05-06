import React, { Component } from 'react';
import Grid from '@material-ui/core/Grid';
import { withStyles } from '@material-ui/core/styles';
import {styles} from "./styles"
import Typography from '@material-ui/core/Typography';
import SvgLoader from "./../../general-components/SvgLoader";
import Logo from "./../../app-resources/svgs/login/Logo-color.svg";
import TextFieldInput from "./../../general-components/Textfield";
import Lock from "./../../app-resources/svgs/login/user-icon.svg";
import PermIdentity from "./../../app-resources/svgs/login/password-icon.svg";
import InputAdornment from '@material-ui/core/InputAdornment';
import Checkbox from '@material-ui/core/Checkbox';
import CircleCheckedFilled from '@material-ui/icons/CheckCircle';
import CircleUnchecked from '@material-ui/icons/RadioButtonUnchecked';
import { FormControl } from '@material-ui/core';
import FormControlLabel from '@material-ui/core/FormControlLabel';
import "./styles.scss";
import PrimaryButton from "./../../general-components/PrimaryButton";
import { connect } from 'react-redux';
import {Helmet} from "react-helmet";
import {showSnackBar} from "./../../general-components/snackBar/action";
import {loggedIn} from "./actions";
import {IS_LOGGED_IN_STATUS, USER, ACTIVE_ROLE} from "./../../config/localStorageKeys";
import {validateEmail} from "./../../helper/helper";
import { Redirect } from 'react-router-dom';
import LinearProgress from '@material-ui/core/LinearProgress';

class LoginScreen extends Component {
    constructor(props){
        super(props);
        this.state={
            username: "",
            password: "",
            remember: false,
            isProcessing:false
        }

    }
    componentDidMount(){
        if(!this.props.isSideBarHidden)
        this.props.hideSidebar(true)
    }
    onUserChangeHandler=(e)=>{
        this.setState({
            username: e.target.value
        })
    }

    onPassChangeHandler=(e)=>{
        this.setState({
            password: e.target.value
        })
    }
    
    handleRememberChange=(e)=>{
        this.setState({
            remember: e.target.checked 
        })
    }
    onHandleSubmit=(e)=>{
        e.preventDefault();
        if(this.state.username!="" && this.state.password!="") {
            if(!validateEmail(this.state.username)){
                this.props.dispatch(showSnackBar("Invalid Email", "error"));
            } else {
                this.setState({
                    isProcessing:true
                })
                var that = this;
                axios({
                    method: 'post',
                    url: baseUrl+'/login',
                    data:{
                        email:that.state.username,
                        password:that.state.password,
                        remember:that.state.remember,
                    }, 
                })
                .then(function (response) {
                    //handle success
                    if(response.data.isLogged){
                        that.setState({
                            isProcessing:false
                        })
                        that.props.dispatch(loggedIn());
                        localStorage.setItem(IS_LOGGED_IN_STATUS,"logged");
                        localStorage.setItem(USER,JSON.stringify(response.data.user));
                        localStorage.setItem(ACTIVE_ROLE,JSON.stringify(response.data.activeRole));                        
                        window.htk.activeRole = response.data.activeRole;
                        window.htk.user = response.data.user;
                        
                        if(response.data.activeRole == 3){
                            window.htk.isAdminPortal = false;
                            localStorage.setItem(htk.constants.IS_ADMIN,JSON.stringify(false)); 
                            that.props.history.replace("/");
                        } else if(response.data.activeRole == 2){
                            window.htk.isAdminPortal = true;
                            localStorage.setItem(htk.constants.IS_ADMIN,JSON.stringify(true)); 
                            that.props.history.replace("/admin");
                        } else{
                            window.htk.isAdminPortal = false;
                            localStorage.setItem(htk.constants.IS_ADMIN,JSON.stringify(false)); 
                            that.props.history.replace("/superAdmin");
                        }
                        that.props.hideSidebar(false)
                    } else{
                        that.setState({
                            isProcessing:false
                        })
                        that.props.dispatch(showSnackBar("Invalid Email or Password.", "error"));
                    }

                })
                .catch(function (response) {
                    //handle error
                    that.setState({
                        isProcessing:false
                    })
                    console.log(response);
                    that.props.dispatch(showSnackBar("Invalid Email or Password.", "error"));
                });
            }

        } else{
            this.props.dispatch(showSnackBar("Missing Email or Password.", "warning"));
        }
    }
    render() {
        const {classes} = this.props;
        const isAuthenticated = localStorage.getItem(IS_LOGGED_IN_STATUS);
        return isAuthenticated ? (            
            <Redirect to={{ pathname: '/'}} />

        ):(
        <div className={`${classes.paperContainer} h-screen login`}>
            
            <Helmet>
                <title>AdTech | Login</title>
            </Helmet>
            <form className={`${classes.container} h-screen relative`} onSubmit={this.onHandleSubmit}>
                <div className="graphLoader bg-white absolute h-full overflow-hidden w-full top-0 left-0 z-10" style={this.state.isProcessing?{display:"block", background:"#ffffffe0"}:{display:"none", background:"#ffffffe0"}} >
                    <LinearProgress />
                    <div className="absolute flex font-bold font-mono h-full items-center justify-center overflow-hidden text-1rem text-sm w-full z-10">
                        Processing...
                    </div>
                </div>
                <SvgLoader
                    src={Logo}
                    height="11rem"
                    />
                <Grid container justify="center" spacing={3}>
                    <Grid item xs={12}>
                        <Typography 
                            className={classes.typo}
                            align="center"
                            variant="button" 
                            display="block" 
                            gutterBottom>
                            log in to your account
                        </Typography>
                    </Grid>

                    <Grid item xs={12}>
                        <TextFieldInput
                            type={"text"}
                            onChange={this.onUserChangeHandler}
                            value={this.state.username}
                            fullWidth={true}
                            placeholder={"Enter Username"}
                            margin={"none"}
                            InputProps={
                                { disableUnderline: true,
                                    classes:{
                                        focused: classes.focused,
                                    },
                                startAdornment: (
                                <InputAdornment position="start" className="ml-2">
                                        <SvgLoader
                                        src={PermIdentity}
                                        height={18}
                                        customClasses="inpAdort"
                                        />
                                </InputAdornment>
                                ),
                            }}
                        />
                    </Grid>

                    <Grid item xs={12}>
                        <TextFieldInput
                            type={"password"}
                            onChange={this.onPassChangeHandler}
                            value={this.state.password}
                            fullWidth={true}
                            placeholder={"Password"}
                            margin={"none"}
                            InputProps={
                                { disableUnderline: true,
                                    classes:{
                                        focused: classes.focused,
                                    },
                                startAdornment: (
                                <InputAdornment position="start" className="ml-2">
                                    <SvgLoader
                                        src={Lock}
                                        height={17}
                                        customClasses="inpAdort"
                                        />
                                </InputAdornment>
                                ),
                            }
                        }
                        />
                    </Grid>

                    <Grid item xs={12} style={{textAlign: 'center'}}>
                        <FormControl component="fieldset">
                            <FormControlLabel
                            control={                                    
                                <Checkbox
                                    icon={<CircleUnchecked />}
                                    checkedIcon={<CircleCheckedFilled />}
                                    checked={this.state.remember}
                                    onChange={this.handleRememberChange}
                                    size={"small"}
                                />}
                                label={
                                <p className={classes.formControlLabel}>Remember me</p>}
                            />    
                        </FormControl>
                    </Grid>

                    <Grid item xs={12} style={{textAlign: 'center'}}>
                        <PrimaryButton
                            btnlabel={"Login"}
                            variant={"contained"}
                            type="submit"
                            customclasses={"w-2/4 rounded"}
                            /> 
                    </Grid>
                </Grid>
            </form>
        </div>
        );
    }
}
export default withStyles(styles)(connect()(LoginScreen));