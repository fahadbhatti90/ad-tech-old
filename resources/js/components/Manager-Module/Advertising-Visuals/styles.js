import { createMuiTheme } from "@material-ui/core/styles";
import { red } from "@material-ui/core/colors";
import { makeStyles } from '@material-ui/core/styles';

export const styles = theme => ({
    card: {
        borderRadius: 15,
        border: '1px solid #e1e1e3',
        backgroundColor: '#fffff',
        padding:'20px 25px 30px',
        boxShadow: "none",
        postion: 'absolute'
   },
   pageTitle:{
        fontSize: '1rem',
        fontWeight: 600
   },
   datepickerClass: {
        border: '1px solid #bdbdbd',
        borderRadius: 25,
        overflow: 'hidden',
        zIndex: 1101
    }
});

export const useStyles = makeStyles(theme => ({
    table: {
      minWidth: 650
    },
    card: {
      borderRadius: 15,
      border: '1px solid #e1e1e3',
      backgroundColor: '#fafafa',
      paddingTop: 20,
      boxShadow: "none",
      postion: 'absolute'
     }
  }));

export const minUseStyles = makeStyles(theme => ({
table: {
    minWidth: 450
},
card: {
    borderRadius: 15,
    border: '1px solid #e1e1e3',
    backgroundColor: '#fafafa',
    paddingTop:20,
    boxShadow: "none",
    postion: 'absolute'
    }
}));
const theme = createMuiTheme({
    overrides: {
        MuiTypography:{
            root:{
                margin:12
            }
        },
        MuiTablePagination:{
            selectRoot:{
                display: 'none'
            }
        },
        MuiTableCell:{
            head:{
                fontWeight: 600,
                color: '#868686'
            },
            footer:{
                color: '#0000008a',
                fontSize: '0.70rem',
                fontWeight: 700,
                background: '#f1f1f1'
            }
        },
        MuiTableBody:{
            root:{
                minHeight: 300
            }
        }
    }
})

export { theme };