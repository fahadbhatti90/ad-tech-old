import{primaryColor, 
    primaryColorLight,
    primaryColorLighter,
    primaryColorLightest,
    secondaryColor, 
    shadeGrayColor, 
    grayColor, 
    grayColorLight, 
    mdGrayColor, 
    success, 
    failure 
} from "./global";
import { createMuiTheme } from "@material-ui/core/styles";

const theme = createMuiTheme({

  palette: {
    primary: {
      light: primaryColorLight,
      main: primaryColor,
      lighter:primaryColorLighter,
      lightest:primaryColorLightest
    },
    secondary: {
      light: grayColorLight,
      main: shadeGrayColor,
      dark: grayColor,
    },
  },

    overrides: {

      MuiRadio: {
        root: {
          color: "#c9c3c38a"
        },
        colorSecondary: {
          '&$checked': {
            color: primaryColor,
          }
        }
      },
      MuiFormControlLabel:{
        label:{
          fontSize: '0.8rem'
        }
      },
      MuiDialogTitle:{
        root:{
          textAlign: 'center'

        }
      },
      MuiDialogContent:{
        dividers:{
          borderTop: 0,
          borderBottom: 0
        }
      },
      MuiTooltip: {
        tooltip: {
          fontSize: "1em"
        }
      },
      MuiButton: {
        root:{
          textTransform: "none",
          padding: "4px 20px",
          borderRadius: 10
        },
        contained:{
          color:"#818181",
          boxShadow: "none",
          background:"#e5e5e5"

        },
        text:{
          padding: "3px 14px"
        },
        label:{
          fontWeight: 400
        }
      },
      MuiOutlinedInput: {
			root: {
              borderRadius: '25px',
              height: '45px',
              backgroundColor: "white",
              "&:hover:not($disabled):not($focused):not($error) $notchedOutline": {
                  border: "1px solid",
                  borderColor: primaryColorLight
                },
              "&:focused $notchedOutline": {
                  borderColor: primaryColorLight //focused
                }
                
			},
			input: {
        padding: "18px 14px",
      }
      },
      MuiInput: {
        input: {
          "&::placeholder": {
            fontSize: '0.73rem !important'
          },
        }
      },
      MuiInputLabel: {
            outlined:{
                transform: 'translate(12px, 15px) scale(1)'
            }
        },
        MuiCheckbox: {
          colorSecondary: {
            '&$checked': {
              color: primaryColor,
            },
          },
        },
         
      PrivateSwitchBase:{
        root:{
          padding: 2
        }
      }
    }
});

export { theme };
