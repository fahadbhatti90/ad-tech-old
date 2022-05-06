import React from 'react';
import ModalDialog from "./../../../../../general-components/ModalDialog";
import {connect} from "react-redux"
import {ShowSuccessMsg} from "./../../../../../general-components/successDailog/actions";
import {ShowFailureMsg} from "./../../../../../general-components/failureDailog/actions";

function BiddingRuleModal(props) {

    function deleteApiCall() {
        const Url = `${baseUrl}/bidding-rule/store-rules`
        const data = {
            'id': props.id,
            'formType': 'delete',
        }
        const headers = {
            'Content-Type': 'application/json',
        }
        const response = axios.post(Url, data, {
            headers: headers
        }).then((response) => {
            props.dispatch(ShowSuccessMsg(response.message, "Successfully", true, "",props.handleModalClose(response.data.tableData)));
        }).catch((error) => {
            console.log(error);
            props.dispatch(ShowFailureMsg("Internal Server Error ", "", true, ""));
        })
    }

    function editApiCall() {
        console.log(props);
    }

    function ApiCall() {
        if (props.callback == 'editApiCall') {
            editApiCall();
        } else if (props.callback == 'deleteApiCall') {
            deleteApiCall();
        }
    }
    return (
        <div>
            <ModalDialog
                open={props.openModal}
                title={props.modalTitle}
                id={props.id}
                handleClose={props.handleClose}
                cancelEvent={props.handleClose}
                component={props.modalBody}
                maxWidth={props.maxWidth}
                fullWidth={true}
                callback={ApiCall}
                disable = {(props.callback != 'deleteApiCall')}
                modelClass={"biddingRulModel relative"}
            />
        </div>
    );
}

export default connect(null)(BiddingRuleModal)