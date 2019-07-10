<?php
/**
Plugin Name: Hierarchy Feed
Description: Creates hierarchy feed for iPhone and Android Apps and adds it to the Wordpress Custom Feeds
Author: William Cole
Version: 1.0
*/

function WBC3_hierarchy() {

	header('Content-Type: ' . feed_content_type('rss-http') . '; charset=' . get_option('blog_charset'), true);
	echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>';
	
	// get vertical name
	$vertical = get_bloginfo('name');
	
	// set cartoons boolean
	$cartoons = (bool)( !empty( $_GET['cartoons'] ) ) ? $_GET['cartoons'] : false;
	
	// set outline name
	$outline_name = (string)( $cartoons ) ? 'cartoons' : strtolower( $vertical );
	
	// default post parameters
	$args = array(
		'order' => 'DESC',
		'posts_per_page' => 20
	);
	
	// if cartoons feed
	if( $cartoons ) {
		
		// get category id for Cartoons of the Week
		$category = get_term_by('name', 'Cartoons of the Week', 'category');
		$cartoons_cat_id = $category->term_id;
		
		// add category parameter if on Ideas vertical				
		$args['cat'] = $cartoons_cat_id;
		
	}
	
	?>
	
	<opml>
		<head>
			<title>News Blogs for Tringapps</title>
			<pubDate><?php echo date('l, j F Y H:i:s').' UTC'; ?></pubDate>
			<hierarchy name="iphone_client" id="7700" type="PAGE"/>
		</head>
		
		<body>
			<outline name="iphone_client" id="7700" type="PAGE">
				<outline name="<?php echo esc_attr( $outline_name ); ?>" id="8800" frontpageDefault="true" displayOrder="1">
				
				<?php
				
				// create new query
				$items = new WP_Query( $args );
				
				// initialize counter
				$c = 0;
					
				// output data
				while( $items->have_posts() ) : $items->the_post();
					?>
					
					<outline frontpageDefault="true" id="<?php the_ID(); ?>" displayOrder="<?php echo $c; ?>" headline="<?php echo esc_attr( strip_tags( get_the_title() ) ); ?>" contentUrl="<?php the_permalink(); ?>" type="blogs" 
						<?php
						
						// addtl data for Cartoons of the Week
						if( $cartoons ) {
							
							// xmlUrl for child elements
							$xmlUrl = get_bloginfo('url') . '?feed=WBC3_firehose&parent_id=' . get_the_ID();
							if( $xmlUrl ) echo ' xmlUrl="' . esc_url( $xmlUrl ) . '"';
							
							// thumbnail image
							$thumb_image = wp_get_attachment_image_src( get_post_thumbnail_id() );
							if( $thumb_image ) echo ' thumbImage="' . $thumb_image[0] . '"';
							
						}
						
						?>
					/>
										
					<?php
					
					// increment counter
					$c++;
					
				endwhile;
				
				?>
				
				</outline>
			</outline>
		</body>
	</opml>
	
	<?php
}
add_action('do_feed_WBC3_hierarchy', 'WBC3_hierarchy');
