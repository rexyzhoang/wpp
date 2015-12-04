<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Pda_Helper {

	public static function generate_unique_string() {
		return uniqid();
	}

	public static function get_plugin_configs() {
		return array('endpoint' => 'pre_dir_acc_61co625547');
	}

	public static function get_guid($file_name, $request_url, $file_type) {
		$guid = preg_replace("/-\d+x\d+.$file_type$/", ".$file_type", $request_url);
	}
}

?>
