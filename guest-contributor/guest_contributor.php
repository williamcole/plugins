<?php

/*
Plugin Name: Guest Contributor
Description: Adds module for Guest Contributor name/bio/headshot on Edit Post Page 
Version: 0.1
Author: William Cole
*/

add_image_size( 'guest-contrib-square', 100, 100, true );
add_image_size( 'guest-contrib-rect', 130, 40, true );

// include JS assets
function WBC3_guest_contributor_assets() {

	wp_enqueue_script('jquery');
	wp_enqueue_script('media-upload');
	wp_enqueue_script('thickbox');
	wp_enqueue_style('thickbox');
	
	$asset_name = "guest-contributor";

	if ( function_exists( 'WBC3_add_static_js_asset' ) ) {
		WBC3_add_static_js_asset( $asset_name, array( 'jquery', 'media-upload', 'thickbox' ), '0.7' );
	}	
}
add_action( 'admin_print_styles-post.php', 'WBC3_guest_contributor_assets' );
add_action( 'admin_print_styles-post-new.php', 'WBC3_guest_contributor_assets' );

// action to add the panel to the editor page
function WBC3_guest_contributor_add_admin_panel() {
	add_meta_box(
		'WBC3_guest_contributor_editor',
		__('Guest Contributor'),
		'WBC3_guest_contributor_editor',
		'post',
		'normal',
		'low'
	);
}
add_action( 'add_meta_boxes', 'WBC3_guest_contributor_add_admin_panel', 10 );
add_action( 'admin_init', 'WBC3_guest_contributor_add_admin_panel', 10 );

// editor panel
function WBC3_guest_contributor_editor() {

	global $post;

	// add nonce field
	wp_nonce_field( 'WBC3_guest_contributor_meta_box', 'WBC3_guest_contributor_meta_box_nonce' );

	// get saved meta values
	$gc_position = get_post_meta( $post->ID, 'WBC3_guest_contributor_position', true );
	$gc_intro = get_post_meta( $post->ID, 'WBC3_guest_contributor_intro', true );
	$gc_name = get_post_meta( $post->ID, 'WBC3_guest_contributor_name', true );
	$gc_url = get_post_meta( $post->ID, 'WBC3_guest_contributor_url', true );
	$gc_bio = get_post_meta( $post->ID, 'WBC3_guest_contributor_bio', true );
	$gc_image = get_post_meta( $post->ID, 'WBC3_guest_contributor_image', true );
	$gc_image_id = (int) get_post_meta( $post->ID, 'WBC3_guest_contributor_image_id', true );
	
	?>
	<div id="vp_main">
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row" style="width:100px">
						<label for="WBC3_guest_contributor_position">Position</label>
					</th>
					<td>
						<select id="WBC3_guest_contributor_position" name="WBC3_guest_contributor_position">
							<option value="none" <?php if( 'none' == $gc_position ) echo 'selected'; ?>>(NONE)</option>
							<?php 
								// BrandVoice blogs should only have bottom option
								if( 'ad' != WBC3_get_blog_type( get_current_blog_id() ) ) { ?>
									<option value="top" <?php if( 'top' == $gc_position ) echo 'selected'; ?>>Top of Post</option>
							<?php } ?>
							<option value="bottom" <?php if( 'bottom' == $gc_position ) echo 'selected'; ?>>Bottom of Post</option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row" style="width:100px">
						<label for="WBC3_guest_contributor_intro">Qualifier / Intro</label>
					</th>
					<td>
						<select id="WBC3_guest_contributor_intro" name="WBC3_guest_contributor_intro">
						<?php

						foreach( WBC3_guest_contributor_intro_options() as $key => $value ) {
							$selected = ( $key == $gc_intro ) ? 'selected' : '';
							echo '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
						}

						?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row" style="width:100px">
						<label for="WBC3_guest_contributor_name">Name</label>
					</th>
					<td>
						<input type="text" id="WBC3_guest_contributor_name" name="WBC3_guest_contributor_name" value="<?php if( isset( $gc_name ) ) echo $gc_name; ?>" class="regular-text" />
    				</td>
				</tr>
				<tr>
					<th scope="row" style="width:100px">
						<label for="WBC3_guest_contributor_url">URL</label>
					</th>
					<td>
						<input type="text" id="WBC3_guest_contributor_url" name="WBC3_guest_contributor_url" value="<?php if( isset( $gc_url ) ) echo $gc_url; ?>" class="regular-text" />
    				</td>
				</tr>
				<tr>
					<th scope="row" style="width:100px">
						<label for="WBC3_guest_contributor_image">Image</label>
					</th>
					<td>
						<?php

						// get image
						$preview_image = WBC3_guest_contributor_image( get_the_ID() );
						
						// determine aspect ratio
						//$aspect_ratio = ( $preview_image[1] == $preview_image[2] ) ? 'square' : 'rect';

						?>
						<img id="WBC3_guest_contributor_image_preview" src="<?php echo esc_url( $preview_image[0] ); ?>">
						<input type="hidden" name="WBC3_guest_contributor_image_id" id="WBC3_guest_contributor_image_id" value="<?php echo (int) $gc_image_id; ?>" class="regular-text" />
						<input type="text" name="WBC3_guest_contributor_image" id="WBC3_guest_contributor_image" value="<?php echo esc_url( $gc_image ); ?>" class="regular-text" readonly />
						<div>
							<input type="button" class="button" value="Upload Image" id="WBC3_guest_contributor_upload_image" />
							<input type="button" class="button" value="Remove Image" id="WBC3_guest_contributor_remove_image" />
						</div>
						<p class="description">Valid Image Sizes: 100x100 or 130x40</p>
					</td>
				</tr>
				<tr>
					<th scope="row" style="width:100px">
						<label for="WBC3_guest_contributor_bio">Bio</label>
					</th>
					<td>
						<p class="description">140 Character Limit &mdash; <span id="characters-remaining">0</span></p>
						<?php
							wp_editor( $gc_bio, 'WBC3_guest_contributor_bio', array(
								'teeny' => true,
								'media_buttons' => false,
								'quicktags' => false,
								'tinymce' => array(
							        'theme_advanced_buttons1' => 'link,unlink',
							        'theme_advanced_buttons2' => '',
							        'theme_advanced_buttons3' => ''
							    )
							) );							
						?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<?php
}

