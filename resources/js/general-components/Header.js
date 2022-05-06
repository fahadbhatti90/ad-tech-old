import React, { Component } from "react";
import { withStyles } from "@material-ui/core/styles";
import AppBar from "@material-ui/core/AppBar";
import Typography from '@material-ui/core/Typography';
import Toolbar from "@material-ui/core/Toolbar";
import { styles } from "./styles";
import classNames from "classnames";

class Header extends Component {
	render() {
		const { classes } = this.props;
		const { open, anchor } = this.props.data;

		return (
			<AppBar
				className={classNames(classes.appBar, {
					[classes.appBarShift]: open,
					[classes[`appBarShift-${anchor}`]]: open
				})}>

				<Toolbar disableGutters={true}>
					<div className={classes.root}>
                    <Typography variant="h6" className={classes.title}>
                        Module Name
                    </Typography>
					</div>
					
				</Toolbar>
			</AppBar>
		);
	}
}


export default withStyles(styles, { withTheme: true })((Header));
