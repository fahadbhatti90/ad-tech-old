import React, { Component } from 'react';
import clsx from 'clsx';
import './CustomMaterailize.scss';
import TextFieldsIcon from '@material-ui/icons/TextFields';
import AddIcon from '@material-ui/icons/Add';
import DeleteIcon from '@material-ui/icons/Delete';
import SettingsIcon from '@material-ui/icons/Settings';
import CloseIcon from '@material-ui/icons/Close';
import ArrowForwardIosIcon from '@material-ui/icons/ArrowForwardIos';
import ArrowBackIosIcon from '@material-ui/icons/ArrowBackIos';
import CreateIcon from '@material-ui/icons/Create';
import { findDOMNode } from 'react-dom';
import { getTags, addTags, updateTag, deleteTag, removeTagsInBulk, assingTag} from './apiCalls';
import {
    getNewDataOnTagDelete,
    getNewDataOnTagEdit,
    getNewDataOnTagAssign,
    getNewDataOnTagBulkUnAssignment
} from './TagManagerHelper'
import $ from 'jquery';
import './TagManager.scss';
import './CampaignTagging.scss';

const DotForTM = (props) => <span className="itemAdded"></span>;
const ExtraDotCounterForTM = (props) => <span className="extraDotCounter">+{props.count}</span>;
function generate(element,elements) {
    return Array.from({ length: elements }, (_, idx) => `${++idx}`).map((value,index)=>{
        return React.cloneElement(element, {
            key: value,
        })
    });
}//end function

