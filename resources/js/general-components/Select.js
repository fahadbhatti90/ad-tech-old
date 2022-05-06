import React, { Component } from "react";
import clsx from 'clsx';
import Select from "react-select";
import {primaryColor,primaryColorLight} from "../app-resources/theme-overrides/global"
import "./styles.scss";

export default class SingleSelect extends Component {
  handleOnchage = ( e, value ) => {
    this.props.onChangeHandler(e,value);
  }
  render() {
    return (
      <>
      <Select
        components={{
          IndicatorSeparator: () => null
        }}
        className={clsx("basic-single",this.props.customClassName)}
        classNamePrefix="select"
        name={this.props.name}
        isClearable={this.props.isClearable ? this.props.isClearable : true}
        options={this.props.Options}
        placeholder={this.props.placeholder}
        onChange={this.handleOnchage}
        value={this.props.value}
        styles={
          this.props.styles?this.props.styles:
          {
          menu: base => ({
            ...base,
            marginTop: 0
          }),
          control: (base, state) => ({
            background: "#fafafa",
            height: 30,
            border: "1px solid #ccc2c2",
            borderRadius: 25,
            display: 'flex', 
            border: state.isFocused ? "2px solid "+primaryColor : "1px solid #ccc2c2", //${primaryColor}
            // This line disable the blue border
            boxShadow: state.isFocused ? 0 : 0,
            '&:hover': {
               border:  state.isFocused ?"2px solid "+primaryColor:"1px solid "+primaryColorLight
            },
            // fontSize: '1rem'
           }),
          container: (provided, state) => ({
            ...provided,
            marginTop: 8
          }),
          valueContainer: (provided, state) => ({
            ...provided,
            overflow: "visible"
          }),
          placeholder: (defaultStyles) => {
            return {
                ...defaultStyles,
               fontSize: '0.73rem' 
            }
        },
        option: (styles, { data, isDisabled, isFocused, isSelected }) => {
          return {
            ...styles,
            backgroundColor: isSelected ? '#DEEBFF':'#FFFFF',
            backgroundColor: isFocused ? '#DEEBFF':'#FFFFF',
            color: '#00000'
          };
        }
        }}
        theme={theme => ({
          ...theme,
          colors: {
              ...theme.colors,
              neutral50: '#9a9999',  // Placeholder color
          },
      })}
        {...this.props}
      />
     </>
    );
  }
}