import {grayColorLight, primaryColorLight} from "./../../app-resources/theme-overrides/global";
const backgroundColor = "#f4f4f4"
import BackgroundImage from "./../../app-resources/assets/LoginBG.jpg"
export const styles = theme => ({
    root: {
      flexGrow: 1,
    },
    paper: {
      padding: theme.spacing(2),
      textAlign: 'center',
      color: theme.palette.text.secondary,
      height: '100%',
      background: backgroundColor
    },
    paperContainer: {
        backgroundImage: `url(${window.assetUrl+BackgroundImage})`,
        minHeight: 625,
        width:"100%",
        backgroundPosition: 'center',
        backgroundRepeat: 'no-repeat',
        backgroundSize: 'cover',
        position: 'relative'
    },
    container:{
        position: 'absolute',
        right: 0,
        // margin: 20,
        maxWidth: '33%',
        minWidth: '33.2%',
        padding: 16,
        display: 'flex',
        flexDirection: 'column',
        alignItems: 'center',
        background: backgroundColor,
        minHeight: 625,
    },
    typo:{
        fontWeight: 600,
        color: grayColorLight,
        fontSize: '0.75rem'
    },
    formControlLabel:{
      color: '#797878',
      fontSize: '1rem',
      fontWeight: 400,
      margin: 0
    }
})