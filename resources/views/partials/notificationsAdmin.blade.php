<ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="buyboxTab">
        <a href="#buybox" aria-controls="buybox" role="tab" data-toggle="tab" class="active"> 
            <span>Buy Box</span>
            <sup></sup>
        </a>
    </li>
    <li role="presentation" class="blacklistTab">
        <a href="#blacklist" aria-controls="blacklist" role="tab" data-toggle="tab">  
            <span>Black List</span>
            <sup></sup>
        </a>
    </li>
    <li role="presentation" class="decaptchaTab">
        <a href="#decaptcha" aria-controls="decaptcha" role="tab" data-toggle="tab">  
            <span>Settings</span>
            <sup></sup>
        </a>
    </li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="buybox">
            <ul class="notify-drop">
                    <div class="notify-drop-title">
                        <div class="row">
                            <div class="col-md-6 col-sm-6 col-xs-6 notiCount">New Notifications (<b>0</b>)</div>
                            <div class="col-md-6 col-sm-6 col-xs-6 text-right markAllReadBtn"><a href="" class="rIcon allRead" data-tooltip="tooltip" data-placement="bottom" title="Mark all as read" data-parent="buybox"><i class="fa fa-dot-circle"></i></a></div>
                        </div>
                    </div>
                    <!-- end notify title -->
                    <!-- notify content -->
                    <div class="drop-content container">
                            <img src="{{ asset('public/images/preloader.gif') }}" class="notiPreloader">
                    
                    </div>
                    <div class="notify-drop-footer text-center">
                        {{-- <a href=""><i class="fa fa-eye"></i> Tümünü Göster</a> --}}
                        loading...
                    </div>
            </ul>
    </div>
    <div role="tabpanel" class="tab-pane" id="blacklist">
            <ul class="notify-drop">
                    <div class="notify-drop-title">
                        <div class="row">
                            <div class="col-md-6 col-sm-6 col-xs-6 notiCount">New Notifications (<b>0</b>)</div>
                            <div class="col-md-6 col-sm-6 col-xs-6 text-right markAllReadBtn"><a href="" class="rIcon allRead" data-tooltip="tooltip" data-placement="bottom" title="Mark all as read" data-parent="blacklist"><i class="fa fa-dot-circle"></i></a></div>
                        </div>
                    </div>
                    <!-- end notify title -->
                    <!-- notify content -->
                    <div class="drop-content container">
                            <img src="{{ asset('public/images/preloader.gif') }}" class="notiPreloader">
                    </div>
                    <div class="notify-drop-footer text-center">
                            loading...
                        {{-- <a href=""><i class="fa fa-eye"></i>Sroll down to see more notifications</a> --}}
                    </div>
            </ul>
    </div>
    <div role="tabpanel" class="tab-pane" id="decaptcha">
            <ul class="notify-drop">
                    <div class="notify-drop-title">
                        <div class="row">
                            <div class="col-md-6 col-sm-6 col-xs-6 notiCount">New Notifications (<b>10</b>)</div>
                            <div class="col-md-6 col-sm-6 col-xs-6 text-right markAllReadBtn" ><a href="" class="rIcon allRead" data-tooltip="tooltip" data-placement="bottom" title="Mark all as read" data-parent="decaptcha"><i class="fa fa-dot-circle"></i></a></div>
                        </div>
                    </div>
                    <!-- end notify title -->
                    <!-- notify content -->
                    <div class="drop-content container">
                            <img src="{{ asset('public/images/preloader.gif') }}" class="notiPreloader">
                        
                    </div>
                    <div class="notify-drop-footer text-center">
                        loading...
                        {{-- <a href=""><i class="fa fa-eye"></i> Tümünü Göster</a> --}}
                    </div>
            </ul>

    </div>
    
</div>
