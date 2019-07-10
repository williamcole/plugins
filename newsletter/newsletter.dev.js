jQuery(document).ready(function($) {

    // define some vars
    var image_field,
        rect_width = 130,
        rect_height = 40,
        max_chars = 140,
        storeSendToEditor = window.send_to_editor,
        newSendToEditor = '';

    newSendToEditor = function(html) {
        $('<div class="noDelete"><span>' + html + '</span></div>').appendTo('#newsletter_files');

        tb_remove();

        window.send_to_editor = storeSendToEditor;
    };

    if ($('#sample-permalink').length) $('#sample-permalink').html($('#sample-permalink').html().replace('/sites/', '/newsletters/'));
    if ($('#view-post-btn a').length) $('#view-post-btn a').attr('href', $('#view-post-btn a').attr('href').replace('/sites/', '/newsletters/'));
    if ($('#wp-admin-bar-view a').length) $('#wp-admin-bar-view a').attr('href', $('#wp-admin-bar-view a').attr('href').replace('/sites/', '/newsletters/'));
    if ($('#message').text().indexOf('View post') > -1) {
        $('#message a').attr('href', $('#message a').attr('href').replace('/sites/', '/newsletters/'));
    }
    $('#post-preview').attr('href', $('#post-preview').attr('href').replace('/sites/', '/newsletters/'));
    
    // load WP Media Upload dialog
	$('#WBC3_newsletter_upload_pdf').on('click', function(e) {
		e.preventDefault();

		window.send_to_editor = newSendToEditor;
		tb_show('', 'media-upload.php?TB_iframe=true');
		$('#TB_iframeContent').addClass('newsletter');
	});

    // clear image fields
    $('#newsletter_files').on('click', 'input', function(e) {
        e.preventDefault();

        $deleteBtn = $(this);
		var data = {
			action: 'delete_attachment',
			attach_id: $deleteBtn.attr('id').split('_')[1]
		};

		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		$.post(ajaxurl, data, function(response) {
			$deleteBtn.parent().remove();
		});

        $('#WBC3_newsletter_image').val('');
        $('#WBC3_newsletter_image_id').val('');
        $('#WBC3_newsletter_image_preview').attr( 'src', '' );
    });

    wait_for_key_elements( "table.describe", gc_image_filters, false, "#TB_iframeContent.newsletter " );
    wait_for_key_elements( "#media-upload-header", gc_image_filters, false, "#TB_iframeContent.newsletter " );
    
    // callback function to run when image selected for Guest Contrib
    function gc_image_filters() {        
        $iframeContent = $('#TB_iframeContent');

        $iframeContent.contents().find('h3').text('Add PDF files from your computer');

        // hide Videos tab
        $iframeContent.contents().find('#tab-type_url').hide();
        $iframeContent.contents().find('#tab-brightcove_api').hide();

        // hide file type filter and search bar
        $iframeContent.contents().find('#media-search').hide();
        $iframeContent.contents().find('#filter ul.subsubsub').hide();

        // get current tab
        gc_current_tab = $iframeContent.contents().find('#sidemenu li a.current').text();
        
        //console.log(gc_current_tab);
        
        setTimeout( function() {

            if( gc_current_tab != 'From URL' ) {

                /*****************/
                /* Media Library */
                /*****************/

                // hide Save All Changes button to prevent confusion
                $('#TB_iframeContent').contents().find('p.savebutton').hide();
                
                // for each image table
                $('#TB_iframeContent').contents().find('table.describe').each( function() {
                    
                    // hide Edit Image buttons
                    $(this).find('input[value="Edit Image"]').hide();

                    // get image size and validate
                    // hide all image rows except for Submit
                    $(this).find('tbody tr').each( function() {
                        if( !$(this).hasClass('submit') ) {
                            $(this).hide();
                        } else {
                            // add some styling
                            $(this).find('td.savesend').css('width', '100%');
                            $(this).find('input.button').css('margin-right', '10px');

                            $(this).find('input.button').addClass('button-primary').val('Add to Post').click(function (e) {
								var data = {
									action: 'attach_pdf',
									parent_id: $('#nlf_postID').val(),
									attach_id: $(this).closest('.media-item').find('thead').attr('id').split('-')[2]
								};

								// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
								$.post(ajaxurl, data, function(response) { });
                            });
                        }
                    });
                });
            }

        }, 250 );
    }

    // utility function that detects and handles ajax content changes
    function wait_for_key_elements (
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
        var controlObj      = wait_for_key_elements.controlObj  ||  {};
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
                        wait_for_key_elements (    selectorTxt,
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
        wait_for_key_elements.controlObj   = controlObj;
    }
});