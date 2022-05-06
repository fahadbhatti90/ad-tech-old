import React, {useEffect, useState} from 'react'

export default function useGraphHandler() {
    
    const [state, setState] = useState({
        displayGraph:false,
        dataForGraph:{
            asin: "",
            productTitle: "",
        },
    })
    useEffect(() => {
        document.addEventListener('click', handleClickOutside, false);
        return () => {
            document.removeEventListener('click', handleClickOutside, false);
        }
    }, [])
    const handleClickOutside = (e) => {
        e.stopPropagation();
        if (!e.path.includes(document.querySelector(".serverSideDataTable")) && state.displayGraph ) {   
            setState((prevState)=>({
                ...prevState, displayGraph:false 
            }));
        }
    }
    const handleRowClickEvent = (row,e)=>{
        $('html, body').animate({
            scrollTop: $(".searchDataTable").offset().top,
            // scrollLeft: $(".searchDataTable").offset().left
        });
        setState((prevState)=>({
            ...prevState,
            dataForGraph:{
                    asin: row.ASIN ?? "B000052YQN",
                    productTitle: row.product_title ?? "NA",
            },
           displayGraph:!state.displayGraph
        }));
    }
    const handleGraphOverLayClick =(e)=>{
        if(($(e.target).hasClass("ProductNarativeGraph") || $(e.target).hasClass("overlay"))){
            setState((prevState)=>({
                ...prevState,
                displayGraph:!state.displayGraph
            }))
        }
    }
    const onDataTableSearch =(e)=>{ 
        setState((prevState)=>({
            ...prevState,
            displayGraph:false
        }));
    }
    return {
        displayGraph:state.displayGraph,
        dataForGraph:state.dataForGraph,
        onDataTableSearch,
        handleRowClickEvent,
        handleGraphOverLayClick
    }
}
