<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class Repository {

	private $wpdb;
	private $table_name;

	public function __construct() {
		global $wpdb;
		$this->wpdb = &$wpdb;
		$this->table_name = $wpdb->prefix . 'prevent_direct_access';
	}

	function create_advance_file( $file_info ) {

		$post_id = $file_info['post_id'];
		$post = $this->get_post_by_id( $post_id );

		$result = false;

		if ( isset( $post ) ) {
			$file_advance = $this->get_advance_file_by_post_id( $post_id );
			if ( !isset( $file_advance ) ) {
				$file_info['hits_count'] = 0;
				$result = $this->wpdb->insert( $this->table_name, $file_info );
			}
			else {
				$isUpdate = $file_advance->is_prevented !== $file_info['is_prevented'];
				if ( $isUpdate ) {
					$result = $this->update_advance_file_by_post_id( $file_info );
				}
			}
		}

		return $result;

	}

	function get_post_by_id( $post_id ) {
		$post = get_post( $post_id );
		return $post;
	}

	function get_post_by_guid( $guid ) {
		$guid = '%' . $guid;
		$table_name = $this->wpdb->posts;
		$queryString = "SELECT * FROM $table_name WHERE post_type='attachment' AND guid LIKE %s";
		$preparation = $this->wpdb->prepare( $queryString, $guid, ARRAY_A );
		$post = $this->wpdb->get_row( $preparation );
		error_log("[repository.49]id: " . $post->ID);
		return $post;
	}

	function get_file_by_name( $name ) {
		$table_name = $this->wpdb->posts;
		$queryString = "SELECT * FROM $table_name WHERE post_type='attachment' AND post_name LIKE %s";
		$preparation = $this->wpdb->prepare( $queryString, $name, ARRAY_A );
		$post = $this->wpdb->get_row( $preparation );
		return $post;
	}

	function get_advance_file_by_post_id( $post_id ) {
		$queryString = "SELECT * FROM $this->table_name WHERE post_id = $post_id";
		$advance_file = $this->wpdb->get_row( $queryString );
		return $advance_file;
	}

	function get_advance_file_by_url( $url ) {
		$advance_file = $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM $this->table_name WHERE url LIKE %s", $url, ARRAY_A ) );
		return $advance_file;
	}

	function delete_advance_file( $id ) {
		$result = $this->wpdb->delete( $this->table_name, array( 'ID' => $id ), array( '%d' ) );
	}

	function update_advance_file_by_id( $id, $data ) {
		//error_log("$data = " . print_r($data, 1), 0);
		//error_log("$id = " . $id, 0);
		$where = array('ID' => $id);
		$result = $this->wpdb->update( $this->table_name, $data, $where );
		return $result;
	}

	function update_advance_file_by_post_id( $file_info ) {
		$data = array( 'is_prevented' => $file_info['is_prevented'], );
		$where = array( 'post_id' => $file_info['post_id'] );
		$result = $this->wpdb->update( $this->table_name, $data, $where );
		return $result;
	}

	function check_advance_file_limitation() {
		$is_prevented = 1;
		$number_of_records = $this->wpdb->get_var( $this->wpdb->prepare( "SELECT count(*) FROM $this->table_name WHERE is_prevented = %d", $is_prevented ) );
		return $number_of_records;
	}

	function delete_advance_file_by_post_id( $post_id ) {
		$advance_file = $this->get_advance_file_by_post_id( $post_id );
		if ( isset( $advance_file ) || $advance_file != null ) {
			$this->delete_advance_file( $advance_file->ID );
		}
	}

	/**
	 * Update the new private link by post id
	 *
	 * @param int     $post_id post's id
	 * @return int|false       The number of rows updated, or false on error
	 */
	function update_private_link_by_post_id( $post_id ) {
		$data = array( 'url' => Pda_Helper::generate_unique_string() );
		$where = array( 'post_id' => $post_id );
		$result = $this->wpdb->update( $this->table_name, $data, $where );
		return $result;
	}
}

?>
