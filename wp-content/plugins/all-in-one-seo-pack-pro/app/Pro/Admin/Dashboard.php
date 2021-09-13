<?php
namespace AIOSEO\Plugin\Pro\Admin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use AIOSEO\Plugin\Common\Admin as CommonAdmin;

/**
 * Class that holds our dashboard widget.
 *
 * @since 4.0.0
 */
class Dashboard extends CommonAdmin\Dashboard {
	/**
	 * Whether or not to show the widget.
	 *
	 * @since 4.0.0
	 *
	 * @return boolean True if yes, false otherwise.
	 */
	protected function showWidget() {
		if ( ! aioseo()->license->isActive() ) {
			return true;
		}

		if ( false === apply_filters( 'aioseo_show_seo_news', true ) ) {
			return false;
		}

		// Check if the option is disabled.
		if ( ! aioseo()->options->advanced->dashboardWidget ) {
			return false;
		}

		return true;
	}
}