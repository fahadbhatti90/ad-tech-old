@extends('layout.app')
@extends('inc.side_menu')
@extends('inc.nav_bar')
@extends('modal.logout')
@section('title',$pageTitle)
@section('content')
    @push('select2css')
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.6/css/all.css"/>
        <link rel="stylesheet" href="{{asset('public/fullcalendar/core/main.css')}}"/>
        <link rel="stylesheet" href="{{asset('public/fullcalendar/daygrid/main.css')}}"/>
        <link rel="stylesheet" href="{{asset('public/fullcalendar/bootstrap/main.css')}}"/>
        <link rel="stylesheet" href="{{asset('public/tooltipster/dist/css/tooltipster.bundle.min.css')}}"/>
        <link rel="stylesheet" href="{{asset('public/ams/dayparting/css/history.css')}}"/>

    @endpush

    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Begin Breadcrumb -->
{{--    {{ Breadcrumbs::render('day_partying_history') }}--}}
    <!-- End Breadcrumb -->
        <!-- Page Heading -->
        <div class="row">
            <!-- Area Chart -->
            <div class="col-xl-12 col-lg-12">
                {{-- Show Success and Errro Messages --}}
                <div class="card shadow mb-4">
                {{--@include('subpages.vc.messages')--}}
                <!-- Card Header - Dropdown -->
                    <a href="#dayPartingHistoryContainer" class="d-block card-header py-3" data-toggle="collapse"
                       role="button" aria-expanded="true" aria-controls="dayPartingHistoryContainer">
                        <h6 class="m-0 font-weight-bold text-primary">{{isset($pageHeading)?$pageHeading:''}}</h6>
                    </a>
                    <!-- Card Content - Collapse -->
                    <div class="collapse show" id="dayPartingHistoryContainer">
                        <!-- Card Body -->
                        <div class="card-body row">
                            <label class="col-form-label">Child Brand</label>
                            <div class="form-group formHistory col-md-3">
                                <select class="form-control fkProfileIdDayPartingHistory" name="fkProfileId"
                                        autocomplete="off">
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
                            <div id='calendar'></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('select2js')
    <script src="{{asset('public/vendor/daterangepicker/moment.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('public/tooltipster/dist/js/tooltipster.bundle.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('public/fullcalendar/core/main.js')}}"></script>
    <script type="text/javascript" src="{{ asset('public/fullcalendar/daygrid/main.js')}}"></script>
    <script type="text/javascript" src="{{ asset('public/fullcalendar/bootstrap/main.js')}}"></script>
    <script type="text/javascript" src="{{ asset('public/fullcalendar/interaction/main.js')}}"></script>
    <script src="{{asset('public/js/vc_scripts/vccustom.js?'.time())}}"></script>
    <script>
        $(function () {
            var calendarEl = document.getElementById('calendar');
            // On change portfolio or campaign will come in select2
            $("select.fkProfileIdDayPartingHistory").on('change', function () {
                var fkProfileId = $('.fkProfileIdDayPartingHistory').val();
                AjaxFunctionDaypartingHistory(fkProfileId, calendarEl);
            }); // End on change functionality

            var fkProfileId = $("select.fkProfileIdDayPartingHistory:nth-child(1)").val();
            AjaxFunctionDaypartingHistory(fkProfileId, calendarEl);

            function AjaxFunctionDaypartingHistory(fkProfileId, calendarEl) {
                $("#calendar").empty();
                var csrfToken = $("body").attr('csrf');
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    plugins: ['bootstrap', 'dayGrid', 'yearView', 'interaction'],
                    defaultView: 'dayGridMonth',
                    themeSystem: 'bootstrap',
                    header: {
                        right: 'prevYear,prev,next,nextYear today',
                        center: 'title',
                        left: 'year',
                    },
                    buttonIcons:false,
                    showNonCurrentDates: false,
                    lazyFetching: true,
                    selectable: true,
                    //navLinks: true,
                    eventLimit: true, // allow "more" link when too many events
                    eventPositioned: function (info) {
                        $(info.el).find('.fc-title').tooltipster({
                            content: info.event.extendedProps.description,
                            trigger: 'hover',
                            debug: false
                        });
                    },
                    events: {
                        url: siteURL + '/dayParting/getHistorySchedule?_=' + new Date().getTime(),
                        method: 'POST',
                        extraParams: {
                            "_token": csrfToken,
                            "fkProfileId": fkProfileId
                        },
                    },
                    eventSourceSuccess: function(content, xhr) {
                        showTooltiper('button.fc-prevYear-button', 'Previous Year');
                        showTooltiper('button.fc-prev-button', 'Previous Month');
                        showTooltiper('button.fc-next-button', 'Next Month');
                        showTooltiper('button.fc-nextYear-button', 'Next Year');
                    }
                });
                calendar.render();
            }

            showTooltiper('button.fc-prevYear-button', 'Previous Year');
            showTooltiper('button.fc-prev-button', 'Previous Month');
            showTooltiper('button.fc-next-button', 'Next Month');
            showTooltiper('button.fc-nextYear-button', 'Next Year');

            function showTooltiper(className, content){
                $(className).tooltipster({
                    content: content,
                    trigger: 'hover',
                    debug: false
                });
            }
        });
    </script>
@endpush
