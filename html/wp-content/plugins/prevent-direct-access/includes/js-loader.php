<?php

class Pda_JS_Loader {
	public static function admin_load_js() {
		wp_register_script( 'ajaxHandle', plugins_url( '../js/custom-file.js', __FILE__ ), array( 'jquery' ) );
		wp_enqueue_script( 'ajaxHandle' );
		wp_localize_script( 'ajaxHandle', 'ajax_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	}
}
?>
