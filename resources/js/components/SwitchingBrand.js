import React from 'react'
import { makeStyles } from '@material-ui/core/styles';
import Container from '@material-ui/core/Container';
import Fade from '@material-ui/core/Fade';

const useStyles = makeStyles((theme) => ({
    // necessary for content to be below app bar
    toolbar: theme.mixins.toolbar,
    content: {
        flexGrow: 1,
        padding: 0,
    },
}));

export default function SwitchingBrand() {
    const classes = useStyles();
    return (
        <main className={classes.content} >
            <div className={classes.toolbar} />
            <Container>
                <div className="block w-full text-center">Loading...</div>
            </Container>
        </main>
    )
}
