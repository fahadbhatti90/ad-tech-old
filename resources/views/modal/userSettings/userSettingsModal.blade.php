@section('userSettingsModal')

<!--------------   user settings modal starts --------------------------->

 @push('css')
        <link rel="stylesheet" href="{{ asset('public/tooltipster/dist/css/tooltipster.bundle.min.css') }}"
              type="text/css"/>
        <link rel="stylesheet" href="{{ asset('public/vendor/datatables/dataTables.bootstrap4.min.css') }}"
              type="text/css"/>
        <link rel="stylesheet" href="{{ asset('public/css/addManager/add_manager_custom_style.css?'.time()) }}" type="text/css"/>
        <link rel="stylesheet" href="{{ asset('public/css/addManager/addManagerPage.css?'.time()) }}"
              type="text/css"/>
    @endpush

    @push('js')
        <script type="text/javascript"
                src="{{ asset('public/tooltipster/dist/js/tooltipster.bundle.min.js')}}"></script>
        <script type="text/javascript" src="{{asset('public/vendor/datatables/jquery.dataTables.min.js')}}"></script>
        <script type="text/javascript"
                src="{{asset('public/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
        <script type="text/javascript"
                src="https://cdnjs.cloudflare.com/ajax/libs/es6-shim/0.35.3/es6-shim.min.js"></script>
        <script type="text/javascript"
                src="{{asset('public/vendor/formvalidation/dist/js/plugins/zxcvbn.js')}}"></script>
        <script type="text/javascript"
                src="{{asset('public/vendor/formvalidation/dist/js/plugins/PasswordStrength.min.js')}}"></script>
        <script type="text/javascript" src="{{ asset('public/js/userSettings/userSettings.js?'.time()) }}"></script>
    @endpush
 
  <div class="modal fade userSettingsModal" id="userSettingsModal" tabindex="-1" role="dialog"
                     aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">User Settings</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    {{-- Cron Job List --}}
                                    <div class="col-12">
                                        <div class=" mt-3">
                                            {{-- Get Profile List --}}
                                            <div class="col-xl-12 input-group mb-3">
                                                <form id="userSettingsForm" method="POST"
                                                      action="{{ route('user.settings.update') }}" action_type="1"
                                                      enctype="multipart/form-data">
                                                    @csrf
                                                    {{-- Client Name --}}
                                                    <div class="form-group ">
                                                        <label for="clientName">Name</label>
                                                        <input type="text" name="clientName" class="form-control"
                                                               placeholder="Manager Name" id="clientName" value="{{ Auth::user()->name }}">
                                                    </div>
                                                    {{-- Client Email --}}
                                                    <div class="form-group ">
                                                        <label for="clientEmail">Email</label>
                                                        <input type="text" name="clientEmail" class="form-control"
                                                               placeholder="Client Email" id="clientEmail" value="{{ Auth::user()->email }}">
                                                    </div>
                                                   
                                                    {{-- password --}}
                                                    {{-- Client Name --}}
                                                    <div class="form-group passwordParent">
                                                        <label for="password">Password</label>
                                                        <input type="password" name="password" class="form-control"
                                                               placeholder="Password" id="password">
                                                    </div>
                                                    <div class="cf mb2">
                                                        <div class="fl w-100">
                                                            <div class="fl w-25 pa2"></div>
                                                            <div class="fl w-100 ba b--black-10 h1"
                                                                 style="height: 0.25rem">
                                                                <div id="passwordMeter" class="h-100"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                                data-dismiss="modal">Close
                                                        </button>
                                                        <button class="btn btn-primary ClientSubmitButton"
                                                                type="submit">Save
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

    <!--------------   user settings modal ends --------------------------->   
@endsection