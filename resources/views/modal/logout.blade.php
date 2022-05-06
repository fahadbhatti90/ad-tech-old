@section('modal_logout')
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    {{-- @auth('agency')    
                        <a class="btn btn-primary" href="{{ route('agency.logout') }}">Logout</a>
                    @endauth
                    @auth('web')    
                        <a class="btn btn-primary" href="{{ route('helotek.logout') }}">Logout</a>
                    @endauth --}}
                    @if(isset(Auth::user()->type) && Auth::user()->type =="client")
                    <a class="btn btn-primary" href="{{ route('client.logout') }}">Logout</a>
                    @else
                    <a class="btn btn-primary" href="{{ route('helotek.logout') }}">Logout</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
