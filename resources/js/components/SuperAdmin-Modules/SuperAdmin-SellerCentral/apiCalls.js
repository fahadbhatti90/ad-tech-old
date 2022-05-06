export const SCHEDULE_CRON_URL = window.baseUrl+"/mws-addCron";
export const SCHEDULED_CRON_URL = window.baseUrl+"/mws-scheduling";

export function getScheduledCrons(callback, errorcallback){
    axios.get(SCHEDULED_CRON_URL)
    .then(res => {
      if(callback != null){
        let data = res.data.allCrons;
        callback(data);
      }
    })
    .catch(err => {
      if(errorcallback != null){
          errorcallback(err);
      }
    })
  }

export function scheduleCronsCall(params,callback, errorcallback){
    axios.post(SCHEDULE_CRON_URL,params)
      .then(res => {
        if(callback != null){
          let data = res.data;
          callback(data);
        }
      })
      .catch(err => {
        if(errorcallback != null){
            errorcallback(err);
        }
      })
  }