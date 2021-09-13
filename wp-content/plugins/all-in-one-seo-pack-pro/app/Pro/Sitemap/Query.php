<?php
namespace AIOSEO\Plugin\Pro\Sitemap;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use AIOSEO\Plugin\Common\Sitemap as CommonSitemap;

/**
 * Handles all complex queries for the sitemap.
 *
 * @since 4.0.0
 */
class Query extends CommonSitemap\Query {
	/**
	 * Returns all eligble sitemap entries for a given taxonomy.
	 *
	 * @since 4.0.0
	 *
	 * @param  string $taxonomy       The taxonomy.
	 * @param  array  $additionalArgs Any additional arguments for the term query.
	 * @return array                  The term objects.
	 */
	public function terms( $taxonomy, $additionalArgs = [] ) {
		// Let's just make sure the tables exist. This should never be a problem outside of our dev environments.
		if ( ! aioseo()->db->tableExists( 'aioseo_terms' ) ) {
			aioseo()->updates->addInitialCustomTablesForV4();
		}

		// Set defaults.
		$fields  = '`t`.`term_id`, `at`.`priority`, `at`.`frequency`';
		$offset  = aioseo()->sitemap->offset;

		// Override defaults if passed as additional arg.
		foreach ( $additionalArgs as $name => $value ) {
			$$name = esc_sql( $value );
			if ( 'root' === $name ) {
				$fields = 't.term_id';
			}
		}

		$termRelationshipsTable = aioseo()->db->db->prefix . 'term_relationships';
		$termTaxonomyTable      = aioseo()->db->db->prefix . 'term_taxonomy';
		$query = aioseo()->db
			->start( aioseo()->db->db->terms . ' as t', true )
			->select( $fields )
			->leftJoin( 'aioseo_terms as at', '`at`.`term_id` = `t`.`term_id`' )
			->whereRaw( '( `at`.`robots_noindex` IS NULL OR `at`.`robots_noindex` IS FALSE )' )
			->whereRaw( "
			( `t`.`term_id` IN
				(
					SELECT `tt`.`term_id`
					FROM `$termTaxonomyTable` as tt
					WHERE `tt`.`taxonomy` = '$taxonomy'
					AND `tt`.`count` > 0
				)
			)" );

		$excludedTerms = aioseo()->sitemap->helpers->excludedTerms();
		if ( $excludedTerms ) {
			$query->whereRaw("
				( `t`.`term_id` NOT IN
					(
						SELECT `tr`.`term_taxonomy_id`
						FROM `$termRelationshipsTable` as tr
						WHERE `tr`.`term_taxonomy_id` IN ( $excludedTerms )
					)
				)" );
		}

		if ( ! aioseo()->helpers->isTaxonomyNoindexed( $taxonomy ) ) {
			$query->whereRaw( '( `at`.`robots_noindex` IS NULL OR `at`.`robots_default` = 1 OR `at`.`robots_noindex` = 0 )' );
		} else {
			$query->whereRaw( '( `at`.`robots_default` = 0 AND `at`.`robots_noindex` = 0 )' );
		}

		if ( aioseo()->sitemap->indexes && empty( $additionalArgs['root'] ) ) {
			$query->limit( aioseo()->sitemap->linksPerIndex, $offset );
		}

		$terms = $query->orderBy( '`t`.`term_id` ASC' )
			->run()
			->result();

		foreach ( $terms as $term ) {
			// Convert ID from string to int.
			$term->term_id = intval( $term->term_id );
			// Add taxonomy name to object manually instead of querying it to prevent redundant join.
			$term->taxonomy = $taxonomy;
		}
		return $terms;
	}
}