import React, { Component } from 'react';
import clsx from 'clsx';
import './home.scss'
import C3Chart from 'react-c3js';
import 'c3/c3.css';
import * as d3 from 'd3';
import LinearProgress from '@material-ui/core/LinearProgress';
import CheckIcon from '@material-ui/icons/Check';
import EventLine from "./EventLine";
import {
    ManagerEvents,
    getYAxisTooltipFormate,
    getYAxisFormate,
    getEventFormated,
    getformatedGrapData,
} from './Helper';
import {
    getVissualPopupData
} from './apiCalls';
import {primaryColor} from './../../../../app-resources/theme-overrides/global'

var defualtState = {}
export default class VisualPopup extends Component {
    constructor(props){
        super(props);
        this.state =  {
            extraLine:{
                isGraphLoaded:false,
                extraLineOffsetLeft:0
            },
            events:{//used in events to display gradients
            },
            selectMonthsEvents:[],
            selectedEventIds:[],
            shouldUpdateState:false,
            selectedDate:"",
            isGraphLoaded:false,
            ajaxData:{
                year:"NA",
                month:"NA",
                fullMonthYear:"NA",
                attribute:"shipped_cogs",
                attributeDisplayName:"Sales",
                asin:props.dataForGraph.asin,
            },
            graph:{
                missingDates:0,
                availbleDates:[],
                x:[],
                data:[],
                events:[],
                eventIds:[]
            },
            productTitle:props.dataForGraph.productTitle,
            isVisualPopupLoaded:false
        };
        defualtState = this.state;
    }
    componentDidMount(){
        let _this = this;
        let asin = this.state.ajaxData.asin;
        getVissualPopupData(this.state.ajaxData,
        (response)=>{
            const {graph,ajaxData} = _this.state;
            
            let formatedGrapData = getformatedGrapData(response, "Sales");
            graph.x = formatedGrapData.x;
            graph.data= formatedGrapData.data; 
            graph.availbleDates = response.availbleDates;
            graph.events = response.events;
            graph.eventIds = getEventFormated(response);


            ajaxData.month = _this.state.graph.availbleDates && _this.state.graph.availbleDates.length > 0 ? _this.state.graph.availbleDates[0].month : '';
            ajaxData.fullMonthYear = _this.state.graph.availbleDates && _this.state.graph.availbleDates.length > 0 ?_this.state.graph.availbleDates[0].fullMonthYear : '';
            ajaxData.year = _this.state.graph.availbleDates && _this.state.graph.availbleDates.length > 0 ? _this.state.graph.availbleDates[0].year : '';
            let fullDate = _this.state.graph.availbleDates && _this.state.graph.availbleDates.length > 0 ? _this.state.graph.availbleDates[0].fullDate : '';
            _this.setState({
                isGraphLoaded:!_this.state.isGraphLoaded,
                graph:graph,
                ajaxData,
                selectedDate:fullDate
            },()=>{
                _this.DrawGraph();
            });
        },(error)=>{

        })
       
    }
    DrawGraph = ()=>{
        if(this.state.graph.x && this.state.graph.x.length > 0){
            
            var minDate = this.state.graph.x[1];//get first date of x-axis
            var missingDates = 0
            if(minDate > 1){
                missingDates = 31 - minDate;
            }
            this.setState((prevState)=>({
                graph:{
                    ...prevState.graph,
                    missingDates
                },
            }));
        }
    }
    static UNSAFE_componentWillReceiveProps(nextProps, prevState) {
        if (nextProps.dataForGraph) {
            const {ajaxData} = prevState;
            ajaxData.asin = nextProps.dataForGraph.asin;
            return ({
                productTitle:nextProps.dataForGraph.productTitle,
                ajaxData:ajaxData
             });
        }
        return null;
    }
    handleEventButtonChange =(e)=>{    
        this.setSelectedEventsState(e, $(e.target).is(":checked"))
    }
    handleEventButtonHoverIn =(e)=>{  
        if( !this.isCheckBoxChecked(e) )
        this.setSelectedEventsState(e, true);
    }
    handleEventButtonHoverOut =(e)=>{   
        if( !this.isCheckBoxChecked(e) )
        this.setSelectedEventsState(e, false);
    }
    isCheckBoxChecked = (e)=> {
        return  e.target.tagName == "INPUT" ? $(e.target).is(":checked") :
        $(e.target).parent("label").find("input[type='checkbox']").is(":checked");
    }
    setSelectedEventsState = (e, isSelected)=>{
        let eventIndexTemp = $(e.target).parent().attr("data-index");
        eventIndexTemp = (typeof eventIndexTemp == "undefined") ?  $(e.target).attr("data-index") : eventIndexTemp;
        this.setState((prevState)=>({
            events:{
                ...prevState.events,
                ["event"+eventIndexTemp]:isSelected
            }
        }));
    }
    handleProductVisualGraphAttributeChange =(e)=>{
       let attr = $(e.target).val().split("|");
       const {ajaxData} = this.state;
       ajaxData.attribute = attr[0];
       ajaxData.attributeDisplayName = attr[1];

       this.setState({
           ajaxData,
           isVisualPopupLoaded:false,
       },()=>{
           this.getGraphData();    
       });
    }
    handleProductVisualGraphDateChange =(e)=>{
        let date = $(e.target).val().split("|");
        let year = date[0];
        let month = date[1];
        let fullDate = date[2];
        let fullMonthYear = date[3];
        const {ajaxData} = this.state;
        ajaxData.year = year;
        ajaxData.month = month;
        ajaxData.fullMonthYear = fullMonthYear;
        this.setState({
            ajaxData,
            isVisualPopupLoaded:false,
            selectedDate:fullDate
        },()=>{
            this.getGraphData();    
        });
    }
    getGraphData = ()=>{
        const _this = this;
        $.ajax({
            type: "GET",
            url: $("body").attr("baseUrl")+'/getEvents',
            data:_this.state.ajaxData, 
            success: function (response) {
                defualtState.extraLine.isGraphLoaded=true;
                defualtState.extraLine.isGraphLoaded=_this.state.extraLine.extraLineOffsetLeft;
                defualtState.graph.availbleDates=_this.state.graph.availbleDates;
                defualtState.ajaxData=_this.state.ajaxData;
                defualtState.selectedDate=_this.state.selectedDate;

                _this.setState(defualtState);
                
                const {graph,extraLine} = _this.state;
                graph.availbleDates = response.availbleDates;
                graph.events = response.events;
                graph.eventIds = getEventFormated(response);

                let formatedGrapData = getformatedGrapData(response, _this.state.ajaxData.attributeDisplayName);
                graph.x = formatedGrapData.x;
                graph.data= formatedGrapData.data; 

                extraLine.isGraphLoaded=!extraLine.isGraphLoaded
                _this.setState({
                    isGraphLoaded:!_this.state.isGraphLoaded,
                    graph:graph,
                    extraLine:extraLine
                }, () => {
                    _this.DrawGraph();
                });        
            },
            error:function(e){
                // console.log(e.responseText);
            }
        });
    }
    oninit = () => {
        let _this = this;
        setTimeout(() => {
            ManagerEvents(_this);
        }, 100);
    };
    getDataSetting = (salesData)=>{
        return {
            x:'x0',
            columns: salesData,
            empty: {
                label: {
                    text: "No Data"
                }
            },
            color:  (color, d) => {
                return  d && d.value == 0 ? d3.rgb("#E91E63") : primaryColor;
            },
            onmouseover: (d) => {
                if(d.value == 0)
                $(".productTableVisuals").addClass("warningPoint");
                else if($(".productTableVisuals").hasClass("warningPoint")) 
                $(".productTableVisuals").removeClass("warningPoint");
            },
            selection: {
                enabled: true,
                multiple: true,
                draggable: true,
                isselectable: function (d) { return d.value != null; }
              }
        }
    }
    getZeroXAxisData = (data) => {
        // return [];
        if(this.state.graph.data.length <= 0) return [];
        let zeroData = [];
        this.state.graph.data.forEach((value, index) => {
            if(index > 0 && value <= 0){
                zeroData.push({value: index-1, text: 'No Data Found', class: 'labelNoData', position: 'end'});
            }
        });
        return zeroData;
    }
    render() { 
        const {extraLine, events, selectMonthsEvents,selectedEventIds,graph,isGraphLoaded} = this.state;
        let fullMonthYear = this.state.ajaxData.fullMonthYear;
        let _this = this;
        var salesData = [
            this.state.graph.x,
            this.state.graph.data,
        ];
        // bindto: "#salesChart",
        
        let legend={
            position: 'right',
            show: false
        }
        let size= {
            height: 270
        }
        let tooltip= {
            format: {
                title: function (d, index) { return  (salesData[0][(d+1)]+" "+fullMonthYear)},
                value: function (value, ratio, id) { return getYAxisTooltipFormate(_this.state.ajaxData.attributeDisplayName)(value); },
            }
        }
        let axis = {
            y: {
                show: true,
                label: {
                    text: _this.state.ajaxData.attributeDisplayName,
                    position: 'outer-middle'
                },
                padding : {
                    bottom : 1
                },
                tick: {
                    outer: false,
                    format:function (d) { return getYAxisFormate(_this.state.ajaxData.attributeDisplayName)(d); } 
                },
                min: 0
            },
            x:{
                type: 'category',
                tick: {
                    multiline:false,
                    outer: false,
                },
            }
        }
        let grid= {
            y: {
                show:true
            },
            x:{
                lines: this.getZeroXAxisData(salesData[1])
            }
        }
        let line= {
            connectNull: true
        }
        
        return (
            <div className ="productsVisuals bg-white w-full h-full shadow-2xl pb-10" >
                <div className="productTableVisuals realtive">
                    <div className="graphLoader bg-white absolute h-full overflow-hidden w-full " style={!this.state.isVisualPopupLoaded?{display:"block"}:{display:"none"}} >
                        <LinearProgress />
                        <div className="absolute h-full overflow-hidden w-full flex justify-center items-center text-gray-500 text-sm font-mono">
                            Loading...
                        </div>
                    </div>
                    <div className="flex mb-3 pt-5 selectedFilterDetails">
                            <div className="offset-1 text-center w-7/12">
                                <p className="">
                                {
                                    this.state.productTitle.length > 57 ? this.state.productTitle.slice(0,57)+"..." : this.state.productTitle
                                }
                                </p>
                            </div>
                            <div className="w-5/12 pl-10">
                            <span className="font-semibold inline-block mr-3 text-sm themeTextColor">{this.state.ajaxData.asin}</span>
                                <select id="saledd" className="monthSelector mr-2 p-1" onChange={this.handleProductVisualGraphDateChange}>  
                                    {
                                        graph.availbleDates && graph.availbleDates.length > 0 ?graph.availbleDates.map((date)=><option key={date.fullDate} value={`${date.year}|${date.month}|${date.fullDate}|${date.fullMonthYear}`} >{date.fullDate}</option>) : <option>No Data</option>
                                    }
                                </select>
                                <select id="saledd" className="p-1" onChange={this.handleProductVisualGraphAttributeChange}>
                                    <option value="shipped_cogs|Sales" >Sales</option>
                                    <option value="price|Price">Price</option>
                                    <option value="salesrank|Sales Rank">Sales Rank</option>
                                </select>
                            </div>
                    </div>
                    <div className="flex row vissualContainer">
                        <div className="mainVisuals overflow-hidden overflow-y-auto p-0 w-10/12">
                            <div id="salesChart" className=" relative">
                                {
                                    isGraphLoaded ? <C3Chart 
                                    axis={axis}
                                    data={this.getDataSetting(salesData)}
                                    tooltip={tooltip} 
                                    oninit={this.oninit}
                                    // point={point} 
                                    size={size}
                                    grid={grid}
                                    line={line}
                                    legend={legend} />:null
                                }
                                {
                                    extraLine.isGraphLoaded ? <div className="extraLine" style={{left:extraLine.extraLineOffsetLeft}}><div className="eventLabelContainer"><div className="eventsLabel">Events</div></div></div> : null
                                }
                            </div>
                            <div id="eventsChart" className={selectMonthsEvents && selectMonthsEvents.length > 0 ?"" : "flex items-center justify-center text-gray-500 text-xs"}>
                                {
                                    isGraphLoaded && selectMonthsEvents && selectMonthsEvents.length > 0 ?selectMonthsEvents.map((event,index)=>{
                                        const element = event;
                                        /**Event Width [4,'Crap', 23, 31, 5,"linear-gradient(180deg, #4e73df00 10%, #224abe80 100%);","#224abe"], */
                                        var index1 = element[2];
                                        var index2 = element[3];
                                        
                                        var newIndex1 = null;
                                        var newIndex2 = null;
                                        do {
                                            $.each($("#salesChart svg > g > .c3-axis.c3-axis-x > .tick"), function (indexInArray, valueOfElement) { 
                                                if($(valueOfElement).text() == index1)
                                                {
                                                    newIndex1 = $(valueOfElement).index();
                                                }
                                                if($(valueOfElement).text() == index2)
                                                {
                                                    newIndex2 = $(valueOfElement).index();
                                                }
                                            });
                                            if(newIndex1 == null){
                                                if(index1 == index2){ //25 dose not exist and index1 = index2 = 25 means index2 also doses not exist
                                                    return null;
                                                }
                                                index1++;
                                            }
                                            if(newIndex2 == null){
                                                index2--;
                                            }
                                            if(index2 < index1){ //25 dose not exist and index1 = index2 = 25 means index2 also doses not exist
                                                return null;
                                            }
                                        } while (newIndex1 == null || newIndex2 == null);

                                        index1 = newIndex1-1;
                                        index2 = newIndex2-1;
                                        // return;
                                        var element1OffsetForWidth = $($("#salesChart svg > g > .c3-axis.c3-axis-x > .tick")[(index1)]).offset().left;
                                        var element2OffsetForWidth = $($("#salesChart svg > g > .c3-axis.c3-axis-x > .tick")[(index2)]).offset().left;
                                        var newElementWidth = element2OffsetForWidth - element1OffsetForWidth;
                                        newElementWidth = newElementWidth > 0 ? newElementWidth + 10 : newElementWidth ;
                                        var amountSubtractFromLeftPosition  = index1.toString().length == 1 ? 5 : 2;
                                        /**Event Width */
                                        /**Poitioning event */
                                        var element1Offset = $($("#salesChart svg > g > .c3-axis.c3-axis-x > .tick")[index1]).offset().left;
                                        var svgOffset = $("#salesChart svg").offset().left;
                                        var startPoint = element1Offset - svgOffset;
                                        /**Poitioning event */
                                        return <EventLine 
                                            key={index}
                                            eventNumber={index}
                                            eventId={element[0]}
                                            eventBg={element[5]}
                                            showGradient={events["event"+element[0]]}
                                            positonFromTop={element[4]}
                                            positionFromLeft={startPoint-amountSubtractFromLeftPosition}
                                            width={(newElementWidth)+"px"}
                                            eventGradient={`linear-gradient(180deg, #00000000 10%, ${element[5]}b3 100%)`}
                                            delay={((index*4) * 20)}
                                        />
                                    }):"No Event Found"
                                }
                            </div>
                        </div>
                        <div className="eventsButtons p-0 w-2/12 flex flex-col overflow-y-auto overflow-x-hidden">
                            {
                               isGraphLoaded && selectedEventIds && selectedEventIds.length > 0 ? selectedEventIds.map((event)=>{

                                return <label 
                                key={event.id} className="checkboxContainer contentEventButton" data-index = {event.id}>
                                    {event.name}
                                    <input type="checkbox" onChange={this.handleEventButtonChange}/>
                                    <span className={"checkmark "} style={{backgroundColor:event.color}}>
                                        <CheckIcon/>
                                    </span>
                                </label>
                                }):null
                            }
                        </div>
                    </div>
                    <div id="DatePart" className="text-center font-medium">
                        {this.state.selectedDate}
                    </div>
                </div>
            </div>
        )
    }
}
