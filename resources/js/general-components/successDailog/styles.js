import {
    primaryColor,
  } from "./../../app-resources/theme-overrides/global";
  
  export const styles = theme => ({
  
    item: {
      padding: "5px 12px !important"
    },
    modalCnt: {
      maxWidth: 730,
      padding: 20,
      minWidth: 310
    },
    defaultBtn: {
      color: "white",
      fontWeight: "bold",
      textTransform: "none",
      borderRadius: "2em",
      padding: "8px 30px",
      "&:hover": {
        backgroundColor: primaryColor
      }
    }
  });
  