<?php
namespace AIOSEO\Plugin\Pro\Main;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use AIOSEO\Plugin\Common\Main as CommonMain;

/**
 * Activate class with methods that are called.
 *
 * @since 4.0.0
 */
class Activate extends CommonMain\Activate {
	/**
	 * Runs on activate.
	 *
	 * @since 4.0.0
	 *
	 * @param  bool $networkWide Whether or not this is a network wide activation.
	 * @return void
	 */
	public function activate( $networkWide ) {
		if ( is_multisite() && $networkWide ) {
			global $wpdb;
			foreach ( $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" ) as $blogId ) {
				switch_to_blog( $blogId );

				aioseo()->access->addCapabilities();

				restore_current_blog();
			}
		}

		parent::activate( $networkWide );
	}
}