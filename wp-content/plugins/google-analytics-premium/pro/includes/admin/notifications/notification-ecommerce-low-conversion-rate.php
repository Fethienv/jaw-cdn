<?php

/**
 * Add notification when when plus or pro version activated, enhanced eCommerce is active & conversion rate is lower than previous 15 days.
 * Recurrence: 15 Days
 *
 * @since 7.12.3
 */
final class MonsterInsights_Notification_Ecommerce_Low_Conversion_Rate extends MonsterInsights_Notification_Event {

	public $notification_id             = 'monsterinsights_notification_ecommerce_low_conversion_rate';
	public $notification_interval       = 15; // in days
	public $notification_type           = array( 'master', 'pro' );

	/**
	 * Build Notification
	 *
	 * @return array $notification notification is ready to add
	 *
	 * @since 7.12.3
	 */
	public function prepare_notification_data( $notification ) {
		$enhanced_commerce = (bool) monsterinsights_get_option( 'enhanced_ecommerce', false );

		if ( true === $enhanced_commerce ) {
			$data                                    = array();
			$report                                  = $this->get_report( 'ecommerce', $this->report_start_from, $this->report_end_to );
			$data['conversionrate']                  = isset( $report['data']['infobox']['conversionrate']['value'] ) ? number_format( $report['data']['infobox']['conversionrate']['value'], 2, '.', ',' ) : 0;
			$data['prev_conversion_rate_difference'] = isset( $report['data']['infobox']['conversionrate']['prev'] ) ? $report['data']['infobox']['conversionrate']['prev'] : 0;

			if ( ! empty( $data ) && $data['prev_conversion_rate_difference'] < 0 ) {
				// Translators: low conversion notification title
				$notification['title']   = sprintf( __( 'Your eCommerce Conversion Rate is %s%%', 'ga-premium' ), $data['conversionrate'] );
				// Translators: low conversion notification content
				$notification['content'] = sprintf( __( 'Your eCommerce Conversion Rate is lower compared to the previous 15 days. The first step is to identify what is causing this and track changes made to improve it. There are lots of possible explanations for a low conversion rate, and we’ll look at them in %sthis article%s.', 'ga-premium' ), '<a href="'. $this->build_external_link( 'https://optinmonster.com/reasons-your-ecommerce-site-has-a-low-conversion-rate/' ) .'" target="_blank">', '</a>' );
				$notification['btns']    = array(
					"view_report" => array(
						'url'  => $this->get_view_url( 'monsterinsights-report-ecommerce-conversion-rate', 'monsterinsights_reports', 'ecommerce' ),
						'text' => __( 'View eCommerce Report', 'ga-premium' )
					),
					"learn_more"  => array(
						'url'           => $this->build_external_link( 'https://optinmonster.com/reasons-your-ecommerce-site-has-a-low-conversion-rate/' ),
						'text'          => __( 'Learn More', 'ga-premium' ),
						'is_external'   => true,
					),
				);

				return $notification;
			}

			return false;
		}

		return false;
	}

}

// initialize the class
new MonsterInsights_Notification_Ecommerce_Low_Conversion_Rate();
