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
	$advance_file = get_advance_file_by_post_id($post->ID);
	$checked = isset($advance_file) && $advance_file->is_prevented;
	$url = isset($advance_file) ? $advance_file->url : '';
	if($column_name != 'direct_access')
		return;
		?>
		<input <?php if($checked) echo 'checked="checked"';?> onclick="customFile.preventFile('<?php echo $post->ID ?>')" type="checkbox"/><?php _e('Prevent direct access'); ?>
		<div>Url: <label id="custom_url_<?php echo $post->ID ?>"><?php echo site_url() . '/' . $url ?></label></div>	
		<?php
}

function admin_load_js(){
    wp_register_script( 'ajaxHandle', plugins_url( '/js/custom-file.js', __FILE__ ), array('jquery') );
    wp_enqueue_script( 'ajaxHandle' );
    wp_localize_script( 'ajaxHandle', 'ajax_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
}
add_action('admin_enqueue_scripts', 'admin_load_js');

add_action( 'wp_ajax_myaction', 'so_wp_ajax_function' );
function so_wp_ajax_function(){
  //DO whatever you want with data posted
  //To send back a response you have to echo the result!
  $post_id = $_POST['id'];
  $file_info = array(
  			'time' => current_time( 'mysql' ), 
  			'post_id'		=> $post_id,
  			'url'       => generate_random_string()
  	);
  $result = create_advance_file($file_info);
  if($result < 0 || $result === false) {
  	$file_info = array(
  	  		'error' => true,
  	  		'message'=> "Cannot create file advance's info"
  		);
  } 
  wp_send_json($file_info);
  wp_die(); // ajax call must die to avoid trailing 0 in your response
}

function generate_random_string($length = 10) {
    return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
}

global $jal_db_version;
$jal_db_version = '1.0';

function jal_install() {
	global $wpdb;
	global $jal_db_version;

	$wpdb->show_errors();

	$table_name = $wpdb->prefix . 'advancefiles';
	
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		ID mediumint(9) NOT NULL AUTO_INCREMENT,
		post_id mediumint(9) NOT NULL,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		url varchar(55) DEFAULT '' NOT NULL,
		is_prevented tinyint(1) DEFAULT 1,
		UNIQUE KEY id (id)
	) $charset_collate;";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);

	add_option('jal_db_version', $jal_db_version);
}

function create_advance_file($file_info) {
	global $wpdb;	
	$post_id = $file_info['post_id'];
	$post = get_post_by_id($post_id);
	$result = false;
	if(isset($post)) {
		$file_advance = get_advance_file_by_post_id($post_id);
		if(!isset($file_advance)) {
			$table_name = $wpdb->prefix . 'advancefiles';
			$result = $wpdb->insert( 
				$table_name, 
				$file_info 
			);	
		}
	} 
	return $result;
}

function get_post_by_id($post_id) {
	$post = get_post($post_id);
	return $post;
}

function get_advance_file_by_post_id($post_id) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'advancefiles';
	$advance_file = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE post_id = $post_id", ARRAY_A) );
	return $advance_file; 

}

register_activation_hook(__FILE__, 'jal_install');

?>