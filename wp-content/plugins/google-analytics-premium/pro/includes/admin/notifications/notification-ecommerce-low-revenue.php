<?php

/**
 * Add notification when plus or pro version activated, eCommerce is active & revenue is lower than previous 30 days
 * Recurrence: 30 Days
 *
 * @since 7.12.3
 */
final class MonsterInsights_Notification_Ecommerce_Low_Revenue extends MonsterInsights_Notification_Event {

	public $notification_id             = 'monsterinsights_notification_ecommerce_low_revenue';
	public $notification_interval       = 30; // in days
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
			$data                            = array();
			$report                          = $this->get_report( 'ecommerce', $this->report_start_from, $this->report_end_to );
			$data['revenue']                 = isset( $report['data']['infobox']['revenue']['value'] ) ? $report['data']['infobox']['revenue']['value'] : 0;
			$data['prev_revenue_difference'] = isset( $report['data']['infobox']['revenue']['prev'] ) ? $report['data']['infobox']['revenue']['prev'] : 0;

			if ( ! empty( $data ) && $data['prev_revenue_difference'] < 0 ) {
				// Translators: low revenue notification title
				$notification['title']   = sprintf( __( 'Your eCommerce revenue has dropped to $%s', 'ga-premium' ), $data['revenue'] );
				// Translators: low revenue notification content
				$notification['content'] = sprintf( __( 'Your eCommerce revenue decreased compared to the previous 30 days. Is your online store performing as well as it could? Unless every single person who visits your store makes a purchase, there’s always room for improvement, and eCommerce optimization may not be as much work as you think. <br><br>If you know where to look, just a few small tweaks can mean a 50%% increase in sales (or even more). To boost revenue, take a look at the guidelines in %sthis article%s.', 'ga-premium' ), '<a href="'. $this->build_external_link( 'https://optinmonster.com/ecommerce-optimization-guide/' ) .'" target="_blank">', '</a>' );
				$notification['btns']    = array(
					"view_ecommerce_report" => array(
						'url'  => $this->get_view_url( 'monsterinsights-report-ecommerce-revenue', 'monsterinsights_reports', 'ecommerce' ),
						'text' => __( 'View eCommerce Report', 'ga-premium' )
					),
					"learn_more"            => array(
						'url'           => $this->build_external_link( 'https://optinmonster.com/ecommerce-optimization-guide/' ),
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
new MonsterInsights_Notification_Ecommerce_Low_Revenue();
