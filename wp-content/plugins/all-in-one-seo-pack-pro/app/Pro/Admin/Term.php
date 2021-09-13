<?php
namespace AIOSEO\Plugin\Pro\Admin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use AIOSEO\Plugin\Pro\Models;

/**
 * Abstract class that Pro and Lite both extend.
 *
 * @since 4.0.0
 */
class Term {
	/**
	 * Initialize the admin.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		// Save New Term data
		add_action( 'created_term', [ $this, 'saveNewTerm' ], 1000, 3 );

		// Update term metabox
		add_action( 'edit_term', [ $this, 'saveTermSettingsMetabox' ], 10, 1 );
	}

	/**
	 * Save New Term data into custom table.
	 *
	 * @since 4.0.0
	 *
	 * @param  int    $termId Term ID.
	 * @param  int    $ttid   Term taxonomy ID.
	 * @param  string $slug   Taxonomy slug.
	 * @return void
	 */
	public function saveNewTerm( $termId, $ttid, $slug ) {
		$term                      = Models\Term::getTerm( $termId );
		$term->term_id             = $termId;
		$term->priority            = 'default';
		$term->frequency           = 'default';
		$term->tabs                = Models\Term::getDefaultTabsOptions();
		$term->seo_score           = 0;
		$term->schema_type         = 'none';
		$term->schema_type_options = Models\Term::getDefaultSchemaOptions();
		$term->save();
	}

	/**
	 * Handles metabox saving.
	 *
	 * @since 4.0.3
	 *
	 * @param  int  $termId Term ID.
	 * @return void
	 */
	public function saveTermSettingsMetabox( $termId ) {
		// Security check
		if ( ! isset( $_POST['TermSettingsNonce'] ) || ! wp_verify_nonce( $_POST['TermSettingsNonce'], 'aioseoTermSettingsNonce' ) ) {
			return;
		}

		// If we don't have our term settings input, we can safely skip.
		if ( ! isset( $_POST['aioseo-term-settings'] ) ) {
			return;
		}

		$currentPost = json_decode( stripslashes( $_POST['aioseo-term-settings'] ), true ); // phpcs:ignore HM.Security.ValidatedSanitizedInput

		Models\Term::saveTerm( $termId, $currentPost );

	}
}