import moment from "moment";

export const SCHEDULED_REPORTS_DATATABLE_APIURL = window.baseUrl+"/advertisingReports/emailSchedule";
export const SCHEDULED_REPORTS_POPUP_APIURL = window.baseUrl+"/advertisingReports/getPopUpData";

export const SCHEDULED_REPORTS_METRICS_APIURL =  window.baseUrl+"/advertisingReports/getMetricsPopupData";
export const SCHEDULED_REPORTS_DELETE_SCHEDULE = window.baseUrl+"/advertisingReports/";
export const GET_REPORT_TYPE_URL = window.baseUrl+"/advertisingReports/getReportTypes";
export const SUBMIT_REPORT_URL = window.baseUrl+"/manageEmailSchedule";
export const GET_REPORT_METRICS_URL = window.baseUrl+"/advertisingReports/getReportMetrics";
export const GET_EDIT_FORM_URL = window.baseUrl +"/advertisingReports/getEditFormData";

const days = ["mon","tue","wed","thu","fri","sat","sun","time"];
const metricsMappings = [
    {"campaingMetricsString":1},
    {"adGroupMetricsString":2},
    {"productAdsMetricsString":3},
    {"keywordMetricsString":4},
    {"asinsMetricsString":5}
]

export function getScheduleReportData(callback, errorcallback){
    axios.get(SCHEDULED_REPORTS_DATATABLE_APIURL)
    .then(res => {
      if(callback != null){
         let data = res.data.scheduledEmails;
          if(data.length > 0){
            /**
             * extract report types
             */
            let reportTypes= data.map((item,)=>{
                return item.selected_report_types
                })
            /**
             * extract sponsored types
             */
            let sponsoredTypes= data.map((item,)=>{
                return item.selected_sponsored_types
                })
            /**
             * extract report Names from report types
             */
            let reportTypeNames = reportTypes.map((item)=>{
                let report_types=item.map(it=>{
                    return it.reportName;
                })  
                return report_types.toString();
              })

            let sponsoredTypeNames = sponsoredTypes.map((item)=>{
                let sponsored_types=item.map(it=>{
                    return it.sponsordTypenName;
                })  
                return sponsored_types.toString();
            })
            /**
             * extract days from data and manipulate according to datatable
             */
            let scheduleArr =[]
            for(let i=0; i < data.length; i++) {
                let schedule = "";
                    if(data[i].mon == 1){
                        schedule += "Monday,";
                    }
                    if(data[i].tue == 1){
                        schedule += "Tuesday,";
                    }
                    if(data[i].wed == 1){
                        schedule += "Wednesday,";
                    }
                    if(data[i].thu == 1){
                        schedule += "Thursday,";
                    }
                    if(data[i].fri == 1){
                        schedule += "Friday,";
                    }
                    if(data[i].sat == 1){
                        schedule += "Saturday,";
                    }
                    if(data[i].sun == 1){
                        schedule += "Sunday";
                    }

                    schedule += " at "+ moment(data[i].time, 'HH:mm').format('hh:mm A')

                    scheduleArr.push(schedule);
            }

            for(let i=0; i < data.length; i++){
                let obj = Object.assign({}, data[i]);
                obj.schedule = scheduleArr[i]; 
                obj.sponsored_type = sponsoredTypeNames[i]; 
                obj.report_type = reportTypeNames[i];
                obj.serial = i+1;
                data[i] = obj; 
            }
        }
          callback(data);
      }
    })
    .catch(err => {
      if(errorcallback != null){
          errorcallback(err);
      }
    })
}

export function getMetricsData(Id,callback, errorcallback){
    axios.post(SCHEDULED_REPORTS_METRICS_APIURL,{
        scheduleId:Id
    })
    .then(res => {
        let indexes = res.data.getSelectedParameterTypesArray;
        let data = [];
        let keys = metricsMappings.map(x => Object.keys(x)[0]);
        for(var t=0;t<keys.length;t++){
            if(indexes.includes(metricsMappings[t][keys[t]])){
                data.push({[keys[t]]:res.data[keys[t]]})
            }
        }
        if(callback != null){
            callback(data);
        }
      })
    .catch(err => {
      if(errorcallback != null){
          errorcallback(err);
      }
    })
}

export function deleteSchedule(scheduleId,callback,errorcallback){
    axios.delete(`${SCHEDULED_REPORTS_DELETE_SCHEDULE}${scheduleId}/deleteSchedule`)
    .then(res => {
      if(callback != null){
            callback(res.data);
        }
      }
    )
    .catch(err => {
      if(errorcallback != null){
          errorcallback(err);
      }
    })   
}

export function getAdReportsData(callback, errorcallback){
    axios.get(SCHEDULED_REPORTS_POPUP_APIURL)
        .then(res => {
            if(callback != null){
                callback(res.data);
            }
        })
        .catch(err => {
            if(errorcallback != null){
                errorcallback(err);
            }
        })
}

export function getReportTypesApi(sponsoredTypes, callback, errorcallback) {
    axios.post(GET_REPORT_TYPE_URL,{
        sponsordTypeValue:sponsoredTypes.toString()
    }).then(res => {
        let data = res.data;
        if (data.length > 0) {
            data = data.map((obj, idx) => {
                return {
                    value: obj.id,
                    label: obj.reportName,
                    key:idx
                }
            })
        }
        if (callback != null) {
            callback(data);
        }
    }).catch(err => {
        if (errorcallback != null) {
            errorcallback(err);
        }
    })
}

export function getReportMetrics(reportTypes, callback, errorcallback){
    axios.post(GET_REPORT_METRICS_URL,{
        reportTypeValue:reportTypes.toString()
    }).then(res => {
        let data = res.data;
        if (callback != null) {
            data=filterMetrics(data)
            callback(data);
        }

    }).catch(err => {
        if (errorcallback != null) {
            errorcallback(err);
        }
    })
}

function filterData(data){
    let updatedData=data.map(x =>{ 
        return{
                id:x.id, 
                metricName: x.metricName,
                isChecked: (x.isChecked == 'true')
            }
    });

    return updatedData;
}
export function filterMetrics(data){
    let metrics = [];

    if(data["Campaign"]){
        let updatedData=filterData(data["Campaign"])
        metrics.push({Campaign: updatedData})
    }
    
    if(data["Ad Group"]){
        let updatedData=filterData(data["Ad Group"])
        metrics.push({'Ad Group': updatedData})
    }

    if(data["Product Ads"]){
        let updatedData=filterData(data["Product Ads"])
        metrics.push({'Product Ads': updatedData})
    }

    if(data["Keyword"]){
        let updatedData=filterData(data["Keyword"])
        metrics.push({Keyword: updatedData})
    }

    if(data["ASINS"]){
        let updatedData=filterData(data["ASINS"])
        metrics.push({ASINS: updatedData})
    }

    return metrics;
}

export function createSchedule(formData, callback, errorcallback){
    axios.post(SUBMIT_REPORT_URL,{
        ...formData
    }).then(res => {
       callback(res.data)
    }).catch(err => {
        if (errorcallback != null) {
            errorcallback(err);
        }
    })
} 

export function getEditFormData(Id,callback, errorcallback){
    axios.post(GET_EDIT_FORM_URL,{
            scheduleId: Id
    }).then(res => {
            if(callback != null){
                callback(res.data);
            }
        })
        .catch(err => {
            if(errorcallback != null){
                errorcallback(err);
            }
        })
}