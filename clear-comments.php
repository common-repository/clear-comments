<?php
/*
  Plugin Name: Clear comments
  Plugin URI: https://emelianovip.ru/2021/01/21/318/
  Description: This plugin replaces words from the stop list with asterisks ****
  Author: Brahman  <fb@emelianovip.ru>
  Author URI: https://emelianovip.ru
  Version: 1.0.4
  License: GPLv2
  Text Domain: clear-comments
 */

if (!function_exists('add_action')) {
    exit;
}


define( 'EPV_CLEAR_COMMENTS__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );


require_once( EPV_CLEAR_COMMENTS__PLUGIN_DIR . 'class.clear-comments.php' );


add_action( 'init', array( 'EPV_ClearComments', 'init' ) );

if ( is_admin() ) {
	require_once( EPV_CLEAR_COMMENTS__PLUGIN_DIR . 'class.clear-comments-admin.php' );
	add_action( 'init', array( 'EPV_ClearComments_Admin', 'init' ) );
        add_action( 'admin_enqueue_scripts', 'clear_comments_admin_styles' );
        add_action( 'plugins_loaded', 'clear_comments_load_plugin_textdomain' );        
}

function clear_comments_load_plugin_textdomain() {
    load_plugin_textdomain( 'clear-comments', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}

function clear_comments_admin_styles() {
    wp_register_style( 'clear_comments_admin_stylesheet', plugins_url( '/css/style.css', __FILE__ ) );
    wp_enqueue_style( 'clear_comments_admin_stylesheet' );
}

/**
 * Install table structure
 */
register_activation_hook(__FILE__, 'clear_comments_plugin_activation');

function clear_comments_plugin_activation() {
    global $table_prefix;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    $sql = "CREATE TABLE IF NOT EXISTS  `" . $table_prefix . "clear_comments_log`(
				`id` int(11) unsigned NOT NULL auto_increment,	
                                `cid` int(11) NOT NULL DEFAULT '0',
				`comment` text NOT NULL default '',
				`created_on` datetime NOT NULL default '0000-00-00 00:00:00',
                                
				PRIMARY KEY  (`id`)				
				) DEFAULT COLLATE utf8_general_ci;";
    dbDelta($sql);    
    
    return true;
}

