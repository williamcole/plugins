<?php

/**
 * Polldaddy
 *
 * Allows for some custom options when adding polls to packages.
 *
 */
 
# TODO - Future Enhancement
# use Polldaddy API to create polls for all slides in this package, so we dont have to do it manually
	
// define some poll variables
define( 'POLL_API_HOST', 'https://api.polldaddy.com/' );
define( 'POLL_API_KEY', '#' );
define( 'POLL_API_USERCODE', '#' );
define( 'POLL_IS_SSL', false );

/*
 * POLL INIT
 */

// check whether or not to display polls
function WBC3_polldaddy_display_polls() {
	global $post;
	if( get_post_meta( $post->ID, 'WBC3_poll_package_display_polls', true ) ) {
		return true;
	} else {
		return false;	
	}
}

// only run filters if polls are set to display
function WBC3_polldaddy_init() {
	if( WBC3_polldaddy_display_polls() && !is_feed() ) {
		add_filter( 'body_class', 'WBC3_polldaddy_body_class' );
		add_filter( 'wp_footer', 'WBC3_polldaddy_facebook_footer' );
	}
}
add_action( 'wp_head', 'WBC3_polldaddy_init' );

// include custom poll CSS and JS
function WBC3_polldaddy_enqueue_scripts() {
	if( WBC3_polldaddy_display_polls() ) {
		wp_enqueue_style( 'WBC3-polldaddy', WBC3_CSS_URL . '/polldaddy.css' );
		wp_enqueue_script( 'jquery-tablesorter', WBC3_JS_URL . '/libs/plugins/jquery.tablesorter.min.js', null, false, true );
		wp_enqueue_script( 'WBC3-polldaddy', WBC3_JS_URL . '/polldaddy.min.js', null, false, true );
	}
}
add_action( 'wp_enqueue_scripts', 'WBC3_polldaddy_enqueue_scripts' );

// filter to add body class when on a slide with a poll
function WBC3_polldaddy_body_class( $classes ) {
	$classes[] = 'has-poll';	
	return $classes;
}

/*
 * ADMIN OPTIONS
 */

// add an additional custom field for the Polls on package slides
function WBC3_polldaddy_package_options( $options ) {

	$polls = $options['special']['panels']['polls'] = array(
		'label' => 'Polls',
		'fields' => array(
			'WBC3_poll_package_display_polls' => array(
				'type' => 'checkbox',
				'label' => 'Display Polls',
				'help' => 'Check to enable polls on this package.',
			),
			'WBC3_poll_package_yes_language' => array(
				'label' => 'Poll Yes Language',
				'default' => 'Yes',
				'help' => 'Select the language to be displayed for "Yes" answer (i.e. Absolutely). This must match the language used for each slide poll.',
			),
			'WBC3_poll_package_no_language' => array(
				'label' => 'Poll No Language',
				'default' => 'No',
				'help' => 'Select the language to be displayed for "No" answer (i.e. No Way). This must match the language used for each slide poll.',
			),
			'WBC3_poll_package_results_url' => array(
				'label' => 'Poll Results URL',
				'help' => 'Enter the URL to the Poll Results post or slide.',
			),
			'WBC3_poll_package_results_display_type' => array(
				'type' => 'select',
				'label' => 'Poll Results Display Type',
				'options' => array(
					'vote' => 'Votes',
					'percent' => 'Percentages',
				),
				'default' => 'vote',
				'help' => 'Select what type of results to display.',
			),
			'WBC3_poll_package_sort_by_last_name' => array(
				'type' => 'checkbox',
				'label' => 'Sort Results By Last Name',
				'help' => 'Check to sort poll slides by last name. This can be overridden on the slide level.',
			),
			'WBC3_poll_package_facebook_subtitle' => array(
				'label' => 'Facebook Subtitle',
				'help' => 'Enter a subtitle to be displayed below article title on Facebook shares.',
			),
			'WBC3_poll_package_facebook_xid' => array(
				'label' => 'Facebook XID Parameter',
				'default' => 'poll-fb',
				'help' => 'Enter the XID parameter to be appended to all shared links on Facebook.',
			),
			'WBC3_poll_package_twitter_xid' => array(
				'label' => 'Twitter XID Parameter',
				'default' => 'poll-tweet',
				'help' => 'Enter the XID parameter to be appended to all shared links on Twitter.',
			),			
			'WBC3_poll_package_twitter_hashtag' => array(
				'label' => 'Twitter Hashtag',
				'help' => 'Enter a Twitter hashtag to be appended to all re-tweets.',
			),
			'WBC3_poll_package_tweet_text_yes' => array(
				'type' => 'textarea',
				'label' => 'Dynamic Tweet Message for Yes Vote',
				'help' => 'Set the default "Yes" tweet message for this poll. You can use %title% to represent the slide title.'
			),
			'WBC3_poll_package_tweet_text_no' => array(
				'type' => 'textarea',
				'label' => 'Dynamic Tweet Message for No Vote',
				'help' => 'Set the default "No" tweet message for this poll. You can use %title% to represent the slide title.'
			),
		)
	);
	
	return $options;
	
}
add_filter( 'WBC3_package_options', 'WBC3_polldaddy_package_options' );

