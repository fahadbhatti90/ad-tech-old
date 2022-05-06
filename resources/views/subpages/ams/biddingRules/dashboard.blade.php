@extends('layout.app')
@extends('inc.side_menu')
@extends('inc.nav_bar')
@extends('modal.logout')
@section('title', $pageTitle)
@section('content')
    @push('select2css')
        <link href="{{asset('public/css/select2.min.css')}}" rel="stylesheet">
        <link href="{{asset('public/css/multiple-emails.css')}}" rel="stylesheet">
        <link href="{{asset('public/ams/bidding-rule/css/biddingrule.css?'.time())}}" rel="stylesheet">
        <link href="{{asset('public/css/bootstrap-datetimepicker.min.css')}}" rel="stylesheet">
        {{--<link rel="stylesheet" href="{{asset('public/tooltipster/dist/css/tooltipster.bundle.min.css')}}"/>--}}
        <link rel="stylesheet" type="text/css"
              href="{{ asset('public/tooltipster/dist/css/tooltipster.bundle.min.css')}}"/>
        <link href="{{asset('public/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet" type="text/css">
        <link href="{{asset('public/vendor/datatables/responsive.dataTables.min.css')}}" rel="stylesheet" type="text/css">
    @endpush
    <script type="text/javascript" src="{{asset('public/js/multiple-emails.js')}}"></script>
    <script>
        //Plug-in function for the bootstrap version of the multiple email
        $(function () {
            // multiple email add in input fields
            loadDatatables("biddingRuleDataTable", "{{ route('get-bidding-rule-list') }}");
            //To render the input device to multiple email input using BootStrap icon
            $('#cc_emailBS').multiple_emails({position: "bottom"});
            ccEmailTooltip();
        });
    </script>
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Begin Breadcrumb -->
    {{--    {{ Breadcrumbs::render('ams_bidding_rule') }}--}}
    <!-- End Breadcrumb -->
        {{-- Bidding Rules Definition --}}
        <div class="row">
            <div class="col-xl-12 col-lg-12">
                <!-- Collapsable Card Example -->
                <div class="card shadow mb-4">
                    <!-- Card Header - Accordion -->
                    <a href="#biddingRuleCard" class="d-block card-header py-3" data-toggle="collapse"
                       role="button" aria-expanded="true" aria-controls="collapseBiddingRuleCard">
                        <h6 class="m-0 font-weight-bold text-primary">{{isset($pageHeading)?$pageHeading:''}}</h6>
                    </a>
                    <!-- Card Content - Collapse -->
                    <div class="collapse show" id="biddingRuleCard">
                        <!-- Card Body -->
                        <div class="card-body">
                            <div class="col-md-12">
                                <form id="biddingRuleForm" data-action="{{route('store-rule')}}" data-save-as-rule="{{route('only-store-rule')}}" action="javascript:void(0)">
                                    @csrf
                                    <input type="hidden" class="form-control" name="formType" value="add">
                                    <input type="hidden" id="campaignListUrl" value="{{route('campaignPortfolioList')}}">
                                    <input type="hidden" id="presetRule" value="{{route('presetRule')}}">
                                    {{-- Rule name and Sponsored type --}}
                                    <div class="form-row mb-n4">
                                        <div class="form-group col-sm-4">
                                            <label class="col-form-label">Rule name<sup class="required">*</sup></label>
                                            <input type="text" class="form-control" name="ruleName"
                                                   placeholder="Rule name">
                                        </div>
                                        <div class="form-group col-sm-4">
                                            <label class="col-form-label">Child brand<sup class="required">*</sup></label>
                                            <div class="form-group">
                                                <select class="form-control profileName"
                                                        id="profile_fk_id"
                                                        name="profileFkId">
                                                    <option value="" selected>Select child brand</option>
                                                    @if(!empty($brands))
                                                        @foreach($brands as $brand)
                                                            @isset($brand->fkId)
                                                                @if(!empty(trim($brand->ams['name'])) || !empty($brand->brand_alias[0]->overrideLabel))
                                                                    @php
                                                                        $brandOptionValue = '';
                                                                           $brandOptionValue =   $brand->brand_alias != null ?
                                                                              ($brand->brand_alias != null &&
                                                                              count($brand->brand_alias) > 0 ?
                                                                              ($brand->brand_alias[0]->overrideLabel != null ?
                                                                               ($brand->brand_alias[0]->overrideLabel > 40 ?  $brand->brand_alias[0]->overrideLabel: str_limit($brand->brand_alias[0]->overrideLabel,40)):
                                                                              ($brand->ams != null ? ($brand->ams['name'] > 40 ?  $brand->ams['name']:  str_limit($brand->ams['name'],40)) : '')) :
                                                                              ($brand->ams != null ? ($brand->ams['name'] > 40 ?  $brand->ams['name']:  str_limit($brand->ams['name'],40)) : '')):
                                                                              ($brand->ams != null ? ($brand->ams['name'] > 40 ?  $brand->ams['name']:  str_limit($brand->ams['name'],40)): '');
                                                                           $brandOptionTitle = '';
                                                                           $brandOptionTitle =   $brand->brand_alias != null ?
                                                                              ($brand->brand_alias != null &&
                                                                              count($brand->brand_alias) > 0 ?
                                                                              ($brand->brand_alias[0]->overrideLabel != null ?
                                                                               ( $brand->brand_alias[0]->overrideLabel):
                                                                              ($brand->ams != null ? (  $brand->ams['name']) : '')) :
                                                                              ($brand->ams != null ? (  $brand->ams['name']) : '')):
                                                                              ($brand->ams != null ? (  $brand->ams['name']): '');
                                                                    @endphp
                                                                    <option title="{{$brandOptionTitle}}" value="{{$brand->fkId}}">

                                                                        {{$brandOptionValue}}
                                                                    </option>
                                                                @endif
                                                            @endisset
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-4">
                                            <label class="col-form-label">Ad type<sup class="required">*</sup></label>
                                            <div class="form-group">
                                                <select class="form-control sponsored_type"
                                                        id="sponsored_type"
                                                        name="sponsoredType">
                                                    <option value="" selected>Select Ad type</option>
                                                    <option value="sponsoredBrand">Brand</option>
                                                    <option value="sponsoredProducts">Product</option>
                                                    <option value="sponsoredDisplay">Display</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Portfolio and campaign List --}}
                                    <div class="form-row mb-n4">
                                        <div class="form-group col-sm-6">
                                            <label class="col-form-label">Portfolio/Campaign<sup class="required">*</sup></label>
                                            <div class="form-group">
                                                <select class="form-control"
                                                        id="type"
                                                        name="type">
                                                    <option value="" selected>Select type</option>
                                                    <option value="Campaign">Campaign</option>
                                                    <option value="Portfolio">Portfolio</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row mb-n3">
                                        <div class="form-group col-sm-6">
                                            <label class="col-form-label">Portfolios/Campaigns<sup class="required">*</sup></label>
                                            <div class="form-group">
                                                <select class="custom-select js-example-basic-multiple"
                                                        name="pfCampaigns[]"
                                                        id="pfCampaigns"
                                                        autocomplete="off"
                                                        multiple="multiple"
                                                        disabled="disabled">
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- lookback, frequency--}}
                                    <div class="form-row mb-n3">
                                        <div class="form-group col-sm-4">
                                            <label class="col-form-label">Pre-set rules</label>
                                            <div class="form-group">
                                                <select class="form-control"
                                                        name="fKPreSetRule"
                                                        id="fKPreSetRule">
                                                    <option value="" selected>Select pre-set rule</option>
                                                    @if(!empty($preset))
                                                        @foreach($preset as $single)
                                                            <option value="{{$single->id}}">{{$single->presetName}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-4">
                                            <label class="col-form-label">Lookback period rules<sup class="required">*</sup></label>
                                            <div class="form-group">
                                                <select class="form-control"
                                                        name="lookBackPeriod"
                                                        id="lookBackPeriod">
                                                    <option value="" selected>Select periods</option>
                                                    <option value="7d">Last 7 days</option>
                                                    <option value="14d">Last 14 days</option>
                                                    <option value="21d">Last 21 days</option>
                                                    <option value="1m">Last 1 Month</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-4">
                                            <label class="col-form-label">Frequency<sup class="required">*</sup></label>
                                            <div class="form-group">
                                                <select class="form-control"
                                                        name="frequency"
                                                        id="frequency">
                                                    <option value="" selected>Select frequency</option>
                                                    <option value="once_per_day">Once per day</option>
                                                    <option value="every_day">Every other day</option>
                                                    <option value="w">Once per week</option>
                                                    <option value="m">Once per month</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row mb-n1">
                                        <label for="inputPassword3" class="col-form-label">Statement<sup class="required">*</sup>:</label>
                                        <label for="inputPassword3" class="col-form-label">if&nbsp;&nbsp;</label>
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <select class="form-control"
                                                        data-metric-index="0"
                                                        name="metric[0]"
                                                        id="metric">
                                                    <option value="" selected>Select metric</option>
                                                    <option value="impression">Impression</option>
                                                    <option value="clicks">Clicks</option>
                                                    <option value="cost">Spend</option>
                                                    <option value="revenue">Sales</option>
                                                </select>
                                            </div>
                                        </div>
                                        <label for="inputPassword3" class="col-form-label">is</label>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <select class="form-control"
                                                        name="condition[0]"
                                                        id="condition">
                                                    <option value="" selected>Select condition</option>
                                                    <option value="greater">Greater than</option>
                                                    <option value="less">Less than</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <input type="text" class="form-control "
                                                       name="integerValues[0]"
                                                       id="integerValues"
                                                       placeholder="value">
                                            </div>
                                        </div>
                                        <div class="col-xs-1">
                                            <div class="form-group">
                                                <button class="text-primary p-2 btn btn-link" type="button" id="addButton">
                                                    Add More
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    {{--  Temmplate --}}
                                    <div class="form-row row d-none mb-n1" id="template">
                                        <div class="col-sm-3 offset-sm-2 align-self-center ">
                                            <div class="form-group">
                                                <select class="form-control ml-3_3" data-name="andOr" autocomplete="off" id="andOr">
                                                    <option value="" selected="">Select and / or</option>
                                                    <option value="and">And</option>
                                                    <option value="or">Or</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-row row col-sm-12">
                                            <label for="inputPassword3" class="col-form-label">Statement<sup class="required">*</sup>:</label>
                                            <label for="inputPassword3" class="col-form-label pt-1">if&nbsp;&nbsp;</label>
                                            <div class="col-sm-2">
                                                <div class="form-group">
                                                    <select class="form-control"
                                                            data-metric-index="0"
                                                            data-name="metric"
                                                            id="metric1"
                                                            autocomplete="off">
                                                        <option value="" selected>Select metric</option>
                                                        <option value="impression">Impression</option>
                                                        <option value="clicks">Clicks</option>
                                                        <option value="cost">Spend</option>
                                                        <option value="revenue">Sales</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <label for="inputPassword3" class="col-form-label">is</label>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <select class="form-control"
                                                            data-name="condition"
                                                            id="condition1"
                                                            autocomplete="off">
                                                        <option value="" selected>Select condition</option>
                                                        <option value="greater">Greater than</option>
                                                        <option value="less">Less than</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <div class="form-group">
                                                    <input type="text" class="form-control"
                                                           data-name="integerValues"
                                                           id="integerValues1"
                                                           placeholder="value">
                                                </div>
                                            </div>
                                            <div class="col-sm-1">
                                                <div class="form-group">
                                                    <button type="button" class="text-danger btn btn-link p-2 removeButton">
                                                        Remove
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- End template here --}}
                                    <div class="form-row mb-n2">
                                        <label for="inputPassword3" class="col-form-label">Then<sup class="required">*</sup></label>
                                        <div class="col-sm-2 ml-8">
                                            <div class="form-group">
                                                <select class="form-control"
                                                        name="thenClause"
                                                        id="thenClause">
                                                    <option value="" selected>Select</option>
                                                    <option value="raise">Raise</option>
                                                    <option value="lower">Lower</option>
                                                </select>
                                            </div>
                                        </div>
                                        <label for="inputPassword3" class="col-form-label">Bid by<sup class="required">*</sup></label>
                                        <div class="form-group col-sm-2">
                                            <input type="number" class="form-control"
                                                   name="bidBy"
                                                   id="bidBy"
                                                   placeholder="value"></div>
                                        <div class="form-group bid-by-padding mt-0">%</div>
                                    </div>
                                    <div class="form-row mb-n1">
                                        <label for="inputPassword3" class="col-form-label">Add cc</label>
                                        <div class="form-group col-sm-3 ccEmailArea email-id-row">
                                            <input type='text' id='cc_emailBS' name='ccEmails' class='form-control ccemail'>
                                        </div>
                                        <div class="form-group col-sm-3 offset-sm-5">
                                            <div class="form-group float-right">
                                                <button type="submit" class="btn btn-primary saveRule" data-save="1">
                                                    Save as Rule
                                                </button>
                                                &nbsp;&nbsp;
                                                <button type="submit" class="btn btn-primary">Submit</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{--  Bidding Rules History --}}
        <div class="row">
            <div class="col-xl-12 col-lg-12">
                <!-- Collapsable Card Example -->
                <div class="card shadow mb-4">
                    <!-- Card Header - Accordion -->
                    <a href="#biddingRuleCardHistory" class="d-block card-header py-3" data-toggle="collapse"
                       role="button" aria-expanded="true" aria-controls="biddingRuleCardHistory">
                        <h6 class="m-0 font-weight-bold text-primary">{{isset($pageHeading)?$pageHeading:''}}
                            Schedule</h6>
                    </a>
                    <!-- Card Content - Collapse -->
                    <div class="collapse show" id="biddingRuleCardHistory">
                        <!-- Card Body -->
                        <div class="card-body">
                            <div class="col-12">
                                <table id="biddingRuleDataTable"
                                       class="table table-striped table-bordered rounded"
                                       style="width:100%">
                                    <thead>
                                    <tr>
                                        <th>Sr. #</th>
                                        <th>Rule Name</th>
                                        <th>Campaign/Portfolio</th>
                                        <th>Included</th>
                                        <th>Rule</th>
                                        <th>Frequency</th>
                                        <th>Statement</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
    <!-- Edit Modal -->
    <div class="modal fade" id="staticEditModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticEditModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal- w-100 text-center text-primary" id="staticEditModalLabel">Edit Bidding Rule</h5>
                </div>
                <div class="modal-body">
                    <form id="editBiddingRuleForm" data-action="{{route('store-rule')}}" action="javascript:void(0);">
                        @csrf
                        <input type="hidden" class="form-control" name="formType" value="edit">
                        <input type="hidden" class="form-control" id="edit_bidRuleId" name="bidRuleId" value="">
                        {{-- Rule name and Sponsored type --}}
                        <div class="form-row mb-n4">
                            <div class="form-group col-sm-4">
                                <label class="col-form-label">Rule name <sup class="required">*</sup>
                                </label>
                                <input type="text" class="form-control" id="edit_ruleName" name="ruleName" placeholder="Rule Name" readonly>
                            </div>
                            <div class="form-group col-sm-4">
                                <label class="col-form-label">Child brand <sup class="required">*</sup></label>
                                <div class="form-group">
                                    <select class="form-control profileName"
                                            id="edit_profileid"
                                            name="profileFkId">
                                        <option value="">Select Child Brand</option>
                                        @if(!empty($brands))
                                            @foreach($brands as $brand)
                                                @isset($brand->fkId)
                                                    @if(!empty(trim($brand->ams['name'])) || !empty($brand->brand_alias[0]->overrideLabel))
                                                        @php
                                                            $brandOptionValue = '';
                                                               $brandOptionValue =   $brand->brand_alias != null ?
                                                                  ($brand->brand_alias != null &&
                                                                  count($brand->brand_alias) > 0 ?
                                                                  ($brand->brand_alias[0]->overrideLabel != null ?
                                                                   ($brand->brand_alias[0]->overrideLabel > 30 ?  $brand->brand_alias[0]->overrideLabel: str_limit($brand->brand_alias[0]->overrideLabel,30)):
                                                                  ($brand->ams != null ? ($brand->ams['name'] > 30 ?  $brand->ams['name']:  str_limit($brand->ams['name'],30)) : '')) :
                                                                  ($brand->ams != null ? ($brand->ams['name'] > 30 ?  $brand->ams['name']:  str_limit($brand->ams['name'],30)) : '')):
                                                                  ($brand->ams != null ? ($brand->ams['name'] > 30 ?  $brand->ams['name']:  str_limit($brand->ams['name'],30)): '');
                                                               $brandOptionTitle = '';
                                                               $brandOptionTitle =   $brand->brand_alias != null ?
                                                                  ($brand->brand_alias != null &&
                                                                  count($brand->brand_alias) > 0 ?
                                                                  ($brand->brand_alias[0]->overrideLabel != null ?
                                                                   ( $brand->brand_alias[0]->overrideLabel):
                                                                  ($brand->ams != null ? (  $brand->ams['name']) : '')) :
                                                                  ($brand->ams != null ? (  $brand->ams['name']) : '')):
                                                                  ($brand->ams != null ? (  $brand->ams['name']): '');
                                                        @endphp
                                                        <option title="{{$brandOptionTitle}}" value="{{$brand->fkId}}">
                                                            {{$brandOptionValue}}
                                                        </option>
                                                    @endif
                                                @endisset
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-sm-4">
                                <label class="col-form-label">Ad type<sup class="required">*</sup></label>
                                <div class="form-group">
                                    <select class="form-control"
                                            id="edit_sponsored_type"
                                            name="sponsoredType">
                                        <option value="" selected>Select Ad type</option>
                                        <option value="sponsoredBrand">Brand</option>
                                        <option value="sponsoredProducts">Product</option>
                                        <option value="sponsoredDisplay">Display</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        {{-- Portfolio and campaign List --}}
                        <div class="form-row mb-n4">
                            <div class="form-group col-md-6">
                                <label class="col-form-label">Portfolio/Campaign<sup class="required">*</sup></label>
                                <div class="form-group">
                                    <select class="form-control preSelectedCampaigns"
                                            id="edit_type"
                                            name="type">
                                        <option value="">Select type</option>
                                        <option value="Campaign">Campaign</option>
                                        <option value="Portfolio">Portfolio</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-row mb-n4">
                            <div class="form-group col-md-6">
                                <label class="col-form-label">Portfolios/Campaigns List<sup class="required">*</sup></label>
                                <div class="form-group">
                                    <select class="form-control js-example-basic-multiple-edit"
                                            name="pfCampaigns[]"
                                            id="edit_pfCampaigns"
                                            autocomplete="off"
                                            multiple="multiple">
                                    </select>
                                </div>
                            </div>
                        </div>
                        {{-- lookback, frequency--}}
                        <div class="form-row  ">
                            <div class="form-group col-sm-4">
                                <label class="col-form-label">Pre-set rules</label>
                                <div class="form-group">
                                    <select class="form-control"
                                            name="edit_fKPreSetRule"
                                            id="edit_fKPreSetRule">
                                        <option value="" selected>Select pre-set rule</option>
                                        @if(!empty($preset))
                                            @foreach($preset as $single)
                                                <option value="{{$single->id}}">{{$single->presetName}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                            </div>
                            <div class="form-group col-sm-4">
                                <label class="col-form-label">Lookback period rules<sup class="required">*</sup></label>
                                <div class="form-group">
                                    <select class="form-control"
                                            id="edit_lookBackPeriod"
                                            name="lookBackPeriod">
                                        <option value="">Select periods</option>
                                        <option value="7d">Last 7 days</option>
                                        <option value="14d">Last 14 days</option>
                                        <option value="21d">Last 21 days</option>
                                        <option value="1m">Last 1 Month</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-sm-4">
                                <label class="col-form-label">Frequency<sup class="required">*</sup></label>
                                <div class="form-group">
                                    <select class="form-control"
                                            id="edit_frequency"
                                            name="frequency">
                                        <option value="">Select frequency</option>
                                        <option value="once_per_day">Once per day</option>
                                        <option value="every_day">Every other day</option>
                                        <option value="w">Once per week</option>
                                        <option value="m">Once per month</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-row row col-sm-12 p-0">
                            <label for="inputPassword3" class="col-form-label">Statement<sup class="required">*</sup>:</label>
                            <label for="inputPassword3" class="col-form-label pt-1">if&nbsp;</label>
                            <div class="col-3">
                                <div class="form-group">
                                    <select class="form-control"
                                            data-metric-index="0"
                                            id="edit_metric"
                                            name="metric[0]">
                                        <option value="">Select metric</option>
                                        <option value="impression">Impression</option>
                                        <option value="clicks">Clicks</option>
                                        <option value="cost">Spend</option>
                                        <option value="revenue">Sales</option>
                                    </select>
                                </div>
                            </div>
                            <label for="inputPassword3" class="col-form-label">is</label>
                            <div class="col-3">
                                <div class="form-group">
                                    <select class="form-control "
                                            id="edit_condition"
                                            name="condition[0]">
                                        <option value="">Select condition</option>
                                        <option value="greater">Greater than</option>
                                        <option value="less">Less than</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <input type="text" class="form-control "
                                           name="integerValues[0]"
                                           id="edit_integerValues"
                                           placeholder="value">
                                </div>
                            </div>
                            <div class="col-xs-1">
                                <div class="form-group">
                                    <button class="text-primary btn btn-link" type="button" id="edit_addButton">Add more</button>
                                </div>
                            </div>
                        </div>
                        {{--  Temmplate --}}
                        <div class="form-row row d-none" id="edit_template">
                            {{--<div class="col align-self-center">--}}
                                <div class="col-3 offset-2 ml-22">
                                <div class="form-group ">
                                    <select class="form-control" data-name="andOr" autocomplete="off" id="edit_andOr">
                                        <option value="" selected="">Select and / or</option>
                                        <option value="and">And</option>
                                        <option value="or">Or</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row row col-12">
                                <label for="inputPassword3" class="col-form-label">Statement<sup class="required">*</sup>:</label>
                                <label for="inputPassword3" class="col-form-label">if&nbsp;</label>
                                <div class="col-3">
                                    <div class="form-group">
                                        <select class="form-control eidt_form_statement_fields"
                                                data-metric-index="0"
                                                data-name="metric"
                                                id="edit_metric1"
                                                autocomplete="off">
                                            <option value="" selected>Select metric</option>
                                            <option value="impression">Impression</option>
                                            <option value="clicks">Clicks</option>
                                            <option value="cost">Spend</option>
                                            <option value="revenue">Sales</option>
                                        </select>
                                    </div>
                                </div>
                                <label for="inputPassword3" class="col-form-label">is</label>
                                <div class="col-3">
                                    <div class="form-group">
                                        <select class="form-control eidt_form_statement_fields"
                                                data-name="condition"
                                                id="edit_condition1"
                                                autocomplete="off">
                                            <option value="" selected>Select condition</option>
                                            <option value="greater">Greater than</option>
                                            <option value="less">Less than</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <input type="text" class="form-control eidt_form_statement_fields"
                                               data-name="integerValues"
                                               id="edit_integerValues1"
                                               placeholder="value">
                                    </div>
                                </div>
                                <div class="col-xs-1">
                                    <div class="form-group">
                                        <button type="button" class="text-danger btn btn-link p-2 edit_removeButton">
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- End template here --}}
                        {{--show hide options ends--}}
                        <div class="form-row mb-n1">
                            <label for="inputPassword3" class="col-form-label">Then<sup class="required">*</sup></label>
                            <div class="col-3 ml-8">
                                <div class="form-group ">
                                    <select class="form-control "
                                            id="edit_thenClause"
                                            name="thenClause">
                                        <option value="">Select</option>
                                        <option value="raise">Raise</option>
                                        <option value="lower">Lower</option>
                                    </select>
                                </div>
                            </div>
                            <label for="inputPassword3" class="col-form-label">Bid by<sup class="required">*</sup></label>
                            <div class="form-group col-3">
                                <input type="number" class="form-control"
                                       name="bidBy"
                                       id="edit_bidBy"
                                       placeholder="value">
                            </div>
                            <div class="form-group col-1 bid-by-padding">%</div>
                        </div>
                        <div class="form-row">
                            <label for="inputPassword3" class="col-form-label">Add cc</label>
                            <div class="form-group col-5 ccEmailArea email-id-row">
                                <input type='text' id='editcc_emailBS' name='ccEmails' class='form-control ccemail'>
                            </div>
                            <div class="form-group col-3 offset-3 mt-3">
                                <div class="form-group float-right">
                                    <button type="button" class="btn btn-secondary close-btn" data-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Delete Modal -->
    <div class="modal fade" id="staticDeleteModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content ">
                <div class="modal-header border-0">
                    <h5 class="modal-title w-100 text-center text-primary" id="staticDeleteModalLabel">Bidding Rule</h5>
                </div>
                <div class="modal-body ">
                    <form id="biddingRuleFormDelete" data-action="{{route('store-rule')}}" action="javascript:void(0)">
                        @csrf
                        <input type="hidden" class="form-control" name="formType" value="delete">
                        <p class="text-center">Do you really want to delete this record?</p>
                        <input type="hidden" id="delete_bidding_rule_id" name="id" value="">
                        <div class="modal-footer d-flex justify-content-center border-0">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary deleteModalButton">Continue</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('select2js')
    <script type="text/javascript" src="{{asset('public/js/select2.min.js')}}"></script>
    <script type="text/javascript"
            src="{{ asset('public/tooltipster/dist/js/tooltipster.bundle.min.js')}}"></script>
    <script src="{{asset('public/vendor/daterangepicker/moment.min.js') }}"></script>
    <script type="text/javascript" src="{{asset('public/js/bootstrap-datetimepicker.min.js')}}"></script>
    {{--<script type="text/javascript" src="{{ asset('public/tooltipster/dist/js/tooltipster.bundle.min.js')}}"></script>--}}
    <script src="{{asset('public/vendor/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('public/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
    <script src="https://cdn.datatables.net/fixedcolumns/3.3.1/js/dataTables.fixedColumns.min.js"></script>
    <script type="text/javascript" src="{{asset('public/js/ams_scripts/tooltipster-scrollableTip.js')}}"></script>
    <script type="text/javascript" src="{{asset('public/js/ams_scripts/bidding-rule-ajax.js?'.time())}}"></script>
    <script type="text/javascript" src="{{asset('public/js/ams_scripts/bidding-rule-validation.js?'.time())}}"></script>
@endpush