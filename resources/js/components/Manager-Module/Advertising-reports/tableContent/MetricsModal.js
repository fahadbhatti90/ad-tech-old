import React from 'react';
import ModalDialog from './../../../../general-components/ModalDialog';
import MetricContent from "./MetricContent";

export default function MetricsModal(props) {
    return (
        <div>
            <ModalDialog
                open={props.open}
                title="Metrics List"
                handleClose ={props.handleModalClose}
                component={
                    <MetricContent 
                        rowId={props.rowId}
                    />
                }
                maxWidth={"sm"}
                fullWidth={true}
                disable={true}
                cancel={true}
                >
            </ModalDialog>
        </div>
    );
};