import React, { Component } from 'react';
import LoaderGif from "./../../app-resources/assets/LoaderGif.gif";
import './styles.css';
import { connect } from "react-redux";
import { showSnackBar } from '../snackBar/action';
import SvgLoader from "./../SvgLoader";
class ProgressLoader extends Component {
    render() {
        const showLoader = this.props.showLoader;
        if(!showLoader){
            return null;
        }
        return (
            <div className="loader-container">
                <div className="loader">
                    <SvgLoader src={LoaderGif} height={35}/>
                </div>
            </div>
        );
    }
}

function mapStateToProps(state) {
    return {
        showLoader: state.LOADING_SPINNER.UISettings.showLoader.open
    }
}

export default connect(mapStateToProps,null,null)(ProgressLoader);