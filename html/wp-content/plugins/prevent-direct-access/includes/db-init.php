<?php
global $jal_db_version;
$jal_db_version = '1.0';

function jal_install() {	
	pda_validate_activation();

	global $wpdb;
	global $jal_db_version;

	$wpdb->show_errors();

	$table_name = $wpdb->prefix . 'advancefiles';
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
	    //table is not created. you may create the table here.
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
}

function pda_validate_activation() {
	$activation_failed_messages = fa_htaccess_writable();
	error_log($activation_failed_messages, 0);
	
	if ($activation_failed_messages !== true) {	
			deactivate_plugins( basename( __FILE__ ) );    
			wp_die('<p>The <strong>' . $activation_failed_messages . '</strong></p>','Plugin Activation Error',  array( 'response'=>200, 'back_link'=>TRUE ) );			
	}	
}

?>