<?php
/*
Plugin Name: File Advance
Plugin URI: https://github.com/gaupoit/wpp
Description: A hello world plugin used to demonstrate the process of creating plugins.
Version: 1.0
Author: HTH 
Author URI: https://github.com/gaupoit/wpp
License: GPL
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
add_filter("manage_upload_columns", 'upload_columns');
add_action("manage_media_custom_column", 'media_custom_columns', 0, 2);

function upload_columns($columns) {

	// unset($columns['parent']);
	$columns['direct_access'] = "Prevent Direct Access";

	return $columns;

}
function media_custom_columns($column_name, $id) {

	$post = get_post($id);

	if($column_name != 'direct_access')
		return;
		?>
		<input onclick="customFile.preventFile('<?php echo $post->ID ?>')" type="checkbox"/><?php _e('Prevent direct access'); ?>
		<!-- <a class="hide-if-no-js" onclick="findPosts.open('media[]','<?php echo $post->ID ?>');return false;" href="#the-list"><?php _e('Prevent direct access for this file'); ?></a> -->
		<?php
}

function admin_load_js(){
    wp_register_script( 'ajaxHandle', plugins_url( '/js/custom-file.js', __FILE__ ), array('jquery') );
    wp_enqueue_script( 'ajaxHandle' );
    wp_localize_script( 'ajaxHandle', 'ajax_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
}
add_action('admin_enqueue_scripts', 'admin_load_js');

add_action( 'wp_ajax_myaction', 'so_wp_ajax_function' );
add_action( 'wp_ajax_nopriv_myaction', 'so_wp_ajax_function' );
function so_wp_ajax_function(){
  //DO whatever you want with data posted
  //To send back a response you have to echo the result!
  echo $_POST['id'];
  wp_die(); // ajax call must die to avoid trailing 0 in your response
}

?>