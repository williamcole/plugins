<?php

/*
 * Newsletter Helper Functions
 */

// get vertical name
function WBC3_newsletter_vertical() {
	
	// get vertical from COMPANY settings
	global $WBC3_settings;
	$domain = $WBC3_settings['domain'];
	$domain = explode( '.', $domain );
	$vertical = strtolower( $domain[0] );	
	
	// output
	return $vertical;

}

// get vertical logo
function WBC3_newsletter_header_logo() {
	
	// get vertical
	$vertical = WBC3_newsletter_vertical();
	
	// set image src based on vertical
	$img_src = WBC3_newsletter_image( 'hdr_'.$vertical.'.png', $return = true );
	
	// set blog url and title
	$blog_url = WBC3_newsletter_xid( get_bloginfo( 'url' ), $return = true );
	$blog_name = get_bloginfo( 'title' );
	
	// get logo image based on vertical name
	switch( $vertical ) {
		
		case 'business':
			$width = 263;
			$height = 30;
			break;
		
		case 'entertainment':
			$width = 203;
			$height = 30;
			break;
		
		case 'healthland':
			$width = 224;
			$height = 30;
			break;
		
		case 'ideas':
			$width = 79;
			$height = 30;
			break;
		
		case 'keepingscore':
			$width = 97;
			$height = 30;
			break;
		
		case 'moneyland':
			$width = 95;
			$height = 32;
			break;
		
		case 'nation':
			$width = 57;
			$height = 30;
			break;
		
		case 'newsfeed':
			$width = 145;
			$height = 30;
			break;
		
		case 'science':
			$width = 242;
			$height = 30;
			break;
		
		case 'sports':
			$width = 97;
			$height = 30;
			break;
		
		case 'style':
			$width = 198;
			$height = 30;
			break;
				
		case 'swampland':
			$width = 163;
			$height = 30;
			break;
		
		case 'techland':
			$width = 65;
			$height = 30;
			break;
		
		case 'world':
			$width = 82;
			$height = 30;
			break;
			
	}
	
	// construct html
	$html = '';
	$html .= '<a href="'.esc_url( $blog_url ).'">';
	$html .= '<img src="'.esc_url( $img_src ).'" alt="'.esc_attr( $blog_name ).'" title="'.esc_attr( $blog_name ).'" ';
	
	// add width and height if they are set
	if( $width ) {
		$html .= 'width="'.intval( $width ).'" ';
	}
	if( $height ) {
		$html .= 'height="'.intval( $height ).'" ';
	}
	
	$html .= 'border="0">';
	$html .= '</a>';
	
	// output
	echo $html;
	
}