// add an additional custom field for the Polls on package slides
function WBC3_polldaddy_slide_options( $options ) {

	$polls = $options['slide']['panels']['polls'] = array(
		'label' => 'Polls',
		'fields' => array(
			'WBC3_poll_slide_poll_id' => array(
				'label' => 'Slide Poll ID',
				'help' => 'Enter PollDaddy ID for this slide.',
				'validate' => 'intval'
			),
			'WBC3_poll_slide_title_sort' => array(
				'label' => 'Slide Poll Title Sort',
				'help' => 'Enter the text to sort this slide by. Helpful when you want to sort by last name.',
			),
			'WBC3_poll_slide_tweet_text_yes' => array(
				'type' => 'textarea',
				'label' => 'Custom Tweet Message for Yes Vote',
				'help' => 'Set a custom "Yes" tweet message for this slide.'
			),
			'WBC3_poll_slide_tweet_text_no' => array(
				'type' => 'textarea',
				'label' => 'Custom Tweet Message for No Vote',
				'help' => 'Set a custom "No" tweet message for this slide.'
			),
		)
	);
	
	return $options;
	
}
add_filter( 'WBC3_slide_options', 'WBC3_polldaddy_slide_options' );

/*
 * HELPER FUNCTIONS
 */

// check if the poll is closed
function WBC3_polldaddy_is_poll_closed() {

	// check if slide has a polldaddy id
	$poll_id = WBC3_polldaddy_get_slide_poll_id();
	
	if( $poll_id ) {
		$poll_data = WBC3_polldaddy_api_get_poll_data( $poll_id );
	}
	
	// check if poll has a close date
	if( $poll_data->closePoll == 'yes' ) {
		$todays_date = date( "Y-m-d H:i:s" );
		$poll_close_date = $poll_data->closeDate;
	}
	
	// check if poll close date has not occurred
	if( isset( $poll_close_date ) && ( $poll_close_date < $todays_date ) ) {
		return true;
	} else {
		return false;
	}	
}

// display the appropriate poll header text
function WBC3_polldaddy_header_text() {
	if( WBC3_polldaddy_is_poll_closed() ) {
		$poll_header_text =  'Poll Results';
	} else {
		$poll_header_text = 'What do you think?';
	}	
	return $poll_header_text;
}

// get package id of a slide
function WBC3_polldaddy_get_package_id( $slide_id ) {

	global $post;
	
	// get slide id
	$slide_id = ( isset( $slide_id ) ) ? $slide_id : $post->ID;
	
	$parent_ids = get_post_ancestors( $slide_id );
	$package_id = end( $parent_ids );

	return $package_id;
	
}

