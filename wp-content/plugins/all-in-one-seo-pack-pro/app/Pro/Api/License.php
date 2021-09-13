<?php
namespace AIOSEO\Plugin\Pro\Api;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use AIOSEO\Plugin\Common\Models;

/**
 * Route class for the API.
 *
 * @since 4.0.0
 */
class License {
	/**
	 * Activate the license key.
	 *
	 * @since 4.0.0
	 *
	 * @param  \WP_REST_Request  $request The REST Request
	 * @return \WP_REST_Response          The response.
	 */
	public static function activateLicense( $request ) {
		$body         = $request->get_json_params();
		$responseCode = 200;
		$response     = [
			'success' => true
		];

		// Save the license key.
		aioseo()->options->general->licenseKey = ! empty( $body['licenseKey'] ) ? sanitize_text_field( $body['licenseKey'] ) : null;

		// Check if it validates.
		$validated = aioseo()->license->activate();

		$license = [
			'isActive'   => aioseo()->license->isActive(),
			'isExpired'  => aioseo()->license->isExpired(),
			'isDisabled' => aioseo()->license->isDisabled(),
			'isInvalid'  => aioseo()->license->isInvalid(),
			'expires'    => aioseo()->internalOptions->internal->license->expires
		];

		if ( $validated ) {
			// Force WordPress to check for updates.
			delete_site_option( '_site_transient_update_plugins' );
			aioseo()->transients->delete( 'addons' );

			$response['licenseData'] = aioseo()->internalOptions->internal->license->all();
			$response['license']     = $license;

			$addons = aioseo()->addons->getAddons( true );
			foreach ( $addons as $addon ) {
				aioseo()->addons->getAddon( $addon->sku, true );
			}
		}

		// If it does not validate, update the response to be an error.
		if ( ! $validated ) {
			aioseo()->options->general->licenseKey = null;
			$responseCode = 400;
			$response     = [
				'error'       => true,
				'licenseData' => aioseo()->internalOptions->internal->license->all(),
				'license'     => $license
			];
		}

		aioseo()->notices->init();
		$response['notifications'] = Models\Notification::getNotifications();

		return new \WP_REST_Response( $response, $responseCode );
	}

	/**
	 * Deactivate the license key.
	 *
	 * @since 4.0.0
	 *
	 * @param  \WP_REST_Request  $request The REST Request
	 * @return \WP_REST_Response          The response.
	 */
	public static function deactivateLicense() {
		// Check if it validates.
		$deactivated = aioseo()->license->deactivate();

		if ( $deactivated ) {
			// Force WordPress to check for updates.
			delete_site_option( '_site_transient_update_plugins' );
			aioseo()->transients->delete( 'addons' );

			// Remove the saved license key.
			aioseo()->options->general->licenseKey = null;
			aioseo()->internalOptions->internal->license->reset(
				[
					'expires',
					'expired',
					'invalid',
					'disabled',
					'activationsError',
					'connectionError',
					'requestError',
					'level',
					'addons'
				]
			);

			$license = [
				'isActive'   => aioseo()->license->isActive(),
				'isExpired'  => aioseo()->license->isExpired(),
				'isDisabled' => aioseo()->license->isDisabled(),
				'isInvalid'  => aioseo()->license->isInvalid(),
				'expires'    => aioseo()->internalOptions->internal->license->expires
			];

			$responseCode = 200;
			$response     = [
				'success'     => true,
				'licenseData' => aioseo()->internalOptions->internal->license->all(),
				'license'     => $license
			];
		}

		// If it does not validate, update the response to be an error.
		if ( ! $deactivated ) {
			aioseo()->options->general->licenseKey = null;

			$license = [
				'isActive'   => aioseo()->license->isActive(),
				'isExpired'  => aioseo()->license->isExpired(),
				'isDisabled' => aioseo()->license->isDisabled(),
				'isInvalid'  => aioseo()->license->isInvalid(),
				'expires'    => aioseo()->internalOptions->internal->license->expires
			];

			$responseCode = 200;
			$response     = [
				'error'       => true,
				'licenseData' => aioseo()->internalOptions->internal->license->all(),
				'license'     => $license
			];
		}

		aioseo()->options->general->licenseKey = null;

		aioseo()->notices->init();
		$response['notifications'] = Models\Notification::getNotifications();

		return new \WP_REST_Response( $response, $responseCode );
	}
}