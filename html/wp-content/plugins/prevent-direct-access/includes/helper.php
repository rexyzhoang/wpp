<?php
<<<<<<< HEAD
if ( ! defined( 'ABSPATH' ) ) exit;
function generate_unique_string() {
	return uniqid();
}

function fa_get_file_limitation() {
	return 3;
}

function fa_get_plugin_configs() {
	return array(
		'endpoint' => 'pre_dir_acc_61co625547'
	);
}
=======

class Pda_Helper {

	public static function generate_unique_string() {
		return uniqid();
	}

	public static function get_file_limitation() {
		return 3;
	}

	public static function get_plugin_configs() {
		return array('endpoint' => 'pre_dir_acc_61co625547');
	}

>>>>>>> dea698d01acd731c149499eb2c398d9996cfea5d
?>