// generate html for footer
function WBC3_newsletter_footer() {

	// construct html
	$html = '';
	
	$html .= '<table cellpadding="0" cellspacing="0" border="0" width="728" bgcolor="#ed1c2e">';
	$html .= '	<tr>';
	$html .= '		<td width="10" bgcolor="#ed1c2e"><img src="'.WBC3_newsletter_image( 'spacer.gif', $return = true ).'" width="10" height="1" border="0" /></td>';
	$html .= '		<td width="708" bgcolor="#ed1c2e" colspan="3"><table cellpadding="0" cellspacing="0" border="0" width="708" bgcolor="#ed1c2e">';
	$html .= '			<tr>';
	$html .= '				<td colspan="6"><img src="'.WBC3_newsletter_image( 'spacer.gif', $return = true ).'" width="708" height="15" border="0" /></td>';
	$html .= '			</tr>';
	$html .= '			<tr>';
	$html .= '				<td colspan="6"><font face="georgia,serif;" style="color:#ffffff; font-weight:normal; font-size:17px;">Stay Connected with <a href="'.WBC3_newsletter_xid( 'http://www.company.com/', $return = true ).'" style="color:#ffffff; text-decoration:none">COMPANY.com</a></font></td>';
	$html .= '			</tr>';
	$html .= '			<tr>';
	$html .= '				<td colspan="6"><img src="'.WBC3_newsletter_image( 'spacer.gif', $return = true ).'" width="708" height="10" border="0" /></td>';
	$html .= '			</tr>';
	$html .= '			<tr>';
	$html .= '				<td width="104" valign="top"><table cellpadding="0" cellspacing="0" border="0" width="104">';
	$html .= '					<tr>';
	$html .= '						<td width="37"><a style="font-family:arial,sans-serif; font-size:11px; color:#ffffff; text-decoration:none;" href="'.WBC3_newsletter_xid( 'http://www.company.com/company/rss#rssfeeds/', $return = true ).'"><img src="http://img.company.net/company/www/i/icon_stay_connected_rss.png" alt="Icon for Subscribe to RSS Feeds" width="32" height="32" title="Subscribe to RSS Feeds" border="0" /></a></td>';
	$html .= '						<td width="67"><a style="font-family:arial,sans-serif; font-size:11px; color:#ffffff; text-decoration:none;" title="Get Feeds" href="'.WBC3_newsletter_xid( 'http://www.company.com/company/rss#rssfeeds/', $return = true ).'">Subscribe to <br />RSS Feeds</a></td>';
	$html .= '					</tr>';
	$html .= '				</table></td>';
	$html .= '				<td width="104" valign="top"><table cellpadding="0" cellspacing="0" border="0" width="104">';
	$html .= '					<tr>';
	$html .= '						<td width="37"><a style="font-family:arial,sans-serif; font-size:11px; color:#ffffff; text-decoration:none;" href="http://ebm.cheetahmail.com/r/regf2?a=0&amp;aid=1078532063&amp;n=1&amp;WBC3_SOURCE=newsletter-newsfeed;xid=newsletter-'.WBC3_newsletter_vertical( $return = true ).'"><img src="http://img.company.net/company/www/i/icon_stay_connected_nl.png" alt="Icon for Sign Up for Newsletters" width="32" height="32" title="Sign Up for Newsletters" border="0" /></a></td>';
	$html .= '						<td width="67"><a style="font-family:arial,sans-serif; font-size:11px; color:#ffffff; text-decoration:none;" target="_blank" href="http://ebm.cheetahmail.com/r/regf2?a=0&amp;aid=1078532063&amp;n=1&amp;WBC3_SOURCE=newsletter-newsfeed;xid=newsletter-'.WBC3_newsletter_vertical( $return = true ).'">Sign Up for <br/>Newsletters</a></td>';
	$html .= '					</tr>';
	$html .= '				</table></td>';
	$html .= '				<td width="149" valign="top"><table cellpadding="0" cellspacing="0" border="0" width="149">';
	$html .= '					<tr>';
	$html .= '						<td width="37"><a style="font-family:arial,sans-serif; font-size:11px; color:#ffffff; text-decoration:none;" href="'.WBC3_newsletter_xid( 'http://www.company.com/company/ipad/', $return = true ).'"><img src="http://img.company.net/company/www/i/icon_stay_connected_ipad.png" alt="Icon for Get the COMPANY Magazine iPad Edition" width="32" height="32" title="Get the COMPANY Magazine iPad Edition" border="0" /></a></td>';
	$html .= '						<td width="112"><a style="font-family:arial,sans-serif; font-size:11px; color:#ffffff; text-decoration:none;" target="_blank" href="'.WBC3_newsletter_xid( 'http://www.company.com/company/ipad/', $return = true ).'">Get the COMPANY <br/>Magazine iPad Edition</a></td>';
	$html .= '					</tr>';
	$html .= '				</table></td>';
	$html .= '				<td width="132" valign="top"><table cellpadding="0" cellspacing="0" border="0" width="132">';
	$html .= '					<tr>';
	$html .= '						<td width="37"><a style="font-family:arial,sans-serif; font-size:11px; color:#ffffff; text-decoration:none;" href="'.WBC3_newsletter_xid( 'http://www.company.com/company/mobile/', $return = true ).'"><img src="http://img.company.net/company/www/i/icon_stay_connected_mobile.png" alt="Icon for Read COMPANY Mobile on your Phone" width="32" height="32" title="Read COMPANY Mobile on your Phone" border="0" /></a></td>';
	$html .= '						<td width="95"><a style="font-family:arial,sans-serif; font-size:11px; color:#ffffff; text-decoration:none;" target="_blank" href="'.WBC3_newsletter_xid( 'http://www.company.com/company/mobile/', $return = true ).'">Read COMPANY Mobile <br/>on your Phone</a></td>';
	$html .= '					</tr>';
	$html .= '				</table></td>';
	$html .= '				<td width="102" valign="top"><table cellpadding="0" cellspacing="0" border="0" width="102">';
	$html .= '					<tr>';
	$html .= '						<td width="37"><a target="_blank" style="font-family:arial,sans-serif; font-size:11px; color:#ffffff; text-decoration:none;" href="http://www.facebook.com/company/"><img src="http://img.company.net/company/www/i/icon_stay_connected_fb.png" alt="Icon for Become a Fan of COMPANY" width="32" height="32" title="Become a Fan of COMPANY" border="0" /></a></td>';
	$html .= '						<td width="65"><a style="font-family:arial,sans-serif; font-size:11px; color:#ffffff; text-decoration:none;" target="_blank" href="http://www.facebook.com/company/">Become a <br/>Fan of COMPANY</a></td>';
	$html .= '					</tr>';
	$html .= '				</table></td>';
	$html .= '				<td width="117" valign="top"><table cellpadding="0" cellspacing="0" border="0" width="117">';
	$html .= '					<tr>';
	$html .= '						<td width="37"><a target="_blank" style="font-family:arial,sans-serif; font-size:11px; color:#ffffff; text-decoration:none;" href="http://twitter.com/#!/company/"><img src="http://img.company.net/company/www/i/icon_stay_connected_twitter.png" alt="Icon for Get COMPANY Twitter Updates" width="32" height="32" title="Get COMPANY Twitter Updates" border="0" /></a></td>';
	$html .= '						<td width="80"><a style="font-family:arial,sans-serif; font-size:11px; color:#ffffff; text-decoration:none;" target="_blank" href="http://twitter.com/#!/company/">Get COMPANY <br/>Twitter Updates</a></td>';
	$html .= '					</tr>';
	$html .= '				</table></td>';
	$html .= '			</tr>';
	$html .= '			<tr>';
	$html .= '				<td colspan="6"><table cellpadding="0" cellspacing="0" border="0" width="708" bgcolor="#ed1c2e">';
	$html .= '					<tr>';
	$html .= '						<td width="708" bgcolor="#ed1c2e"><img src="'.WBC3_newsletter_image( 'spacer.gif', $return = true ).'" width="708" height="15" border="0" /></td>';
	$html .= '					</tr>';
	$html .= '					<tr>';
	$html .= '						<td width="708" bgcolor="#ed1c2e"><font face="arial, sans-serif" style="color:#ffffff; font-size:11px">';
	$html .= '							<font style="color:#ffffff; font-weight:bold;">TO UNSUBSCRIBE</font> <br />';
	$html .= '							You have received this e-mail because you are subscribed to this newsletter from <a href="'.WBC3_newsletter_xid( 'http://www.company.com/', $return = true ).'" style="color:#ffffff;">COMPANY.com</a>.<br />';
	$html .= '							<a href="http://ebm.cheetahmail.com/r/webunsub?t=%%t%%&n=1&email=%%email%%" style="color:#ffffff;">Unsubscribe here.</a><br />';
	$html .= '							<br />';
	$html .= '							<font style="color:#ffffff; font-weight:bold;">EMAIL OPT-OUTS</font><br />';
	$html .= '							<a href="'.WBC3_newsletter_xid( 'http://www.company.com/emailprivacy/', $return = true ).'" style="color:#ffffff;">Click here</a> for more information on how to opt-out of marketing communications from us and our partners, or copy and paste this link into your browser: <a href="'.WBC3_newsletter_xid( 'http://www.company.com/emailprivacy/', $return = true ).'" style="color:#ffffff;">http://www.company.com/emailprivacy</a><br />';
	$html .= '							<br />';
	$html .= '							<font style="color:#ffffff; font-weight:bold;">PRIVACY POLICY</font><br />';
	$html .= '							Please read our <a href="'.WBC3_newsletter_xid( 'http://www.company.com/privacy/', $return = true ).'" style="color:#ffffff;">Privacy Policy</a>, or copy and paste this link into your browser: <a href="'.WBC3_newsletter_xid( 'http://www.company.com/privacy/', $return = true ).'" style="color:#ffffff;">http://www.company.com/privacy</a><br />';
	$html .= '							<br />';
	$html .= '							<font style="color:#ffffff; font-weight:bold;">FOR FURTHER COMMUNICATION, PLEASE CONTACT:</font><br />';
	$html .= '							COMPANY Customer Service<br />';
	$html .= '							3000 University Center Drive<br />';
	$html .= '							Tampa, FL 33612-6408<br />';
	$html .= '							<a href="http://www.timemediakit.com/" style="color:#ffffff;">How To Advertise</a> | <a href="https://subscription.company.com/storefront/subscribe-to-company/site/td-brightred-donor56for30.html?link=1002068&#038;xid=newsletter-'.WBC3_newsletter_vertical( $return = true ).'" style="color:#ffffff;">Give the Gift of COMPANY</a> | <a href="http://ebm.cheetahmail.com/r/webunsub?t=%%t%%&n=2&email=%%email%%" style="color:#ffffff;">Update Email</a></font>';
	$html .= '						</td>';
	$html .= '					</tr>';
	$html .= '					<tr>';
	$html .= '						<td width="708" bgcolor="#ed1c2e"<img src="'.WBC3_newsletter_image( 'spacer.gif', $return = true ).'" width="708" height="15" border="0" /></td>';
	$html .= '					</tr>';
	$html .= '				</table></td>';
	$html .= '			</tr>';
	$html .= '		</table></td>';
	$html .= '		<td width="10" bgcolor="#ed1c2e"><img src="'.WBC3_newsletter_image( 'spacer.gif', $return = true ).'" width="10" height="1" border="0" /></td>';
	$html .= '	</tr>';
	$html .= '</table>';

	// output
	echo $html;

}

