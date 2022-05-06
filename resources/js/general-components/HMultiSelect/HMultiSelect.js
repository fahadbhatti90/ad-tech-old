import React, {Component} from "react";
import clsx from 'clsx';
import Select, {components} from "react-select";
import {primaryColor, primaryColorLight} from "./../../app-resources/theme-overrides/global"
import {styles} from "./../styles";
import {withStyles} from "@material-ui/core/styles";
import AddIcon from '@material-ui/icons/Add';
import "./styles.scss";
import {Grid} from "@material-ui/core";
import {containsObject} from "./../../helper/helper";

class HMultiSelect extends Component {
    constructor(props) {
        super(props);
        this.state = {
            items: [],
            selectedValue: null
        }
    }
    static getDerivedStateFromProps(nextProps, prevState){
        if(nextProps.value && prevState.items.length <= 0) {
            return {
                items:nextProps.value
            }
        }
        return null;
    }

    handleDelete = item => {
        this.setState({
            items: this.state.items.filter(i => i !== item)
        }, () => {
            this.props.getUpdatedItems(this.state.items);
        });
    };

    onChangeHandler = (value) => {
        let {items} = this.state;
        if (!containsObject(value, items)) {
            items.push(value)
            this.setState({
                items
            }, () => {
                this.props.getUpdatedItems(this.state.items);
            })
        }
    }

    render() {
        const {classes} = this.props;
        const DropdownIndicator = props => {
            return (
                <components.DropdownIndicator {...props}>
                    <AddIcon/>
                </components.DropdownIndicator>
            );
        };

        return (
            <>
                <Select
                    className={clsx("basic-multi-select", this.props.customClassName)}
                    classNamePrefix="select"
                    name={this.props.name}
                    options={this.props.Options}
                    closeMenuOnSelect={true}
                    isDisabled={this.props.isDisabled || false}
                    isClearable={true}
                    components={{
                        IndicatorSeparator: () => null,
                        DropdownIndicator,
                        ClearIndicator: null
                    }}
                    onChange={this.onChangeHandler}
                    value={this.props.value}
                    placeholder={this.props.placeholder}
                    styles={{
                        menu: base => ({
                            ...base,
                            marginTop: 0
                        }),
                        control: (base, state) => ({
                            overflow: 'hidden',
                            background: '#fafafa',
                            border: "1px solid #ccc2c2",
                            height: 30,
                            borderRadius: 25,
                            display: 'flex',
                            border: state.isFocused ? "2px solid " + primaryColor : "1px solid #ccc2c2", //${primaryColor}
                            // This line disable the blue border
                            boxShadow: state.isFocused ? 0 : 0,
                            '&:hover': {
                                border: state.isFocused ? "2px solid " + primaryColor : "1px solid " + primaryColorLight
                            },
                        }),
                        valueContainer: (provided, state) => ({
                            ...provided,
                            visibility: (state.hasValue || state.selectProps.inputValue) ? 'hidden' : 'visible',
                            padding: "0px 8px",
                        }),
                        multiValue: (styles, {data}) => {
                            return {
                                ...styles,
                                borderRadius: 25,
                            };
                        },
                        multiValueRemove: (styles, {data}) => ({
                            ...styles,
                            color: data.color,
                            ':hover': {
                                backgroundColor: primaryColor,
                                color: 'white',
                                borderRadius: 25
                            },
                        }),
                        placeholder: (defaultStyles) => {
                            return {
                                ...defaultStyles,
                                fontSize: '0.73rem'
                            }
                        },
                        option: (styles, {data, isDisabled, isFocused, isSelected}) => {
                            return {
                                ...styles,
                                backgroundColor: isSelected ? '#DEEBFF' : '#FFFFF',
                                backgroundColor: isFocused ? '#DEEBFF' : '#FFFFF',
                                color: '#00000'
                            };
                        }
                    }}
                />

                {(this.state.items && this.state.items.length > 0) ?
                    <Grid container justify="center" spacing={1}>
                        <Grid item xs={12}>
                            <div className="overflow-hidden parentContainer">
                                <div className="list-Items">
                                    {this.state.items.map(item => (
                                        <div className="tag-Item" key={item.value}>
                                            {item.label}
                                            <button
                                                type="button"
                                                className="button"
                                                onClick={() => this.handleDelete(item)}
                                            >
                                                &times;
                                            </button>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        </Grid>
                    </Grid> :
                    ''
                }
            </>
        );
    }
}

export default withStyles(styles)(HMultiSelect);