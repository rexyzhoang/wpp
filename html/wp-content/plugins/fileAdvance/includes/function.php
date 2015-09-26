<?php
if (!defined('ABSPATH')) die('You do not have sufficient permissions to access this file.');


// TEMPORARY DONT USE
/***** Vytvoření zálohy htaccess souboru ******************************/
function WPHE_CreateBackup(){
	$WPHE_backup_path = ABSPATH.'wp-content/htaccess.backup';
	$WPHE_orig_path = ABSPATH.'.htaccess';
	@clearstatcache();

	WPHE_CreateSecureWPcontent();

	if(file_exists($WPHE_backup_path)){
		WPHE_DeleteBackup();

		if(file_exists(ABSPATH.'.htaccess')){
			$htaccess_content_orig = @file_get_contents($WPHE_orig_path, false, NULL);
			$htaccess_content_orig = trim($htaccess_content_orig);
			$htaccess_content_orig = str_replace('\\\\', '\\', $htaccess_content_orig);
			$htaccess_content_orig = str_replace('\"', '"', $htaccess_content_orig);
			@chmod($WPHE_backup_path, 0666);
			$WPHE_success = @file_put_contents($WPHE_backup_path, $htaccess_content_orig, LOCK_EX);
			if($WPHE_success === false)
			{
				unset($WPHE_backup_path);
				unset($WPHE_orig_path);
				unset($htaccess_content_orig);
				unset($WPHE_success);
				return false;
			}else{
				unset($WPHE_backup_path);
				unset($WPHE_orig_path);
				unset($htaccess_content_orig);
				unset($WPHE_success);
				return true;
			}
			@chmod($WPHE_backup_path, 0644);
		}else{
			unset($WPHE_backup_path);
			unset($WPHE_orig_path);
			return false;
		}
	}else{
		if(file_exists(ABSPATH.'.htaccess')){
			$htaccess_content_orig = @file_get_contents($WPHE_orig_path, false, NULL);
			$htaccess_content_orig = trim($htaccess_content_orig);
			$htaccess_content_orig = str_replace('\\\\', '\\', $htaccess_content_orig);
			$htaccess_content_orig = str_replace('\"', '"', $htaccess_content_orig);
			@chmod($WPHE_backup_path, 0666);
			$WPHE_success = @file_put_contents($WPHE_backup_path, $htaccess_content_orig, LOCK_EX);
			if($WPHE_success === false){
				unset($WPHE_backup_path);
				unset($WPHE_orig_path);
				unset($htaccess_content_orig);
				unset($WPHE_success);
				return false;
			}else{
				unset($WPHE_backup_path);
				unset($WPHE_orig_path);
				unset($htaccess_content_orig);
				unset($WPHE_success);
				return true;
			}
			@chmod($WPHE_backup_path, 0644);
		}else{
			unset($WPHE_backup_path);
			unset($WPHE_orig_path);
			return false;
		}
	}
}


// TEMPORARY DONT USE
/***** Vytvoření htaccess souboru ve složce wp-content ****************/
function WPHE_CreateSecureWPcontent(){
	$wphe_secure_path = ABSPATH.'wp-content/.htaccess';
	$wphe_secure_text = '
# WP Htaccess Editor - Secure backups
<files htaccess.backup>
order allow,deny
deny from all
</files>
';

	if(is_readable(ABSPATH.'wp-content/.htaccess')){
		$wphe_secure_content = @file_get_contents(ABSPATH.'wp-content/.htaccess');

		if($wphe_secure_content !== false){
			if(strpos($wphe_secure_content, 'Secure backups') === false){
				unset($wphe_secure_content);
				$wphe_create_sec = @file_put_contents(ABSPATH.'wp-content/.htaccess', $wphe_secure_text, FILE_APPEND|LOCK_EX);
				if($wphe_create_sec !== false){
					unset($wphe_secure_text);
					unset($wphe_create_sec);
					return true;
				}else{
					unset($wphe_secure_text);
					unset($wphe_create_sec);
					return false;
				}
			}else{
				unset($wphe_secure_content);
				return true;
			}
		}else{
			unset($wphe_secure_content);
			return false;
		}
	}else{
		if(file_exists(ABSPATH.'wp-content/.htaccess')){
			return false;
		}else{
			$wphe_create_sec = @file_put_contents(ABSPATH.'wp-content/.htaccess', $wphe_secure_text, LOCK_EX);
			if($wphe_create_sec !== false){
				return true;
			}else{
				return false;
			}
		}
	}
}


// TEMPORARY DONT USE
/***** Obnova zálohy htaccess souboru *********************************/
function WPHE_RestoreBackup(){
	$wphe_backup_path = ABSPATH.'wp-content/htaccess.backup';
	$WPHE_orig_path = ABSPATH.'.htaccess';
	@clearstatcache();

	if(!file_exists($wphe_backup_path)){
		unset($wphe_backup_path);
		unset($WPHE_orig_path);
		return false;
	}else{
		if(file_exists($WPHE_orig_path)){
			if(is_writable($WPHE_orig_path)){
				@unlink($WPHE_orig_path);
			}else{
				@chmod($WPHE_orig_path, 0666);
				@unlink($WPHE_orig_path);
			}
		}
		$wphe_htaccess_content_backup = @file_get_contents($wphe_backup_path, false, NULL);
		if(WPHE_WriteNewHtaccess($wphe_htaccess_content_backup) === false){
			unset($wphe_success);
			unset($WPHE_orig_path);
			unset($wphe_backup_path);
			return $wphe_htaccess_content_backup;
		}else{
			WPHE_DeleteBackup();
			unset($wphe_success);
			unset($wphe_htaccess_content_backup);
			unset($WPHE_orig_path);
			unset($wphe_backup_path);
			return true;
		}
	}
}


