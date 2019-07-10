<?php

/*
Plugin Name: Newsletter
Description: Description goes here
Version: 0.1
Author: William Cole
*/

// include JS assets
function WBC3_newsletter_assets() {
	global $blog_id;
	$blog_type = WBC3_get_blog_type($blog_id);

	if ($blog_type !== 'newsletter') return;

	$asset_name = 'newsletter';

	if ( function_exists( 'WBC3_add_static_js_asset' ) ) {
		WBC3_add_static_js_asset( $asset_name, array( 'media-upload', 'thickbox' ), '0.5' );
	}
}
add_action( 'admin_print_styles-post.php', 'WBC3_newsletter_assets', 50 );
add_action( 'admin_print_styles-post-new.php', 'WBC3_newsletter_assets', 50 );
add_action( 'admin_print_styles-options-general.php', 'WBC3_newsletter_assets', 50 );

add_action( 'admin_footer-edit.php', 'change_post_list_view_links' );
function change_post_list_view_links () {
	global $blog_id;
	$blog_type = WBC3_get_blog_type($blog_id);

	if ($blog_type !== 'newsletter') return;
	
?>
<script type="text/javascript" >
jQuery(document).ready(function($) {
	if ($('span.view a').length) $('span.view a').attr('href', $('span.view a').attr('href').replace('/sites/', '/newsletters/'));
});
</script>
<?php
}

add_action( 'admin_footer-user-edit.php', 'hide_website' );
add_action( 'admin_footer-profile.php', 'hide_website' );
function hide_website() {
	global $user_id;
	if (!intval($user_id)) $user_id = wp_get_current_user()->ID;
	$user_type = WBC3_get_user_type( $user_id );

	if ($user_type !== 'Newsletter') return;
	
?>
<script type="text/javascript" >
jQuery(document).ready(function($) { $('#url').closest('tr').hide(); });
</script>
<?php
}

// action to add the panel to the editor page
function WBC3_newsletter_add_admin_panel() {
	add_meta_box(
		'WBC3_newsletter_options',
		__('Newsletter Options'),
		'WBC3_newsletter_options',
		'post',
		'normal',
		'low'
	);
}
global $blog_id;
$blogtype = WBC3_get_blog_type($blog_id);
if ($blogtype == 'newsletter') {
	add_action( 'add_meta_boxes', 'WBC3_newsletter_add_admin_panel', 10 );
	add_action( 'admin_init', 'WBC3_newsletter_add_admin_panel', 10 );
}

add_action( 'wp_ajax_delete_attachment', 'delete_attachment' );
function delete_attachment() {
	$attachmentID = $_POST['attach_id'];
	wp_update_post(array('ID' => $attachmentID, 'post_parent' => 0));
}

add_action( 'wp_ajax_attach_pdf', 'attach_pdf' );
function attach_pdf() {
	$attachmentID = $_POST['attach_id'];
	$parentID = $_POST['parent_id'];
	wp_update_post(array('ID' => $attachmentID, 'post_parent' => $parentID));
}

// editor panel
function WBC3_newsletter_options() {
	global $post;

	// add nonce field
	wp_nonce_field( 'WBC3_newsletter_meta_box', 'WBC3_newsletter_meta_box_nonce' );

	// get saved meta values
	$newsletterOptions = get_post_meta( $post->ID, 'newsletterOptions', true );
?>
	<div id="newsletter_body">
		<div id="hotlineWrapper">
			<input id="hotline" type="checkbox" name="newsletterOptions[hotline]"<?php if (isset($newsletterOptions['hotline']) && $newsletterOptions['hotline'] == TRUE) echo ' checked="checked"' ?>> <label for="hotline">Hotline</label>
		</div>
		<hr style="margin: 1em 0; border: none; height: 1px; background-color: #AAA;">
		<div id="newsletter_files">
			<input type="hidden" name="nlf_postID" id="nlf_postID" value="<?php echo $post->ID; ?>">
<?php
	$args = array( 'post_mime_type' => 'application/pdf', 'post_type' => 'attachment', 'numberposts' => -1, 'post_status' => null, 'post_parent' => $post->ID );
	$attachments = get_posts($args);
	if ($attachments) {
		foreach ( $attachments as $attachment ) {
?>
			<div>
				<input id="file_<?php echo $attachment->ID; ?>" type="button" class="button" value="Remove File" />
				<span><?php echo $attachment->post_title; ?></span>
			</div>
<?php
		}
	}
?>
		</div>
		<div>
			<input type="button" class="button" value="Upload PDF" id="WBC3_newsletter_upload_pdf" />
		</div>
	</div>
	<?php
}

// save meta values
function WBC3_newsletter_save_meta_box( $post_id ) {

	// verify nonce
    if( isset( $_POST['WBC3_newsletter_meta_box_nonce'] ) && !wp_verify_nonce( $_POST['WBC3_newsletter_meta_box_nonce'], 'WBC3_newsletter_meta_box' ) ) {
		return $post_id;
	}

	// check autosave
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return $post_id;
    }

	// save meta values if they are set
	$newsletterOptions = get_post_meta( $post->ID, 'newsletterOptions', true );
    if( isset( $_POST['newsletterOptions']['hotline'] ) ) $newsletterOptions['hotline'] = TRUE;
    else $newsletterOptions['hotline'] = FALSE;
    update_post_meta( $post_id, 'newsletterOptions', $newsletterOptions );
}
add_action( 'save_post', 'WBC3_newsletter_save_meta_box' );
?>