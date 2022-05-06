import React, { Component } from 'react'
import './styles.scss';
import {formateData} from './../../../../../helper/helper';
import {getNotificationData, getNotificationUrl} from './apiCalls';//NOTIFICATION_ID
import {SetNotificationCount} from './../../../../../general-components/Notification/actions'
import {connect} from 'react-redux';
import LinearProgress from '@material-ui/core/LinearProgress';

class NotificationPreview extends Component {
    constructor(props){
        super(props);
        this.state = {
            notiId:this.props.notiId,
            notificationExist:false,
            details:{},
            message:"",
            notiType:0,
            notification:{},
            isLoading:false,
        }
    }
    shouldComponentUpdate(nextProps, nextState) {
        return true;
    }

    componentDidMount(){
        this.fetchNotificationData();
    }
    componentDidUpdate(prevProps) {
        // Typical usage (don't forget to compare props):
        if (this.props.notiId !== prevProps.notiId) {
            this.fetchNotificationData();
        }
    }
    fetchNotificationData = ()=>{
        this.setState({
            isLoading:!this.state.isLoading
        })
        var notiId = this.props.notiId;
        if(notiId == 0);
        notiId = this.props.match.params.notiId;
        getNotificationData({
            notiId
        },(response)=>{
            let data = response.data;
            if(data.status){
                this.setState({
                    notiId,
                    details:data.details,
                    message:data.message,
                    notiType:data.notiType,
                    notification:data.notification,
                    notificationExist:true,
                    isLoading:!this.state.isLoading
                })
               
                this.props.dispatch(SetNotificationCount( data.unseenNotiCount ));
            }else{
                this.setState({
                    isLoading:!this.state.isLoading
                })
            }
        },(error)=>{
            console.log(error)
        });
    }
    handleDownloadNotificationFile = (e) => {
        let notiId = $(e.target).attr("noti-id");
        let NotificaitonStart = getNotificationUrl();
   
        window.open(`${NotificaitonStart+notiId}/download` , '_blank');
    }
    render() {
        return (
                this.state.notificationExist ? 
                <div className="bg-white rounded-lg py-10 px-20 notiPreview relative overflow-hidden ">
                        <div className="absolute bg-white h-full left-0 top-0 w-full" style={this.state.isLoading?{display:"block"}:{display:"none"}}>
                            <LinearProgress />
                            <div className="flex h-full justify-center pt-3 text-lg text-purple-900">Loading...</div>
                        </div>
                        <div className=" text-capitalize text-gray-600">
                                <h2 className="font-normal">Notification:</h2>
                                <hr/>
                        </div>
                        <div className="mt-5">
                            {
                                this.state.notification ? Object.entries(this.state.notification).map(([key,value],i)=>{
                                    return  <div className="flex my-2" key={i}>
                                                <div className={`text-capitalize font-bold text-gray-600 text-1rem w-3/12 ${i==0?"pl-1":""}`}>
                                                    { key }
                                                </div>
                                                <div className="w-9/12 text-gray-600 text-1rem text-capitalize">
                                                    { value }
                                                </div>
                                            </div>
                                }):null
                            }
                        </div>
                        
                        <div className="mt-16 text-capitalize text-gray-600">
                            <h2 className="font-normal">Details:</h2>
                            <p> {this.state.message} </p>
                            <hr/>
                        </div>
                        <div  className="mt-5">
                            {
                                this.state.details ? Object.entries(this.state.details).map(([key,noti],i)=>{
                                    return <div className="flex my-2" key={i}>
                                            <div className="text-capitalize font-bold text-gray-600 text-1rem w-3/12">
                                                <b>
                                                    { key }
                                                </b>
                                            </div>
                                            <div className="w-9/12 text-gray-600 text-1rem text-capitalize">
                                                    {
                                                        key.toLowerCase().includes("details download link")?
                                                            <span className="hover:underline cursor-pointer text-blue-500" noti-id = {this.state.notification["ID #:"]} onClick={this.handleDownloadNotificationFile}>{noti}</span>:
                                                            key.toLowerCase().includes("completed at")?
                                                            formateData(noti):
                                                            noti

                                                    }
                                            </div> 
                                        </div>
                                }):null
                            }
                        </div>
                        {/* @endforeach */}

                </div>: 
                <div className="bg-white text-center rounded-lg py-10 px-20 notiPreview relative overflow-hidden">
                    <div className="absolute bg-white h-full left-0 top-0 w-full" style={this.state.isLoading?{display:"block"}:{display:"none"}}>
                            <LinearProgress />
                            <div className="flex h-full justify-center pt-3 text-lg text-purple-900">Loading...</div>
                    </div>
                    Sorry, Notification Not Found
                </div>
        )
    }
}
function mapStateToProps(state) {
    return { 
        notiId: state.NOTIFICATION_ID.notiId, 
    };
} 
  
export default connect(mapStateToProps)(NotificationPreview);