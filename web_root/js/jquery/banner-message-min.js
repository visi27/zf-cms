(function (jQuery) {
    var settings = {
        speed: 700,
        message: 'No message is specified',
        color: 'default',
        noMarker: false
    };
    jQuery.fn.showRibbonMessage = function (options) {
        if (options) {
            $.extend(settings, options)
        }
        if (isShowingMessage()) {
            hideMessage(true);
            return this
        } else if (isShowingMarker()) {
            hideMarker(true);
            return this
        }
        buildMessage();
        var element = jQuery(".notify");
        element.animate({
            "top": "0px"
        }, settings.speed, 'easeOutBounce');
        jQuery("body").animate({
            "padding-top": "40px"
        }, settings.speed, 'easeOutBounce');
        return this
    };
    jQuery.fn.showRibbonMarker = function (options) {
        if (options) {
            $.extend(settings, options)
        }
        if (isShowingMessage()) {
            disposeMessage(onFinishHidingMessage);
            return this
        }
        if (isShowingMarker()) {
            disposeMarker(onFinishHidingMessage);
            return this
        }
        onFinishHidingMessage();
        return this
    };
    jQuery.fn.hideRibbonMessage = function () {
        if (isShowingMessage()) {
            hideMessage()
        }
        return this
    };
    jQuery.fn.toggleRibbonMessage = function (delta) {
        if (!delta) {
            var delta = jQuery(".notify").length == 1 ? 0 : 1
        }
        if (delta == 1) hideMarker(false);
        else hideMessage(false)
    };
    jQuery.fn.disposeRibbonMessage = function () {
        if (isShowingMessage()) {
            disposeMessage()
        } else if (isShowingMarker()) {
            disposeMarker()
        }
    };

    function isShowingMessage() {
        return jQuery(".notify").length == 1
    };

    function isShowingMarker() {
        return jQuery(".notify-marker").length == 1
    };

    function hideMessage(explicit) {
        disposeMessage(function () {
            if (explicit) {
                jQuery().showRibbonMessage();
                return
            }
            onFinishHidingMessage()
        })
    };

    function hideMarker(explicit) {
        disposeMarker(function () {
            if (explicit) {
                jQuery().showRibbonMessage();
                return
            }
            onFinishHidingBookmark()
        })
    };

    function disposeMessage(callback) {
        var onFinish = function () {
            jQuery(".notify").detach();
            if (callback) callback()
        };
        var element = $(".notify");
        element.animate({
            "top": "-" + element.css("height")
        }, settings.speed, 'easeOutBounce', onFinish);
        jQuery("body").animate({
            "padding-top": "0px"
        }, settings.speed, 'easeOutBounce')
    }
    function disposeMarker(callback) {
        var onFinish = function () {
            jQuery(".notify-marker").detach();
            if (callback) callback()
        };
        var element = $(".notify-marker .messagespace a");
        element.animate({
            "top": "-" + element.css("height")
        }, settings.speed, 'easeOutBounce', onFinish)
    }
    function onFinishHidingMessage() {
        jQuery(".notify").detach();
        if (settings.noMarker) return;
        buildBookmark();
        $(".notify-marker .messagespace").css("display", "block");
        $(".notify-marker .messagespace a").css("display", "block").animate({
            "top": "0"
        }, settings.speed, 'easeOutBounce')
    };

    function onFinishHidingBookmark() {
        jQuery(".notify-marker").detach();
        jQuery().showRibbonMessage()
    };

    function buildMessage() {
        var html = jQuery("<div class='notify'><div class='messagespace'></div></div>");
        var message = jQuery("<p></p>").html(settings.message);
        var messagespace = html.find(".messagespace").append(message).append("<div class='hide'><a href='' class='' onclick='jQuery().toggleRibbonMessage(); return false;'></a></div>");
        if ($.browser.msie && $.browser.version == "6.0") html.addClass("notify-ie6");
        if (settings.color != "default") html.addClass("notify-" + settings.color);
        jQuery("body").prepend(html)
    };

    function buildBookmark() {
        html = jQuery("<div class='notify-marker'><div class='messagespace'></div></div>");
        marker = jQuery("<a href='' class='' onclick='jQuery().toggleRibbonMessage(); return false;'></a>");
        messagespace = html.find(".messagespace").append(marker);
        if (settings.color != "default") marker.addClass(settings.color);
        jQuery("body").prepend(html)
    }
})(jQuery);