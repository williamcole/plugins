<?php

/*
Plugin Name: Featured Images
Description: Customize featured image functionality
Version: 0.1
Author: William Cole
*/

function WBC3_image_defaults() {
	
	// set default values for image options
	update_option( 'image_default_align', 'none' );
	update_option( 'image_default_link_type', 'none' );
	update_option( 'image_default_size', 'large' );

	// increase large image size
	// max content width 970 x 2 = 1940 
	update_option( 'large_size_w', '1940' );
	update_option( 'large_size_h', '1940' );
	
}
add_action( 'after_setup_theme', 'WBC3_image_defaults' );

// add Featured Image filters/actions
function WBC3_featured_image_admin_init() {

	global $post;
	
	// enable featured image support
	add_theme_support( 'post-thumbnails' );

	// create Square Image post thumbnail
	if( class_exists( 'MultiPostThumbnails' ) ) {
	    new MultiPostThumbnails(
	    	array(
	            'label' => 'Square Image',
	            'id' => 'square-image',
	            'post_type' => 'post'
	        )
	    );
	}

	add_filter( 'media_view_strings', 'WBC3_remove_featured_image_tab' );
	add_action( 'admin_enqueue_scripts', 'WBC3_featured_images_assets' );
	add_action( 'admin_enqueue_scripts', 'WBC3_featured_images_toggle_assets' );
	add_action( 'do_meta_boxes', 'WBC3_render_new_post_thumbnail_meta_box' );

	// Featured Image should be available but hidden by default for non-staff
	if( 'Staff' !== WBC3_get_user_type( get_current_user_id() ) ) {
		add_action( 'admin_enqueue_scripts', 'WBC3_featured_images_hide_assets' );
	}
}
add_action( 'admin_init', 'WBC3_featured_image_admin_init' );

// remove the Set Featured Image tab from the media gallery
function WBC3_remove_featured_image_tab( $strings ) {
	unset( $strings['setFeaturedImageTitle'] );
	return $strings;
}

// include JS for Featured Images
function WBC3_featured_images_assets() {
	$asset_name = 'featured-images';
	if( function_exists( 'WBC3_add_static_js_asset' ) ) {
		WBC3_add_static_js_asset( $asset_name, array( 'jquery' ), '1.1' );
	}
}

// include JS to toggle Featured Images depending on template type
function WBC3_featured_images_toggle_assets() {
	$asset_name = 'featured-images-toggle';
	if( function_exists( 'WBC3_add_static_js_asset' ) ) {
		WBC3_add_static_js_asset( $asset_name, array( 'jquery' ), '1.1' );
	}
}

// include JS to hide Featured Images by default
function WBC3_featured_images_hide_assets() {
	$asset_name = 'featured-images-hide';
	if( function_exists( 'WBC3_add_static_js_asset' ) ) {
		WBC3_add_static_js_asset( $asset_name, array( 'jquery' ), '1.1' );
	}
}

// get featured image meta attributes
function WBC3_get_featured_image_meta( $post_id = null, $type = 'featured' ) {
	
	global $post;
	$post_id = ( isset( $post_id ) ) ? $post_id : $post->ID;

	// get the featured image id
	switch( $type ) {
		case 'featured':
			$image_id = get_post_thumbnail_id( $post_id );
		break;
		
		case 'square':
			$image_id = MultiPostThumbnails::get_post_thumbnail_id( 'post', 'square-image', $post_id );
		break;
	}
	
	if( $image_id ) {

		$image_meta = array();

		// get image src, width, height
		$image_data = wp_get_attachment_image_src( $image_id, 'large' );
		
		// set url, width, height
		if( $image_data ) {

			// check for Company Life post
			$subtype = get_post_meta( $post_id, 'WBC3_subtype', true );
			
			if( $subtype == 'companylife' ) {

				$resized_image = vt_resize( $image_id, NULL, 1152, 1152, false );
				
				if( !empty( $resized_image ) ) {
					$image_meta['url'] = $resized_image['url'];
					$image_meta['width'] = $resized_image['width'];
					$image_meta['height'] = $resized_image['height'];
				}

			} else {

				$image_meta['url'] = $image_data[0];
				$image_meta['width'] = $image_data[1];
				$image_meta['height'] = $image_data[2];
			
			}
		}

		// get image title, caption, description
		$image = get_posts( array(
			'p' => $image_id,
			'post_type' => 'attachment',
			'posts_per_page' => 1
		) );
		
		// set title, caption, description
		if( $image && isset( $image[0] ) ) {
			$image_meta['title'] = $image[0]->post_title;
			$image_meta['caption'] = $image[0]->post_excerpt;
			$image_meta['description'] = $image[0]->post_content;
		}

		// set alt text
		$alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
		
		if( !empty( $alt ) ) {
			$image_meta['alt'] = $alt;
		}
	} else {
		$image_meta = null;
	}
	
	return $image_meta;
}

