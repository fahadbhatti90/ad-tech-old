import React, {Component} from 'react';
import Card from '@material-ui/core/Card';
import "./../LabelOverride.scss"
import {connect} from "react-redux"
import IconBtn from "./../../../../general-components/IconBtn";
import {ShowSuccessMsg} from "./../../../../general-components/successDailog/actions";
import {ShowFailureMsg} from "./../../../../general-components/failureDailog/actions";
import GetAppIcon from '@material-ui/icons/GetApp';
import PublishIcon from '@material-ui/icons/Publish';
import LinearProgress from '@material-ui/core/LinearProgress';
import {
    uploadAliasFile
} from './../apiCalls';
class LabelOverrideIO extends Component {
    constructor(props) {
        super(props)
        this.state = {
            isLoading:false
        };
    }
    handleDownloadLabelOverrideFileButtonClick = e => {
        let url = null;
        switch ($(e.target).text()) {
            case "Product":
                url = `${baseUrl}/admin/labelOverride/download/1/attribute`;
                break;
            case "Sub Category":
                url = `${baseUrl}/admin/labelOverride/download/2/attribute`;
                break;
            case "Category":
                url = `${baseUrl}/admin/labelOverride/download/3/attribute`;
                break;
            case "Brand":
                url = `${baseUrl}/admin/labelOverride/download/4/attribute`;
                break;
        }
        
        window.open( url, '_blank');
    }
    handleUploadLabelOverrideFileButtonClick = e =>{
        this.inputElement.click();
    }
    handleUploadLabelOverrideFileInputChage = e => {
        this.setState({
            isLoading:true
        })
        var DatatoUpload = new FormData();
        if( window.FormData === undefined )
        {
            return;
        }
        DatatoUpload.append("attributeFile",$(e.target).get(0).files[0]);
        uploadAliasFile(DatatoUpload, (response)=>{
            $(this.inputElement).val('');
            this.props.dispatch(ShowSuccessMsg("Successfull", response.message, true, "",this.props.helperReloadDataTable(true)));
            this.setState({
                isLoading:false
            })
        }, (error)=>{
            $(this.inputElement).val('');
            this.setState({
                isLoading:false
            })
            console.log(error);
            this.props.dispatch(ShowFailureMsg(error, "", true, ""));
        })
    }
    render() {
        return (
            <>
                <div style={{display: 'table', tableLayout: 'fixed', width: '100%'}} className="labelOverride mb-10">
                    <Card className="overflow-hidden relative">
                        <div className="graphLoader bg-white absolute h-full overflow-hidden w-full top-0 left-0 z-10" style={this.state.isLoading?{display:"block"}:{display:"none"}} >
                            <LinearProgress />
                            <div className="absolute flex font-bold font-mono h-full items-center justify-center overflow-hidden text-1rem text-sm w-full z-10">
                                Uploading...
                            </div>
                        </div>
                        <div className="flex p-5 bg-gray-100">
                            <div className="font-semibold w-full">Use Buttons To The Right For Downloading File Format Of Respective Attribute</div>
                        </div>
                        <div className="flex justify-between w-full py-5 px-5">
                            <div className="flex w-6/12 justify-around">
                                <IconBtn
                                    BtnLabel="Product"
                                    variant={"contained"}
                                    className="whitespace-no-wrap"
                                    icon={<GetAppIcon/>}
                                    onClick={this.handleDownloadLabelOverrideFileButtonClick}
                                />
                                <IconBtn
                                    BtnLabel="Sub Category"
                                    variant={"contained"}
                                    className="whitespace-no-wrap"
                                    icon={<GetAppIcon/>}
                                    onClick={this.handleDownloadLabelOverrideFileButtonClick}
                                />
                                <IconBtn
                                    BtnLabel="Category"
                                    variant={"contained"}
                                    className="whitespace-no-wrap"
                                    icon={<GetAppIcon/>}
                                    onClick={this.handleDownloadLabelOverrideFileButtonClick}
                                />
                                <IconBtn
                                    BtnLabel="Brand"
                                    variant={"contained"}
                                    className="whitespace-no-wrap"
                                    icon={<GetAppIcon/>}
                                    onClick={this.handleDownloadLabelOverrideFileButtonClick}
                                />
                            </div>
                            <input ref={input => this.inputElement = input} type="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" class="hidden" onChange={this.handleUploadLabelOverrideFileInputChage}/>
                                <IconBtn 
                                    BtnLabel="Upload"
                                    variant={"contained"}
                                    className="whitespace-no-wrap"
                                    icon={<PublishIcon/>}
                                    onClick={this.handleUploadLabelOverrideFileButtonClick}
                                />
                        </div>
                    </Card>
                </div>
            </>
        )
    }
}


export default connect(null)(LabelOverrideIO)
