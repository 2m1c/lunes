$(function() {

// follow a branch
var $followBranch = (function(){
    //dom elements
    var $btn    = $(".js-follow-branch"),
    href        = "func/follow_branch.php",

    // initalize fuction
    init        = function() {
        initEvents();
    },

    // ajax function
    ajaxPost    = function(elem,branch_data) {
        elem.html('<img src="img/preloader.gif" />');
        $.ajax({
            type: 'POST',
            url: href,
            data: { branch_data:branch_data },
            success: function(data) {
               
                if(data == 'no-auth')
                {
                    alert('Takip etmek için üye girişi yapmanız gerekmetedir');
                    window.location = 'login.php';
                }
                else if(data==1) {
                    elem
                        .removeClass("btn-follow")
                        .addClass("btn-unfollow")
                        .text("Takibi Bırak");
                        // reload the page.
                        location.reload(true);
               } else if(data==2) {
                    elem
                    .removeClass("btn-unfollow")
                    .addClass("btn-follow")
                    .html("Takip Et");
                    // reload the page.
                    location.reload(true);
               }

               
               
            }
        });
    },

    initEvents  = function() {

        $btn.on('click',this,function(event){
            event.preventDefault();
            that        = $(this);
            branch_data = that.data('branch');
            ajaxPost(that, branch_data);
        });
    };

    return { init : init };

})();
$followBranch.init();

// keyup search
var $ajaxkeyUpSearch = (function() {

    // dom elem
    var $input  = $('.js-keyup-search'),
    href        = "inc/keyup_search.php",

    //initialize function
    init = function() {
        initEvents();
    },

    ajaxHandler = function(inputTag,keyUpData, searchField)
    {
        $.ajax({
            type: 'POST',
            url: href,
            data: { keyUpData : keyUpData, searchField : searchField },
            success: function(data) {
                inputTag.siblings(".js-keyup-result").html(data);
            }
        });
    },

    closeResult = function(inputTag) {
        inputTag.siblings(".js-keyup-result")
                .delay(100)
                .fadeOut(100)
                .fadeIn(0, function(){ $(this).html(' ') } );
                
    },

    initEvents = function() {
        $input.keyup(function(){
            thisVal     = $(this).val();
            if(thisVal.length >= 3) {
                searchField = $(this).data("search");
                ajaxHandler( $(this) ,thisVal , searchField);
            }
        });

        $input.blur(function(){
            closeResult($(this));
        });
    };

    return { init : init };


}) ();
$ajaxkeyUpSearch.init();

// post comment via ajax
$postComment = (function(){
    
    var $commentBox = $(".js-post-comment"),
    href        = "func/post_comment.php",

    init        = function() {
        initEvents();
    },

    ajaxHandler = function(elem, commentData, postId, postOwner) {
        elem.attr("disabled", true);
        $.ajax({
            type: 'POST',
            url: href,
            data: { commentData : commentData, postId : postId, postOwner : postOwner },
            success: function(data) {
                if(data == 'no-auth')
                {
                    alert('Yorum yapmak için üye girişi yapmanız gerekmetedir');
                    window.location = 'login.php';
                }
                else if(data == 0) {
                    alert("Yorum Gönderilemedi");
                } else {
                    elem.parents(".js-commentsBoxlist").find(".js-comments").html(data);
                    elem.val("");
                    elem.attr("disabled", false);
                }
            }
        });
    },

    initEvents  = function() {
        $commentBox.keydown(function(e){
            
            if(e.which == 10 || e.which == 13) {
                thisVal     = $(this).val(),
                postId      = $(this).data("post"),
                postOwner   = $(this).data("postowner");
                if(thisVal.length == 0) {
                    alert("birşeyler yaz");
                }
                else {
                    ajaxHandler( $(this), thisVal, postId, postOwner);
                }
            }
        });
    };

    return { init : init };

})();
$postComment.init();


// post comment via ajax
$postDiagnosis = (function(){
    
    var $commentBox = $(".js-post-diganosis"),
    href            = "inc/post_diagnosis.php",

    init        = function() {
        initEvents();
    },

    ajaxHandler = function(elem, commentData, postId) {
        elem.attr("disabled", true);
        $.ajax({
            type: 'POST',
            url: href,
            data: { commentData : commentData, postId : postId },
            success: function(data) {
                if(data == 0) {
                    alert("Tanı Gönderilemedi");
                } else {
                    elem.val(data);
                    elem.siblings(".js-diganosis").text(data);
                    alert("Tanı Kayıt Edildi");
                    elem.attr("disabled", false);
                }
            }
        });
    },

    initEvents  = function() {
        $commentBox.keydown(function(e){
            
            if(e.which == 10 || e.which == 13) {
                var thisVal     = $(this).val(),
                postId      = $(this).data("post");
                if(thisVal.length == 0) {
                    alert("birşeyler yaz");
                }
                else {
                    ajaxHandler( $(this), thisVal, postId );
                }
            }
        });
    };

    return { init : init };

})();
$postDiagnosis.init();

// post panel textarea action
$postPanelTextarea = (function() {
    var $input  = $("#postPanelTextarea"),

    init        = function() {
        initEvents();
    },

    // increase size of height
    increaseHeight= function(elem) {
        elem.animate({height: '200px'}, 250);
    },

    // decrease size of height
    decreaseHeight= function(elem) {
        elem.animate({height: '54px'}, 250);
    },

    initEvents  = function() {

        // increase size of height if focus
        $input.focus(function(){
            increaseHeight( $(this) );
        });

        // increase size of height if decrease
        $input.blur(function(){
            decreaseHeight( $(this) );
        });

    };

    return { init : init };

})();
$postPanelTextarea.init();

/* 
    => vote for comment
    => only post owner can vote
    => if comment is useful it will be yellow else black
*/
$commentVote    = (function() {
    var $vote   = $(".js-vote"),
        href    = 'inc/vote_comment.php',

    init        = function() {
        initEvents();
    },

    ajaxHandler = function(elem, commentId) {
        elem.html("...");
        $.ajax({
            type: 'POST',
            url: href,
            data: { commentId : commentId  },
            success: function(data) {
                if(data == 1) {
                   elem.html('<i class="fa fa-star yellow"></i>');
                   elem.offsetParent().parent().siblings().find(".js-vote").html('<i class="fa fa-star dark"></i>');
                } else if(data == 0) {
                    elem.html('<i class="fa fa-star dark"></i>');
                } else if(data == 'error') {
                    alert("Oylama başarısız");
                }
            }
        });
    },

    initEvents  = function() {
        $vote.on('click',this,function(event){
            event.preventDefault();
            thisCommentId = $(this).data("id");
            ajaxHandler($(this), thisCommentId);
        });
    };

    return { init : init };

})();
$commentVote.init();

// like comment
$like       = (function(){
    var $likeBtn    = $(".js-like"),
        href        = "func/like.php",

        init = function () {
            initEvents();
        },

        ajaxHandler = function(elem, itemId, itemType, postId, object) {
            elemHtml = elem.html();
            elem.html("...");
            $.ajax({
                type: 'POST',
                url: href,
                data: { itemId : itemId, itemType : itemType, postId : postId, object : object },
                success: function(data) {
                    if(data == 'no-auth')
                    {
                        alert('Beğenmek için üye girişi yapmanız gerekmektedir');
                        window.location = 'login.php';
                    }
                    else if(data != false) {
                        elem.html(data);
                    } else {
                        elem.html(elemHtml);
                    }
                }
            });
        },

        initEvents  = function() {
            $likeBtn.on('click',this,function(event){
                event.preventDefault();
                thisItemId = $(this).data("id");
                thisItemType = $(this).data("type");
                thisPostId = $(this).data("post");
                thisObject = $(this).data("object");
                ajaxHandler($(this), thisItemId, thisItemType, thisPostId, thisObject);
            });
        };

    return { init : init };
})();
$like.init();

//notification
$notification = (function(){
    var $notifcationAmount      = $(".js-ajaxForNotification"),
        $notificationList       = $(".js-notificationList"),
        $branchNotification     = $(".js-branch-notification"),
        href_amount             = 'inc/notification-amount.php',
        href_list               = 'inc/notification-list.php',
        href_branch_notificaton = 'inc/notification-branch.php',
        increment               = 0,
      
        init                = function() {
            // Prevent ajax queries in login page
            if(!$("body").hasClass("loginPage")) {
                autoAjaxHandler();
                initEvents();
            }
        },

        autoAjaxHandler     = function() {
            $.ajax({
                type: "POST",
                url: href_amount,
                success: function(data) {
                    $notifcationAmount.text(data);
                }   

            });

            $.ajax({
                type: "POST",
                dataType: 'json',
                url: href_branch_notificaton,
                success: function(data) {
                    $(".js-notification-popup-wrapper").prepend(data.html);
                   
                    
                    $(".js-notification-popup").each(function(){
                        increment += 100;
                        
                       $(this).animate({marginLeft: "-10px"});

                       $(this).find("button").on("click",this, function(event){
                            event.preventDefault();
                            closePopup( $(this) );
                       })

                    });
                }   

            });
        },

        listAjaxHandler    = function() {
            $notificationList.html("<p>doluyor!!!...</p>");
            $.ajax({
                type: "POST",
                url: href_list,
                success: function(data) {
                    $notificationList.html(data);
                }   
            });
        },

        closePopup          = function(item) {
            item.parent().stop(true,true)
                .animate({marginLeft: "-100%"},{
                    duration: 200,
                    complete: function() {
                        $(this).remove();
                    }
                });
        },

        //panel display function
        displayPanel = function() {
            $notificationList.stop(true,true).fadeToggle(100);
        },

        initEvents          = function() {

            setInterval(function(){
                autoAjaxHandler();
            },3000);

            $notifcationAmount.click(function(event){
                event.preventDefault();
                displayPanel();
                listAjaxHandler();
            });
            
        };

        return { init : init };

})();
//$notification.init();

// display selected image's name
/*
$('.js-get-upload-value').change(function() {
    var $this       = $(this),
    thisVal = $this.val(),
    thisValLen = thisVal.length;
    $(this).siblings("span").text(thisVal);
    if(thisValLen >= 1) {
        $('.js-get-upload-value').clone().appendTo("body");
    }
    else
    {
        alert(":(");
    }
   
}); 
*/

});

