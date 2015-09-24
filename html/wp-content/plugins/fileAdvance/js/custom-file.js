(function() {
	var customFile = {
		preventFile : _preventFile
	};
	function _preventFile(fileId){
		jQuery.ajax({
		    url: ajax_object.ajaxurl, // this is the object instantiated in wp_localize_script function
		    type: 'POST',
		    data:{
		      action: 'myaction',
		      id: fileId, // this is the function in your functions.php that will be triggered
		    },
		    success: function( data ){
		      //Do something with the result from server
		      var labelId = "#custom_url_" + fileId;
		      jQuery(labelId).text(data.url);
		    }
		  });
	}
	window.customFile = customFile;
})(window);
