@extends('layout.appclient')
@extends('inc.side_menu')
@extends('inc.nav_bar')
@extends('modal.logout')
@section('title', isset($pageTitle)?$pageTitle:'Dashboard')

@push('css')
    <!-- Load c3.css -->

    <link href="https://cdnjs.cloudflare.com/ajax/libs/c3/0.7.15/c3.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/tooltipster/dist/css/tooltipster.bundle.min.css')}}" />
    <link href="{{asset('public/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('public/vendor/datatables/responsive.dataTables.min.css')}}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{ asset('public/vendor/daterangepicker/daterangepicker.css') }}">  
    <!-- Load Material Icons  -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
    <!-- Load d3.js and c3.js -->
    <link rel="stylesheet" href="{{ asset('public/css/client/product.css?'.time())  }}"/>

    <link rel="stylesheet" href="{{ asset('public/css/select2.min.css?'.time())  }}"/>
    <link rel="stylesheet" href="{{ asset('public/css/client/productTable.css?'.time())  }}">
    <link rel="stylesheet" href="{{ asset('public/css/client/tagManager.css?'.time())  }}">
    <link rel="stylesheet" href="{{ asset('public/css/client/productTableForDemo.css?'.time())  }}"/>
    @endpush


@push('daterangepickerjs')
<script src="{{ asset('public/vendor/daterangepicker/moment.min.js') }}"></script>
@endpush