var cloner = function(root) {
    var me = this;
    me.root = $(root);

    var _init = function(element) {
        item = element.find(".js-clone-trigger");
        item.change(function(){
            var $this   = $(this),
            thisVal     = $this.val(),
            thisValLen  = thisVal.length;

            if(thisValLen >= 1) {
                $this.clone(true).appendTo(element);
            } else {
                return false;
            }

        });
    }

    me.root.each(function(){
        new _init( $(this) );
    });

}

$(document).ready(function(){
    //new cloner(".js-clone-body");
});

//tootlip = infotip
$(function(){

$infotip        = (function(){
    // dom elements
    var $infotip    = $(".js-infotip"),

    init            = function(){
        initEvents();
    },

    // infotip(tooltip)
    /*
        @tip = the content will be displayed in infoTipBox
        @posTop = top value of infoTipBox
        @posLeft = left value of infoTipBox
    */
    infoTipBox      = function(tip, posTop, posLeft){
        var infoTipBoxHtml = '<div class="infotipBox"><span class="tip">'+tip+'</span></div>';
        $('body').append(infoTipBoxHtml);
        $(".infotipBox").css({
            position: 'absolute',
            top: posTop + 40,
            left: posLeft,
            zIndex: '999999'
        })
        .stop(true, true)
        .animate({
            top: posTop + 50,
        },{
            easing: 'linear',
            duration: 100
        });
    },

    // remove infotip
    removeTipBox = function() {
        $(".infotipBox").remove();
    },

    initEvents      = function(){
        $infotip.hover(function(event){
            var tip = $(this).data("tip"),
            posTop  = $(this).offset().top;
            posLeft = $(this).offset().left;
            infoTipBox(tip, posTop, posLeft);

        }, function(){
            removeTipBox();
        });
    };

    return { init :  init };



})();
$infotip.init();
});

