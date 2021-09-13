<?php
/**
 * Forms Report
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

final class MonsterInsights_Report_Forms extends MonsterInsights_Report {

	public $title;
	public $class = 'MonsterInsights_Report_Forms';
	public $name = 'forms';
	public $version = '1.0.0';
	public $level = 'pro';

	/**
	 * Primary class constructor.
	 *
	 * @access public
	 * @since 6.0.0
	 */
	public function __construct() {
		$this->title = __( 'Forms', 'ga-premium' );
		parent::__construct();
	}

	public function requirements( $error = false, $args = array(), $name = '' ) {
		if ( ! empty( $error ) || $name !== 'forms' ) {
			return $error;
		}

		if ( ! class_exists( 'MonsterInsights_Forms' ) ) {
			add_filter( 'monsterinsights_reports_handle_error_message', array( $this, 'add_error_addon_link' ) );

			// Translators: %s will be the action (install/activate) which will be filled depending on the addon state.
			$text = __( 'Please %s the MonsterInsights Forms addon to view custom dimensions reports.', 'ga-premium' );

			if ( monsterinsights_can_install_plugins() ) {
				return $text;
			} else {
				return sprintf( $text, __( 'install', 'ga-premium' ) );
			}
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
		// Add form titles.
		if ( ! empty( $data['data']['forms'] ) ) {
			foreach ( $data['data']['forms'] as $key => $formdata ) {
				$title = $formdata['id'];
				// WPForms.
				if ( function_exists( 'wpforms' ) ) {
					if ( 0 === strpos( $title, 'wpforms-submit-' ) || 0 === strpos( $title, 'wpforms-form-' ) ) {
						$title = str_replace( 'wpforms-submit-', '', $title );
						$title = str_replace( 'wpforms-form-', '', $title );
						$title = get_the_title( $title );
					}
				}

				// Gravity Forms.
				if ( class_exists( 'GFAPI' ) ) {
					if ( 0 === strpos( $title, 'gform_' ) ) {
						$title = str_replace( 'gform_', '', $title );
						$title = GFAPI::get_form( $title );
						$title = $title['title'];
					}
				}

				// Contact Form 7.
				if ( defined( 'WPCF7_VERSION' ) ) {
					if ( 0 === strpos( $title, 'wpcf7-f' ) ) {
						$title = str_replace( 'wpcf7-f', '', $title );
						$title = get_the_title( $title ); // Example: wpcf7-f1203.
					}
				}

				// Formidable Forms.
				if ( class_exists( 'FrmForm' ) ) {
					if ( 0 === strpos( $title, 'form_' ) ) {
						$form_key = str_replace( 'form_', '', $title );
						$id       = FrmForm::get_id_by_key( $form_key );
						if ( $id > 0 ) {
							$form = FrmForm::getOne( $id );
							if ( ! empty( $form->name ) ) {
								$title = $form->name;
							}
						}
					}
				}
				if ( empty( $title ) ) {
					$title = $formdata['id'];
				}
				$data['data']['forms'][ $key ]['id'] = $title;
			}
		}

		// Add GA links.
		if ( ! empty( $data['data'] ) ) {
			$data['data']['galinks'] = array(
				'forms' => 'https://analytics.google.com/analytics/web/#report/content-event-events/' . MonsterInsights()->auth->get_referral_url() . $this->get_ga_report_range( $data['data'] ) . '%3Fexplorer-table.plotKeys%3D%5B%5D%26_r.drilldown%3Danalytics.eventCategory%3Aform/',
			);
		}

		return $data;
	}
}