@section('content')
        <!-- /.container-fluid -->
        <!-- Begin Page Content -->
        <div class="container-fluid productsVisuals">

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
                <span class="itemLabel">Item</span> Selected
                <div class="itemCounts">
                    
                <span class="itemAdded"></span></div>
            </div>
            <div class="inputSection">
                <input type="text" name="tag" id="tag" placeholder="Write Tag Then Press Enter" autocomplete="off" spellcheck="false">
            </div>
        </div>
        <div class="col-lg-5 col-md-5 col-sm-2 col-2 sectionCustom section3">
            <div class="control settingControl" unselectable="on" style="user-select: none;">
                <i class="material-icons">settings_applications</i>
                <div>Settings </div>
            </div>
            <div class="controlsContainer">
                <div class="control tagControl" unselectable="on" style="user-select: none;">
                    <i class="material-icons">text_fields</i>
                    <div>apply existing tag </div>
                </div>
                <div class="control addControl" unselectable="on" style="user-select: none;">
                    <i class="material-icons">add</i>
                    <div>create new tag</div>
                </div>
                
                <div class="control deleteControl" unselectable="on" style="user-select: none;">
                    <i class="far fa-trash-alt"></i>
                    <div>remove all tags</div>
                </div>
                <div class="tags-container shadow selectingType" style="top: -245px;">
                    <div class="progress">
                            <div class="indeterminate"></div>
                    </div>
                    
                       
                    <div class="selectTagType tagManagerSelectionElements">
                        <div class="navigationButton closeSelectTypePopUp" unselectable="on" style="user-select: none;"><i class="material-icons">keyboard_arrow_right</i></div>
                        <div class="tag row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-12 tag-left activeTr">
                                <label class="cursorPointer mb-0 displayInlineFlex centerChild">
                                <input data-type="1" type="radio" name="tagTypeRadioButton" class="mr-2">Product Type</label>
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
                                
                                
                            <div class="col-lg-12 col-md-12 col-sm-12 col-12 tag-left activeTr">
                                <label class="cursorPointer mb-0 displayInlineFlex centerChild">
                                    <input data-type="3" type="radio" name="tagTypeRadioButton" class="mr-2">Custom Type
                                </label>
                            </div>
                        </div>
                    </div>
                                        <div class="tags tagManagerSelectionElements">
                    </div>
                    <div class="assignTagButton ">
                        <span unselectable="on" style="user-select: none;">assign
                        </span>
                    </div>
                       
                        <div class="navigationButton openSelectTypePopUp" unselectable="on" style="user-select: none;"><i class="material-icons">keyboard_arrow_left</i></div>
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

            <!-- Filters  -->
            <div class="card shadow cardLayout filterLayout">
            <div class="row justify-content-end" style="margin-right: 10px;">
                    <button type="button" id="upFilters" class="filterBtn btn d-flex justify-content-center align-content-between">
                        <img src="{{asset('public/images/filter-icon.svg')}}" alt="icon"> <span>Filter</span><i class="material-icons">arrow_drop_down</i>
                    </button>
                </div>
                <div id="CatBrandSection" class="row showHide">
                   
                    <div class="col-5 filterSelects selectPosition selectCategoryParent">
                        <label class="labelFilter" for="select-category">Select Category</label>
                        <select id="select-category" multiple="multiple">
                            <optgroup label="Office Products">
                                <option value="1">Option 1</option>
                                <option value="2">Option 2</option>
                                <option value="3">Option 3</option>
                            </optgroup>
                            <optgroup label="Biss">
                                <option value="1">Option 1</option>
                                <option value="2">Option 2</option>
                                <option value="3">Option 3</option>
                            </optgroup>
                            <optgroup label="Health & Personal Care">
                                <option value="1">Option 1</option>
                                <option value="2">Option 2</option>
                                <option value="3">Option 3</option>
                            </optgroup>
                            <optgroup label="Home">
                                <option value="1">Option 1</option>
                                <option value="2">Option 2</option>
                                <option value="3">Option 3</option>
                            </optgroup>
                            <optgroup label="Furniture">
                                <option value="1">Option 1</option>
                                <option value="2">Option 2</option>
                                <option value="3">Option 3</option>
                            </optgroup>
                            <optgroup label="Tools">
                                <option value="1">Option 1</option>
                                <option value="2">Option 2</option>
                                <option value="3">Option 3</option>
                            </optgroup>
                            <optgroup label="Pantry">
                                <option value="1">Option 1</option>
                                <option value="2">Option 2</option>
                                <option value="3">Option 3</option>
                            </optgroup>
                            <optgroup label="Biss">
                                <option value="1">Option 1</option>
                                <option value="2">Option 2</option>
                                <option value="3">Option 3</option>
                            </optgroup>
                            <optgroup label="Grocery">
                                <option value="1">Option 1</option>
                                <option value="2">Option 2</option>
                                <option value="3">Option 3</option>
                            </optgroup>
                            <optgroup label="Personal_Care_Appliances">
                                <option value="1">Option 1</option>
                                <option value="2">Option 2</option>
                                <option value="3">Option 3</option>
                            </optgroup>
                            <optgroup label="UNKNOWN">
                                <option value="1">Option 1</option>
                                <option value="2">Option 2</option>
                                <option value="3">Option 3</option>
                            </optgroup>
                            <optgroup label="Home">
                                <option value="1">Option 1</option>
                                <option value="2">Option 2</option>
                                <option value="3">Option 3</option>
                            </optgroup>
                            
                        </select>
                    </div>
                
                    <div class="col-5 filterSelects selectPosition selectChildBrandParent">
                        <label class="labelFilter" for="select-category">Select Child Brand</label>
                        <select id="select-brand" multiple="multiple">
                            <optgroup label="Acme" >
                                <span class="glyphicon glyphicon-chevron-down"></span>
                                <option value="1">Option 1</option>
                                <option value="2">Option 2</option>
                                <option value="3">Option 3</option>
                            </optgroup>
                            <optgroup label="PhysiciansCare" >
                                <span class="glyphicon glyphicon-chevron-down"></span>
                                <option value="1">Option 1</option>
                                <option value="2">Option 2</option>
                                <option value="3">Option 3</option>
                            </optgroup>
                            <optgroup label="Clauss" >
                                <span class="glyphicon glyphicon-chevron-down"></span>
                                <option value="1">Option 1</option>
                                <option value="2">Option 2</option>
                                <option value="3">Option 3</option>
                            </optgroup>
                            <optgroup label="Spill Magic" >
                                <span class="glyphicon glyphicon-chevron-down"></span>
                                <option value="1">Option 1</option>
                                <option value="2">Option 2</option>
                                <option value="3">Option 3</option>
                            </optgroup>
                            <optgroup label="Westcott" >
                                <span class="glyphicon glyphicon-chevron-down"></span>
                                <option value="1">Option 1</option>
                                <option value="2">Option 2</option>
                                <option value="3">Option 3</option>
                            </optgroup>
                            <optgroup label="First Aid Only" >
                                <span class="glyphicon glyphicon-chevron-down"></span>
                                <option value="1">Option 1</option>
                                <option value="2">Option 2</option>
                                <option value="3">Option 3</option>
                            </optgroup>
                            
                        </select>
                    </div>

                    <!-- <div class="col-2">
                
                    </div> -->
                </div>
            </div>
            <!-- Comps Visuals  -->
            <div class="card shadow cardLayout">
                 <div class="row">
                    <div class="col-6 aggregatedGraph p-0">
                        <nav class="nav nav-tabs responsive nav-justified">
                            <a class="nav-item nav-link active" data-toggle="tab" href="#home">Shipped Units</a>
                            <a class="nav-item nav-link" data-toggle="tab" href="#menu1">PO Units</a>
                            <a class="nav-item nav-link" data-toggle="tab" href="#menu2">BB Win%</a>
                            <a class="nav-item nav-link" data-toggle="tab" href="#menu3">Ad Spend</a>
                        </nav>

                        <div class="tab-content">
                            <div id="home" class="tab-pane fade show active">
                                <div class="mt-10 mr-4">
                                    <div class="mtop20" id="shippedChart"></div>
                                </div>
                            </div>
                            <div id="menu1" class="tab-pane fade show">
                                <div class="mt-10 mr-4">
                                    <div class="mtop20" id="poUnitsChart"></div>
                                </div>
                            </div>
                            <div id="menu2" class="tab-pane fade">
                                <div class="mt-10 mr-4">
                                    <div class="mtop20" id="bbwinChart"></div>
                                </div>
                            </div>
                            <div id="menu3" class="tab-pane fade">
                                <h5 class="comingSoon">
                                    coming soon!
                                </h5>
                            </div>
                        </div>

    
                    </div>
                    <div class="col-5 col-offset-2 compTabGraph p-0">
                        <nav class="nav nav-tabs responsive nav-justified">
                            <a class="nav-item nav-link active" data-toggle="tab" href="#comps" style="font-size: 14px;">Historical Benchmarks</a>
                            <a class="nav-item nav-link" data-toggle="tab" href="#retail">Retail Ready</a>
                            <a class="nav-item nav-link" data-toggle="tab" href="#eventsum">Events Summary</a>
                        </nav>

                        <div class="tab-content">
                            <div id="comps" class="tab-pane fade show active">
                                <div class="h-100 mt-10">
                                    <!-- Com Card with 6 values  -->
                                    <div id="compCard" class="boxHeight col h-100 p-0">
                                        <div class="align-items-center h-100 over-div row">
                                        <div id="wowFlex1" class="col flexProp h-100">
                                                <fieldset class="border">
                                                    <legend class ='text-center labelData'>Last Day</legend>
                                                        <label id="comm-value" class="innerPara currency">$74,542</label>
                                                        <br>
                                                        <label id="comm-value" class="innerPara currency">203%</label>
                                                    
                                                </fieldset>    
                

                                                <fieldset class="border">
                                                    <legend class ='text-center labelData'>Last Week</legend>
                                                        <label id="wow-cost-currency" class="innerPara currency">$419,861</label>
                                                        <br>
                                                        <label id="wow-cost-currency" class="innerPara currency">55%</label>
                                                    
                                                </fieldset>

                                               
                                                <fieldset class="border">
                                                    <legend class ='text-center labelData'>Last Month</legend>
                                                        <label id="wow-rev-currency" class="innerPara currency">$1,838,205</label>
                                                        <br>
                                                        <label id="wow-rev-currency" class="innerPara currency redColor">43%</label>
                                                    
                                                </fieldset>
                                            </div>
                                            <div id="wowFlex2" class="col flexProp h-100">
                                                
                                                <fieldset class="border">
                                                    <legend class ='text-center labelData'>Last Quarter</legend>
                                                        <label id="wow-acos-currency" class="innerPara currency">$5,768,226</label>
                                                        <br>
                                                        <label id="wow-acos-currency" class="innerPara currency redColor">49%</label>
                                                    
                                                </fieldset>

                                               
                                                <fieldset class="border">
                                                    <legend class ='text-center labelData'>YTD</legend>
                                                        <label id="wow-cpc-currency" class="innerPara currency">$9,563,378</label>
                                                        <br>
                                                        <label id="wow-cpc-currency" class="innerPara currency">53%</label>
                                                    
                                                </fieldset>

                                                <fieldset class="border">
                                                    <legend class ='text-center labelData'>QTD</legend>
                                                        <label id="wow-roas-currency" class="innerPara currency">$3,795,151</label>
                                                        <br>
                                                        <label id="wow-roas-currency" class="innerPara currency">61.3%</label>
                                                    
                                                </fieldset>
                                               
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="retail" class="fade h-100 show tab-pane">
                                <div id="retailCard" class="boxHeight col h-100 p-0">
                                    <div class="align-items-center h-100 over-div row">
                                        
                                    <div id="wowFlex1" class="col flexProp">
                                        
                                        <fieldset class="border" style="margin-bottom: 9px;">
                                            <legend class ='text-center labelData'>Rating &lt; 3.5</legend>
                                                <label id="comm-value" class="innerPara currency">101</label>
                                                <br>
                                                <label id="comm-value" class="innerPara currency">11.22%</label>
                                            
                                        </fieldset>


                                        <fieldset class="border" style="margin-bottom: 0px;">
                                            <legend class ='text-center labelData'>Count &gt; 20</legend>
                                                <label id="wow-cost-currency" class="innerPara currency">386</label>
                                                <br>
                                                <label id="wow-cost-currency" class="innerPara currency">42.88%</label>
                                            
                                        </fieldset>


                                        <fieldset class="border" style="margin-bottom: 6%;">
                                            <legend id="wbr" class ='text-center labelData'>Img/bullets/video/A+</legend>
                                                <label id="wow-cost-currency" class="innerPara currency">882</label>
                                                <br>
                                                <label id="wow-cost-currency" class="innerPara currency">96%</label>
                                            
                                        </fieldset>
                                    
                                    </div>
                                    
                                    <div id="wowFlex2" class="col flexProp">
                                       
                                        <fieldset class="border" style="margin-bottom: 10px;">
                                            <legend class ='text-center labelData'>Prime</legend>
                                                <label id="wow-cost-currency" class="innerPara currency">0</label>
                                                <br>
                                                <label id="wow-cost-currency" class="innerPara currency">99.66%</label>
                                            
                                        </fieldset>
                                    
                                        <fieldset class="border" style="margin-bottom: 10px;">
                                            <legend class ='text-center labelData'>OOS</legend>
                                                <label id="wow-cost-currency" class="innerPara currency">0.061</label>
                                                <br>
                                                <label id="wow-cost-currency" class="innerPara currency">6.1%</label>
                                            
                                        </fieldset>
                                    
                                        <fieldset class="border" style="margin-bottom: 10px;">
                                            <legend class ='text-center labelData'>Buy Box</legend>
                                                <label id="wow-cost-currency" class="innerPara currency">0.012</label>
                                                <br>
                                                <label id="wow-cost-currency" class="innerPara currency">1.2%</label>
                                            
                                        </fieldset>
                                        
                                    </div>                                             
                                    </div>
                                </div>
                            </div>
                            <div id="eventsum" class="tab-pane fade">
                               <h5  class="comingSoon">
                                    coming soon!
                                </h5>
                            </div>
                        
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Table Card  -->
            <div class="row productTable ">
                <div class="col-xl-12 col-md-12 mb-4">
                    <div class="card mb-4 shadow productTableCard">
                        <div class="card-header py-3" style="display: flex;">
                            <h6 class="m-0 font-weight-bold" style="flex-basis:70%">
                                Products
                            </h6>
                            <div class="col col-6 filterControls">
                                <div id="dataTable_filter1" class="dataTables_filter">
                                    <label class="inner-addon right-addon" style="
                                        margin: 0;
                                    ">
                                        <input type="search" class="" placeholder="Search" aria-controls="dataTable">
                                        <span class="material-icons prefix" style="margin:8px 0px 2px -29px; color:#ccc7c7">search</span>
                                    </label>
                                </div>
                                <button type="button" class="filterBtn btn d-flex justify-content-center align-content-between">
                                    <img src="{{asset('public/images/filter-icon.svg')}}" alt="icon"> <span>Filter</span><i class="material-icons">arrow_drop_down</i>
                                </button>
                            </div>   
                        </div>
                        <div id="filterSection" class="showHide">
                            <div class="row msRow justify-content-center align-items-center">       
                                <div class="col-3 offset-1 mSlabel pl-0 selectProductSegmentParent">
                                    <div class="mb-1">
                                        <span class="material-icons">folder</span> 
                                        <span>Product Segment</span>
                                    </div>
                                    <select id="select-segment" multiple="multiple">
                                        <optgroup label="Sales">
                                            <option value="1">LW sales 10% or more LOWER than 3 wk sales avg</option>
                                            <option value="2">Bestseller top 10 now</option>
                                            <option value="3">Bestseller rank drop</option>
                                            <option value="4">Sales takeoff</option>
                                            <option value="5">YTD underperform to LY</option>
                                        </optgroup>
                                        <optgroup label="Product page">
                                            <option value="6">AMZ choice</option>
                                            <option value="7">Missing 5 img / bp / A+</option>
                                            <option value="8">Product title change</option>
                                            
                                        </optgroup>
                                        <optgroup label="Reviews">
                                            <option value="9">Rating < 3.5</option>
                                            <option value="10">Count < 20</option>
                                            <option value="11">Review conversion drop</option>
                                          
                                        </optgroup>
                                        <optgroup label="Buybox">
                                            <option value="12">Oos now</option>
                                            <option value="13">Price change</option>
                                            <option value="14">Seller change</option>
                                        </optgroup>
                                        <optgroup label="Product page">
                                            <option value="15">Active offer</option>
                                        </optgroup>
                                    </select>
                                </div>
    
                                <div class="col-3 mSlabel pl-0 selectProductTagParent">
                                    <div class="mb-1">
                                        <span style="" class="material-icons">local_offer</span>
                                        <span>Product Tags</span> 
                                    </div>
                                    <select id="select-tag" multiple="multiple">
                                        <option value="1">2020 q4 promo </option>
                                        <option value="2">client priority</option>
                                        <option value="3">amg spend</option>
                                        <option value="4">long term oos</option>
                                        </select>
                                </div>
    
                                <div class="col-3 mSlabel pl-0 selectColumnParent">
                                    <div class="mb-1">
                                        <span class="material-icons">filter_alt</span> 
                                        <span>Add/Remove Column</span>
                                    </div>
                                    <select id="select-addcol" multiple="multiple">
                                        <optgroup label="Shipped units">
                                            <option value="all">shipped units yesterday</option>
                                            <option value="all">shipped units last month</option>
                                            <option value="all">shipped units last year</option>
                                            <option value="all">shipped units ytd</option>
                                            <option value="all">shipped units qtd</option>
                                            <option value="all">shipped units last month (MoM)</option>
                                            <option value="5">shipped units last week (WoW)</option>
                                            <option value="all">shipped units ytd vs LY</option>
                                            <option value="all">shipped units qtd vs LY</option>
                                            <option value="4">shipped units last week</option> 
                                            <option value="all">shipped units yesterday paid</option>
                                            <option value="all">shipped units last week paid</option>
                                            <option value="all">shipped units last month paid</option>
                                            <option value="all">shipped units qtd paid</option>
                                            <option value="all">shipped units ytd paid</option>
                                        </optgroup>
                                        <optgroup label="PO units">
                                            <option value="all">po units yesterday</option>
                                            <option value="all">po units last month</option>
                                            <option value="all">po units last year</option>
                                            <option value="all">po units ytd</option>
                                            <option value="all">po units qtd</option>
                                            <option value="all">po units last month (MoM)</option>
                                            <option value="all">po units last week (WoW)</option>
                                            <option value="all">po units ytd vs LY</option>
                                            <option value="all">po units qtd vs LY</option>
                                            <option value="all">po units last week</option>
                                        </optgroup>
                                        <optgroup label="Reviews">
                                            <option value="8">review rating</option>
                                            <option value="9">review count</option>
                                        </optgroup>
                                        <optgroup label="Rankings">
                                            <option value="6">bestseller rank</option>
                                            <option value="all">bestseller rank 2 wk avg</option>
                                            <option value="all">organic search rank</option>
                                            <option value="all">organic search rank 2 wk avg</option>
                                        </optgroup>
                                        <optgroup label="Inventory">
                                            <option value="7">sellable qty</option>
                                        </optgroup>
                                        {{-- <optgroup label="General">
                                            <option value="1" selected>ASIN</option>
                                            <option value="2" selected>Product Title</option>
                                            <option value="3" selected>Fulfillment</option>
                                        </optgroup> --}}
    
                                    </select>
                                </div>
    
                                <div class="col-2 mSlabel p-0">
                                    <div class="filterBtns">
                                        <button id="resetBtn" type="button" class="btn d-inline-flex mb-1 p-0">
                                            <i class="iconFlip material-icons">refresh</i> <span style="color:black">Reset all</span>
                                        </button>
    
                                        <button id="applyBtn" type="button" class="applyBtn btn  btn-primary d-flex justify-content-center align-content-between">
                                            Apply
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body schedulingCardBody position-relative">
                                <table id="dataTable" class="table table-bordered  cronListTable" style="width:100%" un-assign-single-tag="{{ route("productTable.tag.single.unassign") }}">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <div class="selectContainer"><div class="checkboxMiniContainer"><span><i class="fas fa-check"></i></span></div></div>
                                                    Sr. #
                                                </th>
                                                <th>ASIN</th>
                                                <th>Product Title</th>
                                                <th>Fulfillment</th>
                                                <th>Shipped Units Last Week</th>
                                                <th>Shipped Units Last Week</th>
                                                <th>Shipped Units Last Week</th>
                                                <th>Shipped Units</th>
                                                <th>Review Score</th>
                                                <th>Review Count</th>
                                                {{-- <th class="text-center">Action</th> --}}
                                            </tr>
                                        </thead>
                                        <tbody class="allASINS">
                                        
                                        </tbody>
                                        </tbody>
                                       
                                </table>
                                <div class=" d-none preloaderProduct">
                                    <div>
                                        <p>
                                            loading...
                                        </p>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- <div class="card right_card shadow h-100">
                <div class="card-body">
                    <h5 class="card-title">Product Table</h5>
                </div>
            </div> -->

            <!-- The Modal -->
            <div class="modal productTableVisuals" id="myModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                <!-- Modal Header -->
                {{-- <div class="modal-header">
                    <h4 class="modal-title">Product Event Tracking</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div> --}}

                <!-- Modal body -->
                <div class="modal-body mt-1">
                    <div class="mb-3 row selectedFilterDetails">
                        <div class="offset-1 col-7">
                            <p class="">First Aid Only 298 Piece All-Purpose First Aid Kit (FAO-442)</p>
                        </div>
                        <div class="col-2 d-flex">
                            <select id="saledd" class="monthSelector mr-2">
                                <option value="6|06|June|2020" selected>June 2020</option>
                                <option value="5|05|May|2020" >May 2020</option>
                                <option value="4|04|April|2020" >April 2020</option>
                                <option value="3|03|March|2020" >March 2020</option>
                                <option value="2|02|February|2020" >February 2020</option>
                                <option value="1|01|January|2020" >January 2020</option>
                                <option value="0|12|December|2019" >December 2019</option>
                            </select>
                            <select id="saledd">
                                <option value="Sales" selected="">Sales</option>
                                <option value="Price">Price</option>
                                <option value="Sales Rank">Sales Rank</option>
                            </select>
                        </div>
                    </div>
                    <div class="vissualContainer row">
                        <div class="mainVisuals col-10 p-0">
                            <div id="salesChart"></div>
                            <div id="eventsChart"></div>
                        </div>
                        <div class="eventsButtons col-2 p-0">
                            <label class="checkboxContainer reviewEventButton" data-index = "0">Reviews
                                <input type="checkbox">
                                <span class="checkmark reviewEventButton-checkmark">
                                    <i class="fa fa-check" aria-hidden="true"></i>
                                </span>
                            </label>
                            <label class="checkboxContainer contentEventButton" data-index = "1">Content
                                <input type="checkbox">
                                <span class="checkmark contentEventButton-checkmark">
                                    <i class="fa fa-check" aria-hidden="true"></i>
                                </span>
                            </label>
                            
                            <label class="checkboxContainer pageNotFoundEventButton" data-index = "2">Page Not Found
                                <input type="checkbox">
                                <span class="checkmark pageNotFoundEventButton-checkmark">
                                    <i class="fa fa-check" aria-hidden="true"></i>
                                </span>
                            </label>
                            <label class="checkboxContainer andonEventButton" data-index = "3">Andon Cord
                                <input type="checkbox">
                                <span class="checkmark andonEventButton-checkmark">
                                    <i class="fa fa-check" aria-hidden="true"></i>
                                </span>
                            </label>
                            <label class="checkboxContainer crapEventButton" data-index = "4">Crap
                                <input type="checkbox">
                                <span class="checkmark crapEventButton-checkmark">
                                    <i class="fa fa-check" aria-hidden="true"></i>
                                </span>
                            </label>
                            
                            <label class="checkboxContainer outOfStockEventButton" data-index = "5">Out of Stock
                                <input type="checkbox">
                                <span class="checkmark outOfStockEventButton-checkmark">
                                    <i class="fa fa-check" aria-hidden="true"></i>
                                </span>
                            </label>
                            <label class="checkboxContainer sellerChangeEventButton" data-index = "6">Seller Change
                                <input type="checkbox">
                                <span class="checkmark sellerChangeEventButton-checkmark">
                                    <i class="fa fa-check" aria-hidden="true"></i>
                                </span>
                            </label>
                            <label class="checkboxContainer priceChangeEventButton" data-index = "7">Price Change
                                <input type="checkbox">
                                <span class="checkmark priceChangeEventButton-checkmark">
                                    <i class="fa fa-check" aria-hidden="true"></i>
                                </span>
                            </label>
                            <label class="checkboxContainer advertisingEventButton" data-index = "8">Advertising
                                <input type="checkbox">
                                <span class="checkmark advertisingEventButton-checkmark">
                                    <i class="fa fa-check" aria-hidden="true"></i>
                                </span>
                            </label>
                        </div>
                    </div>
                    <div id="DatePart">
                        May, 2020
                    </div>
                </div>
                </div>
            </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    @include('client.partials.tagPopup')
@endsection

@push('js')
    <!-- Load d3.js and c3.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/c3/0.7.15/c3.min.js" charset="utf-8"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/5.15.0/d3.min.js"></script>
    <!-- Ajax Query  -->
    <script type="text/javascript" src="{{ asset('public/tooltipster/dist/js/tooltipster.bundle.min.js')}}"></script>
    <script src="{{asset('public/vendor/datatables/jquery.dataTables.min.js')}}"></script> 
    <script src="{{asset('public/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{asset('public/vendor/datatables/dataTables.responsive.min.js')}}"></script>
    <script src="{{ asset('public/js/charts/product.js?'.time()) }}"></script>
    <script src="{{ asset('public/js/charts/productEventFilter.js?'.time()) }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>

    <script src="{{ asset('public/js/select2.min.js?'.time()) }}"></script>
    <script src="{{ asset('public/js/multiAuth/client/productTableForDemo.js?'.time()) }}"></script>
    <script src="{{ asset('public/js/multiAuth/client/productTableTaggingSingleTagUnAssign.js?'.time()) }}"></script>
    
@endpush