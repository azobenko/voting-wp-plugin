<?php
/**
 * Plugin that allows users to vote on various articles
 *
 * Plugin Name: Simple Voting
 * Description: Plugin for voting on various articles
 * Version: 1.0.0
 * Author: Alex Zobenko
 * Author URI: https://azobenko.com
 * Text Domain: simple-voting
 */


// If this file is called directly, abort
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Core Plugin Class
 */
require_once( plugin_dir_path( __FILE__ ) . '/inc/SimpleVoting.php' );

/**
 * Begins plugin execution
 */
$plugin = new SimpleVoting();
$plugin->run();