// appends xid tracking code to links
function WBC3_newsletter_xid( $link, $return = false, $tag = null ) {
	
	// if no tag set, determine xid based on vertical title
	if( !$tag ) {
		$tag = 'newsletter-'.WBC3_newsletter_vertical();
	}
	
	// append xid to link
	if( isset( $tag ) ) {
		$link = add_query_arg( 'xid', $tag, $link );
	}
	
	// output
	if( $return ) {
		return $link;
	} else {
		echo esc_url( $link );
	}
	
}

// replace special characters to be compatible with email clients
function WBC3_newsletter_replace_special_chars( $text ) {
	
	if( $text ) {
		// first replace UTF-8 characters
		$text = str_replace(
			array("\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6"),
			array("'", "'", '"', '"', '-', '--', '...'),
			$text
		);
		
		// next replace their Windows-1252 equivalents
		$text = str_replace(
			array(chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133)),
			array("'", "'", '"', '"', '-', '--', '...'),
			$text
		);
	}
	
	// output
	return $text;
	
}

/*
 * Newsletter Content Helper Functions
 */

// get newsletter article title
function WBC3_newsletter_title( $subject = false, $return = false ) {
	
	global $post;
	
	$newsletter_title = wp_kses_post( get_post_meta( $post->ID, 'WBC3_newsletter_title', true ) );
	
	if( !empty( $newsletter_title ) ) {
		// if newsletter title custom field is set, use that instead of post title
		$title = $newsletter_title;
	} else {
		// otherwise use post title
		$title = get_the_title();
		
		// remove "Cartoons of the Week" text
		$title = trim( str_replace( 'Cartoons of the Week:', '', $title ) );
	}
	
	if( $subject ) {
		// strip tags if subject line
		$title = WBC3_newsletter_replace_special_chars( strip_tags( $title ) );
	} else {
		// otherwise add italics if quote or number
		if( WBC3_is_quote_or_number() ) {
			$title = '<em>'.$title.'</em>';
		}
	}
	
	// output
	if( $return ) {
		return $title;
	} else {
		echo $title;
	}
	
}

// get category slug
function WBC3_newsletter_category( $link = false, $return = false ) {

	global $post;
	
	// get slug category if set
	$cat_id = get_post_meta( $post->ID, 'WBC3_post_slug', true );
	$category = get_the_category_by_ID( $cat_id );
	
	// if not set then get first category
	if( empty( $category ) ) {
		$categories = get_the_category( $post->ID );
		$cat_id = $categories[0]->cat_ID;
		$category = $categories[0]->name;
	}
	
	// set category link
	if( $link ) {
		$category_link = WBC3_newsletter_xid( get_category_link( $cat_id ), $return = true );
		$category = '<a style="color:#1b4f89; text-decoration:none" href="'.$category_link.'">'.$category.'</a>';
	}
	
	// output
	if( $return ) {
		return $category;
	} else {
		echo $category;
	}

}

// get author
function WBC3_newsletter_author( $return = false, $text = false ) {
	
	global $post;
	
	// check for byline
	$author_byline = get_post_meta( $post->ID, 'WBC3_byline', true );
	$hide_byline = get_post_meta( $post->ID, 'WBC3_hide_byline', true );
	
	// check for guest author
	$guest_author = WBC3_get_guest_author( $post->ID );
	
	// get author name
	$author_name = get_the_author( $post->ID );
	
	// determine which author to display
	if( !empty( $author_byline ) && ( empty( $hide_byline ) ) ) {
		// byline
		$author_name = $author_byline;
	} else if( !empty( $guest_author ) ) {
		// guest
		$author_name = $guest_author->post_title;
	} else {
		// default
		$author_name = $author_name;
	}
	
	if( $text ) {
		// capitalize for text newsletter
		$author_name = ucwords( $author_name );
	} else {
		// uppercase for html newsletter
		$author_name = strtoupper( $author_name );
	}
	
	// prepend 'By' text
	$author = 'By ' . $author_name;
	
	// output
	if( $return ) {
		return $author;
	} else {
		echo $author;
	}

}

// get article content for newsletter
function WBC3_newsletter_content() {
	
	$content = '';
	$deck = WBC3_get_deck();
	$excerpt = get_the_excerpt();
	
	// get content from deck or excerpt if possible
	if( $deck ) {
		$content = trim( $deck );
	} else if( $excerpt ) {
		$content = trim( $excerpt );
	} else {
		$content = get_the_content();
	}
	
	// strip shortcodes, html tags, and 'more...' text
	$content = strip_tags( strip_shortcodes( $content ) );
	$content = str_replace( '(more...)', '', $content );
	
	// strip vodpod shortcodes
	$content = preg_replace('/(?<=\[vodpod).*?(?=\])/', '', $content);
	$content = str_replace( '[vodpod]', '', $content );	
	
	// display first 150 characters of article content with ellipsis
	$max_chars = 150;
	if( strlen( $content ) > $max_chars ) {
		$content = substr( $content, 0, $max_chars );
		$words = explode( ' ', $content );
		array_pop( $words );
		$content = join( ' ', $words ) . '...';
	}							
	
	// output
	echo $content;
	
}

// get newsletter posts
function WBC3_newsletter_get_posts( $cartoons = false ) {

	// get most recent posts
	// exclude Numbers, Quotes, and Single Photo posts
	$args = array(
		'posts_per_page' => 8,
		'tax_query' => array(
			array(
				'taxonomy' => 'WBC3_post_format',
				'field' => 'slug',
				'terms' => array(
					'WBC3-post-format-number',
					'WBC3-post-format-photo',
					'WBC3-post-format-quote',
				),
				'operator' => 'NOT IN'
			)
		)
	);
	
	// adjust arguments for Ideas since it has 2 newsletters: Ideas and Cartoons
	if( WBC3_newsletter_vertical() == 'ideas' ) {
		
		if( $cartoons ) {
		
			// only show latest 3 posts
			$args['posts_per_page'] = 3;
			
			// add Cartoons category parameter
			$args['category_name'] = 'Cartoons of the Week';
		
		} else {
		
			// get category id for Cartoons of the Week
			$category = get_term_by( 'name', 'Cartoons of the Week', 'category' );
			$cartoons_cat_id = $category->term_id;
			
			// exclude posts in Cartoons category
			$args['cat'] = '-'.$cartoons_cat_id;
			
		}
		
	}
	
	// check if cache exists
	$items = wp_cache_get( 'WBC3_newsletter_' . WBC3_newsletter_vertical() );
	
	if( !$items ) {
		
		// create new query
		$items = new WP_Query( $args );
		
		if( $items ) {
			wp_cache_set( 'WBC3_newsletter_' . WBC3_newsletter_vertical(), $items, WBC3_CACHE, 3600 );
		}
	
	}
	
	// output
	return $items;

}

// get image path
function WBC3_newsletter_image( $filename = null, $return = false ) {
	if( isset( $filename ) ) {
		
		// set image path
		$image = get_stylesheet_directory_uri().'/library/assets/images/newsletter/'.$filename;
		
		// output
		if( $return ) {
			return $image;
		} else {
			echo $image;
		}
		
	}
}

