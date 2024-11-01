<?php
/*
Plugin Name: Speed matters
Description: Remove unnecessary add-ons and tweak your database to improve website speed
Tags: speed
Version: 0.4
Author: Carl Conrad
Author URI: https://carlconrad.net
License: GPL2
Text Domain: speed-matters
Domain Path: /languages/
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'SPEED_MATTERS_DOMAIN', 'speed-matters' );
define( 'SPEED_MATTERS_PLUGIN_DIR', dirname( __FILE__ ) . DIRECTORY_SEPARATOR );
define( 'SPEED_MATTERS_LANG_DIR', dirname( plugin_basename( __FILE__ ) )  . DIRECTORY_SEPARATOR . 'languages' . DIRECTORY_SEPARATOR );

if ( is_admin() ){
	include( SPEED_MATTERS_PLUGIN_DIR . 'settings.php' );
	add_action( 'admin_init', 'register_speed_matters_settings' );
	add_action( 'admin_menu', 'speed_matters_options_page' );
}

include( SPEED_MATTERS_PLUGIN_DIR . 'actions.php' );

add_action( 'plugins_loaded', 'speed_matters_load_textdomain' );
function speed_matters_load_textdomain() {
	load_plugin_textdomain( SPEED_MATTERS_DOMAIN, false, SPEED_MATTERS_LANG_DIR );
}