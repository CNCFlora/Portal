var equipe = function() {
    var membros = $(".membros");

    var size = 580;
    var width = $(".membro",membros).length * size;
    membros.parent().css("overflow",'hidden');
    membros.css("width", width );
    membros.css("position","relative");
    membros.css("left",0);

    var left = $("<div class='left'></div>");
    var right = $("<div class='right'></div>");

    membros.parent().prepend(left);
    membros.parent().append(right);

    left.on('click',function(){
        var left = parseInt(membros.css("left").replace("px",""));
        var to = left + size ;
        console.log(to);
        if(to >= 20) to = 0;
        console.log(to);
        membros.stop(true,true).animate({ left: to });
    });

    right.on('click',function(){
        var left = parseInt(membros.css("left").replace("px",""));
        var to = left - size - 20;
        console.log(to);
        if(to <= width * -1) to = left - 20 ;
        console.log(to);
        membros.stop(true,true).animate({ left: to + 20});
    });
};
