import React from 'react';
import Drawer from '@material-ui/core/Drawer';
import Hidden from '@material-ui/core/Hidden';
import List from '@material-ui/core/List';
import SideBarLogo from "./SideBarLogo";
import ManagerSideBar from "./ManagerSideBar";
import AdminSideBar from "./AdminSideBar";
import SuperAdminSideBar from "./SuperAdminSideBar";
import {sideBarStyles} from "./../../app-resources/theme-overrides/sideBarStyles";
import { useTheme } from '@material-ui/core/styles';

export default function SideBar(props) {
    let loc = htk.history ? htk.history.location : null;
    const { window } = props;
    const classes = sideBarStyles();
    const theme = useTheme();

    const container = window !== undefined ? () => window().document.body : undefined;
    return (
        <nav className={classes.drawer} aria-label="mailbox folders">
            {/* The implementation can be swapped with js to avoid SEO duplication of links. */}
            <Hidden smUp implementation="css" className="sideBar">
                <Drawer
                    container={container}
                    variant="temporary"
                    anchor={theme.direction === 'rtl' ? 'right' : 'left'}
                    open={props.mobileOpen}
                    onClose={props.handleDrawerToggle}
                    classes={{
                        paper: classes.drawerPaper,
                    }}
                    ModalProps={{
                        keepMounted: true, // Better open performance on mobile.
                    }}
                >      
                    <div className="sideBarDrawer sideBarMobile">
                        <SideBarLogo classes={classes}/>
                        <List>
                        {htk && htk.activeRole && htk.activeRole == 3?
                            <ManagerSideBar/>
                            :htk.activeRole == 2 ?
                            <AdminSideBar/>
                            :
                            <SuperAdminSideBar/>
                        }
                        </List>
                    </div>
                </Drawer>
            </Hidden>
            <Hidden xsDown implementation="css">
                <Drawer

                    classes={{
                        paper: classes.drawerPaper,
                    }}
                    variant="permanent"
                    open
                >
                    <div className="sideBarDrawer sideBarDesktop">
                        <SideBarLogo classes={classes}/>
                        {htk && htk.activeRole && htk.activeRole == 3?
                            <ManagerSideBar/>
                            :htk.activeRole == 2 ?
                            <AdminSideBar/>
                            :
                            <SuperAdminSideBar/>
                            
                        }
                    </div>
                </Drawer>
            </Hidden>
        </nav>
    )
}
