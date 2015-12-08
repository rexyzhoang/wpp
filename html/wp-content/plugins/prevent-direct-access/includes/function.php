<?php
if ( !defined( 'ABSPATH' ) ) die( 'You do not have sufficient permissions to access this file.' );

class Pda_Function {

    public $htaccess_content = "# BEGIN WordPress
                                        RewriteRule private/([a-zA-Z0-9]+)$ index.php?pre_dir_acc_61co625547=$1 [R=301,L]
                                        RewriteCond %{REQUEST_FILENAME} -s
                                        RewriteRule wp-content/uploads(/[a-zA-Z_\-\s0-9\.]+)+\.([a-zA-Z0-9]+)$ index.php?pre_dir_acc_61co625547=$1&is_direct_access=true&file_type=$2 [QSA,L]
                                        <IfModule mod_rewrite.c>
                                        RewriteEngine On
                                        RewriteBase /
                                        RewriteRule ^index\.php$ - [L]
                                        RewriteCond %{REQUEST_FILENAME} !-f
                                        RewriteCond %{REQUEST_FILENAME} !-d
                                        RewriteRule . /index.php [L]
                                        </IfModule>
                                        Options -Indexes

                                # END WordPress";
    function get_htaccess_file_path() {

        //global $wp_rewrite;
        $home_path = get_home_path();
        $htaccess_file = $home_path . '.htaccess';

        return $htaccess_file;
    }

    function htaccess_writable() {
        $htaccess_file = $this->get_htaccess_file_path();

        if ( !file_exists( $htaccess_file ) ) {
            return '.htaccess file not existed';
        }

        $file_contents = file_get_contents( $htaccess_file );
        $file_contents = preg_replace('/\s+/', '', trim( $file_contents ) );
        $this->htaccess_content =  preg_replace('/\s+/', '', trim( $this->htaccess_content ) );
        if($this->htaccess_content === $file_contents){
            return true;
        }
        if ( is_writable( $htaccess_file ) ) {
            return true;
        }

        @chmod( $htaccess_file, 0666 );

        if ( !is_writable( $htaccess_file ) ) {
            return 'Please ask host manager to grant write permission for .htaccess file.';
        }

        return true;
    }

    function get_htaccess_content() {

        //global $wp_rewrite;

        $htaccess_file = $this->get_htaccess_file_path();

        if ( !file_exists( $htaccess_file ) ) {
            return false;
        }

        if ( !is_writable( $htaccess_file ) ) {
            @chmod( $htaccess_file, 0666 );
        }

        if ( !$f = fopen( $htaccess_file, 'r' ) ) {
            return false;
        }

        return file_get_contents( $htaccess_file );
    }

    function sanitized_rule( $rule ) {
        $rule = trim( $rule );
        $rule = str_replace( '\\\\', '\\', $rule );
        $rule = str_replace( '\"', '"', $rule );

        return $rule;
    }

}