// get twitter url
function WBC3_newsletter_twitter() {
	
	// get twitter username from COMPANY settings
	global $WBC3_settings;
	$twitter = $WBC3_settings['twitter'];
	
	// output twitter link
	echo esc_url( 'http://twitter.com/'.$twitter );
	
}

/*
 * Newsletter Right Rail Modules
 */

// right rail Popular module
function WBC3_newsletter_popular() {

	// construct html
	$html = '';
	$html .= '<table width="307" border="0" cellpadding="0" cellspacing="0">';
	$html .= '<tr>';
	$html .= '<td colspan="2"><img src="'.WBC3_newsletter_image( 'module_line.png', $return = true ).'" width="307" height="3" border="0" /></td>';
	$html .= '</tr>';
	$html .= '<tr>';
	$html .= '<td colspan="2" height="30"><font face="arial,sans-serif" style="font-size:17px; font-weight:bold; color:#000000; letter-spacing:-1px;">'.WBC3_newsletter_popular_title().'</font></td>';
	$html .= '</tr>';
	$html .= '<tr>';
	$html .= '<td colspan="2"><img src="'.WBC3_newsletter_image( 'module_line.png', $return = true ).'" width="307" height="1" border="0" /></td>';
	$html .= '</tr>';
	
	// get 5 most popular articles
	if( function_exists('wpcom_is_vip') ) {
		// from this vertical
		$html .= WBC3_newsletter_get_rss( get_bloginfo('url').'/?feed=mostpopular&duration=1&limit=11', 5, 'mostpop2', $return = true );
	} else {
		// from Newsfeed
		$html .= WBC3_newsletter_get_rss( 'http://newsfeed.company.com/?feed=mostpopular&duration=1&limit=11', 5, 'mostpop2', $return = true );
	}
	
	$html .= '<tr>';
	$html .= '<td colspan="2"><img src="'.WBC3_newsletter_image( 'spacer.gif', $return = true ).'" width="307" height="40" border="0" /></td>';
	$html .= '</tr>';
	$html .= '</table>';
		
	// output html
	echo $html;

}

// helper function for Popular module title
function WBC3_newsletter_popular_title() {
	
	// get blog title and remove 'COMPANY' and ampersands
	$blog_name = strtolower( get_bloginfo('name') );
	$blog_name = str_replace( 'time ', '', $blog_name );
	$blog_name = str_replace( '&amp;', ' & ', $blog_name );
	
	// convert to uppercase
	$title = strtoupper( 'Popular on ' . $blog_name );
	
	// output
	return esc_html( $title );

}

// get most popular articles from vertical - modified version of WBC3_get_rss()
// accepts feed, count, and tag parameters
function WBC3_newsletter_get_rss( $feed, $count, $tag = null, $return = false ) {
	
	$i = 0;
	$items = $count;
	$rss = fetch_feed( $feed );
	
	if ( !is_wp_error( $rss ) ) {
		
		$rss->enable_order_by_date(false);
		$html = '';

		foreach( $rss->get_items( 0, $items ) as $item ) {
			$title = strip_tags( $item->get_title() );
			
			// make sure there is a title
			if( !empty( $title ) ) {
				
				// increment counter
				$i++;
				
				// add newsletter xid to article link
				$link = WBC3_newsletter_xid( $item->get_link(), $return = true );
				
				// construct html
				$html .= '<tr>';
				$html .= '<td colspan="2"><img src="'.WBC3_newsletter_image( 'spacer.gif', $return = true ).'" width="307" height="20" border="0" /></td>';
				$html .= '</tr>';				
				$html .= '<tr>';
				$html .= '<td valign="top" width="20"><font face="arial,sans-serif" style="font-weight:bold; font-size:17px;">'.$i.'.</font></td>';
				$html .= '<td valign="top"><font face="arial,sans-serif" style="font-weight:normal; font-size:13px;"><a href="'.esc_url( $link ).'" style="color:#000000; text-decoration:none;">'.esc_html( $title ).'</a></font></td>';
				$html .= '</tr>';

			}
		}
		
		// output
		if( $return ) {
			return $html;
		} else {
			echo $html;
		}
	
	}
	
	unset($rss);
}

// get articles from Editors Picks belt
function WBC3_newsletter_editors_picks() {

	global $post, $WBC3_settings;
	
	// check if belt is enabled
	if( isset( $WBC3_settings['belt'] ) && ( $WBC3_settings['belt'] == 1 ) && ( isset( $WBC3_settings['belt_ids'] ) ) ) {
	
		$belt_posts = WBC3_get_belt_posts();
		
		// only display module if posts exist
		if( $belt_posts ) {
			
			$html = '';			
			$html .= '<table border="0" cellpadding="0" cellspacing="0" width="307">';
			$html .= '<tr>';
			$html .= '<td colspan="2"><img src="'.WBC3_newsletter_image( 'module_line.png', $return = true ).'" width="307" height="3" border="0" /></td>';
			$html .= '</tr>';
			$html .= '<tr>';
			$html .= '<td colspan="2" height="30"><font face="arial,sans-serif" style="font-size:17px; font-weight:bold; color:#000000; letter-spacing:-1px;">EDITORS\' PICKS</font></td>';
			$html .= '</tr>';
			$html .= '<tr>';
			$html .= '<td colspan="2"><img src="'.WBC3_newsletter_image( 'module_line.png', $return = true ).'" width="307" height="1" border="0" /></td>';
			$html .= '</tr>';
			
			// loop through articles
			foreach( $belt_posts as $post ) {
			
				// construct html
				$html .= '<tr>';
				$html .= '<td colspan="2"><img src="'.WBC3_newsletter_image( 'spacer.gif', $return = true ).'" width="307" height="20" border="0" /></td>';
				$html .= '</tr>';
				$html .= '<tr>';
				$html .= '<td colspan="2" style="padding-bottom:5px"><font face="arial,sans-serif" style="font-weight:bold; font-size:11px; color:#1b4f89; text-transform:uppercase;">'.WBC3_newsletter_category( $link = true, $return = true ).'</font></td>';
				$html .= '</tr>';
				$html .= '<tr>';
				
				// check for thumbnail image
				$image = get_the_post_thumbnail( $post->ID, 'newsletter-thumb-square', array( 'border'=>0, 'title'=>'', 'alt'=>'' ) );
				
				// adjust column width depending on image thumbnail
				if( $image ) {
					$html .= '<td width="222" valign="top">';
				} else {
					$html .= '<td colspan="2" width="307" valign="top">';
				}
				
				$html .= '<font face="arial,sans-serif" style="font-weight:bold; font-size:18px; letter-spacing:-1px;"><a href="'.WBC3_newsletter_xid( get_permalink(), $return = true ).'" style="color:#000000; text-decoration:none;">'.WBC3_newsletter_title( $subject = false, $return = true ).'</a></font><br>';
				$html .= '<img src="'.WBC3_newsletter_image( 'spacer.gif', $return = true ).'" width="222" height="5" border="0" /><br>';
				$html .= '<font face="arial,sans-serif" style="font-weight:normal; font-size:11px; color:#808080;">'.WBC3_newsletter_author( $return = true, $text = false ).'</font>';
				$html .= '</td>';
				
				// output image
				if( $image ) {
					$html .= '<td width="85" valign="top" align="right">';
					$html .= '<div align="center" style="width:75px; height:75px; background:#eee; text-align:center; overflow:visible;">';
					$html .= '<a href="'.WBC3_newsletter_xid( get_permalink(), $return = true ).'">'.$image.'</a>';
					$html .= '</div>';
					$html .= '</td>';				
				}
				
				$html .= '</tr>';
			
			}
			
			$html .= '<tr>';
			$html .= '<td colspan="2"><img src="'.WBC3_newsletter_image( 'spacer.gif', $return = true ).'" width="307" height="20" border="0" /></td>';
			$html .= '</tr>';
			$html .= '</table>';
					
		}
				
		// output html
		echo $html;
	
	}
}