// query filters for getting our slide data
function WBC3_polldaddy_posts_clauses( $query ) {
	
	global $wpdb, $WBC3_polldaddy_package_id;

	if( $WBC3_polldaddy_package_id ) {
		$query['orderby'] = "h.menu_order, $wpdb->posts.menu_order ASC";
		$query['join'] = $wpdb->prepare( "LEFT JOIN $wpdb->posts AS h ON h.post_parent = %d", $WBC3_polldaddy_package_id );
		$query['where'] .= " AND $wpdb->posts.post_parent = h.ID";
	}

	return $query;

}

// get the slide poll id
function WBC3_polldaddy_get_slide_poll_id( $slide_id = null ) {
	
	global $post;
	
	// get slide id
	$slide_id = ( isset( $slide_id ) ) ? $slide_id : $post->ID;
	
	if( !$slide_id )
		return;
	
	// check if slide has a polldaddy id
	$poll_id = get_post_meta( $slide_id, 'WBC3_poll_slide_poll_id', true );
	
	// output
	if( isset( $poll_id ) ) {
		return $poll_id;
	} else {
		return false;
	}

}

// get the slide poll question
// currently deprecated in favor of custom slide title language
function WBC3_polldaddy_get_slide_poll_question() {

	// check if slide has a polldaddy id
	$poll_id = WBC3_polldaddy_get_slide_poll_id();
	
	if( $poll_id ) {
		$poll_data = WBC3_polldaddy_api_get_poll_data( $poll_id );
		return $poll_data->question;
	} else {
		return false;
	}

}

// get poll IDs from all slides in package
function WBC3_polldaddy_get_package_slides( $package_id = null ) {

	global $post, $WBC3_polldaddy_package_id;

	if ( !$package_id ) {
		$package_id = $post->ID;
	}

	if( !$package_id )
		return;

	$WBC3_polldaddy_package_id = $package_id; // global that we can use in our post clauses callback

	// define array for poll IDs
	$package_slides = array();
	
	// get all slides under this post
	add_filter( 'posts_clauses', 'WBC3_polldaddy_posts_clauses' );
			
	$slides = get_posts( array(
		'post_status' => is_preview() ? 'any' : 'publish',
		'post_type' => 'WBC3_slide',
		'posts_per_page' => 365,
		'fields' => 'ids',
		'suppress_filters' => false,
	) );
	
	remove_filter( 'posts_clauses', 'WBC3_polldaddy_posts_clauses' );

	// get all slide poll IDs
	foreach( $slides as $slide ) {
	
		// get poll id
		$poll_id = WBC3_polldaddy_get_slide_poll_id( $slide );
		$slide_post = get_post( $slide );
		
		// make sure we pull only pull slides from this package
		if( !empty( $poll_id ) ) {
			$package_slides[] = array(
				'poll_id' => $poll_id,
				'slide_id' => $slide,
			);
		}
	
	}

	$WBC3_polldaddy_package_id = null; // reset this since we don't need it anymore

	return $package_slides;

}

// replicate the polldaddy shortcode for this slide
function WBC3_polldaddy_get_shortcode() {
	
	// check if slide has a polldaddy id
	$poll_id = WBC3_polldaddy_get_slide_poll_id();
		
	// output
	if( $poll_id ) {
		$shortcode = '[polldaddy poll="' . $poll_id . '"]';
		return $shortcode;
	} else {
		return false;
	}

}

