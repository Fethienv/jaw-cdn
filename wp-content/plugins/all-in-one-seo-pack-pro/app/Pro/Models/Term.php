<?php
namespace AIOSEO\Plugin\Pro\Models;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use AIOSEO\Plugin\Common\Models as CommonModels;

/**
 * The Term DB Model.
 *
 * @since 4.0.0
 */
class Term extends CommonModels\Model {
	/**
	 * The name of the table in the database, without the prefix.
	 *
	 * @since 4.0.0
	 *
	 * @var string
	 */
	protected $table = 'aioseo_terms';

	/**
	 * Fields that should be json encoded on save and decoded on get.
	 *
	 * @since 4.0.0
	 *
	 * @var array
	 */
	protected $jsonFields = [ 'videos' ];

	/**
	 * Fields that should be boolean values.
	 *
	 * @since 4.0.0
	 *
	 * @var array
	 */
	protected $booleanFields = [
		'twitter_use_og',
		'pillar_content',
		'robots_default',
		'robots_noindex',
		'robots_noarchive',
		'robots_nosnippet',
		'robots_nofollow',
		'robots_noimageindex',
		'robots_noodp',
		'robots_notranslate'
	];

	/**
	 * Fields that should be hidden when serialized.
	 *
	 * @since 4.0.0
	 *
	 * @var array
	 */
	protected $hidden = [ 'id' ];

	/**
	 * Returns a Term with a given ID.
	 *
	 * @since 4.0.0
	 *
	 * @param  int  $termId The Term ID.
	 * @return Term         The Term object.
	 */
	public static function getTerm( $termId ) {
		return aioseo()->db
			->start( 'aioseo_terms' )
			->where( 'term_id', $termId )
			->run()
			->model( 'AIOSEO\\Plugin\\Pro\\Models\\Term' );
	}

