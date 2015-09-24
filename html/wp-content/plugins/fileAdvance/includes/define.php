<?php

if (!defined('ABSPATH')) {
    die();
}

defined('FILE_ADVANCE_DIR') || define('FILE_ADVANCE_DIR', realpath(dirname(__FILE__) . '/..'));
define('FILE_ADVANCE_FILE', 'fileAdvance/fileAdvance.php');
define('FILE_ADVANCE_INC_DIR', W3TC_DIR . '/includes');

defined('WP_CONTENT_DIR') || define('WP_CONTENT_DIR', realpath(FILE_ADVANCE_DIR . '/../..'));

/**
 * Returns true if server is Apache
 *
 * @return boolean
 */
function fa_is_apache() {
    return (isset($_SERVER['SERVER_SOFTWARE']) && stristr($_SERVER['SERVER_SOFTWARE'], 'Apache') !== false);
}

/**
 * Check whether server is LiteSpeed
 *
 * @return bool
 */
function fa_is_litespeed() {
    return (isset($_SERVER['SERVER_SOFTWARE']) && stristr($_SERVER['SERVER_SOFTWARE'], 'LiteSpeed') !== false);
}

/**
 * Returns true if server is nginx
 *
 * @return boolean
 */
function fa_is_nginx() {
    return (isset($_SERVER['SERVER_SOFTWARE']) && stristr($_SERVER['SERVER_SOFTWARE'], 'nginx') !== false);
}

/**
 * Check whether $engine is correct CDN engine
 *
 * @param string $engine
 * @return boolean
 */
function fa_is_cdn_engine($engine) {
    return in_array($engine, array('ftp', 's3', 'cf', 'cf2', 'rscf', 'azure', 'mirror', 'netdna', 'maxcdn',
                                'cotendo', 'akamai', 'edgecast', 'att'));
}

/**
 * Returns true if CDN engine is mirror
 *
 * @param string $engine
 * @return bool
 */
function fa_is_cdn_mirror($engine) {
    return in_array($engine, array('mirror', 'netdna', 'maxcdn', 'cotendo', 'cf2', 'akamai', 'edgecast', 'att'));
}

/**
 * Returns true if CDN has purge all support
 * @param $engine
 * @return bool
 */
function fa_cdn_can_purge_all($engine) {
    return in_array($engine, array('cotendo', 'edgecast', 'att', 'netdna', 'maxcdn'));
}

/**
 * Returns domain from host
 *
 * @param string $host
 * @return string
 */
function fa_get_domain($host) {
    $host = strtolower($host);

    if (($pos = strpos($host, ':')) !== false) {
        $host = substr($host, $pos+3);
    }
    if (($pos = strpos($host, '/')) !== false) {
        $host = substr($host, 0, $pos);
    }

    $host = rtrim($host, '.');

    return $host;
}

/**
 * Returns absolute path to home directory
 *
 * Example:
 *
 * DOCUMENT_ROOT=/var/www/vhosts/domain.com
 * Install dir=/var/www/vhosts/domain.com/site/blog
 * home=http://domain.com/site
 * siteurl=http://domain.com/site/blog
 * return /var/www/vhosts/domain.com/site
 *
 * No trailing slash!
 *
 * @return string
 */
function fa_get_home_root() {
	
    return '/var/www/vhosts/domain.com/site';
}

/**
 * Returns home URL
 *
 * No trailing slash!
 *
 * @return string
 */
function fa_get_home_url() {
    
    return 'http://localhost/wordpress';
}

/**
 * Returns home path
 *
 * Example:
 *
 * home=http://domain.com/site/
 * siteurl=http://domain.com/site/blog
 * return /site/
 *
 * With trailing slash!
 *
 * @return string
 */
function fa_get_home_path() {

    return '/';
}

/**
 * Returns path to WP directory relative to document root
 *
 * Example:
 *
 * DOCUMENT_ROOT=/var/www/vhosts/domain.com/
 * Install dir=/var/www/vhosts/domain.com/site/blog/
 * return /site/blog/
 *
 * With trailing slash!
 *
 * @return string
 */