// prepend the content with the slide poll
function WBC3_polldaddy_show_slide_poll( $content ) {

	global $post;
	
	// get polldaddy shortcode
	$poll_shortcode = WBC3_polldaddy_get_shortcode();
	
	if( !empty( $poll_shortcode ) ) {
	
		// set some package variables
		$package_id = WBC3_polldaddy_get_package_id( $post->ID );
		$package_link = get_permalink( $package_id );
		$package_title = get_the_title( $package_id );
		
		// poll results link
		$poll_results_link = get_post_meta( $package_id, 'WBC3_poll_package_results_url', true );
		
		// set some slide variables
		$slide_link = get_permalink( $post->ID );
		$slide_title = strip_tags( get_the_title( $post->ID ) );
		$slide_images = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
		$slide_image = $slide_images[0];
		
		// construct poll html
		$poll_html = '';
		$poll_html .= '<div class="slide-poll">';
		
		// check for twitter hashtag
		$poll_twitter_hashtag = get_post_meta( $package_id, 'WBC3_poll_package_twitter_hashtag', true );
		if( $poll_twitter_hashtag ) {
			$twitter_hash = ' ' . $poll_twitter_hashtag;
		} else {
			$twitter_hash = '';
		}
		
		// set JS vars for each slide
		$poll_html .= '<script type="text/javascript">
			window.POLLDATA = {
				slideTitle: "'. esc_js( $slide_title ) .'",
				articleUrl: "'. esc_js( $slide_link ) .'",
				yesOpt: "'. esc_js( get_post_meta( $package_id, 'WBC3_poll_package_yes_language', true ) ) .'",
				noOpt: "'. esc_js( get_post_meta( $package_id, 'WBC3_poll_package_no_language', true ) ) .'",
				twitterXID: "'. esc_js( get_post_meta( $package_id, 'WBC3_poll_package_twitter_xid', true ) ) .'",
				twitterHash: "'. esc_js( $twitter_hash ) .'",
				twitterVotedYesMsg: "'. esc_js( WBC3_polldaddy_get_tweet_text( $package_id, $option = 'yes', '%slide_title%' ) ) .'",
				twitterVotedNoMsg: "'. esc_js( WBC3_polldaddy_get_tweet_text( $package_id, $option = 'no', '%slide_title%' ) ) .'",
				facebookXID: "'. esc_js( get_post_meta( $package_id, 'WBC3_poll_package_facebook_xid', true ) ) .'",
				facebook: {
					fb_headline: "'.esc_js( $slide_title.' | '.$package_title ).'", 
					fb_abbr_article: "'. esc_js( get_post_meta( $package_id, 'WBC3_poll_package_facebook_subtitle', true ) ) .'",
					fb_description: "", 					
					fb_image: "'.esc_js( $slide_image ).'",
					fb_url: "'. esc_js( get_permalink().'?xid='.get_post_meta( $package_id, 'WBC3_poll_package_facebook_xid', true ) ).'"
				}
			}
		</script>';
		
		// poll header
		$poll_html .= '<h3 class="poll-header">'.WBC3_polldaddy_header_text().'</h3>';		
		$poll_html .= '<div class="slide-poll-wrap">';
		
		// poll question
		$current_poll_id = get_post_meta( $post->ID, 'WBC3_poll_slide_poll_id', true );
		$poll_stuff = WBC3_polldaddy_api_get_poll_data( $current_poll_id );
		$poll_html .= '<div class="slide-poll-title">' . esc_html( $poll_stuff->question ) . '</div>';
		
		// share buttons
		$poll_html .= '<div class="poll-share-box">';
		$poll_html .= ' <em>Post results on</em>';
		$poll_html .= ' <span class="fb-share"><img alt="facebook" src="http://img.company.net/company/rd/trunk/www/web/feds/i/polldaddy-fb.png"></span>';
		$poll_html .= ' <span class="twt-share"><img alt="twitter" src="http://img.company.net/company/rd/trunk/www/web/feds/i/polldaddy-twt.png"></span>';
		$poll_html .= '</div>';
		
		// if poll is closed, use API to get results
		if( WBC3_polldaddy_is_poll_closed() ) {
			
			// get closed poll results
			$closed_poll_results = WBC3_polldaddy_api_get_poll_results( $current_poll_id );			
			$poll_yes = $closed_poll_results[0];
			$poll_no = $closed_poll_results[1];
			
			// generate poll results markup
			if( isset( $poll_yes ) && isset( $poll_no ) ) {
				$poll_html .= '
					<div class="pds-answer">
						<div class="pds-feedback-group">
							<label class="pds-feedback-label">
								<span class="pds-answer-text"> ' . esc_html( $poll_yes->text ). ' </span>
								<span class="pds-feedback-result">
									<span class="pds-feedback-per">&nbsp;' . intval( round( $poll_yes->percent ) ) . '%</span>
								</span>
							</label>
						</div>
						<div class="pds-feedback-group">						
							<label class="pds-feedback-label">
								<span class="pds-answer-text"> ' . esc_html( $poll_no->text ) . ' </span>
								<span class="pds-feedback-result">
									<span class="pds-feedback-per">&nbsp;' . intval( round( $poll_no->percent ) ). '%</span>
								</span>
							</label>
						</div>
					</div>
				';
			}		
		
		} else {
			
			// if poll is open, use polldaddy shortcode
			$poll_html .= $poll_shortcode;
		
		}
		
		// results link
		if( !empty( $poll_results_link ) ) {
			$poll_html .= '<div class="poll-footer">';
			$poll_html .= '<div class="poll-results-link">';
			$poll_html .= ' <a href="'.$poll_results_link.'">See All Poll Results</a>';
			$poll_html .= '</div>';
			$poll_html .= '<div class="poll-clear"></div>';
			$poll_html .= '</div><!-- .poll-footer -->';
		}
		
		// close html
		$poll_html .= '</div><!-- .slide-poll-wrap -->';
		$poll_html .= '</div><!-- .slide-poll -->';
		
		// prepend poll content
		$content = $poll_html . $content;
		
	}
	
	// output
	return $content;

}
add_filter( 'the_content', 'WBC3_polldaddy_show_slide_poll' );

