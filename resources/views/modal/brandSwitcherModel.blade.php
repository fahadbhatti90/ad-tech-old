<div class="customModel disableClose customToolTip">
    <div class="customModelContent">
        <div class="brandPrelaoder">
            Loading 
            <div class="dot-typing"></div>
        </div>
        <div class="customModelHeader">
            Parent Brand Switcher
            <span class="close">&times</span>
        </div>
        <div class="amazonBrandsList ">
            <input class="form-control  searchBrandInput" tabindex="1" type="text" placeholder="Search Parent Brand">
            <div class="amazonBrandsListContainer ">
                {{-- <div class="amazonBrand "><a href="#">Pebbel bee</a></div> --}}
                
            </div>
        </div>
        <div class="customModelFooter">
            <a href="#" class="btn btn-sm closeBrandSwitchPopUpButton" tabindex="10">close</a>
            <a href="#" class="btn btn-sm switchBrandButton" tabindex="11" action-url="{{ route('manager.brand.switch') }}/">Switch Brand</a>
        </div>
    </div>    
</div>