function fa_get_base_path() {
    return '/wordpress/';
}

/**
 * Returns if there is multisite mode
 *
 * @return boolean
 */
function fa_is_network() {
    return (fa_is_wpmu() || fa_is_multisite());
}

/**
 * Returns true if it's WPMU
 *
 * @return boolean
 */
function fa_is_wpmu() {
    static $wpmu = null;

    if ($wpmu === null) {
        $wpmu = file_exists(ABSPATH . 'wpmu-settings.php');
    }

    return $wpmu;
}

/**
 * Returns true if it's WP with enabled Network mode
 *
 * @return boolean
 */
function fa_is_multisite() {
    static $multisite = null;

    if ($multisite === null) {
        $multisite = ((defined('MULTISITE') && MULTISITE) || defined('SUNRISE') || fa_is_subdomain_install());
    }

    return $multisite;
}

/**
 * Returns true if WPMU uses vhosts
 *
 * @return boolean
 */
function fa_is_subdomain_install() {
    return ((defined('SUBDOMAIN_INSTALL') && SUBDOMAIN_INSTALL) || (defined('VHOST') && VHOST == 'yes'));
}

/**
 * Returns path of CDN rules file
 *
 * @return string
 */
function fa_get_cdn_rules_path() {
    switch (true) {
        case fa_is_apache():
        case fa_is_litespeed():
            return '.htaccess';

        case fa_is_nginx():
            return 'nginx.conf';
    }

    return false;
}

/**
     * rules core modification
     **/

    /**
     * Writes directives to WP .htaccess
     *
     * @param fa_Config $config
     * @param SelfTestExceptions $exs
     * @throws FilesystemOperationException with S/FTP form if it can't get the required filesystem credentials
     * @throws FileOperationException
     */
    private function rules_add($config, $exs) {
        fa_add_rules($exs, fa_get_browsercache_rules_cache_path(),
            $this->rules_generate($config),
            W3TC_MARKER_BEGIN_CDN,
            W3TC_MARKER_END_CDN,
            array(
                W3TC_MARKER_BEGIN_MINIFY_CORE => 0,
                W3TC_MARKER_BEGIN_PGCACHE_CORE => 0,
                W3TC_MARKER_BEGIN_BROWSERCACHE_NO404WP => 0,
                W3TC_MARKER_BEGIN_BROWSERCACHE_CACHE => 0,
                W3TC_MARKER_BEGIN_WORDPRESS => 0,
                W3TC_MARKER_END_PGCACHE_CACHE => strlen(W3TC_MARKER_END_PGCACHE_CACHE) + 1,
                W3TC_MARKER_END_MINIFY_CACHE => strlen(W3TC_MARKER_END_MINIFY_CACHE) + 1
            )
        );
    }

    /**
     * Removes Page Cache core directives
     *
     * @param SelfTestExceptions $exs
     * @throws FilesystemOperationException with S/FTP form if it can't get the required filesystem credentials
     * @throws FileOperationException
     */
    private function rules_remove($exs) {
        fa_remove_rules($exs,
            fa_get_browsercache_rules_cache_path(),
            W3TC_MARKER_BEGIN_CDN,
            W3TC_MARKER_END_CDN);
    }

    /**
     * Generates rules for WP dir
     *
     * @param fa_Config $config
     * @param bool $cdnftp
     * @return string
     */
    private function rules_generate($config, $cdnftp = false) {
        $fa_dispatcher = fa_instance('fa_Dispatcher');
        $fa_sharedRules = fa_instance('fa_SharedRules');

        $rules = '';
        if ($fa_dispatcher->canonical_generated_by($config, $cdnftp) == 'cdn')
            $rules .= $fa_sharedRules->canonical($config, $cdnftp);
        if ($fa_dispatcher->allow_origin_generated_by($config) == 'cdn')
            $rules .= $fa_sharedRules->allow_origin($config, $cdnftp);

        if (strlen($rules) > 0)
            $rules = 
                W3TC_MARKER_BEGIN_CDN . "\n" .
                $rules . 
                W3TC_MARKER_END_CDN . "\n";

        return $rules;
    }