/*
 * RESULTS
 */

// add a shortcode to display poll results for a specific package
function WBC3_polldaddy_results_shortcode( $atts, $content = null ) {

	// dont output anything in feeds
	if( is_feed() ) return '';

	$defaults = array(
		'id' => null,
	);
	
	extract( shortcode_atts( $defaults, $atts ) );
	
	$package_id = ( intval( $id ) ) ? $id : false;
	
	if( !$package_id )
		return;
	
	$html = WBC3_polldaddy_results( $package_id );
	
	return $html;
	
}
add_shortcode( 'poll-results', 'WBC3_polldaddy_results_shortcode' );

// create the html for our custom poll results
function WBC3_polldaddy_results( $package_id = null ) {

	// get package id
	$package_id = ( isset( $package_id ) ) ? $package_id : false;
	
	if( !$package_id )
		return;
	
	// define new array
	$poll_results_rows = array();
	
	// get all slide poll IDs
	$package_slides = WBC3_polldaddy_get_package_slides( $package_id );
	
	foreach( $package_slides as $slide ) {
		
		// get slide and poll IDs
		$slide_id = $slide['slide_id'];
		$poll_id = $slide['poll_id'];
		
		// get poll data
		$poll_data = WBC3_polldaddy_api_get_poll_data( $poll_id );
		$poll_data_answers = $poll_data->answers;
		
		// determine answer ids so we can display properly
		$yes_id = $poll_data_answers->answer[0]->id;
		$no_id = $poll_data_answers->answer[1]->id;		
		
		// get poll results
		$poll_results = WBC3_polldaddy_api_get_poll_results( $poll_id );
		$poll_results_type = get_post_meta( $package_id, 'WBC3_poll_package_results_display_type', true );
		
		if( $poll_results_type == 'percent' ) {
			// percentages
			$poll_results_yes_field = 'yes_percent';
			$poll_results_no_field = 'no_percent';			
		} else {
			// total votes
			$poll_results_yes_field = 'yes_votes';
			$poll_results_no_field = 'no_votes';
		}
		
		// if no answer is displayed first, switch the two arrays
		if( $yes_id == $poll_results[1]->id ) {
			// no, yes
			$yes_result = $poll_results[1];
			$no_result = $poll_results[0];
		} else {
			// yes, no
			$yes_result = $poll_results[0];
			$no_result = $poll_results[1];
		}
		
		// add values to results row
		$poll_results_rows[] = array(
			'slide_id' => $slide_id,			
			'poll_id' => $poll_id,
			'yes_id' => $yes_id,
			'yes_percent' => round( $yes_result->percent ) . '%',
			'yes_votes' => number_format( $yes_result->total ),
			'no_id' => $no_id,
			'no_percent' => round( $no_result->percent ) . '%',
			'no_votes' => number_format( $no_result->total ),
		);
		
	}
	
	// construct html
	$html = '';
	
	// load results JS function
	$html .= '<script type="text/javascript"> poll_results_sorting(); </script>';
	
	// results html markup
	$html .= '<div id="polldaddy-results">';
	$html .= '<table id="most-popular-results" class="results-table" sortbylastname="'.esc_attr( get_post_meta( $package_id, 'WBC3_poll_package_sort_by_last_name', true ) ).'">';
	$html .= '<thead>';
	$html .= '<tr>';	
	$html .= '	<th><span>Name</span></th>';
	$html .= '	<th><span>'.esc_html( get_post_meta( $package_id, 'WBC3_poll_package_yes_language', true ) ).'</span></th>';
	$html .= '	<th><span>'.esc_html( get_post_meta( $package_id, 'WBC3_poll_package_no_language', true ) ).'</span></th>';
	$html .= '</tr>';
	$html .= '</thead>';
	
	// by default sort results by yes column
	WBC3_polldaddy_array_sort_by_column( $poll_results_rows, $poll_results_yes_field );
	
	// loop through results, add new row
	foreach( $poll_results_rows as $result ) {
		$html .= '<tr sort="'.esc_attr( WBC3_polldaddy_get_sort_field( $package_id, $result['slide_id'] ) ).'">';
		$html .= '	<td class="result-title"><a href="'. get_permalink( $result['slide_id'] ) .'">' . get_the_title( $result['slide_id'] ) . '</a></td>';
		$html .= '	<td class="result-votes">' . esc_html( $result[$poll_results_yes_field] ) . '</td>';		
		$html .= '	<td class="result-votes">' . esc_html( $result[$poll_results_no_field] ) . '</td>';		
		$html .= '</tr>';
	}
	
	// close html
	$html .= '</table>';
	$html .= '</div>';
	$html .= '<div class="clear"></div>';
	
	return $html;
	
}

