

    // Enable pusher logging - don't include this in production
    Pusher.logToConsole = false;
     
    var pusher = new Pusher('361445dda10cc3a35fc6', {
      cluster: 'ap2',
      forceTLS: true,
    //authEndpoint: '/broadcasting/auth',
      auth: {
        headers: { 'X-CSRF-Token': $("body").attr("csrf") }
      }
    });
    
    
    function notificationClicked(data) { 
    }
    $(function () {
        var channel = pusher.subscribe('hello-tec-channel');
        channel.bind('sendNotification', function (data) {
            // console.log(data)
            if (!('host' in data) || data.host == "404" || data.type == 1){
                // alert("no HOst Found in incoming message");
                return;
            }
           
            var CurrentHost = $("body").attr("host");
            if (CurrentHost == "404") {
                toastr.options = {
                    "closeButton": true,
                    "debug": false,
                    "newestOnTop": true,
                    "progressBar": true,
                    "positionClass": "toast-top-right",
                    "preventDuplicates": false,
                    "onclick": null,
                    "showDuration": "300",
                    "hideDuration": "1000",
                    "timeOut": "5000",
                    "extendedTimeOut": "0",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                  }
                  toastr["info"]("Major Host Error","No Host Found in Settings Table Please Contact Your Service Provider till then notifications Module will not display live notfication");
            } else {
                if(!CurrentHost.includes(data.host)){
                    // alert(data.host.includes($("body").attr("base_url")));
                    return;
                }
            }
           
            $(".notificationLi").removeClass("notify");
            $(".notificationLi").addClass("notify");
            // $("#notificationDropdown > span > mark").addClass("on");
            $("#notificationDropdown mark").text(parseInt($("#notificationDropdown mark").text())+1);
                // console.log(data.message);
                var icon = "";
                switch (parseInt(data.type)) {
                    case 2:
                        icon = "BL";
                        break;
                    case 3:
                            icon = "S";
                            break;
                    default:
                        icon = "BB";
                        break;
                }
                // console.log(icon);
                var notiTemplate = ' <li class="row unseen"  id="'+data.id+'">'
                                    +'    <div class="col-md-3 col-sm-3 col-xs-3"><div class="notify-img">'+icon+'</div></div>'
                                    +'    <div class="col-md-9 col-sm-9 col-xs-9 pd-l0">'
                                    +'        <p ><b>'+data.title
                                    +'          </b>'
                                    +'    <span class="rIcon">'
                                    +'        <i class="fa fa-dot-circle"></i>'
                                    +'    </span>'
                                    
                                    +'</p>'
                                    +'        <p >'+data.message
                                    +'</p>'
                                   
                                    +'        <hr>'
                                    +'        <p class="time">'
                                    +(data.created_at)
                                    +'        </p>'
                                    +'    </div>'
                                    +'</li>';
                                    totalUnseenCount = parseInt($("#notificationDropdown > span > mark").attr("data-count"));
                                    isBlackListNotiExist = $("#blacklist .drop-content li").length;
                                    isDecaptchaNotiExist = $("#decaptcha .drop-content li").length;
                                    isBuyBoxNotiExist = $("#buybox .drop-content li").length;
                                    // console.log(isNotiExist)
                                    // if(totalUnseenCount <= 0 && isNotiExist <= 0 )
                                    // {
                                    // }
                                    $("#notificationDropdown > span > mark").addClass("on show");
                                    $("#notificationDropdown > span > mark").html(totalUnseenCount+1);
                                    $("#notificationDropdown > span > mark").attr("data-count",totalUnseenCount+1);
                                    switch (parseInt(data.type)) {
                                        case 2:
                                            blackListCount = parseInt($(".notificationPopUp > .nav-tabs > .blacklistTab > a > sup").attr("data-count"))+1;
    
                                            if(isBlackListNotiExist > 0)
                                            $("#blacklist .drop-content li:nth-child(1)").before(notiTemplate);
                                            else
                                            $("#blacklist .drop-content").html(notiTemplate);
                                            
                                            $("#blacklist .allRead").addClass("unseen");
                                            $(".notificationPopUp > .nav-tabs > .blacklistTab > a > sup").text(blackListCount);             
                                            $(".notificationPopUp > .nav-tabs > .blacklistTab > a > sup").attr("data-count",blackListCount);
                                            $("#blacklist .notify-drop-title .notiCount b").html(blackListCount);
                                            $("#blacklist .notify-drop-footer").text("");
                                            break;
                                        case 3:
                                                decaptchaCount = parseInt($(".notificationPopUp > .nav-tabs > .decaptchaTab > a > sup").attr("data-count"))+1;
                                                if(isDecaptchaNotiExist>0)
                                                $("#decaptcha .drop-content li:nth-child(1)").before(notiTemplate);
                                                else
                                                $("#decaptcha .drop-content").html(notiTemplate);
    
                                                $("#decaptcha .allRead").addClass("unseen");
                                                $(".notificationPopUp > .nav-tabs > .decaptchaTab > a > sup").text(decaptchaCount);    
                                                $(".notificationPopUp > .nav-tabs > .decaptchaTab > a > sup").attr("data-count",decaptchaCount);
                                                $("#decaptcha .notify-drop-title .notiCount b").html(decaptchaCount);
                                                $("#decaptcha .notify-drop-footer").text("");
                                                break;
                                        default:
                                            blackListCount = parseInt($(".notificationPopUp > .nav-tabs > .blacklistTab > a > sup").attr("data-count"))+1;
    
                                            if(isBlackListNotiExist > 0)
                                            $("#blacklist .drop-content li:nth-child(1)").before(notiTemplate);
                                            else
                                            $("#blacklist .drop-content").html(notiTemplate);
                                            
                                            $("#blacklist .allRead").addClass("unseen");
                                            $(".notificationPopUp > .nav-tabs > .blacklistTab > a > sup").text(blackListCount);             
                                            $(".notificationPopUp > .nav-tabs > .blacklistTab > a > sup").attr("data-count",blackListCount);
                                            $("#blacklist .notify-drop-title .notiCount b").html(blackListCount);
                                            $("#blacklist .notify-drop-footer").text("");
                                            break;
                                    }
            setTimeout(function(){
                $(".notificationLi").removeClass("notify");
                toastr.options = {
                    "closeButton": true,
                    "debug": false,
                    "newestOnTop": true,
                    "progressBar": true,
                    "positionClass": "toast-top-right",
                    "preventDuplicates": false,
                    "onclick": function(){
                        window.location.href = $("body").attr("base_url")+"/notifications/"+data.id+"/preview/"; 
                    },
                    "showDuration": "300",
                    "hideDuration": "1000",
                    "timeOut": "5000",
                    "extendedTimeOut": "0",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                  }
                  toastr["info"](data.message,data.title);
                $("#notificationDropdown > span > mark").removeClass("on");
            },1000);
            channel = pusher.subscribe('hello-tec-channel');
        });
        var notiPreloader = '<img src="'+$("body").attr("base_url")+'/public/images/NotiPreloader.gif" class="notiPreloader">';
        var tries = 1;
        var isDataFound = false;
        var CurrentHost = $("body").attr("host");
        if (CurrentHost == "404") {
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "0",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            }
            toastr["warning"]("No host found in settings please contact your service provider till then notifications module will not display any live notfication( Need To REFRESH For New Notification )", "Major Host Error");
        }
   
        $.ajax({
            type: "get",
            url: $("body").attr("base_url") + "/getNotifications",
            data: {
                userType:1
            },
            success: function (response) {
                isDataFound = !isDataFound;
                var buybox = response.BuyBoxNotificaitons;
                var blacklist = response.BlackListNotificaitons;
                var decaptcha = response.DecaptchaNotificaitons;
                
                var notiTemplateBuyBox = "";
                var notiTemplateBlackList = "";
                var notiTemplateDecaptcha = "";

                var buyboxUnseenCount = response.BuyBoxUnseenNotificaitonsCount; 
                var blackListUnseenCount = response.BlackListUnseenNotificaitonsCount; 
                var DecaptchaUnseenCount = response.DecaptchaUnseenNotificaitonsCount;
                var totalUnseen = buyboxUnseenCount + blackListUnseenCount + DecaptchaUnseenCount;

                notiTemplateBuyBox = setBuyBoxNoti(buybox);
                notiTemplateBlackList = setBlackListNoti(blacklist);
                notiTemplateDecaptcha = setDecaptchaNoti(decaptcha);
                
                

                $("#notificationDropdown > span > mark").addClass(totalUnseen > 0 ? "on show":"hide");
                $("#notificationDropdown > span > mark").html(totalUnseen > 0 ? totalUnseen:"");
                $("#notificationDropdown > span > mark").attr("data-count", totalUnseen);

                /*if(notiTemplateBuyBox != "")
                {
                    $("#buybox .drop-content").html(notiTemplateBuyBox);
                }
                else
                {
                    $("#buybox .drop-content").html(notiTemplateBuyBox);
                    $("#buybox .notify-drop-footer").text("No New Notification");
                }*/

                if(notiTemplateBlackList !="")
                {
                    $("#blacklist .drop-content").html(notiTemplateBlackList);
                }   
                else
                {
                    $("#blacklist .drop-content").html(notiTemplateBlackList);
                    $("#blacklist .notify-drop-footer").text("No New Notification");
                }

                if(notiTemplateDecaptcha !="")
                { 
                    $("#decaptcha .drop-content").html(notiTemplateDecaptcha);
                }
                else
                {
                    $("#decaptcha .drop-content").html(notiTemplateDecaptcha);
                    $("#decaptcha .notify-drop-footer").text("No New Notification");
                }
                //notify-drop-title count 

                /*if(buyboxUnseenCount != 0)
                {   
                    console.log(buyboxUnseenCount);
                    $("#buybox .allRead").addClass("unseen");
                    $("#buybox .notify-drop-footer").text(buyboxUnseenCount+" New Notification");
                }
                else
                {
                    $("#buybox .notify-drop-footer").text("No New Notification");
                }*/
                if(blackListUnseenCount != 0)
                {
                    $("#blacklist .allRead").addClass("unseen");
                    $("#blacklist .notify-drop-footer").text(blackListUnseenCount+" New Notification");
                }  
                else
                {
                    $("#blacklist .notify-drop-footer").text("No New Notification");
                }
                if(DecaptchaUnseenCount !=0)
                { 
                    $("#decaptcha .allRead").addClass("unseen");
                    $("#blacklist .notify-drop-footer").text(DecaptchaUnseenCount+" New Notification");
                }
                else
                {
                    $("#decaptcha .notify-drop-footer").text("No New Notification");
                }
                //$("#buybox .notify-drop-title .notiCount b").html(buyboxUnseenCount);
                $("#blacklist .notify-drop-title .notiCount b").html(blackListUnseenCount);
                $("#decaptcha .notify-drop-title .notiCount b").html(DecaptchaUnseenCount);
                //tab title count
                //$(".notificationPopUp > .nav-tabs > .buyboxTab > a > sup").html(parseInt(buyboxUnseenCount) > 0?buyboxUnseenCount:"");
                //$(".notificationPopUp > .nav-tabs > .buyboxTab > a > sup").attr("data-count",buyboxUnseenCount);
            
                $(".notificationPopUp > .nav-tabs > .blacklistTab > a > sup").html(parseInt(blackListUnseenCount) > 0?blackListUnseenCount:"");
                $(".notificationPopUp > .nav-tabs > .blacklistTab > a > sup").attr("data-count",blackListUnseenCount);
            
                $(".notificationPopUp > .nav-tabs > .decaptchaTab > a > sup").html(parseInt(DecaptchaUnseenCount)>0 ?DecaptchaUnseenCount:"");
                $(".notificationPopUp > .nav-tabs > .decaptchaTab > a > sup").attr("data-count",DecaptchaUnseenCount);

                // setInterval(function(){
                //     $("#notificationDropdown > span > mark").removeClass("on");
                // },2000);
            },
            error:function(error){
                if(error.responseText.includes("Unauthenticed")){
                    location.reload();
                }
                else
                { 
                    if(tries>=3){
                        isDataFound = !isDataFound;
                    }
                }
            }
        });//end ajax function
       
        $(".notify-drop div.drop-content").on("click","li.row", function (e) {
            e.preventDefault();
            $(this).find("")
            noti_id = $(this).attr("id");
            window.location.href = $("body").attr("base_url")+"/notifications/"+noti_id+"/preview/";
        });
        $(".notify-drop div.drop-content").on("click","span.rIcon", function (e) {
            e.preventDefault();
            // e.stopPropagation();
            // alert($(this).text())
        });
        //mark all read 
        $('.markAllReadBtn > a.allRead').on('click',function (event) {
            event.preventDefault();
            unseenLi = $(this).parents(".notify-drop").find(".drop-content .unseen");

            if(unseenLi.length<=0)
            return;
            var parent = $(this).attr("data-parent");
            $(this).find("i").hide();
            $(this).append(notiPreloader);
                thisobj = $(this);
            var arr = [];
            for (let i = 0; i < unseenLi.length; i++) {
                element = unseenLi[i];
                arr.push($(element).attr("id"))
                $(element).removeClass("unseen");
                $(element).find(".rIcon").html(notiPreloader);
            }
            //updates the notification status
            $.ajax({
                type: "post",
                url: $("body").attr("base_url")+"/notifications/readAll",
                data: {
                    "ids":arr,
                    "_token":$("body").attr("csrf")
                },
                success: function (response) {
                    // console.log(response)
                    if(parseInt(response) > 0){
                        $(thisobj).find("i").show();
                        $(thisobj).find("img").remove();
                        $(thisobj).removeClass("unseen")
                        //remove preloader
                        for (let i = 0; i < unseenLi.length; i++) {
                            element = unseenLi[i];
                            $(element).find(".rIcon").html("");
                        }//end for loop
                        
                        totalUnseenCount = parseInt($("#notificationDropdown > span > mark").attr("data-count"));
                        newTotalUnseenCount = totalUnseenCount-unseenLi.length;
                        // console.log(newTotalUnseenCount)
                        if(newTotalUnseenCount > 0){
                            $("#notificationDropdown > span > mark").html(newTotalUnseenCount);
                            $("#notificationDropdown > span > mark").attr("data-count",newTotalUnseenCount);

                        }
                        else{
                            
                            $("#notificationDropdown > span > mark").html("");
                            $("#notificationDropdown > span > mark").attr("data-count",0);
                            $("#notificationDropdown > span > mark").removeClass("show");
                            $("#notificationDropdown > span > mark").addClass("hide");
                        }
                        //update the ui
                        switch (parent) {
                            case "blacklist":
                                $("#blacklist .allRead").removeClass("unseen");
                                $(".notificationPopUp > .nav-tabs > .blacklistTab > a > sup").text("");             
                                $(".notificationPopUp > .nav-tabs > .blacklistTab > a > sup").attr("data-count",0);
                                $("#blacklist .notify-drop-title .notiCount b").html(0);
                                $("#blacklist .notify-drop-footer").text("No New Notification");
                                break;
                            case "decaptcha":
                                    $("#decaptcha .allRead").removeClass("unseen");
                                    $(".notificationPopUp > .nav-tabs > .decaptchaTab > a > sup").text("");    
                                    $(".notificationPopUp > .nav-tabs > .decaptchaTab > a > sup").attr("data-count",0);
                                    $("#decaptcha .notify-drop-title .notiCount b").html(0);
                                    $("#decaptcha .notify-drop-footer").text("No New Notification");
                                    break;
                            default:
                                $("#blacklist .allRead").removeClass("unseen");
                                $(".notificationPopUp > .nav-tabs > .blacklistTab > a > sup").text("");             
                                $(".notificationPopUp > .nav-tabs > .blacklistTab > a > sup").attr("data-count",0);
                                $("#blacklist .notify-drop-title .notiCount b").html(0);
                                $("#blacklist .notify-drop-footer").text("No New Notification");
                                break;
                        }


                    }//end if
                },//end success function
                error:function(error){
                    // console.log(error)
                    if(error.responseText.includes("Unauthenticed")){
                        location.reload();
                    }
                    else
                    { 
                        Swal.fire({
                            title: '<strong>Error</strong>',
                            type: 'error',
                            text:"Some Error Occur"
                        })
                    }
                }
            });//end ajax function

        //   console.log(typeof arr);
        });//end click function

        //on scroll load message
        $(".notify-drop .drop-content").scroll(function () { 
            if ($(this).scrollTop() +  $(this).innerHeight() >= $(this)[0].scrollHeight) 
            { 
            
            } 
        });//end scroll function

        //toggle notificaiton popup
        $('li.notificationLi > a').on('click',function (event) {
            event.preventDefault();
            $(".notificationPopUp").toggleClass('show');
        });//end click function
    
        // toastr.info('Are you the 6 fingered man?')

        function setBuyBoxNoti(buybox){
            notiTemplateBuyBox = '';
            buybox.forEach(element => {
                icon = "BB";
            
                notiTemplateBuyBox += ' <li class="row '+(!element.status?"unseen":"")+'" id="'+element.id+'">'
                +'    <div class="col-md-3 col-sm-3 col-xs-3"><div class="notify-img">'+icon+'</div></div>'
                +'    <div class="col-md-9 col-sm-9 col-xs-9 pd-l0">'
                +'        <p ><b>'+element.title
                +'          </b>'
                +'    <span class="rIcon">'
                +(!element.status?'<i class="fa fa-dot-circle"></i>':"")
                +'    </span>'
                
                +'</p>'
                +'        <p >'+element.message
                +'</p>'

                +'        <hr>'
                +'        <p class="time">'
                +(element.created_at)
                +'        </p>'
                +'    </div>'
                +'</li>';
            });//end foreach
            return notiTemplateBuyBox;
        }//end function
        function setBlackListNoti(blacklist){
            notiTemplateBlackList = '';
            blacklist.forEach(element => {
                icon = "BL";
                notiTemplateBlackList += ' <li class="row '+(!element.status?"unseen":"")+'" id="'+element.id+'">'
                +'    <div class="col-md-3 col-sm-3 col-xs-3"><div class="notify-img">'+icon+'</div></div>'
                +'    <div class="col-md-9 col-sm-9 col-xs-9 pd-l0">'
                +'        <p ><b>'+element.title
                +'          </b>'
                +'    <span class="rIcon">'
                +(!element.status?'<i class="fa fa-dot-circle"></i>':"")
                +'    </span>'
                
                +'</p>'
                +'        <p >'+element.message
                +'</p>'

                +'        <hr>'
                +'        <p class="time">'
                +(element.created_at)
                +'        </p>'
                +'    </div>'
                +'</li>';
            });//end foreach
            return notiTemplateBlackList;
        }//end function
        function setDecaptchaNoti(decaptcha){
            notiTemplateDecaptcha = '';
            decaptcha.forEach(element => {
                icon = "D";
                notiTemplateDecaptcha += ' <li class="row '+(!element.status?"unseen":"")+'" id="'+element.id+'">'
                +'    <div class="col-md-3 col-sm-3 col-xs-3"><div class="notify-img">'+icon+'</div></div>'
                +'    <div class="col-md-9 col-sm-9 col-xs-9 pd-l0">'
                +'        <p ><b>'+element.title
                +'          </b>'
                +'    <span class="rIcon">'
                +(!element.status?'<i class="fa fa-dot-circle"></i>':"")
                +'    </span>'
                
                +'</p>'
                +'        <p >'+element.message
                +'</p>'

                +'        <hr>'
                +'        <p class="time">'
                +(element.created_at)
                +'        </p>'
                +'    </div>'
                +'</li>';
            });//end foreach
            return notiTemplateDecaptcha;
        }//end function
});//end ready function 
