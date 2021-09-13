<?php
/**
 * Email Summaries body template (plain text).
 *
 * @since 7.10.5
 *
 * @version 7.10.5
 *
 * @var array $info_block
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$site_url   = get_site_url();
$start_date = isset( $startDate ) ? $startDate : date( "Y-m-d", strtotime( "-1 day, last week" ) );
$start_date = date( "F j, Y", strtotime( $start_date ) );
$end_date   = isset( $endDate ) ? $endDate : date( "Y-m-d", strtotime( "last saturday" ) );
$end_date   = date( "F j, Y", strtotime( $end_date ) );

$total_visitors           = isset( $summaries['data']['infobox']['sessions']['value'] ) ? $summaries['data']['infobox']['sessions']['value'] : 0;
$prev_visitors_percentage = isset( $summaries['data']['infobox']['sessions']['prev'] ) ? $summaries['data']['infobox']['sessions']['prev'] : 0;
// Translators: Placeholder adds the percentage of visitors.
$visitors_difference = sprintf( __( 'Decrease visitors %s', 'ga-premium' ), $prev_visitors_percentage );
if ( (int) $prev_visitors_percentage === (int) $prev_visitors_percentage && (int) $prev_visitors_percentage >= 0 ) {
	// Translators: Placeholder adds the percentage of visitors.
	$visitors_difference = sprintf( __( 'Increase visitors %s', 'ga-premium' ), $prev_visitors_percentage );
}

$total_pageviews           = isset( $summaries['data']['infobox']['pageviews']['value'] ) ? $summaries['data']['infobox']['pageviews']['value'] : 0;
$prev_pageviews_percentage = isset( $summaries['data']['infobox']['pageviews']['prev'] ) ? $summaries['data']['infobox']['pageviews']['prev'] : 0;
// Translators: Placeholder adds the percentage of pageviews.
$pageviews_difference      = sprintf( __( 'Decrease pageviews %s', 'ga-premium' ), $prev_pageviews_percentage );
if ( (int) $prev_pageviews_percentage === (int) $prev_pageviews_percentage && (int) $prev_pageviews_percentage >= 0 ) {
	// Translators: Placeholder adds the percentage of pageviews.
	$pageviews_difference = sprintf( __( 'Increase pageviews %s', 'ga-premium' ), $prev_pageviews_percentage );
}

$top_pages      = isset( $summaries['data']['toppages'] ) ? $summaries['data']['toppages'] : '';
$top_referrals  = isset( $summaries['data']['referrals'] ) ? $summaries['data']['referrals'] : '';
$more_pages     = isset( $summaries['data']['galinks']['topposts'] ) ? $summaries['data']['galinks']['topposts'] : '';
$more_referrals = isset( $summaries['data']['galinks']['referrals'] ) ? $summaries['data']['galinks']['referrals'] : '';

echo esc_html__( 'Hi there!', 'ga-premium' ) . "\n\n";


echo esc_html__( 'Website Traffic Summary', 'ga-premium' ) . "\n";
echo esc_html__( 'Let’s take a look at how your website traffic performed in the past month.', 'ga-premium' ) . "\n\n";

echo $start_date . " - " . $end_date . "\n";
echo $site_url . "\n\n";

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo esc_html__( 'Total Visitors', 'ga-premium' ) . '   -   ' . number_format_i18n( $total_visitors ) . "\n";
echo $visitors_difference . "%\n\n";

echo esc_html__( 'Total Pageviews', 'ga-premium' ) . '   -   ' . number_format_i18n( $total_pageviews ) . "\n";
echo $pageviews_difference . "%\n\n";

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

if ( ! empty( $top_pages ) ) {
	echo esc_html__( 'Top Pages', 'ga-premium' ) . "\n\n";

	$i = 0;
	while ( $i <= 2 ) {
		echo $i + 1 . ". " . $top_pages[ $i ]['title'] . " - " . $top_pages[ $i ]['hostname'] . $top_pages[ $i ]['url'] . "\n\n";
		$i ++;
	}

	echo "View More - " . $more_pages . "\n\n";

	echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";
}


if ( ! empty( $top_referrals ) ) {
	echo esc_html__( 'Top Referrals', 'ga-premium' ) . "\n\n";

	$i = 0;
	while ( $i <= 2 ) {
		echo $i + 1 . ". " . $top_referrals[ $i ]['url'] . "\n\n";
		$i ++;
	}

	echo "View More - " . $more_referrals . "\n\n";

	echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";
}

if ( isset( $info_block['title'] ) && ! empty( $info_block['title'] ) ) {
	echo esc_html__( 'Pro Tip from our experts', 'ga-premium' ) . "\n\n";

	echo $info_block['title'] . "\n\n";
	echo $info_block['html'] . "\n\n";

	if ( isset( $info_block['link_text'] ) && ! empty( $info_block['link_text'] ) && isset( $info_block['link_url'] ) && ! empty( $info_block['link_url'] ) ) {
		echo $info_block['link_text'] . " " . $info_block['link_url'] . "\n\n";
	}
}

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo esc_html__( "To make sure you keep getting these emails, please add support@monsterinsights.com to your address book or whitelist us. Want out of the loop? Unsubscribe ", "ga-premium" ) . $settings_tab_url . "\n\n";
