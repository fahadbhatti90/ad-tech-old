import { withStyles } from "@material-ui/core/styles";
import {primaryColor, primaryColorLight} from "../../../app-resources/theme-overrides/global";

export const styles = theme => ({
    card: {
    borderRadius: 15,
    border: '1px solid #e1e1e3',
    backgroundColor: '#fffff',
    // padding:'20px 25px 0px',
    boxShadow: "none",
    postion: 'absolute'
   },
   pageTitle:{
    fontSize: '1rem',
    fontWeight: 600,
   }
});

export const Overflowstyles = theme => ({ dialogPaper: { overflow: 'visible' } });

export const useStyles = theme => ({
    root: {
      '& .MuiInputBase-root':{
        marginTop: 8,        
        borderRadius: 12,
        border: "1px solid #c3bdbd8c",
        height: 35,
        background: '#fff'
      },
      "&:hover .MuiInputBase-root": {
        borderColor: primaryColorLight,
        borderRadius: "12px",
      },
      '& .MuiInputBase-input':{
        margin: props=>props.margin || 15,
        fontSize:'0.72rem',
        padding: '7px 0 7px'
      }
    },
    focused:{
      border: "2px solid !important",
      borderColor: `${primaryColor} !important`,
    },
    card: {
      borderRadius: 15,
      border: '1px solid #e1e1e3',
      padding:'20px 25px 0px',
      boxShadow: "none",
      paddingBottom: 25,
      marginTop: 10,
      minHeight: 200,
      overflow: 'visible'
     },
     pageTitle:{
      fontSize: '1rem',
      fontWeight: 600
     },
     datepickerClass: {
      zIndex: 1101
  }
});

export const customStyle ={
    menu: base => ({
    ...base,
    marginTop: 0
    }),
    control: (base, state) => ({
    background: '#fff',
    height: 35,
    border: "1px solid #c3bdbd8c",
    borderRadius: 12,
    display: 'flex', 
    border: state.isFocused ? "2px solid "+primaryColor : "1px solid #c3bdbd8c", //${primaryColor}
    // This line disable the blue border
    boxShadow: state.isFocused ? 0 : 0,
    '&:hover': {
        border:  state.isFocused ?"2px solid "+primaryColor:"1px solid "+primaryColorLight
    },
    fontSize: '0.72rem'
    }),
    container: (provided, state) => ({
    ...provided,
    marginTop: 8
    }),
    option: (styles, { data, isDisabled, isFocused, isSelected }) => {
      return {
        ...styles,
        backgroundColor: isSelected ? '#DEEBFF':'#FFFFF',
        backgroundColor: isFocused ? '#DEEBFF':'#FFFFF',
        cursor: isDisabled ? 'not-allowed' : 'default',
        // This is an example: backgroundColor: isDisabled ? 'rgba(206, 217, 224, 0.5)' : 'white'
        backgroundColor: isDisabled ? '#f6f6f6' : '#FFFFF',
        color: '#00000'
      };
    },
    valueContainer: (provided, state) => ({
    ...provided,
    padding: "0px 8px",
    overflowY: "auto",

    }),
    multiValue: (styles, { data }) => {
    return {
        ...styles,
        borderRadius:12
    };
    },
    multiValueRemove: (styles, { data }) => ({
    ...styles,
    color: data.color,
    ':hover': {
        backgroundColor: primaryColor,
        color: 'white',
        borderRadius: 12
    },
    }),
}