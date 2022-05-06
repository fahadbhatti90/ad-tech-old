import React, {Component} from 'react';
import CheckBox from "./../../../../general-components/CheckBox";

class SelectDay extends Component {
    constructor(props) {
        super(props);
        this.state = {
            M: false,
            T: false,
            W: false,
            TH: false,
            F: false,
            SA: false,
            SU: false
        }
    }

    // componentDidMount=()=>{
    //     let updateDays = this.props.selectedDays;
    //     if(updateDays){
    //         console.log('here it is')
    //         if(key in this.state){
    //
    //         }
    //     }
    // }

    static getDerivedStateFromProps(nextProps, prevState) {
        //console.log('nextProps.selectedDays', nextProps.selectedDays)
        if (nextProps.selectedDays) {
            return {
                ...nextProps.selectedDays
            }
        }
        return null;
    }

    onCheckBoxesChangeHandler = (e) => {

        this.setState({
            [e.target.name]: e.target.checked
        }, () => {
            let data = this.state;
            this.props.updateSelectedDays({selectedDays: data});
        })
    }

    render() {
        return (
            <div className={this.props.errors.selectedD.length > 0 ? "error ml-3 mt-2" : "ml-3 mt-2"} style={{minHeight: 76}}>
                <CheckBox
                    label="M"
                    size="small"
                    name={"M"}
                    checked={this.state.M}
                    onChange={this.onCheckBoxesChangeHandler}
                />
                <CheckBox
                    label="T"
                    name={"T"}
                    checked={this.state.T}
                    size="small"
                    onChange={this.onCheckBoxesChangeHandler}
                />
                <CheckBox
                    label="W"
                    name={"W"}
                    size="small"
                    checked={this.state.W}
                    onChange={this.onCheckBoxesChangeHandler}
                />
                <CheckBox
                    label="TH"
                    name={"TH"}
                    size="small"
                    checked={this.state.TH}
                    onChange={this.onCheckBoxesChangeHandler}
                />
                <CheckBox
                    label="F"
                    name={"F"}
                    size="small"
                    checked={this.state.F}
                    onChange={this.onCheckBoxesChangeHandler}
                />
                <CheckBox
                    label="SA"
                    name={"SA"}
                    size="small"
                    checked={this.state.SA}
                    onChange={this.onCheckBoxesChangeHandler}
                />
                <CheckBox
                    label="SU"
                    name={"SU"}
                    size="small"
                    checked={this.state.SU}
                    onChange={this.onCheckBoxesChangeHandler}
                />
                <div className="error pl-3">{this.props.errors.selectedD}</div>
            </div>
        );
    }
}

export default SelectDay;