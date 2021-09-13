<?php

/**
 * Add notification when pro version is activated and custom dimension for author tracking is not setup
 * Recurrence: 30 Days
 *
 * @since 7.12.3
 */
final class MonsterInsights_Notification_To_Enable_Author_Tracking extends MonsterInsights_Notification_Event {

	public $notification_id             = 'monsterinsights_notification_to_enable_author_tracking';
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
		$custom_dimensions          = monsterinsights_get_option( 'custom_dimensions', array() );
		$is_author_tracking_enabled = false;

		if ( is_array( $custom_dimensions ) && ! empty( $custom_dimensions ) ) {
			foreach ( $custom_dimensions as $custom_dimension ) {
				if ( isset( $custom_dimension['type'] ) && "author" === $custom_dimension['type'] ) {
					$is_author_tracking_enabled = true;
					break;
				}
			}
		}

		if ( false === $is_author_tracking_enabled || ( is_array( $custom_dimensions ) && empty( $custom_dimensions ) ) ) {

			$notification['title']   = __( 'Author Tracking is Not Enabled', 'ga-premium' );
			// Translators: author tracking notification content
			$notification['content'] = sprintf( __( 'Author tracking in Google Analytics gives you valuable insights into the performance of your blog authors. With author tracking, you can discover the most popular author of your blog, sort page views for articles by author, see which author’s posts keep visitors on your site by analyzing the bounce rate, get instant WordPress author stats for optimization. <br><br>%sCheck this article%s to set up author tracking.', 'ga-premium' ), '<a href="'. $this->build_external_link( 'https://www.monsterinsights.com/how-to-set-up-author-tracking-in-google-analytics/#:~:text=How%20to%20Track%20Author%20Using%20MonsterInsights&text=MonsterInsights%20automatically%20tracks%20different%20authors,most%20traffic%20on%20your%20site.' ) .'" target="_blank">', '</a>' );
			$notification['btns']    = array(
				"learn_more" => array(
					'url'           => $this->build_external_link( 'https://www.monsterinsights.com/how-to-set-up-author-tracking-in-google-analytics/#:~:text=How%20to%20Track%20Author%20Using%20MonsterInsights&text=MonsterInsights%20automatically%20tracks%20different%20authors,most%20traffic%20on%20your%20site.' ),
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
new MonsterInsights_Notification_To_Enable_Author_Tracking();