function isAlphaNumaric(value) {
    var letters = /^[0-9a-zA-Z]/gi;
    if (value.match(letters) == null) return false;
    return true;
}//end function
export default class TagManager extends Component {
    constructor(props){
        super(props);
        this.state = {
            dotsLimit:0,
            dotCounter:props.dots,
            move:false,
            isMoved:true,
            showAssignTagPopup:false,
            showSelectTagTypePopup:true,
            isTagEditing:false,
            tags:[],
            TagData:{
                tagType:null,
                tag:[],
                isSaving:false,
            },
            tagPopup:{
                isLoading:false,
                isEditing:false,
                showAssignTagButton:false,
            },
            editTagInput:{
                value:"#",
                placeHolder:"Write Tag Then Press Enter",
                isValid:true,
                isLoading:false,
            },
            tagInput:{
                value:"#",
                placeHolder:"Write Tag Then Press Enter",
                isValid:true,
                isLoading:false,
            }
        }
    }
    componentDidMount(){
        let itemCounterWidth = $(".itemCounts").width();
        let dotsLimit = Math.floor(itemCounterWidth / 12)-1;
        this.setState({
            dotsLimit,
        })
    }
    static getDerivedStateFromProps(nextProps, prevState) {
    
        if (nextProps.dots) {
            
          return ({dotCounter : nextProps.dots});
        }
        return null;
    }
    handleAddTagInputBoxMovement = (e)=>{
        this.setState((state) => {
            return {
                ...state,
                showAssignTagPopup:false,
                move:!state.move,
                isMoved:false
            }
        });
       
        if(!this.state.move)
        setTimeout(() => {
            $("#tag").focus();
        }, 200);
    }
    setTagTypeValue = (e) =>{
        $(".tags.tagManagerSelectionElements .selectedTagInputs").prop("checked",false);
        let tagType = e.target.value;
        this.setState((prevState)=>({
            showSelectTagTypePopup:false,
            TagData:{
                ...prevState.TagData,
                tagType:tagType,
                tag:{},
            },
            tagPopup: {
                ...prevState.tagPopup,
                showAssignTagButton: false
            }
        }));
    }
    handleAssignTagPopupOpen = (e) =>{
        if (this.state.tagPopup.isLoading) return;
        var thisObj = e.target;
        var _self = this;
        if ($(".tagGroupManager .tags-container").is(":visible")) {
            this.setState({
                showAssignTagPopup:!this.state.showAssignTagPopup
            })
          return;
        }
        // if ($(".tagGroupManager > .row > .progress").is(":visible")) return;
    
        this.setState((prevState)=>({
            move:false,
            tagPopup:{
                ...prevState.tagPopup,
                isEditing:true,
                isLoading:true,
        }
        }));
        getTags(
            {type:this.props.type},
            (response)=>{
            _self.setState((prevState)=>({
                tags:response.data,
                showSelectTagTypePopup:true,
                showAssignTagPopup:true,
                TagData:{
                    ...prevState.TagData,
                    tagType:null
                },
                tagPopup:{
                    ...prevState.tagPopup,
                    isEditing:false,
                    isLoading:false,
                }
            }));
            },(error)=>{
            _self.setState((prevState)=>({
                tagPopup:{
                    ...prevState.tagPopup,
                    isEditing:false,
                    isLoading:false,
                }
            }));
        });
    }
    handleOnTagSelection = (e) => {
        let tagsSelected = $(".tags.tagManagerSelectionElements .selectedTagInputs:checked");
        let tags = {};
        $.each(tagsSelected, function (indexInArray, valueOfElement) { 
            let tagId =  $(valueOfElement).parents(".editBox").attr("id");    
            tags[tagId] = $(valueOfElement).parents(".editBox").find(".tag .tag-left span")
                            .text()
                            .trim();
        });
        if($(".tags.tagManagerSelectionElements .selectedTagInputs").is(":checked")){
            this.setState((prevState)=>({
                tagPopup:{
                    ...prevState.tagPopup,
                    showAssignTagButton:true,
                },
                TagData:{
                    ...prevState.TagData,
                    tag:tags
                }
            }));
        }
        else{
            this.setState((prevState)=>({
                tagPopup:{
                    ...prevState.tagPopup,
                    showAssignTagButton:false,
                },
                TagData:{
                    ...prevState.TagData,
                    tag:[]
                }
            }));
        }
    }
    handleAddTagOnKeyUp = (e)=>{
       if(this.state.tagInput.isLoading){
            return;
        } 

        let tag = e.target.value;
        const $this = e.target;
        var _self = this;
        if (!tag.includes("#") || tag[0] != "#") {
            tag = tag.replace("#", "");
            _self.helperSetAddTagInputValue("#" + tag, null, null);
        }
    
        if (e.keyCode == 13) {
          if ($($this).hasClass("invalid")) return;
          if (tag.length <= 1) {
            _self.helperShowAddTagInputInvalid();
            return;
          } //end if
          _self.helperSetAddTagLoader(true);

          addTags(
            {
                type:this.props.type,
                tag: tag,
            },//success
            ()=>{
                if(this.props.type == 1 && this.props.showFilter){
                    this.props.helperLoadFilterAgain();
                }
                _self.helperSetAddTagInputValue("#",null, null);
                _self.setState({
                    move:false
                })
              _self.helperSetAddTagLoader(false);
            },//error
            ()=>{
                _self.helperShowAddTagInputInvalid();
                _self.helperSetAddTagLoader(false);
            },
          );
         
        } //end if
    }
    handleEditTagOnKeyUp = (e)=>{
        if(this.state.editTagInput.isLoading){
             return;
         } 
 
         let newTag = e.target.value;
         const $this = e.target;
         var _self = this;
         if (!newTag.includes("#") || newTag[0] != "#") {
             newTag = newTag.replace("#", "");
             _self.helperSetEditTagInputValue("#" + newTag, null, null);
         }
     
         if (e.keyCode == 13) {
           if ($($this).hasClass("invalid")) return;
           if (newTag.length <= 1) {
             _self.helperShowEditTagInputInvalid();
             return;
           } //end if
           _self.helperSetEditTagLoader(true);
           
           let tagId = $(e.target).attr("tag-id");
           updateTag(
            {type:this.props.type,tagId,newTag},
            (response)=>{
                
                if(this.props.type == 1 && this.props.showFilter){
                    this.props.helperLoadFilterAgain();
                }
                _self.props.showDataTableLoader(false);
                _self.helperSetEditTagLoader(false);
                //Updateing Tags
                _self.setState((prevState)=>({
                    tags:response.tags,
                    TagData:{
                        ...prevState.TagData,
                        tagType:null
                    },
                    tagPopup:{
                        ...prevState.tagPopup,
                        isEditing:false,
                    }
                }));
                //Updateing Tags
                //Reloading Updated Data in Datatable
                
                if(_self.props.updateDataTable)
                _self.props.updateDataTable(getNewDataOnTagEdit(_self.props.orignalData, tagId, newTag));

                //Removing Edit Tag INPUT Tag
                const editInput = findDOMNode(_self.refs[`editInput${tagId}`]);

                $(editInput)
                .parents(".editBox")
                .removeClass("edit");

                $(editInput)
                .hide()
                //Removing Edit Tag INPUT Tag
                
            },
            (error)=>{
                _self.helperSetEditTagLoader(false);
                _self.helperShowEditTagInputInvalid();
            }
           );
        } //end if
    }
    handleTagKeyPress = (e) =>{
        var letters = /^[0-9a-zA-Z]/gi;
        if (!isAlphaNumaric(e.key) || e.target.value.length > 18) e.preventDefault();
    }
    helperSetEditTagInputValue = (value,placeHolder,isValid)=>{
        const {editTagInput} = this.state;
        
        if(!value && !placeHolder && isValid == null) return;

        if(value){
            editTagInput.value = value;
        }
        if(placeHolder)
        {
            editTagInput.placeHolder = placeHolder;
        }
        if(isValid != null)
        {
            editTagInput.isValid = isValid;
        }
        this.setState({
            editTagInput: (editTagInput)
        });
    }
    helperSetAddTagInputValue = (value,placeHolder,isValid)=>{
        const {tagInput} = this.state;
        
        if(!value && !placeHolder && isValid == null) return;

        if(value){
            tagInput.value = value;
        }
        if(placeHolder)
        {
            tagInput.placeHolder = placeHolder;
        }
        if(isValid != null)
        {
            tagInput.isValid = isValid;
        }
        this.setState({
            tagInput: (tagInput)
        });
    }
    helperSetAddTagLoader = (isLoading)=>{
        const {tagInput,tagPopup} = this.state;
        tagInput.isLoading = isLoading;
        tagPopup.isLoading = isLoading;
        this.setState({
            tagInput,
            tagPopup
        });
    }
    helperSetEditTagLoader = (isEditing)=>{
        this.handleTagPopupLodaer(isEditing);
        this.setState((prevState)=>({
            tagPopup:{
                ...prevState.tagPopup,
                isEditing:isEditing,
            }
        }));
    }
    helperShowAddTagInputInvalid() {
        this.helperSetAddTagInputValue(null,"Please Enter Some Thing", false);
        let _self = this;
        setTimeout(function() {
            _self.helperSetAddTagInputValue(null,"Write Tag Then Press Enter", true);
        }, 500);
    }
    helperShowEditTagInputInvalid() {
        this.helperSetEditTagInputValue(null,"Please Enter Some Thing", false);
        let _self = this;
        setTimeout(function() {
            _self.helperSetEditTagInputValue(null,"Write Tag Then Press Enter", true);
        }, 500);
    }
    handleEditTagOnChange = (e) =>{
        this.helperSetEditTagInputValue(e.target.value,null);
    }
    handleAddTagOnChange = (e) =>{
        this.helperSetAddTagInputValue(e.target.value,null);
    }
    validatePastEvent = (e) => {
        var reg = /[^a-zA-Z0-9]/g;
        const pastObj = e.target;
        // access the clipboard using the api
        var pastedData = e.clipboardData.getData("text");
        var result = pastedData.match(reg);
        if (result != null) e.preventDefault();
        setTimeout(function() {
          if (pastObj.value.length > 18) {
            var orignal = pastObj.value;
            if (!orignal.includes("#")){
              this.setState({
                addTagValue: ("#" + orignal.substring(0, 19))
              });
            }
            else {
                this.setState({
                    addTagValue: (orignal.substring(0, 19))
                });
            }
          }
        }, 100);
    }
    handleTagMangerPopupClose = (e) => {
        this.setState((prevState)=>({
            TagData:{
                ...prevState.TagData,
                tagType:null
            }
        }));
       this.props.onTagPopupClose(true); 
    }
    handleSelectTagTypePopupToggling = (e) =>{
        this.setState({
            showSelectTagTypePopup:!this.state.showSelectTagTypePopup
        })
    }
    handleTagPopupLodaer = (show)=>{
        this.setState((prevState)=>({
            TagData:{
                ...prevState.TagData,
                isSaving:show,
            }
        }));
    }
    handleAssignTagButtonClick = (e) => {
        let tags = this.state.TagData.tag;
        let asins = this.props.selectedObject;
        let _this = this;
        if(Object.size(tags) <= 0 || Object.size(asins) <= 0 ){
            return ;
        }
        if((this.props.type == "2" && this.state.TagData.tagType == null )){
            this.setState({
                showSelectTagTypePopup:true,
            })
            return ;
        }
        this.handleTagPopupLodaer(true);
        var ajaxData = {
            asins: asins,
            tagsObj: tags,
            type: this.state.TagData.tagType,
            _token: $("body").attr("csrf")
          };
          assingTag(
              {
                  type:this.props.type,
                  ajaxData
              },
              (response)=>{
                  _this.handleTagPopupLodaer(false);

                    
                    if(_this.props.updateDataTable){
                        let newData = getNewDataOnTagAssign(_this.props.orignalData, ajaxData, this.props.type, this.props.type == 1 ?"ASIN":"campaignId");
                        
                        if(_this.props.updateDataTable)
                        _this.props.updateDataTable(newData);
                    }
                  _this.props.onTagPopupClose(true); 
                  _this.props.showDataTableLoader(false);
              },
              (error)=>{
                _this.handleTagPopupLodaer(false);
                _this.props.showDataTableLoader(false);
              },
          )  
       
    }
    handleOnTagEditButtonClick = (e) => {
        const editButton = e.target;
        let tagId = $(editButton).attr("id");
        let tag = $(editButton).attr("tag");
         
        tagId = typeof tagId == "undefined" ? $(editButton).parents("svg").attr("id") :tagId;
        tag = typeof tag == "undefined" ? $(editButton).parents("svg").attr("tag") :tag;
        const editInput = findDOMNode(this.refs[`editInput${tagId}`]);
        $(editInput)
        .parents(".editBox")
        .addClass("edit");
        $(editInput)
        .val(tag);
        $(editInput)
        .show()
        .focus();
    }
    handleOnTagDeleteButtonClick = (e) => {
        const deleteButton = e.target;
        let tagId = $(deleteButton).attr("id");
        tagId = typeof tagId == "undefined" ? $(deleteButton).parents("svg").attr("id") :tagId;{}

        var _self = this;
        _self.helperSetEditTagLoader(true);
        deleteTag(
            {type:this.props.type,tagId},
            (response)=>{
                
                if(this.props.type == 1 && this.props.showFilter){
                    this.props.helperLoadFilterAgain();
                }
                _self.helperSetEditTagLoader(false);

                this.props.showDataTableLoader(false);
                //Updateing Tags
                _self.setState((prevState)=>({
                    tags:response.tags,
                    TagData:{
                        ...prevState.TagData,
                        tagType:null
                    },
                    tagPopup:{
                        ...prevState.tagPopup,
                        isEditing:false,
                    }
                }));
                //Updateing Tags
                //Reloading Updated Data in Datatable
                if(_self.props.updateDataTable)
                _self.props.updateDataTable(getNewDataOnTagDelete(_self.props.orignalData, tagId));
            },//success
            (error)=>{
                _self.helperSetEditTagLoader(false);
                _self.helperShowEditTagInputInvalid();
            },//error
        )
    }
    handleUnAssignTagsInBulk = (e) => {
        this.helperSetAddTagLoader(true);
        let _this = this;
        removeTagsInBulk(
            {
                type:this.props.type,
                selectedObject:this.props.selectedObject//asins
            },
            (response) => {
                _this.helperSetAddTagLoader(false);
                
                this.props.showDataTableLoader(false);
                if(_this.props.updateDataTable)
                _this.props.updateDataTable(getNewDataOnTagBulkUnAssignment(_this.props.orignalData, this.props.selectedObject, this.props.type == 1 ?"ASIN":"campaignId"));

                _this.props.onTagPopupClose(true); 
                // _this.props.showDataTableLoader(false);
            },
            (error) => {
                _this.helperSetAddTagLoader(false);
            }
        )
    }
    render() {
        const {dotsLimit, dotCounter, move, showAssignTagPopup, showSelectTagTypePopup, tagInput, tagPopup, tags, TagData} = this.state;
        var totalDots = [];
        if(dotCounter <= dotsLimit) {
            totalDots = generate(<DotForTM/>,dotCounter);
        }else{
            totalDots = generate(<DotForTM/>,dotsLimit);
            totalDots.push(generate(<ExtraDotCounterForTM count={(dotCounter-dotsLimit)} />,1));
        }
        return (
            <>
                <div className="tagGroupManager shadow materializeCss">
                    <div className="flex row">
                        <div className="progress"  style={tagPopup.isLoading ? {display:"block"}:{display:"none"}}>
                            <div className="indeterminate"></div>
                        </div>
                        <div className="flex h-full items-center justify-center m-0 p relative section1 sectionCustom text-center text-white w-1/12">
                            <div className="counter">{dotCounter}</div>
                        </div>
                        <div className="font-hairline h-full overflow-hidden section2 sectionCustom sm:w-4/12 w-8/12 whitespace-no-wrap">
                            <div className={clsx("min-h-full pl-5 pt-2 statsSection ",move ?"moved":"")} style={ move ? {transform:"translateY(-100%)"}:{}}>
                                <span className="itemLabel">Items</span> Selected
                                <div className="itemCounts">
                                    {
                                        dotCounter > 0 ?
                                            totalDots
                                        :
                                        null
                                    }
                                </div>
                            </div>
                            <div className={clsx("min-h-full pl-5 pt-2 inputSection relative")} style={ move ? {transform:"translateY(-100%)"}:{}}>
                                <input 
                                type="text" 
                                name="tag" 
                                id="tag" 
                                autoComplete="off" 
                                spellCheck="false" 
                                value={tagInput.value} 
                                placeholder={tagInput.placeHolder} 
                                className={tagInput.isValid ? "":"invalid"}
                                onChange={this.handleAddTagOnChange} 
                                onKeyPress={this.handleTagKeyPress} 
                                onPaste={this.validatePastEvent} 
                                onKeyUp={this.handleAddTagOnKeyUp} 
                                />
                            </div>
                        </div>
                        <div className="flex font-normal relative section3 sectionCustom sm:w-6/12 text-right w-2/12">
                            <div className="control settingControl">
                                <div>Settings </div>
                            </div>
                            <div className="controlsContainer">
                                <div className="control tagControl" unselectable="on" onClick={this.handleAssignTagPopupOpen}>
                                    <TextFieldsIcon/>
                                    <div>apply existing tag </div>
                                </div>
                                <div className="control addControl" unselectable="on" onClick={this.handleAddTagInputBoxMovement}>
                                    <AddIcon />
                                    <div>create new tag</div>
                                </div>
                                <div className="control deleteControl" unselectable="on" onClick={this.handleUnAssignTagsInBulk}>
                                    <DeleteIcon />
                                    <div>remove all tags</div>
                                </div>
                                <div 
                                className={clsx("tags-container shadow ",showSelectTagTypePopup ? "selectingType" : "")} 
                                style={showAssignTagPopup ? {display:"block"}:{}}
                                >
                                    <div className="progress" style={TagData.isSaving?{display:"block"}:{}}>
                                        <div className="indeterminate"></div>
                                    </div>
                                    {/* @if */}
                                    {
                                        this.props.type == "2"?
                                        <div className="selectTagType tagManagerSelectionElements">
                                            <div className="navigationButton closeSelectTypePopUp" onClick={this.handleSelectTagTypePopupToggling}>
                                                <ArrowForwardIosIcon style={{width:"1rem"}} />
                                            </div>
                                            <div className="tag row">
                                                <div className="col-lg-12 col-md-12 col-sm-12 col-12 tag-left activeTr">
                                                    <label className="cursorPointer mb-0 displayInlineFlex centerChild">
                                                    <input data-type="1" value="1" checked={this.state.TagData.tagType=="1"? true:false} onChange={this.setTagTypeValue}  type="radio" name="tagTypeRadioButton" className="mr-2" />Product Type</label>
                                                </div>
                                            </div>
                                            <div className="tag row">
                                                <div className="col-lg-12 col-md-12 col-sm-12 col-12 tag-left activeTr">
                                                    <label className="cursorPointer mb-0 radio-inline displayInlineFlex centerChild">
                                                        <input data-type="2" checked={this.state.TagData.tagType=="2"? true:false} onChange={this.setTagTypeValue}  value="2" type="radio" name="tagTypeRadioButton" className="mr-2" />Strategy Type
                                                    </label>
                                                
                                                </div>
                                            </div>
                                            <div className="tag row">
                                                <div className="col-lg-12 col-md-12 col-sm-12 col-12 tag-left activeTr">
                                                    <label className="cursorPointer mb-0 displayInlineFlex centerChild">
                                                        <input data-type="3" checked={this.state.TagData.tagType=="3"? true:false} onChange={this.setTagTypeValue}  value="3" type="radio" name="tagTypeRadioButton" className="mr-2" />Custom Type
                                                    </label>
                                                </div>
                                            </div>
                                        </div>:null
                                    }
                                    
                                    {/* @endif */}
                                    <div className="tags tagManagerSelectionElements">
                                        {
                                            tags.map((tag) =>  
                                            <div className="editBox" id={tag.id} key={tag.id} >
                                                <div className="flex row tag">
                                                    <input 
                                                    tag-id = {tag.id} 
                                                    type="text" 
                                                    style={{display:"none"}} 
                                                    ref={`editInput${tag.id}`}
                                                    onBlur={this.handleEditOnBlureEvent}
                                                    name="editTag" 
                                                    id="editTag" 
                                                    value={this.state.editTagInput.value}
                                                    autoComplete="off" 
                                                    spellCheck="false" 
                                                    placeholder={this.state.editTagInput.placeHolder} 
                                                    className={this.state.editTagInput.isValid ? "":"invalid"}
                                                    onChange={this.handleEditTagOnChange} 
                                                    onKeyPress={this.handleTagKeyPress} 
                                                    onPaste={this.validatePastEvent} 
                                                    onKeyUp={this.handleEditTagOnKeyUp} 
                                                    />
                                                    <div className="tag-left w-9/12">
                                                        <label className="cursorPointer mb-0 displayInlineFlex flex items-center">
                                                            {
                                                                this.props.type == "1" ? 
                                                                <>
                                                                    <input type="checkbox" name="selectedTagRadioButton" onChange={this.handleOnTagSelection} className="mr-1 selectedTagInputs"/>
                                                                    <span>{tag.tag}</span>
                                                                </> :
                                                                <>
                                                                    <input type={TagData.tagType=="3"?"checkbox":"radio"} name="selectedTagRadioButton" onChange={this.handleOnTagSelection} className="mr-1 selectedTagInputs"/>
                                                                    <span>{tag.tag}</span>
                                                                </>
                                                            }
                                                        </label>
                                                    </div>
                                                    <div className="tag-right w-3/12 flex items-center justify-center">
                                                        <span className="editTagContainer" >
                                                            <CreateIcon className="editTagButton"  id={tag.id} tag={tag.tag} onClick={this.handleOnTagEditButtonClick}/>
                                                        </span>
                                                        <span className="deleteTagContainer" >
                                                            <DeleteIcon className="deleteTagButton" id={tag.id}  onClick={this.handleOnTagDeleteButtonClick}/>
                                                        </span>
                                                        <span className="badge flex items-center justify-around">
                                                            {this.props.type=="1"?tag.products_count :tag.compaigns_count}</span>
                                                    </div>
                                                </div>
                                            </div>)
                                        }
                                        <div className="coverContainer" style={tagPopup.isEditing ? {display:"block"}:{display:"none"}}></div>
                                    </div>
                                    <div className="assignTagButton "  onClick={this.handleAssignTagButtonClick} style={tagPopup.showAssignTagButton ? {visibility:"visible"}:{visibility:"hidden"}}>
                                        <span>assign
                                        </span>
                                    </div>
                                    {/* @if (Request::segment(2) == 'campaign')    */}
                                        {this.props.type == "2" ? <div className="navigationButton openSelectTypePopUp" onClick={this.handleSelectTagTypePopupToggling}>
                                            <ArrowBackIosIcon style={{width:"1rem"}} />
                                        </div>:null}
                                    {/* @endif */}
                                </div>
                            </div>
                        </div>
                        <div className="flex items-center justify-center section4 sectionCustom w-1/12">
                            <div className="closeButton" onClick={this.handleTagMangerPopupClose}>
                                <CloseIcon />
                            </div>
                        </div>
                    </div>
                </div>
            </>
        )
    }
}


