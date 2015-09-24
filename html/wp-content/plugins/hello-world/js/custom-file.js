(function() {
	var customFile = {
		preventFile : _preventFile
	};
	function _preventFile(id){
		alert(id);
	}
	window.customFile = customFile;
})(window);
