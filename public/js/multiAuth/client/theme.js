
  $(function () {
      /******************************************************Theme Switcher************************************************/
      if(!localStorage.getItem("activeTheme"))
      {
        localStorage.setItem("activeThemeStopLeftColor", "#4e73df6b");
        localStorage.setItem("activeThemeStopRightColor", "#4e73df");
        $(".settingsButton").addClass("pulse");
      }
      
      $("#wrapper").addClass(localStorage.getItem("activeTheme"));
      
      $(".themeSubContainer").click(function (e) { 
        e.preventDefault();
        $("#wrapper").removeClass(localStorage.getItem("activeTheme"));
        newThemeName  = $(this).data("themename");
        localStorage.setItem("activeTheme", newThemeName);
        localStorage.setItem("activeThemeStopLeftColor", "#4e73df6b");
        localStorage.setItem("activeThemeStopRightColor", "#4e73df");
        $("#chart svg .stop-left").attr("stop-color","#4e73df6b");
        $("#chart svg .stop-right").attr("stop-color","#4e73df");
        if(newThemeName == "grayTheme"){
            
        $("#chart svg .stop-left").attr("stop-color","#8587966b");
        $("#chart svg .stop-right").attr("stop-color","#858796");
            localStorage.setItem("activeThemeStopLeftColor", "#8587966b");
            localStorage.setItem("activeThemeStopRightColor", "#858796");
        }
        $("#wrapper").addClass(newThemeName);
        $("#settingsPopup").removeClass("show");
      });//end click
      
      $(".settingsButton").click(function (e) { 
        e.preventDefault();
        if(!localStorage.getItem("activeTheme"))
        {  
          localStorage.setItem("activeTheme", "defaultThem");  
          $(".settingsButton").removeClass("pulse");
        }//end if
        $("#settingsPopup").toggleClass("show");
      });//end click 
      /******************************************************Theme Switcher************************************************/
  });

