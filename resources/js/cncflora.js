if (typeof String.prototype.endsWith !== 'function') {
    String.prototype.endsWith = function(suffix) {
        return this.indexOf(suffix, this.length - suffix.length) !== -1;
    };
}
head.js('/portal/resources/js/jquery.min.js')
    .js('/portal/resources/js/bootstrap.min.js')
    .js('/portal/resources/js/floral.js')
    .js('/portal/resources/js/jquery.pjax.js')
    .ready(function(){
        floral();
        $(document).pjax('nav a', '#content');

        var makeClosable = function() {
            $("span.close").remove();
            $(".intro").append("<span class='close'>[ x ]</span>");
            $(".close").click(function(){
                if($(".intro").hasClass("minify")) {
                    $(".intro").removeClass("minify");
                    $(this).html('[ x ]');
                } else {
                    $(".intro").addClass("minify");
                    $(this).html('[ + ]');
                }
            });
        };

        makeClosable();

        $("#content").on('pjax:end',function(){
            var path = location.pathname;
            var section = path.substring(1,path.lastIndexOf('/')).replace(/\//g,'-');
            var page = path.substring(path.lastIndexOf('/') + 1);
            var classes = $("html").attr("class").split(' ');
            for(var i in classes){
                if(classes[i].endsWith("-section")) {
                    $("html").removeClass(classes[i]);
                }
            }
            $("html").addClass(section+"-section");
            $("html").attr("id",page+"-page");
            makeClosable();
        });
    });
