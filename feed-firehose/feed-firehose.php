<?php
/**
 * Plugin Name: Firehose Feed
 * Description: Creates parent and child feeds for iPhone and Android Apps and adds it to the Wordpress Custom Feeds
 * Author: William Cole
 * Version 1.1
 *
 * Version History
 *
 * 1.0
 * Initial build
 *
 * 1.1
 * Replaced WBC3_is_special_gallery() with WBC3_get_post_format() so feed is compatible with theme.
 * Added helper function to determine if we are on the new theme or not.
 * Added query filters to properly get slides on packages
 */

function WBC3_firehose() {

	header('Content-Type: ' . feed_content_type('rss-http') . '; charset=' . get_option('blog_charset'), true);
	$more = 0;
	
	echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>'; ?>
	
	<rss version="2.0"
		xmlns:content="http://purl.org/rss/1.0/modules/content/"
		xmlns:wfw="http://wellformedweb.org/CommentAPI/"
		xmlns:dc="http://purl.org/dc/elements/1.1/"
		xmlns:atom="http://www.w3.org/2005/Atom"
		xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
		xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
		<?php do_action('rss2_ns'); ?>
	>
	
	<channel>
		<title><?php bloginfo_rss('name'); wp_title_rss(); ?></title>
		<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
		<link><?php bloginfo_rss('url'); ?></link>
		<description><?php bloginfo_rss("description"); ?></description>
		<lastBuildDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?></lastBuildDate>
		<language><?php echo get_option('rss_language'); ?></language>
		<sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'hourly' ); ?></sy:updatePeriod>
		<sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency>
		<?php //do_action('rss2_head'); ?>
		
		<?php
			
			// set parent id
			$parent_id = WBC3_firehose_get_parent_id();
			
			// set some image vars
			$img_count = 0;
			$img_thumb_size = '?w=150&amp;h=150';
			$img_large_size = '?w=563&amp;h=372';
			
			// create new query
			$items = WBC3_firehose_get_items( $parent_id );
			
			// output data
			while( $items->have_posts() ) : $items->the_post();
				?>
				
				<item>
					<title><![CDATA[<?php the_title(); ?>]]></title>
					<link><?php the_permalink_rss(); ?></link>
					<comments><?php comments_link_feed(); ?></comments>
					<pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?></pubDate>
					<?php the_category_rss('rss2'); ?>
					<guid isPermaLink="false"><?php the_guid(); ?></guid>
					
					<?php
					
						if( !empty( $parent_id ) ) {
						
							/* CHILDREN */
							
							if( WBC3_firehose_is_package( $parent_id ) ) {
								
								# special gallery slides
								
								// author and description
								?>								
								<dc:creator><?php the_author(); ?></dc:creator>
								<description><![CDATA[<?php the_content_feed('rss2'); ?>]]></description>								
								<?php
								
								// credit and caption for packages
								$credit = '';
								$caption = '';
							
								if( has_post_thumbnail( get_the_ID() ) ) {
												
									$post_thumbnail_id = get_post_thumbnail_id( get_the_ID() );
									$post_thumbnail_image = get_post( $post_thumbnail_id );
									
									if( $post_thumbnail_image ) {
										$credit = $post_thumbnail_image->post_excerpt;
										$caption = $post_thumbnail_image->post_content;
									}
									
								}
								
								if( $credit ) echo '<mediaCredit>'.$credit.'</mediaCredit>';
								if( $caption ) echo '<mediaCaption>'.$caption.'</mediaCaption>';
								
							} else {
							
								# photo galleries
								
								$post_status = 'inherit';
								$post_type = 'attachment';
								
								$child_args = array(
									'post_parent' => $parent_id,
									'post_status' => $post_status,
									'post_type' => $post_type,
									'order' => 'ASC',
									'orderby' => 'menu_order',
									'numberposts' => 1,
									'offset' => $img_count
								);
								
								// get image attachments
								$attachments = get_posts( $child_args );
								
								if( $attachments ) {
									foreach ( $attachments as $attachment ) {
										
										// credit and caption for galleries
										?>
										<dc:creator><?php echo $attachment->post_excerpt; ?></dc:creator>
										<description><![CDATA[<?php echo $attachment->post_content; ?>]]></description>
										<?php
									
									}
									
								}	
							
							}
							
							// get slide or image id
							$id = get_the_ID();
							
							if( WBC3_firehose_is_package( $parent_id ) ) {
								// slide
								$img = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), 'full' );								
							} else {
								// image
								$img = wp_get_attachment_image_src( $id, 'full' );								
							}
							
							$c = 1;
							
							if( $img ) {
								echo "<image_thumb_$c>".$img[0].$img_thumb_size."</image_thumb_$c>";
								echo "<image_large_$c>".$img[0].$img_large_size."</image_large_$c>";
							}
							
							$img_count++;
							
						} else {
						
							/* PARENT */
							
							// author and description
							?>							
							<dc:creator><?php the_author(); ?></dc:creator>
							<description><![CDATA[<?php the_excerpt_rss(); ?>]]></description>
							<?php
							
							// get post id
							$post_id = get_the_ID();
						
							// images
							$img_args = array(
								'post_parent' => $post_id,
								'post_type' => 'attachment',
								'numberposts' => 366,
								'post_status' => 'inherit'						
							);
							
							$attachments = get_posts( $img_args );
							
							if( $attachments ) {
							
								// children
								if( count( $attachments ) > 1 ) {
									echo '<children>'.get_bloginfo('url').'/?feed=WBC3_firehose&amp;parent_id='.$post_id.'</children>';
								}
								
								// image thumbnails							
								$c = 1;
								foreach( $attachments as $attachment ) {
									$img = wp_get_attachment_image_src( $attachment->ID, 'full' );
									if( $img ) {
										echo "<image_thumb_$c>".$img[0].$img_thumb_size."</image_thumb_$c>";
										echo "<image_large_$c>".$img[0].$img_large_size."</image_large_$c>";
									}
									$c++;
								}
							}
							
							// get thumbnail image from parent post
							$img = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
						
						}
						
						// images						
						if( $img ) {
							echo '<thumb_image>'.$img[0].'?w=147&amp;h=96&amp;crop=1</thumb_image>';
							echo '<large_image>'.$img[0].'?w=236&amp;h=141&amp;crop=1</large_image>';
						}
					
					?>
					
					<content:encoded><![CDATA[<?php the_content_feed('rss2'); ?>]]></content:encoded>
					<wfw:commentRss><?php echo esc_url( get_post_comments_feed_link( null, 'rss2' ) ); ?></wfw:commentRss>
					<slash:comments><?php echo get_comments_number(); ?></slash:comments>
					
					<?php rss_enclosure(); ?>
					<?php //do_action( 'rss2_item' ); ?>
				</item>
				
				<?php
			endwhile;
		?>

	</channel>
				
	</rss>
	
	<?php
}
add_action( 'do_feed_WBC3_firehose', 'WBC3_firehose' );

