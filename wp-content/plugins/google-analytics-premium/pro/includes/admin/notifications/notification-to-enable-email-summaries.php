<?php

/**
 * Add notification When pro version activated & email summaries option is disabled
 * Recurrence: 30 Days
 *
 * @since 7.12.3
 */
final class MonsterInsights_Notification_To_Enable_Summaries extends MonsterInsights_Notification_Event {

	public $notification_id             = 'monsterinsights_notification_to_enable_summaries';
	public $notification_interval       = 30; // in days
	public $notification_type           = array( 'basic', 'master', 'plus', 'pro' );

	/**
	 * Build Notification
	 *
	 * @return array $notification notification is ready to add
	 *
	 * @since 7.12.3
	 */
	public function prepare_notification_data( $notification ) {
		$email_summaries = monsterinsights_get_option( 'email_summaries', 'on' );

		if ( 'off' === $email_summaries ) {
			$settings_url            = is_network_admin() ? $this->get_view_url( 'monsterinsights-settings-block-email-summaries', 'monsterinsights_network', 'advanced' ) : $this->get_view_url( 'monsterinsights-settings-block-email-summaries', 'monsterinsights_settings', 'advanced' );
			$notification['title']   = __( 'Enable Email Summaries', 'ga-premium' );
			// Translators: email summaries notification content
			$notification['content'] = sprintf( __( 'Wouldn’t it be easy if you could get your website’s performance report in your email inbox every week? With our new feature, Email Summaries, you can now view all your important stats in a simple report that’s delivered straight to your inbox. You get an overview of your site\'s performance without logging in to WordPress or going through different Analytics reports. To enable email summaries feature, %sclick here%s.', 'ga-premium' ), '<a href="' . $settings_url . '">', '</a>' );
			$notification['btns']    = array(
				"enable_email_summaries" => array(
					'url'           => $settings_url,
					'text'          => __( 'Enable Email Summaries', 'ga-premium' ),
				),
			);

			return $notification;
		}

		return false;
	}

}

// initialize the class
new MonsterInsights_Notification_To_Enable_Summaries();
