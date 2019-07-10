<?php

/*
Plugin Name: Company Tweet Quotes
Description: Add TinyMCE button to insert tweet quote shortcode
Version: 0.1
Author: William Cole
*/

function WBC3_tweet_quote_tinymce_add_buttons() {
    add_filter( 'mce_external_plugins', 'WBC3_tweet_quote_tinymce_add_plugin' );
    add_filter( 'mce_buttons', 'WBC3_tweet_quote_tinymce_register_buttons' );
}
add_action( 'init', 'WBC3_tweet_quote_tinymce_add_buttons' ); 

function WBC3_tweet_quote_tinymce_add_plugin( $plugins ) {
    $plugins[ 'tweet_quote' ] = apply_filters( 'cdn_wrapper', '/assets/js/tweet-quote-tinymce-plugin.js' );
    return $plugins;
}

function WBC3_tweet_quote_tinymce_register_buttons( $buttons ) {
    array_push( $buttons, 'tweet_quote_insert_button' );
    array_push( $buttons, 'tweet_quote_delete_button' );
    return $buttons;
}

function WBC3_tweet_quote_print_styles() {
    if( function_exists( 'WBC3_add_static_css_asset' ) ) {
        WBC3_add_static_css_asset( 'tweet-quote', false, '0.5' );
    }
}
add_action( 'admin_print_styles-post.php', 'WBC3_tweet_quote_print_styles' );
add_action( 'admin_print_styles-post-new.php', 'WBC3_tweet_quote_print_styles' );
    
function WBC3_tweet_quote_dialog() {
    ?>
    <script type="text/javascript">

        jQuery(document).ready(function($) {

            var tq_char_limit = 140;
            var leading_whitespace = '';

            // customize some styling
            $('#TB_window').addClass('tweet-quote');
            $('#TB_ajaxContent').css('width','auto');

            // display selected text and populate tweet quote textarea
            var tq_selected_text = tinyMCE.activeEditor.selection.getContent();
            if( tq_selected_text ) {

                // check for leading whitespace to be preserved upon insertion
                if( /^\s/.test( tq_selected_text.substring(0,1) ) ) {
                    leading_whitespace = ' ';
                }

                // trim whitespace and strip html tags
                tq_selected_text = $.trim( tq_selected_text.replace( /(<([^>]+)>)/ig, '' ) );

                // strip entity shortcodes but keep text content inside them
                tq_selected_text = tq_selected_text.replace( /\[entity[^\]]+\]/ig, '' ); 
                tq_selected_text = tq_selected_text.replace( /\[\/entity\]/ig, '' );

                // insert text
                $('#tweet-quote-text').val( tq_selected_text );
            }

            // display character count when dialog first loads
            tq_update_character_count();

            /* CLICK EVENTS */
            
            // update character count when text changes
            $('#tweet-quote-text').on( 'keyup', function() {
                tq_update_character_count();
            });

            // submit button
            $('#tweet-quote-insert').click( function() {

                // check character count
                var tq_text = $('#tweet-quote-text').val();

                // if no text is highlighted, use custom text for tweet quote
                if( !tq_selected_text || ( tq_selected_text == '' ) ) {
                    tq_selected_text = tq_text;
                }

                // replace quotation marks and brackets in selected content
                // NOTE: special chars are double encoded here and will be double-escaped on templates
                tq_text = tq_text.replace( /"/g, "&ampquot;" ); // quote
                tq_text = tq_text.replace( /\[/g, "&amp#91;" ); // opening bracket
                tq_text = tq_text.replace( /\]/g, "&amp#93;" ); // closing bracket
                //tq_text = tq_text.replace( /#/g, "&#35;" );   // hash 
                //tq_text = tq_text.replace( /@/g, "&#64;" );   // at sign
                
                // trim whitespace and strip html tags
                tq_text = $.trim( tq_text.replace( /(<([^>]+)>)/ig, '' ) );
                tq_selected_text = $.trim( tq_selected_text );

                if( tq_get_character_count( tq_text ) <= tq_char_limit ) {

                    // wrap text in span tag
                    tq_content = leading_whitespace + '<span display="' + tq_text + '" class="tweet_quote">' + tq_selected_text + '<span class="tweet_icon"></span></span> ';
                    
                    // insert tweet quote into editor
                    tinyMCE.activeEditor.execCommand( 'mceInsertContent', 0, tq_content );
                    tinyMCE.activeEditor.focus();

                    // close dialog
                    tb_remove();

                } else {
                    return false;
                }
            });

            /* HELPER FUNCTIONS */

            // refresh the character count
            function tq_update_character_count() {

                // get tweet text
                var text = $('#tweet-quote-text').val();

                // take bitly url into account
                var placeholder = ' http://bit.ly/1a2b3c4';
                var char_count = tq_get_character_count( text ) + tq_get_character_count( placeholder );

                // update character count
                if( char_count > tq_char_limit ) {
                    $('#tweet-quote-char-count').addClass('over');
                    var char_count_text = ( char_count - tq_char_limit ) + ' Characters Over Limit';
                    $('#tweet-quote-insert').attr('disabled','disabled');
                } else {
                    $('#tweet-quote-char-count').removeClass('over');
                    var char_count_text = ( tq_char_limit - char_count ) + ' Characters Remaining';
                    if( tq_get_character_count( text ) == 0 ) {
                        $('#tweet-quote-insert').attr('disabled','disabled');
                    } else {
                        $('#tweet-quote-insert').removeAttr('disabled','disabled');
                    }
                }

                // update character count
                $('#tweet-quote-char-count').html( char_count_text );

                // strip html tags and update preview 
                text = $.trim( text.replace( /(<([^>]+)>)/ig, '' ) );
                $('#tweet-quote-preview').html( text + placeholder );                

            }

            // get the character count of a string
            function tq_get_character_count( text ) {
                var char_count = text.length
                return char_count;
            }

        });

    </script>
    <?php

    
    // tweet quote dialog markup    
    echo '<div id="tweet-quote-wrap">
        <div class="label">Customize tweet</div>
        <textarea id="tweet-quote-text" class="tweet-quote-block" name="tweet-quote-text"></textarea>
        
        <div class="label">Preview tweet</div>
        <div id="tweet-quote-preview" class="tweet-quote-block"></div>
        
        <div id="tweet-quote-char-count"></div>
        <button id="tweet-quote-insert">Insert Tweet Quote</button>
    </div>';
    
    // dont return anything
    die();

}
add_action( 'wp_ajax_WBC3_tweet_quote_dialog', 'WBC3_tweet_quote_dialog' );

// wrap single-line tweet quotes in <p> tags
// because Templates uses wpauto filter which doesnt get applied to <span> tags
function WBC3_tweet_quote_parse_api_text( $content ) {

    $content = preg_replace( "~\\r\\n\[tweet_quote~", "<p class=\"tweet_line\">[tweet_quote", $content ); 
    $content = preg_replace( "~\[\/tweet_quote\]\\r\\n~", "[/tweet_quote]</p>", $content ); 

    return $content;
}
add_filter( 'WBC3_api_pre_send_content', 'WBC3_tweet_quote_parse_api_text' );