/*
 * Helper Functions
 */

// get parent id from query parameter
function WBC3_firehose_get_parent_id() {
	$parent_id = !empty( $_GET['parent_id'] ) ? intval( $_GET['parent_id'] ) : '';
	return $parent_id;
}

// determine if we are on WBC3 theme
function WBC3_firehose_is_2012() {
	if( defined( 'WBC3_2012' ) && WBC3_2012 ) {
		return true;
	} else {
		return false;	
	}
}

// determine if post is a Package / Special Gallery
function WBC3_firehose_is_package( $parent_id ) {
	if( empty( $parent_id ) )
		return;
	
	if( function_exists( 'WBC3_get_post_format' ) && ( WBC3_get_post_format( $parent_id ) == 'special' ) ) {
		return true;
	}	
}

// query filters for getting our slide data
function WBC3_firehose_posts_clauses( $query ) {
	
	global $wpdb;
	
	$parent_id = WBC3_firehose_get_parent_id();
	
	if( $parent_id ) {
		$query['orderby'] = "h.menu_order, $wpdb->posts.menu_order ASC";
		$query['join'] = "LEFT JOIN $wpdb->posts AS h ON h.post_parent = $parent_id";
		$query['fields'] = "$wpdb->posts.*, h.post_title AS hub, h.ID as hub_id";
		$query['where'] .= " AND $wpdb->posts.post_parent = h.ID";
	}
	
	return $query;

}

// get the desired post items
function WBC3_firehose_get_items( $parent_id = null ) {
	
	if( empty( $parent_id ) ) {
	
		# PARENT POSTS
		
		// initial query to get IDs of non-gallery posts to exclude from main query
		$exclude_items = new WP_Query( array(
			'order' => 'DESC',
			'posts_per_page' => 10
		) );
		
		$exclude_ids = array();
		
		foreach( $exclude_items->posts as $post ) :
		
			// check for attachments					
			$attachments = get_posts(
				array(
					'post_type' => 'attachment',
					'post_parent' => $post->ID,
					'post_status' => 'inherit',
					'orderby' => 'menu_order',
					'order' => 'ASC',
					'numberposts' => 5 // ok to limit 5, since we just need to know if post has more than 1 attachment
				)
			);
			
			// if post has 1 or less attachments, exclude it from main query
			if( count( $attachments ) <= 1 ) {
				$exclude_ids[] = $post->ID;
			}					
			
		endforeach;
		
		// main query to get all gallery posts, excluding ids from above
		$items = new WP_Query( array(
			'order' => 'DESC',
			'posts_per_page' => 10,
			'post__not_in' => $exclude_ids
		) );
		
	} else {
	
		# CHILD POSTS
		
		if( WBC3_firehose_is_package( $parent_id ) ) {
			
			# special gallery slides
			
			if( WBC3_firehose_is_2012() ) {
		
				# special packages on WBC3 theme
				
				// add filter so we can get slides in the correct order
				add_filter( 'posts_clauses', 'WBC3_firehose_posts_clauses' );
				
				$items = new WP_Query( array(
					'post_status' => 'publish',
					'post_type' => 'WBC3_slide',
					'posts_per_page' => 366,
				) );
				
				remove_filter( 'posts_clauses', 'WBC3_firehose_posts_clauses' );
			
			} else {
			
				# special packages on older themes
				
				$items = new WP_Query( array(
					'post_parent' => $parent_id,
					'post_status' => 'publish',
					'post_type' => 'WBC3_slide',
					'order' => 'ASC',
					'orderby' => 'menu_order',
					'posts_per_page' => 366
				) );
				
			}
			
		} else {
		
			# image attachments
			
			$items = new WP_Query( array(
				'post_parent' => $parent_id,
				'post_status' => 'inherit',
				'post_type' => 'attachment',
				'order' => 'ASC',
				'orderby' => 'menu_order',
				'posts_per_page' => 366
			) );
		
		}
		
	}
	
	// output
	return $items;
	
}