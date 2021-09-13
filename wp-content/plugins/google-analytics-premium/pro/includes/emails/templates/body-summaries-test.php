<?php
/**
 * Email Body
 *
 * Heavily influenced by the great AffiliateWP plugin by Pippin Williamson.
 * https://github.com/AffiliateWP/AffiliateWP/tree/master/templates/emails
 *
 * @since 7.10.5
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$icon_stats = plugins_url( "pro/assets/img/emails/summaries/stats.png", MONSTERINSIGHTS_PLUGIN_FILE );
$icon_stats_2x = plugins_url( "pro/assets/img/emails/summaries/stats@2x.png", MONSTERINSIGHTS_PLUGIN_FILE );
$icon_users = plugins_url( "pro/assets/img/emails/summaries/users.png", MONSTERINSIGHTS_PLUGIN_FILE );
$icon_users_2x = plugins_url( "pro/assets/img/emails/summaries/users@2x.png", MONSTERINSIGHTS_PLUGIN_FILE );
$icon_views = plugins_url( "pro/assets/img/emails/summaries/views.png", MONSTERINSIGHTS_PLUGIN_FILE );
$icon_views_2x = plugins_url( "pro/assets/img/emails/summaries/views@2x.png", MONSTERINSIGHTS_PLUGIN_FILE );
$icon_increase = plugins_url( "pro/assets/img/emails/summaries/increase.png", MONSTERINSIGHTS_PLUGIN_FILE );
$icon_increase_2x = plugins_url( "pro/assets/img/emails/summaries/increase@2x.png", MONSTERINSIGHTS_PLUGIN_FILE );
$icon_decrease = plugins_url( "pro/assets/img/emails/summaries/decrease.png", MONSTERINSIGHTS_PLUGIN_FILE );
$icon_decrease_2x = plugins_url( "pro/assets/img/emails/summaries/decrease@2x.png", MONSTERINSIGHTS_PLUGIN_FILE );
$icon_pages = plugins_url( "pro/assets/img/emails/summaries/pages.png", MONSTERINSIGHTS_PLUGIN_FILE );
$icon_pages_2x = plugins_url( "pro/assets/img/emails/summaries/pages@2x.png", MONSTERINSIGHTS_PLUGIN_FILE );
$icon_referrals = plugins_url( "pro/assets/img/emails/summaries/referrals.png", MONSTERINSIGHTS_PLUGIN_FILE );
$icon_referrals_2x = plugins_url( "pro/assets/img/emails/summaries/referrals@2x.png", MONSTERINSIGHTS_PLUGIN_FILE );

$site_url 	= 'https://example.com';
$start_date = "January 01";
$end_date = "January 31, 2020";

$total_visitors  		   = "484000";
$prev_visitors_percentage  = "40";
$visitors_percentage_icon  = $icon_decrease;
$visitors_percentage_icon_2x  	= $icon_decrease_2x;
$visitors_percentage_class 		= 'mcnTextDecrease';
$visitors_difference 	   = __( 'Decrease visitors: ', 'ga-premium' );
if ( (int)$prev_visitors_percentage == $prev_visitors_percentage && (int)$prev_visitors_percentage >= 0 ) {
	$visitors_percentage_icon  		= $icon_increase;
	$visitors_percentage_icon_2x  	= $icon_increase_2x;
	$visitors_percentage_class  = 'mcnTextIncrease';
	$visitors_difference = __('Increase visitors: ', 'ga-premium' );
}

$total_pageviews = "1800000";
$prev_pageviews_percentage  = "-32";
$pageviews_percentage_icon  = $icon_decrease;
$pageviews_percentage_icon_2x  	= $icon_decrease_2x;
$pageviews_percentage_class 	= 'mcnTextDecrease';
$pageviews_difference 	    	= __('Decrease pageviews: ', 'ga-premium' );
if ( (int)$prev_pageviews_percentage == $prev_pageviews_percentage && (int)$prev_pageviews_percentage >= 0 ) {
	$pageviews_percentage_icon  	= $icon_increase;
	$pageviews_percentage_icon_2x  	= $icon_increase_2x;
	$pageviews_percentage_class = 'mcnTextIncrease';
	$pageviews_difference = __('Increase pageviews: ', 'ga-premium' );
}

$top_pages = array(
	array(
		'url' => '/contact',
		'title' => 'Contact Page Your Website',
		'hostname' => 'https://example.com',
		'sessions' => '10980',
	),
	array(
		'url' => '/sample-page',
		'title' => 'Sample Page - Your Website',
		'hostname' => 'https://example.com',
		'sessions' => '980',
	),
	array(
		'url' => '/test-page',
		'title' => 'Test Page Your Website',
		'hostname' => 'https://example.com',
		'sessions' => '80',
	),
	array(
		'url' => '/contact-us',
		'title' => 'Contact Us Your Website',
		'hostname' => 'https://example.com',
		'sessions' => '80',
	),
);
$top_referrals = array(
	array(
		'url' => 'https://facebook.com/',
		'sessions' => '100980',
	),
	array(
		'url' => 'https://youtube.com/',
		'sessions' => '9080',
	),
	array(
		'url' => 'https://wordpress.org/',
		'sessions' => '9080',
	),
	array(
		'url' => 'https://example.org/',
		'sessions' => '9080',
	),
);
$more_pages = "https://example.com";
$more_referrals = "https://example.com";

?>
<tr>
	<td valign="top" class="mcnTextBlockInner" style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
		
		<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" class="mcnTextContentContainer">
			<tbody>
				<tr style="display:block;">
					<td style="padding-right: 40px;padding-left: 40px;font-weight: bold;font-size: 24px;line-height: 28px;color: #393F4C;" class="mcnTextContent"><?php echo $title; ?></td>
				</tr>
				<tr style="display:block;">
					<td style="padding-right: 40px;padding-left: 40px;padding-top:8px;font-weight: normal;font-size: 14px;line-height: 16px;color: #7F899F;" class="mcnTextContent"><?php echo $start_date; ?> - <?php echo $end_date; ?></td>
				</tr>
				<tr style="display:block;">
				  	<td style="padding-top:8px;padding-left: 40px;padding-right: 40px;font-weight: bold;font-size: 14px;line-height: 16px;color: #7F899F;text-align:left;" class="mcnTextContent">
				    	<?php 
					  		if( ! empty( $icon_stats ) ) {
					  			echo '<img style="margin-right:5px;margin-bottom: -2px;" src="' . esc_url( $icon_stats ) . '" srcset="' . esc_url( $icon_stats_2x ) . ' 2x" target="_blank" alt="' . __( 'Website: ', 'ga-premium' ) . '" />';
					  		}
					  	?>
						<a href="<?php echo $site_url; ?>" style="font-weight: bold;font-size: 14px;line-height: 16px;color: #7F899F;text-decoration: underline;"><?php echo $site_url; ?></a>
					</td>
				</tr>
				<tr style="display:block;padding: 30px 40px 0 40px;">
					<td style="font-weight: bold;font-size: 14px;line-height: 27px;color: #393F4C;" class="mcnTextContent"><?php _e( 'Hi there!', 'ga-premium' ); ?></td>
				</tr>
				<tr style="display:block;padding:0 40px;">
					<td style="font-weight: normal;font-size: 14px;line-height: 20px;color: #4F5769;" class="mcnTextContent"><?php echo $description; ?></td>
				</tr>                           
			</tbody>
		</table>

		<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" role="presentation">
			<tbody>
				<tr style="display:inline-block;width:82%;padding: 65px 9% 0 9%;">
					<td style="width:50%;float:left;text-align:center;">
						<?php 
					  		if( ! empty( $icon_users ) ) {
					  			echo '<img src="' . esc_url( $icon_users ) . '" srcset="' . esc_url( $icon_users_2x ) . ' 2x" target="_blank" alt="' . __( 'Visitors', 'ga-premium' ) . '" />';
					  		}
					  	?>
					</td>
					<td style="width:50%;float:left;text-align:center;">
						<?php 
					  		if( ! empty( $icon_views ) ) {
					  			echo '<img src="' . esc_url( $icon_views ) . '" srcset="' . esc_url( $icon_views_2x ) . ' 2x" target="_blank" alt="' . __( 'Pageviews', 'ga-premium' ) . '" />';
					  		}
					  	?>
					</td>
				</tr>
				<tr style="display:inline-block;width:82%;padding: 0 9%;">
					<td style="width:50%;float:left;padding-top:5px;text-align:center;font-weight: bold;font-size: 14px;line-height: 16px;color: #393F4C;" class="mcnTextContent"><?php _e( 'Total Visitors', 'ga-premium' ); ?></td>
					<td style="width:50%;float:left;padding-top:5px;text-align:center;font-weight: bold;font-size: 14px;line-height: 16px;color: #393F4C;" class="mcnTextContent"><?php _e( 'Total Pageviews', 'ga-premium' ); ?></td>
				</tr>
				<tr style="display:inline-block;width:82%;padding: 0 9%;">
					<td style="width:50%;float:left;padding-top:10px;text-align:center;font-weight: normal;font-size: 32px;line-height: 37px;color: #393F4C;" class="mcnTextContent"><?php echo number_format_i18n( $total_visitors ); ?></td>
					<td style="width:50%;float:left;padding-top:10px;text-align:center;font-weight: normal;font-size: 32px;line-height: 37px;color: #393F4C;" class="mcnTextContent"><?php echo number_format_i18n( $total_pageviews ); ?></td>
				</tr>
				<tr style="display:inline-block;width:82%;padding: 0 9%;">
					<td style="width:50%;float:left;padding-top:15px;text-align:center;line-height: 16px;" class="mcnTextContent <?php echo $visitors_percentage_class; ?>">
						<?php 
					  		if( ! empty( $visitors_percentage_icon ) ) {
					  			echo '<img src="' . esc_url( $visitors_percentage_icon ) . '" srcset="' . esc_url( $visitors_percentage_icon_2x ) . ' 2x" target="_blank" alt="' . $visitors_difference . '" />';
					  		}
					  	?>
					  	<?php echo $prev_visitors_percentage; ?>%
					</td>
					<td style="width:50%;float:left;padding-top:15px;text-align:center;line-height: 16px;" class="mcnTextContent <?php echo $pageviews_percentage_class; ?>">
						<?php 
					  		if( ! empty( $pageviews_percentage_icon ) ) {
					  			echo '<img src="' . esc_url( $pageviews_percentage_icon ) . '" srcset="' . esc_url( $pageviews_percentage_icon_2x ) . ' 2x" target="_blank" alt="' . $pageviews_difference . '" />';
					  		}
					  	?>
					  	<?php echo $prev_pageviews_percentage; ?>%
					</td>
				</tr>
				<tr style="display:inline-block;width:82%;padding: 0 9%;">
					<td style="width:50%;float:left;padding-top:5px;text-align:center;font-weight: normal;font-size: 12px;line-height: 14px;color: #9CA4B5;" class="mcnTextContent"><?php _e( 'vs previous 30 days', 'ga-premium' ); ?></td>
					<td style="width:50%;float:left;padding-top:5px;text-align:center;font-weight: normal;font-size: 12px;line-height: 14px;color: #9CA4B5;" class="mcnTextContent"><?php _e( 'vs previous 30 days', 'ga-premium' ); ?></td>
				</tr>
			</tbody>
		</table>


		<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" role="presentation">
			<tbody>
                <tr style="display:block;width:100%;">
                  <td style="width:100%;display:block;height: 50px;border-bottom:1px solid #F0F2F4;"></td>
                </tr>
            </tbody>
        </table>

        <?php if( ! empty( $top_pages ) )  : ?>
            <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" role="presentation">
				<tbody>
					<tr style="display:block;width:100%;padding: 40px 0 0 0;">
						<td style="display:block;width:100%;text-align:center;">
							<?php 
								if( ! empty( $icon_pages ) ) {
						  			echo '<img src="' . esc_url( $icon_pages ) . '" srcset="' . esc_url( $icon_pages_2x ) . ' 2x" target="_blank" alt="' . __( 'Pages', 'ga-premium' ) . '" />';
						  		}
							?>
						</td>
					</tr>
					<tr style="display:block;width:100%;">
						<td style="display:block;width:100%;padding-top:5px;text-align:center;font-weight: bold;font-size: 14px;line-height: 20px;color: #393F4C;" class="mcnTextContent"><?php _e( 'Top Pages', 'ga-premium' ); ?></td>
					</tr>
				</tbody>
			</table>

			<table align="center" border="0" cellpadding="0" cellspacing="0" width="64%" style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" role="presentation">
				<tbody>
					<tr style="">
						<td style="width:67%;float:left;padding-top:30px;padding-bottom:10px;text-align:left;font-weight: normal;font-size: 12px;line-height: 14px;color: #9CA4B5;" class="mcnTextContent"><?php _e( 'Page Title', 'ga-premium' ); ?></td>
						<td style="width:33%;float:left;padding-top:30px;padding-bottom:10px;text-align:right;font-weight: normal;font-size: 12px;line-height: 14px;color: #9CA4B5;" class="mcnTextContent"><?php _e( 'Pageviews', 'ga-premium' ); ?></td>
					</tr>

					<?php $i = 0; ?>
					<?php while ( $i <= 2 ) : ?>
						<tr style="display:flex;">
							<td style="width:67%;float:left;padding-top:8px;padding-bottom:8px;border-bottom:1px solid #F0F2F4;text-align:left;font-weight: normal;font-size: 14px;line-height: 16px;color: #393F4C;overflow:hidden;" class="mcnTextContent"><a href="<?php echo esc_url( $top_pages[$i]['hostname'] . $top_pages[$i]['url'] ); ?>" target="_blank" style="text-decoration:none;color: #393F4C;"><?php echo $i + 1 . '. ' . monsterinsights_trim_text( $top_pages[$i]['title'], 2 ); ?></a></td>
							<td style="width:33%;float:left;padding-top:8px;padding-bottom:8px;border-bottom:1px solid #F0F2F4;text-align:right;font-weight: normal;font-size: 14px;line-height: 16px;color: #338EEF;overflow:hidden;text-overflow: ellipsis;" class="mcnTextContent"><?php echo number_format_i18n( $top_pages[$i]['sessions'] ); ?></td>
						</tr>
						<?php $i++; ?>
					<?php endwhile; ?>	

					<tr style="display:flex;">
						<td style="width:67%;float:left;padding-top:18px;text-align:left;font-weight: normal;font-size: 12px;line-height: 14px;color: #9CA4B5;text-decoration: underline;" class="mcnTextContent"><a href="<?php echo $more_pages; ?>" style="color: #9CA4B5;"><?php _e( 'View More', 'ga-premium'); ?></a></td>
					</tr>
				</tbody>
			</table>

			<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" role="presentation">
				<tbody>
                    <tr style="display:block;width:100%;">
                      <td style="width:100%;display:block;height: 50px;border-bottom:1px solid #F0F2F4;"></td>
                    </tr>
                </tbody>
            </table>
		<?php endif; ?>

		<?php if( ! empty( $top_referrals ) )  : ?>
            <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" role="presentation">
				<tbody>
					<tr style="display:block;width:100%;padding: 40px 0 0 0;">
						<td style="display:block;width:100%;text-align:center;">
							<?php 
						  		if( ! empty( $icon_referrals ) ) {
						  			echo '<img src="' . esc_url( $icon_referrals ) . '" srcset="' . esc_url( $icon_referrals_2x ) . ' 2x" target="_blank" alt="' . __( 'Referrals', 'ga-premium' ) . '" />';
						  		}
						  	?>
						</td>
					</tr>
					<tr style="display:block;width:100%;">
						<td style="display:block;width:100%;padding-top:5px;text-align:center;font-weight: bold;font-size: 14px;line-height: 20px;color: #393F4C;" class="mcnTextContent"><?php _e( 'Top Referrals', 'ga-premium' ); ?></td>
					</tr>
				</tbody>
			</table>

			<table align="center" border="0" cellpadding="0" cellspacing="0" width="64%" style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" role="presentation">
				<tbody>
					<tr style="">
						<td style="width:67%;float:left;padding-top:30px;padding-bottom:10px;text-align:left;font-weight: normal;font-size: 12px;line-height: 14px;color: #9CA4B5;" class="mcnTextContent"><?php _e( 'Page Title', 'ga-premium' ); ?></td>
						<td style="width:33%;float:left;padding-top:30px;padding-bottom:10px;text-align:right;font-weight: normal;font-size: 12px;line-height: 14px;color: #9CA4B5;" class="mcnTextContent"><?php _e( 'Sessions', 'ga-premium' ); ?></td>
					</tr>

					<?php $i = 0; ?>
					<?php while ( $i <= 2 ) : ?>
						<tr style="display:flex;">
							<td style="width:67%;float:left;padding-top:8px;padding-bottom:8px;border-bottom:1px solid #F0F2F4;text-align:left;font-weight: normal;font-size: 14px;line-height: 16px;color: #393F4C;overflow:hidden;" class="mcnTextContent"><a href="<?php echo esc_url( $top_referrals[$i]['url'] ); ?>" target="_blank" style="text-decoration:none;color: #393F4C;"><?php echo $i + 1 . '. '; ?><?php echo $top_referrals[$i]['url']; ?></a></td>
							<td style="width:33%;float:left;padding-top:8px;padding-bottom:8px;border-bottom:1px solid #F0F2F4;text-align:right;font-weight: normal;font-size: 14px;line-height: 16px;color: #338EEF;overflow:hidden;text-overflow: ellipsis;" class="mcnTextContent"><?php echo number_format_i18n( $top_referrals[$i]['sessions'] ); ?></td>
						</tr>
						<?php $i++; ?>
					<?php endwhile; ?>

					<tr style="display:flex;">
						<td style="width:67%;float:left;padding-top:18px;text-align:left;font-weight: normal;font-size: 12px;line-height: 14px;color: #9CA4B5;text-decoration: underline;" class="mcnTextContent"><a href="<?php echo $more_referrals; ?>" style="color: #9CA4B5;"><?php _e( 'View More', 'ga-premium' ); ?></a></td>
					</tr>
				</tbody>
			</table>

			<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" role="presentation">
				<tbody>
                    <tr style="display:block;width:100%;">
                      <td style="width:100%;display:block;height: 50px;border-bottom:1px solid #F0F2F4;"></td>
                    </tr>
                </tbody>
            </table>
        <?php endif; ?>
	</td>
</tr>