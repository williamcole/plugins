<?php
/**
  Plugin Name: Time Promo Types
  Plugin URI: http://company.com
  Description: Allows users to add a promo type to a post, with corresponding filters for omniture variables
  Author: William Cole
  Version: 1.0
  Author URI: http://company.com/
 */

/**
  Add custom field for Promo Types
 */

// set array of promo types with their names and respective values
function WBC3_get_promo_types() {
	
	$promo_types = array(
		array(
			'label' => 'No Promotion',
			'slug' => 'default',
			'help' => '',
		),
		array(
			'label' => 'Magazine Teaser',
			'slug' => 'teaser',
			'help' => 'Articles written about this week\'s magazine issue.',
		),
		array(
			'label' => 'Premium Promotion',
			'slug' => 'magpro',
			'help' => 'Articles that contain a link to archive magazine content.',
		),
	
	);
	
	return apply_filters( 'WBC3_get_promo_types', $promo_types );
}

// display Promo Type options, help text, and example links (time2012 theme only)
function WBC3_promo_type_help_text() {
	
	$text = '<br/>';

	$promo_types = WBC3_get_promo_types();
	
	if( $promo_types ) {
		foreach( $promo_types as $promo_type ) {
			
			// output Promo options and corresponding help text
			if( !empty( $promo_type['help'] ) ) {
				$text .= '<strong>'.strtoupper( $promo_type['label'] ).'</strong><br/>';
				$text .= 'DESCRIPTION: '.$promo_type['help'].'<br/>';
				$text .= 'EXAMPLE URL: http://company.com/promotional-link/<strong>?pcd='.$promo_type['slug'].'</strong><br/>';
				$text .= '<br/>';
			}
		}
		
		$text .= 'Please make sure you append any promotional links with the appropriate tracking variable (see above examples).';
	}
	
	return $text;

}

// only display meta box on older themes
// Time2012 theme will display under Post Options
if( !defined( 'WBC3_2012' ) ) {
	
	// add custom meta box
	function WBC3_add_promo_type_meta_box() {
		add_meta_box( 'WBC3_promo_type_meta_box', 'Magazine Promotion', 'WBC3_promo_type_meta_box', 'post', 'side', 'low' );
	}
	add_action( 'admin_menu', 'WBC3_add_promo_type_meta_box' );
	
}

// add a meta box that allows the user to select from the given promo types
function WBC3_promo_type_meta_box( $post ) {

	global $post;
	
	// default
	$selected_promo_type = WBC3_get_promo_type( $post->ID );	
	$selected_promo_type = !empty( $selected_promo_type ) ? $selected_promo_type : 'default';
	
	// nonce the field
	wp_nonce_field( 'WBC3_promo_type', 'WBC3_promo_type_nonce', false );
	
	?>
	
	<script type="text/javascript">
		jQuery(document).ready(function() {
			
			// get default promo type attributes
			var slug = jQuery("#WBC3_promo_type option:selected").val();
			var help = jQuery("#WBC3_promo_type option:selected").attr('help');
			
			// default help text
			update_help_text();
				
			// update help text when options are selected
			jQuery("#WBC3_promo_type").change(function(){
				update_help_text();
			});
			
			function update_help_text() {
				
				// get selected promo type attributes
				var sel_slug = jQuery("#WBC3_promo_type option:selected").val();
				var sel_help = jQuery("#WBC3_promo_type option:selected").attr('help');
				
				// update slug and help text on change
				if( slug !== sel_slug) {
					slug = sel_slug;
					help = sel_help;
				}
				
				// construct help text output
				help_output =  '<p>'+help+'</p>';
				
				if( slug !== 'default') {
					help_output += '<p>NOTE: Be sure to append your promo links with the appropriate tracking variable:</p>';
					help_output += '<p>EX: http://company.com/promo-link/<strong>?pcd='+slug+'</strong></p>';
				}
				
				// update the html
				jQuery("#promo_type_howto").html( help_output );
			
			}
		
		});
	</script>
	
	<select name="WBC3_promo_type" id="WBC3_promo_type">
		<?php foreach( WBC3_get_promo_types() as $promo_type ) : ?>
			<?php $selected = !empty( $selected_promo_type ) ? selected( $selected_promo_type, $promo_type['slug'], false ) : false; ?>
			<option value="<?php echo sanitize_key( $promo_type['slug'] ); ?>" help="<?php echo esc_html( $promo_type['help'] ); ?>" <?php echo $selected; ?>><?php echo esc_html( $promo_type['label'] ); ?></option>
		<?php endforeach; ?>
	</select>
	<p id="promo_type_howto" class="howto"></p>
	<?php
}

