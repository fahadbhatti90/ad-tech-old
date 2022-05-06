import React, {Component} from 'react';
import CheckBox from "./../../../../general-components/CheckBox";
import {Grid} from "@material-ui/core";
import MetCheckBoxes from "./MetCheckBoxes";

import "./../styles.scss";

class Metrics extends Component {
    constructor(props) {
        super(props);
        this.state = {
            check: false,
            MetricsData: [],
        }
    }

    static getDerivedStateFromProps(nextProps, prevState) {
        console.log("nextProps", nextProps)
        //if (nextProps.isAllCheckBoxSelected) {
            return {
                MetricsData: nextProps.metricsCbData,
                //metrixSelected: nextProps.metrixSelected
            }
        //}
        // return null;
    }

    componentDidMount() {

        if (this.props.isAllCheckBoxSelected) {
            this.setState({
                check:this.props.isAllCheckBoxSelected
            })
        }
    }

    selectAllChange = (e) => {

        this.setState({
            check: e.target.checked
        }, () => {

            if (this.state.check) {

                let {MetricsData} = this.state;
                MetricsData = MetricsData.map(item => {
                    let key = Object.keys(item)[0];
                    let data = item[key].map(it => {
                        return {
                            ...it,
                            isChecked: true
                        }
                    })
                    return {[key]: data};
                })
                this.setState({MetricsData}, () => {
                    this.props.updateMetricsData(MetricsData);
                    this.props.updateMetrix("selected");
                    this.props.updateCheckBox(this.state.check);
                })
            } else {

                let {MetricsData} = this.state;
                MetricsData = MetricsData.map(item => {
                    let key = Object.keys(item)[0];
                    let data = item[key].map(it => {
                        return {
                            ...it,
                            isChecked: false
                        }
                    })
                    return {[key]: data};
                })
                this.setState({MetricsData}, () => {
                    this.props.updateMetricsData(MetricsData);
                    this.props.updateMetrix("");
                    this.props.updateCheckBox(this.state.check);
                })
            }
        })
    }


    handleChange = (e, name, idx, pIdx) => {
        let {MetricsData} = this.state;
        MetricsData[pIdx][name][idx]["isChecked"] = e.target.checked;
        this.setState({
            MetricsData,
            check:false

        }, () => {
            let check = this.checkAllMetrics(MetricsData);
            console.log("check", check)
            if (check) {
                this.props.updateMetrix("selected");
                //this.props.updateCheckBox(this.state.check);
            } else {
                this.props.updateMetrix("");
                //this.props.updateCheckBox(this.state.check);
            }
            this.props.updateCheckBox(this.state.check);
            this.props.updateMetricsData(MetricsData);
        })
    }

    checkAllMetrics = (MetricsData) => {
        let size = MetricsData.length;
        MetricsData.map(item => {
            let key = Object.keys(item)[0];
            let check = false;
            item[key].some(it => {
                if (it.isChecked) {
                    check = true;
                }
            })
            if (check) {
                size = size - 1;
            }
        })
        if (size == 0) {
            return true;
        }
        return false;
    }

    render() {
        let {MetricsData} = this.state;
        return (
            <div>
                <fieldset className="border ">
                    <legend>Select Metrics <span className="required-asterisk">*</span></legend>
                    <div className="selectAll">
                        <CheckBox
                            label="Select All Metrics"
                            checked={this.state.check}
                            size="small"
                            onChange={this.selectAllChange}
                            name={this.state.name}
                            class="selectAllMetrics"
                        />
                    </div>
                    <div>
                        {MetricsData.length > 0 ?
                            <MetCheckBoxes
                                MetricsData={this.state.MetricsData}
                                handleChange={this.handleChange}/>
                            :
                            <div className="metricsDiv" style={{paddingTop: '15%'}}>
                                <div className="text-center">No Metrics Available</div>
                            </div>
                        }
                    </div>
                </fieldset>
                <div className="error pl-3">{this.props.errors.selectMetrix}</div>
            </div>
        );
    }
}

export default Metrics;