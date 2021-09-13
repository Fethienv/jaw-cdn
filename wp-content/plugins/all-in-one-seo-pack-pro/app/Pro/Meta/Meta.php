<?php
namespace AIOSEO\Plugin\Pro\Meta;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use \AIOSEO\Plugin\Common\Meta as CommonMeta;

use AIOSEO\Plugin\Pro\Models;

/**
 * Instantiates the Meta classes.
 *
 * @since 4.0.0
 */
class Meta extends CommonMeta\Meta {
	/**
	 * Class constructor.
	 *
	 * @since 4.0.0
	 */
	public function __construct() {
		$this->metaData     = new MetaData();
		$this->title        = new Title();
		$this->description  = new Description();
		$this->amp          = new CommonMeta\Amp();
		$this->links        = new CommonMeta\Links();
		$this->keywords     = new CommonMeta\Keywords();

		add_action( 'delete_post', [ $this, 'deletePostMeta' ], 1000, 2 );
		add_filter( 'delete_term', [ $this, 'deleteTermMeta' ], 1000, 2 );
	}

	/**
	 * When we delete the meta, we want to delete our post model.
	 *
	 * @since 4.0.1
	 *
	 * @param  integer $termId The ID of the post.
	 * @return void
	 */
	public function deleteTermMeta( $termId ) {
		$aioseoTerm = Models\Term::getTerm( $termId );
		if ( $aioseoTerm->exists() ) {
			$aioseoTerm->delete();
		}
	}
}