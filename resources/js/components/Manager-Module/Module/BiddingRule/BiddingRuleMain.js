import React, { Component } from 'react'
import { Helmet } from 'react-helmet';
import BiddingRuleDatatables from './biddingRuleDatatable/BiddingRuleDatatables';
export default class BidddingRuleMain extends Component {
    render() {
        const {classes} = this.props;
        return (
            <div>   
                <Helmet>
                    <title>Ad-Tech | Bidding Rule</title>
                </Helmet>
               <BiddingRuleDatatables/>
            </div>
        )
    }
}
