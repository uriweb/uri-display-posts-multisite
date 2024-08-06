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


class DPSMultisite {

	function __construct() {
		$this->path = plugin_dir_path( __FILE__ );
		$this->url = plugin_dir_url( __FILE__ );
		$this->dpsmulti_switched = FALSE;
		$this->atts = array();

		add_shortcode( 'display-posts-multisite', array( $this, 'dpsmulti_shortcode' ) );
		add_filter( 'the_permalink', array( $this, 'autofix_permalink' ), 99 );
		
	}

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

	function autofix_permalink( $url ) {
		global $post;
		if( $this->dpsmulti_switched && FALSE !== strpos( $url, '?p=' ) && isset( $post->post_type ) ) {
			return dpsmulti_get_post_permalink( $post );
		}
		return $url;
	}

}

new DPSMultisite();