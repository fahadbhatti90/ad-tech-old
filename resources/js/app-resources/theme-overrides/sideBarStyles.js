import { makeStyles } from '@material-ui/core/styles';
import React from 'react';

const drawerWidth = 240;
export const sideBarStyles = makeStyles((theme) => ({
    active: {
        backgroundImage: 'linear-gradient(270deg,#9935c3,10%,#571986)'
      },
    root: {
        display: 'flex',
    },
    drawer: {
        [theme.breakpoints.up('sm')]: {
            width: drawerWidth,
            flexShrink: 0,
        },
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
    drawerHeader: {
        display: 'flex',
        alignItems: 'center',
        padding: theme.spacing(0, 1),
        // necessary for content to be below app bar
        ...theme.mixins.toolbar,
        justifyContent: 'center',
    },
    menuButton: {
        marginRight: theme.spacing(2),
        [theme.breakpoints.up('sm')]: {
            display: 'none',
        },
    },
    nested: {
        paddingLeft: theme.spacing(4),
    },
    // necessary for content to be below app bar
    toolbar: theme.mixins.toolbar,
    drawerPaper: {
        width: drawerWidth,
    },
    content: {
        flexGrow: 1,
        padding: 0,
    },
}));