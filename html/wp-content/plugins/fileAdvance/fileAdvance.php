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

include 'includes/fileAdvanceDB.php';
include 'includes/javascriptLoader.php';
include 'includes/helper.php';
include 'includes/advanceFileRepository.php';

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
add_filter("manage_upload_columns", 'upload_columns');
add_action("manage_media_custom_column", 'media_custom_columns', 0, 2);
add_action('admin_enqueue_scripts', 'admin_load_js');
add_action( 'wp_ajax_myaction', 'so_wp_ajax_function' );
register_activation_hook(__FILE__, 'jal_install');

/**
* Require plugin configuration
*/
//require_once dirname(__FILE__) . '/includes/define.php';

require_once dirname(__FILE__) . '/includes/function.php';

// TEMPORARY THIS HOOK NOT WORK
//add_filter('mod_rewrite_rules', 'fa_htaccess_contents');
function fa_htaccess_contents( $rules ) {
    $my_content = <<<EOD
    # BEGIN WordPress
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase /
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]
</IfModule>
# END WordPress\n
EOD;
	$rules = $my_content . $rules;
	global $wp_rewrite;
	$wp_rewrite->flush_rules();
    return $rules;
}

function upload_columns($columns) {

	$columns['direct_access'] = "Prevent Direct Access";

	return $columns;

}
function media_custom_columns($column_name, $id) {

	$post = get_post($id);	
	$advance_file = get_advance_file_by_post_id($post->ID);
	$checked = isset($advance_file) && $advance_file->is_prevented;
	$url = isset($advance_file) && $checked ? site_url() . '/' . $advance_file->url : '';
	if($column_name != 'direct_access')
		return;
		?>
		<input id="ckb_<?php echo $post->ID ?>" <?php if($checked) echo 'checked="checked"';?> onclick="customFile.preventFile('<?php echo $post->ID ?>')" type="checkbox"/><?php _e('Prevent direct access'); ?>
		<div><label id="custom_url_<?php echo $post->ID ?>"><?php echo $url ?></label></div>	
		<?php
}
function so_wp_ajax_function(){
  //DO whatever you want with data posted
  //To send back a response you have to echo the result!
  $post_id = $_POST['id'];
  $is_prevented = $_POST['is_prevented'];
  $file_info = array(
  			'time' => current_time( 'mysql' ), 
  			'post_id'		=> $post_id,
  			'is_prevented' => $is_prevented,
  			'url'       => generate_unique_string()
  	);
  $result = create_advance_file($file_info);
  if($result < 1 || $result === false) {
  	$file_result = array(
  	  		'error' => true,
  	  		'message'=> "Cannot create file advance's info"
  		);
  } else {
  	$file_result = get_advance_file_by_post_id($file_info['post_id']);
  	$file_result->url = site_url() . '/' . $file_result->url;
		
		// TODO: better extract to method
		$post = get_post($_POST['id']);
		$file_url = $post->guid;
		$redirect_url_rule = fa_generate_prevent_rule(site_url(), $file_url);
		
		// write new rule $redirect_url_rule to .htaccess file
  	if ($file_result->is_prevented == "1") {  					
		WPHE_WriteNewHtaccess($redirect_url_rule);			
  	} else {
  		WPHE_RemoveHtaccess($redirect_url_rule);
	}
  }
  wp_send_json($file_result);
  wp_die(); // ajax call must die to avoid trailing 0 in your response
}

?>