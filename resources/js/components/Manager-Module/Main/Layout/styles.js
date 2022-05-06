import React from 'react'
import { makeStyles } from '@material-ui/core/styles';


const drawerWidth = 240;

export const useStyles = makeStyles((theme) => ({
    root: {
        display: 'flex',
    },
    appBar: {
        [theme.breakpoints.up('sm')]: {
            width: `calc(100% - ${drawerWidth}px)`,
            marginLeft: drawerWidth,
        },
        // backgroundColor:background,
        backgroundColor:"transparent",
        boxShadow:"none",
        color:"#000",
    },
    menuButton: {
        marginRight: theme.spacing(2),
        [theme.breakpoints.up('sm')]: {
            display: 'none',
        },
    },
   
}));
