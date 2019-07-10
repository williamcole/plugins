<?php

/**
 * Image Poll
 *
 * Allows for some custom options for adding UGC poll to interactive post template.
 *
 */
 

/*
 * ADMIN OPTIONS
 */

// add some additional custom fields for image poll
function WBC3_imagepoll_post_options( $options ) {

	$options['post']['panels']['WBC3_imagepoll'] = array(
		'label' => 'Image Poll',
		'fields' => array(			
			'WBC3_imagepoll_display_poll' => array(
				'type' => 'checkbox',
				'label' => 'Display Image Poll',
				'help' => 'ON/OFF Switch. Check to enable poll on this post.',
			),
			'WBC3_imagepoll_is_company100' => array(
				'type' => 'checkbox',
				'label' => 'COMPANY 100 Image Poll',
				'help' => 'Check to enable custom styling and functionality for COMPANY 100 poll.',
			),			
			'WBC3_imagepoll_ids' => array(
				'type' => 'textarea',
				'label' => 'Image Poll IDs',
				'help' => 'Enter a comma-separated list of IDs from the UGC poll tool.',
			),
			'WBC3_imagepoll_banner' => array(
				'label' => 'Poll Banner Image URL',
				'help' => 'Enter an image URL for the banner at the top of the poll. URL must be on a valid COMPANY.com domain.',
			),
			'WBC3_imagepoll_banner_480_breakpoint' => array(
				'label' => 'Poll Banner Image URL (480 Breakpoint)',
				'help' => 'Enter an image URL for the 480 breakpoint banner at the top of the poll. URL must be on a valid COMPANY.com domain.',
			),
			'WBC3_imagepoll_clickthrough_url' => array(
				'type' => 'url',
				'label' => 'Poll Banner Clickthrough URL',
				'help' => 'Enter a clickthrough URL for the poll banner.',
			),
			'WBC3_imagepoll_title' => array(
				'label' => 'Poll Title',
				'help' => 'Enter a title text for the poll.',
			),
			'WBC3_imagepoll_deck' => array(
				'label' => 'Poll Deck',
				'help' => 'Enter a deck text for the poll.',
			),
			'WBC3_imagepoll_facebook_share_language' => array(
				'type' => 'textarea',
				'label' => 'Facebook Share Language',
				'help' => 'Enter a Facebook share message for this poll. You can use %title% to represent the title.',
			),
			'WBC3_imagepoll_facebook_xid' => array(
				'label' => 'Facebook XID Parameter',
				'help' => 'Enter an XID parameter to be appended to all shared links on Facebook.',
			),
			'WBC3_imagepoll_twitter_share_language' => array(
				'type' => 'textarea',
				'label' => 'Twitter Share Language',
				'help' => 'Enter a Twitter share message for this poll. You can use %title% to represent the title.',
			),
			'WBC3_imagepoll_twitter_xid' => array(
				'label' => 'Twitter XID Parameter',
				'help' => 'Enter an XID parameter to be appended to all shared links on Twitter.',
			),			
			'WBC3_imagepoll_twitter_hashtag' => array(
				'label' => 'Twitter Hashtag',
				'help' => 'Enter a Twitter hashtag to be appended to all re-tweets.',
			),
			'WBC3_imagepoll_end_title' => array(
				'label' => 'End Slide Title',
				'help' => 'Enter a title text for the end slide of the poll.',
			),
			'WBC3_imagepoll_end_subtitle' => array(
				'label' => 'End Slide Subtitle',
				'help' => 'Enter a subtitle text for the end slide of the poll.',
			),
			'WBC3_imagepoll_end_deck' => array(
				'label' => 'End Slide Deck',
				'help' => 'Enter a deck text for the end slide of the poll.',
			),
			'WBC3_imagepoll_end_image' => array(
				'label' => 'End Slide Image URL',
				'help' => 'Enter an image URL for the end slide of the poll. URL must be on a valid domain.',
			),
			'WBC3_imagepoll_closed_message' => array(
				'label' => 'Poll Closed Overlay Message',
				'help' => 'Enter text to be displayed when poll is closed.',
				'default' => 'This matchup is closed.'
			),
		)
	);
	
	return $options;
	
}
add_filter( 'WBC3_post_options', 'WBC3_imagepoll_post_options' );

// check whether or not to display poll
function WBC3_imagepoll_display_poll() {
	
	global $post;	
	$bool = true;
	
	// make sure the switch is on
	if( !get_post_meta( $post->ID, 'WBC3_imagepoll_display_poll', true ) ) {
		$bool = false;
	}
	
	// make sure there are poll ids
	if( !WBC3_imagepoll_ids() ) {
		$bool = false;
	}
	
	return $bool;
}

