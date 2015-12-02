<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Pda_Database {

	public function __construct() {
		global $jal_db_version;
		$jal_db_version = '1.0';
		add_option( 'jal_db_version', $jal_db_version );
	}
	
	function install() {
		flush_rewrite_rules();

		global $wpdb;
		global $jal_db_version;

		$wpdb->show_errors();

		$table_name = $wpdb->prefix . 'prevent_direct_access';
		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
			//table is not created. you may create the table here.
			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE $table_name (
		    	ID mediumint(9) NOT NULL AUTO_INCREMENT,
		    	post_id mediumint(9) NOT NULL,
		    	time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		    	url varchar(55) DEFAULT '' NOT NULL,
		    	is_prevented tinyint(1) DEFAULT 1,
		    	hits_count mediumint(9) NOT NULL,
		    	UNIQUE KEY id (id)
		    ) $charset_collate;";

			// require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			// dbDelta( $sql );
			$wpdb->query( $sql );
		}
	}

	function uninstall() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'prevent_direct_access';
        $wpdb->query( "DROP TABLE IF EXISTS $table_name" );
    }
}

?>
