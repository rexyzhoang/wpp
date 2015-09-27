<?php 
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
			
		} else {
			$isUpdate = $file_advance->is_prevented !== $file_info->is_prevented;
			if($isUpdate) {
				$result = update_advance_file_by_post_id($file_info);
			}
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

function get_advance_file_by_url($url) {
	global $wpdb;
	$wpdb->show_errors();
	$table_name = $wpdb->prefix . 'advancefiles';
	$advance_file = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE url LIKE %s", $url, ARRAY_A) );
	return $advance_file; 
}

function update_advance_file_by_post_id($fileInfo) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'advancefiles';

	$data = array(
	    'is_prevented' => $fileInfo['is_prevented'],
	);
	$where = array( 'post_id' => $fileInfo['post_id'] );

	$result = $wpdb->update( $table_name, $data, $where );
	return $result;
}
?>