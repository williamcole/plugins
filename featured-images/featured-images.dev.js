/*
 * Featured Images
 * 
 */

jQuery(document).ready(function($) {

	// define restrictions for image size and aspect ratio
	var settings = {
	    'post' : {
	        'min_w' : 640,	// minimum width
	    },
	    'featured' : {
	        'min_w' : 970,	// minimum width
	        'min_h' : 546,	// minimum height
	        'ar_w' 	: 16,	// aspect ratio width
	        'ar_h'  : 9 	// aspect ratio height
	    },
	    'square' : {
	        'min_w' : 640,	// minimum width
	        'min_h' : 640,	// minimum height
	        'ar_w' 	: 1,	// aspect ratio width
	        'ar_h'  : 1 	// aspect ratio height
	    }
	};

	// define image type vars
	var cnt = 0,
		target,
		image_type,
		image_type_qv = 'image-type',
		max_height,
		max_width,
		html,
		clicked = false,
		defaultSendToEditor = window.send_to_editor,
		imgSendToEditor = function(html) {
      	tinymce.activeEditor.execCommand( 'mceInsertContent', false, html );
   		window.send_to_editor = defaultSendToEditor;
    };

    /* Edit Post Page */

	// init
	remove_custom_fields();
	
	// set click event for Featured image
	$('#set-post-thumbnail').bind('click', function() {
		image_type = 'featured';
		do_image_validation(image_type);
	});

	// set click event for Square featured image
	$('#set-post-square-image-thumbnail').bind('click', function() {
		image_type = 'square';
		do_image_validation(image_type);
	});

	// set click event for Add Media button (regular and full-screen modes)
	$('.insert-media, #wp_fs_wp-media-library').bind('click', function() {
		image_type = 'post';
		do_image_validation(image_type);
	});

	// set click event for image validation
	$('body').unbind('click').bind('click', function() {

		//console.log('CLICK');

		image_type = get_image_type();

		// show or hide image description based on media screen
		if( get_media_screen() == 'Edit Gallery') {
			$('label[data-setting="description"]').show();
    	} else {
    		$('label[data-setting="description"]').hide();
    	}
    	
		// no validation if Media window is not loaded, or if we're on Edit Gallery/Image Details screens
		if( !is_media_window_visible() || get_media_screen() == 'Edit Gallery' || get_media_screen() == 'Image Details' ) {
			image_type = '';
			return;
		}

		if( is_media_uploader_visible() ) {
			
			// set clicked var to false so we can refresh attachments after upload
			clicked = false;
			remove_image_buttons();
		
		} else {

			// set clicked var to true to prevent attachments refresh on click
			clicked = true;

			if( is_media_library_visible() ) {

				// hide error messages
				$('.media-uploader-status.errors').hide();
							
			} else {

				if( get_media_screen() == 'Insert from URL' ) {
					
					// check if image already loaded
					if( $('.embed-media-settings').is(':visible') ) {
						validate_embedded_image();				
					} else {
						$('.embed-url input').on('keyup', function() {
							remove_image_buttons();
							setTimeout( function() {
								validate_embedded_image();
							}, 500);
						});
					}				
				}
			}
		}
	});

	// detect changes in media library
	waitForKeyElements( ".attachments", attachmentCallbackFunction );

    // detect changes in media sidebar
	waitForKeyElements( ".attachment-details", sidebarCallbackFunction );

	// callback function to run when media library changes
    function attachmentCallbackFunction() {
		
		hide_gallery_options();

		setTimeout( function() {
    		validate_image_size(image_type);
    	}, 2000 );
    }

    // callback function to run when media upload screen changes
    function sidebarCallbackFunction() {
		
		validate_image_size(image_type);
		
		if( clicked ) {
			clicked = false;
			return;
		}

		// refresh attachments browser to display images in correct order after upload
		setTimeout( function() {
			if( $('.attachment-filters').is(':visible') ) {
				//console.log('refresh attachments');
				var orig_value = ( $('.attachment-filters').val() ) ? $('.attachment-filters').val() : 'all';
				var new_value = ( orig_value == 'uploaded' ) ? 'image' : 'uploaded';
				
				// trigger select change
				$('.attachment-filters').val(new_value).trigger('change');
				
				// restore
				$('.attachment-filters').val(orig_value).trigger('change');
			}
		}, 2000 );
    }

    // helper function to more accurately get image type based off media screen instead of var
    function get_image_type() {
    	if( image_type == '' || image_type == 'undefined' || image_type == undefined ) {
			if( get_media_screen() == 'Set Square Image' ) {
				image_type = 'square';
			} else if( get_media_screen() == 'Media Library' ) {
				image_type = 'featured';
			} else if( get_media_screen() == 'Insert Media' ) {
				image_type = 'post';
			}
		}
		return image_type;
    }

   	// main validation function that runs for each selected image
	function do_image_validation(image_type) {
		setTimeout( function() {
			validate_image_size(image_type);
		}, 1000 );
	}

	// validate image size (and aspect ratio for featured/square images)
	function validate_image_size(image_type) {

		// make sure we get correct refreshed image type
		image_type = get_image_type();

		//console.log('validate_image_size > ' + image_type);
		
		if( !image_type ) {
			return;
		}

		// set media button object
		if( image_type == 'post' ) {
			$media_btn = $('.media-button-insert');
		} else {
			$media_btn = $('.media-button-select');
		}

		// no validation on Edit Gallery or Image Details screens
		if( get_media_screen() == 'Edit Gallery' || get_media_screen() == 'Image Details' ) {
			$media_btn.show();
			return;
		}		

		// dont validate pdfs
		if( $('.attachment.selected .attachment-preview').hasClass('subtype-pdf') ) {
			$media_btn.show();
			return;
		}

		// clear error messages and hide Insert buttons by default
		remove_image_buttons();

		// prevent multiple images from being selected
		if( image_type == 'post' ) {

			var num_images = get_selected_images();
			
			if( num_images == 0 ) {
				remove_image_buttons();
				return;
			}

			if( num_images > 1 ) {
				$media_btn.hide();
				$media_btn.before('<div class="error img-error" style="margin:10px 0;"><p>Please select only 1 image</p></div>');
				return;
			}
		}
		
		// capitalize first letter to use in title
		var image_type_title = image_type.charAt(0).toUpperCase() + image_type.slice(1);

		// define object for Edit Attachment button
		$edit_img_link = $('.attachment-details:visible').find('.edit-attachment');

		if( $edit_img_link.length ) {
			// append query string to Edit Image link so we know this is a featured image
			var edit_link = $edit_img_link.attr('href');
			if( edit_link ) {
				edit_link = update_edit_img_link( edit_link, image_type );
				
				if( image_type == 'square' ) {
					// edit square image in new window
					$edit_img_link.attr('href', edit_link);
					target = '_blank';
				} else {
					// featured image is edited inline
					target = '_self';
				}
			}
		}

		// get image dimensions
		var dimensions = $('.attachment-details:visible').find('.dimensions').html();
		if( dimensions ) {

			// extract width and height values
			var dimension_vals = dimensions.split(" ");
			var width = dimension_vals[0];
			var height = dimension_vals[2];

			// make sure width and height are set before displaying button
			if( width && height ) {

				if( image_type == 'post' ) {

					/* post images */			
					if( width < settings[image_type]['min_w'] ) {
					
						// image is too small, disable submit button and display error message
						$media_btn.hide();
						$media_btn.before('<div class="error img-error" style="margin:10px 0;"><p>Please select a larger image. Minimum width is ' + settings[image_type]['min_w'] + ' px.</p></div>');
					
					} else {
					
						// image is ok, enable submit button
						$media_btn.show();
					
					}

				} else {

					/* featured and square images */				
					if( ( width < settings[image_type]['min_w'] ) || ( height < settings[image_type]['min_h'] ) ) {
	
						// image is too small, disable submit button and display error message
						$media_btn.hide();
						$media_btn.before('<div class="error img-error" style="margin:10px 0;"><p>Please select a larger image. Minimum dimensions are ' + settings[image_type]['min_w'] + ' x ' + settings[image_type]['min_h'] + '.</p></div>');
	
					} else {
						
						if( is_correct_aspect_ratio( image_type, width, height ) ) {
	
							// image is ok, enable submit button
							$media_btn.show();
	
						} else {
	
							// image is large enough but not 16:9 aspect ratio, disable submit button and display new Edit Image button
							$media_btn.hide();
							
							if( edit_link ) {

								// display link to edit image
								$media_btn.before('<div class="img-error" style="margin:10px 0;"><p><a class="edit-attachment-button button media-button button-primary button-large" href="' + edit_link + '" target="' + target + '">Edit ' + image_type_title + ' Image</a></p></div>');
							
							} else {

								// user does not have permission to edit image, display generic error message
								$media_btn.before('<div class="error img-error" style="margin:10px 0;"><p>Image has incorrect aspect ratio.</p></div>');
							
							}
						}
					}
				}					
			}
		}

		// customize Edit Image link
		if( image_type == 'featured' || image_type == 'square' ) {

			// failsafe click event for Edit Image link
			$edit_img_link.on('click', function() {
				display_refresh_button();			
			});

			// hide Edit Image text link, force user to use our custom button
			$edit_img_link.hide();

			// click event for Edit Featured/Square Image button
			$('.edit-attachment-button').on('click', function(e) {
				if( image_type == 'square' ) {
					display_refresh_button();
				} else {
					e.preventDefault();
					$edit_img_link.trigger('click');
				}
			});

			$('.edit-attachment, .edit-attachment-button').on('click', function(e) {
				if( image_type !== 'square' ) {
					customize_edit_image_screen(image_type);
				}
			});
		}
	}

	function customize_edit_image_screen(image_type) {
		
		// customize Edit Image screen options for Featured and Square Images
		if( image_type == 'featured' || image_type == 'square' ) {
			
			// half second timeout
			setTimeout( function() {

				// hide Scale Image and Thumbnail Settings
				$('.imgedit-group:first').hide();
				$('.imgedit-applyto').hide();

				// duplicate crop button to use when disabling
				$('.imgedit-crop').before('<div class="imgedit-crop-disabled"></div>');

				// get max image dimensions
				max_width = $("input:visible[id*='imgedit-x-']").val();
				max_height = $("input:visible[id*='imgedit-y-']").val();
				
				// get aspect ratio input fields
				$new_aspect_w_input = $("input:visible[id*='imgedit-crop-width']");
				$new_aspect_h_input = $("input:visible[id*='imgedit-crop-height']");

				// set default values for aspect ratio
				$new_aspect_w_input.val( settings[image_type]['ar_w'].toString() );
				$new_aspect_h_input.val( settings[image_type]['ar_h'].toString() );

				// prevent aspect ratio edit
				$new_aspect_w_input.attr('disabled','disabled');
				$new_aspect_h_input.attr('disabled','disabled');

				// force aspect ratio on edit outline
				$('.imgedit-crop-wrap').on('mouseup', function() {
					set_aspect_ratio(image_type);
					display_image_size_helper_text(image_type);
				});

			}, 1000);
		}
	}

	// validate image embedded from URL
	function validate_embedded_image() {

		var embedded_img = $('.embed-media-settings').find('img');
		var actual_img_width;
		
		if( embedded_img ) {

			// make copy of image in memory to avoid css issues
			$('<img/>').attr( 'src', embedded_img.attr('src') ).load( function() {
			        
		    	// get actual width of image
		        actual_img_width = this.width;

				if( actual_img_width < settings['post']['min_w'] ) {
					$('.media-button-select').hide();
					if( !$('.img-error').is(':visible') ) {
						$('.media-button-select').before('<div class="error img-error" style="margin:10px 0;"><p>Please select a larger image. Minimum width is ' + settings['post']['min_w'] + ' px.</p></div>');
					}
				} else {
					$('.img-error').remove();
					$('.media-button-select').show();
				}
		    });		
		}
	}

	// get the title of the media screen
	function get_media_screen() {
		return $('.media-modal:visible').find('.media-frame-title h1').html();
	}

	// check whether media library is showing or now
	function is_media_window_visible() {
		return ( $('.media-modal').is(':visible') ) ? true : false;
	}

	// check whether media library is showing or now
	function is_media_library_visible() {
		return ( $('.media-modal').find('.attachments').is(':visible') ) ? true : false;
	}

	// check whether media uploader is showing or now
	function is_media_uploader_visible() {
		return ( $('.media-modal').find('.uploader-inline').is(':visible') ) ? true : false;
	}

	// remove any visible custom buttons
	function remove_image_buttons() {
		$('.media-button-insert').hide();
		$('.media-button-select').hide();
		$('.img-error').remove();
	}

	// hide Add To Gallery button and Gallery Display Settings
	function hide_gallery_options() {
		$('.media-modal').find("a:contains('Add to Gallery')").hide();
		$('.media-sidebar').find('.gallery-settings').hide();
	}

	// check if multiple images are selected
	function get_selected_images() {
		return $('.media-frame-content:visible').find('.attachment.selected').length;
	}

	// remove some custom fields
	function remove_custom_fields() {
		
		// hide template type in custom field options
		$('#postcustomstuff').find('input[value="template_type"]').parent('td').parent('tr').hide();

		// get number of visible custom field table rows
		custom_field_row_count = 0;
		$('#the-list > tr').each(function(){
			if( $(this).is(':visible') ) {
				custom_field_row_count++;
			}
		});

		// hide header row if there are no visible table rows
		if( custom_field_row_count == 0 ) {
			$('#postcustomstuff').find('#list-table').hide();
		}
	}

	// display large Refresh Image button for better UX
	function display_refresh_button() {

		// remove existing buttons
		remove_image_buttons();

		// add Refresh button
		$('.media-button-select').before('<div class="img-error" style="margin:10px 0;"><p><a class="refresh-attachment-button button media-button button-primary button-large" href="#"">Refresh Image</a></p></div>');

		// set click event for large Refesh Image button to trigger WP link function
		$('.refresh-attachment-button').on('click', function(e) {
			$('.refresh-attachment').trigger('click');
			setTimeout( function() {
				validate_image_size(image_type);
			}, 2000);			
		});
	}

	// add query arg to edit image link
	function update_edit_img_link( edit_link, image_type ) {

		// check if query arg already added
		if( edit_link.indexOf(image_type_qv) < 0 ) {
			edit_link = edit_link + '&' + image_type_qv + '=' + image_type;
		}

		return edit_link;
	}

	// crop image at proper aspect ratio
	function set_aspect_ratio(image_type) {

		// get selected crop position
		$crop_div = $('.imgedit-crop-wrap').find('div:first');
		
		// top position based on thumbnail size
		var thumb_top_pos = parseInt( $crop_div.css('top') );
		var thumb_height = parseInt( $("input:visible[id*='image-preview-']").css('height') );
		var top_pos_percent = thumb_top_pos / thumb_height;
		
		// calculate top position based on actual image size
		var top_pos = parseInt( top_pos_percent * max_height );
		
		// get selected crop size
		var current_w = $("input:visible[id*='imgedit-sel-width']").val();
		var current_h = $("input:visible[id*='imgedit-sel-height']").val();

		// reset selection height based on aspect ratio
		if( current_w && current_h ) {
			
			// calculate new height based on aspect ratio
			var calc_h = parseInt( current_w * settings[image_type]['ar_h'] / settings[image_type]['ar_w'] );
			
			// set new dimensions
			new_w = current_w;
			new_h = calc_h;

			// if calculated height and position too big, use current height to calculate new width
			if( calc_h > max_height ) {
				new_w = parseInt( current_h * settings[image_type]['ar_w'] / settings[image_type]['ar_h'] );
				new_h = current_h;
			}

			// set new image dimensions
			$("input:visible[id*='imgedit-sel-width']").val( new_w ).keyup();
			$("input:visible[id*='imgedit-sel-height']").val( new_h ).keyup();
		}
	}

	// compare current image size with minimum settings
	function display_image_size_helper_text(image_type) {
		
		// reset helper text display
		$('.imgedit-crop-helper').remove();
		
		// get the current selection size
		var current_size = get_current_image_size();
		
		// if selected area is below minimum size requirements
		if( current_size && ( ( current_size['width'] < parseInt( settings[image_type]['min_w'] ) ) || ( current_size['height'] < parseInt( settings[image_type]['min_h'] ) ) ) ) {
			
			// display the error message
			$('.imgedit-applyto').before('<div class="imgedit-crop-helper error">Image is too small. Please make sure the image size is at least ' + settings[image_type]['min_w'] + ' x ' + settings[image_type]['min_h'] + '.</div>');

			// image size too small, disable crop button
			$('.imgedit-crop-disabled').show();
			$('.imgedit-crop').hide();
		
		} else {

			if( ( image_type == 'square' ) && ( current_size['width'] !== current_size['height'] ) ) {

				// display the error message
				$('.imgedit-applyto').before('<div class="imgedit-crop-helper error">Image is not square. Please adjust your crop.</div>');

				// image size too small, disable crop button
				$('.imgedit-crop-disabled').show();
				$('.imgedit-crop').hide();	
			
			} else {
		
				// image size ok, hide disabled button and show crop button
				$('.imgedit-crop-disabled').hide();
				$('.imgedit-crop').show();
			}
		}		
	}

	// get current image dimensions array for comparison
	function get_current_image_size() {
		
		current_size = new Array();
		current_w = $("input:visible[id*='imgedit-sel-width']").val();
		current_h = $("input:visible[id*='imgedit-sel-height']").val();
		
		if( current_w && current_h ) {
			current_size['width'] = current_w;
			current_size['height'] = current_h;
			return current_size;
		}
	}

	// validate whether image size is correct aspect ratio within a certain margin of error
	function is_correct_aspect_ratio(image_type, width, height) {
		
		if( image_type && width && height ) {

			// determine aspect ratio based on image type
			if( image_type == 'featured' ) {
			var img_aspect_ratio = (width/height).toFixed(2);
				var aspect_ratio = (16/9).toFixed(2);
				var margin = 0.01;
			} else if( image_type == 'square' ) {
				var img_aspect_ratio = (width/height);
				var aspect_ratio = 1/1;
				var margin = 0;
			} else {
				return;
			}

			// compare image aspect ratio
			if(
				( img_aspect_ratio >= ( parseFloat( aspect_ratio - margin ) ) ) && 
				( img_aspect_ratio <= ( parseFloat( aspect_ratio + margin ) ) )
			) {
				return true;
			} else {
				return false;
			}
		}
	}

	/*
	 * Edit Media Page
	 * Square Image still uses old WP method of editing images on separate screen
	 */ 

	// check url query vars to see if we are on image editor page
	url_vars = get_url_vars();

	if( url_vars[image_type_qv] && ( ( url_vars[image_type_qv] == 'featured' ) || ( url_vars[image_type_qv] == 'square' ) ) ) {

		// default image_type to square since featured image is now edited inline
		if( url_vars[image_type_qv] == '' || url_vars[image_type_qv] == 'undefined' || url_vars[image_type_qv] == undefined ) {
			image_type = 'square';
		} else {
			image_type = url_vars[image_type_qv];
		}

		// get post id
		var post_id = url_vars['post'];

		// get max image dimensions
		var max_width = $( '#imgedit-x-' + post_id ).val();
		var max_height = $( '#imgedit-y-' + post_id ).val();

		// get aspect ratio input fields
		$aspect_w_input = $( '#imgedit-crop-width-' + post_id );
		$aspect_h_input = $( '#imgedit-crop-height-' + post_id );

		// set default values
		$aspect_w_input.val( settings[image_type]['ar_w'].toString() );
		$aspect_h_input.val( settings[image_type]['ar_h'].toString() );

		// prevent user from changing aspect ratio
		$aspect_w_input.on('change', function() {
			$(this).val( settings[image_type]['ar_w'].toString() );
		});

		$aspect_h_input.on('change', function() {
			$(this).val( settings[image_type]['ar_h'].toString() );
		});

		// force aspect ratio on edit outline
		$('body').on('mouseup', function() {
			set_aspect_ratio('square');
			display_image_size_helper_text('square');
		});

		// duplicate crop button to use when disabling
		$('.imgedit-crop').before('<div class="imgedit-crop-disabled"></div>');

		// hide Scale Image option and Thumbnail Settings
		$('.imgedit-group:first, .imgedit-applyto').hide();

		// disable editing of image crop inputs
		$('.imgedit-group').find('input').attr('readonly','readonly');
	}

	// get url query variables and return them as an associative array
	function get_url_vars() {
	    var vars = [], hash;
	    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
	    for( var i = 0; i < hashes.length; i++ ) {
	        hash = hashes[i].split('=');
	        vars.push(hash[0]);
	        vars[hash[0]] = hash[1];
	    }
	    return vars;
	}

	// utility function that detects and handles ajax content changes
	function waitForKeyElements (
	    selectorTxt,    /* Required: The jQuery selector string that
	                        specifies the desired element(s).
	                    */
	    actionFunction, /* Required: The code to run when elements are
	                        found. It is passed a jNode to the matched
	                        element.
	                    */
	    bWaitOnce,      /* Optional: If false, will continue to scan for
	                        new elements even after the first match is
	                        found.
	                    */
	    iframeSelector  /* Optional: If set, identifies the iframe to
	                        search.
	                    */
	) {
	    var targetNodes, btargetsFound;
	 
	    if (typeof iframeSelector == "undefined")
	        targetNodes     = $(selectorTxt);
	    else
	        targetNodes     = $(iframeSelector).contents ()
	                                           .find (selectorTxt);
	 
	    if (targetNodes  &&  targetNodes.length > 0) {
	        btargetsFound   = true;
	        /*--- Found target node(s).  Go through each and act if they
	            are new.
	        */
	        targetNodes.each ( function () {
	            var jThis        = $(this);
	            var alreadyFound = jThis.data ('alreadyFound')  ||  false;
	 
	            if (!alreadyFound) {
	                //--- Call the payload function.
	                var cancelFound     = actionFunction (jThis);
	                if (cancelFound)
	                    btargetsFound   = false;
	                else
	                    jThis.data ('alreadyFound', true);
	            }
	        } );
	    }
	    else {
	        btargetsFound   = false;
	    }
	 
	    //--- Get the timer-control variable for this selector.
	    var controlObj      = waitForKeyElements.controlObj  ||  {};
	    var controlKey      = selectorTxt.replace (/[^\w]/g, "_");
	    var timeControl     = controlObj [controlKey];
	 
	    //--- Now set or clear the timer as appropriate.
	    if (btargetsFound  &&  bWaitOnce  &&  timeControl) {
	        //--- The only condition where we need to clear the timer.
	        clearInterval (timeControl);
	        delete controlObj [controlKey]
	    }
	    else {
	        //--- Set a timer, if needed.
	        if ( ! timeControl) {
	            timeControl = setInterval ( function () {
	                    waitForKeyElements (    selectorTxt,
	                                            actionFunction,
	                                            bWaitOnce,
	                                            iframeSelector
	                                        );
	                },
	                300
	            );
	            controlObj [controlKey] = timeControl;
	        }
	    }
	    waitForKeyElements.controlObj   = controlObj;
	}

});
