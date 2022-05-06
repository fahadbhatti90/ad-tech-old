import React from 'react';
import PropTypes from 'prop-types';
import "./styles.scss";
import Tooltip from '@material-ui/core/Tooltip';
    
function SingleComCard(props) {
    var component = (
            <p className="innerPara">
                {
                props.prefix=="$"?
                
                props.prefix+props.value
                :
                props.value+props.prefix
                }
            </p>
    );
       
    return (
        <div>
            <fieldset className="border">
                <legend >{props.title}</legend>
                    {props.toolTip?
                    <Tooltip placement="top" title={
                        props.prefix=="$"?
                        
                        props.prefix+props.tooltipTitle
                        :
                        props.tooltipTitle+props.prefix
                        } arrow>
                        {component}
                    </Tooltip>:
                    component}
            </fieldset>
        </div>
    );
   
}

SingleComCard.propTypes = {
    title: PropTypes.string.isRequired, 
    value: PropTypes.string
};

export default SingleComCard;