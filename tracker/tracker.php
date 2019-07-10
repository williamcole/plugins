<?php
/**
 * Plugin Name: COMPANY Tracker
 * Description: Appends an omniture tracking code to internal URLs in various modules
 * Author: William Cole
 */
 
// time tracking tag
function WBC3_tracking_tag($url, $module) {
	
	global $WBC3_blog;
	
	if( isset($url) && isset($module) && isset($WBC3_blog['omniture']['tag']) ) {
	
		// if this is an internal URL, add the tracking code
		if( preg_match('/company.com/', $url) ) {
				
			// remove IID and XID variable from url
			$url = remove_query_arg( 'iid', $url );
			$url = remove_query_arg( 'xid', $url );
			
			// determine section based on template
			if ( (is_home()) || (is_front_page()) ) {
				$section = 'main';     // home page
			} elseif ( is_category() ) {
				$section = 'category'; // category
			} elseif ( is_single() ) {
				$section = 'article';  // article
			} elseif ( is_author() ) {
				$section = 'author';   // contributor
			} elseif ( is_search() ) {
				$section = 'search';   // search results
			} elseif ( is_page() ) {
				$section = 'page';	   // page
			} else {
				$section = 'x';        // unknown
			}
			
			// create tracking tag
			$tag = $WBC3_blog['omniture']['tag'].'-'.$section.'-'.$module;
			
			// append url with new tracking tag
			$url = add_query_arg( 'iid', $tag, $url );
	
		}
		
	}

	return $url;

}

// adds time tracking code to lede article permalinks
function WBC3_tracking_lede_permalink($url) {

	if( WBC3_is_lede() ){
		$url = WBC3_tracking_tag($url, 'lede');
	}
	return $url;
}
add_filter('the_permalink', 'WBC3_tracking_lede_permalink');

?>