// save meta values
function WBC3_guest_contributor_save_meta_box( $post_id ) {

	// verify nonce
    if( isset( $_POST['WBC3_guest_contributor_meta_box_nonce'] ) && !wp_verify_nonce( $_POST['WBC3_guest_contributor_meta_box_nonce'], 'WBC3_guest_contributor_meta_box' ) ) {
		return $post_id;
	}

	// check autosave
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return $post_id;
    }

	// save meta values if they are set
	if( isset( $_POST['WBC3_guest_contributor_position'] ) ) {
        update_post_meta( $post_id, 'WBC3_guest_contributor_position', sanitize_text_field( $_POST['WBC3_guest_contributor_position'] ) );
    }
    if( isset( $_POST['WBC3_guest_contributor_intro'] ) ) {
        update_post_meta( $post_id, 'WBC3_guest_contributor_intro', sanitize_text_field( $_POST['WBC3_guest_contributor_intro'] ) );
    }
    if( isset( $_POST['WBC3_guest_contributor_name'] ) ) {
        update_post_meta( $post_id, 'WBC3_guest_contributor_name', sanitize_text_field( $_POST['WBC3_guest_contributor_name'] ) );
    }
    if( isset( $_POST['WBC3_guest_contributor_url'] ) && filter_var( $_POST['WBC3_guest_contributor_url'], FILTER_VALIDATE_URL ) ) {
        update_post_meta( $post_id, 'WBC3_guest_contributor_url', esc_url( $_POST['WBC3_guest_contributor_url'] ) );
    } else {
        delete_post_meta( $post_id, 'WBC3_guest_contributor_url' );
    }
    if( isset( $_POST['WBC3_guest_contributor_image'] ) ) {
        update_post_meta( $post_id, 'WBC3_guest_contributor_image', sanitize_text_field( $_POST['WBC3_guest_contributor_image'] ) );
    }
	if( isset( $_POST['WBC3_guest_contributor_image_id'] ) ) {
        update_post_meta( $post_id, 'WBC3_guest_contributor_image_id', (int) $_POST['WBC3_guest_contributor_image_id'] );
    }
	if( isset( $_POST['WBC3_guest_contributor_bio'] ) ) {

		// remove non-link html tags
		$bio = strip_tags( $_POST['WBC3_guest_contributor_bio'], '<a>' );

		// remove shortcodes
		$bio = strip_shortcodes( $bio );
		
		// update field
		update_post_meta( $post_id, 'WBC3_guest_contributor_bio', $bio );
    }
}
add_action( 'save_post', 'WBC3_guest_contributor_save_meta_box' );

/*
 * Helper Functions
 */

// make sure Guest Contrib is enabled
function WBC3_guest_contributor_is_enabled( $post_id = null ) {

	if( !isset( $post_id ) )
		return;

	if( 'none' == get_post_meta( $post_id, 'WBC3_guest_contributor_position', true ) ) {
		return false;
	} else {
		return true;
	}
}

