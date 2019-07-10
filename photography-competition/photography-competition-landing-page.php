<?php
/**
 * Template Name: Photography Competition: Landing
 */


get_header(); ?>
	<div id="columns">
		<?php get_sidebar(); ?>
		<div id="container">
			<div id="content" role="main">

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

				<div id="contest" class="landing">
				
					<?php echo WBC3_photo_contest_banner_image( $link = false ); ?>
					
					<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<div class="entry-content">
							
							<div class="contest-landing-content">
								<?php the_content(); ?>
							</div>
							
							<?php echo WBC3_photo_contest_display_text(); ?>
							
							<div class="contest-share-links">
								<h4>Help us find the winner: Share with friends<br>on Facebook and Twitter <em>(#NextGenPhotog)</em></h4>
								<div class="entry-share">
									<div class="like-btn">
										<fb:like href="http://lightbox.company.com/nextgen/" layout="button_count" show_faces="false"></fb:like>
									</div>
									<div class="tweet-btn">
										<a href="http://twitter.com/share" class="twitter-share-button" data-text="CONTEST: @COMPANY is looking for the next generation of great photographers. Here's how to enter. #NextGenPhotog" data-url="" data-count="horizontal">Tweet</a>
									</div>
									<div class="clear"></div>
								</div>
								<div class="clear"></div>
							</div>
							<div class="starbucks-logo">
								<img src="<?php bloginfo('stylesheet_directory'); ?>/images/sponsored_by_starbucks.png" alt="Sponsored by Starbucks" title="Sponsored by Starbucks">
							</div>
							
							<div class="clear"></div>
							
							<div class="contest-column col-1">
								<?php
								
								if( get_post_meta($post->ID, 'photo_contest_landing_text_col1', true) ) {
									echo wpautop( get_post_meta($post->ID, 'photo_contest_landing_text_col1', true) );
								}
								
								echo WBC3_photo_contest_form_link();
								
								if( get_post_meta($post->ID, 'photo_contest_landing_text_credits', true) ) {
									echo wpautop( get_post_meta($post->ID, 'photo_contest_landing_text_credits', true) );
								}
								
								?>
							</div>
							<div class="contest-column col-2">
								<?php
								
								if( get_post_meta($post->ID, 'photo_contest_landing_text_col2', true) ) {
									echo wpautop( get_post_meta($post->ID, 'photo_contest_landing_text_col2', true) );
								}
								
								?>
							</div>
							<div class="contest-column col-3">
								<?php
								
								if( get_post_meta($post->ID, 'photo_contest_landing_text_col3', true) ) {
									echo wpautop( get_post_meta($post->ID, 'photo_contest_landing_text_col3', true) );
								}
								
								?>
							</div>
							
							<div class="clear"></div>
							
						</div><!-- .entry-content -->
					</div><!-- #post-## -->
				
				</div><!-- #contest -->

<?php endwhile; ?>

			</div><!-- #content -->
		</div><!-- #container -->
	</div><!-- #columns -->
<?php get_footer(); ?>
