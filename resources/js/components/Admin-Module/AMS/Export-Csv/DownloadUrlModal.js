import React from 'react'
import ModalDialog from './../../../../general-components/ModalDialog';
import DownloadUrlDailog from "./DownloadUrl";

const DownloadUrlModal = (props)=>{
   
        return (
            <div className="modelClass">
                <ModalDialog
                    open={props.open}
                    title={props.status?props.modalTitle:""}
                    handleClose={props.handleModalClose}
                    component={<DownloadUrlDailog 
                        handleModalClose={props.handleModalClose}
                        message = {props.message}
                        title={props.title}
                        url={props.url}
                        status={props.status}
                    />}
                    maxWidth={"xs"}
                    fullWidth={true}
                    disable={true}
                    cancel={true}
                    >
                </ModalDialog>
            </div>
        )
}
export default DownloadUrlModal;