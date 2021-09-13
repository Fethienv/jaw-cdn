<?php
namespace AIOSEO\Plugin\Pro\Utils;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use AIOSEO\Plugin\Common\Utils as CommonUtils;

/**
 * Contains helper methods specific to the addons.
 *
 * @since 4.0.0
 */
class Addons extends CommonUtils\Addons {
	/**
	 * The licensing URL.
	 *
	 * @since 4.0.13
	 *
	 * @var string
	 */
	protected $licensingUrl = 'https://licensing.aioseo.com/v1/';

	/**
	 * Returns our addons.
	 *
	 * @since 4.0.0
	 *
	 * @param  boolean $flushCache Whether or not to flush the cache.
	 * @return array               An array of addon data.
	 */
	public function getAddons( $flushCache = false ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';

		$addons = aioseo()->transients->get( 'addons' );
		

		// The API request will tell us if we can activate a plugin, but let's check if its already active.
		$installedPlugins = array_keys( get_plugins() );
		foreach ( $addons as $key => $addon ) {
			$addons[ $key ]->basename    = $this->getAddonBasename( $addon->sku );
			$addons[ $key ]->installed   = in_array( $this->getAddonBasename( $addon->sku ), $installedPlugins, true );
			$addons[ $key ]->isActive    = is_plugin_active( $addons[ $key ]->basename );
			$addons[ $key ]->canInstall  = $this->canInstall();
			$addons[ $key ]->canActivate = $this->canActivate();
			$addons[ $key ]->capability  = $this->getManageCapability( $addon->sku );
		}

		return $addons;
	}

	/**
	 * Gets the payload to send in the request.
	 *
	 * @since 4.1.0
	 *
	 * @param  string $sku The sku to use in the request.
	 * @return array       A payload array.
	 */
	protected function getAddonPayload( $sku = 'all-in-one-seo-pack-pro' ) {
		$payload            = parent::getAddonPayload( $sku );
		$payload['license'] = aioseo()->options->general->licenseKey;
		return $payload;
	}

	/**
	 * Check to see if there are unlicensed addons installed and activated.
	 *
	 * @since 4.1.3
	 *
	 * @return boolean True if there are unlicensed addons, false if not.
	 */
	public function unlicensedAddons() {
		$unlicensed = [
			'addons'  => [],
			'message' => ''
		];

		$addons = $this->getAddons();
		foreach ( $addons as $addon ) {
			if ( ! $addon->isActive ) {
				continue;
			}

			if ( aioseo()->license->isExpired() ) {
				$message = sprintf(
					// Translators: 1 - Opening HTML link tag, 2 - Closing HTML link tag.
					__( 'The following addons cannot be used, because your plan has expired. To renew your subscription, please %1$svisit our website%2$s.', 'aioseo-pro' ),
					'<a target="_blank" href="' . aioseo()->helpers->utmUrl( AIOSEO_MARKETING_URL . 'account/subscriptions/', $addon->name, 'notifications-fail-plan-expired' ) . '">', // phpcs:ignore WordPress.Security.EscapeOutput, Generic.Files.LineLength.MaxExceeded
					'</a>'
				);

				$unlicensed['addons'][] = $addon;
				$unlicensed['message']  = $message;
				continue;
			}

			if ( aioseo()->license->isInvalid() || aioseo()->license->isDisabled() ) {
				$message = sprintf(
					// Translators: 1 - "All in One SEO", 2 - Opening HTML link tag, 3 - Closing HTML link tag.
					__( 'The following addons cannot be used, because they require an active license for %1$s. Your license is missing or has expired. To verify your subscription, please %2$svisit our website%3$s.', 'aioseo-pro' ), // phpcs:ignore Generic.Files.LineLength.MaxExceeded
					esc_html( AIOSEO_PLUGIN_NAME ),
					'<a target="_blank" href="' . aioseo()->helpers->utmUrl( AIOSEO_MARKETING_URL . 'account/', $addon->name, 'notifications-fail-valid-license' ) . '">', // phpcs:ignore WordPress.Security.EscapeOutput, Generic.Files.LineLength.MaxExceeded
					'</a>'
				);

				$unlicensed['addons'][] = $addon;
				$unlicensed['message']  = $message;
				continue;
			}

			if ( ! aioseo()->license->isAddonAllowed( $addon->sku ) ) {
				$level   = aioseo()->internalOptions->internal->license->level;
				$level   = empty( $level ) ? __( 'Unlicensed', 'aioseo-pro' ) : $level;
				$message = sprintf(
					// Translators: 1 - The current plan name, 2 - Opening HTML link tag, 3 - Closing HTML link tag.
					__( 'The following addons cannot be used, because your plan level %1$s does not include access to these addons. To upgrade your subscription, please %2$svisit our website%3$s.', 'all-in-one-seo-pack' ), // phpcs:ignore Generic.Files.LineLength.MaxExceeded
					'<strong>(' . wp_kses_post( ucfirst( $level ) ) . ')</strong>',
					'<a target="_blank" href="' . aioseo()->helpers->utmUrl( AIOSEO_MARKETING_URL . 'pro-upgrade/', $addon->name, 'notifications-fail-plan-level' ) . '">', // phpcs:ignore WordPress.Security.EscapeOutput, Generic.Files.LineLength.MaxExceeded
					'</a>'
				);

				$unlicensed['addons'][] = $addon;
				$unlicensed['message']  = $message;
			}
		}

		return $unlicensed;
	}
}