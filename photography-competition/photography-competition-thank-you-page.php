<?php
/**
 * Template Name: Photography Competition: Thank You
 */


get_header(); ?>
	<div id="columns">
		<?php get_sidebar(); ?>
		<div id="container">
			<div id="content" role="main">

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

				<div id="contest">
				
					<?php echo WBC3_photo_contest_banner_image(); ?>
					
					<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<div class="entry-content">
							<?php the_content(); ?>
							
							<div class="entry-share">
								<div class="like-btn">
									<fb:like href="http://lightbox.company.com/nextgen/" layout="button_count" show_faces="true"></fb:like>
								</div>								
								<div class="tweet-btn follow">
									<a href="https://twitter.com/TIME" class="twitter-follow-button" data-show-count="false">Follow @COMPANY</a>
									<script src="//platform.twitter.com/widgets.js" type="text/javascript"></script>
								</div>
							</div>							
							<div class="clear"></div>
							
							<?php echo WBC3_parent_page_link(); ?>
						</div><!-- .entry-content -->
					</div><!-- #post-## -->
				
				</div><!-- #contest -->

<?php endwhile; ?>

			</div><!-- #content -->
		</div><!-- #container -->
	</div><!-- #columns -->
<?php get_footer(); ?>
