<?php
/*
 * Template Name: Newsletter Page: Text Version
 */
?>

LIGHTBOX
<?php echo date('l, j F Y'); ?>


---------------------------------------------------------------------
<?php
	// declare global more variable to properly display content excerpt
	global $more;
	$count = 0;
	
	// get most recent posts
	$posts = get_posts( 'posts_per_page=5' );
	
	foreach( $posts as $post ) : setup_postdata($post);
		
		$more = 0;
		$count++;
		
		if( $count == 1 ) {
			?>
			
<?php echo '<!-- From: '.get_bloginfo('name').' - company.com -->'; ?>

<?php echo '<!-- Subject: Must-See Photo Essays and Pictures of the Week for '.date('F j, Y').' -->'; ?>
			
			<?php			
		}
		
		// category
		$category = get_the_category();
		?>

<?php echo esc_html( $category[0]->name ); ?>

<?php the_title(); ?>

<?php WBC3_newsletter_xid( get_permalink() ); ?>

<?php echo 'By '.get_the_author(); ?>
		
		<?php		
	endforeach;
?>

---------------------------------------------------------------------

Copyright &copy; <?php echo date('Y'); ?> Time Inc. All rights reserved.
Reproduction in whole or in part without permission is prohibited.

TO UNSUBSCRIBE
You have received this e-mail because you are subscribed to this newsletter from COMPANY Magazine. To unsubscribe, please click here:
http://ebm.cheetahmail.com/r/webunsub?t=%%t%%&n=1&email=%%email%%

EMAIL OPT-OUTS
Click here for more information on how to opt-out of marketing communications from us and our partners, or copy and paste this link into your browser:
http://www.company.com/emailprivacy

PRIVACY POLICY
Please read our Privacy Policy, or copy and paste this link into your browser:
http://www.company.com/privacy

FOR FURTHER COMMUNICATION, PLEASE CONTACT:
COMPANY Customer Service
3000 University Center Drive
Tampa, FL 33612-6408

---------------------------------------------------------------------

How To Advertise
http://www.company.com/company/mediakit/index.shtml

Give the Gift of TIME
https://subs.company.net/TD/tdnewdonor_20cconly.jhtml?experience_id=175365&source_id=2&_requestid=631869

Update Email
http://ebm.cheetahmail.com/r/webunsub?t=%%t%%&n=2&email=%%email%%