$(function(){

$lightboxModule          = (function(){

    //dom elements
    var $body           = $("body"),
    $win                = $(window),
    $trigger            = $(".js-lighbox-trigger"),
    activeTriggerGroup  = "active-trigger-group",
    $overlay            = $(".js-lightbox-overlay"),
    $lightbox           = $(".js-lightbox"),
    $close              = $lightbox.find(".lightboxClose"),
    $leftArr            = $lightbox.find(".lightboxLeftArrow"),
    $rightArr           = $lightbox.find(".lightboxRightArrow"),
    $content            = $lightbox.find(".lightboxContent"),

    init                = function(){
        initEvents();
    },

    displayLightBox     = function(content) {
        $overlay.fadeIn();
        $lightbox.fadeIn();
        img  = '<img class="lbImage" src="'+content+'" />';

        $content.html(img);
        $content.find(".lbImage").load(function(){
            imgWidth    = ( $(this).width() >= 900 ? 900 : $(this).width() );
            imgHeight   = ( $(this).height() >= 600 ? 600 : $(this).height() );
            
            $(this).css({width: imgWidth+'px', height: imgHeight+'px'});
            $lightbox.css({width: imgWidth+'px', height: imgHeight+'px'});
        });
    },

    closeLightBox       = function() {

        $(".i-am-active").removeClass("i-am-active");
        $("."+activeTriggerGroup).removeClass(activeTriggerGroup);

        $overlay.fadeOut(0);
        $lightbox.fadeOut(0).css({width: '10%',height: '10%'});
        $content.html(" ");
    },

    rightMove           = function() {
        $next = $("."+activeTriggerGroup);
        nextLength = $next.length;

        $iAmActive = $(".i-am-active");


        if($iAmActive.index() < nextLength - 1) {
            $iAmActive.removeClass("i-am-active").next().addClass("i-am-active");

            $iAmActive = $(".i-am-active");
            activeHref = $iAmActive.attr("href");
            displayLightBox(activeHref);
        }
    },

    leftMove           = function() {
        $next = $("."+activeTriggerGroup);
        nextLength = $next.length;

        $iAmActive = $(".i-am-active");


        if($iAmActive.index() >= 1) {
            $iAmActive.removeClass("i-am-active").prev().addClass("i-am-active");

            $iAmActive = $(".i-am-active");
            activeHref = $iAmActive.attr("href");
            displayLightBox(activeHref);
        }
    },

    initEvents          = function(){
        $trigger.click(function(event){
            event.preventDefault();
            thisHref           = $(this).attr("href");
            // if window width more than 500px execeute the function
            if($win.width() > 500) {
                displayLightBox(thisHref);
            }
            $(this).addClass(activeTriggerGroup);
            $(this).addClass("i-am-active");
            $(this).siblings(".js-lighbox-trigger").addClass(activeTriggerGroup);

        });

        $close.click(function(event){
            event.preventDefault();
            closeLightBox();
        });

        $overlay.click(function(event){
            event.preventDefault();
            closeLightBox();
        });

        $rightArr.click(function(){
            rightMove();
        });

        $leftArr.click(function(){
            leftMove();
        });

        $(document).keyup(function(e) {
            //press esc
            if (e.keyCode == 27) { closeLightBox(); }

            //press right arrow
            if (e.keyCode == 39) { rightMove(); }

            //press left arrow
            if (e.keyCode == 37) { leftMove(); }
        });
    };
    return { init : init };

})();
$lightboxModule.init();
});

