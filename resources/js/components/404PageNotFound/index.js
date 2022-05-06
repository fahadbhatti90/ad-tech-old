import React from 'react'
import {Helmet} from "react-helmet";
import { makeStyles } from '@material-ui/core/styles';
import pageNotFoundSvg from './../../app-resources/svgs/error/404.svg'
import SvgLoader from "./../../general-components/SvgLoader";
import Container from '@material-ui/core/Container';
import './style.scss'
const useStyles = makeStyles((theme) => ({
    // necessary for content to be below app bar
    toolbar: theme.mixins.toolbar,
    content: {
        flexGrow: 1,
        padding: 0,
    },
}));
export default function index() {
    const classes = useStyles();
    return (
        <>
            <Helmet>
                <title>404 Page Not Found</title>
            </Helmet> 
            {/* <main className={classes.content} >
            <div className={classes.toolbar} />
            <Container> */}
                <div className="block w-full text-center">
                    <SvgLoader customClasses="svg404" src={pageNotFoundSvg} height="auto"/>
                </div>
            {/* </Container> */}
        {/* </main> */}
        </>
    )
}
