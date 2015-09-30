<?php

include 'includes/fileAdvanceDB.php';
include 'includes/javascriptLoader.php';
include 'includes/helper.php';
include 'includes/advanceFileRepository.php';

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

//defined('FILE_ADVANCE_DIR') || define('FILE_ADVANCE_DIR', realpath(dirname(__FILE__) . '/..'));
//define('FILE_ADVANCE_FILE', 'fileAdvance/fileAdvance.php');
//define('FILE_ADVANCE_INC_DIR', W3TC_DIR . '/includes');

//defined('WP_CONTENT_DIR') || define('WP_CONTENT_DIR', realpath(FILE_ADVANCE_DIR . '/../..'));

add_filter("manage_upload_columns", 'upload_columns');
add_action("manage_media_custom_column", 'media_custom_columns', 0, 2);
add_action('admin_enqueue_scripts', 'admin_load_js');
add_action( 'wp_ajax_myaction', 'so_wp_ajax_function' );
register_activation_hook(__FILE__, 'jal_install');

function jal_install() {
	$is_valid_activate = fa_htaccess_writable();
	if ($is_valid_activate != true) {
		wp_die($is_valid_activate);
	}
}

/**
* Require plugin configuration
*/
//require_once dirname(__FILE__) . '/includes/define.php';

require_once dirname(__FILE__) . '/includes/function.php';
//require_once dirname(__FILE__) . '/download.php';

// TEMPORARY THIS HOOK NOT WORK
add_filter('mod_rewrite_rules', 'fa_htaccess_contents');
function fa_htaccess_contents( $rules ) {
	// Temporary comment
	/*
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
	*/
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
	$url = isset($advance_file) && $checked ? site_url() . '/private/' . $advance_file->url : '';
	if($column_name != 'direct_access')
		return;
		?>
		<input id="ckb_<?php echo $post->ID ?>" <?php if($checked) echo 'checked="checked"';?> onclick="customFile.preventFile('<?php echo $post->ID ?>')" type="checkbox"/><?php _e('Prevent direct access'); ?>
		<div><a id="custom_url_<?php echo $post->ID ?>" href="<?php echo $url ?>"><?php echo $url ?></a></div>	
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
  	$generated_file_code = $file_result->url;
  	$file_result->url = site_url() . '/private/' . $file_result->url;
		
		// TODO: better extract to method
		$post = get_post($_POST['id']);
		$file_url = $post->guid;
		$redirect_download_rule = fa_generate_redirect_download_page($generated_file_code);
		$redirect_prevent_rule = fa_generate_prevent_rule(site_url(), $file_url);
		
		// write new rule $redirect_url_rule to .htaccess file
  	if ($file_result->is_prevented == "1") {  					
		WPHE_WriteNewHtaccess($redirect_download_rule, $redirect_prevent_rule);			
  	} else {
  		WPHE_RemoveHtaccess($redirect_download_rule, $redirect_prevent_rule);
	}
  }
  wp_send_json($file_result);
  wp_die(); // ajax call must die to avoid trailing 0 in your response
}

register_uninstall_hook(    __FILE__, 'WCM_Setup_Demo_on_uninstall' );
function WCM_Setup_Demo_on_uninstall()
{
    if ( ! current_user_can( 'activate_plugins' ) )
        return;
    check_admin_referer( 'bulk-plugins' );

    // Important: Check if the file is the one
    // that was registered during the uninstall hook.
    if ( __FILE__ != WP_UNINSTALL_PLUGIN )
        return;

    # Uncomment the following line to see the function in action
    exit( var_dump( $_GET ) );
}
/**
 * Check for hook
 */
// if ( function_exists('register_uninstall_hook') )
register_uninstall_hook(__FILE__, 'uninstall');


 /**
 * Delete options in database
 */
function uninstall() {
  global $wpdb;
  $table_name = $wpdb->prefix . 'advancefiles';
  $wpdb->query("DROP TABLE IF EXISTS $table_name");
}


?>