/*
 * Newsletter Anchors TinyMCE Plugin
 *
 */

jQuery(document).ready(function($) {

    tinymce.create('tinymce.plugins.newsletter_anchors_plugin', {
        init : function( ed, url ) {
            
            // register Insert Newsletter Anchor button and click event
            ed.addButton('newsletter_anchors_insert_button', {
                title : 'Insert Newsletter Anchor',
                image: './../../assets/images/newsletter_anchors_insert_button.jpg',
                onclick: function() {
                
                    var na_selected_content = tinyMCE.activeEditor.selection.getContent(),
                        na_new_content;

                    // do nothing if no text is highlighted
                    if( na_selected_content.length  == 0 ) {
                        alert('Please highlight some text to insert a Table of Contents anchor');
                        return;
                    }

                    // wrap highlighted text in a div
                    na_new_content = '<span class="newsletter-anchor">' + na_selected_content + '</span>';

                    // insert shortcode into editor
                    tinyMCE.activeEditor.execCommand( 'mceInsertContent', 0, na_new_content );
                    tinyMCE.activeEditor.focus();
                }
            });

            // register Delete Newsletter Anchor button and click event
            ed.addButton('newsletter_anchors_delete_button', {
                title : 'Delete Newsletter Anchor(s)',
                image: './../../assets/images/newsletter_anchors_delete_button.jpg',
                onclick: function() {
                    
                    var na_selected_content = tinyMCE.activeEditor.selection.getContent(),
                        na_all_content, na_new_content;

                    if( na_selected_content ) {
                        
                        // delete Newsletter Anchors from selected content
                        na_new_content = WBC3_newsletter_anchors_delete( na_selected_content );
                        tinyMCE.activeEditor.execCommand( 'mceReplaceContent', 0, na_new_content );
                    
                    } else {
                        
                        // delete Newsletter Anchors from all content
                        na_all_content = tinyMCE.activeEditor.getContent();
                        na_new_content = WBC3_newsletter_anchors_delete( na_all_content );
                        tinyMCE.activeEditor.execCommand( 'mceSetContent', 0, na_new_content );
                    
                    }                    
                }
            });

            // This adds a listener that does the shortcode -> markup before content is loaded
            ed.onBeforeSetContent.add( function( ed, o ) {
                o.content = WBC3_newsletter_anchors_markup( o.content );
            } );
            
            // This adds a listener that does the shortcode -> unmarkup
            ed.onPostProcess.add( function( ed, o ) {
                if( o.get ) {
                    o.content = WBC3_newsletter_anchors_unmarkup( o.content );   
                }                
            } );
        
        }
    });

    // register TinyMCE plugin
    tinymce.PluginManager.add( 'newsletter_anchors', tinymce.plugins.newsletter_anchors_plugin );

    // define regex vars
    var na_shortcode_regex = new RegExp( /\[(?:newsletter_anchor\])(?:(.+?)\[\/newsletter_anchor\])?/gi );
    var na_markup_regex = new RegExp( /<span class="newsletter-anchor">(.+?)<\/span>/gi );
    
    // helper functions
    function WBC3_newsletter_anchors_markup( content ) {
        return content.replace( na_shortcode_regex, function( na_shortcode, na_text ) {
            // replace shortcode with markup
            return '<span class="newsletter-anchor">' + na_text + '</span>';
        } );      
    }

    function WBC3_newsletter_anchors_unmarkup( content ) {
        return content.replace( na_markup_regex, function( na_markup, na_text ) {
            // replace markup with shortcode
            return '[newsletter_anchor]' + na_text + '[/newsletter_anchor]';
        } );
    }

    function WBC3_newsletter_anchors_delete( content ) {
        return content.replace( na_shortcode_regex, function( na_shortcode, na_text ) {
            // replace shortcode with markup
            return na_text;
        } );
    }

});