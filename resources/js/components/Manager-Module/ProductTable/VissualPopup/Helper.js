
import * as d3 from 'd3';
export function ManagerEvents(_mainObject) {
    if(!_mainObject.state.extraLine.isGraphLoaded){
        let extraLineOffsetLeft = ((parseInt($("#salesChart svg > g > .c3-axis.c3-axis-y path").offset().left) - parseInt($("#salesChart svg").offset().left))-1)+"px";
        _mainObject.setState((prevState)=>({
            extraLine:{
                ...prevState.extraLine,
                isGraphLoaded:true,
                extraLineOffsetLeft : extraLineOffsetLeft
            },
            isVisualPopupLoaded:true
        }));
    }
     
    var eventsUi = _mainObject.state.graph.events;
    _mainObject.setState((prevState)=>({
        selectMonthsEvents:eventsUi,
        selectedEventIds:_mainObject.state.graph.eventIds,
    }));
    $(".productTableVisuals .c3-axis.c3-axis-y > path").hide();
}
export function getYAxisTooltipFormate(yLabel){
     switch (yLabel) {
         case "Sales Rank":
             return d3.format(",");
             break;
     
         default:
         return d3.format("$,");
             break;
     }
}
export function getYAxisFormate(yLabel){
     switch (yLabel) {
         case "Sales Rank":
             return d3.format("~s");
             break;
     
         default:
         return d3.format("$,.2s");
             break;
     }
}
export function getEventFormated(response){
    let events = response.events;
    let eventIds = [];
    let temEventIds = [];
    $.each(events, function (indexInArray, valueOfElement) { 
        events[indexInArray].push(response.eventsData[valueOfElement[0]-1].eventColor)
        if(!temEventIds.includes(events[indexInArray][0])){
            temEventIds.push(events[indexInArray][0]);
            eventIds.push({
                id:events[indexInArray][0],
                name:events[indexInArray][1],
                color:response.eventsData[valueOfElement[0]-1].eventColor
            });
        }
    });
    return eventIds;
}
export function getformatedGrapData(response, attributeDisplayName){
    let graphData = response.graph;
    let x = [];
    let data=[];
    x.push("x0");
    data.push(attributeDisplayName)
    $.each(graphData, function (indexInArray, valueOfElement) { 
        x.push(parseInt(indexInArray));
        data.push(parseInt(valueOfElement));
    });
    return {x, data};
}