import React, {Component} from 'react';
import PrimaryButton from "./../../../../general-components/PrimaryButton";
import TextFieldInput from "./../../../../general-components/Textfield";
import {Grid, Card, Typography, withStyles} from "@material-ui/core";
import {getApiConfig} from "./../apiCalls";
import {useStyles} from "./../../Manage-Users/styles";
import AddApiModal from "./../Api-Config/AddApi/AddApiModal";
import LinearProgress from '@material-ui/core/LinearProgress';
import {Helmet} from "react-helmet";

class ApiConfig extends Component {
    constructor(props) {
        super(props);
        this.state = {
            clientId: "",
            clientSecret: "",
            showAddBtn: false,
            openAddModal: false,
            isProcessing: true
        }
    }

    componentDidMount() {
        this.getApiConfigCall();
    }

    handleModalClose = () => {
        this.setState({
            openAddModal: false
        })
    }

    updateAfterSubmit = () => {
        this.getApiConfigCall();
    }

    handleModalOpen = () => {
        this.setState({
            openAddModal: true
        })
    }
    handleLoginWithAmazon = () => {
        const returnUrl = window.baseUrl + '/apiConfig';
        const SCHEDULED_CRON_URL = 'https://www.amazon.com/ap/oa?client_id=amzn1.application-oa2-client.5b2c41d7822440dc8628610ffafc2488&scope=cpc_advertising:campaign_management&response_type=code&redirect_uri=${returnUrl}';
        axios.get(SCHEDULED_CRON_URL)
            .then(res => {
                debugger;
            })
            .catch(err => {

            });
    }

    getApiConfigCall = () => {
        getApiConfig((data) => {
            if (data) {
                this.setState({
                    clientId: data.client_id,
                    clientSecret: data.client_secret,
                    showAddBtn: false,
                    openAddModal: false,
                    isProcessing: false
                })
            } else {
                this.setState({
                    showAddBtn: true,
                    openAddModal: false,
                    isProcessing: false
                })
            }
        }, (err) => {
            //error
            // this.props.dispatch(showSnackBar());
        });
    }


    tfChangeHandler = (e) => {
        this.setState({
            [e.target.name]: e.target.value
        })
    }

    render() {
        const {classes} = this.props;
        return (
            <>
                <Helmet>
                    <title>Ad-Tech AMS</title>
                </Helmet>
                <div className="flex justify-end">
                    {/*<PrimaryButton*/}
                    {/*    btnlabel={"Account Setup"}*/}
                    {/*    variant={"contained"}*/}
                    {/*    onClick={() => {*/}
                    {/*        console.log("clicked")*/}
                    {/*    }}*/}
                    {/*/>*/}
                    {/*<div className="ml-5">*/}
                    {/*    <div className="ml-5">*/}
                    {/*        <a className="btn-icon btn bg-gradient-warning waves-effect waves-light"*/}
                    {/*           href="https://www.amazon.com/ap/oa?client_id=amzn1.application-oa2-client.5b2c41d7822440dc8628610ffafc2488&scope=cpc_advertising:campaign_management&response_type=code&redirect_uri=https://devapi-adtech.diginc.pk/#/apiConfig">*/}
                    {/*            <i class=" feather icon-plus"></i> Login with Amazon*/}
                    {/*        </a>*/}
                    {/*    </div>*/}

                    {/*</div>*/}
                    {
                        this.state.showAddBtn ?
                            <div className="ml-5">
                                <PrimaryButton
                                    btnlabel={"Add Api Parameter"}
                                    variant={"contained"}
                                    onClick={this.handleModalOpen}
                                />
                            </div>
                            :
                            ""}
                </div>
                <Card classes={{root: classes.card}} className="relative">
                    {/* Header of the Module */}
                    <Typography variant="h6" className={`${classes.pageTitle}`} noWrap>
                        Api Config
                    </Typography>

                    <div className="graphLoader bg-white absolute h-full overflow-hidden w-full top-0 left-0 z-10"
                         style={this.state.isProcessing ? {display: "block"} : {display: "none"}}>
                        <LinearProgress/>
                        <div
                            className="absolute flex font-bold font-mono h-full items-center justify-center overflow-hidden text-1rem text-sm w-full z-10">
                            Processing...
                        </div>
                    </div>

                    <div className="mt-5">
                        <Grid container spacing={2}>
                            <Grid item xs={6}>
                                <label className="text-xs font-normal ml-2">
                                    Client ID
                                </label>
                                <TextFieldInput
                                    disabled={true}
                                    placeholder="Client ID"
                                    id="name"
                                    name="clientId"
                                    type="text"
                                    value={this.state.clientId}
                                    onChange={this.tfChangeHandler}
                                    fullWidth={true}
                                    classesstyle={classes}
                                />
                            </Grid>

                            <Grid item xs={6}>
                                <label className="text-xs font-normal ml-2">
                                    Client Secret
                                </label>

                                <TextFieldInput
                                    disabled={true}
                                    placeholder="Client Secret"
                                    id="name"
                                    name="clientSecret"
                                    type="text"
                                    value={this.state.clientSecret}
                                    onChange={this.tfChangeHandler}
                                    fullWidth={true}
                                    classesstyle={classes}
                                />
                            </Grid>
                        </Grid>
                    </div>
                </Card>

                <AddApiModal
                    open={this.state.openAddModal}
                    modalTitle={"Add Api Parameter"}
                    handleModalClose={this.handleModalClose}
                    updateAfterSubmit={this.updateAfterSubmit}
                />
            </>
        );
    }
}

export default withStyles(useStyles)(ApiConfig);