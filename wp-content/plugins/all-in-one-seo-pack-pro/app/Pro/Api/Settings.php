<?php
namespace AIOSEO\Plugin\Pro\Api;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use AIOSEO\Plugin\Common\Api as CommonApi;
use AIOSEO\Plugin\Pro\Models;

/**
 * Route class for the API.
 *
 * @since 4.0.0
 */
class Settings extends CommonApi\Settings {
	/**
	 * Save options from the front end.
	 *
	 * @since 4.1.0
	 *
	 * @param  \WP_REST_Request  $request The REST Request
	 * @return \WP_REST_Response          The response.
	 */
	public static function saveChanges( $request ) {
		$response = parent::saveChanges( $request );

		Api::addonsApi( $request, null, '\\Api\\Settings', 'saveChanges' );

		return $response;
	}

	/**
	 * Imports settings.
	 *
	 * @since 4.0.0
	 *
	 * @param  \WP_REST_Request  $request The REST Request
	 * @return \WP_REST_Response          The response.
	 */
	public static function importSettings( $request ) {
		$response = parent::importSettings( $request );
		$file     = $request->get_file_params()['file'];
		$wpfs     = aioseo()->helpers->wpfs();
		$contents = @$wpfs->get_contents( $file['tmp_name'] );
		if ( ! empty( $file['type'] ) && 'application/json' === $file['type'] ) {
			// Since this could be any file, we need to pretend like every variable here is missing.
			$contents = json_decode( $contents, true );
			if ( empty( $contents ) ) {
				return new \WP_REST_Response( [
					'success' => false
				], 400 );
			}

			if ( ! empty( $contents['postOptions'] ) ) {
				$notAllowedFields = aioseo()->access->getNotAllowedPageFields();
				foreach ( $contents['postOptions'] as $postType => $postData ) {
					// Terms.
					if ( ! empty( $postData['terms'] ) ) {
						foreach ( $postData['terms'] as $term ) {
							unset( $term['id'] );
							// Clean up the array removing fields the user should not manage.
							$term    = array_diff_key( $term, $notAllowedFields );
							$theTerm = Models\Term::getTerm( $term['term_id'] );
							$theTerm->set( $term );
							$theTerm->save();
						}
					}
				}
			}
		}

		$response->data['license'] = [
			'isActive'   => aioseo()->license->isActive(),
			'isExpired'  => aioseo()->license->isExpired(),
			'isDisabled' => aioseo()->license->isDisabled(),
			'isInvalid'  => aioseo()->license->isInvalid(),
			'expires'    => aioseo()->internalOptions->internal->license->expires
		];

		return Api::addonsApi( $request, $response, '\\Api\\Settings', 'importSettings' );
	}

	/**
	 * Export settings.
	 *
	 * @since 4.0.6
	 *
	 * @param  \WP_REST_Request  $request The REST Request
	 * @return \WP_REST_Response          The response.
	 */
	public static function exportSettings( $request ) {
		$response    = parent::exportSettings( $request );
		$body        = $request->get_json_params();
		$postOptions = ! empty( $body['postOptions'] ) ? $body['postOptions'] : [];

		if ( ! empty( $postOptions ) ) {
			$notAllowedFields = aioseo()->access->getNotAllowedPageFields();
			foreach ( $postOptions as $postType ) {
				$taxonomies = get_object_taxonomies( $postType );
				$terms      = aioseo()->db->start( 'aioseo_terms as at' )
					->select( 'at.*' )
					->join( 'term_taxonomy as tt', 'tt.term_id = at.term_id' )
					->whereIn( 'tt.taxonomy', $taxonomies )
					->run()
					->result();

				foreach ( $terms as $term ) {
					// Clean up the array removing fields the user should not manage.
					$term = array_diff_key( (array) $term, $notAllowedFields );
					if ( count( $term ) > 2 ) {
						$response->data['settings']['postOptions'][ $postType ]['terms'][] = $term;
					}
				}
			}
		}

		return Api::addonsApi( $request, $response, '\\Api\\Settings', 'exportSettings' );
	}

	/**
	 * Reset settings.
	 *
	 * @since 4.1.0
	 *
	 * @param  \WP_REST_Request  $request The REST Request
	 * @return \WP_REST_Response The response.
	 */
	public static function resetSettings( $request ) {
		$response = parent::resetSettings( $request );

		return Api::addonsApi( $request, $response, '\\Api\\Settings', 'resetSettings' );
	}
}