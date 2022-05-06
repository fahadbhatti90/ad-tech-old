import React, { Component } from 'react'

export default class DataTableLoadingCheck extends Component {
    constructor(props){
        super(props)
    }
    componentDidMount(){
    }
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
