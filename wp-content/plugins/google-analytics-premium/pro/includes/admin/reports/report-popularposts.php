<?php
/**
 * Popular Posts Report
 *
 * Ensures all of the reports have a uniform class with helper functions.
 *
 * @since 7.13.0
 *
 * @package MonsterInsights
 * @subpackage Reports
 * @author  MonsterInsights Team
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class MonsterInsights_Report_PopularPosts extends MonsterInsights_Report {

	public $title;
	public $class = 'MonsterInsights_Report_PopularPosts';
	public $name = 'popularposts';
	public $version = '1.0.0';
	public $level = 'pro';

	/**
	 * Primary class constructor.
	 *
	 * @access public
	 * @since 6.0.0
	 */
	public function __construct() {
		$this->title = __( 'Popular Posts', 'ga-premium' );
		parent::__construct();
	}

	public function requirements( $error = false, $args = array(), $name = '' ) {
		if ( ! empty( $error ) || $name !== 'popularposts' ) {
			return $error;
		}

		if ( ! class_exists( 'MonsterInsights_Dimensions' ) ) {
			add_filter( 'monsterinsights_reports_handle_error_message', array( $this, 'add_error_addon_link' ) );

			// Translators: %s will be the action (install/activate) which will be filled depending on the addon state.
			$text = __( 'Please %s the MonsterInsights Dimensions addon to load popular posts.', 'ga-premium' );

			if ( current_user_can( 'install_plugins' ) ) {
				return $text;
			} else {
				return sprintf( $text, __( 'install', 'ga-premium' ) );
			}
		}

		$dimension_id = $this->get_post_type_dimension_id();
		if ( empty( $dimension_id ) || 0 === $dimension_id ) {
			add_filter( 'monsterinsights_reports_handle_error_message', array(
				$this,
				'add_dimensions_settings_link'
			) );

			return __( 'Please enable the "Post type" custom dimension in order to load Popular Posts data correctly.', 'ga-premium' );
		}

		return $error;
	}

	public function additional_data() {
		$dimension_id = $this->get_post_type_dimension_id();

		return array( 'dimension' => $dimension_id );
	}

	/**
	 * Get the custom dimensions for the dashboard
	 *
	 * @return array
	 */
	public function get_post_type_dimension_id() {
		$custom_dimension_option = monsterinsights_get_option( 'custom_dimensions', array() );

		$dimension_id = 0;

		foreach ( $custom_dimension_option as $dimension ) {
			if ( 'post_type' === $dimension['type'] ) {
				$dimension_id = $dimension['id'];
				break;
			}
		}

		return $dimension_id;
	}

	/**
	 * Prepare report-specific data for output.
	 *
	 * @param array $data The data from the report before it gets sent to the frontend.
	 *
	 * @return mixed
	 */
	public function prepare_report_data( $data ) {

		// Add active dimensions.
		if ( class_exists( 'MonsterInsights_Admin_Custom_Dimensions' ) ) {
			foreach ( $data['data'] as $key => $dimension_data ) {
				if ( intval( $key ) > 0 ) {
					foreach ( $data['data'][ $key ]['data'] as $dimension_key => $dimension_info ) {
						$title = $dimension_info['label'];
						if ( 'true' === $title ) {
							$title = __( 'Logged-In', 'monsterinsights-dimensions' );
						} else if ( $title === 'false' ) {
							$title = __( 'Logged-Out', 'monsterinsights-dimensions' );
						}
						if ( is_string( $title ) &&
						     preg_match( '/^' .
						                 '(\d{4})-(\d{2})-(\d{2})T' . // YYYY-MM-DDT ex: 2014-01-01T.
						                 '(\d{2}):(\d{2}):(\d{2})' .  // HH-MM-SS  ex: 17:00:00.
						                 '(Z|((-|\+)\d{2}:\d{2}))' .  // Z or +01:00 or -01:00.
						                 '$/', $title, $parts ) ) {
							$title = date( 'l, F jS, Y \a\t g:ia ', strtotime( $title, current_time( 'timestamp' ) ) );
						}
						$data['data'][ $key ]['data'][ $dimension_key ]['label'] = $title;
					}
				}
			}
			$data['data']['dimensions'] = $this->get_post_type_dimension_id();
		}

		return $data;
	}

	/**
	 * Add link to settings for dimensions error when no dimensions are set.
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	public function add_dimensions_settings_link( $data ) {
		if ( current_user_can( 'monsterinsights_save_settings' ) ) {
			$data['data']['footer'] = sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=monsterinsights_settings#/conversions' ), esc_html__( 'Update dimensions settings', 'ga-premium' ) );
		}

		return $data;
	}
}
