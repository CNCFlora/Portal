
function floral() {
    var dback = $("#floral").css('background','url("/static/img/floral/04.jpg") no-repeat');
	var pics = ['00','01','02','03','04'];
	function next() {
        if($("html").hasClass('lt-768')) {
            setTimeout(next,1000);
            return ;
        }
		var actual = pics.reverse().pop();
		var bg = "url('/static/img/floral/"+actual+".jpg') no-repeat scroll";
		dback.fadeOut(3000,function() {
			dback.css('background',bg).fadeIn(3000,function(){
				pics.reverse().push(actual);
				setTimeout(next,1000);
			});
		});
	}
	setTimeout(next,2000);
}
