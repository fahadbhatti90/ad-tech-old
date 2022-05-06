<div class="tagGroupManager shadow materializeCss">
    <div class="row">
        <div class="progress">
            <div class="indeterminate"></div>
        </div>
        <div class="col-lg-1 col-md-1 col-sm-1 col-1 corner sectionCustom section1">
            <div class="counter">1</div>
        </div>
        <div class="col-lg-5 col-md-5 col-sm-8 col-8 sectionCustom section2">
            <div class="statsSection">
                <span class="itemLabel">Items</span> Selected
                <div class="itemCounts">
                    
                </div>
            </div>
            <div class="inputSection">
                <input type="text" name="tag" id="tag" placeholder="Write Tag Then Press Enter" autocomplete="off" spellcheck="false">
            </div>
        </div>
        <div class="col-lg-5 col-md-5 col-sm-2 col-2 sectionCustom section3">
            <div class="control settingControl">
                <i class="material-icons">settings_applications</i>
                <div>Settings </div>
            </div>
            <div class="controlsContainer">
                <div class="control tagControl">
                    <i class="material-icons">text_fields</i>
                    <div>apply existing tag </div>
                </div>
                <div class="control addControl">
                    <i class="material-icons">add</i>
                    <div>create new tag</div>
                </div>
                {{-- <div class="control groupControl">
                    <i class="material-icons">group_add</i>
                    <div>Group</div>
                </div> --}}
                <div class="control deleteControl">
                    <i class="far fa-trash-alt"></i>
                    <div>remove all tags</div>
                </div>
                <div class="tags-container shadow selectingType">
                    <div class="progress">
                            <div class="indeterminate"></div>
                    </div>
                    {{-- <div class="title">
                        <p>Select Tag   </p>
                    </div> --}}
                    @if (Request::segment(2) == 'campaign')   
                    <div class="selectTagType tagManagerSelectionElements">
                        <div class="navigationButton closeSelectTypePopUp"><i class="material-icons">keyboard_arrow_right</i></div>
                        <div class="tag row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-12 tag-left activeTr">
                                <label class="cursorPointer mb-0 displayInlineFlex centerChild">
                                <input data-type="1"  type="radio" name="tagTypeRadioButton" class="mr-2">Product Type</label>
                            </div>
                        </div>
                        <div class="tag row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-12 tag-left activeTr">
                                <label class="cursorPointer mb-0 radio-inline displayInlineFlex centerChild">
                                    <input data-type="2" type="radio" name="tagTypeRadioButton" class="mr-2">Strategy Type
                                </label>
                               
                            </div>
                        </div>
                        <div class="tag row">
                                {{-- <div class="selectContainer">
                                    <div class="checkboxMiniContainer">
                                        <span type="3"><i class="fas fa-check"></i></span>
                                    </div>
                                </div> --}}
                                
                            <div class="col-lg-12 col-md-12 col-sm-12 col-12 tag-left activeTr">
                                <label class="cursorPointer mb-0 displayInlineFlex centerChild">
                                    <input data-type="3" type="radio" name="tagTypeRadioButton" class="mr-2">Custom Type
                                </label>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="tags tagManagerSelectionElements">
                    </div>
                    <div class="assignTagButton ">
                        <span>assign
                        </span>
                    </div>
                    @if (Request::segment(2) == 'campaign')   
                        <div class="navigationButton openSelectTypePopUp"><i class="material-icons">keyboard_arrow_left</i></div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-1 col-md-1 col-sm-1 col-1 corner sectionCustom section4">
            <div class="closeButton">
                <i class="material-icons">close</i>
            </div>
        </div>
    </div>
</div>