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
include 'includes/repository.php';
include 'includes/js-loader.php';
include 'includes/helper.php';

require_once dirname( __FILE__ ) . '/includes/function.php';

class Pda_Admin {

    private $pda_function;

    function __construct() {
        $this->pda_function = new Pda_Function();
        add_filter( 'manage_upload_columns', array($this, 'add_upload_columns') );
        add_action( 'manage_media_custom_column', array($this, 'media_custom_columns'), 0, 2 );
        add_action( 'admin_enqueue_scripts', array('Pda_JS_Loader', 'admin_load_js') );
        add_action( 'wp_ajax_myaction', array($this, 'so_wp_ajax_function' ) );
        add_action( 'delete_post', array($this, 'delete_prevent_direct_access' ) );
        add_action( 'admin_notices', array($this, 'admin_notices') );
        add_action( 'init', array($this, 'my_endpoint') );
        add_action( 'parse_query', array($this, 'parse_query') );

        register_activation_hook( __FILE__, array($this, 'plugin_install') );
        register_deactivation_hook( __FILE__, array($this, 'deactivate') );
        register_uninstall_hook( __FILE__, array('Pda_Admin', 'plugin_uninstall') );
        add_filter( 'mod_rewrite_rules', array($this, 'htaccess_contents') );
    }

    public function my_endpoint(){
        $configs = Pda_Helper::get_plugin_configs();
        $endpoint = $configs['endpoint'];
        add_rewrite_endpoint( $endpoint, EP_ROOT );
    }

    public function parse_query( $query ){
        $configs = Pda_Helper::get_plugin_configs();
        $endpoint = $configs['endpoint'];
        if( isset( $query->query_vars[$endpoint] ) ){
            include( plugin_dir_path( __FILE__ ) . '/download.php');
            exit;
        }
    }

    public function admin_notices() {
        global $pagenow;

        if ( $pagenow == 'plugins.php' || $pagenow == 'upload.php') {
            $activation_failed_messages = $this->pda_function->htaccess_writable();
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

    public function htaccess_contents( $rules ) {
        // eg. index.php?pre_dir_acc_61co625547=$1 [R=301,L]
        $configs = Pda_Helper::get_plugin_configs();
        $endpoint = $configs['endpoint'];
        $downloadFileRedirect = str_replace(trailingslashit(site_url()), '', 'index.php') . "?{$endpoint}=$1 [R=301,L]" . PHP_EOL;

        $newRule = "RewriteRule private/([a-zA-Z0-9]+)$ " . $downloadFileRedirect;
        $newRule .= "RewriteCond %{REQUEST_FILENAME} -s" . PHP_EOL;

        $directAccessPath = str_replace(trailingslashit(site_url()), '', 'index.php') . "?{$endpoint}=$1&is_direct_access=true&file_type=$2 [QSA,L]" . PHP_EOL;

        // eg. RewriteRule wp-content/uploads(/[a-zA-Z_\-\s0-9\.]+)+\.([a-zA-Z0-9]+)$ index.php?pre_dir_acc_61co625547=$1&is_direct_access=true&file_type=$2 [QSA,L]
        $newRule .= "RewriteRule " . str_replace(trailingslashit(site_url()), '', wp_upload_dir()['baseurl']) . "(/[a-zA-Z_\-\s0-9\.]+)+\.([a-zA-Z0-9]+)$ " . $directAccessPath;

        return $newRule . $rules . "Options -Indexes" . PHP_EOL;
    }

    public function add_upload_columns( $columns ) {
        $columns['direct_access'] = "Prevent Direct Access";
        return $columns;
    }

    public function media_custom_columns( $column_name, $id ) {
        $repository = new Repository;
        $post = get_post( $id );
        $advance_file = $repository->get_advance_file_by_post_id( $post->ID );
        $checked = isset( $advance_file ) && $advance_file->is_prevented;
        $url = isset( $advance_file ) && $checked ? site_url() . '/private/' . $advance_file->url : '';
        if ( $column_name != 'direct_access' ) {
            return;
        }
    ?>
         <input id="ckb_<?php
        echo $post->ID
        ?>" <?php
        if ( $checked ) echo 'checked="checked"'; ?> onclick="customFile.preventFile('<?php
        echo $post->ID
        ?>')" nonce="<?php echo wp_create_nonce('pda_ajax_nonce' . $post->ID); ?>" type="checkbox"/><?php
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

    public function so_wp_ajax_function() {
        $nonce = $_REQUEST['security_check'];
        $post_id = $_REQUEST['id'];
        if ( ! wp_verify_nonce( $nonce, 'pda_ajax_nonce' . $post_id ) ) {
            error_log('not verify nonce', 0);
            wp_die( 'invalid_nonce' );
        }

        $repository = new Repository;
        $post_id = $_POST['id'];
        $is_prevented = $_POST['is_prevented'];
        if ( $is_prevented === '1' ) {
            $limit = Pda_Helper::get_file_limitation();
            $number_of_records = $repository->check_advance_file_limitation();
            if ( $number_of_records >= $limit ) {
                $file_result = array( 'error' => "You can only protect 3 files & photos on the free version. Please contact us for the premium version." );
            }
            else {
                $file_result = $this->insert_prevent_direct_access( $post_id, $is_prevented );
            }
        }
        else {
            $file_result = $this->insert_prevent_direct_access( $post_id, $is_prevented );
        }
        wp_send_json( $file_result );
        wp_die();
    }

    public function insert_prevent_direct_access( $post_id, $is_prevented ) {
        $repository = new Repository;
        $file_info = array( 'time' => current_time( 'mysql' ), 'post_id' => $post_id, 'is_prevented' => $is_prevented, 'url' => Pda_Helper::generate_unique_string() );
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

    public function delete_prevent_direct_access( $post_id ) {
        $repository = new Repository;
        $repository->delete_advance_file_by_post_id( $post_id );
    }

    public function deactivate() {
        remove_action( 'mod_rewrite_rules', array($this, 'htaccess_contents') );
        $GLOBALS['wp_rewrite']->flush_rules();
    }

    public function plugin_install() {
        include dirname(__FILE__) . '/includes/db-init.php';
        $db = new Pda_Database();
        $db->install();
    }

    public function plugin_uninstall() {
        include dirname(__FILE__) . '/includes/db-init.php';
        $db = new Pda_Database();
        $db->uninstall();
    }
}

$pda_admin = new Pda_Admin();


?>
