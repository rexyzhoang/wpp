<?php
if (!defined('ABSPATH')) die('You do not have sufficient permissions to access this file.');

function fa_get_htaccess_file_path() {
    
    //global $wp_rewrite;
    $home_path = get_home_path();
    $htaccess_file = $home_path . '.htaccess';
    
    return $htaccess_file;
}

function fa_htaccess_writable() {
	$htaccess_file = fa_get_htaccess_file_path();
	
	if (!file_exists($htaccess_file)) {
		return '.htaccess file not existed';
	}
	
	if(is_writable($htaccess_file)) {
		return true;		
	}
	
	@chmod($htaccess_file, 0666);
	
	if (!is_writable($htaccess_file)) {
		return 'Please ask host manager to grant write permission for .htaccess file.';
	}
	
	return true;
}

function fa_get_htaccess_content() {
    
    //global $wp_rewrite;
    
    $htaccess_file = fa_get_htaccess_file_path();
    
    if (!file_exists($htaccess_file)) {
        return false;
    }
    
    if (!is_writable($htaccess_file)) {
        @chmod($htaccess_file, 0666);
    }
    
    if (!$f = fopen($htaccess_file, 'r')) {
        return false;
    }
    
    return file_get_contents($htaccess_file);
}

function fa_sanitized_rule($fa_rule) {
    $fa_rule = trim($fa_rule);
    $fa_rule = str_replace('\\\\', '\\', $fa_rule);
    $fa_rule = str_replace('\"', '"', $fa_rule);
    
    return $fa_rule;
}

function fa_generate_prevent_rule($site_url, $file_url) {
    $site_url.= '/';
    $redirect_url_rule = str_replace($site_url, '^', $file_url);
    $redirect_url_rule = str_replace('.', '(-[0-9]+x[0-9]+)?\.', $redirect_url_rule);
    $redirect_url_rule.= '$ - [F,L]';
    $redirect_url_rule = 'RewriteRule ' . $redirect_url_rule;
    return $redirect_url_rule;
}

function fa_generate_redirect_download_page($generated_file_code) {
    $redirect_url_rule = 'RewriteRule ^private/' . $generated_file_code;
    $redirect_url_rule.= ' wp-content/plugins/prevent-direct-access/download.php?download_file=' . $generated_file_code . ' [R=301,L]';
    return $redirect_url_rule;
}
