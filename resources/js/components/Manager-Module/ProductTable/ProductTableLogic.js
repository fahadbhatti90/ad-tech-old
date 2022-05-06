import React, { Component } from 'react'

export default class ProductTableLogic extends Component {
    state = {
        dataForGraph:{
            asin:"",
            productTitle:"",  
        },
        displayGraph:false,
    }
    showGraph = (displayGraph) => {
        this.setState({
            displayGraph
        })
    }
}