// display hidden elements
var displayHiddenElem = function(root) {
    var me = this;
    me.root = $(root);

    var _init = function(element) {

        element.click(function(event){
            event.preventDefault();
            $(this).toggleClass("active");
            
            if( $(this).hasClass("active") ) {
                thisData    = $(this).data("show");
                item        = $(this).siblings("."+thisData);
                $(item).stop(true,true).show();
            } else {
                thisData    = $(this).data("show");
                item        = $(this).siblings("."+thisData);
                $(item).stop(true,true).hide();
            }
        });
    }

    me.root.each(function(){
        new _init( $(this) );
    });

}
$(document).ready(function(){
    new displayHiddenElem(".js-display-item");
});

// display archive files' name
// if move arcihve is selected
var displayHiddenOption;
displayHiddenOption = function (root) {
    var me = this;
    me.root = $(root);

    var _init = function (element) {
        var target = $(".js-hidden-option");
        target.css({display: 'none'});
        element.change(function (event) {
            var selectedOption = $(this).find('option:selected').val();
            if (selectedOption == "move_archive") {
                target.css({display: 'block'});
            } else {
                target.css({display: 'none'});
            }
        });
    }

    me.root.each(function () {
        new _init($(this));
    });

};
$(document).ready(function(){
    new displayHiddenOption(".js-display-option");
});


