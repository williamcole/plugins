<?php

/**
 * Plugin Name: COMPANY Twitter Cards
 * Description: Adds twitter-specific meta tags that describe our content in the open graph
 * Author: William Cole
 * Version: 0.1
 */

function WBC3_twitter_card() {

	global $post, $WBC3_gallery;
	
	// display on single pages only
	if( !is_single() )
		return;
	
	// default image sizes
	$default_width = 435;
	$default_height = 455;
	
	// define array of post formats to accept
	$WBC3_twitter_card_post_formats = array(
		'video', // only display on Video posts for now
	);
	
	// get post ID and format so we can determine what content to display
	$post_id = WBC3_is_gallery() ? $WBC3_gallery->current_item->ID : $post->ID;
	$post_format = WBC3_get_post_format();
	
	// only display meta tags on single pages
	if( in_array( $post_format, $WBC3_twitter_card_post_formats ) ) {	
		
		// initialize twitter array
		$twitter = array();
		
		# TWITTER SITE / CREATOR
		$twitter['site'] = '@COMPANY';
		// $twitter['creator'] = '@COMPANY';
		
		# TWITTER TITLE
		$title = get_post_meta( $post_id, 'WBC3_html_title', true ) ? get_post_meta( $post_id, 'WBC3_html_title', true ) : get_the_title();
		$title = strlen( $title ) > 70 ? substr( $title, 0, 63 ) . '...' : $title;
		$twitter['title'] = $title;
		
		# TWITTER URL
		$parts = parse_url( home_url() . esc_attr( $_SERVER['REQUEST_URI'] ) );
		if( $parts ) $twitter['url'] = home_url() . $parts['path'];
		
		# TWITTER CONTENT
		$deck = ( function_exists( 'WBC3_get_deck' ) ) ? WBC3_get_deck() : '';
		$excerpt = get_the_excerpt();

		// get content from deck or excerpt if possible
		if( $deck ) {
			$description = $deck;
		} else if( $excerpt ) {
			$description = $excerpt;
		} else {
			$description = $post->post_content;
		}
		
		// clean up description
		$description = strip_tags( $description );
		$description = strlen( $description ) > 200 ? substr( $description, 0, 197 ) . '...' : $description;
		$twitter['description'] = $description;
		
		# TWITTER IMAGE
		if( WBC3_is_gallery() ) {
			// gallery image
			$thumb = wp_get_attachment_image_src( $post_id );
		} else if( has_post_thumbnail( $post_id ) ) {
			// featured image
			$thumb = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ) );
		}
		
		// set image dimensions
		// Make sure the image is at least 68,600 pixels (a 262x262 square image, or a 350x196 16:9 image) or bigger
		// @link https://dev.twitter.com/docs/cards
		if( $thumb ) {
			
			// featured image
			$image_url = $thumb[0];
			$image_width = $thumb[1];
			$image_height = $thumb[2];
			
			// strip size parameters from image url
			$image_url_parts = explode( '?', $thumb[0] );
			$image_url = $image_url_parts[0];
			
		} else {
			
			// default COMPANY icon
			$image_url = get_stylesheet_directory_uri().'/library/assets/images/WBC3-twitter-card-video-default.jpg';
			$image_width = $default_width;
			$image_height = $default_height;
			
		}
		
		$twitter['image'] = $image_url;
		$twitter['image:width'] = $image_width;
		$twitter['image:height'] = $image_height;
		
		# TWITTER CARD TYPE
		
		// define arrays for summary types
		$summary_type = array(
			'article', 
			'standard',
			'special',
			'number',
			'quote',
			'review',
			'recap',
			'interactive',
		);
		
		$photo_type = array(
			'photo', 
			'gallery',
			'endless',
		);
		
		$video_type = array(
			'video',
		);
		
		// define card type: photo, player, or summary
		if( in_array( $post_format, $photo_type ) ) {
			
			# PHOTO			
			$twitter['card'] = 'photo';
			
		} else if( in_array( $post_format, $video_type ) ) {
		
			# PLAYER
			$twitter['card'] = 'player';
			
			// get video id
			$video = get_post_meta( get_the_ID(), 'WBC3_video', true );
					
			if( !empty( $video ) ) {
					
				// determine video type and id
				$video = substr( $video, 1, -1 );
				$video = explode( "=", $video );
				
				// make sure this is a brightcove video
				if ( $video[0] == "WBC3-brightcove videoid" ) {
					
					// get video id from shortcode
					$video_id = $video[1];
					
					if( !empty( $video_id ) ) {
						
						// define secure url to brightcove player
						$twitter['player'] = 'https://a248.e.akamai.net/f/1016/606/1m/www.company.com/company/twitter-cards/player.html?video=' . $video_id;
						
						// You should make this image the same dimensions as your player
						// images with fewer than 68,600 pixels (a 262x262 square image, or a 350x196 16:9 image) will cause the player card not to render.
						// @link https://dev.twitter.com/docs/cards
						
						// make sure video player and image have the same dimensions
						$twitter['player:width'] = $default_width;
						$twitter['player:height'] = $default_height;
						$twitter['image:width'] = $default_width;
						$twitter['image:height'] = $default_height;
					
					}
				
				} else {
					
					// otherwise set twitter card to summary
					$twitter['card'] = 'summary';
				}
				
			}
			
		} else {
			
			# SUMMARY (default)
			$twitter['card'] = 'summary';
			
		}
		
		// filter hook
		$twitter = apply_filters( 'WBC3_twitter_card', $twitter );
		
		// output twitter meta data
		foreach( $twitter as $key => $val ) {
			
			if( !empty( $val ) ) {
			
				// properly escape vars depending on type			
				if( ( $key == 'url' ) || ( $key == 'image' ) || ( $key == 'player' ) ) {
					$content = esc_url( $val );
				} else {
					$content = esc_attr( $val );
				}
				
				// twitter meta data
				echo '<meta name="' . esc_attr( 'twitter:' . $key ) . '" content="' . $content . '"/>' . "\n";
			
			}
		}
	}	
}
add_filter( 'wp_head', 'WBC3_twitter_card', 6 );