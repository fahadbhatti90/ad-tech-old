import React, { Component } from 'react';
import MetricsList from "./MetricsList";
import {getMetricsData} from "./../apicalls";
import ContainerLoader from './../../../../general-components/ProgressLoader/ContainerLoader';

class MetricContent extends Component {
    constructor(props){
        super(props);
        this.state={
            data : [],
            loading: true
        }
    }

    componentDidMount(){
        getMetricsData(this.props.rowId,(data) => {
            //success
            this.setState({
                data,
                loading:false
            })
        },(err) => {
            //error
            this.setState({
               loading:false
            })
            console.log("Failed Response",err);
        });
    }
    render() {
        return (
            <>
                {this.state.loading?<ContainerLoader 
                    height={30} 
                    classStyles={"mt-1"}
                    />
                    :
                    ""}
            { this.state.data.map((item,i) => <MetricsList data={item} keyValue={i} key={i}/>)}
            </>
        );
    }
}

export default MetricContent;