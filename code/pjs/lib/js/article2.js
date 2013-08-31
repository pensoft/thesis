var showArticleInfo = false;
function toogleArticleInfo(){
	showArticleInfo = !showArticleInfo;
	if (showArticleInfo) {
		$('.P-Current-Author-Addresses').show();
		$('.P-Article-Preview-Addresses').show();
		$('.P-Article-Preview-Base-Info-Block').show();
		$('#arrow').attr("src", 'http://pwt.pensoft.net/i/arrow-up-icon.png');
	} else {
		$('#arrow').attr("src", 'http://pwt.pensoft.net/i/arrow-down-icon.png');
		$('.P-Current-Author-Addresses').hide();
		$('.P-Article-Preview-Addresses').hide();
		$('.P-Article-Preview-Base-Info-Block').hide();
	}
}