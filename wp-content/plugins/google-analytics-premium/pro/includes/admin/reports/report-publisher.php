<?php
/**
 * Publisher Report
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

final class MonsterInsights_Report_Publisher extends MonsterInsights_Report {

	public $title;
	public $class = 'MonsterInsights_Report_Publisher';
	public $name = 'publisher';
	public $version = '1.0.0';
	public $level = 'plus';

	/**
	 * Primary class constructor.
	 *
	 * @access public
	 * @since 6.0.0
	 */
	public function __construct() {
		$this->title = __( 'Publishers', 'ga-premium' );
		parent::__construct();
	}

	/**
	 * Prepare report-specific data for output.
	 *
	 * @param array $data The data from the report before it gets sent to the frontend.
	 *
	 * @return mixed
	 */
	public function prepare_report_data( $data ) {

		// Prepare age colors.
		if ( ! empty( $data['data']['age'] ) ) {
			foreach ( $data['data']['age']['graph']['colors'] as $color_key => $color ) {
				$data['data']['age']['graph']['colors'][ $color_key ] = str_replace( '"', '', $color );
			}
			foreach ( $data['data']['age']['graph']['labels'] as $label_key => $label ) {
				$data['data']['age']['graph']['labels'][ $label_key ] = str_replace( '"', '', $label );
			}
		}
		// Prepare gender colors.
		if ( ! empty( $data['data']['gender'] ) ) {
			foreach ( $data['data']['gender']['graph']['colors'] as $color_key => $color ) {
				$data['data']['gender']['graph']['colors'][ $color_key ] = str_replace( '"', '', $color );
			}
			foreach ( $data['data']['gender']['graph']['labels'] as $label_key => $label ) {
				$data['data']['gender']['graph']['labels'][ $label_key ] = str_replace( '"', '', $label );
			}
		}

		// ESC_HTML on outbound links.
		if ( ! empty( $data['data']['outboundlinks'] ) ) {
			foreach ( $data['data']['outboundlinks'] as $link_key => $outboundlink ) {
				if ( ! isset( $data['data']['outboundlinks'][ $link_key ]['title'] ) ) {
					continue;
				}
				$data['data']['outboundlinks'][ $link_key ]['title'] = esc_html( $outboundlink['title'] );
			}
		}

		// ESC_HTML on affiliate links.
		if ( ! empty( $data['data']['affiliatelinks'] ) ) {
			foreach ( $data['data']['affiliatelinks'] as $link_key => $affiliatelink ) {
				if ( ! isset( $data['data']['affiliatelinks'][ $link_key ]['title'] ) ) {
					continue;
				}
				$data['data']['affiliatelinks'][ $link_key ]['title'] = esc_html( $affiliatelink['title'] );
			}
		}

		if ( ! empty( $data['data'] ) ) {
			$data['data']['galinks'] = array(
				'landingpages'   => 'https://analytics.google.com/analytics/web/#report/content-landing-pages/' . MonsterInsights()->auth->get_referral_url() . $this->get_ga_report_range( $data['data'] ),
				'exitpages'      => 'https://analytics.google.com/analytics/web/#report/content-exit-pages/' . MonsterInsights()->auth->get_referral_url() . $this->get_ga_report_range( $data['data'] ),
				'outboundlinks'  => 'https://analytics.google.com/analytics/web/#report/content-event-events/' . MonsterInsights()->auth->get_referral_url() . $this->get_ga_report_range( $data['data'] ) . '%3Fexplorer-table.plotKeys%3D%5B%5D%26_r.drilldown%3Danalytics.eventCategory%3Aoutbound-link/',
				'affiliatelinks' => 'https://analytics.google.com/analytics/web/#report/content-event-events/' . MonsterInsights()->auth->get_referral_url() . $this->get_ga_report_range( $data['data'] ) . '%3Fexplorer-table.advFilter%3D%5B%5B0%2C%22analytics.eventCategory%22%2C%22BW%22%2C%22outbound-link-%22%2C0%5D%5D%26explorer-table.plotKeys%3D%5B%5D/',
				'downloadlinks'  => 'https://analytics.google.com/analytics/web/#report/content-event-events/' . MonsterInsights()->auth->get_referral_url() . $this->get_ga_report_range( $data['data'] ) . '%3Fexplorer-table.plotKeys%3D%5B%5D%26_r.drilldown%3Danalytics.eventCategory%3Adownload/',
				'interest'       => 'https://analytics.google.com/analytics/web/#report/visitors-demographics-interest-others/' . MonsterInsights()->auth->get_referral_url() . $this->get_ga_report_range( $data['data'] ),
			);
		}

		return $data;
	}
}
