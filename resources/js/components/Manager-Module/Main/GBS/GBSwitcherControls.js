import React, {Component} from 'react'
import clsx from 'clsx';
import './styles.scss'
import {connect} from "react-redux"
import { withStyles } from "@material-ui/core/styles";
import {primaryColor, primaryColorLight} from "./../../../../app-resources/theme-overrides/global";
import {changeActiveParentBrand} from './apiCalls';
import {updateParentBrand} from './action';
import {SetNotificationCount} from './../../../../general-components/Notification/actions'
import TextButton from "./../../../../general-components/TextButton";
import PrimaryButton from "./../../../../general-components/PrimaryButton";
import TextFieldInput from "./../../../../general-components/Textfield";
import ThemeRadioButtons from './../../../../general-components/AsinCollectionRadioButton';
import LinearProgress from '@material-ui/core/LinearProgress';
import RadioGroup from '@material-ui/core/RadioGroup';
import FormControlLabel from '@material-ui/core/FormControlLabel';
import FormControl from '@material-ui/core/FormControl';
import Tooltip from '@material-ui/core/Tooltip';

const useStyles = theme => ({
    root: {
      '& .MuiInputBase-root':{
        marginTop: 8,        
        borderRadius: 12,
        border: "2px solid #c3bdbd8c",
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
    
});
function RadioButtonLabel ({parentBrand}){
    let label = parentBrand.brand.name;
    
    return <Tooltip 
                key={parentBrand.brand.id}  
                placement="top" 
                title={parentBrand.brand.name} 
                arrow 
                interactive
            > 
                <div>{label.length > 20 ? label.substr(0,20)+"...": label}</div> 
            </Tooltip>
}
class GBSwitcherControls extends Component {
    constructor(props){
        super(props);
        this.state = {
            isDataLoaded :false,
            isLoading:false,
            parentBrands:this.props.parentBrands,
            orignalParentBrands:this.props.parentBrands,
            showModel:false,
            selectedParentBrandId: 0,
            selectedParentBrandName: "",
        }
    }
    componentDidMount(){
        this.setState({
            selectedParentBrandId: this.props.selectedBrandId,
            selectedParentBrandName: this.props.selectedBrandName,
        })
    }
    filterBrands = (e)=>{
        let searchBrand = e.target.value;
        let newBrands = this.state.orignalParentBrands.brands.filter((brand)=>{
            return brand.brand.name.toLowerCase().includes(searchBrand.toLowerCase())
        });
        this.setState((prevState)=>({
            parentBrands:{
                ...prevState.parentBrands,
                brands:newBrands
            }
        }));
    }
    onChangeActiveParentBrandButtonClick= (e) =>{
        let tempPath = htk.history.location.pathname;
        if(this.props.selectedBrandId == this.state.selectedParentBrandId)
        {
            this.props.handleModalClose();
            return;
        }
        this.setState({
            isLoading:true
        },()=>{
            changeActiveParentBrand({
                parentBrandId:this.state.selectedParentBrandId
            },(response)=>{
                htk.history.push('/reload');
                setTimeout(()=>{
                    htk.history.push(tempPath);
                },1000)
                this.setState({
                    isLoading:false
                },()=>{
                    this.props.dispatch(SetNotificationCount(response.unseenNotiCount));
                    this.props.handleModalClose(true);
                    this.props.propHandlerForModelClosing();
                })
               
            },(error)=>{
                console.log(error)
            });
        });
        
    }
    updateSelectedParentBrand = (parentBrand) => {
        // this.props.dispatch(updateParentBrand(parentBrand))
        this.props.setSelectedBrand(parentBrand)
    }
    setSelectedParentBrandId = (e)=>{
        let selectedPB = e.target.value;
        selectedPB = selectedPB.split("|")
        this.setState({
            selectedParentBrandId: selectedPB[0],
            selectedParentBrandName: selectedPB[1]
        }, () => {
            this.updateSelectedParentBrand({id:selectedPB[0], name:selectedPB[1]});
        })
    }
    render() {
        const {classes} = this.props;
        return (
            <>
            <div className="GBSForm">
                <div className="graphLoader bg-white absolute h-full overflow-hidden w-full top-0 left-0 z-10" style={this.state.isLoading?{display:"block"}:{display:"none"}} >
                    <LinearProgress />
                    <div className="absolute flex font-bold font-mono h-full items-center justify-center overflow-hidden text-1rem text-sm w-full z-10">
                        Processing...
                    </div>
                </div>
                <div className={clsx("parentBraand pt-5")}>
                    <label className="text-xs font-normal ml-2">
                        Select Parent Brand
                    </label>
                    <div className="ThemeInput">
                        <TextFieldInput
                            placeholder="Search Brand Name Here..."
                            type="text"
                            id="parentBrand"
                            name={"parentBrand"}
                            fullWidth={true}
                            onChange={this.filterBrands}
                            classesstyle = {classes}
                        />
                    </div>
                </div>
                <div className={clsx("parentBrandsRadioButtons")}>
                    <FormControl component="fieldset">
                        <RadioGroup row aria-label="status" name="status" value={this.state.selectedParentBrandId +"|"+ this.state.selectedParentBrandName} onChange={this.setSelectedParentBrandId}>
                            {
                                this.state.parentBrands.brands.length > 0 ?
                                this.state.parentBrands.brands.map((parentBrand, index)=>{
                                    return <FormControlLabel 
                                        value={parentBrand.brand.id +"|"+ parentBrand.brand.name } 
                                        control={<ThemeRadioButtons />} 
                                        label={<RadioButtonLabel parentBrand={parentBrand}/>} 
                                        className="w-full"
                                    />
                                }):
                                <div className="amazonBrand">No Brand Found</div>
                            }
                        </RadioGroup>
                    </FormControl>  
                </div>
                <div className="flex float-right items-center justify-center my-5 mt-10 w-full">
                        <div className="mr-3">
                            <TextButton
                            BtnLabel={"Cancel"}
                            color="primary"
                            onClick={this.props.handleModalClose}/>
                        </div>
                        <PrimaryButton
                        btnlabel={"Switch Brand"}
                        variant={"contained"}
                        type="submit"
                        onClick={this.onChangeActiveParentBrandButtonClick}
                        />     
                </div>
            </div>
            </>
        )
    }
}

export default withStyles(useStyles)(connect(null)(GBSwitcherControls))