// save the promo type that the user selected
function WBC3_save_promo_type( $post_id ) {

	if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
		return;
	
	if( !current_user_can('edit_post', $post_id) )
		return;
		
	if( !isset($_POST['WBC3_promo_type']) )
		return;
	
	if( !isset($_POST['WBC3_promo_type_nonce']) )
		return;
		
	if( !wp_verify_nonce($_POST['WBC3_promo_type_nonce'], 'WBC3_promo_type') )
		return;
		
	$WBC3_promo_type = sanitize_key( $_POST['WBC3_promo_type'] );
	if( !empty( $WBC3_promo_type ) ) {
		update_post_meta( $post_id, 'WBC3_promo_type', $WBC3_promo_type );
	}
		
}
add_action('save_post', 'WBC3_save_promo_type');

// get the promo type for the post
function WBC3_get_promo_type( $post_id = null ) {

	if( !$post_id ) {
		global $post;
		$post_id = $post->ID;
	}
	
	$promo_type = get_post_meta( $post_id, 'WBC3_promo_type', true );
	
	return $promo_type;
}

// filter to adjust omniture values when different Promo Types are set
function WBC3_promo_type_omniture( $omniture ) {

	global $post;
	
	$promo_type = WBC3_get_promo_type( $post->ID );
	
	// set custom omniture variables if promo type is set
	if( is_single() && ( !empty( $promo_type ) ) && ( $promo_type !== 'default' ) ) {
		
		$prop35 = array(
			'date' => get_the_date( 'Y-m-d' ),
			'pcd' => $promo_type,
			'guid' => $post->ID,
			'prop16' => $omniture['prop16'],
			'prop30' => $omniture['prop30'],
		);
		
		$omniture['prop35'] = implode( '|', $prop35 );
		$omniture['prop39'] = WBC3_get_promo_type( $post->ID );
		
		// set duplicates for eVars
		$omniture['eVar35'] = 'D=c35';
		$omniture['eVar39'] = 'D=c39';
		
	}
	
	return $omniture;
}
add_filter( 'WBC3_omniture', 'WBC3_promo_type_omniture' );


/**
 * Adds a shortcode tag to format Paywall links within content and append tracking variable.
 * 
 * Example Use:
 *
 * url = required url to link to
 * title = required text to be hyperlinked
 * 
 * [paywall url="http://company.com" title="Article Title"]
 */
function WBC3_paywall_shortcode( $atts ) {

	global $post;

	$defaults = array(
		'title' => null,
		'url' => null,
	);
	
	extract( shortcode_atts( $defaults, $atts ) );
	
	// check to make sure we have a valid company.com url and a title
	if( isset( $title ) && isset( $url ) && WBC3_is_valid_domain( $url ) ) {
	
		// remove existing query vars from url string
		$url_parts = explode( '?', $url );
		$url = $url_parts[0];		
		
		// check for promo type
		$pcd = get_post_meta( $post->ID, 'WBC3_promo_type', true );
		
		// append promo type tracking code
		if( isset( $pcd ) && ( $pcd !== 'default' ) ) {
			$url = add_query_arg( 'pcd', $pcd, $url );
		}
		
		// output		
		return '<a href="'. esc_url( $url ).'">'. esc_html( $title ).'</a>';
	
	}
		
}
add_shortcode( 'paywall', 'WBC3_paywall_shortcode' );

?>