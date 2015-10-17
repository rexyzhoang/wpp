<?php
require_once explode( "wp-content" , __FILE__ )[0] . "wp-load.php";
require_once 'includes/class-repository.php';

ignore_user_abort( true );
set_time_limit( 0 ); // disable the time limit for this script

$home_url = get_home_url();
$is_direct_access = isset( $_GET['is_direct_access'] ) ? $_GET['is_direct_access'] : '';
if ( $is_direct_access === 'true' ) {
    check_file_is_prevented();
} else {
    show_file_from_private_link();
}


function check_file_is_prevented() {
    
    $file_name = $_GET['download_file'];
    $guid = $_SERVER['REQUEST_URI'];
    $file_type = $_GET['file_type'];
    $guid = preg_replace("/-\d+x\d+.$file_type$/", ".$file_type", $guid);
    $file_name = preg_replace('{^/|\?.*}', '', $file_name);

    $repository = new Repository;
    $post = $repository->get_post_by_guid( $guid );
    if ( isset( $post ) ) {
        error_log("[download.25]PostId: " . $post->ID);
        $advance_file = $repository->get_advance_file_by_post_id( $post->ID );
        //check whether the file is prevented
        if ( isset( $advance_file ) && $advance_file->is_prevented === "1" ) {
            status_header( 404 );
            die( '404 &#8212; File not found (line31).' );
        } else {
            $post_date = $post->post_date;
            $month = date("m", strtotime($post_date)); //10
            $year = date("Y", strtotime($post_date));  //2015
            $path_to_upload = wp_upload_dir();
            $base_dir = $path_to_upload['basedir'];
            $file = $base_dir . '/' . $year . '/' . $month . '/' . $file_name . '.' . $file_type;
            error_log("[download.25]file: " . $file);
            send_file_to_client( $file );
        }
    } else {
        status_header( 404 );
        die( '404 &#8212; File not found (line41).' );
    }
}

function send_file_to_client( $file ) {
    if ( !is_file( $file ) ) {
        status_header( 404 );
        die( '404 &#8212; File not found (line49)' );
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
    $private_url = $_GET['download_file'];
    $repository = new Repository;
    $advance_file = $repository->get_advance_file_by_url( $private_url );
    //var_dump($advance_file);
    if ( isset( $advance_file ) ) {
        $post_id = $advance_file->post_id;
        $post = $repository->get_post_by_id( $post_id );

        if ( isset( $post ) ) {
            download_file( $post );
        } else {
            echo '<h2>Sorry! Invalid post!</h2>';
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
