<?php
/**
 * eCommerce Report
 *
 * Ensures all of the reports have a uniform class with helper functions.
 *
 * @since 6.0.0
 *
 * @package MonsterInsights
 * @subpackage Reports
 * @author  Chris Christoff
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class MonsterInsights_Report_eCommerce extends MonsterInsights_Report {

	public $title;
	public $class = 'MonsterInsights_Report_eCommerce';
	public $name = 'ecommerce';
	public $version = '1.0.0';
	public $level = 'pro';

	/**
	 * Primary class constructor.
	 *
	 * @access public
	 * @since 6.0.0
	 */
	public function __construct() {
		$this->title = __( 'eCommerce', 'ga-premium' );
		parent::__construct();
	}

	public function requirements( $error = false, $args = array(), $name = '' ) {
		if ( ! empty( $error ) || $name !== $this->name ) {
			return $error;
		}

		if ( ! class_exists( 'MonsterInsights_eCommerce' ) ) {
			add_filter( 'monsterinsights_reports_handle_error_message', array( $this, 'add_error_addon_link' ) );

			// Translators: %s will be the action (install/activate) which will be filled depending on the addon state.
			$text = __( 'Please %s the MonsterInsights eCommerce addon to view custom dimensions reports.', 'ga-premium' );

			if ( monsterinsights_can_install_plugins() ) {
				return $text;
			} else {
				return sprintf( $text, __( 'install', 'ga-premium' ) );
			}
		}

		$enhanced_commerce = (bool) monsterinsights_get_option( 'enhanced_ecommerce', false );

		if ( ! $enhanced_commerce ) {
			add_filter( 'monsterinsights_reports_handle_error_message', array( $this, 'add_ecommerce_settings_link' ) );

			return __( 'Please enable enhanced eCommerce in the MonsterInsights eCommerce settings to use the eCommerce report.', 'ga-premium' );
		}

		return $error;
	}

	/**
	 * Prepare report-specific data for output.
	 *
	 * @param array $data The data from the report before it gets sent to the frontend.
	 *
	 * @return mixed
	 */
	public function prepare_report_data( $data ) {

		// Add GA links.
		if ( ! empty( $data['data'] ) ) {
			$data['data']['galinks'] = array(
				'products'    => 'https://analytics.google.com/analytics/web/#report/conversions-ecommerce-product-performance/' . MonsterInsights()->auth->get_referral_url() . $this->get_ga_report_range( $data['data'] ),
				'conversions' => 'https://analytics.google.com/analytics/web/#report/trafficsources-referrals/' . MonsterInsights()->auth->get_referral_url() . $this->get_ga_report_range( $data['data'] ) . '%3F_u.dateOption%3Dlast7days%26explorer-table-dataTable.sortColumnName%3Danalytics.transactionRevenue%26explorer-table-dataTable.sortDescending%3Dtrue%26explorer-table.plotKeys%3D%5B%5D/',
				'days'        => 'https://analytics.google.com/analytics/web/#report/bf-time-lag/' . MonsterInsights()->auth->get_referral_url() . $this->get_ga_report_range( $data['data'] ),
				'sessions'    => 'https://analytics.google.com/analytics/web/#report/bf-path-length/' . MonsterInsights()->auth->get_referral_url() . $this->get_ga_report_range( $data['data'] ),
			);
		}

		return $data;
	}

	/**
	 * Add link to ecommerce settings to the footer of the disabled enhanced ecommerce notice.
	 *
	 * @param array $data The data being sent back to the Ajax call.
	 *
	 * @return array
	 */
	public function add_ecommerce_settings_link( $data ) {
		$ecommerce_link         = add_query_arg( array( 'page' => 'monsterinsights_settings' ), admin_url( 'admin.php' ) );
		$ecommerce_link        .= '#/ecommerce';
		$data['data']['footer'] = $ecommerce_link;

		return $data;
	}
}
