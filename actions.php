<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Update jquery
if ( get_option( 'speed_matters_update_jquery' ) == 1 )
	add_action( 'wp_enqueue_scripts', 'speed_matters_update_jquery' );
function speed_matters_update_jquery() {
	wp_deregister_script( 'jquery' );
//	wp_register_script( 'jquery', plugins_url() . '/speed-matters/assets/jquery/2.2.4/jquery.min.js', false, '2.2.4' );
	wp_register_script( 'jquery', plugins_url() . '/speed-matters/assets/jquery/3.5.1/jquery.min.js', false, '3.5.1' );
	wp_enqueue_script( 'jquery' );
}

// Remove the loading of jQuery Migrate
if ( get_option( 'speed_matters_remove_jquery_migrate' ) == 1 )
	add_action( 'wp_default_scripts', 'speed_matters_remove_jquery_migrate' );
function speed_matters_remove_jquery_migrate( $scripts ) {
    if ( !is_admin() && isset( $scripts->registered['jquery'] ) ) {
        $script = $scripts->registered['jquery'];
        
        if ( $script->deps ) {
            $script->deps = array_diff( $script->deps, array(
                'jquery-migrate'
            ));
        }
    }
}

// Remove the loading of WP Embed
if ( get_option( 'speed_matters_remove_wpembed' ) == 1 )
	add_action( 'wp_footer', 'speed_matters_remove_wpembed' );
function speed_matters_remove_wpembed( $scripts ) {
    wp_dequeue_script( 'wp-embed' );
}

// Disable the emoji's
if ( get_option( 'speed_matters_disable_emojis' ) == 1 )
	add_action( 'init', 'speed_matters_disable_emojis' );
function speed_matters_disable_emojis() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' ); 
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' ); 
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	add_filter( 'tiny_mce_plugins', 'speed_matters_disable_emojis_tinymce' );
	add_filter( 'wp_resource_hints', 'speed_matters_disable_emojis_remove_dns_prefetch', 10, 2 );
}

function speed_matters_disable_emojis_tinymce( $plugins ) {
	if ( is_array( $plugins ) ) {
		return array_diff( $plugins, array( 'wpemoji' ) );
	} else {
		return array();
	}
}

function speed_matters_disable_emojis_remove_dns_prefetch( $urls, $relation_type ) {
	if ( 'dns-prefetch' == $relation_type ) {
		$emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/' );
		$urls = array_diff( $urls, array( $emoji_svg_url ) );
	}
	return $urls;
}

// Disable the WordPress heartbeat
if ( get_option( 'speed_matters_disable_heartbeat' ) == 1 )
	add_action( 'init', 'speed_matters_disable_heartbeat', 1 );
function speed_matters_disable_heartbeat() {
	wp_deregister_script('heartbeat');
}