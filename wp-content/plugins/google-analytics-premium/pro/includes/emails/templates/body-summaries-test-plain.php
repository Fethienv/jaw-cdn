<?php
/**
 * Email Summaries body template (test plain text).
 *
 * @since 7.10.5
 *
 * @version 7.10.5
 *
 * @var array  $info_block
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$top_pages = array(
	array(
		'url' => 'https://example.com/test',
		'title' => 'Contact Page',
		'hostname' => 'https://example.com',
		'sessions' => '10980',
	),
	array(
		'url' => 'https://example.com/test',
		'title' => 'Sample Page',
		'hostname' => 'https://example.com',
		'sessions' => '980',
	),
	array(
		'url' => 'https://example.com/test',
		'title' => 'Test Page',
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
);
$more_pages = "https://example.com";
$more_referrals = "https://example.com";

echo esc_html__( 'Hi there!', 'ga-premium' ) . "\n\n";


echo esc_html__( 'Website Traffic Summary', 'ga-premium' ) . "\n";
echo esc_html__( 'Let’s take a look at how your website traffic performed in the past month.', 'ga-premium' ) . "\n\n";

echo esc_html__( 'January 01 - January 31, 2020', 'ga-premium' ) . "\n";
echo esc_url( 'https://example.com' ) . "\n\n";

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo esc_html__( 'Total Visitors', 'ga-premium' ) . '   -   ' . number_format_i18n( '484000' ) . "\n";
echo esc_html__( 'Increase Visitors 13%', 'ga-premium' ) . "\n\n";

echo esc_html__( 'Total Pageviews', 'ga-premium' ). '   -   ' . number_format_i18n( '1800000' ) . "\n";
echo esc_html__( 'Decrease Pageviews 2%', 'ga-premium' ) . "\n\n";

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo esc_html__( 'Top Pages', 'ga-premium' ) . "\n\n";

$i = 0;
while ( $i <= 2 ) {
	echo $i + 1 . ". " . $top_pages[$i]['title'] . " - " . $top_pages[$i]['url'] . "\n\n";
	$i++;
}

echo "View More - " . $more_pages . "\n\n";;

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo esc_html__( 'Top Referrals', 'ga-premium' ) . "\n\n";

$i = 0;
while ( $i <= 2 ) {
	echo $i + 1 . ". " . $top_referrals[$i]['url'] . "\n\n";
	$i++;
}

echo "View More - " . $more_referrals . "\n\n";

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo esc_html__( 'To make sure you keep getting these emails, please add support@monsterinsights.com to your address book or whitelist us. Want out of the loop? Unsubscribe.', 'ga-premium' ) . "\n\n";
