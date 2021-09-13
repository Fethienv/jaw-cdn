<?php

/**
 * Add notification when pro version is activated and custom dimension for tracking category/tags are not setup
 * Recurrence: 35 Days
 *
 * @since 7.12.3
 */
final class MonsterInsights_Notification_To_Track_Categories extends MonsterInsights_Notification_Event {

	public $notification_id             = 'monsterinsights_notification_to_track_categories';
	public $notification_interval       = 35; // in days
	public $notification_first_run_time = '+10 day';
	public $notification_type           = array( 'master', 'pro' );

	/**
	 * Build Notification
	 *
	 * @return array $notification notification is ready to add
	 *
	 * @since 7.12.3
	 */
	public function prepare_notification_data( $notification ) {
		$custom_dimensions            = monsterinsights_get_option( 'custom_dimensions', array() );
		$is_category_tracking_enabled = false;
		$is_tags_tracking_enabled     = false;

		if ( is_array( $custom_dimensions ) && ! empty( $custom_dimensions ) ) {
			foreach ( $custom_dimensions as $custom_dimension ) {
				if ( isset( $custom_dimension['type'] ) && "category" === $custom_dimension['type'] ) {
					$is_category_tracking_enabled = true;
				}
				if ( isset( $custom_dimension['type'] ) && "tags" === $custom_dimension['type'] ) {
					$is_tags_tracking_enabled = true;
				}
			}
		}

		if ( false === $is_category_tracking_enabled || false === $is_tags_tracking_enabled || ( is_array( $custom_dimensions ) && empty( $custom_dimensions ) ) ) {

			$notification['title']   = __( 'Track WordPress Categories and Tags in Google Analytics', 'ga-premium' );
			// Translators: track categories notification content
			$notification['content'] = sprintf( __( 'Do you want to know which WordPress categories and tags are the most popular on your site? By default, Google Analytics doesn’t track them, but there’s an easy way to track WordPress tags and categories. <br><br>All you need to do is set up the Custom Dimensions settings in MonsterInsights and Google Analytics. Detailed instructions on how to set up categories and tags tracking are available in %sthis article.%s', 'ga-premium' ), '<a href="'. $this->build_external_link( 'https://www.monsterinsights.com/how-to-track-wordpress-categories-and-tags-in-google-analytics/' ) .'" target="_blank">', '</a>' );
			$notification['btns']    = array(
				"learn_more" => array(
					'url'           => $this->build_external_link( 'https://www.monsterinsights.com/how-to-track-wordpress-categories-and-tags-in-google-analytics/' ),
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
new MonsterInsights_Notification_To_Track_Categories();
