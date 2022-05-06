import React, { Component } from 'react'
import {Helmet} from "react-helmet";

export default class Dashboard extends Component {
    render() {
        return (
            <div>
                <Helmet>
                    <title>Ad-Tech Dashboard</title>
                </Helmet>
                Dashboard
            </div>
        )
    }
}
