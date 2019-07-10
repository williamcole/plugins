/* Image Poll JS */

var COMPANY = window.COMPANY || [];

// detect hostname
var hostname = window.location.hostname;
var devhost = ( hostname.indexOf("devwp.company.com") > -1 );

(function(){

	COMPANY.ImagePoll = {
		
		// set bitly share data vars
		bitly_account : 'clientdotcom',
		bitly_apikey : 'R_d514a81e85b444dfbbc234f661e7c38a',
			
		// set poll base url based depending on if this is Dev or Prod
		pollbaseurl : devhost ? 'http://rc-profiles.company.net/brands/Company/sections/polls/articles/' : 'http://profiles.company.net/brands/Company/sections/polls/articles/',
		
		// set some vars for each slide
		selectedpoll : false,
		currentpoll : 0,
		pollsdiv : '',
		pollname : '',
		pollid : '',
		pollquestion : '',
		polltitle : '',
		pollafter : '',
		prevresults : '',
		totalvotes : 0,
		pollanswers : '',
		selectedanswer : '',
		selectedurl : '',
		selectedimage : '',
		pollopen : true,
		
		init : function(){
		
			// set slide number from query string in url
			var selected_slide = getSlideNumber( 'slide' );		
			if( selected_slide && ( !this.selectedpoll ) ) this.currentpoll = slide;
							
			// display poll
			this.pollname = COMPANY.ImagePoll.poll_array[this.currentpoll];
			var url = this.pollbaseurl + this.pollname + '/polls?add_document_domain=true&callback=render';
			$.getScript(url);
			
		},
		
		render : function(data){
		
			COMPANY.ImagePoll.pollscont = $('#poll-container');
			COMPANY.ImagePoll.pollsdiv = $('#polls');
			COMPANY.ImagePoll.pollid = data[0].questions[0].poll_id;
			COMPANY.ImagePoll.pollquestion = data[0].questions[0].id;
			COMPANY.ImagePoll.polldescription = data[0].description;
			COMPANY.ImagePoll.pollenddate = data[0].stops_at;
			COMPANY.ImagePoll.polltitle = data[0].questions[0].text;
			COMPANY.ImagePoll.totalvotes = data[0].questions[0].total_vote_count;
			COMPANY.ImagePoll.pollanswers	= data[0].questions[0].answers;
			COMPANY.ImagePoll.pollopen = true;	
			
			// check if poll is open or closed
			if( COMPANY.ImagePoll.pollenddate && ( new Date().getTime() > new Date(COMPANY.ImagePoll.pollenddate).getTime() ) ) {
				COMPANY.ImagePoll.pollopen = false;
				$('.pollarea').removeClass('open');
				$('.pollarea').addClass('closed');
			} else {
				$('.pollarea').removeClass('closed');
				$('.pollarea').addClass('open');
			}
			
			// split description text into an array
			descs = COMPANY.ImagePoll.polldescription.split("\n");
			
			var i = 0;
			var h = '<div id="userform">';
				
			// loop through each answer and output
			for(;i<COMPANY.ImagePoll.pollanswers.length;i++){
			
				title = COMPANY.ImagePoll.pollanswers[i].text;
				//subtitle = getPollDate( COMPANY.ImagePoll.pollanswers[i].image_source_url );
				
				// if the title contains a colon separator
				if( title.indexOf(":") > -1 ) {
					
					// split title text into an array
					title_array = title.split(":");
					
					// set new title and subtitle
					title = title_array[0]; // trim
					subtitle = title_array[1]; // trim
					
				}
				
				// voting images
				h	+=	'<div class="vote' + (i+1) +'">'
					+		'<a onclick="COMPANY.ImagePoll.submit('+ COMPANY.ImagePoll.pollanswers[i].id + ',' + COMPANY.ImagePoll.totalvotes + ',' +  COMPANY.ImagePoll.pollanswers.length +')"><span class="vote-hover">Vote</span><span><b><img src="' + COMPANY.ImagePoll.pollanswers[i].image_source_url + '" width="100%" alt="' + COMPANY.ImagePoll.pollanswers[i].text + '" /></b></span>'
					+ 			'<div class="image-text">'
					+				'<h3>' + COMPANY.ImagePoll.get_title( COMPANY.ImagePoll.pollanswers[i].text ) + '</h3>'				
					+				'<div class="subtitle">' + subtitle + '</div>'
					+				'<div class="description">' + descs[i] + '</div>' // trim
					+			'</div>'
					+		'</a>'
					+	'</div>';
			}
			
			if( ! COMPANY.ImagePoll.pollopen ) {				
				// poll closed
				h += 		'<div class="closed-message"><h3>' + COMPANY.ImagePoll.poll_closed_message + '</h3></div>';
			} else {			
				// vs image
				h += 		'<div class="vs">vs.</div>';			
			}
			
			// close div
			h  	+=		'<div class="clear"></div>'
				+	'</div>';
			
					
			COMPANY.ImagePoll.pollsdiv.append(h);
			
			COMPANY.ImagePoll.results();

			// bind some touch actions so we can vote properly in iphone mode
			if ( typeof window.ad_mode !== "undefined" && ( 'tablet' == window.ad_mode || 'mobile' == window.ad_mode ) ) {
				
				// set up the overlay when we touch down
				$( '.vote1, .vote2' ).bind( 'touchstart', function() {

					// if this mimics a tap event, do the stuff we want
					$( this ).on( 'touchend', function( e ) {
						$( this ).find( '.vote-hover' ).css( 'visibility', 'hidden' );
					});

					// if this appears to be a scrolling event, ignore our touchend event
					$(this).on('touchmove', function(e){
						$( this ).off( 'touchend' );
					});

				});
			}
			
		},
		
		submit : function( pollanswer, polltotalvotes, pollanswerlength ){
		
			// dont allow votes if poll is closed
			if( ! COMPANY.ImagePoll.pollopen ) {
				alert( COMPANY.ImagePoll.poll_closed_message );
				return;
			}
		
			// update omniture tracking and url
			COMPANY.ImagePoll.update_tracking();
			
			// refresh ads
			COMPANY.ImagePoll.update_ads();
			
			var results = '<div class="resultarea">';
				
			// add share text
			results += '<div class="share-text">Share this Result</div>'
					+  '<a onclick="COMPANY.ImagePoll.sharetwitter()" id="twitter-button">Twitter</a>'
					+  '<a onclick="COMPANY.ImagePoll.sharefacebook()" id="facebook-button">Facebook</a>'
					+  '<div class="clear"></div>';
			
			// define some vars to be used for results
			var i = 0;
			var pollpercent;
			var pollthumb;
			
			// increment total votes by 1
			polltotalvotes++;
			
			// display previous poll results
			for(;i<COMPANY.ImagePoll.pollanswers.length;i++){
			
				if (pollanswer == this.pollanswers[i].id){
					
					this.pollanswers[i].vote_count++;	// increment selected poll answer by 1
					
					// set selected poll name, answer, url, and image for sharing
					COMPANY.ImagePoll.selectedpollname = this.pollname;
					COMPANY.ImagePoll.selectedanswer = COMPANY.ImagePoll.get_title( COMPANY.ImagePoll.pollanswers[i].text );
					
					// set selected url
					if( COMPANY.ImagePoll.poll_is_time100 ) {
						// Time 100 shared urls should redirect back to the poll landing page
						COMPANY.ImagePoll.selectedurl = COMPANY.ImagePoll.poll_url;
					} else {
						// all other shared urls should append a query string to redirect back to the specific poll matchup
						COMPANY.ImagePoll.selectedurl = COMPANY.ImagePoll.poll_url + '?slide=' + parseInt(COMPANY.ImagePoll.currentpoll+1);
					}
					
					COMPANY.ImagePoll.selectedimage = this.pollanswers[i].image_source_url;
					
				};
				
				// shorten facebook link
				$.getJSON(
					'http://api.bit.ly/v3/shorten?&callback=?&format=json',
					{
						'longUrl': COMPANY.ImagePoll.selectedurl + '?xid=' + COMPANY.ImagePoll.poll_facebook_xid, 
						'login': COMPANY.ImagePoll.bitly_account,  
						'apiKey': COMPANY.ImagePoll.bitly_apikey
					},
					function(data){
						COMPANY.ImagePoll.bitly_fbUrl = data.data.url;
					}
				);
				
				// shorten twitter link
				$.getJSON(
					'http://api.bit.ly/v3/shorten?&callback=?&format=json',
					{
						'longUrl': COMPANY.ImagePoll.selectedurl + '?xid=' + COMPANY.ImagePoll.poll_twitter_xid, 
						'login': COMPANY.ImagePoll.bitly_account,  
						'apiKey': COMPANY.ImagePoll.bitly_apikey
					},
					function(data){
						COMPANY.ImagePoll.bitly_twtUrl = data.data.url;
					}
				);
				
				// calculate vote percentage
				pollpercent = Math.round( ( this.pollanswers[i].vote_count / polltotalvotes ) * 100 );
				
				// set image url
				pollthumb = this.pollanswers[i].image_source_url;
				
				// change filename to get the thumbnail (75x75) path ('XXXX-75.jpg')
				// pollthumb = pollthumb.replace("320","75");
				
				result_title = this.pollanswers[i].text;
				
				// if the title contains a colon separator
				if( result_title.indexOf(":") > -1 ) {
					
					// split title text into an array
					title_array = result_title.split(":");
					
					// set new title and subtitle
					result_title = title_array[0]; // trim
					
				}
				
				// display previous poll results
				results	+=	'<div class="result' + (i+1) +'">'
						+		'<div class="thumb"><img src="' + pollthumb + '" width="100%" alt="' + this.pollanswers[i].text + '" /></div>'
						+		'<div class="text">'
						+			'<div class="percent">' + pollpercent + '%</div>'
						+			'<h6>' + result_title + '</h6>'
						+		'</div>'
						+	'</div>';
			}
			
			results		+=	'<div class="clear"></div>'
						+	'</div>';
			
			results_form = '<form id="submitform" action="'+this.pollbaseurl+this.pollname+'/polls/'+COMPANY.ImagePoll.pollid+'/vote?add_document_domain=true" target="submitiframe" method="post"><fieldset><input name="poll[votes][]" value="' + pollanswer + '" /><input name="question_'+this.pollquestion+'" value="' + pollanswer + '" /></fieldset></form>'
						+	'<iframe id="submitiframe" name="submitiframe" border="1"></iframe>';
					
			// save prev results
			COMPANY.ImagePoll.prevresults = results;
			
			// append the html results to the poll div
			$('#polls').append( results_form );
			
			// submit the form
			$('#submitform').submit();
			$('#submitiframe').load(function(){
				
				var response = $('#submitiframe').children().find("body").html();
				try { response = eval( response ); }
				catch(e) { response = 'Something went wrong! Please, try again!'; }
				
				if (response == null) {
	
					$('#userform').remove();
					
					setTimeout(function() {
						$('#submitform').remove();
						$('#submitiframe').remove();
					}, 200);
					
					++COMPANY.ImagePoll.currentpoll;
					++COMPANY.ImagePoll.selectedpoll;
					
					if (COMPANY.ImagePoll.currentpoll >= COMPANY.ImagePoll.poll_array.length) {
		
						// display end slide after last poll
						var endslide	=	'<div class="endslide">'
										+		'<div class="imgcont">'
										+			'<a href="'+ COMPANY.ImagePoll.poll_url +'"><img src="' + COMPANY.ImagePoll.poll_end_image + '" width="100%" alt="" /></a>'
										+		'</div>'
										+		'<div class="txtcont">'
										+			'<h2><a href="'+ COMPANY.ImagePoll.poll_url +'">' + COMPANY.ImagePoll.poll_end_title + '</a></h2>'
										+			'<h3><a href="'+ COMPANY.ImagePoll.poll_url +'">' + COMPANY.ImagePoll.poll_end_subtitle + '</a></h3>'									
										+			'<p class="deck">' + COMPANY.ImagePoll.poll_end_deck + '</p>' 
										+		'</div><!-- /.txtcont-->'
										+		'<div class="clear"></div>'
										+	'</div><!-- /.endslide -->';
		
						// append the end slide html
						$('#polls').append(endslide);
						
						COMPANY.ImagePoll.results();
						
						// add class to parent element
						$('#poll-container').addClass('end');
						
						// update end url
						// window.history.pushState( {1:25}, COMPANY.ImagePoll.poll_title, '?slide=end' );
					
					} else {
						
						// load the next poll slide
						COMPANY.ImagePoll.init();
						
					}
				}
			});
					
		},
		
		results : function(){
		
			// clear poll-after html
			var pollafter = '';
			$('#poll-after').html( pollafter );
			
			// determine content to display after poll
			if (COMPANY.ImagePoll.currentpoll == 0) {
			
				// display poll deck if we are on the first poll
				pollafter += '<div id="poll-deck">' + COMPANY.ImagePoll.poll_deck + '</div>';
			
			} else {
			
				// otherwise display previous poll results			
				pollafter += '<div id="poll-results">' + COMPANY.ImagePoll.prevresults + '</div>';
			
			}
			
			// add content to poll-after div
			$('#poll-after').html( pollafter );
		
		},
		
		sharefacebook : function(){
			
			// get custom share language and replace title			
			fbText = COMPANY.ImagePoll.poll_facebook_share_language;
			fbText = fbText.replace( '%title%', COMPANY.ImagePoll.selectedanswer );
			
			FB.ui({
				"method": "feed",
				"name": COMPANY.ImagePoll.poll_title,
				//"caption": 'This is the caption',
				"description": fbText,
				"link": COMPANY.ImagePoll.bitly_fbUrl,
				"picture": COMPANY.ImagePoll.selectedimage
			});
			
		},
		
		sharetwitter : function(){
		
			// get custom share language and replace title			
			twtText = COMPANY.ImagePoll.poll_twitter_share_language;
			twtText = twtText.replace( '%title%', COMPANY.ImagePoll.selectedanswer );
			
			// prepend hashtag with space
			if( COMPANY.ImagePoll.poll_twitter_hashtag !== '' ) {
				COMPANY.ImagePoll.poll_twitter_hashtag = ' ' + COMPANY.ImagePoll.poll_twitter_hashtag;
			}
			
			// append COMPANY twitter handle
			COMPANY.ImagePoll.poll_twitter_handle = ' via @' + COMPANY.ImagePoll.poll_twitter_handle;
			
			var twtShareStatus = twtText + ' | ' + COMPANY.ImagePoll.bitly_twtUrl + COMPANY.ImagePoll.poll_twitter_hashtag + COMPANY.ImagePoll.poll_twitter_handle,
				twtShareUrl = 'http://twitter.com/home?status='+escape(twtShareStatus), 
				w = window.open(twtShareUrl,'twtShare','height=300,width=450,scrollbars=1');
			
		},
		
		get_title : function( title ){
		
			// if the title contains a colon separator
			if( title.indexOf(":") > -1 ) {
				
				// split title text into an array
				title_array = title.split(":");
				
				// set new title and subtitle
				title = title_array[0]; // trim
				
			}
			
			return title;
		
		},
		
		update_tracking : function(){
		
			// current poll
			num = parseInt( this.currentpoll + 1 );
			
			// update prop17 on slide change
			s_time.prop17 = this.poll_url + '?slide=' + num;
			
			s_code = s_time.t();
			
			// update url to reflect current slide
			//window.history.pushState( {1:25}, COMPANY.ImagePoll.poll_title, '?slide=' + num );
		
		},
		
		update_ads : function(){
		
			$('.google-ad-iframe').each(function() {
				$(this).attr('src', $(this).attr('src') );
			});
			
			adFactory.refreshAds(['leaderboard-ad', 'sidebar-ad']);
			
		}
	
	};
	
	// helper function to get slide parameter from url
	function getSlideNumber( name ) {
		
		name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
		var regexS = "[\\?&]" + name + "=([^&#]*)";
		var regex = new RegExp(regexS);
		var results = regex.exec(window.location.search);
		
		if( results == null ) {
			
			return false;
			
		} else {
			
			// get the slide number
			slide = decodeURIComponent(results[1].replace(/\+/g, " "));
			
			// make sure its within range
			if( slide > COMPANY.ImagePoll.poll_array.length ) {
				return false;
			} else {
				slide = parseInt( slide );	// make sure slide is an integer
				slide-- // decrease by 1 since poll array starts at 0			
				return slide;
			}
		}
		
	}
	
	// helper function to get date from image url
	function getPollDate( url ) {
	
		date = '';
	
		// check if image exists on img.company.net
		if( url.search( 'http://img.company.net/company/cheesiest-covers/img' ) != -1) {
		
			// remove 3-character file extension (JPG)
			url = url.slice(0, -4);
			
			// get date from locally uploaded image
			date = url.split('-').splice(-1,1);
			
		} else {
			
			// get date from COMPANY archive image url
			date = url.split('/').splice(-2,1);
			
		}
		
		return date;	
	}

})();

// set last
var render = COMPANY.ImagePoll.render;

// define trim function to fix IE8 issue
if(typeof String.prototype.trim !== 'function') {
	String.prototype.trim = function() {
		return this.replace(/^\s+|\s+$/g, '');
	}
}
