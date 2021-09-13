<?php
/**
 * Real-Time Report
 *
 * Ensures all of the reports have a uniform class with helper functions.
 *
 * @since 7.2.0
 *
 * @package MonsterInsights
 * @subpackage Reports
 * @author  Chris Christoff
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class MonsterInsights_Report_RealTime extends MonsterInsights_Report {

	public $title;
	public $class = 'MonsterInsights_Report_RealTime';
	public $name = 'realtime';
	public $version = '1.0.0';
	public $level = 'plus';

	/**
	 * Primary class constructor.
	 *
	 * @access public
	 * @since 6.0.0
	 */
	public function __construct() {
		$this->title = __( 'Real-Time', 'ga-premium' );
		add_filter( 'monsterinsights_report_transient_expiration', array( $this, 'sixty_seconds' ), 10, 2 );
		add_filter( 'monsterinsights_report_use_cache', array( $this, 'use_cache' ), 10, 2 );
		parent::__construct();
	}

	public function sixty_seconds( $length, $name ) {
		return $this->name === $name ? 60 : $length;
	}

	public function use_cache( $use_cache, $name ) {
		return $this->name === $name ? true : $use_cache;
	}

	/**
	 * Prepare report-specific data for output.
	 *
	 * @param array $data The data from the report before it gets sent to the frontend.
	 *
	 * @return mixed
	 */
	public function prepare_report_data( $data ) {
		if ( ! empty( $data['data']['realtime'] ) ) {
			foreach ( $data['data']['realtime']['pageviewsovertime']['labels'] as $key => $label ) {
				// Translators: Number of minutes passed since the view.
				$data['data']['realtime']['pageviewsovertime']['labels'][ $key ] = sprintf( esc_html__( '%s minutes ago', 'ga-premium' ), $label );
			}

			// Add countries data.
			$countries            = array_flip( monsterinsights_get_country_list( false ) );
			$countries_translated = monsterinsights_get_country_list( true );

			foreach ( $data['data']['realtime']['countries'] as $country_key => $country_data ) {
				if ( ! empty( $countries[ $country_data['country'] ] ) ) {
					$data['data']['realtime']['countries'][ $country_key ]['iso']  = $countries[ $country_data['country'] ];
					$data['data']['realtime']['countries'][ $country_key ]['name'] = $countries_translated[ $countries[ $country_data['country'] ] ];
				} else {
					$data['data']['realtime']['countries'][ $country_key ]['iso']  = '';
					$data['data']['realtime']['countries'][ $country_key ]['name'] = $country_data['country'];
				}
			}
			foreach ( $data['data']['realtime']['cities'] as $city_key => $city_data ) {
				if ( ! empty( $countries[ $city_data['country'] ] ) ) {
					$data['data']['realtime']['cities'][ $city_key ]['iso']  = $countries[ $city_data['country'] ];
					$data['data']['realtime']['cities'][ $city_key ]['name'] = $countries_translated[ $countries[ $city_data['country'] ] ];
				} else {
					$data['data']['realtime']['cities'][ $city_key ]['iso']  = '';
					$data['data']['realtime']['cities'][ $city_key ]['name'] = $city_data['country'];
				}
			}
		}

		// Add GA links.
		if ( ! empty( $data['data'] ) ) {
			$data['data']['galinks'] = array(
				'toppages'  => 'https://analytics.google.com/analytics/web/#/realtime/rt-content/' . MonsterInsights()->auth->get_referral_url(),
				'referrals' => 'https://analytics.google.com/analytics/web/#/realtime/rt-traffic/' . MonsterInsights()->auth->get_referral_url(),
				'countries' => 'https://analytics.google.com/analytics/web/#/realtime/rt-location/' . MonsterInsights()->auth->get_referral_url(),
				'city'      => 'https://analytics.google.com/analytics/web/#/realtime/rt-location/' . MonsterInsights()->auth->get_referral_url(),
			);
		}

		return $data;
	}

	public function default_end_date() {
		return date( 'Y-m-d' );
	}
}
