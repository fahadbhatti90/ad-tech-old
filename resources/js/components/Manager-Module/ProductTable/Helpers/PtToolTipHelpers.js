import React from 'react'
import Chip from '@material-ui/core/Chip';
import Tooltip from '@material-ui/core/Tooltip';
import CloseIcon from '@material-ui/icons/Close';
import LinearProgress from '@material-ui/core/LinearProgress';

export const getTaggedTooltipContent = (row, tags, handleSingleTagUnAssignment)=>{
    const updatedTags = tags.map((tag, index)=>{
        return  <>
                    <div key={tag} className="flex items-center singleTagDelete">
                        <span>{tag}</span>
                        <CloseIcon 
                        asin={row.ASIN} 
                        account-id ={row.fk_account_id} 
                        tag-id ={row.fkTagIds.split(",")[index]}
                        // rowid={row["Sr.#"]} 
                        className="cursor-pointer" 
                        onClick={handleSingleTagUnAssignment}/>
                    </div>
                </> 
    });//end map funciton
    return <div className="ProductTableTooltip">
                <div className="singleTagLoader absolute left-0 top-0 w-full hidden" >
                    <LinearProgress />
                </div>
                {updatedTags}
            </div>
}

export const getTaggedTooltipTarget = (tag) =>{
    return  <div className="productTableChip">
                <Chip size="small" label={tag[0]} /> 
            </div>
}

export const productTitleRowHandler = (row,classes) =>{

    const childBrand = <Tooltip classes={{
        popper:classes.mainClass,
        popperInteractive:classes.productTable,
        tooltip:classes.ptTooltip,
        arrow:classes.ptArrow,
       }} className="newClass" placement="top" title={row.overrideLabel ? row.overrideLabel : row.product_title} arrow interactive>
                            <div
                            fk-account-id={row.fk_account_id}
                            ffc={row.fullfillment_channel}
                            asin={row.ASIN}
                            rowid={row["Sr.#"]}
                            className="RowTitle tooltipText"
                            >
                                {row.overrideLabel ? row.overrideLabel : row.product_title}
                            </div>
                        </Tooltip>
    return childBrand;
}