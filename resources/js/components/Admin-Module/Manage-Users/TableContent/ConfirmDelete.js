import React from 'react';
import ModalDialog from './../../../../general-components/ModalDialog';
import "./../styles.scss";
import LinearProgress from '@material-ui/core/LinearProgress';
import TextButton from "./../../../../general-components/TextButton";
import PrimaryButton from "./../../../../general-components/PrimaryButton";

const DeleteForm =(props)=>{
    return(
        <>
            <div className="graphLoader bg-white absolute h-full overflow-hidden w-full top-0 left-0 z-10" style={props.isProcessing?{display:"block"}:{display:"none"}} >
                <LinearProgress />
                <div className="absolute flex font-bold font-mono h-full items-center justify-center overflow-hidden text-1rem text-sm w-full z-10">
                    Processing...
                </div>
            </div>
            <form onSubmit={props.callback}>
                <div className="font-medium text-center text-gray-600">
                    Do you really want to delete this record?
                </div>
                <div className="flex float-right items-center justify-center my-5 w-full">
                    <div className="mr-3">
                        <TextButton
                        BtnLabel={"Cancel"}
                        color="primary"
                        onClick={props.handleClose}/>
                    </div>
                    <PrimaryButton
                        btnlabel={"Continue"}
                        variant={"contained"}
                        type="submit"
                    />     
                </div>
            </form>
        </>
    );
}

const ConfirmDelete = (props) => {
    return (
            <ModalDialog
                open={props.open}
                title={"Confirmation Message"}
                handleClose ={props.handleModalClose}
                cancelEvent ={props.handleModalClose}
                component={
                    <DeleteForm 
                        handleClose ={props.handleModalClose}
                        callback = {props.deleteCallback}
                        isProcessing = {props.isProcessing}
                    />
                }
                maxWidth={"xs"}
                fullWidth={true}
                disable={true}
                >
            </ModalDialog>
    );
};

export default ConfirmDelete;