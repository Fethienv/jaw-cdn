<?php
namespace AIOSEO\Plugin\Pro\Schema\Graphs;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * EDD product graph class.
 *
 * @since 4.0.13
 */
class EddProduct extends Product {
	/**
	 * The download object.
	 *
	 * @since 4.0.13
	 *
	 * @var EDD_Download
	 */
	private $download = null;

	/**
	 * The product options.
	 *
	 * @since 4.0.13
	 *
	 * @var array
	 */
	protected $productOptions = null;

	/**
	 * Class constructor.
	 *
	 * @since 4.0.13
	 */
	public function __construct() {
		parent::__construct();

		add_filter( 'edd_add_schema_microdata', '__return_false' );
		if ( aioseo()->helpers->isEddReviewsActive() ) {
			add_filter( 'edd_reviews_json_ld_data', [ $this, 'unsetEddHeadSchema' ] );
			remove_action( 'the_content', [ \EDD_Reviews::get_instance(), 'microdata' ] );
		}

		$this->download = edd_get_download( get_the_id() );
	}

	/**
	 * Returns the graph data.
	 *
	 * @since 4.0.13
	 *
	 * @return array The graph data.
	 */
	public function get() {
		if ( ! is_object( $this->download ) ) {
			return [];
		}

		$data = [
			'@type' => 'Product',
			'@id'   => aioseo()->schema->context['url'] . '#eddProduct',
			'name'  => aioseo()->schema->context['name'],
			'url'   => aioseo()->schema->context['url'],
		];

		$dataFunctions = [
			'sku'         => 'getSku',
			'productID'   => 'getSku',
			'description' => 'getDescription',
			'brand'       => 'getBrand',
			'image'       => 'getImage',
			'offers'      => 'getOffers',
		];

		if (
			! empty( $this->productOptions->identifierType ) &&
			'none' !== lcfirst( $this->productOptions->identifierType ) &&
			! empty( $this->productOptions->identifier )
		) {
			$data[ $this->productOptions->identifierType ] = $this->productOptions->identifier;
		}

		$reviewData = $this->getReviewData();
		if ( $reviewData ) {
			$data += $reviewData;
		}

		return $this->getData( $data, $dataFunctions );
	}

	/**
	 * Unsets the AggregateRating graph EDD outputs in the HEAD.
	 *
	 * @since 4.0.13
	 *
	 * @param  array $data The graph data.
	 * @return array $data The neutralized graph data.
	 */
	public function unsetEddHeadSchema( $data ) {
		if ( isset( $data['aggregateRating']['ratingCount'] ) ) {
			$data['aggregateRating']['ratingCount'] = 0;
		}
	}

	/**
	 * Returns the product SKU.
	 *
	 * @since 4.0.13
	 *
	 * @return string The product SKU.
	 */
	protected function getSku() {
		return method_exists( $this->download, 'get_sku' ) ? $this->download->get_sku() : '';
	}

	/**
	 * Returns the offer data.
	 *
	 * @since 4.0.13
	 *
	 * @return array The offer data.
	 */
	protected function getOffers() {
		$offer = [
			'@type' => 'Offer',
			'url'   => aioseo()->schema->context['url'] . '#offers'
		];

		$dataFunctions = [
			'price'           => 'getPrice',
			'priceCurrency'   => 'getPriceCurrency',
			'priceValidUntil' => 'getPriceValidUntil',
			'availability'    => 'getAvailability',
			'category'        => 'getCategory'
		];

		return $this->getData( $offer, $dataFunctions );
	}

	/**
	 * Returns the product price.
	 *
	 * @since 4.0.13
	 *
	 * @return float The product price.
	 */
	protected function getPrice() {
		if ( method_exists( $this->download, 'is_free' ) && $this->download->is_free() ) {
			return '0';
		}
		return method_exists( $this->download, 'get_price' ) ? $this->download->get_price() : '';
	}

	/**
	 * Returns the product currency.
	 *
	 * @since 4.0.13
	 *
	 * @return string The product currency.
	 */
	protected function getPriceCurrency() {
		return function_exists( 'edd_get_currency' ) ? edd_get_currency() : 'USD';
	}

	/**
	 * Returns the product category.
	 *
	 * @since 4.0.13
	 *
	 * @return string The product category.
	 */
	protected function getCategory() {
		$categories = wp_get_post_terms( $this->download->get_id(), 'download_category', [ 'fields' => 'names' ] );
		return ! empty( $categories ) && __( 'Uncategorized', '' ) !== $categories[0] ? $categories[0] : ''; // phpcs:ignore WordPress.WP.I18n.TextDomainMismatch
	}

	/**
	 * Returns the review data.
	 *
	 * @since 4.0.13
	 *
	 * @return array The review data.
	 */
	protected function getReviewData() {
		if ( aioseo()->helpers->isEddReviewsActive() ) {
			$dataFunctions = [
				'aggregateRating' => 'getEddAggregateRating',
				'review'          => 'getEddReview'
			];
			return $this->getData( [], $dataFunctions );
		}

		return parent::getReviewData();
	}

	/**
	 * Returns the AggregateRating graph data.
	 *
	 * @since 4.0.13
	 *
	 * @return array The graph data.
	 */
	protected function getEddAggregateRating() {
		return [
			'@type'       => 'AggregateRating',
			'@id'         => aioseo()->schema->context['url'] . '#aggregrateRating',
			'worstRating' => 1,
			'bestRating'  => 5,
			'ratingValue' => get_post_meta( $this->download->get_id(), 'edd_reviews_average_rating', true ),
			'reviewCount' => get_comments_number( $this->download->get_id() )
		];
	}

	/**
	 * Returns the Review graph data.
	 *
	 * @since 4.0.13
	 *
	 * @return array The graph data.
	 */
	protected function getEddReview() {
		// Because get_comments() doesn't seem to work for EDD, we use our own DB class here.
		$comments = aioseo()->db->start( 'comments' )
			->where( 'comment_post_ID', $this->download->get_id() )
			->where( 'comment_type', 'edd_review' )
			->limit( 25 )
			->run()
			->result();

		if ( empty( $comments ) ) {
			return [];
		}

		$reviews = [];
		foreach ( $comments as $comment ) {
			$approved = get_comment_meta( $comment->comment_ID, 'edd_review_approved', true );
			if ( empty( $approved ) ) {
				continue;
			}

			$review = [
				'@type'         => 'Review',
				'reviewRating'  => [
					'@type'       => 'Rating',
					'ratingValue' => get_comment_meta( $comment->comment_ID, 'edd_rating', true ),
					'worstRating' => 1,
					'bestRating'  => 5
				],
				'author'        => [
					'@type' => 'Person',
					'name'  => $comment->comment_author
				],
				'datePublished' => mysql2date( DATE_W3C, $comment->comment_date_gmt, false )
			];

			$reviewTitle = get_comment_meta( $comment->comment_ID, 'edd_review_title', true );
			if ( ! empty( $reviewTitle ) ) {
				$review['headline'] = $reviewTitle;
			}

			if ( ! empty( $comment->comment_content ) ) {
				$review['reviewBody'] = $comment->comment_content;
			}

			$reviews[] = $review;
		}
		return $reviews;
	}
}