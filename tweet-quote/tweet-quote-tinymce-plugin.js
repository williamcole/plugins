/*
 * Tweet Quote TinyMCE Plugin
 *
 */

jQuery(document).ready(function($) {

    tinymce.create('tinymce.plugins.tweet_quote_plugin', {
        init : function( ed, url ) {
            
            // register Insert Tweet Quote button and click event
            ed.addButton('tweet_quote_insert_button', {
                title : 'Insert Tweet Quote',
                image: './../../assets/images/tweet_quote_insert_button.png',
                onclick: function() {
                    tb_show( 'Insert Tweet Quote', 'admin-ajax.php?action=WBC3_tweet_quote_dialog' );
                }
            });

            // register Delete Tweet Quote button and click event
            ed.addButton('tweet_quote_delete_button', {
                title : 'Delete Tweet Quote(s)',
                image: './../../assets/images/tweet_quote_delete_button.jpg',
                onclick: function() {
                    
                    var tq_selected_content = tinyMCE.activeEditor.selection.getContent(),
                        tq_all_content, tq_new_content;

                    if( tq_selected_content ) {
                        // delete tweet quotes from selected content
                        tq_new_content = WBC3_tweet_quote_delete( tq_selected_content );
                        tinyMCE.activeEditor.execCommand( 'mceReplaceContent', 0, tq_new_content );
                    } else {
                        // delete tweet quotes from all content
                        tq_all_content = tinyMCE.activeEditor.getContent();
                        tq_new_content = WBC3_tweet_quote_delete( tq_all_content );
                        tinyMCE.activeEditor.execCommand( 'mceSetContent', 0, tq_new_content );
                    }                    
                }
            });

            // This adds a listener that does the shortcode -> markup before content is loaded
            ed.onBeforeSetContent.add( function( ed, o ) {
                o.content = WBC3_tweet_quote_markup( o.content );
            } );
            
            // This adds a listener that does the shortcode -> unmarkup
            ed.onPostProcess.add( function( ed, o ) {
                if ( o.get ) {
                    o.content = WBC3_tweet_quote_unmarkup( o.content );   
                }                
            } );
        }
    });

    // register TinyMCE plugin
    tinymce.PluginManager.add( 'tweet_quote', tinymce.plugins.tweet_quote_plugin );

    // define regex vars
    var tq_shortcode_regex = new RegExp( /\[(?:tweet_quote)\b\sdisplay="(.+?)"(?:.*?)\](?:(.+?)\[\/tweet_quote\])?/gi );
    var tq_markup_regex = new RegExp( /<span\b\sdisplay="(.+?)"(?:[^>]*?)(?:class="tweet_quote">)(.+?)<span class="tweet_icon"><\/span><\/span>/gi );
    var tq_delete_regex = new RegExp( /\[(?:tweet_quote)\b\sdisplay="(?:.+?)"(?:.*?)\](?:(.+?)\[\/tweet_quote\])?/gi );

    // helper functions
    function WBC3_tweet_quote_markup( content ) {
        return content.replace( tq_shortcode_regex, function( tq_shortcode, tq_text, tq_quote ) {
            // replace shortcode with markup
            return '<span display="' + tq_text + '" class="tweet_quote">' + tq_quote + '<span class="tweet_icon"></span></span>';
        } );      
    }

    function WBC3_tweet_quote_unmarkup( content ) {
        return content.replace( tq_markup_regex, function( tq_markup, tq_text, tq_quote ) {
            // replace markup with shortcode
            return '[tweet_quote display="' + tq_text + '"]' + tq_quote + '[/tweet_quote]';
        } );
    }

    function WBC3_tweet_quote_delete( content ) {
        return content.replace( tq_delete_regex, function( tq_shortcode, tq_text ) {
            // replace shortcode with markup
            return tq_text;
        } );
    }

});