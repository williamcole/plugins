<?php
/**
 * Template Name: Newsletter Page 2012
 */
?>

<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body bgcolor="#ffffff" leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" link="#1b4f89" vlink="#1b4f89">
<!-- %%FRIEND_MESSAGE%% -->
<table width="728" border="0" cellpadding="0" cellspacing="0" bgcolor="#ffffff" align="center">
	<tr>
		<td><table width="728" border="0" cellpadding="0" cellspacing="0" bgcolor="#ffffff">
			<tr>
				<td colspan="5"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/newsletter/spacer.gif" width="728" height="12" border="0" /></td>
			</tr>
			<tr>
				<td width="197"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/newsletter/spacer.gif" width="197" height="10" border="0" /></td>
				<td width="288" align="center">
					<a href="<?php WBC3_newsletter_xid( 'http://www.company.com/company/' ); ?>"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/newsletter/WBC3_blk.png" alt="TIME" title="TIME" width="75" height="30" border="0"></a>
					<a href="<?php bloginfo( 'url' ); ?>"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/newsletter/hdr_lightbox.png" alt="LightBox" width="126" height="30" border="0"></a><br />
					<font face="georgia,serif" style="font-size:16px; font-style:italic; font-weight:normal; color:#000000;">From the photo editors of TIME</font>
				</td>
				<td width="180" align="right" valign="top">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/newsletter/spacer.gif" width="180" height="10" border="0" />
					<font face="georgia,serif" style="font-style:italic; font-size:13px; font-weight:normal; color:#000000;"><a href="http://raf.cheetahmail.com/r/raf?t=%%t%%&u=%%RAF_TRACK%%&remail=%%email%%" style="color:#000000; text-decoration:none;"><img src="http://img.company.net/company/www/i/icon_forward_friend.png" border="0" alt="Forward to a Friend Icon" width="12" height="8" title="Forward to a Friend" />&nbsp;&nbsp;Forward to a Friend</a></font>
				</td>
				<td width="15"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/newsletter/spacer.gif" width="15" height="1" border="0" /></td>
			</tr>
		</table></td>
	</tr>
	<tr>
		<td colspan="5"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/newsletter/spacer.gif" width="728" height="15" border="0" /></td>
	</tr>
	<tr>
		<td height="1" align="center"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/newsletter/module_line.png" width="728" height="1" border="0" /></td>
	</tr>
	<tr>
		<td><table width="728" height="20" border="0" cellpadding="0" cellspacing="0" bgcolor="#ffffff">
			<tr>
				<td height="20" colspan="3"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/newsletter/spacer.gif" width="728" height="20" border="0" /></td>
			</tr>
		</table></td>
	</tr>
	<tr>
		<td><table width="728" border="0" cellpadding="0" cellspacing="0" bgcolor="#ffffff">
			<tr>
				<td width="40"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/newsletter/spacer.gif" alt="" width="40" height="20" border="0" /></td>
				<td valign="top" width="430"><table width="430" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td colspan="2"><font face="'arial black',arial" style="font-size:25px; font-weight:normal; color:#b2b2b2; letter-spacing:-1px; line-height:28px;">Latest Photos</font></td>
					</tr>
					<tr>
						<td colspan="2"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/newsletter/spacer.gif" alt="" width="430" height="10" border="0" /></td>
					</tr>
					
				<?php
				
				// define counter variable and recent_posts array
				$count = 0;
				$recent_posts = array();
				
				// get most recent posts
				$items = new WP_Query( array(
					'posts_per_page' => 5
				) );
				
				// loop through items
				while( $items->have_posts() ) : $items->the_post();
					
					// add post id to array so we can exclude in right rail
					$recent_posts[] = $post->ID;
					
					// increment counter
					$count++;
				
					if( $count == 1 ) {
						
						// from
						echo '<!-- From: '.get_bloginfo('name').' - company.com -->';
						
						// subject line
						echo '<!-- Subject: Must-See Photo Essays and Pictures of the Week for '.date('F j, Y').' -->';
						
						// first article					
						?>
						<tr>
							<td colspan="2" width="430">
								<?php $image = get_the_post_thumbnail( $post->ID, array( '430','430' ), array( 'border'=>0, 'title'=>'', 'alt'=>'' ) ); ?>
								<?php if( $image ) { ?>
									<div align="center" style="width:430px; min-height:200px; background:#eee; text-align:center; overflow:visible;">
										<a href="<?php WBC3_newsletter_xid( get_permalink() ); ?>"><?php echo $image; ?></a>
									</div>
								<?php } ?>
								<p><font face="georgia,serif" style="font-size:22px; font-weight:normal; line-height:25px;"><a href="<?php WBC3_newsletter_xid( get_permalink() ); ?>" style="text-decoration:none; color:#000000;"><?php the_title(); ?></a></font></p>
								<p><a href="http://www.facebook.com/plugins/like.php?href=<?php the_permalink(); ?>?ref%3Dnewsletter-photos-weekly&amp;layout=standard&amp;show_faces=false&amp;width=450&amp;action=like&amp;colorscheme=light&amp;height=80" target="_blank"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/newsletter/btn_fb_like.png" alt="Facebook Like" width="45" height="20" border="0" title="Like this Photo" /></a>&nbsp;&nbsp;&nbsp;<a href="https://twitter.com/share?url=<?php the_permalink(); ?>&amp;via=timepictures&amp;text=<?php the_title(); ?> - LightBox" target="_blank"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/newsletter/btn_tweet.png" alt="Tweet" width="55" height="20" border="0" title="Tweet this Photo" /></a></p>
							</td>
						</tr>
						<tr>
							<td colspan="2"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/newsletter/spacer.gif" alt="" width="430" height="15" border="0" /></td>
						</tr>
						<tr>
							<td colspan="2" height="1"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/newsletter/module_line.png" alt="" width="430" height="1" border="0" /></td>
						</tr>
						<tr>
							<td colspan="2"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/newsletter/spacer.gif" alt="" width="430" height="15" border="0" /></td>
						</tr>
						<?php
					
					} else {
					
						// remaining articles
						?>						
						<tr>
							<td valign="top" width="200" style="padding-right:15px; text-align:center;">
								<?php $image = get_the_post_thumbnail( $post->ID, 'latest-large', array( 'border'=>0, 'title'=>'', 'alt'=>'' ) ); ?>
								<?php if( $image ) { ?>
									<div align="center" style="width:200px; height:150px; background:#eee; text-align:center; overflow:visible;">
										<a href="<?php WBC3_newsletter_xid( get_permalink() ); ?>"><?php echo $image ?></a>
									</div>
								<?php } ?>
							</td>
							<td valign="top" width="215">
								<font face="arial,sans-serif" style="font-size:12px; font-weight:bold;"><?php
									$category = get_the_category();
									echo esc_html( $category[0]->name );
								?></font><br />
								<font face="georgia,serif" style="font-size:18px; font-weight:normal;"><a href="<?php WBC3_newsletter_xid( get_permalink() ); ?>" style="text-decoration:none; color:#000000;"><?php the_title(); ?></a></font><br />
								<font face="georgia,serif" style="font-size:12px; line-height:18px;"><?php echo WBC3_newsletter_content(); ?></font>
							</td>
						</tr>
						<tr>
							<td colspan="2"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/newsletter/spacer.gif" alt="" width="430" height="25" border="0" /></td>
						</tr>						
						<?php
						
					}
					
				endwhile;
				
				?>
			
				</table></td>
				<td width="40"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/newsletter/spacer.gif" alt="" width="40" height="1" border="0" /></td>
				<td valign="top" width="175">
								
					<table border="0" cellpadding="0" cellspacing="0" width="175">
						<tr>
							<td colspan="2"><font face="'arial black',arial" style="font-size:18px; font-weight:normal; color:#b2b2b2; letter-spacing:-1px;">Photo Essays</font></td>
						</tr>
						<tr>
							<td><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/newsletter/spacer.gif" alt="" width="175" height="12" border="0" /></td>
						</tr>						
						<?php						
							// get most recent photo essay posts
							$photo_essays = new WP_Query( array(
								'category_name' => 'photo-essay',
								'post__not_in' => $recent_posts,
								'posts_per_page' => 6
							) );
							
							while( $photo_essays->have_posts() ) : $photo_essays->the_post();
								$image = get_the_post_thumbnail( $post->ID, array( '175','175' ), array( 'border'=>0, 'title'=>'', 'alt'=>'' ) );
								if( $image ) {
									?>
									<tr>
										<td width="175" valign="top" style="padding-bottom:5px;">
											<div align="center" style="width:175px; min-height:115px; background:#eee; text-align:center; overflow:visible;">
												<a href="<?php WBC3_newsletter_xid( get_permalink() ); ?>"><?php echo $image; ?></a>
											</div>
										</td>
									</tr>
									<?php
								} ?>
								<tr>
									<td><font face="georgia,serif" style="font-weight:normal; font-size:14px; line-height:17px;"><a href="<?php WBC3_newsletter_xid( get_permalink() ); ?>" style="color:#000000; text-decoration:none;"><?php the_title(); ?></a></font></td>
								</tr>
								<tr>
									<td><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/newsletter/spacer.gif" width="175" height="20" border="0" /></td>
								</tr>
								<?php
							endwhile;
						?>					
					</table>
					<table height="1" border="0" cellpadding="0" cellspacing="0" width="175">
						<tr>
							<td height="1"><img src="http://img.company.net/company/www/i/line_gray_308x1.gif" alt="" width="175" height="1" border="0" /></td>
						</tr>
					</table>
					<table border="0" cellpadding="0" cellspacing="0" width="175">
						<tr>
							<td><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/newsletter/spacer.gif" alt="" width="175" height="8" border="0" /></td>
						</tr>
						<tr>
							<td><font face="georgia,serif" style="font-size:18px; font-weight:normal;"><a href="http://www.company.com/company/photoessays?xid=newsletter-photos-weekly" target="_blank" style="color:#000000; font-weight:bold; font-size:16px; font-family:georgia,serif; text-decoration:none; letter-spacing:-1px;">See More Photos >></a></font></td>
						</tr>
						<tr>
							<td><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/newsletter/spacer.gif" alt="" width="175" height="8" border="0" /></td>
						</tr>
					</table>
					<table height="1" border="0" cellpadding="0" cellspacing="0" width="175">
						<tr>
							<td height="1"><img src="http://img.company.net/company/www/i/line_gray_308x1.gif" alt="" width="175" height="1" border="0" /></td>
						</tr>
					</table>
				
				</td>
				<td width="40"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/newsletter/spacer.gif" alt="" width="40" height="20" border="0" /></td>
			</tr>
			<tr>
				<td colspan="5"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/newsletter/spacer.gif" alt="" width="728" height="20" border="0" /></td>
			</tr>
			<tr>
				<td colspan="5"><!-- begin 728x90 ad tag ADID %eaid!--><a href="*http://ad.doubleclick.net/N8484/jump/tim/newsletter_photos;tile=3;sz=728x90;dcove=r;ord=%%t%%" target="_top"><img src="http://ad.doubleclick.net/N8484/ad/tim/newsletter_photos;tile=3;sz=728x90;dcove=r;ord=%%t%%" border="0" width="728" height="90"></a><!-- End ad tag --></td>
			</tr>
			<tr>
				<td colspan="5"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/newsletter/spacer.gif" alt="" width="728" height="20" border="0" /></td>
			</tr>
		</table>
		<table cellpadding="0" cellspacing="0" border="0" width="728" bgcolor="#ed1c2e">
			<tr>
				<td width="10" bgcolor="#8c8c8c"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/newsletter/spacer.gif" width="10" height="1" border="0" /></td>
				<td width="708" bgcolor="#ed1c2e" colspan="3"><table cellpadding="0" cellspacing="0" border="0" width="708" bgcolor="#8c8c8c">
					<tr>
						<td colspan="6"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/newsletter/spacer.gif" alt="" width="708" height="20" border="0" /></td>
					</tr>
					<tr>
						<td colspan="6"><font face="georgia,serif;" style="color:#ffffff; font-weight:normal; font-size:17px;">Stay Connected with <a href="http://www.company.com/?xid=newsletter-photos-weekly" style="color:#ffffff; text-decoration:none">company.com</a></font></td>
					</tr>
					<tr>
						<td colspan="6"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/newsletter/spacer.gif" alt="" width="708" height="10" border="0" /></td>
					</tr>
					<tr>
						<td width="104" valign="top"><table cellpadding="0" cellspacing="0" border="0" width="104">
							<tr>
								<td width="37"><a style="font-family:arial,sans-serif; font-size:11px; color:#ffffff; text-decoration:none;" href="http://www.company.com/company/rss#rssfeeds/?xid=newsletter-photos-weekly"><img src="http://img.company.net/company/www/i/icon_stay_connected_rss_gray.png" alt="Icon for Subscribe to RSS Feeds" width="32" height="32" title="Subscribe to RSS Feeds" border="0" /></a></td>
								<td width="67"><a style="font-family:arial,sans-serif; font-size:11px; color:#ffffff; text-decoration:none;" title="Get Feeds" href="http://www.company.com/company/rss#rssfeeds/?xid=newsletter-photos-weekly">Subscribe to <br />RSS Feeds</a></td>
							</tr>
						</table></td>
						<td width="104" valign="top"><table cellpadding="0" cellspacing="0" border="0" width="104">
							<tr>
								<td width="37"><a style="font-family:arial,sans-serif; font-size:11px; color:#ffffff; text-decoration:none;" href="http://ebm.cheetahmail.com/r/regf2?a=0&amp;aid=1078532063&amp;n=1&amp;WBC3_SOURCE=newsletter-photos;xid=newsletter-photos-weekly"><img src="http://img.company.net/company/www/i/icon_stay_connected_nl_gray.png" alt="Icon for Sign Up for Newsletters" width="32" height="32" title="Sign Up for Newsletters" border="0" /></a></td>
								<td width="67"><a style="font-family:arial,sans-serif; font-size:11px; color:#ffffff; text-decoration:none;" target="_blank" href="http://ebm.cheetahmail.com/r/regf2?a=0&amp;aid=1078532063&amp;n=1&amp;WBC3_SOURCE=newsletter-photos;xid=newsletter-photos-weekly">Sign Up for <br/>Newsletters</a></td>
							</tr>
						</table></td>
						<td width="149" valign="top"><table cellpadding="0" cellspacing="0" border="0" width="149">
							<tr>
								<td width="37"><a style="font-family:arial,sans-serif; font-size:11px; color:#ffffff; text-decoration:none;" href="http://www.company.com/company/ipad/?xid=newsletter-photos-weekly"><img src="http://img.company.net/company/www/i/icon_stay_connected_ipad_gray.png" alt="Icon for Get the COMPANY Magazine iPad Edition" width="32" height="32" title="Get the COMPANY Magazine iPad Edition" border="0" /></a></td>
								<td width="112"><a style="font-family:arial,sans-serif; font-size:11px; color:#ffffff; text-decoration:none;" target="_blank" href="http://www.company.com/company/ipad/?xid=newsletter-photos-weekly">Get the COMPANY <br/>Magazine iPad Edition</a></td>
							</tr>
						</table></td>
						<td width="132" valign="top"><table cellpadding="0" cellspacing="0" border="0" width="132">
							<tr>
								<td width="37"><a style="font-family:arial,sans-serif; font-size:11px; color:#ffffff; text-decoration:none;" href="http://www.company.com/company/mobile/?xid=newsletter-photos-weekly"><img src="http://img.company.net/company/www/i/icon_stay_connected_mobile_gray.png" alt="Icon for Read COMPANY Mobile on your Phone" width="32" height="32" title="Read COMPANY Mobile on your Phone" border="0" /></a></td>
								<td width="95"><a style="font-family:arial,sans-serif; font-size:11px; color:#ffffff; text-decoration:none;" target="_blank" href="http://www.company.com/company/mobile/?xid=newsletter-photos-weekly">Read COMPANY Mobile <br/>on your Phone</a></td>
							</tr>
						</table></td>
						<td width="102" valign="top"><table cellpadding="0" cellspacing="0" border="0" width="102">
							<tr>
								<td width="37"><a target="_blank" style="font-family:arial,sans-serif; font-size:11px; color:#ffffff; text-decoration:none;" href="http://www.facebook.com/company/"><img src="http://img.company.net/company/www/i/icon_stay_connected_fb_gray.png" alt="Icon for Become a Fan of TIME" width="32" height="32" title="Become a Fan of TIME" border="0" /></a></td>
								<td width="65"><a style="font-family:arial,sans-serif; font-size:11px; color:#ffffff; text-decoration:none;" target="_blank" href="http://www.facebook.com/company/">Become a <br/>Fan of TIME</a></td>
							</tr>
						</table></td>
						<td width="117" valign="top"><table cellpadding="0" cellspacing="0" border="0" width="117">
							<tr>
								<td width="37"><a target="_blank" style="font-family:arial,sans-serif; font-size:11px; color:#ffffff; text-decoration:none;" href="http://twitter.com/#!/company/"><img src="http://img.company.net/company/www/i/icon_stay_connected_twitter_gray.png" alt="Icon for Get COMPANY Twitter Updates" width="32" height="32" title="Get COMPANY Twitter Updates" border="0" /></a></td>
								<td width="80"><a style="font-family:arial,sans-serif; font-size:11px; color:#ffffff; text-decoration:none;" target="_blank" href="http://twitter.com/#!/company/">Get COMPANY <br/>Twitter Updates</a></td>
							</tr>
						</table></td>
					</tr>
					<tr>
						<td colspan="6"><table cellpadding="0" cellspacing="0" border="0" width="708" bgcolor="#8c8c8c">
							<tr>
								<td width="708" bgcolor="#8c8c8c"><font face="arial, sans-serif" style="color:#ffffff; font-size:11px"><br />
									<font style="color:#ffffff; font-weight:bold;">TO UNSUBSCRIBE</font> <br />
									You have received this e-mail because you are subscribed to this newsletter from <a href="http://company.com?xid=newsletter-photos-weekly" style="color:#ffffff;">company.com</a>.<br />
									<a href="http://ebm.cheetahmail.com/r/webunsub?t=%%t%%&n=1&email=%%email%%" style="color:#ffffff;">Unsubscribe here.</a><br />
									<br />
									<font style="color:#ffffff; font-weight:bold;">EMAIL OPT-OUTS</font><br />
									<a href="http://www.company.com/emailprivacy/?xid=newsletter-photos-weekly" style="color:#ffffff;">Click here</a> for more information on how to opt-out of marketing communications from us and our partners, or copy and paste this link into your browser: <a href="http://www.company.com/emailprivacy?xid=newsletter-photos-weekly" style="color:#ffffff;">http://www.company.com/emailprivacy</a><br />
									<br />
									<font style="color:#ffffff; font-weight:bold;">PRIVACY POLICY</font><br />
									Please read our <a href="http://www.company.com/privacy/?xid=newsletter-photos-weekly" style="color:#ffffff;">Privacy Policy</a>, or copy and paste this link into your browser: <a href="http://www.company.com/privacy?xid=newsletter-photos-weekly" style="color:#ffffff;">http://www.company.com/privacy</a><br />
									<br />
									<font style="color:#ffffff; font-weight:bold;">FOR FURTHER COMMUNICATION, PLEASE CONTACT:</font><br />
									COMPANY Customer Service<br />
									3000 University Center Drive<br />
									Tampa, FL 33612-6408<br />
									<a href="http://www.timemediakit.com/" style="color:#ffffff;">How To Advertise</a> | <a href="https://subscription.company.com/storefront/subscribe-to-company/site/td-brightred-donor56for30.html?link=1002068&xid=newsletter-photos-weekly" style="color:#ffffff;">Give the Gift of TIME</a> | <a href="http://ebm.cheetahmail.com/r/webunsub?t=%%t%%&n=2&email=%%email%%" style="color:#ffffff;">Update Email</a><br />
									<br />
									</font>
								</td>
							</tr>
						</table></td>
					</tr>
				</table></td>
				<td width="10" bgcolor="#8c8c8c"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/newsletter/spacer.gif" width="10" height="1" border="0" /></td>
			</tr>
		</table></td>
	</tr>
</table>
</body>
</html>