// get the main image for the post
function WBC3_api_get_post_thumbnail( $blog_id = null, $post_id = null ) {

	$post_thumbnail = '';

	if( isset( $blog_id ) && isset( $post_id ) ) {
		
		if( has_post_thumbnail( $post_id ) && ( 'standard' == get_post_meta( $post_id, 'template_type', true ) ) ) {
			
			// featured image
			$image_id = get_post_thumbnail_id( $post_id );
			
			// resize image
			$resized_featured_image = vt_resize( $image_id, NULL, 300, 169, true );
			
			if( !empty( $resized_featured_image ) ) {
				$post_thumbnail = $resized_featured_image['url'];
			}
			
		} elseif( class_exists( 'MultiPostThumbnails' ) && MultiPostThumbnails::get_the_post_thumbnail( 'post', 'square-image', $post_id ) ) {

			// square image
			$image_id = MultiPostThumbnails::get_post_thumbnail_id( 'post', 'square-image', $post_id );

			// resize image
			$resized_square_image = vt_resize( $image_id, NULL, 300, 300, true );
			
			if( !empty( $resized_square_image ) ) {
				$post_thumbnail = $resized_square_image['url'];
			}

		} else {

			// check for images embedded in content

			// never call the thumbnail generator unless the post has been published
			if( 'publish' == get_post_status( $post_id ) ) {
				$post_thumbnail = ppi_get_post_thumbnail_url( 'o', $blog_id, $post_id );
				error_log( 'IMAGE THUMB 1 - ppi_get_post_thumbnail_url() ' . $post_thumbnail );
			}

			if( !empty( $post_thumbnail ) ) {
				$post_thumbnail = apply_filters( 'cdn_wrapper', $post_thumbnail );
				error_log( 'IMAGE THUMB 2 - cdn_wrapper() ' . $post_thumbnail );
			} else {
				$post_thumbnail = ''; // don't send false or 0 ever; CSR-169
			}

			error_log( 'IMAGE THUMB FINAL ' . $post_thumbnail );
					
		}
	}

	return $post_thumbnail;
}

// return array of Featured Image data
function WBC3_api_get_featured_images( $post_id = null ) {
	
	if( isset( $post_id ) ) {
		
		$data = array();

		// Featured Image only available for Standard template type
		if( has_post_thumbnail( $post_id ) && ( 'standard' == get_post_meta( $post_id, 'template_type', true ) ) ) {
			$data['featured'] = WBC3_get_featured_image_meta( $post_id, $type = 'featured' );
		}

		// Square Image available for all template types
		if( class_exists( 'MultiPostThumbnails' ) && MultiPostThumbnails::get_the_post_thumbnail( 'post', 'square-image', $post_id ) ) {
			$data['square'] = WBC3_get_featured_image_meta( $post_id, $type = 'square' );
		}

		if (sizeof($data)) return json_encode( $data );
		else return NULL;

	} else {
		return null;
	}
}

// custom Featured Image meta box
function WBC3_post_thumbnail_meta_box() {

	global $post;

	// 16:9 aspect ratio
	$minimum = array(
    	'width' => '970',
    	'height' => '546'
    );

	if( has_post_thumbnail() ) {
    	
    	// get the featured image id
		$image_id = get_post_thumbnail_id( $post->ID );
		
		// get image src, width, height
		$image_data = wp_get_attachment_image_src( $image_id, 'large' );
		
		// enforce minimum image size
		if( $image_data ) {
			
			// get width and height
			$width = $image_data[1];
			$height = $image_data[2];

			// error if image is not 16:9 aspect ratio also
			if( ( $width < $minimum['width'] ) || ( $height < $minimum['height'] ) ) {

				// delete post thumbnail
				delete_post_thumbnail( $post );

				// display error message
				echo '<div class="error"><p>Featured Image dimensions are too small: ' . $width . 'px by ' . $height . 'px. Minimum dimensions are <strong>' . $minimum['width'] . 'px by ' . $minimum['height'] . 'px</strong>.</p></div>';
			}
		}
    }

    // display the html markup for the thumbnail
	echo _wp_post_thumbnail_html( get_post_meta( $post->ID, '_thumbnail_id', true ) );
}

