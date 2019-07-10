/*
 * Guest Contributor Image
 *
 */
jQuery(document).ready(function($) {
    
    // define some vars
    var image_field,
        rect_width = 130,
        rect_height = 40,
        max_chars = 140,
        storeSendToEditor = window.send_to_editor,
        newSendToEditor   = '';

    newSendToEditor = function(html) {
        
        // extract image src from 'html' string
        image_url = $(html).filter('img').attr('src');
        
        // extract the image id from the class
        image_class = $(html).filter('img').attr('class'); 
        image_class_parts = image_class.split(" ");
        image_class_parts = image_class_parts[image_class_parts.length-1];
        image_class_parts = image_class.split("-");
        image_id = image_class_parts[image_class_parts.length-1];
        
        // insert image url and id into input fields
        $('#WBC3_guest_contributor_image').val( image_url );
        $('#WBC3_guest_contributor_image_id').val( image_id );

        // update image preview
        $('#WBC3_guest_contributor_image_preview').attr( 'src', image_url );

        tb_remove();

        window.send_to_editor = storeSendToEditor;
    };
    
    // load WP Media Upload dialog
    $('#WBC3_guest_contributor_upload_image').on('click', function(e) {
        e.preventDefault();
        window.send_to_editor = newSendToEditor;
        tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
        $('#TB_iframeContent').addClass('guest-contrib');
    });

    // clear image fields   
    $('#WBC3_guest_contributor_remove_image').bind('click', function() {
        $('#WBC3_guest_contributor_image').val('');
        $('#WBC3_guest_contributor_image_id').val('');
        $('#WBC3_guest_contributor_image_preview').attr( 'src', '' );
    });

    // bio field
    setTimeout( function() {
        
        // define vars for bio
        $bio_field = $('#WBC3_guest_contributor_bio_ifr').contents().find('body.WBC3_guest_contributor_bio');
        $bio_content = $('#WBC3_guest_contributor_bio_ifr').contents().find('body');
        
        // style textarea
        $bio_field.css('background','white');
        
        $('#WBC3_guest_contributor_bio_tbl').css('height','auto');
        $('#WBC3_guest_contributor_bio_ifr').css('height','70px');

        // update character count
        update_character_count( $bio_content.html() );

        $bio_content.bind('keyup', function() {
            update_character_count( $(this).html() );
        });
        
        // prevent Zemanta and Related Galleries modules from inserting into editor field
        $('#zemanta-wordpress, #WBC3_daylife_galleries_box').bind('hover', function() {
            $bio_field.focusout();
            //$('#content_ifr').contents().find('#tinymce').focus();
        });

    }, 1000 );

    // detect changes in Media Upload
    wait_for_key_elements( "table.describe", gc_image_filters, false, "#TB_iframeContent.guest-contrib" );
    wait_for_key_elements( "#media-upload-header", gc_image_filters, false, "#TB_iframeContent.guest-contrib" );
    
    // callback function to run when image selected for Guest Contrib
    function gc_image_filters() {        
        
        // hide Company Videos tab
        $('#TB_iframeContent').contents().find('#tab-type_url').hide();
        $('#TB_iframeContent').contents().find('#tab-brightcove_api').hide();

        // hide file type filter and search bar
        $('#TB_iframeContent').contents().find('#media-search').hide();
        $('#TB_iframeContent').contents().find('#filter ul.subsubsub').hide();

        // get current tab
        gc_current_tab = $('#TB_iframeContent').contents().find('#sidemenu li a.current').text();
        
        setTimeout( function() {

            if( gc_current_tab == 'From URL' ) {

                /************/
                /* From URL */
                /************/  

                /*
                
                // only show rows with image url and submit button
                $('#TB_iframeContent').contents().find('table.describe tr').each( function() {
                    if( !$(this).find('input#src').length && !$(this).find('input#go_button').length ) {
                        $(this).hide();
                    }
                });

                // style the submit button
                $('#TB_iframeContent').contents().find('#go_button').addClass('button-primary').val('Set Guest Contributor Image').css('color','white');

                // only allow images to be inserted
                $('#TB_iframeContent').contents().find('#media-items .media-types').hide();
                $('#TB_iframeContent').contents().find('#not-image').removeAttr('checked');
                $('#TB_iframeContent').contents().find('#image-only').attr('checked', 'checked');
                
                */

            } else {

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
                    valid_image = false;
                    image_size = $(this).find('p:contains("Dimensions")').children('span').text();
                    image_size_vals = image_size.split("×");
                    width = parseInt( image_size_vals[0] );
                    height = parseInt( image_size_vals[1] );
                    
                    // check for valid image size (square aspect ratio OR 130 x 40)
                    if( ( width == height ) || ( ( width == rect_width ) && ( height = rect_height ) ) ) {
                        valid_image = true;
                    }

                    // hide all image rows except for Submit
                    $(this).find('tbody tr').each( function() {
                        if( !$(this).hasClass('submit') ) {
                            $(this).hide();
                        } else {
                            
                            // add some styling
                            $(this).find('td.savesend').css('width', '100%');
                            $(this).find('input.button').css('margin-right', '10px')

                            // change button value
                            if( valid_image ) {
                                $(this).find('input.button').addClass('button-primary').val('Set Guest Contributor Image');
                            } else {
                                // disable button if invalid image size
                                $(this).find('input.button').attr('disabled', 'disabled').val('Invalid Image Size');
                            }
                        }
                    });
                });
            }

        }, 250 );
    }

    // calculate number of characters in text body (html tags ignored)
    function update_character_count( txt ) {

        if( !txt )
            return;
        
        // trim whitespace and strip html tags
        txt = $.trim( txt.replace( /(<([^>]+)>)/ig, '' ) );

        // get the character count
        count = txt.length;
        
        // checkout count against max characters
        if( count > max_chars ) {
            
            // disable publish button
            $('#publishing-action').find('input[type="submit"]').attr('disabled', 'disabled');
            
            // add class to highlight red
            $('#characters-remaining').addClass('over');
            $('#WBC3_guest_contributor_bio_ifr').addClass('over');

            // calculate diff
            diff = parseInt( count - max_chars );
            note = ' Over';
        
        } else {
            
            // reenable publish button
            $('#publishing-action').find('input[type="submit"]').removeAttr('disabled');
            
            // remove class
            $('#characters-remaining').removeClass('over');
            $('#WBC3_guest_contributor_bio_ifr').removeClass('over');
            
            // calculate diff
            diff = parseInt( max_chars - count );
            note = ' Remaining';
        
        }

        // update count
        $('#characters-remaining').html( diff + note );
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