// helper function to determine sort field
function WBC3_polldaddy_get_sort_field( $package_id, $slide_id ) {

	if( !isset( $package_id ) || !isset( $slide_id ) )
		return;
	
	// if Sort By Last Name is checked, get last name from title string
	if( get_post_meta( $package_id, 'WBC3_poll_package_sort_by_last_name', true ) ) {
		$parts = explode( ' ', get_the_title( $slide_id ) );
		$sort_field = end( $parts );		
	}
	
	// override sort field on slide level
	if( get_post_meta( $slide_id, 'WBC3_poll_slide_title_sort', true ) ) {
		$sort_field = get_post_meta( $slide_id, 'WBC3_poll_slide_title_sort', true );
	}
	
	return $sort_field;

}

// helper function to sort results arrays
function WBC3_polldaddy_array_sort_by_column( &$arr, $col, $dir = SORT_DESC ) {
    $sort_col = array();
    foreach ($arr as $key=> $row) {
        $sort_col[$key] = $row[$col];
    }
    array_multisort($sort_col, $dir, $arr);
}

// helper function to get custom tweet text
function WBC3_polldaddy_get_tweet_text( $package_id, $option = 'yes', $slide_title ) {

	global $post;

	if( !$package_id )
		return;
		
	// get custom tweet text for package and slide
	$slide_tweet_text = get_post_meta( $post->ID, 'WBC3_poll_slide_tweet_text_'.$option, true );
	$package_tweet_text = get_post_meta( $package_id, 'WBC3_poll_package_tweet_text_'.$option, true );
	
	if( !empty( $slide_tweet_text ) ) {
	
		// use slide tweet text if set
		$tweet_text = $slide_tweet_text;
	
	} elseif( !empty( $package_tweet_text ) ) {
		
		// use dynamic package tweet text
		// replace %title% with actual slide title
		$tweet_text = str_replace( '%title%', $slide_title, $package_tweet_text );
	
	} else {
		
		// use default tweet text
		$tweet_text_prefix = ( $option == 'no' ) ? 'There is no way ' : 'I think ';
		$tweet_text = $tweet_text_prefix . $slide_title . ' should be in our ' . get_the_title( $package_id ) .' list. Vote now!';
		
	}
	
	return $tweet_text;
	
}

