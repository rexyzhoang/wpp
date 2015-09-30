<?php

//fa_require_once(W3TC_INC_DIR . '/functions/rule_cut.php');

define('W3TC_MARKER_BEGIN_WORDPRESS', '# BEGIN WordPress');
define('W3TC_MARKER_BEGIN_PGCACHE_CORE', '# BEGIN W3TC Page Cache core');
define('W3TC_MARKER_BEGIN_PGCACHE_CACHE', '# BEGIN W3TC Page Cache cache');
define('W3TC_MARKER_BEGIN_PGCACHE_LEGACY', '# BEGIN W3TC Page Cache');
define('W3TC_MARKER_BEGIN_PGCACHE_WPSC', '# BEGIN WPSuperCache');
define('W3TC_MARKER_BEGIN_BROWSERCACHE_CACHE', '# BEGIN W3TC Browser Cache');
define('W3TC_MARKER_BEGIN_BROWSERCACHE_NO404WP', '# BEGIN W3TC Skip 404 error handling by WordPress for static files');
define('W3TC_MARKER_BEGIN_MINIFY_CORE', '# BEGIN W3TC Minify core');
define('W3TC_MARKER_BEGIN_MINIFY_CACHE', '# BEGIN W3TC Minify cache');
define('W3TC_MARKER_BEGIN_MINIFY_LEGACY', '# BEGIN W3TC Minify');
define('W3TC_MARKER_BEGIN_CDN', '# BEGIN W3TC CDN');
define('W3TC_MARKER_BEGIN_NEW_RELIC_CORE', '# BEGIN W3TC New Relic core');


define('W3TC_MARKER_END_WORDPRESS', '# END WordPress');
define('W3TC_MARKER_END_PGCACHE_CORE', '# END W3TC Page Cache core');
define('W3TC_MARKER_END_PGCACHE_CACHE', '# END W3TC Page Cache cache');
define('W3TC_MARKER_END_PGCACHE_LEGACY', '# END W3TC Page Cache');
define('W3TC_MARKER_END_PGCACHE_WPSC', '# END WPSuperCache');
define('W3TC_MARKER_END_BROWSERCACHE_CACHE', '# END W3TC Browser Cache');
define('W3TC_MARKER_END_BROWSERCACHE_NO404WP', '# END W3TC Skip 404 error handling by WordPress for static files');
define('W3TC_MARKER_END_MINIFY_CORE', '# END W3TC Minify core');
define('W3TC_MARKER_END_MINIFY_CACHE', '# END W3TC Minify cache');
define('W3TC_MARKER_END_MINIFY_LEGACY', '# END W3TC Minify');
define('W3TC_MARKER_END_CDN', '# END W3TC CDN');
define('W3TC_MARKER_END_NEW_RELIC_CORE', '# END W3TC New Relic core');


/*
 * Returns URI from filename/dirname
 * Used for rules mainly since is not usable for regular URI,
 * because wordpress adds blogname to uri making it uncompatible with
 * directory structure
 *
 * @return string
 */
function fa_filename_to_uri($filename) {
    $document_root = fa_get_document_root();

    return substr($filename, strlen($document_root));
}

/**
 * Check if WP permalink directives exists
 *
 * @return boolean
 */
function fa_is_permalink_rules() {
    if ((fa_is_apache() || fa_is_litespeed()) && !fa_is_network()) {
        $path = fa_get_home_root() . '/.htaccess';

        return (($data = @file_get_contents($path)) && strstr($data, W3TC_MARKER_BEGIN_WORDPRESS) !== false);
    }

    return true;
}

/**
 * Removes empty elements
 */
function fa_array_trim(&$a) {
    for ($n = count($a) - 1; $n >= 0; $n--) {
        if (empty($a[$n]))
            array_splice($a, $n, 1);
    }
}

/**
 * Returns nginx rules path
 *
 * @return string
 */
function fa_get_nginx_rules_path() {
    $config = fa_instance('fa_Config');

    $path = $config->get_string('config.path');

    if (!$path) {
        $path = fa_get_document_root() . '/nginx.conf';
    }

    return $path;
}

/**
 * Returns path of pagecache core rules file
 *
 * @return string
 */
function fa_get_pgcache_rules_core_path() {
    switch (true) {
        case fa_is_apache():
        case fa_is_litespeed():
            return fa_get_home_root() . '/.htaccess';

        case fa_is_nginx():
            return fa_get_nginx_rules_path();
    }

    return false;
}

/**
 * Returns path of browsercache cache rules file
 *
 * @return string
 */
function fa_get_browsercache_rules_cache_path() {
    switch (true) {
        case fa_is_apache():
        case fa_is_litespeed():
            return fa_get_home_root() . '/.htaccess';

        case fa_is_nginx():
            return fa_get_nginx_rules_path();
    }

    return false;
}

/**
 * Returns path of browsercache no404wp rules file
 *
 * @return string
 */
function fa_get_browsercache_rules_no404wp_path() {
    switch (true) {
        case fa_is_apache():
        case fa_is_litespeed():
            return fa_get_home_root() . '/.htaccess';

        case fa_is_nginx():
            return fa_get_nginx_rules_path();
    }

    return false;
}

/**
 * Returns path of minify rules file
 *
 * @return string
 */
function fa_get_minify_rules_core_path() {
    switch (true) {
        case fa_is_apache():
        case fa_is_litespeed():
            return W3TC_CACHE_MINIFY_DIR . '/.htaccess';

        case fa_is_nginx():
            return fa_get_nginx_rules_path();
    }

    return false;
}

