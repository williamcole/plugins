<?php

/*
Plugin Name: Company Signature File
Description: Appends signature file to posts, with the ability to disable 
Version: 0.1
Author: William Cole
*/

// include JS assets
function WBC3_sigfile_assets() {

	$asset_name = 'sig-file';

	if( function_exists( 'WBC3_add_static_js_asset' ) ) {
		WBC3_add_static_js_asset( $asset_name, false, '0.6' );
	}	
}
add_action( 'edit_user_profile', 'WBC3_sigfile_assets' );
add_action( 'show_user_profile', 'WBC3_sigfile_assets' );

// action to add the panel to the editor page
function WBC3_sigfile_add_admin_panel() {
	add_meta_box( 'WBC3_sigfile_panel', __('Signature File'), 'WBC3_sigfile_panel', 'post', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'WBC3_sigfile_add_admin_panel', 10 );
add_action( 'admin_init', 'WBC3_sigfile_add_admin_panel', 10 );

// editor panel
function WBC3_sigfile_panel() {

	global $post;
	
	// get if sigfile exists
	$sigfile = get_user_meta( $post->post_author, 'sigfile', true );

	// get checkbox status
	$enable_sigfile = get_post_meta( $post->ID, 'enable_sigfile', true );
	
	// determine whether to check the box or not
	$checked = ( !empty( $sigfile ) && ( $enable_sigfile == 'on' ) ) ? 'checked' : '';

	// nonce field for sigfile checkbox
	wp_nonce_field( basename( __FILE__ ), 'enable_sigfile_nonce' );

	// display error message if no sig file set
	$disabled = ( empty( $sigfile ) ) ? true : false;

	?>

	<p><input type="checkbox" name="enable_sigfile" id="enable_sigfile" <?php echo $checked; if( $disabled ) echo ' disabled="disabled"'; ?>> Enable signature file for this post</p>
	<?php if( $disabled ) echo '<p><strong>You do not have a signature file saved.</strong></p>'; ?> 
	<p><a href="<?php echo get_edit_user_link() . '#signature-file'; ?>" target="_blank">Create or Edit your signature file</a></p>
	
	<?php
}

// save sigfile meta data
function WBC3_sigfile_save_post( $post_id ) {

	/* Verify the nonce before proceeding. */
	if ( !isset( $_POST['enable_sigfile_nonce'] ) || !wp_verify_nonce( $_POST['enable_sigfile_nonce'], basename( __FILE__ ) ) )
		return $post_id;
	
	// check autosave
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return $post_id;
    }

    // save/update the meta field in the database
	$enable_sigfile = ( !empty( $_POST['enable_sigfile'] ) ) ? $_POST['enable_sigfile'] : '';
	update_post_meta( $post_id, 'enable_sigfile', $enable_sigfile );

}
add_action( 'save_post', 'WBC3_sigfile_save_post' );

?>