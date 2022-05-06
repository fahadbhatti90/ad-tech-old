import React, { Component } from 'react';
import C3Chart from 'react-c3js';
import 'c3/c3.css';
import * as d3 from 'd3';
import "./../MultiComCards/styles.scss";
class Graph extends Component {
    constructor(props){
        super(props);
        this.state={
            dataA:"",
            dataB:"",
            dataC:"",
            types:[],
            colors:[],
            yOneText:"",
            yTwoText:""
        }
        this.myRef = React.createRef();
    }

    componentDidUpdate=(prevProps, prevState, snapshot)=>{
        if(prevProps.dataChart.length != this.props.dataChart.length){
            this.myRef.current.chart=this.myRef.current.chart.destroy();
        }
    }

    static getDerivedStateFromProps(nextProps, prevState) {
        let dataA = nextProps.dataChart.length > 0? nextProps.dataChart[1][0]:"";
        let dataB = nextProps.dataChart.length > 0?  nextProps.dataChart[2][0]:"";
        let dataC = nextProps.dataChart.length > 0? nextProps.dataChart[3][0]:"";
        let types = nextProps.types;
        let colors = nextProps.colors;
        let yOneText = nextProps.yOneText;
        let yTwoText = nextProps.yTwoText;
        return {
            dataA,
            dataB,
            dataC,
            types,
            colors,
            yOneText,
            yTwoText
        }
    }

    render() {
        let that = this;
        let data = {
            x: 'x',
            columns: this.props.dataChart,
            empty: {
                label: {
                    text: "No Data"
                }
            },
            names: {
                0:this.state.dataA, 
                1:this.state.dataB, 
                2:this.state.dataC 
            },
            types:{
                [this.state.dataA]: this.state.types?this.state.types[0]:"bar",
                [this.state.dataB]: this.state.types?this.state.types[1]:"spline",
                [this.state.dataC]: this.state.types?this.state.types[2]:"spline"
                },
            colors: {
                [this.state.dataA]: this.state.colors?this.state.colors[0]:'#08bdda',
                [this.state.dataB]: this.state.colors?this.state.colors[1]:'#059656',
                [this.state.dataC]: this.state.colors?this.state.colors[2]:'#6a1b9a'
            },
            axes: this.props.axes?
                {
                    [this.state.dataB]: 'y',
                    [this.state.dataC]: 'y',
                    [this.state.dataA]: 'y2'
                }:{
                    [this.state.dataC]: 'y',
                    [this.state.dataB]: 'y',
                    [this.state.dataA]: 'y2'
                }
        };
        
        let tooltip = {            
                show: true,
                point: true,
                format: {
                   value: d3.format(''), // apply this format to both y and y2
                }
        }
        let point = {
                    focus: {
                        expand: {
                        enabled: true,
                        }
                    }
                };
        let grid = {
            x: {show: false},
            y: {show: false}
        };

        let bar= {
            width: {
                ratio: 0.4 // this makes bar width 50% of length between ticks
            }
        };

        let axis= {
            x: {    
                type: 'category',
                tick: {
                    rotate: -45,
                    multiline: false,
                    culling: {
                        max: 10 // the number of tick texts will be adjusted to less than this value
                    },
                },  
                height:50,  
                label:{
                    position: 'middle'
                }, 
            },
            y: {
                label: {
                    text: this.state.yOneText,
                    position: 'outer-middle'
                },
                tick:{
                    format: d3.format(".2s"),
                },
                // min : 0,
                padding: {
                    top: 50,
                    bottom: 50
                    }
            },
            y2: {
                show: true,
                label: {
                    text: this.state.yTwoText,
                    position: 'outer-middle'
                },
                tick:{
                    format: d3.format(".2s")
                },
                // min:0,
                padding: {top: 50,
                    bottom: 50
                    }
            }
        }

        let legend = {
                item: {
                    onclick: function(id) {
                        this.api.toggle(id);
                        let dataA = this.api.data.names();
                        setTimeout(() => {
                            let firstlegend = document.getElementsByClassName(`c3-legend-item-${dataA[0]}`)[0].classList.contains('c3-legend-item-hidden');
                            let secondlegend = document.getElementsByClassName(`c3-legend-item-${dataA[1]}`)[0].classList.contains('c3-legend-item-hidden');
                            this.api.axis.labels({
                                y: firstlegend ? dataA[1] 
                                :secondlegend ?  dataA[2]: `${dataA[1]}/${dataA[2]}`,
                                y2: firstlegend ? dataA[2] : dataA[0],
                            });
                            this.api.data.axes({
                                [dataA[2]]: firstlegend ? 'y2' 
                                :secondlegend ?  'y':'y'
                            });
                        }, 100);
                    }
                },
                show: true,
                position: 'inset',
                inset: {
                    anchor: 'top-left',
                    x:0,
                    y: -10,
                    step: -5
                }
        }
    
        let zoom ={
            enabled: true,
            rescale: false,
            onzoomend: d => { 
                    console.log(d);
                    document.querySelectorAll(`${this.props.customClass} .c3-axis-x g text`).forEach(function(v,i){ 
                        if((i <= (+d[0]) || i >= (+d[1]) ) && v.style.display != "none"){
                            v.style.display="none";
                            v.setAttribute("data-aupdated","updated");
                            return;
                        }
                        if(v.hasAttribute("data-aupdated") && v.style.display == "none")
                            v.style.display="inline-block"
                    });
                }
            }
        return (
            <div>
                <C3Chart 
                    ref={this.myRef}
                    axis={axis}
                    zoom={zoom}
                    data={data}
                    tooltip={tooltip} 
                    point={point} 
                    size={{height: 240}}
                    grid={grid}
                    bar={bar}
                    legend={legend}
                    />
            </div>
        );
    }
}

export default Graph;