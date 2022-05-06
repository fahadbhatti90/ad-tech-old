import React from 'react';
import ModalDialog from './../../../../general-components/ModalDialog';
import "./../styles.scss";
import SvgLoader from "./../../../../general-components/SvgLoader";
import Cross from "./../../../../app-resources/svgs/Cross.svg";
import PrimaryButton from "./../../../../general-components/PrimaryButton";

const AssociatedBrands =(props)=>{
    return (
        <div className="flex flex-col justify-center items-center text-gray-600">
            {props.brands.length > 0?
            <div>
                <div className="font-medium">{props.isUser?"Following users are associated to this brand.":"Following brands are associated to this user."}</div>
                <ul className={props.brands.length>3?"ulist":""}>
                {props.brands.length > 0?
                    props.brands.map((item,i) => <li key={i} className="p-1">{item.name}</li>):
                ""}
                </ul>
            </div>
            :
            <div className="text-center text-xl">
                    <div className="w-full">
                    <SvgLoader src={Cross} height={"5rem"}/>
                    </div>
                    {props.isUser?"No User Associated":"No Brand Associated"}
            </div>
            
            }

                <PrimaryButton
                    btnlabel={"OK"}
                    variant={"contained"}
                    onClick={props.handleClose}
                />  
        </div>
    );
}


const BrandsInfo = (props) => {
    return (
        <ModalDialog
            open={props.open}
            title={props.title}
            handleClose ={props.handleModalClose}
            cancelEvent ={props.handleModalClose}
            component={
                <AssociatedBrands 
                    handleClose ={props.handleModalClose}
                    brands={props.brands}
                    isUser={props.isUser?true:false}/>}
            maxWidth={"xs"}
            fullWidth={true}
            disable={true}
            >
        </ModalDialog>
    );
};

export default BrandsInfo;