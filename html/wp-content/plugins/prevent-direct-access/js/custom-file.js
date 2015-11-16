(function() {
	var customFile = {
		preventFile : _preventFile,
		copyToClipboard: _copyToClipboard
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
		      is_prevented: isPrevented,
					security_check: jQuery(checkBoxId).attr('nonce')
		    },
		    success: function( data ){
		      //Do something with the result from server
		      if(typeof data.error !== 'undefined') {
		      	jQuery(checkBoxId).prop('checked', false);
		      	alert(data.error);
		      } else if (data == 'invalid_nonce') {
		      	alert('No! No! No! Verify Nonce Fails!');
		      	if (jQuery(checkBoxId).is(':checked')) {
		      		jQuery(checkBoxId).prop('checked', false);
		      	} else {
		      		jQuery(checkBoxId).prop('checked', true);
		      	}
		      } else {
		      	var labelId = "#custom_url_" + data.post_id;
		      	var btnCopyId = '#btn_copy_' + data.post_id;
		      	var divCustomUrlId = '#custom_url_div_' + data.post_id;
		      	var custom_url_class = '.custom_url_' + data.post_id;
		      	if(data.is_prevented === "1"){
		      		jQuery(custom_url_class).fadeIn();
		      		jQuery(labelId).val(data.url);
		      	} else {
		      		jQuery(custom_url_class).fadeOut();
		      	}
		      }
		    },
		    error: function (error) {
		    	console.log("Errors", error);
		    	alert(error.responseText);
		    }
		  });
	}

	window.customFile = customFile;

	function _copyToClipboard(btn, txt_input) {
  		var $temp = jQuery("<input>");
  		jQuery("body").append($temp);
  		$temp.val(jQuery(txt_input).val()).select();
  		document.execCommand("copy");
  		$temp.remove();

  		jQuery(btn).text("URL Copied");
		setTimeout(function() {
		 	jQuery(btn).text("Copy URL");
		}, 5000);
	}

})(window);
