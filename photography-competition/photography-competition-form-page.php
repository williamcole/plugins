<?php
/**
 * Template Name: Photography Competition: Form
 */


get_header(); ?>

	<script type="text/javascript" charset="utf-8">
		// capitalize string
		// e.g. "hey You!" => "Hey you!"
		String.prototype.capitalize = function() {
			return this.charAt(0).toUpperCase() + this.substring(1).toLowerCase();
		}
		
		// humanize string
		// e.g. "password_confirmation" => "Password confirmation"
		String.prototype.humanize = function() {
			return this.replace(/_/g, ' ').capitalize();
		}
		
		jQuery(document).ready(function() {
			jQuery('div.ucs_form').each(function() {
				var div = jQuery(this);
				var url = 'https://ucs.company.net/campaigns/';
				var id = jQuery(this).attr('id');
				jQuery.getJSON(url+id+'/form.json?callback=?', function(data) {
				
					var create_entry_url = data.form.entry_route_fn("create_entry", { "format" : "json" });
					var form = '<form method="'+create_entry_url["method"]+'" action="'+create_entry_url["url"]+'" enctype="multipart/form-data"></form>';
					div.append(form);
					form = div.find('form');
					if(div.attr('category_id') || data.form.categories.length == 1) {
						var category_id = div.attr('category_id') || data.form.categories[0].id;
						form.append('<input type="hidden" name="entry[category_id]" value="'+category_id+'"/>');
					} else if(data.form.categories.length > 0) {
						form.append('<p><label for="entry_category_id">Category</label><br/><select name="entry[category_id]" id="entry_category_id">'+data.form.categories.map(function(category) { return '<option value="'+category.id+'">'+category.name+'</option>'; }).join('')+'</select></p>');
					}
					
					jQuery.each(data.form.entry_fields, function(i, field) {
						if(field.type == 'text') {
							form.append('<p><label for="entry_'+field.name+'">'+field.display_name+'</label><br/><input type="text" name="entry['+field.name+']" id="entry_'+field.name+'" /></p>');
						} else if(field.type == 'textarea') {
							form.append('<p><label for="entry_'+field.name+'">'+field.display_name+'</label><br/><textarea name="entry['+field.name+']" id="entry_'+field.name+'"></textarea></p>');
						}
					});
					
					for(var i = 0; i < data.form.number_of_images; i++) {
						form.append('<p><label for="entry_entry_images_attributes_'+i+'_raw_image">File '+(i+1)+'</label><br/><input type="file" name="entry[entry_images_attributes]['+i+'][raw_image]" id="entry_entry_images_attributes_'+i+'_raw_image" /></p>');
					}
					form.append('<p><input type="submit" /></p>');
					form.submit(function() {
					
						// custom form validation
						if( validate_form() ) {
						
							return true;
							
							// submit						
							var form = jQuery(this);
							
							// Change submit button
							var submit_text = form.find(':submit').val();
							form.find(':submit').val('Uploading...').attr('disabled', true);
							
							// Insert iframe
							var id = new Date().getTime();
							var iframe = '<iframe name="'+id+'" id="'+id+'" style="display:none;"></iframe>';
							jQuery(document.body).append(iframe);
							iframe = jQuery('#'+id);
							form.attr('target', id);
							
							iframe.load(function(){
								var data = iframe.contents().find('body').html();
								try { data = jQuery.parseJSON(data); } catch(error) { data = {}; }
								if(data.errors) {
									errors = '';
									for (var key in data.errors) {
										if (data.errors.hasOwnProperty(key)) {
											errors = errors + data.errors[key] + '\n';
										}
									}
									alert(errors);
								} else if(data.entry) {
									var body = '<p><b>Thank you for participating!</b></p>';
									var omit = ['_type', 'campaign_id', 'category_id', 'created_at', 'entry_images', 'id', 'images', 'last_edit_by_id', 'status', 'updated_at'];
									for(var key in data.entry) if(jQuery.inArray(key, omit) == -1) {
										body += '<p><b>'+key.humanize()+':</b><br/>'+data.entry[key]+'</p>';
									}
									jQuery.each(data.entry.images, function() {
										body += '<p><img src="'+this.thumb+'" /></p>';
									});
									div.html(body);
								}
								setTimeout(function() { iframe.remove(); }, 200);
								
								// Restore submit button
								form.find(':submit').val(submit_text||'Submit').attr('disabled', false);
							});
							
						} else {
							// do not submit
							return false;
						}
					});
					
					// CUSTOMIZE FRONT END DISPLAY
					
					// submit to secure url
					var action = jQuery('.ucs_form form').attr('action');
					action = action.replace("http:", "https:");
					jQuery('.ucs_form form').attr('action',action);
					
					// define error and success urls
					jQuery('.ucs_form form').prepend('<input type="hidden" name="error_url" value="http://lightbox.company.com/nextgen/entry-form/">');
					jQuery('.ucs_form form').prepend('<input type="hidden" name="success_url" value="http://lightbox.company.com/nextgen/thank-you/">');
					
					// define text vars
					var step_1_text = '<h1>Step 1: Complete Registration</h1><p class="step-text">Please complete all fields.</p>';
					var step_2_text = '<h1>Step 2: Choose Photos</h1><p class="step-text">Reminder: you should submit 10-20 images. Each image should be submitted with a caption and date, be 1500 x 1000 pixel JPGs at 72 dpi, and be no larger than 1 MB. <a id="see-instructions" href="Javascript://Instructions">See saving instructions.</a></p><div id="saving-instructions"><ol><li>Open your image in Photoshop. In the menu bar, click Image > Image Size...</li><li>Uncheck the "Resample Image" box and change the Resolution to 72 pixels/inch.</li><li>Re-check the "Resample Image" box and change the width to 1500 pixels.<br>Your image should now have a width of 1500 pixels at a Resolution of 72 pixels/inch. Select "OK."</li><li>In the menu bar, click File > Save for Web & Devices.<br>Change the settings to JPEG, High and the Quality to 60. Click "Save."<br>Designate a file name and destination folder and click "Save."</li></ol></div>';
					var select_files = '<div id="image_upload_requirement" class="error-text">Please select between 10-20 images.</div><p id="select-files"><a class="select-files" href="Javascript://Select Files">Select Files</a><a class="clear-selection" href="Javascript://Clear Selection">Clear Selection</a></p>';
					var step_3_text = '<h1>Step 3: Upload and Submit</h1><p class="step-text">Please ensure all fields are complete, and all photos are selected.</p>';
					
					// output text
					jQuery('.ucs_form').prepend(step_1_text);
					jQuery('.ucs_form textarea').parent('p').after(step_2_text);
					jQuery('.ucs_form input[type="submit"]').addClass('submit').attr('value','Submit').parent('p').before(step_3_text);
					
					// add classes depending on input type
					jQuery('.ucs_form input[type="text"]').parent('p').addClass('text');
					jQuery('.ucs_form input[type="file"]').parent('p').addClass('file');
					jQuery('.ucs_form input[type="submit"]').parent('p').addClass('submit');
					
					// adjust label text
					jQuery(".ucs_form label:contains('School City State')").html('School City, State');
					jQuery(".ucs_form label:contains('Name of School Contact')").html('Name of School Contact <span>(instructor, principal, dean)</span>');
					jQuery(".ucs_form label:contains('Overview of Your Work')").html('Overview of Your Work <span>(200-400 words)</span>');
					
					// add error message text
					jQuery('.ucs_form #entry_first_name').before('<div class="error-text">Please enter a first name.</div>');
					jQuery('.ucs_form #entry_last_name').before('<div class="error-text">Please enter a last name.</div>');
					jQuery('.ucs_form #entry_email').before('<div class="error-text">Please enter a valid email address.</div>');
					jQuery('.ucs_form #entry_phone_number').before('<div class="error-text">Please enter a phone number.</div>');
					jQuery('.ucs_form #entry_name_of_school').before('<div class="error-text">Please enter a school name.</div>');
					jQuery('.ucs_form #entry_school_city_state').before('<div class="error-text">Please enter a school city and state.</div>');
					jQuery('.ucs_form #entry_name_of_school_contact').before('<div class="error-text">Please enter a school contact.</div>');
					jQuery('.ucs_form #entry_school_contact_telephone').before('<div class="error-text">Please enter a contact phone number.</div>');					
					jQuery('.ucs_form #entry_overview_of_your_work').before('<div class="error-text">Please enter a statement.</div>');
					jQuery('.ucs_form input[type="file"]').each(function(){ jQuery(this).before('<div class="error-text">This file is too big.</div>'); });
					jQuery('.ucs_form input[type="submit"]').before('<div class="error-text">Please correct all errors above.</div>');
					
					// wrap image upload fields in a div
					jQuery('.ucs_form p.file').wrapAll('<div id="image-upload-fields">');
					jQuery('#image-upload-fields').after(select_files);
					
					// select files button
					jQuery('p#select-files a.select-files').toggle(function(){
						jQuery(this).html('Hide Files');
						var image_count = get_number_of_image_uploads();
						if( image_count > 0 ) {
							var count = 0;
							jQuery('.ucs_form p.file').each(function(){
								// show existing image fields
								if( jQuery(this).children('input').val() !== '' ) {
									jQuery(this).slideDown('slow');
								} else {
									count++;
								}
								// display new image field
								if( count==1 ) jQuery(this).slideDown('slow');
							});
						} else {
							jQuery('.ucs_form p.file:first').slideDown('slow');	
						}
						jQuery('a.clear-selection').show();
					},function(){
						jQuery(this).html('Select Files');
						jQuery('.ucs_form p.file').slideUp('slow');
						jQuery('a.clear-selection').hide();					
					});
					
					// see instructions button
					jQuery('.ucs_form p a#see-instructions').toggle(function(){
						jQuery(this).html('Hide saving instructions');
						jQuery('#saving-instructions').slideDown();
					},function(){
						jQuery(this).html('See saving instructions');
						jQuery('#saving-instructions').slideUp();
					});
					
					// add link to remove image
					var remove_image_link = '<a class="remove-image" href="Javascript://Remove">Remove</a>';
					jQuery('.ucs_form input[type="file"]').each(function(){
						jQuery(this).after(remove_image_link);
					});
					
					// remove image button
					jQuery('.ucs_form p.file a.remove-image').click(function(){
						jQuery(this).hide();
						if( jQuery.browser.msie ) {
							// IE specific method for clearing file input
							html = jQuery(this).parent('p').html();
							jQuery(this).parent('p').html(html);
						} else {
							// clear file input
							jQuery(this).parent('p').children('input').val('');
						}
					});
					
					// clear selection button
					jQuery('p#select-files a.clear-selection').click(function(){
						jQuery('.ucs_form p.file a.remove-image').hide();
						jQuery('.ucs_form p.file').hide();
						jQuery('.ucs_form p.file:first').show();
						jQuery('.ucs_form p.file input').each(function() {
							if( jQuery.browser.msie ) {
								// IE specific method for clearing all file inputs
								html = jQuery(this).parent('p').html();
								jQuery(this).parent('p').html(html);
							} else {
								// clear all file inputs
								jQuery(this).val('');
							}	
						});
					});
										
					// image upload fields
					jQuery('.ucs_form p.file input').each(function(){
						field = jQuery(this);
						field.change(function() {
							image_field = jQuery(this);
							image = image_field.val();
							if( image !== '' ) {
								
								// show next image field
								image_field.parent('p').next().slideDown('slow');
								
								// get image file size (not supported in IE)
								if( !jQuery.browser.msie ) {
									var file_size = image_field[0]; // size returned in bytes
									file_size = bytes_to_kilobytes(file_size.files[0].fileSize);
								} else {
									file_size = 0;
								}
									
								// check file size
								if( file_size > 1048576 ) {
									// too big
									image_field.val('');
									image_field.parent('p').addClass('error');
									image_field.parent('p').children('.error-text').html('This file is too big. Images must be no larger than 1 MB.');
								/*
								} else if( file_size < 0 ) {
									// too small
									image_field.val('');
									image_field.parent('p').addClass('error');
									image_field.parent('p').children('.error-text').html('This file is too small.');
								*/
								} else {
									// just right
									image_field.parent('p').children('a.remove-image').show();
									image_field.parent('p').removeClass('error');
								}																
								
							}
						});
					});
					
					function isNumeric(val) {
						var ValidChars = "0123456789.-()";
						for (i=0; i<val.length; i++) if (ValidChars.indexOf(val.charAt(i)) == -1) return false;
						return true;
					}
					
					function isValidEmail(val) {
						var iLen = val.length;
						if 	((iLen < 6) || (val.indexOf('@') < 1) || ((val.charAt(iLen - 3) != '.') && (val.charAt(iLen - 4) != '.') && (val.charAt(iLen - 5) != '.')) ) return false;
						return true;
					}
					
					function bytes_to_kilobytes(bytes, precision) {
						var kilobyte = 1024;
						return (bytes / kilobyte).toFixed(precision);
					}					
					
					// count how many images have been uploaded
					function get_number_of_image_uploads() {
						var count = 0;
						jQuery('.ucs_form p.file input').each(function(){
							image = jQuery(this).val();
							if( image !== '' ) count++;
						});
						return count;
					}
					
					// verify user filled out all text fields
					function validate_text_fields() {
						
						var has_errors = false;
						
						// check that all text fields are filled out
						jQuery('.ucs_form form input[type="text"]').each(function(){
							field = jQuery(this);
							if( field.val() !== '' ) {
								field.parent('p').removeClass('error');
							} else {
								field.parent('p').addClass('error');
								has_errors = true;
							}
						});
						
						// check for valid email address
						var email = jQuery('.ucs_form #entry_email').val();
						if( !isValidEmail(email) ) {
							jQuery('.ucs_form #entry_email').parent('p').addClass('error');
							has_errors = true;
						}
						
						// check for valid phone numbers
						var phone = jQuery('.ucs_form #entry_phone_number').val();
						if( !isNumeric(phone) ) {
							jQuery('.ucs_form #entry_phone_number').parent('p').addClass('error');
							has_errors = true;
						}
						var contact_phone = jQuery('.ucs_form #entry_school_contact_telephone').val();
						if( !isNumeric(contact_phone) ) {
							jQuery('.ucs_form #entry_school_contact_telephone').parent('p').addClass('error');
							has_errors = true;
						}
						
						// check for errors
						if(has_errors) {
							return false;
						} else {
							return true;
						}

					}
					
					// validate textarea field
					function validate_textarea_field() {
						
						var has_errors = false;
						var overview = jQuery('.ucs_form #entry_overview_of_your_work');
						var overview_words = overview.val().split(/\b[\s,\.-:;]*/).length;
						var overview_error_message = '';
						
						// determine error message
						if( overview_words <= 1 ) {
							overview_error_message = 'Please enter a statement.';
						} else if( overview_words < 200 ) {
							overview_error_message = 'Statement is under the word limit.';
						} else if( overview_words > 400 ) {
							overview_error_message = 'Statement is over the word limit.';
						}
						
						// display error message
						if( (overview_words < 200) || (overview_words > 400) ) {
							overview.parent('p').children('.error-text').html(overview_error_message);
							overview.parent('p').addClass('error');
							has_errors = true;
						} else {
							overview.parent('p').removeClass('error');
						}
						
						// get word count
						word_count = jQuery('.ucs_form #entry_overview_of_your_work').val().split(/\b[\s,\.-:;]*/).length;
						error_message = jQuery('.ucs_form #entry_overview_of_your_work').parent('p').children('.error-text').html();
						if( word_count == 1 ) {
							error_message_language = 'word';
						} else {
							error_message_language = 'words';
						}
						if( jQuery('.ucs_form #entry_overview_of_your_work').val() !== '' ) {
							error_message = error_message+' ('+word_count+' '+error_message_language+')';
							jQuery('.ucs_form #entry_overview_of_your_work').parent('p').children('.error-text').html(error_message);
						}
						
						// check for errors
						if(has_errors) {
							return false;
						} else {
							return true;
						}

					}
					
					// dynamically display number of words entered in overview textarea
					jQuery('.ucs_form #entry_overview_of_your_work').change(function() {
						validate_textarea_field();	
					});
					
					// verify user input 10-20 of images
					function validate_image_uploads() {
						count = get_number_of_image_uploads();
						if( (count >= 10) && (count <= 20) ) {
							jQuery('#image_upload_requirement.error-text').hide();
							return true;
						} else {
							jQuery('#image_upload_requirement.error-text').show();
							if( !jQuery('.ucs_form p.file:first').is(":visible") ) {
								jQuery('p#select-files a.select-files').click();
							}
							return false;
						}
					}
					
					// custom form validation
					function validate_form() {
						validate_text_fields();
						validate_textarea_field();
						validate_image_uploads();
						
						if ( validate_text_fields() && validate_textarea_field() && validate_image_uploads() ) {
							jQuery('.ucs_form p input.submit').parent('p').removeClass('error');
							jQuery('.ucs_form p input.submit').val('Uploading...').attr('disabled', true);
							return true;
						} else {
							jQuery('.ucs_form p input.submit').parent('p').addClass('error');
							jQuery('.ucs_form p input.submit').val('Submit').attr('disabled', false);
							jQuery('.ucs_form p.error:first input').focus();
							return false;
						}
					}
					
				});
			});
		});
	</script>
  
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
							
							<?php if( WBC3_photo_contest_is_active() ) { ?>
								<h3>Entry Form</h3>
								<div class="ucs_form" id="4e84a709ca9f7018d7000003"></div>
							<?php } else { ?>
								<?php if ( WBC3_photo_contest_is_over() ) { ?>
									<h3>The competition is now closed. Winners will be announced on <a href="<?php bloginfo('url'); ?>">LightBox</a> on October 26, 2011.</h3>
								<?php } else { ?>
									<h3>Check back here on October 3 to submit your entries.</h3>
								<?php } ?>
							<?php } ?>
							
						</div><!-- .entry-content -->
					</div><!-- #post-## -->
				
				</div><!-- #contest -->

<?php endwhile; ?>

			</div><!-- #content -->
		</div><!-- #container -->
	</div><!-- #columns -->
<?php get_footer(); ?>
