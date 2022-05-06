import React, {useState} from 'react'

export default function SSRApi() {
    const [DtData, SetDtData] = useState({
        data:[],
        totalRows: 0,
    });
    const [isDataTableLoading, SetIsDataTableLoading] = useState(false);
    const fetchTableData = (dataForAjax, serverSideData) => {
        let params = dataForAjax ? {
            options: serverSideData,
            ...dataForAjax
        } :  {
            options: serverSideData
        };
        SetIsDataTableLoading(true);
        getTableData(props.url, params, (response)=>{
            if(props.getResponseData)
            props.getResponseData(response);

            SetDtData({
                data: response.data,
                totalRows: response.total
            }, ()=>{
                SetIsDataTableLoading(false);
            });

        }, (error)=>{
            SetIsDataTableLoading(false);
        })
    }
    const helperReloadDataTable = () => {
        fetchTableData(props.dataForAjax, serverSideData);
    }
    return {
        DtData,
        isDataTableLoading,
        fetchTableData,
        helperReloadDataTable
    }
}