// remove default Featured Image meta box and add our customized meta box in its place
function WBC3_render_new_post_thumbnail_meta_box() {
	remove_meta_box( 'postimagediv', 'post', 'side' );
    add_meta_box( 'postimagediv', __('Featured Image'), 'WBC3_post_thumbnail_meta_box', 'post', 'side', 'default' );
}

// helper function that parses content and checks for image in first 750 characters
function WBC3_has_embedded_image( $content ) {

	// replace html space entities
	$content = str_replace( '&nbsp;', ' ', $content );

	// replace escaped apostrophes
	$content = str_replace( "\'", "'", $content );

	// ignore line breaks
	$content = str_replace( "\n", '', $content );

	// ignore brightcove shortcodes
	$content = preg_replace( "~\[brightcove[^\]]+\]~", "", $content );

	// ignore tweet_quote shortcodes but keep text content inside them
	$content = preg_replace( "~\[tweet_quote[^\]]+\]~", "", $content ); 
	$content = preg_replace( "~\[\/tweet_quote\]~", "", $content );

	// ignore entity shortcodes but keep text content inside them
	$content = preg_replace( "~\[entity[^\]]+\]~", "", $content ); 
	$content = preg_replace( "~\[\/entity\]~", "", $content );

	// get first 750 chars of content
	// 755 accounts for 1 line return after 1st paragraph and first 4 chars of image and caption)
	$content_chunk = substr( $content, 0, 755 );

	// check for both image and caption strings
	$image_pos = strpos( $content_chunk, '<img' );
	$caption_pos = strpos( $content_chunk, '[cap' );
 	
	// check for image in first paragraph
	// dont assume closed <p> tags, in rare cases there may be classes or inline styling
	$filtered_content = apply_filters( 'the_content', $content );
	$filtered_content_parts = explode( '<p', wpautop( $filtered_content ) );
	$first_paragraph_pos = strpos( $filtered_content_parts[1], '<img' );
	
	if( ( $image_pos !== false ) || ( $caption_pos !== false ) || ( $first_paragraph_pos !== false ) ) {
		return true;
	} else {
		return false;
	}
}

// make sure post content doesn't begin with an image
function WBC3_embedded_image_validation( $data, $postarr ) {
	
	// remove alignment classes from post content
	$find = array( 'alignnone', 'alignleft', 'aligncenter', 'alignright' );
	$replace = array( '', '', '', '' );
	$data['post_content'] = str_replace( $find, $replace, $data['post_content'] );

	// this filter actually runs twice: once to save the post, and again to save the revision
	// we need to check if is this a revision or not for this to work properly
	if( $data['post_parent'] == 0 ) {
		$is_revision = false;
	} else {
		$is_revision = true;
	}

	if( isset( $postarr['template_type'] ) ) { 
		
		// get template type
		$template_type = $postarr['template_type'];
		
		// get post status
		$status = get_post_status( $data['post_parent'] );

		// apply embedded image restrictions to non-admins
	 	if( !current_user_can('administrator') ) {
			
			// if this is not a revision, and is standard template type, and image exists
			if( !$is_revision && ( $template_type == 'standard' ) && WBC3_has_embedded_image( $data['post_content'] ) ) {
				
				if( $status != 'publish' ) {
					// save new post as a draft, do not publish
					$data['post_status'] = 'draft';
					// add filter to display proper Post Draft Updated notice
					add_filter( 'redirect_post_location', 'WBC3_embedded_image_draft_redirect_filter', 99 );
				}

				if( $status == 'publish' ) {
					// add filter to remove 'post updated' notice
					add_filter( 'redirect_post_location', 'WBC3_embedded_image_publish_redirect_filter', 99 );
				}
				
				// add filter to display error message
				add_filter( 'redirect_post_location', 'WBC3_embedded_image_redirect_filter', 99 );

				// dont send data to api if image exists
				remove_action( 'save_post', 'WBC3_notify_post', 110 );
			}
		}
	}

	return $data;
}
add_filter( 'wp_insert_post_data', 'WBC3_embedded_image_validation', '99', 2 );
 
// add location redirect filter
function WBC3_embedded_image_redirect_filter( $location ) {
	remove_filter( 'redirect_post_location', __FILTER__, 99 );
	$location = add_query_arg ( 'embedded_featured_image', 1, $location );
	return $location;
}

