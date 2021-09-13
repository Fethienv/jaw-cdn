<?php
/**
 * Dimensions Report
 *
 * Ensures all of the reports have a uniform class with helper functions.
 *
 * @since 7.0.0
 *
 * @package MonsterInsights
 * @subpackage Reports
 * @author  Chris Christoff
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class MonsterInsights_Report_Dimensions extends MonsterInsights_Report {

	public $title;
	public $class = 'MonsterInsights_Report_Dimensions';
	public $name = 'dimensions';
	public $version = '1.0.0';
	public $level = 'pro';

	/**
	 * Primary class constructor.
	 *
	 * @access public
	 * @since 6.0.0
	 */
	public function __construct() {
		$this->title = __( 'Dimensions', 'ga-premium' );
		parent::__construct();
	}

	public function requirements( $error = false, $args = array(), $name = '' ) {
		if ( ! empty( $error ) || $name !== 'dimensions' ) {
			return $error;
		}

		if ( ! class_exists( 'MonsterInsights_Admin_Custom_Dimensions' ) ) {
			add_filter( 'monsterinsights_reports_handle_error_message', array( $this, 'add_error_addon_link' ) );

			// Translators: %s will be the action (install/activate) which will be filled depending on the addon state.
			$text = __( 'Please %s the MonsterInsights Dimensions addon to view custom dimensions reports.', 'ga-premium' );

			if ( monsterinsights_can_install_plugins() ) {
				return $text;
			} else {
				return sprintf( $text, __( 'install', 'ga-premium' ) );
			}
		}

		$dimensions = $this->get_active_enabled_custom_dimensions();
		if ( empty( $dimensions ) || ! is_array( $dimensions ) ) {
			add_filter( 'monsterinsights_reports_handle_error_message', array(
				$this,
				'add_dimensions_settings_link'
			) );

			return __( 'Please enable at least 1 dimension to use this report', 'ga-premium' );
		}

		return $error;
	}

	public function additional_data() {
		$dimensions = $this->get_active_enabled_custom_dimensions();

		return array( 'count' => count( $dimensions ) );
	}

	/**
	 * Get the custom dimensions for the dashboard
	 *
	 * @return array
	 */
	public function get_active_enabled_custom_dimensions() {
		$dimensions                = array();
		$custom_dimensions         = new MonsterInsights_Admin_Custom_Dimensions();
		$default_custom_dimensions = $custom_dimensions->custom_dimensions();
		$custom_dimension_option   = monsterinsights_get_option( 'custom_dimensions', array() );

		foreach ( $custom_dimension_option as $dimension ) {
			$dimensions[ $dimension['id'] ] = array(
				'id'      => $dimension['id'],
				'key'     => $dimension['type'],
				'name'    => $default_custom_dimensions[ $dimension['type'] ]['title'],
				'label'   => $default_custom_dimensions[ $dimension['type'] ]['label'],
				'enabled' => $default_custom_dimensions[ $dimension['type'] ]['enabled'],
				'metric'  => $default_custom_dimensions[ $dimension['type'] ]['metric'],
			);
		}

		return $dimensions;
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
			$custom_dimensions_option = monsterinsights_get_option( 'custom_dimensions', array() );
			$user_id_dimension_index  = 0;

			foreach ( $custom_dimensions_option as $dimension ) {
				if ( isset( $dimension['type'] ) && 'user_id' === $dimension['type'] ) {
					$user_id_dimension_index = $dimension['id'];
				}
			}

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

						if ( intval( $user_id_dimension_index ) === intval( $key ) ) {
							$user_id  = $title;
							$user     = get_user_by( 'id', $user_id );
							$username = isset( $user->user_login ) ? $user->user_login : $user_id;
							$title    = apply_filters( 'monsterinsights_dimensions_user_id_display', $username, $user_id );
						}

						$data['data'][ $key ]['data'][ $dimension_key ]['label'] = $title;
					}
				}
			}
			$data['data']['dimensions'] = $this->get_active_enabled_custom_dimensions();
		}

		// Add GA links.
		if ( ! empty( $data['data'] ) ) {
			$data['data']['galinks'] = array();
			foreach ( $data['data']['dimensions'] as $key => $dimension_data ) {
				$data['data']['galinks'][ $key ] = 'https://analytics.google.com/analytics/web/#/report/visitors-custom-variables/' . MonsterInsights()->auth->get_referral_url() . $this->get_ga_report_range( $data['data'] ) . rawurlencode( '&explorer-segmentExplorer.segmentId=analytics.customVarName' . $key . '&explorer-table.plotKeys=[]/' );
			}
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
			$data['data']['footer'] = sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=monsterinsights_settings#/conversions' ), esc_html__( 'View dimensions settings', 'ga-premium' ) );
		}

		return $data;
	}
}
