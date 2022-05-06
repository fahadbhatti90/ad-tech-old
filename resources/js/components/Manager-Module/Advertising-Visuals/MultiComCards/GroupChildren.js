
import React from 'react';
import ChildCard from "./ChildCard";

export default function GroupChildren(props) {
    return (
            <div className="childCard">
                { props.cardData.map((data,i) =>
                    <ChildCard title={data.title} prefix={data.prefix} tooltip={props.tooltip} currency={data.currency} commaSep={props.commaSep} label={data.label} key={i}/>
                )}
            </div>);
    }