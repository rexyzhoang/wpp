<?php
if ( !defined( 'ABSPATH' ) ) exit;

require_once 'includes/repository.php';
require_once 'includes/helper.php';
ignore_user_abort( true );
set_time_limit( 0 ); // disable the time limit for this script

$home_url = get_home_url();


$is_direct_access = isset( $_GET['is_direct_access'] ) ? $_GET['is_direct_access'] : '';
if ( $is_direct_access === 'true' ) {
    error_log("is_direct_access ============== " . $is_direct_access);
    check_file_is_prevented();
} else {    
    show_file_from_private_link();
}

function check_file_is_prevented() {
    $configs = Pda_Helper::get_plugin_configs();
    $endpoint = $configs['endpoint'];
    $file_name = $_GET[$endpoint];
    $guid = $_SERVER['REQUEST_URI'];
    $file_type = $_GET['file_type'];
    $guid = preg_replace("/-\d+x\d+.$file_type$/", ".$file_type", $guid);
    error_log("[download.23]guid: " . $guid);
    $file_name = preg_replace('{^/|\?.*}', '', $file_name);

    $repository = new Repository;
    $post = $repository->get_post_by_guid( $guid );
    if ( isset( $post ) ) {
        error_log("[download.25]PostId: " . $post->ID);
        $advance_file = $repository->get_advance_file_by_post_id( $post->ID );
        //error_log("$advance_file = " . $advance_file->ID);
        //check whether the file is prevented
        if ( isset( $advance_file ) && $advance_file->is_prevented === "1" ) {
            status_header( 404 );
            die( '404 &#8212; File not found.' );
        } else {
            $base_dir = ABSPATH;
            $file = $base_dir . $guid;
            send_file_to_client( $file );
        }
    } else {
        status_header( 404 );
        die( '404 &#8212; File not found.' );
    }
}

function send_file_to_client( $file ) {
    if ( !is_file( $file ) ) {
        status_header( 404 );
        die( '404 &#8212; File not found.' );
    }
    $mime = wp_check_filetype( $file );

    if ( false === $mime[ 'type' ] && function_exists( 'mime_content_type' ) ) {
        $mime[ 'type' ] = mime_content_type( $file );
    }
    if ( $mime[ 'type' ] ) {
        $mimetype = $mime[ 'type' ];
    }
    else {
        $mimetype = 'image/' . substr( $file, strrpos( $file, '.' ) + 1 );
    }

    //set header
    header( 'Content-Type: ' . $mimetype ); // always send this
    if ( false === strpos( $_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS' ) ) {
        header( 'Content-Length: ' . filesize( $file ) );
    }

    $last_modified = gmdate( 'D, d M Y H:i:s', filemtime( $file ) );
    $etag = '"' . md5( $last_modified ) . '"';
    header( "Last-Modified: $last_modified GMT" );
    header( 'ETag: ' . $etag );
    header( 'Expires: ' . gmdate( 'D, d M Y H:i:s', time() + 100000000 ) . ' GMT' );
    // Support for Conditional GET
    $client_etag = isset( $_SERVER['HTTP_IF_NONE_MATCH'] ) ? stripslashes( $_SERVER['HTTP_IF_NONE_MATCH'] ) : false;
    if ( ! isset( $_SERVER['HTTP_IF_MODIFIED_SINCE'] ) )
        $_SERVER['HTTP_IF_MODIFIED_SINCE'] = false;
    $client_last_modified = trim( $_SERVER['HTTP_IF_MODIFIED_SINCE'] );
    // If string is empty, return 0. If not, attempt to parse into a timestamp
    $client_modified_timestamp = $client_last_modified ? strtotime( $client_last_modified ) : 0;
    // Make a timestamp for our most recent modification...
    $modified_timestamp = strtotime( $last_modified );

    if ( ( $client_last_modified && $client_etag )
        ? ( ( $client_modified_timestamp >= $modified_timestamp ) && ( $client_etag == $etag ) )
        : ( ( $client_modified_timestamp >= $modified_timestamp ) || ( $client_etag == $etag ) )
    ) {
        status_header( 304 );
        exit;
    }
    
    readfile( $file );
}

function show_file_from_private_link() {
    $configs = Pda_Helper::get_plugin_configs();
    $endpoint = $configs['endpoint'];
    if(isset($_GET[$endpoint])) {
        $private_url = $_GET[$endpoint];
        $repository = new Repository;
        $advance_file = $repository->get_advance_file_by_url( $private_url );
        if ( isset( $advance_file ) && $advance_file->is_prevented === "1" ) {
            $post_id = $advance_file->post_id;
            $post = $repository->get_post_by_id( $post_id );

            error_log("==========show_file_from_private_link()");
            // update hits count by 1
            $new_hits_count = isset($advance_file->hits_count) ? $advance_file->hits_count + 1 : 1;     
            $repository->update_advance_file_by_id($advance_file->ID, array('hits_count' => $new_hits_count));

            if ( isset( $post ) ) {
                download_file( $post );
            } else {
                echo '<h2>Sorry! Invalid post!</h2>';
            }
        } else {
            echo '<h2>Sorry! Invalid url!</h2>';
        }
    } else {
       echo '<h2>Sorry! Invalid url!</h2>'; 
    }
}

function download_file( $post ) {
    $fullPath = $post->guid;
    $site_url = get_site_url();
    $wpDir = ABSPATH;
    $fullPath = str_replace( $site_url . '/', $wpDir, $fullPath );
    send_file_to_client( $fullPath );
}
