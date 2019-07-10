/*
 * Sig File
 *
 */

jQuery(document).ready(function($) {

	var sigfile_max_chars = 200;

	setTimeout( function() {
        
        // reduce height of sigfile editor field
    	$('#sigfile_tbl').css('height','auto');
        $('#sigfile_ifr').css('height','70px');
    
    	// update character count
    	$sigfile_content = $('#sigfile_ifr').contents().find('body');

        sigfile_update_character_count( $sigfile_content.html() );

        $sigfile_content.bind('keyup', function() {
            sigfile_update_character_count( $(this).html() );
        });

    }, 1000 );

    // calculate number of characters in text body (html tags ignored)
    function sigfile_update_character_count( txt ) {

        if( !txt )
            return;
        
        // trim whitespace and strip html tags
        txt = $.trim( txt.replace( /(<([^>]+)>)/ig, '' ) );

        // get the character count
        count = txt.length;
        
        // checkout count against max characters
        if( count > sigfile_max_chars ) {
            
            // disable publish button
            $('#submit').attr('disabled', 'disabled');
            
            // add class to highlight red
            $('#characters-remaining').addClass('over');
            $('#sigfile_ifr').addClass('over');

            // calculate diff
            diff = parseInt( count - sigfile_max_chars );
            note = ' Over';
        
        } else {
            
            // reenable publish button
            $('#submit').removeAttr('disabled');
            
            // remove class
            $('#characters-remaining').removeClass('over');
            $('#sigfile_ifr').removeClass('over');
            
            // calculate diff
            diff = parseInt( sigfile_max_chars - count );
            note = ' Remaining';
        
        }

        // update count
        $('#characters-remaining').html( diff + note );
    }

});