// TEMPORARY DONT USE
/***** Smazání záložního souboru **************************************/
function WPHE_DeleteBackup(){
	$wphe_backup_path = ABSPATH.'wp-content/htaccess.backup';
	@clearstatcache();

	if(file_exists($wphe_backup_path)){
		if(is_writable($wphe_backup_path)){
			@unlink($wphe_backup_path);
		}else{
			@chmod($wphe_backup_path, 0666);
			@unlink($wphe_backup_path);
		}

		@clearstatcache();

		if(file_exists($wphe_backup_path)){
			unset($wphe_backup_path);
			return false;
		}else{
			unset($wphe_backup_path);
			return true;
		}
	}else{
		unset($wphe_backup_path);
		return true;
	}
}



/***** Vytvoření nového htaccess souboru ******************************/
function WPHE_WriteNewHtaccess($WPHE_new_content){
	global $wp_rewrite;

	$home_path = get_home_path();
	$htaccess_file = $home_path.'.htaccess';
	$WPHE_orig_path = $htaccess_file; 
	
	if(file_exists($WPHE_orig_path))
	{
		if(is_writable($WPHE_orig_path))
		{
			//error_log('is_writable');
			//@unlink($WPHE_orig_path);
		}else{
			//error_log('unwritable');
			@chmod($WPHE_orig_path, 0666);
			@unlink($WPHE_orig_path);
		}
	}
	
	if ( !$f = fopen( $WPHE_orig_path, 'r' ) ) {
		//error_log('cant open');
		return false;
	}			
	
	//error_log('WPHE_orig_path', $WPHE_orig_path, ABSPATH, $_SERVER["DOCUMENT_ROOT"]);
	$data = file_get_contents($WPHE_orig_path);
	
	//@clearstatcache();
	//error_log($data);
	
	$WPHE_new_content = trim($WPHE_new_content);
	$WPHE_new_content = str_replace('\\\\', '\\', $WPHE_new_content);
	$WPHE_new_content = str_replace('\"', '"', $WPHE_new_content);
	$WPHE_write_success = file_put_contents($WPHE_orig_path, PHP_EOL . $WPHE_new_content, FILE_APPEND);
	@clearstatcache();
	if(!file_exists($WPHE_orig_path) && $WPHE_write_success === false)
	{
		//error_log('file not exists');
		unset($WPHE_orig_path);
		unset($WPHE_new_content);
		unset($data);
		unset($WPHE_write_success);
		return false;
	}else{
		//error_log('file existed');
		unset($WPHE_orig_path);
		unset($WPHE_new_content);
		unset($data);
		unset($WPHE_write_success);
		return true;
	}
}

function WPHE_RemoveHtaccess($WPHE_rule){
	global $wp_rewrite;

	$home_path = get_home_path();
	$htaccess_file = $home_path.'.htaccess';
	$WPHE_orig_path = $htaccess_file; 
	
	if(file_exists($WPHE_orig_path))
	{
		if(is_writable($WPHE_orig_path))
		{
			//error_log('is_writable');
			//@unlink($WPHE_orig_path);
		}else{
			//error_log('unwritable');
			@chmod($WPHE_orig_path, 0666);
			@unlink($WPHE_orig_path);
		}
	}
	
	if ( !$f = fopen( $WPHE_orig_path, 'r' ) ) {
		//error_log('cant open');
		return false;
	}			
	
	//error_log('WPHE_orig_path', $WPHE_orig_path, ABSPATH, $_SERVER["DOCUMENT_ROOT"]);
	$data = file_get_contents($WPHE_orig_path);
	
	//@clearstatcache();
	//error_log($data);
	
	$WPHE_rule = trim($WPHE_rule);
	$WPHE_rule = str_replace('\\\\', '\\', $WPHE_rule);
	$WPHE_rule = str_replace('\"', '"', $WPHE_rule);
	
	// remove matching rules and EOL
	while (strpos($data, $WPHE_rule) !== false) {
		$start_replace = strpos($data, $WPHE_rule);
		$replace_length = strlen($WPHE_rule);
	
		//error_log('found new rule, removing..');
		$data = str_replace($WPHE_rule, '', $data);
		
		while (substr($data, start_replace - 1, 1) == PHP_EOL) {
			$data = substr_replace ($data, '', start_replace - 1, 1);
			//error_log('after remove rule: ', $data);
		}

	}
	
	$WPHE_write_success = file_put_contents($WPHE_orig_path, $data);
	@clearstatcache();
	if(!file_exists($WPHE_orig_path) && $WPHE_write_success === false)
	{
		//error_log('file not exists');
		unset($WPHE_orig_path);
		unset($WPHE_new_content);
		unset($data);
		unset($WPHE_write_success);
		return false;
	}else{
		//error_log('file existed');
		unset($WPHE_orig_path);
		unset($WPHE_new_content);
		unset($data);
		unset($WPHE_write_success);
		return true;
	}
}

function fa_generate_prevent_rule($site_url, $file_url) {
	$site_url .= '/';
	$redirect_url_rule = str_replace($site_url, '^', $file_url);
	$redirect_url_rule = str_replace('.', '\.', $redirect_url_rule);
	$redirect_url_rule .= '$ - [F,L]';			
	$redirect_url_rule = 'RewriteRule ' . $redirect_url_rule;
	return $redirect_url_rule;
}

/****** debug funkce **************************************************/
function WPHE_Debug($data){
	echo '<pre>';
	//error_log($data);
	echo '</pre>';
}
