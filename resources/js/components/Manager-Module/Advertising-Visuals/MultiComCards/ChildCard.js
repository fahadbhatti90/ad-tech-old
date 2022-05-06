import React from 'react';
import SvgLoader from "./../../../../general-components/SvgLoader";
import PosArrow from "./../../../../app-resources/svgs/manager/advertising-visuals/greenarrow.svg";
import NegArrow from "./../../../../app-resources/svgs/manager/advertising-visuals/redarrow.svg";
import Tooltip from '@material-ui/core/Tooltip';
import "./styles.scss"
import {commaSeparator,commaFormat} from "./../../../../helper/helper";
function TooltipComCard(props){
    return(
        <Tooltip placement="top" title={
            props.prefix=="$"?
            `${props.prefix}${props.currency}`
            :
            `${props.currency}${props.prefix}`
            } 
        arrow>
            <label className="currency">{
             props.prefix=="$"?
                
             `${props.prefix}${commaSeparator(+props.currency)}`
             :
             `${commaSeparator(+props.currency)}${props.prefix}`
            }</label> 
        </Tooltip>
    );
}
function ChildCard(props){
    let arrow = props.label>0?PosArrow:NegArrow;
    let lblColor = props.label>0?"posLabel":"negLabel";
    return ( 
            <div className="childCard">
            <label className="labelData">{props.title}</label> 
            
            {props.tooltip?
                <TooltipComCard {...props}/>
            :
            <label className="currency">{
                props.prefix=="$" || props.prefix==""?
                    props.commaSep?(props.prefix+ commaFormat(props.currency)):(props.prefix+commaSeparator(+props.currency))
                :
                props.commaSep?(props.currency+props.prefix):(commaSeparator(+props.currency)+props.prefix)
             }</label>
            } 
            
            <span className="spanCenter">
                <SvgLoader src={arrow} customClasses={"arrowH mr-1"}/>
                <label className={`labelValue ${lblColor}`}>{props.label+"%"}</label>
            </span>
        </div>
        
        
);
}

export default ChildCard;