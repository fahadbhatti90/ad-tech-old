import React , {useEffect }  from "react";
import { connect } from 'react-redux';
import Layout from './Layout';

const MainSection = (component, props,headerName) => {
    useEffect(() => {
        let auth = props.isLoggedIn;
        if (!auth.isLoggedIn) {
            /**
             * verifying user is logged in
             */
            //redirect to login
            props.history.replace("/login");
        }
      }, []);
    return (
        <React.Fragment>
            <Layout 
                mainComponent={component}
                headerName={headerName}
            />
        </React.Fragment>
    );
};

function mapStateToProps(state) {
    return {
        isLoggedIn: state.IS_LOGGED_IN.isLoggedIn
    }
  }
export default connect(mapStateToProps)(MainSection); 