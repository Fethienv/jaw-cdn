<?php

/**
 * Site-Speed Report - Desktop
 *
 * Ensures all of the reports have a uniform class with helper functions.
 *
 * @since 7.13.2
 *
 * @package MonsterInsights
 * @subpackage Reports
 * @author  Chris Christoff
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Class to handle site speed functionality for Desktop
 *
 * @since 7.13.2
 */
final class MonsterInsights_Report_SiteSpeed extends MonsterInsights_Report
{

	public $title;
	public $class = 'MonsterInsights_Report_SiteSpeed';
	public $name = 'sitespeed';
	public $version = '1.0.0';
	public $level = 'plus';

	/**
	 * Primary class constructor.
	 *
	 * @access public
	 * @since 6.0.0
	 */
	public function __construct()
	{
		$this->title = __('Site-Speed', 'ga-premium');
		add_action('wp_ajax_site_speed_run_audit', array($this, 'run_audit'));
		parent::__construct();
	}

	public function prepare_report_data($data)
	{
		return $data;
	}

	/**
	 * Run the audit for Site Speed.
	 *
	 * This function is hooked to a ajax call and clears the cache for
	 * site speed report on the plugin side.
	 */
	public function run_audit()
	{

		check_ajax_referer('mi-admin-nonce', 'nonce');

		$deleted = delete_option('monsterinsights_report_data_sitespeed');

		if ($deleted) {
			wp_send_json_success();
		} else {
			wp_send_json_error();
		}
	}
}