function WBC3_embedded_image_draft_redirect_filter( $location ) {
	remove_filter( 'redirect_post_location', __FILTER__, 99 );
	$location = add_query_arg ( 'message', 7, $location );
	return $location;
}

function WBC3_embedded_image_publish_redirect_filter( $location ) {
	remove_filter( 'redirect_post_location', __FILTER__, 99 );
	$location = remove_query_arg ( 'message', $location );
	return $location;
}

// update admin notice if post content begins with an image
function WBC3_embedded_image_admin_notices() {
	
	if( !isset( $_GET['embedded_featured_image'] ) )
		return;

	// display error message
	switch( absint( $_GET['embedded_featured_image'] ) ) {
		case 1:
			$message = 'Please move the image lower (after first 750 characters in the post) or remove it.';
			break;
		default:
			$message = 'Unexpected error';
	}
	
	echo '<div class="error"><p>' . $message . '</p></div>';
}
add_action( 'admin_notices', 'WBC3_embedded_image_admin_notices' );

// remove image width and height attributes from image tags
function WBC3_strip_image_dimensions( $content ) {

	if( !$content )
		return;

	$dom = new DOMDocument();

	// prepend with UTF-8 declaration to handle special characters
	$dom->loadHTML( '<?xml version="1.0" encoding="UTF-8"?>' . $content );
	
	// remove width and height from image tags
	$imgs = $dom->getElementsByTagName('img');
	for( $i = 0; $i < $imgs->length; $i++ ) {
		$img = $imgs->item($i);
		if( $img->hasAttribute('width') ) {
			$img->removeAttribute('width');
		}
		if( $img->hasAttribute('height') ) {
			$img->removeAttribute('height');
		}
		if( $img->hasAttribute('style') ) {
			$img->removeAttribute('style');
		}
	}

	// remove inline styling from image caption divs
	$divs = $dom->getElementsByTagName('div');
	for( $i = 0; $i < $divs->length; $i++ ) {
		$div = $divs->item($i);
		if( strpos( $div->getAttribute('class'), 'wp-caption') !== false ) {
			if( $div->hasAttribute('style') ) {
				$div->removeAttribute('style');
			}
		}
	}

	$content = $dom->saveHTML();

	// remove the UTF-8 header and html/body from the content html
	$content = str_replace( array( '<?xml version="1.0" encoding="UTF-8"?>', '<html>', '</html>', '<body>', '</body>' ), array( '', '', '', '', '' ), $content );
	
	// remove doctype that was added by utf-8 conversion
	$content = preg_replace( "~\<!DOCTYPE[^>]+\>~", "", $content );

	return $content;
}
add_filter( 'WBC3_api_pre_send_content', 'WBC3_strip_image_dimensions' );

function WBC3_api_remove_embedded_images_from_preview( $content ) {

	if( !current_user_can('administrator') && WBC3_has_embedded_image( $content ) ) {

		// get content pieces
		$content_first = substr( $content, 0, 750 );
		$content_last = substr( $content, 750 );

		// remove caption
		$caption_pos = strpos( $content_first, '[caption' );
		if( $caption_pos !== false ) {
			$content_first = strip_shortcodes( $content_first );
		}

		// remove image
		$image_pos = strpos( $content_first, '<img' );
		if( $image_pos !== false ) {
			$content_first = preg_replace( "~\<img[^>]+\>~", "", $content_first ); 
		}

		// rejoin content pieces
		$content = $content_first . $content_last;
	}

	return $content;
}
add_filter( 'WBC3_api_post_content', 'WBC3_api_remove_embedded_images_from_preview' );

/*

// DISABLED - NO MINIMUM IMAGE UPLOAD SIZE

// enforce minimum image size for all media uploads
function WBC3_minimum_image_upload_size( $file  ) {

	# NOTE: image dimensions must also be defined in /assets/js/featured-images.dev.js

	if ($file['type'] == 'application/pdf') return $file;

    // define minimum image dimensions
    $minimum = array(
    	'width' => '640',
    	//'height' => '478'
    );

    // get the uploaded image
    $img = getimagesize( $file['tmp_name'] );
    $width = $img[0]; 
    $height =  $img[1];

	// check minimum width, return error message if too small
	if( $width < $minimum['width'] ) {
    	return array( "error" => "Image is too small, please adjust. Minimum width is {$minimum['width']} pixels; uploaded image is {$width} pixels." );
    } else {
    	return $file;
    } 
}
add_filter( 'wp_handle_upload_prefilter', 'WBC3_minimum_image_upload_size' );

*/


