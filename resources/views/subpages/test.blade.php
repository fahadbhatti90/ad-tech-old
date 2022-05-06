<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>D3 Test</title>
    <style>
    #graphType{
        display: block;
    width: 100%;
    height: calc(1.5em + .75rem + 2px);
    padding: .375rem .75rem;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: #6e707e;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #d1d3e2;
    border-radius: .35rem;
    -webkit-transition: border-color .15s ease-in-out, -webkit-box-shadow .15s ease-in-out;
    transition: border-color .15s ease-in-out, -webkit-box-shadow .15s ease-in-out;
    transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
    transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out, -webkit-box-shadow .15s ease-in-out;
    }
    
    </style>
</head>
<body base_url = {{ url("/") }}>
    <select name="graphType" id="graphType" >
            <option disabled selected value="">
                    Choose Type
                </option>
               
        <option value="1">
            Type 1
        </option>
        <option value="2">
            Type 2
        </option>
        <option value="3">
            Type 3
        </option>
    </select>
    <svg class="line-chart"></svg>
    
    <script src="{{asset('public/vendor/jquery/jquery.min.js')}}"></script>
    <script src="https://d3js.org/d3.v5.min.js"></script>
    <script>
        const api = "https://api.coindesk.com/v1/bpi/historical/close.json?start=2017-12-31&end=2018-04-01";

        document.addEventListener("DOMContentLoaded",function(event){
            // fetch(api)
            // .then(function(response){return response.json();})
            // .then(function(data){
            //     var parsedData = parseData(data);
            //     console.log(parsedData);
            //     // drawChart(parsedData);
            // })//end then
            // .catch(function(err){
            //     console.log(err);
            // });//end of function
            $("#graphType").on("change", function () {
                var graphType = $(this).val();
                $.ajax({
                    type: "get",
                    url: $("body").attr("base_url")+"/getGraphData",
                    data: {
                        "graphType":graphType
                    },
                    success: function (response) {
                      console.log(response);  
                      var parsedData = parseData(response);      
                      $("svg").html("");
                drawChart(parsedData);
            console.log(parsedData);
                    }
                });
            });
            
               
            function parseData(data)
            {
                var arr =[];
                for (var i in data) {
                    
                // console.log(data.bpi[i]);
                    arr.push({
                        date: new Date(i),
                        value: +data[i]
                    });
                }//end for
                return arr;
            }//end function

            function drawChart(data)
            {
                var svgWidth = 600, svgHeight = 400;
                var margin = {
                    top: 20, right: 20, bottom: 30, left: 50
                };
                var width = svgWidth - margin.left - margin.right;
                var height = svgHeight - margin.top - margin.bottom;

                var svg = d3.select('svg')
                .attr("width",svgWidth + margin.left + margin.right)
                .attr("height",svgHeight + margin.top + margin.bottom);

                var g = svg.append("g")
                .attr("transform","translate("+margin.left+","+margin.right+")");

                var x = d3.scaleTime()
                .rangeRound([0,width]);
                var y = d3.scaleLinear()
                .rangeRound([height,0]);

                var line = d3.line()
                .x(function(d){ return x(d.date)})
                .y(function(d){ return y(d.value)})
                x.domain(d3.extent(data,function(d){
                    return d.date;
                }))
                y.domain(d3.extent(data,function(d){
                    return d.value;
                }))

                g.append("g")
                .attr("transform","translate(0,"+height+")")
                .call(d3.axisBottom(x))
                .select(".domain")
                .remove();

                g.append("g")
                .call(d3.axisLeft(y))
                .append("text")
                .attr("fill","#000")
                .attr("transform","rotate(-90)")
                .attr("y","6")
                .attr("dy","0.71em")
                .attr("text-anchor","end")
                .attr("Price ($)");

                g.append("path")
                .datum(data)
                .attr("fill","none")
                .attr("stroke","steelblue")
                .attr("stroke-linejoin","round")
                .attr("stroke-linecap","round")
                .attr("stroke-width",1.5)
                .attr("d",line);
            }//end function

            function drawChart1($data){

            }


        });//end function
    </script>
</body>
</html>