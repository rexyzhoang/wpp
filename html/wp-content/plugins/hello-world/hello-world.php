<?php
/*
Plugin Name: Hello-World
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
    wp_enqueue_script( 'custom_js', plugins_url( '/js/custom-file.js', __FILE__ ), array('jquery') );
}
add_action('admin_enqueue_scripts', 'admin_load_js');

?>