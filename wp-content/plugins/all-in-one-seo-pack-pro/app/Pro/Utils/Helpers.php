<?php
namespace AIOSEO\Plugin\Pro\Utils;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use AIOSEO\Plugin\Common\Utils as CommonUtils;
use AIOSEO\Plugin\Pro\Traits\Helpers as TraitHelpers;

/**
 * Contains helper functions.
 *
 * @since 4.0.0
 */
class Helpers extends CommonUtils\Helpers {
	use TraitHelpers\ThirdParty;
	use TraitHelpers\Vue;
}