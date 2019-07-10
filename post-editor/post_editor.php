<?php

/*
Plugin Name: Company Editor
Description: Customize post editor options
Version: 0.1
Author: William Cole
*/

/*
 * Editor and Images
 */

// include CSS and JS files for the post editor
function WBC3_editor_assets() {

	$asset_name = 'editor';

	if( function_exists( 'WBC3_add_static_js_asset' ) ) {
		WBC3_add_static_js_asset( $asset_name, false );
	}

	if( function_exists( 'WBC3_add_static_css_asset' ) ) {
		WBC3_add_static_css_asset( $asset_name, false );
	}
}
add_action( 'admin_print_styles-post.php', 'WBC3_editor_assets' );
add_action( 'admin_print_styles-post-new.php', 'WBC3_editor_assets' );

// add custom MCE editor style
function WBC3_editor_mce_css( $mce_css ) {	
	$base_url_to_use = WBC3_cdn();
	if( empty( $base_url_to_use ) ) {
		$base_url_to_use = "http://" . DOMAIN_CURRENT_SITE;
	}
	$mce_css .= ', ' . $base_url_to_use . '/assets/css/editor-mce.css';
	return $mce_css;
}
add_filter( 'mce_css', 'WBC3_editor_mce_css' );

// remove tinymce buttons from the post editor
if( !function_exists( 'base_extended_editor_mce_buttons' ) ) {
	function WBC3_remove_tinymce_buttons( $buttons ) {
		
		$remove_btns = array(
			'alignleft',
			'aligncenter',
			'alignright',
			'alignjustify',
			'formatselect',
			'wp_more',
			'wp_page',
			'indent',
			'outdent'
		);

		foreach( $remove_btns as $btn ) {
			$key = array_search( $btn, $buttons );
			if( $key !== false ) {
			    unset( $buttons[$key] );
			}
		}
		
		return $buttons;
	}
	add_filter( 'mce_buttons', 'WBC3_remove_tinymce_buttons' );
	add_filter( 'mce_buttons_2', 'WBC3_remove_tinymce_buttons' );
}

/*
 * Template Types
 */

// add template type meta box
function WBC3_template_types_add_meta_box() {
	add_meta_box(
		'WBC3_template_type_meta_box',
		'Template Type',
		'WBC3_template_type_meta_box',
		'post',
		'side',
		'default'
	);
}
add_action( 'admin_menu', 'WBC3_template_types_add_meta_box' );

// markup for template type meta box
function WBC3_template_type_meta_box( $post ) {
	
	// add nonce field
	wp_nonce_field( 'WBC3_template_type_meta_box', 'WBC3_template_type_meta_box_nonce' );

	// get saved post type
	$template_type = get_post_meta( $post->ID, 'template_type', true );
	$default = ( !empty( $template_type ) ) ? $template_type : 'standard';

	$template_types = array(
		'standard' => 'Standard <span>Regular post with up to four pieces of media</span>',
		'takeover' => 'Hi-Rise <span>Single, tall infographic or embed included in post</span>',
		'multifeature' => 'Stacks <span>Multiple pieces of media (five or more) included in a single post</span>'	
	);

	foreach( $template_types as $key => $value ) {
		// check against post meta if it exists
		$checked = ( $key == $default ) ? 'checked="checked"' : '';
		echo sprintf( '<div class="template-type-option"><input name="template_type" type="radio" value="%s" %s> %s</div>', $key, $checked, $value );
	}
}

// save template type meta data
function WBC3_template_type_save_meta_box( $post_id ) {

	// verify nonce
    if( isset( $_POST['WBC3_template_type_meta_box_nonce'] ) && !wp_verify_nonce( $_POST['WBC3_template_type_meta_box_nonce'], 'WBC3_template_type_meta_box' ) ) {
		return $post_id;
	}

	// check autosave
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return $post_id;
    }

	// save/update the meta field in the database
	$template_type = ( !empty( $_POST['template_type'] ) ) ? $_POST['template_type'] : 'standard';
	update_post_meta( $post_id, 'template_type', $template_type );
}
add_action( 'save_post', 'WBC3_template_type_save_meta_box' );

/*
 * Hide Tags
 */

// hide tags for non-BrandVoice blogs
function WBC3_hide_tags_admin_init() {
	
	// check blog type
	if( 'ad' == WBC3_get_blog_type( get_current_blog_id() ) ) {	
		// add back tags meta box for BrandVoice blogs
		add_meta_box( 'tagsdiv-post_tag', 'BrandVoice Tags', 'post_tags_meta_box', 'post', 'side', 'default' );
	} else {
		// remove tags for non-BrandVoice blogs
		add_filter( 'manage_posts_columns', 'WBC3_hide_tags_column' );
	}	
}
add_action( 'admin_init', 'WBC3_hide_tags_admin_init' );

// hide tags column from posts page
function WBC3_hide_tags_column( $columns ) {
	unset( $columns['tags'] );	
	return $columns;
}

// hide meta boxes
function WBC3_hide_meta_from_admin_menu() {
	
	// remove author module
	// using custom Authors and Co-Authors module instead
	remove_meta_box( 'authordiv', 'post', 'normal' );

	// remove tags
	remove_meta_box( 'tagsdiv-post_tag', 'post', 'normal' );
	
	// remove trackbacks
	remove_meta_box( 'trackbacksdiv', 'post', 'normal' );

	// remove modules for non-BrandVoice blogs
	if( 'ad' !== WBC3_get_blog_type( get_current_blog_id() ) ) {	
		
		// remove BV Category Tabs meta box
		remove_meta_box( 'WBC3_subblogs_editor', 'post', 'normal' );

		// remove tags from admin menu
		remove_submenu_page( 'edit.php', 'edit-tags.php?taxonomy=post_tag' );
	}
}
add_action( 'admin_menu', 'WBC3_hide_meta_from_admin_menu' );

/*
 * Network Sites
 */

// adjust User query on Network Sites page
// remove orderby value to decrease load time
function WBC3_pre_user_query( $vars ) {

	global $pagenow;

	// only do this on Network > Sites page
    if( 'sites.php' == $pagenow ) {
	    $vars->query_orderby = "";
    }

	return $vars;
}
add_filter( 'pre_user_query', 'WBC3_pre_user_query' );

?>