// check whether or not this is a COMPANY 100 poll
function WBC3_imagepoll_is_company100() {
	
	global $post;	
	$bool = false;
	
	if( get_post_meta( $post->ID, 'WBC3_imagepoll_is_company100', true ) ) {
		$bool = true;
	}
	
	return $bool;
}

// filter to add body class when on a COMPANY 100 poll
function WBC3_imagepoll_body_class( $classes ) {
	$classes[] = 'company100-image-poll';	
	return $classes;
}

// only run filters if COMPANY 100 poll is set to display
function WBC3_imagepoll_init() {
	if( WBC3_imagepoll_display_poll() && WBC3_imagepoll_is_company100() && !is_feed() ) {
		add_filter( 'body_class', 'WBC3_imagepoll_body_class' );
		add_filter( 'wp_footer', 'WBC3_imagepoll_facebook_footer' );
	}
}
add_action( 'wp_head', 'WBC3_imagepoll_init' );

// make sure user has entered poll ids
function WBC3_imagepoll_ids() {

	global $post;
	
	// get ids
	$ids = get_post_meta( $post->ID, 'WBC3_imagepoll_ids', true );
	
	// convert ids into an array
	$ids = explode( ',', str_replace( ' ', '', $ids ) );
	
	if( !empty( $ids ) ) {
		return (array) $ids;
	}
}

// include custom poll CSS and JS
function WBC3_imagepoll_enqueue_scripts() {
	if( WBC3_imagepoll_display_poll() ) {
		wp_enqueue_style( 'company-imagepoll', WBC3_CSS_URL . '/image-poll.css' );
		wp_enqueue_script( 'company-imagepoll', WBC3_JS_URL . '/image-poll.min.js', null, false, false );
	}
}
add_action( 'wp_enqueue_scripts', 'WBC3_imagepoll_enqueue_scripts' );

// add facebook frictionless sharing to footer
function WBC3_imagepoll_facebook_footer() {
	?>
	<div id="fb-root"></div>
	<script type="text/javascript">
		window.fbAsyncInit = function() {
			FB.init({appId: '53177223193', status: true, cookie: true, xfbml: true});
		};
	</script>	
	<?php
}



/*
 * SHORTCODE
 */