	/**
	 * Save Edited Term.
	 *
	 * @since 4.0.3
	 *
	 * @param  int                    $termId The term ID.
	 * @param  string                 $data   The term data to save.
	 * @return bool|\WP_REST_Response         True if term data was saved or error response.
	 */
	public static function saveTerm( $termId, $data ) {
		$theTerm = aioseo()->db
			->start( 'aioseo_terms' )
			->where( 'term_id', $termId )
			->run()
			->model( 'AIOSEO\\Plugin\\Pro\\Models\\Term' );

		$theTerm->term_id                     = $termId;
		$theTerm->priority                    = ! empty( $data['priority'] ) ? sanitize_text_field( $data['priority'] ) : null;
		$theTerm->frequency                   = ! empty( $data['frequency'] ) ? sanitize_text_field( $data['frequency'] ) : null;
		$theTerm->title                       = ! empty( $data['title'] ) ? sanitize_text_field( $data['title'] ) : null;
		$theTerm->description                 = ! empty( $data['description'] ) ? sanitize_text_field( $data['description'] ) : null;
		$theTerm->keywords                    = ! empty( $data['keywords'] ) ? sanitize_text_field( $data['keywords'] ) : null;
		$theTerm->seo_score                   = ! empty( $data['seo_score'] ) ? sanitize_text_field( $data['seo_score'] ) : 0;
		$theTerm->canonical_url               = ! empty( $data['canonicalUrl'] ) ? esc_url_raw( $data['canonicalUrl'] ) : null;
		$theTerm->pillar_content              = isset( $data['pillar_content'] ) ? rest_sanitize_boolean( $data['pillar_content'] ) : 0;
		$theTerm->robots_default              = isset( $data['default'] ) ? rest_sanitize_boolean( $data['default'] ) : 1; // robots_enabled
		$theTerm->robots_noindex              = isset( $data['noindex'] ) ? rest_sanitize_boolean( $data['noindex'] ) : 0;
		$theTerm->robots_nofollow             = isset( $data['nofollow'] ) ? rest_sanitize_boolean( $data['nofollow'] ) : 0;
		$theTerm->robots_noarchive            = isset( $data['noarchive'] ) ? rest_sanitize_boolean( $data['noarchive'] ) : 0;
		$theTerm->robots_notranslate          = isset( $data['notranslate'] ) ? rest_sanitize_boolean( $data['notranslate'] ) : 0;
		$theTerm->robots_noimageindex         = isset( $data['noimageindex'] ) ? rest_sanitize_boolean( $data['noimageindex'] ) : 0;
		$theTerm->robots_nosnippet            = isset( $data['nosnippet'] ) ? rest_sanitize_boolean( $data['nosnippet'] ) : 0;
		$theTerm->robots_noodp                = isset( $data['noodp'] ) ? rest_sanitize_boolean( $data['noodp'] ) : 0;
		$theTerm->robots_max_snippet          = ! empty( $data['maxSnippet'] ) ? sanitize_text_field( $data['maxSnippet'] ) : 0;
		$theTerm->robots_max_videopreview     = ! empty( $data['maxVideoPreview'] ) ? (int) sanitize_text_field( $data['maxVideoPreview'] ) : 0;
		$theTerm->robots_max_imagepreview     = ! empty( $data['maxImagePreview'] ) ? sanitize_text_field( $data['maxImagePreview'] ) : 'none';
		$theTerm->og_object_type              = ! empty( $data['og_object_type'] ) ? sanitize_text_field( $data['og_object_type'] ) : 'default';
		$theTerm->og_title                    = ! empty( $data['og_title'] ) ? sanitize_text_field( $data['og_title'] ) : null;
		$theTerm->og_description              = ! empty( $data['og_description'] ) ? sanitize_text_field( $data['og_description'] ) : null;
		$theTerm->og_image_custom_url         = ! empty( $data['og_image_custom_url'] ) ? esc_url_raw( $data['og_image_custom_url'] ) : null;
		$theTerm->og_image_custom_fields      = ! empty( $data['og_image_custom_fields'] ) ? sanitize_text_field( $data['og_image_custom_fields'] ) : null;
		$theTerm->og_image_type               = ! empty( $data['og_image_type'] ) ? sanitize_text_field( $data['og_image_type'] ) : 'default';
		$theTerm->og_video                    = ! empty( $data['og_video'] ) ? sanitize_text_field( $data['og_video'] ) : '';
		$theTerm->og_article_section          = ! empty( $data['og_article_section'] ) ? sanitize_text_field( $data['og_article_section'] ) : null;
		$theTerm->og_article_tags             = ! empty( $data['og_article_tags'] ) ? sanitize_text_field( $data['og_article_tags'] ) : null;
		$theTerm->twitter_use_og              = isset( $data['twitter_use_og'] ) ? rest_sanitize_boolean( $data['twitter_use_og'] ) : 0;
		$theTerm->twitter_card                = ! empty( $data['twitter_card'] ) ? sanitize_text_field( $data['twitter_card'] ) : 'default';
		$theTerm->twitter_image               = ! empty( $data['twitter_image'] ) ? sanitize_text_field( $data['twitter_image'] ) : null;
		$theTerm->twitter_image_custom_url    = ! empty( $data['twitter_image_custom_url'] ) ? esc_url_raw( $data['twitter_image_custom_url'] ) : null;
		$theTerm->twitter_image_custom_fields = ! empty( $data['twitter_image_custom_fields'] ) ? sanitize_text_field( $data['twitter_image_custom_fields'] ) : null;
		$theTerm->twitter_image_type          = ! empty( $data['twitter_image_type'] ) ? sanitize_text_field( $data['twitter_image_type'] ) : 'default';
		$theTerm->twitter_title               = ! empty( $data['twitter_title'] ) ? sanitize_text_field( $data['twitter_title'] ) : null;
		$theTerm->twitter_description         = ! empty( $data['twitter_description'] ) ? sanitize_text_field( $data['twitter_description'] ) : null;
		$theTerm->schema_type                 = ! empty( $data['schema_type'] ) ? sanitize_text_field( $data['schema_type'] ) : 'none';
		$theTerm->schema_type_options         = ! empty( $data['schema_type_options'] ) ? wp_json_encode( $data['schema_type_options'] ) : parent::getDefaultSchemaOptions();
		$theTerm->tabs                        = ! empty( $data['tabs'] ) ? wp_json_encode( $data['tabs'] ) : parent::getDefaultTabsOptions();
		$theTerm->updated                     = gmdate( 'Y-m-d H:i:s' );

		if ( ! $theTerm->exists() ) {
			$theTerm->created = gmdate( 'Y-m-d H:i:s' );
		}
		$theTerm->save();
		$theTerm->reset();

		// Update the post meta as well for localization.
		$keywords = ! empty( $data['keywords'] ) ? json_decode( $data['keywords'] ) : [];
		foreach ( $keywords as $k => $keyword ) {
			$keywords[ $k ] = $keyword->value;
		}
		$keywords = implode( ',', $keywords );

		$ogArticleTags = ! empty( $data['og_article_tags'] ) ? json_decode( $data['og_article_tags'] ) : [];
		foreach ( $ogArticleTags as $k => $tag ) {
			$ogArticleTags[ $k ] = $tag->value;
		}
		$ogArticleTags = implode( ',', $ogArticleTags );

		update_term_meta( $termId, '_aioseo_title', $data['title'] );
		update_term_meta( $termId, '_aioseo_description', $data['description'] );
		update_term_meta( $termId, '_aioseo_keywords', $keywords );
		update_term_meta( $termId, '_aioseo_og_title', $data['og_title'] );
		update_term_meta( $termId, '_aioseo_og_description', $data['og_description'] );
		update_term_meta( $termId, '_aioseo_og_article_section', $data['og_article_section'] );
		update_term_meta( $termId, '_aioseo_og_article_tags', $ogArticleTags );
		update_term_meta( $termId, '_aioseo_twitter_title', $data['twitter_title'] );
		update_term_meta( $termId, '_aioseo_twitter_description', $data['twitter_description'] );

		$lastError = aioseo()->db->lastError();
		if ( ! empty( $lastError ) ) {
			return $lastError;
		}

		return true;
	}
}