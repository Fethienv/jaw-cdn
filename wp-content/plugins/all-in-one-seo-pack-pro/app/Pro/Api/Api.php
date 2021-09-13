<?php
namespace AIOSEO\Plugin\Pro\Api;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use AIOSEO\Plugin\Common\Api as CommonApi;

/**
 * Api class for the admin.
 *
 * @since 4.0.0
 */
class Api extends CommonApi\Api {
	/**
	 * The routes we use in the rest API.
	 *
	 * @since 4.0.0
	 *
	 * @var array
	 */
	protected $proRoutes = [
		// phpcs:disable WordPress.Arrays.ArrayDeclarationSpacing.AssociativeArrayFound
		'POST' => [
			'activate'                                                  => [ 'callback' => [ 'License', 'activateLicense' ] ],
			'deactivate'                                                => [ 'callback' => [ 'License', 'deactivateLicense' ] ],
			'notification/local-business-organization-reminder'         => [ 'callback' => [ 'Notifications', 'localBusinessOrganizationReminder' ] ],
			'notification/news-publication-name-reminder'               => [ 'callback' => [ 'Notifications', 'newsPublicationNameReminder' ] ],
			'notification/v3-migration-local-business-number-reminder'  => [ 'callback' => [ 'Notifications', 'migrationLocalBusinessNumberReminder' ] ],
			'notification/v3-migration-local-business-country-reminder' => [ 'callback' => [ 'Notifications', 'migrationLocalBusinessCountryReminder' ] ],
			'notification/import-local-business-country-reminder'       => [ 'callback' => [ 'Notifications', 'importLocalBusinessCountryReminder' ] ],
			'notification/import-local-business-type-reminder'          => [ 'callback' => [ 'Notifications', 'importLocalBusinessTypeReminder' ] ],
			'notification/import-local-business-number-reminder'        => [ 'callback' => [ 'Notifications', 'importLocalBusinessNumberReminder' ] ],
			'notification/import-local-business-fax-reminder'           => [ 'callback' => [ 'Notifications', 'importLocalBusinessFaxReminder' ] ],
			'notification/import-local-business-currencies-reminder'    => [ 'callback' => [ 'Notifications', 'importLocalBusinessCurrenciesReminder' ] ]
		]
		// phpcs:enable WordPress.Arrays.ArrayDeclarationSpacing.AssociativeArrayFound
	];

	/**
	 * Get all the routes to register.
	 *
	 * @since 4.0.0
	 *
	 * @return array An array of routes.
	 */
	protected function getRoutes() {
		$routes       = array_merge_recursive( parent::getRoutes(), $this->proRoutes );
		$loadedAddons = aioseo()->addons->getLoadedAddons();
		if ( ! empty( $loadedAddons ) ) {
			foreach ( $loadedAddons as $addon ) {
				if ( isset( $addon->api ) && method_exists( $addon->api, 'getRoutes' ) ) {
					$routes = array_replace_recursive(
						$addon->api->getRoutes(),
						$routes
					);
				}
			}
		}

		return $routes;
	}

	/**
	 * Pass any API class back to an addon to run a similar method.
	 *
	 * @since 4.1.0
	 *
	 * @param  \WP_Request  $request   The original request.
	 * @param  \WP_Response $response  An optional response.
	 * @param  string       $apiClass  The class to call.
	 * @param  string       $apiMethod The method to call on the class.
	 * @return mixed                  Anything the addon needs to return.
	 */
	public static function addonsApi( $request, $response, $apiClass, $apiMethod ) {
		$loadedAddons = aioseo()->addons->getLoadedAddons();
		if ( ! empty( $loadedAddons ) ) {
			foreach ( $loadedAddons as $addon ) {
				$class        = new \ReflectionClass( $addon );
				$addonClass   = $class->getNamespaceName() . $apiClass;
				$classExists  = class_exists( $addonClass );
				$methodExists = method_exists( $addonClass, $apiMethod );
				if ( $classExists && $methodExists ) {
					$response = call_user_func_array( [ $addonClass, $apiMethod ], [ $request, $response ] );
				}
			}
		}

		return $response;
	}
}