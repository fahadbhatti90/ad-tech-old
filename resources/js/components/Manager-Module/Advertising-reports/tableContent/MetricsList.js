import React from "react";
import List from '@material-ui/core/List';
import ListItem from '@material-ui/core/ListItem';
import ListItemText from '@material-ui/core/ListItemText';

export default function MetricsList(props) {
  function mapper(title){
    switch(title){
      case "campaingMetricsString":
        return "Campaign";
      case "adGroupMetricsString":
        return "Ad Group";
      case "productAdsMetricsString":
        return "Product Ads";
      case "keywordMetricsString":
        return "Keyword";
      case "asinsMetricsString":
        return "Asins";

    }
  }
    let title = Object.keys(props.data)[0];
    return (
      <div>
          <fieldset className="border mb-4">
            <legend className="font-semibold text-base">{mapper(title)}</legend>                          
                <List component="nav">
                  <ListItem  key={props.keyValue}>
                    <ListItemText key={props.keyValue} primary={props.data[title]} />
                  </ListItem>
                </List>
            </fieldset>
      </div>
    );
  }