/*
 * Resize images dynamically using wp built in functions
 * Victor Teixeira
 *
 * @param int $attach_id
 * @param string $img_url
 * @param int $width
 * @param int $height
 * @param bool $crop
 * @return array
 */

if ( !function_exists( 'vt_resize') ) {
	function vt_resize( $attach_id = null, $img_url = null, $width, $height, $crop = false ) {
 
		// this is an attachment, so we have the ID
		if ( $attach_id ) {
 
			$image_src = wp_get_attachment_image_src( $attach_id, 'full' );
			$file_path = get_attached_file( $attach_id );
 
		// this is not an attachment, let's use the image url
		} else if ( $img_url ) {
 
			$file_path = parse_url( $img_url );
			$file_path = $_SERVER['DOCUMENT_ROOT'] . $file_path['path'];
 
			// Look for Multisite Path
			if(file_exists($file_path) === false){
				global $blog_id;
				$file_path = parse_url( $img_url );
				if (preg_match("/files/", $file_path['path'])) {
					$path = explode('/',$file_path['path']);
					foreach($path as $k=>$v){
						if($v == 'files'){
							$path[$k-1] = 'wp-content/blogs.dir/'.$blog_id;
						}
					}
					$path = implode('/',$path);
				}
				$file_path = $_SERVER['DOCUMENT_ROOT'].$path;
			}
			//$file_path = ltrim( $file_path['path'], '/' );
			//$file_path = rtrim( ABSPATH, '/' ).$file_path['path'];
 
			$orig_size = getimagesize( $file_path );
 
			$image_src[0] = $img_url;
			$image_src[1] = $orig_size[0];
			$image_src[2] = $orig_size[1];
		}
 
		$file_info = pathinfo( $file_path );
 
		// check if file exists
		$base_file = $file_info['dirname'].'/'.$file_info['filename'].'.'.$file_info['extension'];
		if ( !file_exists($base_file) )
		 return;
 
		$extension = '.'. $file_info['extension'];
 
		// the image path without the extension
		$no_ext_path = $file_info['dirname'].'/'.$file_info['filename'];
 
		$cropped_img_path = $no_ext_path.'-'.$width.'x'.$height.$extension;
 
		// checking if the file size is larger than the target size
		// if it is smaller or the same size, stop right here and return
		if ( $image_src[1] > $width ) {
 
			// the file is larger, check if the resized version already exists (for $crop = true but will also work for $crop = false if the sizes match)
			if ( file_exists( $cropped_img_path ) ) {
 
				$cropped_img_url = str_replace( basename( $image_src[0] ), basename( $cropped_img_path ), $image_src[0] );
 
				$vt_image = array (
					'url' => $cropped_img_url,
					'width' => $width,
					'height' => $height
				);
 
				return $vt_image;
			}
 
			// $crop = false or no height set
			if ( $crop == false OR !$height ) {
 
				// calculate the size proportionaly
				$proportional_size = wp_constrain_dimensions( $image_src[1], $image_src[2], $width, $height );
				$resized_img_path = $no_ext_path.'-'.$proportional_size[0].'x'.$proportional_size[1].$extension;
 
				// checking if the file already exists
				if ( file_exists( $resized_img_path ) ) {
 
					$resized_img_url = str_replace( basename( $image_src[0] ), basename( $resized_img_path ), $image_src[0] );
 
					$vt_image = array (
						'url' => $resized_img_url,
						'width' => $proportional_size[0],
						'height' => $proportional_size[1]
					);
 
					return $vt_image;
				}
			}
 
			// check if image width is smaller than set width
			$img_size = getimagesize( $file_path );
			if ( $img_size[0] <= $width ) $width = $img_size[0];
 
			// Check if GD Library installed
			if (!function_exists ('imagecreatetruecolor')) {
			    echo 'GD Library Error: imagecreatetruecolor does not exist - please contact your webhost and ask them to install the GD library';
			    return;
			}
 
			// no cache files - let's finally resize it
			$new_img_path = image_resize( $file_path, $width, $height, $crop );			
			$new_img_size = getimagesize( $new_img_path );
			$new_img = str_replace( basename( $image_src[0] ), basename( $new_img_path ), $image_src[0] );
 
			// resized output
			$vt_image = array (
				'url' => $new_img,
				'width' => $new_img_size[0],
				'height' => $new_img_size[1]
			);
 
			return $vt_image;
		}
 
		// default output - without resizing
		$vt_image = array (
			'url' => $image_src[0],
			'width' => $width,
			'height' => $height
		);
 
		return $vt_image;
	}
}

?>