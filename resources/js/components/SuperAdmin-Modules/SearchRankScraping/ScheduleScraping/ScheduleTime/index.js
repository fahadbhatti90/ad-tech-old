import React, {Component} from 'react'
import {connect} from "react-redux"
import {ShowSuccessMsg} from "./../../../../../general-components/successDailog/actions";
import {ShowFailureMsg} from "./../../../../../general-components/failureDailog/actions";
import {primaryColor, primaryColorLight} from "./../../../../../app-resources/theme-overrides/global";
import SingleSelect from "./../../../../../general-components/Select";
import Tooltip from "@material-ui/core/Tooltip";
import { updateSettings } from './apiCalls';

const customStyle = {
    menu: base => ({
        ...base,
        marginTop: 0,
        zIndex: 3
    }),
    control: (base, state) => ({
        background: '#fff',
        height: 32,
        border: "1px solid #c3bdbd8c",
        borderRadius: 20,
        display: 'flex',
        border: state.isFocused ? "2px solid " + primaryColor : "1px solid #c3bdbd8c", //${primaryColor}
        // This line disable the blue border
        boxShadow: state.isFocused ? 0 : 0,
        '&:hover': {
            border: state.isFocused ? "2px solid " + primaryColor : "1px solid " + primaryColorLight
        },
        fontSize: '0.72rem'
    }),
    container: (provided, state) => ({
        ...provided,
        marginTop: 8
    }),
    valueContainer: (provided, state) => ({
        ...provided,
        padding: "0px 8px",
        overflowY: "auto",

    }),
    multiValue: (styles, {data}) => {
        return {
            ...styles,
            borderRadius: 20
        };
    },
    multiValueRemove: (styles, {data}) => ({
        ...styles,
        color: data.color,
        ':hover': {
            backgroundColor: primaryColor,
            color: 'white',
            borderRadius: 20
        },
    }),
}

class ScheduleTime extends Component {
    constructor(props) {
        super(props);
        this.state = {
            scheduleTime:null,
            scheduleTimes:[],
            isLoading:false,
        }
    }
    componentDidMount(){
        let {scheduleTimes} = this.state;
        let scheduleTime = null;
        for (let index = 0; index <= 18; index++) {
            let time = "";
            if(index < 9)
            time= "0"+index+":00";
            else
            time= index+":00";

            if(time == this.props.scheduleTime.value){
                scheduleTime = {label: time, value: time, className: 'custom-class'}
            }
            scheduleTimes.push({label: time, value: time, className: 'custom-class'});
        }
        this.setState({
            scheduleTimes,
            scheduleTime
        })
    }
    onChangeHandler = (value, element) => {
        this.setState({
            scheduleTime: value,
            isLoading: true
        }, ()=>{
            updateSettings(
                this.props.url,
                {
                    scheduleTime:this.state.scheduleTime ? this.state.scheduleTime.value : "00:00",
                    setting_id:this.props.scheduleTime.id,
                },
                (response)=>{
                    this.setState({ 
                        isLoading: false
                    },()=>{
                        this.props.dispatch(ShowSuccessMsg("Successfull", response.message, true, "",null));
                    });
                },
                (error)=>{
                    console.log(error);
                    this.setState({ isLoading: false },()=>{
                        this.props.dispatch(ShowFailureMsg(error, "", true, ""));
                    });
                },
            );
        });
    }
    formatOptionLabel = ({ value, label }) => {
        let labelLimit =  35;
        let option = label.length > labelLimit ? (
          <Tooltip placement="top" title={label} arrow>
              <span>
                  {
                    ( label.substr(0, labelLimit) + "...")
                  }
              </span>
          </Tooltip>
        ) : label;
        return option;
    };
    render() {
        return (
            <>
                <div className="scheduleTime w-32 mt-3 ml-4">
                    <SingleSelect
                        placeholder="Select Time"
                        name={"scheduleTime"}
                        value={this.state.scheduleTime}
                        onChangeHandler={this.onChangeHandler}
                        formatOptionLabel={this.formatOptionLabel}
                        fullWidth={true}
                        Options={this.state.scheduleTimes}
                        styles={customStyle}
                        isLoading={this.state.isLoading}
                        customClassName="ThemeSelect"
                        id="scheduleTime"
                        isClearable = {false}
                    />
                </div>
            </>
        )
    }
}

export default connect(null)(ScheduleTime)
