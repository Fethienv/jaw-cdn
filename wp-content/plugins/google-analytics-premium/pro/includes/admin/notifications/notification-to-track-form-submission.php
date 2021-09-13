<?php

/**
 * Add notification when pro version activated & forms tracking option is disabled.
 * Recurrence: 20 Days
 *
 * @since 7.12.3
 */
final class MonsterInsights_Notification_To_Track_Form_Submission extends MonsterInsights_Notification_Event {

	public $notification_id             = 'monsterinsights_notification_to_track_form_submission';
	public $notification_interval       = 20; // in days
	public $notification_type           = array( 'master', 'pro' );

	/**
	 * Build Notification
	 *
	 * @return array $notification notification is ready to add
	 *
	 * @since 7.12.3
	 */
	public function prepare_notification_data( $notification ) {
		$report = $this->get_report( 'forms', $this->report_start_from, $this->report_end_to );

		if ( isset( $report['success'] ) && false === $report['success'] && ! empty( $report['error'] ) ) {

			$notification['title']   = __( 'Track Form Submissions in WordPress', 'ga-premium' );
			// Translators: form submission notification content
			$notification['content'] = sprintf( __( 'It seems form submissions tracking option is not active on your site. We recommend setting up form tracking to know how your WordPress forms perform or which specific form drives the most conversions. <br><br>Read more about how to use Google Analytics to track form submission in WordPress in %sthis article%s.', 'ga-premium' ), '<a href="https://www.monsterinsights.com/how-to-track-your-wordpress-form-conversions-in-google-analytics/" target="_blank">', '</a>' );
			$notification['btns']    = array(
				"learn_more" => array(
					'url'           => $this->build_external_link( 'https://www.monsterinsights.com/how-to-track-your-wordpress-form-conversions-in-google-analytics/' ),
					'text'          => __( 'Learn More', 'ga-premium' ),
					'is_external'   => true,
				),
			);

			return $notification;
		}

		return false;
	}

}

// initialize the class
new MonsterInsights_Notification_To_Track_Form_Submission();
