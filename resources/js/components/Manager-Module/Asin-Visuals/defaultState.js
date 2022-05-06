export const defaultOptions = {
    scoreCards:[],
    perfData:[],
    effiData:[],
    awareData:[],
    getPerformanceY2Min:null,
    showComcardsLoader:false,
    showPerfLoader:"",
    showEffiLoader:"",
    showAwarLoader:"",
    perfPercentagesData:[
        {
            prefix: "$",
            title:"Revenue",
            label:0,
            currency:0
          },
          {
           prefix: "$",
           title:"Cost",
           label:0,
           currency:0
         },
         {
           prefix: "%",
           title:"Acos",
           label:0,
           currency:0
         }
    ],
    effiPercentagesData:[
        {
            prefix: "$",
            title:"CPC",
            label:0,
            currency:0
          },
          {
           prefix: "$",
           title:"ROAS",
           label:0,
           currency:0
         },
         {
           prefix: "$",
           title:"CPA",
           label:0,
           currency:0
         }
    ],
    awarPercentagesData: [
        {
          prefix: "",
          title:"Impressions",
          label:0,
          currency:0
      },
      {
        prefix: "",
       title:"Clicks",
       label:0,
       currency:0
     },
     {
      prefix: "%",
      title:"CTR",
       label:0,
       currency:0
     }],
}