import React, {Component} from 'react'
import clsx from 'clsx';
import 'react-date-range/dist/styles.css'; // main style file
import 'react-date-range/dist/theme/default.css';
import {DateRangePicker} from 'react-date-range';
import {Calendar} from 'react-date-range';

export default class CustomDateRangePicker extends Component {
    isMounted = false;

    constructor(props) {
        super(props)
        this.state = {
            selectionRange: {
                startDate: "",
                endDate: "",
                key: 'selection',
            },
            date: "",
            dateRangeCounter: 0,
            isVisible: false,
            maxDate: new Date()
        }

        this.wrapperRef = React.createRef();
    }

    componentDidMount() {
        this.isMounted = true;
        this.setState({
            isVisible: true,
            selectionRange: {
                startDate: (this.props.range.startDate),
                endDate: this.props.range.endDate,
                key: 'selection',
            },
            date: new Date(this.props.date)
        })
        document.addEventListener('click', this.handleClickOutside);
    }

    componentWillUnmount() {
        document.removeEventListener('click', this.handleClickOutside);
    }

    handleClickOutside = (event) => {
        if (this.wrapperRef && !this.wrapperRef.current.contains(event.target) && this.isMounted) {
            this.setState({
                isVisible: false,
            })
            this.props.helperCloseDRP();
        }
        if ($(event.target).hasClass("rdrStaticRangeLabel")) {
            this.resetAndClose(this.state.selectionRange);
        }
    }
    handleOnDateRangeChange = (ranges) => {
        let currentCounter = this.state.dateRangeCounter;
        this.setState({
            selectionRange: ranges.selection,
            dateRangeCounter: ++this.state.dateRangeCounter
        })
        if ((currentCounter + 1) > 1) {
            this.resetAndClose(ranges.selection);
        }
    }
    resetAndClose = (ranges) => {
        this.setState({
            dateRangeCounter: 0
        })
        this.props.getValue(ranges)
    }
    handleSingleDateChange = (date) => {
        this.setState({
            date
        }, () => {
            this.props.setSingleDate(date);
        })
    }

    render() {
        return (
            <div ref={this.wrapperRef}
                 className={clsx("CustomDateRangePickerParent absolute z-10", !this.props.isDateRange ? "customSingleCalender" : "", this.props.className ? this.props.className : "")}
                 style={this.state.isVisible ? {transform: "translateX(0)"} : {transform: "translateX(200%)"}}>
                {
                    this.props.isDateRange ?
                    <DateRangePicker
                    className="CustomDateRangePicker"
                    ranges={[this.state.selectionRange]}
                    onChange={this.handleOnDateRangeChange}
                    showSelectionPreview={true}
                    moveRangeOnFirstSelection={false}
                    months={2}
                    maxDate={new Date()}
                    // scroll={{ enabled: true }}
                    direction={this.props.direction}//"horizontal"|"vertical"
                /> :
                    <Calendar className="CustomDateRangePicker" onChange={this.handleSingleDateChange}
                              maxDate={this.props.maxDate ? this.props.maxDate:this.state.maxDate}
                               date={this.state.date}/>
                }
            </div>
        )
    }
}
