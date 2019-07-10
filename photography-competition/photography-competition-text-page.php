<?php
/**
 * Template Name: Photography Competition: Text
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
							<h3><?php the_title(); ?></h3>
							
							<?php the_content(); ?>
							
							<?php echo WBC3_photo_contest_form_link(); ?>
							
							<?php echo WBC3_parent_page_link(); ?>
						</div><!-- .entry-content -->
					</div><!-- #post-## -->
				
				</div><!-- #contest -->

<?php endwhile; ?>

			</div><!-- #content -->
		</div><!-- #container -->
	</div><!-- #columns -->
<?php get_footer(); ?>