/**
 * Returns path of minify rules file
 *
 * @return string
 */
function fa_get_minify_rules_cache_path() {
    switch (true) {
        case fa_is_apache():
        case fa_is_litespeed():
            return W3TC_CACHE_MINIFY_DIR . '/.htaccess';

        case fa_is_nginx():
            return fa_get_nginx_rules_path();
    }

    return false;
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

function fa_get_new_relic_rules_core_path() {
    return fa_get_pgcache_rules_core_path();
}

/**
 * Returns true if we can modify rules
 *
 * @param string $path
 * @return boolean
 */
function fa_can_modify_rules($path) {
    if (fa_is_network()) {
        if (fa_is_apache() || fa_is_litespeed() || fa_is_nginx()) {
            switch ($path) {
                case fa_get_pgcache_rules_cache_path():
                case fa_get_minify_rules_core_path():
                case fa_get_minify_rules_cache_path():
                    return true;
            }
        }

        return false;
    }

    return true;
}

/**
 * Trim rules
 *
 * @param string $rules
 * @return string
 */
function fa_trim_rules($rules) {
    $rules = trim($rules);

    if ($rules != '') {
        $rules .= "\n";
    }

    return $rules;
}

/**
 * Cleanup rewrite rules
 *
 * @param string $rules
 * @return string
 */
function fa_clean_rules($rules) {
    $rules = preg_replace('~[\r\n]+~', "\n", $rules);
    $rules = preg_replace('~^\s+~m', '', $rules);
    $rules = fa_trim_rules($rules);

    return $rules;
}

/**
 * Erases text from start to end
 *
 * @param string $rules
 * @param string $start
 * @param string $end
 * @return string
 */
function fa_erase_rules($rules, $start, $end) {
    $rules = preg_replace('~' . fa_preg_quote($start) . "\n.*?" . fa_preg_quote($end) . "\n*~s", '', $rules);
    $rules = fa_trim_rules($rules);

    return $rules;
}

/**
 * Check if rules exist
 *
 * @param string $rules
 * @param string $start
 * @param string $end
 * @return int
 */
function fa_has_rules($rules, $start, $end) {
    return preg_match('~' . fa_preg_quote($start) . "\n.*?" . fa_preg_quote($end) . "\n*~s", $rules);
}

/**
 * @param SelfTestExceptions $exs
 * @param string $path
 * @param string $rules
 * @param string $start
 * @param string $end
 * @param array $order
 */
function fa_add_rules($exs, $path, $rules, $start, $end, $order) {
    $data = @file_get_contents($path);

    if($data === false)
        $data = '';

    $rules_missing = !empty($rules) && (strstr(fa_clean_rules($data), fa_clean_rules($rules)) === false);
    if (!$rules_missing)
        return;

    $replace_start = strpos($data, $start);
    $replace_end = strpos($data, $end);

    if ($replace_start !== false && $replace_end !== false && $replace_start < $replace_end) {
        $replace_length = $replace_end - $replace_start + strlen($end) + 1;
    } else {
        $replace_start = false;
        $replace_length = 0;

        $search = $order;

        foreach ($search as $string => $length) {
            $replace_start = strpos($data, $string);

            if ($replace_start !== false) {
                $replace_start += $length;
                break;
            }
        }
    }

    if ($replace_start !== false) {
        $data = fa_trim_rules(substr_replace($data, $rules, $replace_start, $replace_length));
    } else {
        $data = fa_trim_rules($data . $rules);
    }

    if (strpos($path, W3TC_CACHE_DIR) === false || fa_is_nginx()) {
        try {
            fa_wp_write_to_file($path, $data);
        } catch (FilesystemOperationException $ex) {
            if ($replace_start !== false)
                $exs->push(new FilesystemModifyException(
                    $ex->getMessage(), $ex->credentials_form(),
                    sprintf(__('Edit file <strong>%s
                        </strong> and replace all lines between and including <strong>%s</strong> and
                        <strong>%s</strong> markers with:', 'w3-total-caceh'),$path, $start,$end), $path, $rules));
            else
                $exs->push(new FilesystemModifyException(
                    $ex->getMessage(), $ex->credentials_form(),
                    sprintf(__('Edit file <strong>%s</strong> and add the following rules
                                above the WordPress directives:', 'w3-total-cache'),
                                $path), $path, $rules));
        }
    } else {
        if (!@file_exists(dirname($path))) {
            fa_mkdir_from(dirname($path), W3TC_CACHE_DIR);
        }

        if (!@file_put_contents($path, $data)) {
            try {
                fa_wp_delete_folder(dirname($path), '',
                    $_SERVER['REQUEST_URI']);
            } catch (FilesystemOperationException $ex) {
                $exs->push($ex);
            }
        }
    }
}

/**
 * @param SelfTestExceptions $exs
 * @param string $path
 * @param string $start
 * @param string $end
 */
function fa_remove_rules($exs, $path, $start, $end) {
    if (!file_exists($path))
        return;

    $data = @file_get_contents($path);
    if ($data === false)
        return;
    if (strstr($data, $start) === false)
        return;

    $data = fa_erase_rules($data, $start,
        $end);

    try {
        fa_wp_write_to_file($path, $data);
    } catch (FilesystemOperationException $ex) {
        $exs->push(new FilesystemModifyException(
            $ex->getMessage(), $ex->credentials_form(),
            sprintf(__('Edit file <strong>%s</strong> and remove all lines between and including <strong>%s</strong>
            and <strong>%s</strong> markers.', 'w3-total-cache'), $path, $start, $end), $path));
    }
}
