<?php

/*
Plugin Name: Prevent Direct Access
Plugin URI: https://github.com/gaupoit/wpp
Description: A simple way to prevent search engines and the public from indexing and accessing your file without user authentication.
Version: 1.0
Author: HTH
Author URI: https://github.com/gaupoit/wpp
License: GPL
*/
if ( !defined( 'ABSPATH' ) ) exit;
include 'includes/class-repository.php';
include 'includes/javascript-loader.php';
include 'includes/helper.php';
include 'includes/db-init.php';
require_once dirname( __FILE__ ) . '/includes/function.php';

add_filter( "manage_upload_columns", 'upload_columns' );
add_action( "manage_media_custom_column", 'media_custom_columns', 0, 2 );
add_action( 'admin_enqueue_scripts', 'admin_load_js' );
add_action( 'wp_ajax_myaction', 'so_wp_ajax_function' );
add_action( 'delete_post', 'delete_prevent_direct_access' );
add_action('admin_notices', 'pda_admin_notices');

register_activation_hook( __FILE__, 'jal_install' );
register_deactivation_hook( __FILE__, 'deactivate' );
register_uninstall_hook( __FILE__, 'wcm_setup_demo_on_uninstall' );
register_uninstall_hook( __FILE__, 'uninstall' );
add_filter( 'mod_rewrite_rules', 'fa_htaccess_contents' );

function pda_admin_notices() {
    global $pagenow;

    if ( $pagenow == 'plugins.php' || $pagenow == 'upload.php') {
        $activation_failed_messages = fa_htaccess_writable();
        error_log( $activation_failed_messages, 0 );

        $plugin = plugin_basename(__FILE__);
        if ( $activation_failed_messages !== true && is_plugin_active($plugin)) {
            // deactivate_plugins( basename( __FILE__ ) );
            
            ?>
            <div class="error is-dismissible notice">
              <p><b><?php echo "Prevent Direct Access: "; ?></b> If your <b>.htaccess</b> file were writable, we could do this automatically, but it isn’t. So please make it writable or alternatively, you can manually update your .htaccess with the mod_rewrite rules found under <b>Settings >> Permanlinks</b>. Until then, the plugin can't work yet. </p>
            </div>
            <?php
        }   
    }

}

function fa_htaccess_contents( $rules ) {
    $newRule = "RewriteRule private/([a-zA-Z0-9]+)$ wp-content/plugins/prevent-direct-access/download.php?download_file=$1 [R=301,L]" . PHP_EOL;
    $newRule .= "RewriteCond %{REQUEST_FILENAME} -s" . PHP_EOL;
    $newRule .= "RewriteRule ^wp-content/uploads(/[a-zA-Z_\-\s0-9\.]+)+\.([a-zA-Z0-9]+)$ wp-content/plugins/prevent-direct-access/download.php?is_direct_access=true&download_file=$1&file_type=$2 [QSA,L]" . PHP_EOL;
    return $newRule . $rules . "Options -Indexes" . PHP_EOL;
}

function upload_columns( $columns ) {

    $columns['direct_access'] = "Prevent Direct Access";

    return $columns;
}

function media_custom_columns( $column_name, $id ) {
    $repository = new Repository;
    $post = get_post( $id );
    $advance_file = $repository->get_advance_file_by_post_id( $post->ID );
    $checked = isset( $advance_file ) && $advance_file->is_prevented;
    $url = isset( $advance_file ) && $checked ? site_url() . '/private/' . $advance_file->url : '';
    if ( $column_name != 'direct_access' ) return;
?>
     <input id="ckb_<?php
    echo $post->ID
    ?>" <?php
    if ( $checked ) echo 'checked="checked"'; ?> onclick="customFile.preventFile('<?php
    echo $post->ID
    ?>')" type="checkbox"/><?php
    _e( 'Prevent direct access' ); ?>
     <div class="custom_url_<?php
    echo $post->ID
    ?>" style="<?php
    if ( !$checked ) echo 'display: none;' ?>">
     <div>Access your file via this link:</div>
     <div>
     <input type="text" id="custom_url_<?php
        echo $post->ID
        ?>" value="<?php
        echo $url
        ?>" style="width: 80%"></div>
     <button id="btn_copy" type="button" onclick="customFile.copyToClipboard(this, '#custom_url_<?php
        echo $post->ID
        ?>'); return;">Copy URL</button>
     </div>
 <?php
}

function so_wp_ajax_function() {
    $repository = new Repository;
    $post_id = $_POST['id'];
    $is_prevented = $_POST['is_prevented'];
    if ( $is_prevented === '1' ) {
        $limit = fa_get_file_limitation();
        $number_of_records = $repository->check_advance_file_limitation();
        if ( $number_of_records >= $limit ) {
            $file_result = array( 'error' => "You can only protect 3 files & photos on the free version. Please contact us for the premium version." );
        }
        else {
            $file_result = prevent_direct_access( $post_id, $is_prevented );
        }
    }
    else {
        $file_result = prevent_direct_access( $post_id, $is_prevented );
    }
    wp_send_json( $file_result );
    wp_die();
}

function prevent_direct_access( $post_id, $is_prevented ) {
    $repository = new Repository;
    $file_info = array( 'time' => current_time( 'mysql' ), 'post_id' => $post_id, 'is_prevented' => $is_prevented, 'url' => generate_unique_string() );
    $result = $repository->create_advance_file( $file_info );
    if ( $result < 1 || $result === false ) {
        $file_result = array( 'error' => "Cannot create file advance's info" );
    }
    else {
        $file_result = $repository->get_advance_file_by_post_id( $file_info['post_id'] );
        $generated_file_code = $file_result->url;
        $file_result->url = site_url() . '/private/' . $file_result->url;
    }
    return $file_result;
}

function delete_prevent_direct_access( $post_id ) {
    $repository = new Repository;
    $repository->delete_advance_file_by_post_id( $post_id );
}

function wcm_setup_demo_on_uninstall() {
    if ( !current_user_can( 'activate_plugins' ) ) return;
    check_admin_referer( 'bulk-plugins' );

    // Important: Check if the file is the one
    // that was registered during the uninstall hook.
    if ( __FILE__ != WP_UNINSTALL_PLUGIN ) return;

    // Uncomment the following line to see the function in action
    exit( var_dump( $_GET ) );
}

function deactivate() {
    remove_action( 'mod_rewrite_rules', 'fa_htaccess_contents' );
    $GLOBALS['wp_rewrite']->flush_rules();
}
function uninstall() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'prevent_direct_access';
    $wpdb->query( "DROP TABLE IF EXISTS $table_name" );
}
?>
