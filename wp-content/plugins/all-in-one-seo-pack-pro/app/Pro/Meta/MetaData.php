<?php

namespace AIOSEO\Plugin\Pro\Meta;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use AIOSEO\Plugin\Common\Meta as CommonMeta;
use AIOSEO\Plugin\Pro\Models;

/**
 * Handles fetching metadata for the current object.
 *
 * @since 4.0.0
 */
class MetaData extends CommonMeta\MetaData {
	/**
	 * Returns the metadata for the current object.
	 *
	 * @since 4.0.0
	 *
	 * @param  WP_Term $term The term object (optional).
	 * @return array         The meta data.
	 */
	public function getMetaData( $term = null ) {
		static $terms = [];
		if (
			is_category() ||
			is_tag() ||
			is_tax() ||
			( is_admin() && function_exists( 'get_current_screen' ) && 'term' === get_current_screen()->base )
		) {
			$termId = is_object( $term ) ? $term->term_id : get_queried_object()->term_id;
			if ( empty( $termId ) ) {
				return parent::getMetaData( $term );
			}

			if ( isset( $terms[ $termId ] ) ) {
				return $terms[ $termId ];
			}
			$terms[ $termId ] = Models\Term::getTerm( $termId );

			if ( ! $terms[ $termId ]->exists() ) {
				$migratedMeta = aioseo()->migration->meta->getMigratedTermMeta( $termId );
				if ( ! empty( $migratedMeta ) ) {
					foreach ( $migratedMeta as $k => $v ) {
						$terms[ $termId ]->{$k} = $v;
					}

					$terms[ $termId ]->save();
				}
			}

			return $terms[ $termId ];
		}

		return parent::getMetaData( $term );
	}
}