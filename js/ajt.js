function tga_mgdoit(imgurl,ajaxurl) {
	var spitm = "tga_export";
	var spdurl = "tafwp_ajax_export";
	var sperinfo ="";
	
	jQuery("span#"+ spitm).fadeOut('fast', function() {
		var loading = '<img border="0" alt="" src="' + imgurl + '" align="absbottom" /> If there are large numbers of posts to export, it will take some time to complete, please wait... ';
		jQuery("span#"+ spitm).fadeIn('fast', function() {
			jQuery("input#mgdhdex").val("on");
			var ame_sack = new sack(ajaxurl);
			ame_sack.execute = 1;
			ame_sack.method = 'POST';
			ame_sack.setVar( "action", spdurl );
			
			ame_sack.onError = function() { alert(sperinfo); };
			ame_sack.runAJAX();
			setTimeout("tga_exqr('"+ajaxurl+"')",100);
			
		});
		jQuery("span#"+ spitm).html( loading );
	});
}

function tga_exqr(aqurl) {//传输状态查询
	if( jQuery("input#mgdhdex").val()=='on'){
		jQuery.ajax({
			  type: "GET",
			  url: aqurl,
			  data: "action=tafwp_ajax_export_status",
			  success: function(msg){
			  	 jQuery("#mgdbgl").css('width',msg+'%');
			  	 jQuery("#mgdtxt").text(msg+'%');
			  	 setTimeout("tga_exqr('"+aqurl+"')",1000);
			  }
		});
 }

}