$(document).ready(function(){
    $( '.swipebox' ).swipebox();
});


// display hidden elements
var displayPreloader = function(root) {
    var me = this;
    me.root = $(root);

    var _init = function(element) {

        element.click(function(event){

           element.html( '<img src="img/preloader.gif" />');

        });
    }

    me.root.each(function(){
        new _init( $(this) );
    });

}
$(document).ready(function(){
    new displayPreloader(".js-preloader-trigger");
});

$(function(){
// scroll and fetch more products
var $scrollAjax = (function() {

    var $document       = $(document),
        href            = 'func/scroll-ajax.php',
        $resultContainer = $('.js-scroll-result'),

    //initialize function
    init        = function() 
    {
        initEvents();
    },

    scrollEvent  = function()
    {

        $document.scroll(function(){
            if($(window).scrollTop() + $(window).height() == $(document).height())
            {
                setTimeout(function(){ 
                   ajaxHandler();
                },300 );
            }
            else
            {
                return false;
            }
        });
    },

    ajaxHandler = function()
    {
        offset = $resultContainer.data('offset'),
        category = $resultContainer.data('category'),
        $.ajax({
            type: 'POST',
            url: href,
            dataType: 'json',
            data: { offset : offset, category : category},
            cache: false,
            success: function(data) {
                
                $resultContainer.append(data.html);
                $resultContainer.data({'offset': data.offset});
                $( '.swipebox' ).swipebox();
            },
            error: function()
            {
                return false;
            }
        });
        
    },

    initEvents  = function() 
    {
        scrollEvent();
    };

    return { init : init };

}) ();
$scrollAjax.init();
});

$(function(){
// search box - search with ajax
var $scrollAjax = (function() {

    var $input          = $('.js-search-box'),
        $target         = $('.js-search-result'),
        href            = 'func/search-ajax.php',

    //initialize function
    init        = function() 
    {
        initEvents();
    },


    ajaxHandler = function(item)
    {
        setTimeout(function(){
            var searchItem = item;
            $.ajax({
                type: 'POST',
                url: href,
                dataType: 'json',
                data: { searchItem : searchItem},
                cache: false,
                success: function(data) {
                    $target.html(data.html);
                },
                error: function()
                {
                    return false;
                }
            });
        }, 300);
        
    },

    closeSearchResult = function()
    {
        $target.find('li')
                .delay(300)
                .fadeOut(0,function(){
                    $(this).find('li').remove();
                });
    }

    initEvents  = function() 
    {
        $input.on('keyup', this, function(){
            var thisVal = $(this).val();
            ajaxHandler(thisVal);
        });

        $input.focusout(function(){
            closeSearchResult();
        });
    };

    return { init : init };

}) ();
$scrollAjax.init();
});