// qualifier/intro values
function WBC3_guest_contributor_intro_options() {
	return array(
		'interview' => 'An interview with',
		'post' => 'Post written by',
		'guest-post' => 'Guest post written by',
		'published' => 'Published on',
	);
}

function WBC3_guest_contributor_get_intro_option( $key = null ) {

	if( !isset( $key ) )
		return;

	$options = WBC3_guest_contributor_intro_options();
	
	return $options[$key];
}

function WBC3_guest_contributor_image( $post_id = null ) {

	if( !isset( $post_id ) )
		return;

	// get guest contributor image
	$image_id = get_post_meta( $post_id, 'WBC3_guest_contributor_image_id', true );

	if( $image_id ) {

		// get dimensions of original image
		$orig_image = wp_get_attachment_metadata( $image_id );
		
		if( $orig_image ) {

			/*
			
			// check for exact 130 x 40 rectangle
			if( ( $orig_image['width'] == 130 ) && ( $orig_image['height'] == 40 ) ) {
				$image = wp_get_attachment_image_src( $image_id, 'guest-contrib-rect' );
			} else {
				$image = wp_get_attachment_image_src( $image_id, 'guest-contrib-square' );
			}

			*/

			$image = wp_get_attachment_image_src( $image_id, 'full' );

		} else {
			$image = null;
		}

	} else {
		$image = null;
	}

	return $image;
}

/*
 * API Helper Functions
 */

function WBC3_api_guest_contributor_position( $post_id = null ) {
	
	if( !isset( $post_id ) )
		return;

	$position = get_post_meta( $post_id, 'WBC3_guest_contributor_position', true );
	
	if( ( $position == 'top' ) || ( $position == 'bottom' ) ) {
		return $position;
	} else {
		return null;
	}
}

function WBC3_api_guest_contributor_intro( $post_id = null ) {
	
	if( !isset( $post_id ) )
		return;

	$intro = null;

	if( WBC3_guest_contributor_is_enabled( $post_id ) ) {
		$intro = WBC3_guest_contributor_get_intro_option( get_post_meta( $post_id, 'WBC3_guest_contributor_intro', true ) );
	}

	return $intro;
}

function WBC3_api_guest_contributor_type( $post_id = null ) {
	
	if( !isset( $post_id ) )
		return;

	$type = null;

	if( WBC3_guest_contributor_is_enabled( $post_id ) ) {
		$type = get_post_meta( $post_id, 'WBC3_guest_contributor_intro', true );
	}

	return $type;
}

function WBC3_api_guest_contributor_name( $post_id = null ) {
	
	if( !isset( $post_id ) )
		return;

	$name = null;

	if( WBC3_guest_contributor_is_enabled( $post_id ) ) {
		$name = get_post_meta( $post_id, 'WBC3_guest_contributor_name', true );
	}

	return $name;
}

function WBC3_api_guest_contributor_url( $post_id = null ) {
	
	if( !isset( $post_id ) )
		return;

	$url = null;

	if( WBC3_guest_contributor_is_enabled( $post_id ) ) {
		$url = get_post_meta( $post_id, 'WBC3_guest_contributor_url', true );
	}

	return $url;
}

function WBC3_api_guest_contributor_bio( $post_id = null ) {
	
	if( !isset( $post_id ) )
		return;

	$bio = null;

	if( WBC3_guest_contributor_is_enabled( $post_id ) ) {
		$bio = get_post_meta( $post_id, 'WBC3_guest_contributor_bio', true );
	}

	return $bio;
}

function WBC3_api_guest_contributor_image_src( $post_id = null ) {
	
	if( !isset( $post_id ) )
		return;

	$src = null;

	if( WBC3_guest_contributor_is_enabled( $post_id ) ) {

		$image = WBC3_guest_contributor_image( $post_id );
		
		if( $image ) {
			$src = $image[0];
		}
	}

	return $src;
}

function WBC3_api_guest_contributor_image_type( $post_id = null ) {
	
	if( !isset( $post_id ) )
		return;

	$type = null;

	if( WBC3_guest_contributor_is_enabled( $post_id ) ) {
	
		// get guest contributor image id
		$image_id = get_post_meta( $post_id, 'WBC3_guest_contributor_image_id', true );
		
		if( $image_id ) {

			// get dimensions of original image
			$orig_image = wp_get_attachment_metadata( $image_id );
			
			// check image size
			if( count( $orig_image ) ) { 
				if( $orig_image['width'] == $orig_image['height'] ) {
					$type = 'square';
				} else {
					$type = 'rect';
				}
			}
		}
	}

	return $type;
}

?>