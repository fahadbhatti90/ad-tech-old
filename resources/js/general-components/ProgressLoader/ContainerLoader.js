import React from 'react';
import SvgLoader from "./../SvgLoader";
import LoaderGif from "./../../app-resources/assets/LoaderGif.gif";
function ContainerLoader(props) {
    return (
            <div className={`${props.classStyles} loaderOverlay absolute top-0 left-0 w-full h-full z-10 flex justify-center items-center`}>
                <SvgLoader 
                    src={LoaderGif}
                    height={props.height}
                />
            </div>
    );
}

export default ContainerLoader;