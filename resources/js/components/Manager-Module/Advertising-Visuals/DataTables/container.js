import React, { Component } from 'react';
import {Grid} from "@material-ui/core";
import ADTable from "./ADTable";
import StrategyTable from './StrategyTable';
import CustomTable from './CustomTable';
import ProductTable from './ProductTable';
import PreTable from './PreTable';
import PreYTDTable from "./PreYTDTable";
import { MuiThemeProvider } from "@material-ui/core/styles";
import {theme} from "./../styles";

function AdVisualTables(props) {
        return (
            <div className="pt-5">
                <MuiThemeProvider theme={theme}>
                    <Grid container spacing={1}>
                    <Grid item xs={12} sm={12} md={6}>
                        <ADTable 
                            rowsToAdd={props.rowsToAdd}
                            rows={props.AdData}
                            grands={props.AdGrands}
                            heading={"AD TYPE"}
                            subHeading = {"​"}
                            reloadApiCall={props.reloadData}
                            showLoader={props.showADLoader}
                            dataType={6}
                    />
                    </Grid>

                   <Grid item xs={12} sm={12} md={6}>
                        <StrategyTable 
                            rowsToAdd={props.StrrowsToAdd}
                            rows={props.StrData}
                            grands={props.StrGrands}
                            heading={"STRATEGY TYPE"}
                            subHeading = {"​"}
                            reloadApiCall={props.reloadData}
                            showLoader={props.showStrLoader}
                            dataType={7} />                            
                    </Grid>
                
             
                    <Grid item xs={12} sm={12} md={6}>
                        <CustomTable 
                            rowsToAdd={props.CstrowsToAdd}
                            rows={props.CstData}
                            grands={props.CstGrands}
                            heading={"CUSTOM TYPE"}
                            subHeading = {"​"}
                            reloadApiCall={props.reloadData}
                            showLoader={props.showCstLoader}
                            dataType={9} />
                    </Grid>
                   
                    <Grid item xs={12} sm={12} md={6}>
                        <ProductTable 
                            rowsToAdd={props.ProrowsToAdd}
                            rows={props.ProData}
                            grands={props.ProGrands}
                            heading={"PRODUCT TYPE"}
                            subHeading = {"​"}
                            reloadApiCall={props.reloadData}
                            showLoader={props.showProLoader}
                            dataType={8} />
                    </Grid>                   

                    <Grid item xs={12} sm={12} md={6}>
                        <PreTable 
                            rowsToAdd={props.PrerowsToAdd}    
                            rows={props.PreData}
                            grands={props.PreGrands}
                            heading={"PERFORMANCE - PREV 30 DAYS"}
                            subHeading = {"​"}
                            reloadApiCall={props.reloadData}
                            showLoader={props.showPreLoader}
                            dataType={10} />
                    </Grid>
                    <Grid item xs={12} sm={12} md={6}>
                        <PreYTDTable 
                            rowsToAdd={props.PreYTDrowsToAdd}
                            rows={props.PreYTDData}
                            grands={props.PreYTDGrands}
                            heading={"PERFORMANCE - YTD"}
                            subHeading = {"​"}
                            reloadApiCall={props.reloadData}
                            showLoader={props.showPreYTDLoader}
                            dataType={11} /> 
                    </Grid>                   
                </Grid>                
                </MuiThemeProvider>
            </div>
        );
}

export default AdVisualTables;