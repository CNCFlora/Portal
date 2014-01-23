var intro = function() {
    $("#slides>li").bind("mouseenter",function() {
        var li = $(this); 
        if(li.hasClass("active")) return;
        $("#slides>li").stop();
        $("#slides>li").addClass("notactive").removeClass("active");
        li.removeClass("notactive").addClass("active");
        $(".notactive").animate({width: 100});
        $(".active").animate({width: 600});
    });
    $("#slides>li:first-child").trigger('mouseenter');
    $("#slides>li>a").each(function(i,e){
        var a = $(e);
        a.append("<span>"+$("img",a).attr('alt')+"</span>");
    });
};
