import React, { Component } from 'react'
import { withStyles } from '@material-ui/core/styles';
import {primaryColor} from "./../../../app-resources/theme-overrides/global";
import FormControl from '@material-ui/core/FormControl';
import FormGroup from '@material-ui/core/FormGroup';
import FormControlLabel from '@material-ui/core/FormControlLabel';
import Checkbox from '@material-ui/core/Checkbox';

const useStyles = (theme) => ({
    root: {
      display: 'flex',
      flexDirection: 'row',
      justifyContent:"space-between"
    },
    formControl: {
      margin: theme.spacing(3),
    },
});
const ThemeCheckbox = withStyles({
    root: {
      color: primaryColor,
      '&$checked': {
        color: primaryColor,
      },
    },
    checked: {},
  })((props) => <Checkbox {...props} size={"small"}/>);    
class EventsCheckBox extends Component {
    isMounted = false;
    constructor(props){
      super(props);
      this.state = {
        andon: props.andon,
        crap: props.crap,
      }
    }
    static getDerivedStateFromProps(nextProps, prevState){
          return {
            andon:nextProps.andon,
            crap:nextProps.crap,
          }
    }
    handleChange = (event) => {
      let selectedInputs = $(".eventCheckBox input:checked");
      let events = [];
      $.each(selectedInputs, function (indexInArray, valueOfElement) { 
         events.push($(valueOfElement).val())
      });
      this.props.helperGetEventsIds(events,event.target.name == "andon" ? event.target.checked : this.state.andon,event.target.name == "crap" ? event.target.checked : this.state.crap, event.target.name);
      this.setState({
        [event.target.name]: event.target.checked 
      });
    };
    render(){
      const classes = this.props;
      this.isMounted = true;
      const { andon, crap} = this.state;
      return (
        <div className={classes.root}>
          <FormControl component="fieldset" className={classes.formControl}>
            <FormGroup className="eventsCheckBoxGroup justify-between">
              
              <FormControlLabel
                disabled = {this.props.disabled}
                control={<ThemeCheckbox className="eventCheckBox" checked={andon} value="4" onChange={this.handleChange} name="andon" />}
                label="Andon Cord"
              />
              <FormControlLabel
                control={<ThemeCheckbox className="eventCheckBox" checked={crap} value="5" onChange={this.handleChange} name="crap" />}
                label="Crap"
                disabled = {this.props.disabled}
              />
            </FormGroup>
          </FormControl>
        </div>
      );

    }
}

export default withStyles(useStyles)(EventsCheckBox)