<?php
/**
  Plugin Name: Feed Options
  Description: Create custom checkbox fields so post content can be included on / excluded from various feeds.
  Author: William Cole
  Version: 1.0
 */

/**
 * Helper function to determine vertical
 */
function WBC3_get_vertical() {

	global $cap, $WBC3_blog;
	
	if ( $cap ) {		
	
		// use Cheezcap subdomain if set
		$vertical = $cap->domain;	
	
	} else {		
	
		// otherwise directly query the HTTP_HOST
		$vertical = $_SERVER['HTTP_HOST'];
		
		// if localhost, check global variable for subdomain
		if ( preg_match( "/localhost/", $vertical ) ) {
		 	$vertical = $WBC3_blog['adfactory']['zone'];
		}
	
	}
	
	// get vertical shortname
	$vertical = explode( '.', $vertical );
	$vertical = $vertical[0];
	
	return $vertical;

}

/**
 * Set array of feeds with their names and respective slugs
 */
function WBC3_get_feeds() {
	$feeds = array(
		array(
			'name' => 'CNN',
			'slug' => 'cnn',
		),
		array(
			'name' => 'Yahoo',
			'slug' => 'yahoo',
		),
		/*
		array(
			'name' => 'Vertical',
			'slug' => 'vertical',
			'verticals' => array(
				// subdomain
				'healthland',
				'techland',
			),
		),
		*/
	);
	
	return apply_filters( 'WBC3_get_feeds', $feeds );
}

/**
 * Add custom meta box
 */
function WBC3_feed_options_admin_menu() {
	add_meta_box( 'WBC3_feed_options_meta_box', 'Feed Options', 'WBC3_feed_options_meta_box', 'post', 'side', 'low' );
}
add_action( 'admin_menu', 'WBC3_feed_options_admin_menu' );

/**
 * Feed options meta box
 */
function WBC3_feed_options_meta_box( $post ) {

	$feeds = WBC3_get_feeds();

	// set feed options nonce field
	wp_nonce_field( 'WBC3_feed_options', 'WBC3_feed_options_nonce', false );

	// declare WBC3_feed_option array
	$WBC3_feed_option = array( );

	// loop through feeds
	foreach ( $feeds as $feed ) {
		
		// check to see if we are on appropriate vertical before displaying
		if ( ( !array_key_exists( 'verticals', $feed ) ) || ( array_key_exists( 'verticals', $feed ) && ( in_array( WBC3_get_vertical(), $feed['verticals'] ) ) ) ) {
			
			// define feed url
			$feed_url = get_bloginfo( 'url' ) . '?feed=feedingtrough&key=5213842c655ee376718dabc732f7bb25&feed_type=' . $feed['slug'];
		
			// save to array
			$WBC3_feed_option[$feed['slug']] = esc_attr( get_post_meta( $post->ID, 'WBC3_feed_post_' . $feed['slug'], true ) );
	
			// output checkbox for each feed
			?>
			<p>
				<input type="checkbox" name="WBC3_feed_post_<?php echo $feed['slug']; ?>" value="1" <?php checked( $WBC3_feed_option[$feed['slug']], '1' ); ?> />
				<label>Include post on <a href="<?php echo esc_url( $feed_url ); ?>" target="_blank"><?php echo $feed['name']; ?> feed</a></label>
			</p>
			<?php
		
		}			
		
	}
		
	// exclude from Yahoo World feed checkbox
	if( ( WBC3_get_vertical() == 'world' ) && ( function_exists( 'WBC3_world_yahoo_feed' ) ) ) {
	
		// define feed url
		$yahoo_world_feed = 'http://world.company.com/?feed=yahoo';
		
		// save option
		$WBC3_feed_exclude_from_yahoo_world = esc_attr( get_post_meta( $post->ID, 'WBC3_feed_exclude_from_yahoo_world', true ) );
		
		// output checkbox
		?>
		<hr style="height: 1px; border: 0px; background: #CCC; color: #CCC">
		<p>
			<input type="checkbox" name="WBC3_feed_exclude_from_yahoo_world" value="1" <?php checked( $WBC3_feed_exclude_from_yahoo_world, '1' ); ?> />
			<label>Exclude post from <a href="<?php echo esc_url( $yahoo_world_feed ); ?>" target="_blank">Yahoo World feed</a></label>
			<p class="howto">NOTE: This feed is different from the above Yahoo feed. If box is NOT checked, post will be included.</p>
		</p>
		<?php

	}
	
}

// save our post meta once its been added
function WBC3_save_feed_options( $post_id ) {

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return;

	if ( !current_user_can( 'edit_post', $post_id ) )
		return;

	$feeds = WBC3_get_feeds();

	// loop through feeds and save
	foreach ( $feeds as $feed ) {
		WBC3_save_feed_option( $post_id, 'WBC3_feed_post_' . $feed['slug'], 'WBC3_feed_options_nonce', 'WBC3_feed_options' );
	}
	
	// save exclude from yahoo world feed
	if ( function_exists( 'WBC3_world_yahoo_feed' ) ) {
		WBC3_save_feed_option( $post_id, 'WBC3_feed_exclude_from_yahoo_world', 'WBC3_feed_options_nonce', 'WBC3_feed_options' );	
	}
	
}
add_action( 'save_post', 'WBC3_save_feed_options' );

/**
 * Saves post meta value
 *
 * @param int ID of the post
 * @param string Name of the post_meta key (same as the $_POST key and nonce name)
 * @param string Name of the nonce key
 * @param string Name of the nonce action
 * @param mixed The default value to be assigned if not set
 *
 * @return string Value that was saved
 */
function WBC3_save_feed_option( $post_id, $option_name, $nonce_name, $nonce_action = '', $default_value = '', $callback = 'sanitize_text_field' ) {

	// Grab the value from the $_POST object, or fallback to the default value
	if ( function_exists( $callback ) )
		$value = isset( $_POST[$option_name] ) ? call_user_func( $callback, $_POST[$option_name] ) : $default_value;
	else
		$value = isset( $_POST[$option_name] ) ? sanitize_text_field( $_POST[$option_name] ) : $default_value;

	// If nonce wasn't posted, exit out
	if ( !isset( $_POST[$nonce_name] ) )
		return;

	// set the nonce action to the option name if its empty
	$nonce_action = empty( $nonce_action ) ? $option_name : $nonce_action;

	// Verify the nonce
	if ( !wp_verify_nonce( $_POST[$nonce_name], $nonce_action ) )
		return;

	// If we have a valid value, save it, else delete
	if ( $value )
		update_post_meta( $post_id, $option_name, $value );
	else
		delete_post_meta( $post_id, $option_name );

	return $value;
}