// output posts from More On Time
function WBC3_newsletter_more_on_time() {

	$html = '';			
	$html .= '<table border="0" cellpadding="0" cellspacing="0" width="307">';
	$html .= '<tr>';
	$html .= '<td colspan="2"><img src="'.WBC3_newsletter_image( 'module_line.png', $return = true ).'" width="307" height="3" border="0" /></td>';
	$html .= '</tr>';
	$html .= '<tr>';
	$html .= '<td colspan="2" height="30"><font face="arial,sans-serif" style="font-size:17px; font-weight:bold; color:#000000; letter-spacing:-1px;">MORE ON COMPANY</font></td>';
	$html .= '</tr>';
	$html .= '<tr>';
	$html .= '<td colspan="2"><img src="'.WBC3_newsletter_image( 'module_line.png', $return = true ).'" width="307" height="1" border="0" /></td>';
	$html .= '</tr>';
	
	// get content
	$rss = fetch_feed( 'http://pipes.yahoo.com/pipes/pipe.run?_id=974c1c5bed758de6a8f2b93bf90bdbd1&_render=rss' );

	if( !is_wp_error( $rss ) ) {
	
		$rss->enable_order_by_date( false );
		$max = $rss->get_item_quantity( 4 );
		$items = $rss->get_items( 0, $max );
		
		// initialize counter
		$i = 1;

		foreach( $items as $item ) {
			
			// get article title and guid
			$title = $item->get_title();
			$url = $item->get_id();
			
			// get image and remove 77px square size
			$enclosures = $item->get_enclosures();
			$image = $enclosures[0]->link;
			$image = str_replace( '77_', '', $image );
			
			// construct html
			$html .= '<tr>';
			$html .= '<td colspan="2"><img src="'.WBC3_newsletter_image( 'spacer.gif', $return = true ).'" width="307" height="20" border="0" /></td>';
			$html .= '</tr>';
			$html .= '<tr>';
			
			// adjust column width depending on image thumbnail
			if( $image ) {
				$html .= '<td width="197" valign="top">';
			} else {
				$html .= '<td width="307" valign="top" colspan="2">';
			}
			
			$html .= '<font face="arial,sans-serif" style="font-weight:bold; font-size:18px; letter-spacing:-1px;"><a href="'.WBC3_newsletter_xid( $url, $return = true ).'" style="color:#000000; text-decoration:none;">'.$title.'</a></font>';
			$html .= '</td>';
			
			// output image
			if( $image ) {
				$html .= '<td width="110" valign="top" align="right">';
				$html .= '<div align="center" style="width:100px; height:auto; background:#eee; text-align:center; overflow:visible;">';
				$html .= '<a href="'.WBC3_newsletter_xid( $url, $return = true ).'"><img src="'.$image.'" width="100"></a>';
				$html .= '</div>';
				$html .= '</td>';				
			}
			
			$html .= '</tr>';
			
			// increment counter
			$i++;
		}
			
	}	
	
	$html .= '<tr>';
	$html .= '<td colspan="2"><img src="'.WBC3_newsletter_image( 'spacer.gif', $return = true ).'" width="307" height="20" border="0" /></td>';
	$html .= '</tr>';
	$html .= '</table>';
	
	// output html
	echo $html;
	
}

/*
 * Newsletter Ads
 */

