import React from 'react';
import { minUseStyles } from './../styles';
import ContainerLoader from './../../../../general-components/ProgressLoader/ContainerLoader';
import CardHeader from "./../../../../general-components/cardHeader";
import Table from '@material-ui/core/Table';
import TableBody from '@material-ui/core/TableBody';
import TableCell from '@material-ui/core/TableCell';
import TableContainer from '@material-ui/core/TableContainer';
import TableHead from '@material-ui/core/TableHead';
import TableRow from '@material-ui/core/TableRow';
import Paper from '@material-ui/core/Paper';
import {PerfYtdTypecolumns} from "./columns";
import TableFooter from '@material-ui/core/TableFooter';
import { Card } from '@material-ui/core';
import "./styles.scss";
import {generate,commaSeparator} from "./../../../../helper/helper";
import TablePagination from '@material-ui/core/TablePagination';
import Tooltip from "@material-ui/core/Tooltip";

const grandsTotal = [{"Grand_total":"Grand Total","cost":"0","acos_":"0","revenue":"0"}];
export default function PreYTDTable(props) {
  const [page, setPage] = React.useState(0);
  const [rowsPerPage, setRowsPerPage] = React.useState(5);  
  const handleChangePage = (event, newPage) => {
    setPage(newPage);
  };
  
  const classes = minUseStyles();
  const totalCount = props.rows.length;
  const totalPages = Math.floor(props.rows.length/5);
    return (
      <Card classes={{ root: classes.card }} className="mt-1 relative">
            {props.dataType==props.showLoader?
              <ContainerLoader height={30}/>
            :""} 
          <CardHeader 
              heading={props.heading}
              subHeading={props.subHeading}
              reloadApiCall={props.reloadApiCall}
              customClass={"customDivider"}
              name={props.dataType}
          />  
        <TableContainer className="bodyTable" component={Paper}>
          <Table className={classes.table} size="small" aria-label="a dense table">
            <TableHead>
              <TableRow >
              {PerfYtdTypecolumns.map(item=>
                <TableCell className="text-sm font-medium" key={item.label}>{item.label}</TableCell>
                )}
              </TableRow>
            </TableHead>
            <TableBody>
            {props.rows.slice(page * rowsPerPage, page * rowsPerPage + rowsPerPage)
                .map((row, index) => (
                <TableRow key={row.account_name}>
                  <TableCell component="th" scope="row">
                    {row.account_name}
                  </TableCell>
                  <TableCell >
                      <Tooltip placement="top" title={row.revenue} arrow>
                          <span>{commaSeparator(row.revenue)}</span>  
                      </Tooltip>
                    </TableCell>
                  <TableCell >{row.ACOS}</TableCell>
                  <TableCell >
                      <Tooltip placement="top" title={row.cost} arrow>
                          <span>{commaSeparator(row.cost)}</span>  
                      </Tooltip>
                  </TableCell>
                </TableRow>
              ))}
              {
              page == totalPages?
                props.rowsToAdd > 0 ? generate(
                  <TableRow className="opacity-0">
                    <TableCell component="th" scope="row">
                      dummy
                    </TableCell>
                    <TableCell >dummy</TableCell>
                    <TableCell >dummy</TableCell>
                    <TableCell >dummy</TableCell>
                  </TableRow>,props.rowsToAdd ) : null         
                  :null
                  }
            </TableBody>
            <TableFooter>
            { props.rows.length >0?
              props.grands.map((row) => (
                <TableRow key={6}>
                    <TableCell component="th" scope="row">{row.Grand_total}</TableCell>
                    <TableCell >
                      <Tooltip placement="top" title={row.revenue} arrow>
                          <span>{commaSeparator(row.revenue)}</span>  
                      </Tooltip>
                    </TableCell>
                    <TableCell >{row.acos_}</TableCell>
                    <TableCell >
                    <Tooltip placement="top" title={row.cost} arrow>
                          <span>{commaSeparator(row.cost)}</span>  
                      </Tooltip>
                    </TableCell>
                    </TableRow>
                  ))
                  :
                  grandsTotal.map((row) => (
                    <TableRow key={4}>
                    <TableCell component="th" scope="row">{row.Grand_total}</TableCell>
                    <TableCell >{row.revenue}</TableCell>
                    <TableCell >{row.acos_}</TableCell>
                    <TableCell >{row.cost}</TableCell>
                    </TableRow>
                  ))
                }
            </TableFooter>
          </Table>
          <TablePagination
              rowsPerPageOptions={""}
              component={'div'}
              count={totalCount}
              rowsPerPage={rowsPerPage}
              page={page}
              onChangePage={handleChangePage}
            />
        </TableContainer>
      </Card>
      );
  }