function WBC3_imagepoll_shortcode( $atts ) {

	global $post, $WBC3_settings;
	
	if( !WBC3_imagepoll_display_poll() )
		return;
	
	?>
	
	<!-- Image Poll requires jQuery Library v1.6.1 in order to work-->
	<script type="text/javascript" src="http://img.company.net/company/www/j/jquery.js"></script>
	<script type="text/javascript">
	
		$(document).ready(function() {
			
			// set poll vars
			COMPANY.ImagePoll.poll_is_company100 = '<?php echo esc_js( get_post_meta( $post->ID, 'WBC3_imagepoll_is_company100', true ) ); ?>';
			COMPANY.ImagePoll.poll_url = '<?php the_permalink(); ?>';
			COMPANY.ImagePoll.poll_title = '<?php echo esc_js( str_replace( "'", "\'", get_post_meta( $post->ID, 'WBC3_imagepoll_title', true ) ) ); ?>';
			COMPANY.ImagePoll.poll_deck = '<?php echo esc_js( str_replace( "'", "\'", get_post_meta( $post->ID, 'WBC3_imagepoll_deck', true ) ) ); ?>';
			COMPANY.ImagePoll.poll_facebook_share_language = '<?php echo esc_js( get_post_meta( $post->ID, 'WBC3_imagepoll_facebook_share_language', true ) ); ?>';
			COMPANY.ImagePoll.poll_facebook_xid = '<?php echo esc_js( get_post_meta( $post->ID, 'WBC3_imagepoll_facebook_xid', true ) ); ?>';
			COMPANY.ImagePoll.poll_twitter_share_language = '<?php echo esc_js( get_post_meta( $post->ID, 'WBC3_imagepoll_twitter_share_language', true ) ); ?>';
			COMPANY.ImagePoll.poll_twitter_xid = '<?php echo esc_js( get_post_meta( $post->ID, 'WBC3_imagepoll_twitter_xid', true ) ); ?>';			
			COMPANY.ImagePoll.poll_twitter_hashtag = '<?php echo esc_js( get_post_meta( $post->ID, 'WBC3_imagepoll_twitter_hashtag', true ) ); ?>';
			COMPANY.ImagePoll.poll_twitter_handle = '<?php echo esc_js( $WBC3_settings['twitter'] ); ?>';
			
			// set end slide vars
			COMPANY.ImagePoll.poll_end_title = '<?php echo esc_js( str_replace( "'", "\'", get_post_meta( $post->ID, 'WBC3_imagepoll_end_title', true ) ) ); ?>';
			COMPANY.ImagePoll.poll_end_subtitle = '<?php echo esc_js( str_replace( "'", "\'", get_post_meta( $post->ID, 'WBC3_imagepoll_end_subtitle', true ) ) ); ?>';
			COMPANY.ImagePoll.poll_end_deck = '<?php echo esc_js( str_replace( "'", "\'", get_post_meta( $post->ID, 'WBC3_imagepoll_end_deck', true ) ) ); ?>';
			COMPANY.ImagePoll.poll_end_image = '<?php echo esc_js( get_post_meta( $post->ID, 'WBC3_imagepoll_end_image', true ) ); ?>';
			COMPANY.ImagePoll.poll_closed_message = '<?php echo esc_js( str_replace( "'", "\'", get_post_meta( $post->ID, 'WBC3_imagepoll_closed_message', true ) ) ); ?>';
			
			// initialize first slide on page load
			COMPANY.ImagePoll.init();
		
		});
	
	</script>
	
	<?php

	$html = '';	

	$clickthrough_url = esc_url( get_post_meta( $post->ID, 'WBC3_imagepoll_clickthrough_url', true ) );
	
	// banner image
	if( get_post_meta( $post->ID, 'WBC3_imagepoll_banner', true ) ) {
		if( !empty( $clickthrough_url ) )
			$html .= '<div class="imagepoll-banner"><a href="'.esc_url( $clickthrough_url ).'"><img src="'.esc_url( get_post_meta( $post->ID, 'WBC3_imagepoll_banner', true ) ).'" width="100%" border="0"></a></div>';
		else
			$html .= '<div class="imagepoll-banner"><img src="'.esc_url( get_post_meta( $post->ID, 'WBC3_imagepoll_banner', true ) ).'" width="100%" border="0"></div>';
	}
	
	// 480 banner image
	if( get_post_meta( $post->ID, 'WBC3_imagepoll_banner_480_breakpoint', true ) ) {
		if( !empty( $clickthrough_url ) )
			$html .= '<div class="imagepoll-banner-480"><a href="'.esc_url( $clickthrough_url ).'"><img src="'.esc_url( get_post_meta( $post->ID, 'WBC3_imagepoll_banner_480_breakpoint', true ) ).'" width="100%" border="0"></a></div>';
		else
			$html .= '<div class="imagepoll-banner-480"><img src="'.esc_url( get_post_meta( $post->ID, 'WBC3_imagepoll_banner_480_breakpoint', true ) ).'" width="100%" border="0"></div>';
	}
	
	// construct poll html
	$html .= '<div id="poll-container">';
	$html .= 	'<div class="pollarea">';
	
	// title text
	$html .= 		'<h1>' . esc_attr( get_post_meta( $post->ID, 'WBC3_imagepoll_title', true ) ) . ' <span class="vote">Vote Now!</span></h1>';
	
	// share buttons
	$html .=		'<div class="entry-sharing group">
						<ul class="entry-share-buttons">
							<li>
								<a href="#">Share</a>
								<ul>
									<li class="share-fb" data-omniture-event="fb-like">
										<div class="fb-like" data-send="false" data-href="'.get_permalink().'" data-layout="button_count" data-width="450" data-show-faces="false"></div>
									</li><!--/.share-fb-->
					
									<li class="share-tw" data-omniture-event="tweet">
										<a href="https://twitter.com/share" class="twitter-share-button" data-url="'.wp_get_shortlink().'" data-counturl="'.get_permalink().'" data-text="'.esc_attr( WBC3_tweet_text() ).'" data-via="'.esc_attr( WBC3_tweet_via() ).'" data-count="horizontal"></a>
									</li><!--/.share-tw-->
					
									<li class="share-gp" data-omniture-event="google+">
										<g:plusone size="medium" href="'.get_permalink().'" callback="plusone_vote"></g:plusone>
									</li><!--/.share-gp-->
								</ul>
							</li>				
						</ul>
					</div><!--/.entry-sharing-group-->';
	
	// poll content gets injected into this div
	$html .=		'<div id="polls"></div>';
	
	// close html
	$html .= 	'</div><!--/.pollarea-->';
	$html .=	'<div id="poll-after"></div>';
	$html .= '</div><!--/#poll-container-->';
	
	// output poll ids
	$html .= '<script type="text/javascript">COMPANY.ImagePoll.poll_array = [';
	
		$i = 0;
		foreach( WBC3_imagepoll_ids() as $id ) {
			if( $i !== 0 ) $html .= ',';
			$html .= '\''.$id.'\'';
			$i++;
		}
	
	$html .= '];</script>';
	
	// clear everything
	$html .= '<div style="clear:both"></div>';
	
	return $html;
	
}
add_shortcode( 'image-poll', 'WBC3_imagepoll_shortcode' );

?>