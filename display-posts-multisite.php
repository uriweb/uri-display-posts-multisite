<?php
/*
Plugin Name: Display Posts Multisite
Requires Plugins: display-posts-shortcode
Plugin URI: 
Description: Adds a blog_id parameter to display posts from other sites on the network.
Version: 0.1
Author: John Pennypacker <john@pennypacker.net>
Author URI: 
*/

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');


/**
 * The wrapper class. Handy for storing values like attributes and swiched.
 */
class DPSMultisite {

	/**
	 * Constructor method.
	 * Sets up instance variables, adds new shortcode, filters permalinks for formatting.
	 */
	function __construct() {
		$this->path = plugin_dir_path( __FILE__ );
		$this->url = plugin_dir_url( __FILE__ );
		$this->dpsmulti_switched = FALSE;
		$this->atts = array();

		add_shortcode( 'display-posts-multisite', array( $this, 'dpsmulti_shortcode' ) );
		add_filter( 'the_permalink', array( $this, 'autofix_permalink' ), 99 );
		
	}

	/**
	 * Shortcode callback.
	 * Switches blogs if id exists, then wraps be_display_posts_shortcode().
	 * @param arr $attributes are the shortcode attributes.
	 * @param str $content is the stuff inside the shortcode (unused).
	 * @param str $shortcode the raw shortcode (unused).
	 * @return str
	 */
	function dpsmulti_shortcode( $attributes, $content, $shortcode ) {
		$this->atts = $attributes;
		if( isset( $attributes['blog_id'] ) ) {
			$site = get_site( $attributes['blog_id'] );
			if( $site->blog_id ) {
				$this->dpsmulti_switched = TRUE;
				switch_to_blog( $site->blog_id );
			}	
		}
		$return = be_display_posts_shortcode( $attributes );
		if( TRUE === $this->dpsmulti_switched ) {
			restore_current_blog();
			$this->dpsmulti_switched = FALSE;
		}
		return $return;
	}

	/**
	 * Links are likely to come back looking ugly becase we don't bootstrap the source site.
	 * The workaround here is to address that. Especially with custom post types.
	 * @param str $url The URL of the link.
	 * @return str
	 */
	function autofix_permalink( $url ) {
		if( $this->dpsmulti_switched && FALSE !== strpos( $url, '?p=' ) && isset( $post->post_type ) ) {
			global $post;
			return dpsmulti_get_post_permalink( $post );
		}
		return $url;
	}

}

new DPSMultisite();