function WBC3_newsletter_ad( $position = 'right', $vertical = false ) {
	
	// if vertical is not set, get from COMPANY settings
	if( !$vertical ) {
		$vertical = WBC3_newsletter_vertical();
	}
	
	// define ads for each vertical
	// NOTE: the asterisk (*) before the ad url is intentional and required for CheetahMail
	$newsletter_ads = array(
		
		// default should match newsfeed
		'default' => array(
			'top' => '<!-- begin 97x70 ad tag ADID %eaid!--><a href="*http://ad.doubleclick.net/N8484/jump/cm.tim/newsletter;tile=1;sz=97x70;dcove=r;ord=%%t%%" target="_top"><img src="http://ad.doubleclick.net/N8484/ad/cm.tim/newsletter;tile=1;sz=97x70;dcove=r;ord=%%t%%" border="0" width="97" height="70"></a><!-- End ad tag -->',
			'right' => '<!-- begin 300x250 ad tag ADID %eaid!--><a href="*http://ad.doubleclick.net/N8484/jump/tim/newsletter_newsfeed;tile=2;sz=300x250;dcove=r;ord=%%t%%" target="_top"><img src="http://ad.doubleclick.net/N8484/ad/tim/newsletter_newsfeed;tile=2;sz=300x250;dcove=r;ord=%%t%%" border="0" width="300" height="250"></a><!-- End ad tag -->',
			'bottom' => '<!-- begin 728x90 ad tag ADID %eaid!--><a href="*http://ad.doubleclick.net/N8484/jump/tim/newsletter_newsfeed;tile=3;sz=728x90;dcove=r;ord=%%t%%" target="_top"><img src="http://ad.doubleclick.net/N8484/ad/tim/newsletter_newsfeed;tile=3;sz=728x90;dcove=r;ord=%%t%%" border="0" width="728" height="90"></a><!-- End ad tag -->',
		),		
		'business' => array(
			'top' => '<!-- begin 97x70 ad tag ADID %eaid!--><a href="*http://ad.doubleclick.net/N8484/jump/cm.tim/newsletter;tile=1;sz=97x70;dcove=r;ord=%%t%%" target="_top"><img src="http://ad.doubleclick.net/N8484/ad/cm.tim/newsletter;tile=1;sz=97x70;dcove=r;ord=%%t%%" border="0" width="97" height="70"></a><!-- End ad tag -->',
			'right' => '<!-- begin 300x250 ad tag ADID %eaid!--><a href="*http://ad.doubleclick.net/N8484/jump/tim/newsletter_business;tile=2;sz=300x250;dcove=r;ord=%%t%%" target="_top"><img src="http://ad.doubleclick.net/N8484/ad/tim/newsletter_business;tile=2;sz=300x250;dcove=r;ord=%%t%%" border="0" width="300" height="250"></a><!-- End ad tag -->',
			'bottom' => '<!-- begin 728x90 ad tag ADID %eaid!--><a href="*http://ad.doubleclick.net/N8484/jump/tim/newsletter_business;tile=3;sz=728x90;dcove=r;ord=%%t%%" target="_top"><img src="http://ad.doubleclick.net/N8484/ad/tim/newsletter_business;tile=3;sz=728x90;dcove=r;ord=%%t%%" border="0" width="728" height="90"></a><!-- End ad tag -->',
		),
		'cartoons' => array(
			'top' => '<!-- begin 97x70 ad tag ADID %eaid!--><a href="*http://ad.doubleclick.net/N8484/jump/cm.tim/newsletter;tile=1;sz=97x70;dcove=r;ord=%%t%%" target="_top"><img src="http://ad.doubleclick.net/N8484/ad/cm.tim/newsletter;tile=1;sz=97x70;dcove=r;ord=%%t%%" border="0" width="97" height="70"></a><!-- End ad tag -->',
			'right' => '<!-- begin 300x250 ad tag ADID %eaid!--><a href="*http://ad.doubleclick.net/N8484/jump/tim/newsletter_cartoons;tile=2;sz=300x250;dcove=r;ord=%%t%%" target="_top"><img src="http://ad.doubleclick.net/N8484/ad/tim/newsletter_cartoons;tile=2;sz=300x250;dcove=r;ord=%%t%%" border="0" width="300" height="250"></a><!-- End ad tag -->',
			'bottom' => '<!-- begin 728x90 ad tag ADID %eaid!--><a href="*http://ad.doubleclick.net/N8484/jump/tim/newsletter_cartoons;tile=3;sz=728x90;dcove=r;ord=%%t%%" target="_top"><img src="http://ad.doubleclick.net/N8484/ad/tim/newsletter_cartoons;tile=3;sz=728x90;dcove=r;ord=%%t%%" border="0" width="728" height="90"></a><!-- End ad tag -->',
		),
		'entertainment' => array(
			'top' => '<!-- begin 97x70 ad tag ADID %eaid!--><a href="*http://ad.doubleclick.net/N8484/jump/cm.tim/newsletter;tile=1;sz=97x70;dcove=r;ord=%%t%%" target="_top"><img src="http://ad.doubleclick.net/N8484/ad/cm.tim/newsletter;tile=1;sz=97x70;dcove=r;ord=%%t%%" border="0" width="97" height="70"></a><!-- End ad tag -->',
			'right' => '<!-- begin 300x250 ad tag ADID %eaid!--><a href="*http://ad.doubleclick.net/N8484/jump/tim/newsletter_entertainment;tile=2;sz=300x250;dcove=r;ord=%%t%%" target="_top"><img src="http://ad.doubleclick.net/N8484/ad/tim/newsletter_entertainment;tile=2;sz=300x250;dcove=r;ord=%%t%%" border="0" width="300" height="250"></a><!-- End ad tag -->',
			'bottom' => '<!-- begin 728x90 ad tag ADID %eaid!--><a href="*http://ad.doubleclick.net/N8484/jump/tim/newsletter_entertainment;tile=3;sz=728x90;dcove=r;ord=%%t%%" target="_top"><img src="http://ad.doubleclick.net/N8484/ad/tim/newsletter_entertainment;tile=3;sz=728x90;dcove=r;ord=%%t%%" border="0" width="728" height="90"></a><!-- End ad tag -->',
		),
		'healthland' => array(
			'top' => '<!-- begin 97x70 ad tag ADID %eaid!--><a href="*http://ad.doubleclick.net/N8484/jump/cm.tim/newsletter;tile=1;sz=97x70;dcove=r;ord=%%t%%" target="_top"><img src="http://ad.doubleclick.net/N8484/ad/cm.tim/newsletter;tile=1;sz=97x70;dcove=r;ord=%%t%%" border="0" width="97" height="70"></a><!-- End ad tag -->',
			'right' => '<!-- begin 300x250 ad tag ADID %eaid!--><a href="*http://ad.doubleclick.net/N8484/jump/tim/newsletter_wellness;tile=2;sz=300x250;dcove=r;ord=%%t%%" target="_top"><img src="http://ad.doubleclick.net/N8484/ad/tim/newsletter_wellness;tile=2;sz=300x250;dcove=r;ord=%%t%%" border="0" width="300" height="250"></a><!-- End ad tag -->',
			'bottom' => '<!-- begin 728x90 ad tag ADID %eaid!--><a href="*http://ad.doubleclick.net/N8484/jump/tim/newsletter_wellness;tile=3;sz=728x90;dcove=r;ord=%%t%%" target="_top"><img src="http://ad.doubleclick.net/N8484/ad/tim/newsletter_wellness;tile=3;sz=728x90;dcove=r;ord=%%t%%" border="0" width="728" height="90"></a><!-- End ad tag -->',
		),
		'ideas' => array(
			'top' => '<!-- begin 97x70 ad tag ADID %eaid!--><a href="*http://ad.doubleclick.net/N8484/jump/cm.tim/newsletter;tile=1;sz=97x70;dcove=r;ord=%%t%%" target="_top"><img src="http://ad.doubleclick.net/N8484/ad/cm.tim/newsletter;tile=1;sz=97x70;dcove=r;ord=%%t%%" border="0" width="97" height="70"></a><!-- End ad tag -->',
			'right' => '<!-- begin 300x250 ad tag ADID %eaid!--><a href="*http://ad.doubleclick.net/N8484/jump/tim/newsletter_opinion;tile=2;sz=300x250;dcove=r;ord=%%t%%" target="_top"><img src="http://ad.doubleclick.net/N8484/ad/tim/newsletter_opinion;tile=2;sz=300x250;dcove=r;ord=%%t%%" border="0" width="300" height="250"></a><!-- End ad tag -->',
			'bottom' => '<!-- begin 728x90 ad tag ADID %eaid!--><a href="*http://ad.doubleclick.net/N8484/jump/tim/newsletter_opinion;tile=3;sz=728x90;dcove=r;ord=%%t%%" target="_top"><img src="http://ad.doubleclick.net/N8484/ad/tim/newsletter_opinion;tile=3;sz=728x90;dcove=r;ord=%%t%%" border="0" width="728" height="90"></a><!-- End ad tag -->',
		),
		'keepingscore' => array(
			'top' => '<!-- begin 97x70 ad tag ADID %eaid!--><a href="*http://ad.doubleclick.net/N8484/jump/cm.tim/newsletter;tile=1;sz=97x70;dcove=r;ord=%%t%%" target="_top"><img src="http://ad.doubleclick.net/N8484/ad/cm.tim/newsletter;tile=1;sz=97x70;dcove=r;ord=%%t%%" border="0" width="97" height="70"></a><!-- End ad tag -->',
			'right' => '<!-- begin 300x250 ad tag ADID %eaid!--><a href="*http://ad.doubleclick.net/N8484/jump/tim/newsletter_keepingscore;tile=2;sz=300x250;dcove=r;ord=%%t%%" target="_top"><img src="http://ad.doubleclick.net/N8484/ad/tim/newsletter_keepingscore;tile=2;sz=300x250;dcove=r;ord=%%t%%" border="0" width="300" height="250"></a><!-- End ad tag -->',
			'bottom' => '<!-- begin 728x90 ad tag ADID %eaid!--><a href="*http://ad.doubleclick.net/N8484/jump/tim/newsletter_keepingscore;tile=3;sz=728x90;dcove=r;ord=%%t%%" target="_top"><img src="http://ad.doubleclick.net/N8484/ad/tim/newsletter_keepingscore;tile=3;sz=728x90;dcove=r;ord=%%t%%" border="0" width="728" height="90"></a><!-- End ad tag -->',
		),
		// moneyland should match business
		'moneyland' => array(
			'top' => '<!-- begin 97x70 ad tag ADID %eaid!--><a href="*http://ad.doubleclick.net/N8484/jump/cm.tim/newsletter;tile=1;sz=97x70;dcove=r;ord=%%t%%" target="_top"><img src="http://ad.doubleclick.net/N8484/ad/cm.tim/newsletter;tile=1;sz=97x70;dcove=r;ord=%%t%%" border="0" width="97" height="70"></a><!-- End ad tag -->',
			'right' => '<!-- begin 300x250 ad tag ADID %eaid!--><a href="*http://ad.doubleclick.net/N8484/jump/tim/newsletter_business;tile=2;sz=300x250;dcove=r;ord=%%t%%" target="_top"><img src="http://ad.doubleclick.net/N8484/ad/tim/newsletter_business;tile=2;sz=300x250;dcove=r;ord=%%t%%" border="0" width="300" height="250"></a><!-- End ad tag -->',
			'bottom' => '<!-- begin 728x90 ad tag ADID %eaid!--><a href="*http://ad.doubleclick.net/N8484/jump/tim/newsletter_business;tile=3;sz=728x90;dcove=r;ord=%%t%%" target="_top"><img src="http://ad.doubleclick.net/N8484/ad/tim/newsletter_business;tile=3;sz=728x90;dcove=r;ord=%%t%%" border="0" width="728" height="90"></a><!-- End ad tag -->',
		),
		'nation' => array(
			'top' => '<!-- begin 97x70 ad tag ADID %eaid!--><a href="*http://ad.doubleclick.net/N8484/jump/cm.tim/newsletter;tile=1;sz=97x70;dcove=r;ord=%%t%%" target="_top"><img src="http://ad.doubleclick.net/N8484/ad/cm.tim/newsletter;tile=1;sz=97x70;dcove=r;ord=%%t%%" border="0" width="97" height="70"></a><!-- End ad tag -->',
			'right' => '<!-- begin 300x250 ad tag ADID %eaid!--><a href="*http://ad.doubleclick.net/N8484/jump/tim/newsletter_us;tile=2;sz=300x250;dcove=r;ord=%%t%%" target="_top"><img src="http://ad.doubleclick.net/N8484/ad/tim/newsletter_us;tile=2;sz=300x250;dcove=r;ord=%%t%%" border="0" width="300" height="250"></a><!-- End ad tag -->',
			'bottom' => '<!-- begin 728x90 ad tag ADID %eaid!--><a href="*http://ad.doubleclick.net/N8484/jump/tim/newsletter_us;tile=3;sz=728x90;dcove=r;ord=%%t%%" target="_top"><img src="http://ad.doubleclick.net/N8484/ad/tim/newsletter_us;tile=3;sz=728x90;dcove=r;ord=%%t%%" border="0" width="728" height="90"></a><!-- End ad tag -->',
		),
		'newsfeed' => array(
			'top' => '<!-- begin 97x70 ad tag ADID %eaid!--><a href="*http://ad.doubleclick.net/N8484/jump/cm.tim/newsletter;tile=1;sz=97x70;dcove=r;ord=%%t%%" target="_top"><img src="http://ad.doubleclick.net/N8484/ad/cm.tim/newsletter;tile=1;sz=97x70;dcove=r;ord=%%t%%" border="0" width="97" height="70"></a><!-- End ad tag -->',
			'right' => '<!-- begin 300x250 ad tag ADID %eaid!--><a href="*http://ad.doubleclick.net/N8484/jump/tim/newsletter_newsfeed;tile=2;sz=300x250;dcove=r;ord=%%t%%" target="_top"><img src="http://ad.doubleclick.net/N8484/ad/tim/newsletter_newsfeed;tile=2;sz=300x250;dcove=r;ord=%%t%%" border="0" width="300" height="250"></a><!-- End ad tag -->',
			'bottom' => '<!-- begin 728x90 ad tag ADID %eaid!--><a href="*http://ad.doubleclick.net/N8484/jump/tim/newsletter_newsfeed;tile=3;sz=728x90;dcove=r;ord=%%t%%" target="_top"><img src="http://ad.doubleclick.net/N8484/ad/tim/newsletter_newsfeed;tile=3;sz=728x90;dcove=r;ord=%%t%%" border="0" width="728" height="90"></a><!-- End ad tag -->',
		),
		'science' => array(
			'top' => '<!-- begin 97x70 ad tag ADID %eaid!--><a href="*http://ad.doubleclick.net/N8484/jump/cm.tim/newsletter;tile=1;sz=97x70;dcove=r;ord=%%t%%" target="_top"><img src="http://ad.doubleclick.net/N8484/ad/cm.tim/newsletter;tile=1;sz=97x70;dcove=r;ord=%%t%%" border="0" width="97" height="70"></a><!-- End ad tag -->',
			'right' => '<!-- begin 300x250 ad tag ADID %eaid!--><a href="*http://ad.doubleclick.net/N8484/jump/tim/newsletter_science;tile=2;sz=300x250;dcove=r;ord=%%t%%" target="_top"><img src="http://ad.doubleclick.net/N8484/ad/tim/newsletter_science;tile=2;sz=300x250;dcove=r;ord=%%t%%" border="0" width="300" height="250"></a><!-- End ad tag -->',
			'bottom' => '<!-- begin 728x90 ad tag ADID %eaid!--><a href="*http://ad.doubleclick.net/N8484/jump/tim/newsletter_science;tile=3;sz=728x90;dcove=r;ord=%%t%%" target="_top"><img src="http://ad.doubleclick.net/N8484/ad/tim/newsletter_science;tile=3;sz=728x90;dcove=r;ord=%%t%%" border="0" width="728" height="90"></a><!-- End ad tag -->',
		),
		// sports should match keeping score
		'sports' => array(
			'top' => '<!-- begin 97x70 ad tag ADID %eaid!--><a href="*http://ad.doubleclick.net/N8484/jump/cm.tim/newsletter;tile=1;sz=97x70;dcove=r;ord=%%t%%" target="_top"><img src="http://ad.doubleclick.net/N8484/ad/cm.tim/newsletter;tile=1;sz=97x70;dcove=r;ord=%%t%%" border="0" width="97" height="70"></a><!-- End ad tag -->',
			'right' => '<!-- begin 300x250 ad tag ADID %eaid!--><a href="*http://ad.doubleclick.net/N8484/jump/tim/newsletter_keepingscore;tile=2;sz=300x250;dcove=r;ord=%%t%%" target="_top"><img src="http://ad.doubleclick.net/N8484/ad/tim/newsletter_keepingscore;tile=2;sz=300x250;dcove=r;ord=%%t%%" border="0" width="300" height="250"></a><!-- End ad tag -->',
			'bottom' => '<!-- begin 728x90 ad tag ADID %eaid!--><a href="*http://ad.doubleclick.net/N8484/jump/tim/newsletter_keepingscore;tile=3;sz=728x90;dcove=r;ord=%%t%%" target="_top"><img src="http://ad.doubleclick.net/N8484/ad/tim/newsletter_keepingscore;tile=3;sz=728x90;dcove=r;ord=%%t%%" border="0" width="728" height="90"></a><!-- End ad tag -->',
		),
		'style' => array(
			'top' => '<!-- begin 97x70 ad tag ADID %eaid!--><a href="*http://ad.doubleclick.net/N8484/jump/cm.tim/newsletter;tile=1;sz=97x70;dcove=r;ord=%%t%%" target="_top"><img src="http://ad.doubleclick.net/N8484/ad/cm.tim/newsletter;tile=1;sz=97x70;dcove=r;ord=%%t%%" border="0" width="97" height="70"></a><!-- End ad tag -->',
			'right' => '<!-- begin 300x250 ad tag ADID %eaid!--><a href="*http://ad.doubleclick.net/N8484/jump/tim/newsletter_style;tile=2;sz=300x250;dcove=r;ord=%%t%%" target="_top"><img src="http://ad.doubleclick.net/N8484/ad/tim/newsletter_style;tile=2;sz=300x250;dcove=r;ord=%%t%%" border="0" width="300" height="250"></a><!-- End ad tag -->',
			'bottom' => '<!-- begin 728x90 ad tag ADID %eaid!--><a href="*http://ad.doubleclick.net/N8484/jump/tim/newsletter_style;tile=3;sz=728x90;dcove=r;ord=%%t%%" target="_top"><img src="http://ad.doubleclick.net/N8484/ad/tim/newsletter_style;tile=3;sz=728x90;dcove=r;ord=%%t%%" border="0" width="728" height="90"></a><!-- End ad tag -->',
		),
		'swampland' => array(
			'top' => '<!-- begin 97x70 ad tag ADID %eaid!--><a href="*http://ad.doubleclick.net/N8484/jump/cm.tim/newsletter;tile=1;sz=97x70;dcove=r;ord=%%t%%" target="_top"><img src="http://ad.doubleclick.net/N8484/ad/cm.tim/newsletter;tile=1;sz=97x70;dcove=r;ord=%%t%%" border="0" width="97" height="70"></a><!-- End ad tag -->',
			'right' => '<!-- begin 300x250 ad tag ADID %eaid!--><a href="*http://ad.doubleclick.net/N8484/jump/tim/newsletter_swampland;tile=2;sz=300x250;dcove=r;ord=%%t%%" target="_top"><img src="http://ad.doubleclick.net/N8484/ad/tim/newsletter_swampland;tile=2;sz=300x250;dcove=r;ord=%%t%%" border="0" width="300" height="250"></a><!-- End ad tag -->',
			'bottom' => '<!-- begin 728x90 ad tag ADID %eaid!--><a href="*http://ad.doubleclick.net/N8484/jump/tim/newsletter_swampland;tile=3;sz=728x90;dcove=r;ord=%%t%%" target="_top"><img src="http://ad.doubleclick.net/N8484/ad/tim/newsletter_swampland;tile=3;sz=728x90;dcove=r;ord=%%t%%" border="0" width="728" height="90"></a><!-- End ad tag -->',
		),
		'techland' => array(
			'top' => '<!-- begin 97x70 ad tag ADID %eaid!--><a href="*http://ad.doubleclick.net/N8484/jump/cm.tim/newsletter;tile=1;sz=97x70;dcove=r;ord=%%t%%" target="_top"><img src="http://ad.doubleclick.net/N8484/ad/cm.tim/newsletter;tile=1;sz=97x70;dcove=r;ord=%%t%%" border="0" width="97" height="70"></a><!-- End ad tag -->',
			'right' => '<!-- begin 300x250 ad tag ADID %eaid!--><a href="*http://ad.doubleclick.net/N8484/jump/tim/newsletter_techland;tile=2;sz=300x250;dcove=r;ord=%%t%%" target="_top"><img src="http://ad.doubleclick.net/N8484/ad/tim/newsletter_techland;tile=2;sz=300x250;dcove=r;ord=%%t%%" border="0" width="300" height="250"></a><!-- End ad tag -->',
			'bottom' => '<!-- begin 728x90 ad tag ADID %eaid!--><a href="*http://ad.doubleclick.net/N8484/jump/tim/newsletter_techland;tile=3;sz=728x90;dcove=r;ord=%%t%%" target="_top"><img src="http://ad.doubleclick.net/N8484/ad/tim/newsletter_techland;tile=3;sz=728x90;dcove=r;ord=%%t%%" border="0" width="728" height="90"></a><!-- End ad tag -->',
		),
		'world' => array(
			'top' => '<!-- begin 97x70 ad tag ADID %eaid!--><a href="*http://ad.doubleclick.net/N8484/jump/cm.tim/newsletter;tile=1;sz=97x70;dcove=r;ord=%%t%%" target="_top"><img src="http://ad.doubleclick.net/N8484/ad/cm.tim/newsletter;tile=1;sz=97x70;dcove=r;ord=%%t%%" border="0" width="97" height="70"></a><!-- End ad tag -->',
			'right' => '<!-- begin 300x250 ad tag ADID %eaid!--><a href="*http://ad.doubleclick.net/N8484/jump/tim/newsletter_world;tile=2;sz=300x250;dcove=r;ord=%%t%%" target="_top"><img src="http://ad.doubleclick.net/N8484/ad/tim/newsletter_world;tile=2;sz=300x250;dcove=r;ord=%%t%%" border="0" width="300" height="250"></a><!-- End ad tag -->',
			'bottom' => '<!-- begin 728x90 ad tag ADID %eaid!--><a href="*http://ad.doubleclick.net/N8484/jump/tim/newsletter_world;tile=3;sz=728x90;dcove=r;ord=%%t%%" target="_top"><img src="http://ad.doubleclick.net/N8484/ad/tim/newsletter_world;tile=3;sz=728x90;dcove=r;ord=%%t%%" border="0" width="728" height="90"></a><!-- End ad tag -->',
		),
	
	);
	
	// output
	if( $vertical && $position ) {
		
		// output ad
		echo $newsletter_ads[ $vertical ][ $position ];
		
		// output spacer for right rail ad unit
		if( $position == 'right' ) {
		
			$html = '';
			$html .= '<table width="307" border="0" cellpadding="0" cellspacing="0">';
			$html .= '<tr><td><img src="'.WBC3_newsletter_image( 'spacer.gif', $return = true ).'" width="307" height="40" border="0" /></td></tr>';
			$html .= '</table>';
			
			echo $html; 
		
		}
		
	}

}