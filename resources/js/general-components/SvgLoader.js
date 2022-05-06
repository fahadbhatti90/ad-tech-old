
import React from 'react';

export default function SvgLoader(props) {
    return (
        <img src={window.assetUrl+props.src} alt={props.alt} className={props.customClasses} style={{height: props.height || 20}} onClick={props.onClick?props.onClick:null}/>
    );
  }