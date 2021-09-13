<?php
namespace AIOSEO\Plugin\Pro\Main;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use AIOSEO\Plugin\Lite\Main as LiteMain;

/**
 * Filters class with methods that are called.
 *
 * @since 4.0.0
 */
class Filters extends LiteMain\Filters {
	/**
	 * Action links for the plugins page.
	 *
	 * @param  array  $actions    An array of existing actions.
	 * @param  string $pluginFile The plugin file we are modifying.
	 * @return array              An array of action links.
	 */
	public function pluginActionLinks( $actions, $pluginFile ) {
		$actionLinks = parent::pluginActionLinks( $actions, $pluginFile );

		// We don't need a Pro upgrade link here so we can unset it.
		unset( $actionLinks['proupgrade'] );

		return $actionLinks;
	}
}