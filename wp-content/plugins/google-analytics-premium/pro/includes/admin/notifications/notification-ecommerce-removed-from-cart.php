<?php

/**
 * Add notification when number of removed items from cart is higher than previous 15 days
 * Recurrence: 15 Days
 *
 * @since 7.12.3
 */
final class MonsterInsights_Notification_Ecommerce_Removed_From_Cart extends MonsterInsights_Notification_Event {

	public $notification_id             = 'monsterinsights_notification_ecommerce_removed_from_cart';
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
			$data                                = array();
			$report                              = $this->get_report( 'ecommerce', $this->report_start_from, $this->report_end_to );
			$data['remfromcart']                 = isset( $report['data']['infobox']['remfromcart']['value'] ) ? $report['data']['infobox']['remfromcart']['value'] : 0;
			$data['prev_remfromcart_difference'] = isset( $report['data']['infobox']['remfromcart']['prev'] ) ? $report['data']['infobox']['remfromcart']['prev'] : 0;

			if ( ! empty( $data ) && $data['prev_remfromcart_difference'] > 0 ) {
				// Translators: eCommerce removed from cart notification title
				$notification['title']   = sprintf( __( 'More items (%s) have been removed from the cart in the last 15 Days', 'ga-premium' ), $data['remfromcart'] );
				// Translators: eCommerce removed from cart notification content
				$notification['content'] = sprintf( __( 'Your site\'s visitors removed products from their cart %s times which is higher than in the previous 15 days. Shopping cart abandonment is one of the biggest problems online business owners face. To reduce cart abandonment, follow the guidelines %shere%s.', 'ga-premium' ), $data['remfromcart'], '<a href="'. $this->build_external_link( 'https://optinmonster.com/11-advanced-tips-to-reduce-shopping-cart-abandonment/' ) .'" target="_blank">', '</a>' );
				$notification['btns']    = array(
					"view_report" => array(
						'url'  => $this->get_view_url( 'monsterinsights-report-removed-from-cart', 'monsterinsights_reports', 'ecommerce' ),
						'text' => __( 'View eCommerce Report', 'ga-premium' )
					),
					"learn_more"  => array(
						'url'           => $this->build_external_link( 'https://optinmonster.com/11-advanced-tips-to-reduce-shopping-cart-abandonment/' ),
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
new MonsterInsights_Notification_Ecommerce_Removed_From_Cart();
