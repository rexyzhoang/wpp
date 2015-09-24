(function() {
	var customFile = {
		preventFile : _preventFile
	};
	function _preventFile(fileId){
		var checkBoxId = "#ckb_" + fileId;
		var isPrevented = jQuery(checkBoxId).is(':checked') ? 1 : 0;

		jQuery.ajax({
		    url: ajax_object.ajaxurl, // this is the object instantiated in wp_localize_script function
		    type: 'POST',
		    data:{
		      action: 'myaction',
		      id: fileId, // this is the function in your functions.php that will be triggered
		      is_prevented: isPrevented
		    },
		    success: function( data ){
		      //Do something with the result from server
		      if(typeof data.error !== 'undefined' && data.error === true) {
		      	alert(data.message);
		      } else {
		      	var labelId = "#custom_url_" + data.post_id;
		      	if(data.is_prevented === "1"){
		      		jQuery(labelId).show();
		      		jQuery(labelId).text(data.url);	
		      	} else {
		      		jQuery(labelId).hide();
		      	}
		      }
		      
		    }
		  });
	}
	window.customFile = customFile;
})(window);
