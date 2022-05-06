import React from 'react';
import ModalDialog from '../../../../../general-components/ModalDialog';
import LinearProgress from '@material-ui/core/LinearProgress';
import TextButton from "../../../../../general-components/TextButton";
import PrimaryButton from "../../../../../general-components/PrimaryButton";

const Delete = (props) => {
    console.log('props delete', props)
    return (
        <>
            <div className="graphLoader bg-white absolute h-full overflow-hidden w-full top-0 left-0 z-10"
                 style={props.isProcessing ? {display: "block"} : {display: "none"}}>
                <LinearProgress/>
                <div
                    className="absolute flex font-bold font-mono h-full items-center justify-center overflow-hidden text-1rem text-sm w-full z-10">
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
                            btntext={"Cancel"}
                            color="primary"
                            onClick={props.handleClose}/>
                    </div>
                    <PrimaryButton
                        btntext={"Continue"}
                        variant={"contained"}
                        type="submit"
                    />
                </div>
            </form>
        </>
    );
}

const MoveToMain = (props) => {
    console.log('props move to main', props)
    return (
        <>
            <div className="graphLoader bg-white absolute h-full overflow-hidden w-full top-0 left-0 z-10"
                 style={props.isProcessing ? {display: "block"} : {display: "none"}}>
                <LinearProgress/>
                <div
                    className="absolute flex font-bold font-mono h-full items-center justify-center overflow-hidden text-1rem text-sm w-full z-10">
                    Processing...
                </div>
            </div>
            <form onSubmit={props.callbackMoveToMain}>
                <div className="font-medium text-center text-gray-600">
                    Do you really want to move to main this record?
                </div>
                <div className="flex float-right items-center justify-center my-5 w-full">
                    <div className="mr-3">
                        <TextButton
                            btntext={"Cancel"}
                            color="primary"
                            onClick={props.handleClose}/>
                    </div>
                    <PrimaryButton
                        btntext={"Continue"}
                        variant={"contained"}
                        type="submit"
                    />
                </div>
            </form>
        </>
    );
}

const askForConfirmation = (props) => {
    console.log('props', props)
    return (
        <ModalDialog
            open={props.open}
            title={"Confirmation Message"}
            handleClose={props.handleModalClose}
            cancelEvent={props.handleModalClose}
            component={
                props.isVerifyMoveToMain == 'submitMoveToMainForm' ?
                <MoveToMain
                    handleClose={props.handleModalClose}
                    callbackMoveToMain={props.moveToMainForm}
                    isProcessing={props.isProcessing}

                /> : <Delete
                        handleClose={props.handleModalClose}
                        callback={props.deleteForm}
                        isProcessing={props.isProcessing}

                    />
            }
            maxWidth={"xs"}
            fullWidth={true}
            disable={true}
        >
        </ModalDialog>
    );
};

export default askForConfirmation;