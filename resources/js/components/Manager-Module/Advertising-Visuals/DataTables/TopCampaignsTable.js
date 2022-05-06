import React from 'react';
import { makeStyles } from '@material-ui/core/styles';
import DataTable from 'react-data-table-component';
import "./styles.scss";
import { Card } from '@material-ui/core';
import {TopCampaignsColumns} from "./columns";
import {Typography} from '@material-ui/core';
import RefreshIcon from "@material-ui/icons/Refresh";
import IconButton from '@material-ui/core/IconButton';
import ContainerLoader from './../../../../general-components/ProgressLoader/ContainerLoader';

const optionValues = [ 5,10,15,20,25,30,35,40,45,50];
export default function TopCampaignsTable (props){
    const useStyles = makeStyles(theme => ({
        table: {
          minWidth: 650
        },
        card: {
          borderRadius: 15,
          border: '1px solid #e1e1e3',
          backgroundColor: '#fafafa',
          padding:'0px',
          boxShadow: "none",
          postion: 'absolute'
         }
      }));
      const classes = useStyles();
    return (
        <Card classes={{ root: classes.card }} className="mt-1 relative">
             {props.dataType==props.showLoader?
              <ContainerLoader height={30}/>
            :""} 
            <Typography component={"div"} className="heading p-3">
                {"TOP"} 
                <select 
                    className={`customSelect ${classes.newIcon}`}
                    onChange={props.onTopXCampaignChange}
                    value={props.topXValue}>
                    {optionValues.map(value=>(
                        <option value={value}>{value}</option>
                    ))}    
                </select>
                {"CAMPAIGNS"}
                <IconButton 
                    className="iconBtn" 
                    aria-label="refresh" 
                    size="small"
                    onClick={()=>props.reloadApiCall("TOP CAMPAIGNS")}
                    >
                    <RefreshIcon className="icon" />
                </IconButton>
            </Typography>
            
            <div style={{display: 'table', tableLayout:'fixed', width:'100%'}} className="TopCampaigns">
                <DataTable
                        Clicked
                        noHeader={true}
                        wrap={false}
                        responsive={true}
                        columns={TopCampaignsColumns}
                        data={props.campaignsData}
                        />
            </div>
        </Card>
       );
    };