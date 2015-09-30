<?php
class Repository {

	private $wpdb;
	private $table_name;

	public function __construct() {
	    global $wpdb;
	    $this->wpdb = &$wpdb;
		$this->table_name = $wpdb->prefix . 'advancefiles';
    }

	function create_advance_file($file_info) {

	    $post_id = $file_info['post_id'];
	    $post = $this->get_post_by_id($post_id);
	    
	    $result = false;
	    
	    if (isset($post)) {
	        $file_advance = $this->get_advance_file_by_post_id($post_id);
	        if (!isset($file_advance)) {
	            $result = $this->wpdb->insert($this->table_name, $file_info);
	        } 
	        else {
	            $isUpdate = $file_advance->is_prevented !== $file_info['is_prevented'];
	            if ($isUpdate) {
	                $result = $this->update_advance_file_by_post_id($file_info);
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
	    $table_name = $this->table_name;
	    $queryString = "SELECT * FROM $this->table_name WHERE post_id = $post_id";
	    $advance_file = $this->wpdb->get_row($queryString);
	    return $advance_file;
	}

	function get_advance_file_by_url($url) {
	    $advance_file = $this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM $this->table_name WHERE url LIKE %s", $url, ARRAY_A));
	    return $advance_file;
	}

	function update_advance_file_by_post_id($file_info) {
	    $data = array('is_prevented' => $file_info['is_prevented'],);
	    $where = array('post_id' => $file_info['post_id']);
	    $result = $this->wpdb->update($this->table_name, $data, $where);
	    return $result;
	}
}

?>