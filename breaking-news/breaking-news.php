<?php
/**
 * Plugin Name: Breaking News
 * Description: Breaking News widget to be displayed at the top of the Home Belt widget area
 * Author: William Cole
 */

// register widget
function WBC3_breaking_news_register_widget_init() {
	register_widget( 'WBC3_breaking_news' );
}
add_action('widgets_init', 'WBC3_breaking_news_register_widget_init');

// breaking news widget
class WBC3_breaking_news extends WP_Widget {

	function __construct() {
		parent::__construct('WBC3_breaking_news', $name = 'Breaking News');
	}
	
	function update($new_instance, $old_instance) {				
		$instance = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['headline'] = sanitize_text_field( $new_instance['headline'] );
		$instance['link'] = sanitize_text_field( $new_instance['link'] );
		return $instance;
    }
    
    function form($instance) {
    	$title = !empty( $instance['title'] ) ? esc_attr( $instance['title'] ) : esc_attr( 'Breaking' );
    	$headline = !empty( $instance['headline'] ) ? esc_attr( $instance['headline'] ) : '';
    	$link = !empty( $instance['link'] ) ? esc_url( $instance['link'] ) : '';
    	?>
        <p>
        	<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Slug:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
        <p>
       		<label>Headline:</label>
       		<input type="text" class="widefat" name="<?php echo $this->get_field_name('headline'); ?>" value="<?php echo $headline; ?>"/>
       	</p>
       	<p>
       		<label>Link:</label>
       		<input type="text" class="widefat" name="<?php echo $this->get_field_name('link'); ?>" value="<?php echo $link; ?>"/>
       	</p>
       	<?php 
    }
	
	function widget($args, $instance) {		
		extract( $args );		
		
		$title = apply_filters('widget_title', $instance['title']);
		$headline = esc_attr( $instance['headline'] );
    	$link = esc_url( $instance['link'] );
    	
    	if( $title && $headline ) {
    	
	    	// begin output
	    	echo '<div id="breaking-news">';
			echo '<span class="breaking">'.$title.'</span>';
			if( $link ) {
				echo '<a href="'.$link.'">';
			}
			echo $headline;
			if( $link ) {
				echo '</a>';
			}
			echo '</div><!-- #breaking-news -->';
			// end output
		
		}	
	}
}

?>