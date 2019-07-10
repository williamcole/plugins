<?php

/*
Plugin Name: Newsletter Anchors
Description: Add TinyMCE button to insert anchor shortcodes for Newsletters
Version: 0.1
Author: William Cole
*/

function WBC3_newsletter_anchors_tinymce_add_buttons() {
    
    // only enable these buttons on newsletter blogs
    if( 'newsletter' == WBC3_get_blog_type() ) {
        add_filter( 'mce_external_plugins', 'WBC3_newsletter_anchors_tinymce_add_plugin' );
        add_filter( 'mce_buttons', 'WBC3_newsletter_anchors_tinymce_register_buttons' );
    }
}
add_action( 'init', 'WBC3_newsletter_anchors_tinymce_add_buttons' ); 

function WBC3_newsletter_anchors_tinymce_add_plugin( $plugins ) {
    $plugins[ 'newsletter_anchors' ] = apply_filters( 'cdn_wrapper', '/assets/js/newsletter-anchors-tinymce-plugin.js' );
    return $plugins;
}

function WBC3_newsletter_anchors_tinymce_register_buttons( $buttons ) {
    array_push( $buttons, 'newsletter_anchors_insert_button' );
    array_push( $buttons, 'newsletter_anchors_delete_button' );
    return $buttons;
}
