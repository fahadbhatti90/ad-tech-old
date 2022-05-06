import React, { Component } from 'react'

export default class DataTableLoadingCheck extends Component {
    componentWillUnmount(){
        this.props.setDatatableLoaded(true);
    }
    render() {
        return (
            <> 
            </>
        )
    }
}
