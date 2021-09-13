<?php
namespace AIOSEO\Plugin\Pro\Social;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use AIOSEO\Plugin\Common\Social as CommonSocial;

/**
 * Handles our social meta.
 *
 * @since 4.0.0
 */
class Social extends CommonSocial\Social {
	/**
	 * Class constructor.
	 *
	 * @since 4.0.0
	 */
	public function __construct() {
		parent::__construct();

		$this->image = new Image();

		if ( wp_doing_ajax() || wp_doing_cron() ) {
			return;
		}

		$this->facebook = new Facebook();
		$this->twitter  = new Twitter();
		$this->output   = new Output();
	}
}