// add facebook frictionless sharing to footer
function WBC3_polldaddy_facebook_footer() {
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
 * API FUNCTIONS
 */

// get polldaddy usercode
function WBC3_polldaddy_api_get_usercode() {
	
	if( !defined( 'POLL_API_KEY' ) ) {
		return;
	}
	
	// construct request array
	$request_array = array(
		'pdAccess' => array(
			'partnerGUID' => POLL_API_KEY,
			'partnerUserID' => 0,
			'demands' => array(
				'demand' => array(
					'id' => 'GetUserCode'
				)
			)
		)
	);
	
	$request = json_encode( $request_array );
	$response = WBC3_polldaddy_api_do_request( $request );
	
	if( !isset( $response ) )
		return;
	
	$user_code = $response->pdResponse->userCode;
	
	if( !isset( $user_code ) )
		return;
		
	return $user_code;
			
}

// do API request to polldaddy
// caches the results so should not be used for write requests
function WBC3_polldaddy_api_do_request( $request ) {

	if( empty( $request ) || !defined( 'POLL_API_HOST' ) ) {
		return;
	}

	$response = wpcom_vip_file_get_contents( POLL_API_HOST, 3, 60, array(
		'http_api_args' => array(
			'headers' => array( 'Content-Type' => 'application/json; charset=utf-8', 'Content-Length' => strlen( $request ) ),
			'user-agent' => 'PollDaddy PHP Client/1.0',
			'body' => $request,
			'method' => 'POST',
		)
	) );
	
	if( !isset( $response ) )
		return;
	
	return json_decode( $response );
	
}

// get poll data
function WBC3_polldaddy_api_get_poll_data( $poll_id = null ) {
	
	// get poll id
	$poll_id = ( isset( $poll_id ) ) ? $poll_id : WBC3_polldaddy_get_slide_poll_id();
	
	// make sure Polldaddy usercode and poll id are set	
	if( !defined( 'POLL_API_USERCODE' ) || !isset( $poll_id ) ) {
		return;
	}
	
	// construct request array
	$request_array = array(
		'pdRequest' => array(
			'partnerGUID' => POLL_API_KEY,
			'userCode' => POLL_API_USERCODE,
			'demands' => array(
				'demand' => array(
					'poll' => array(
						'id' => $poll_id
					),
					'id' => 'GetPoll'
				)
			)
		)
	);
	
	$request = json_encode( $request_array );
	$response = WBC3_polldaddy_api_do_request( $request );
	
	if( !isset( $response ) )
		return;
	
	$poll_data = $response->pdResponse->demands->demand[0]->poll;
	
	if( !isset( $poll_data ) )
		return;
		
	return $poll_data;	
		
}

// get poll results
function WBC3_polldaddy_api_get_poll_results( $poll_id = null ) {
	
	// get poll id
	$poll_id = ( isset( $poll_id ) ) ? $poll_id : WBC3_polldaddy_get_slide_poll_id();
	
	// make sure Polldaddy usercode and poll id are set	
	if( !defined( 'POLL_API_USERCODE' ) || !isset( $poll_id ) ) {
		return 'invalid poll id';
	}
	
	// construct request array
	$request_array = array(
		'pdRequest' => array(
			'partnerGUID' => POLL_API_KEY,
			'userCode' => POLL_API_USERCODE,
			'demands' => array(
				'demand' => array(
					'poll' => array(
						'id' => $poll_id
					),
					'id' => 'GetPollResults'
				)
			)
		)
	);
	
	$request = json_encode( $request_array );
	$response = WBC3_polldaddy_api_do_request( $request );
	
	if( !isset( $response ) )
		return;
		
	if( isset( $response->pdResponse->errors ) )
		return;
	
	return $response->pdResponse->demands->demand[0]->result->answers->answer;
		
}

?>