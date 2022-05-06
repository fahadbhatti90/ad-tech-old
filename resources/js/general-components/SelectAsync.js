import React, { Component } from "react";
import clsx from 'clsx';
import Select, { components }  from "react-select";
import {primaryColor,primaryColorLight} from "../app-resources/theme-overrides/global"
import "./styles.scss";
import Tooltip from "@material-ui/core/Tooltip";
const MenuList = function MenuList(props) {
  const children = props.children;

  if (!children.length) {
      return (<div className="myClassListName">{children}</div>);
  }

  return (
          <div className="myClassListName">
              {children.length && children.map((key, i) => {
                  delete key.props.innerProps.onMouseMove; //FIX LAG!!
                  delete key.props.innerProps.onMouseOver;  //FIX LAG!!
                
                  return (
                      <div className="myClassItemName" key={i}>{key}</div>
                  ); 
              })}
          </div>
  );
};
export function generateOptions(size = 0, orignalOptions, i = 0, options = []) {
  if (i >= size) return options;
  const option = orignalOptions[i];
  return generateOptions(size, orignalOptions, i + 1, [ ...options, option ]);
}

export default class SelectAsync extends Component {
  constructor(props){
    super(props);
    this.state = {
        page:1,
        selectedBrand:0,
        Options:[],
        OrignalOptions:[],
        totalPages:0,
        filteredOptions:[],
        placeholder:"",
        value:"",
        isSearching:false
    }
  }
  handleOnchage = ( value, e ) => {
    this.props.onChangeHandler(value, e);
  }
  static getDerivedStateFromProps(nextProps, prevState){
      if((nextProps.Options && prevState.OrignalOptions.length <= 0 || nextProps.Options.length <= 0 || JSON.stringify(nextProps.Options) != JSON.stringify(prevState.OrignalOptions)) && !prevState.isSearching) {
          let totalPages = Math.ceil(nextProps.Options.length / 10);
          return {
            Options:nextProps.Options,
            OrignalOptions:nextProps.Options,
            totalPages,
            selectedBrand:nextProps.selectedBrand
          }
      }
      return null;
  }
  handlePrevButtonClick = () =>{
    if(this.state.page == 1) return;
    this.setState({
      page:this.state.page == 1?this.state.page :this.state.page - 1,
    })
  }
  handleNextButtonClick = () =>{
    let newPage = this.state.page + 1;
    if(newPage > this.state.totalPages  )
    {  
      return
    };
    this.setState({
      page:(newPage >= this.state.totalPage ? totalPage : newPage),
    })
  }
  filterOrignalData =(value) =>{
      return this.state.OrignalOptions.filter(row => {
          return row.label.toString().toLowerCase().includes(value.toLowerCase())
      });
  }
  handleOnInputChange = (inputValue) =>{
      if(inputValue.length > 0){
        this.setState({
          Options:inputValue.length > 0 ? [] : this.state.OrignalOptions,
          isSearching:true
        },()=>{
            var result = this.filterOrignalData(inputValue);
            if(result.length <= 0) return;
            let totalPages = Math.ceil(result.length / 10);
            this.setState({
                Options:result,
                page:1,
                totalPages:totalPages,
                value:inputValue,
                isSearching:false
            })
      })
    }else{
      let data = this.state.OrignalOptions;
      let totalPages = Math.ceil(data.length / 10);
      this.setState({
          Options:data,
          page:1,
          totalPages:totalPages,
          value:inputValue,
          isSearching:false
      })
    }
  }
  formatOptionLabel = ({ value, label, orignalLable }) => {
      if(value=="pagination") return label;
      let labelLimit = this.props.labelLimit ? this.props.labelLimit  : 25;
      return (
        <Tooltip placement="top" title={label} arrow>
            <span>
                {
                  (label.length > labelLimit ? label.substr(0, labelLimit) + "..." : label)
                }
            </span>
        </Tooltip>
      )
  };
  filterOption = (option, inputValue) => {
    return this.state.Options.length > 0;
  };
  render() {
    const { page } = this.state;
    let pageStart = (page*10)-10;
    let size = page*10;
    size = size > this.state.Options.length ? this.state.Options.length : size;
    const options = this.state.Options && this.state.Options.length > 0 ?  [
      ...generateOptions(size,this.state.Options,pageStart),
      { label: <div className="LoadMoreButton cursor-pointer flex justify-around"><div className="hover:bg-gray-200 p-3 prevCustom w-1/2"  onClick={this.handlePrevButtonClick}>Previous</div><div className="hover:bg-gray-200 nextCustom p-3 w-1/2" onClick={this.handleNextButtonClick}>Next</div></div>, value: 'pagination',  disabled: true }
    ]:[];

    return (
      <>
      <Select
       components={{
        IndicatorSeparator: () => null,
      }}
      className={clsx("basic-single asyncSelect",this.props.customClassName)}
      classNamePrefix="select"
      name={this.props.name}
      isClearable
      options={options}
      placeholder={this.props.placeholder}
      value={this.props.value}
      // menuIsOpen
      onChange={this.handleOnchage}
      filterOption = {this.filterOption }
      onBlurResetsInput={false}
      formatOptionLabel={this.formatOptionLabel}
      onInputChange={this.handleOnInputChange}
      isLoading={this.props.isLoading}
      blurInputOnSelect={(e)=>e.preventDefault()} 
      onBlur={e=>e.preventDefault()}
      onCloseResetsInput={false} 
      isOptionDisabled={option => option.value == 'pagination'}
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