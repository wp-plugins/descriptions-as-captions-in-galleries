<?php

/**
 * The Descriptions as Captions in Galleries Plugin
 *
 * Show image descriptions as captions in galleries.
 *
 * @package Descriptions as Captions
 * @subpackage Main
 */

/**
 * Plugin Name: Descriptions as Captions in Galleries
 * Plugin URI:  http://blog.milandinic.com/wordpress/plugins/descriptions-as-captions-in-galleries/
 * Description: Show image descriptions as captions in galleries.
 * Author:      Milan Dinić
 * Author URI:  http://blog.milandinic.com/
 * Version:     1.0-beta-1
 * Text Domain: dacig
 * Domain Path: /languages/
 * License:     GPL
 */

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Load textdomain for internationalization
 *
 * @since 1.0
 *
 * @uses load_plugin_textdomain() To load translation file
 */
function dacig_load_textdomain() {
	load_plugin_textdomain( 'dacig', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

/**
 * Add action links to plugins page
 *
 * Thanks to Dion Hulse for guide
 * and Adminize plugin for implementation
 *
 * @link http://dd32.id.au/wordpress-plugins/?configure-link
 * @link http://bueltge.de/wordpress-admin-theme-adminimize/674/
 *
 * @since 1.0
 *
 * @uses dacig_load_textdomain() To load translation
 * @uses plugin_basename() To get plugin's file name
 *
 * @param array $links Default links of plugin
 * @param string $file Name of plugin's file
 * @return array $links New & old links of plugin
 */
function dacig_filter_plugin_actions( $links, $file ) {
	/* Load translations */
	dacig_load_textdomain();

	static $this_plugin;

	if ( ! $this_plugin )
		$this_plugin = plugin_basename( __FILE__ );

	if ( $file == $this_plugin ) {
		$donate_link = '<a href="http://blog.milandinic.com/donate/">' . __( 'Donate', 'dacig' ) . '</a>';
		$links = array_merge( array( $donate_link ), $links ); // Before other links
	}

	return $links;
}
add_filter( 'plugin_action_links', 'dacig_filter_plugin_actions', 10, 2 );

/**
 * Register replacement filters.
 *
 * This is used only to register real filters
 * on appropiate place. Passed content
 * is untouched.
 * 
 * @since 1.0
 *
 * @uses add_filter() to register real replacements.
 *
 * @param $output string Original output
 * @return $output string Original output
 */
function dacig_register_filters( $output ) {
	add_filter( 'pre_get_posts', 'dacig_allow_filters' );
	add_filter( 'the_posts',     'dacig_modify_the_posts' );

	return $output;
}
add_filter( 'post_gallery', 'dacig_register_filters' );

/**
 * Deregister replacement filters.
 *
 * This is used only to register real filters
 * on appropiate place. Passed content
 * is untouched.
 * 
 * @since 1.0
 *
 * @uses remove_filter() to deregister real replacements.
 *
 * @param $status bool Original status 
 * @return $status bool Original status 
 */
function dacig_deregister_filters( $status ) {
	remove_filter( 'pre_get_posts', 'dacig_allow_filters' );
	remove_filter( 'the_posts',     'dacig_modify_the_posts' );

	return $status;
}
add_filter( 'use_default_gallery_style', 'dacig_deregister_filters' );

/**
 * Allow filters in get_posts().
 *
 * By default, get_posts() doesn't allow
 * query filters. This changes that.
 * 
 * @since 1.0
 *
 * @param $query object Original query 
 * @return $query object Modified query 
 */
function dacig_allow_filters( $query ) {
	$query->query_vars['suppress_filters'] = false;

	return $query;
}

/**
 * Change the_posts results.
 *
 * Replace post_excerpt with post_content.
 * Excerpt is caption, content is description.
 * 
 * @since 1.0
 *
 * @param $results array List of posts 
 * @return $results array Modified list of posts 
 */
function dacig_modify_the_posts( $results ) {
	$num_posts = count( $results );

	for ( $i = 0; $i < $num_posts; $i++ ) {
		if ( $results[$i]->post_content )
			$results[$i]->post_excerpt = $results[$i]->post_content;
	}

	return $results;
}
