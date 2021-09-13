<?php
namespace AIOSEO\Plugin\Pro\Schema\Graphs;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use AIOSEO\Plugin\Common\Schema\Graphs as CommonGraphs;

/**
 * Product graph class.
 *
 * @since 4.0.13
 */
class Product extends CommonGraphs\Graph {
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
		$metaData      = aioseo()->meta->metaData->getMetaData();
		$schemaOptions = json_decode( $metaData->schema_type_options );
		if ( ! empty( $schemaOptions->product ) ) {
			$this->productOptions = $schemaOptions->product;
		}
	}

	/**
	 * Returns the graph data.
	 *
	 * @since 4.0.13
	 *
	 * @return array The graph data.
	 */
	public function get() {
		if ( ! is_singular() ) {
			return [];
		}

		if ( aioseo()->helpers->isWooCommerceActive() && is_singular( 'product' ) ) {
			return ( new WooCommerceProduct() )->get();
		}

		if ( aioseo()->helpers->isEddActive() && is_singular( 'download' ) && function_exists( 'edd_get_download' ) ) {
			return ( new EddProduct() )->get();
		}

		$data = [
			'@type' => 'Product',
			'@id'   => aioseo()->schema->context['url'] . '#product',
			'name'  => aioseo()->schema->context['name'],
			'url'   => aioseo()->schema->context['url']
		];

		$dataFunctions = [
			'sku'         => 'getSku',
			'productID'   => 'getSku',
			'description' => 'getDescription',
			'brand'       => 'getBrand',
			'image'       => 'getImage',
			'offers'      => 'getOffers'
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
	 * Returns the product SKU.
	 *
	 * @since 4.0.13
	 *
	 * @return string The product SKU.
	 */
	protected function getSku() {
		return ! empty( $this->productOptions->sku ) ? $this->productOptions->sku : '';
	}

	/**
	 * Returns the product description.
	 *
	 * @since 4.0.13
	 *
	 * @return string The product description.
	 */
	protected function getDescription() {
		return ! empty( $this->productOptions->description ) ? $this->productOptions->description : '';
	}

	/**
	 * Returns the product brand.
	 *
	 * @since 4.0.13
	 *
	 * @return array The product brand.
	 */
	protected function getBrand() {
		if ( empty( $this->productOptions->brand ) ) {
			return [];
		}
		return [
			'@type' => 'Brand',
			'name'  => $this->productOptions->brand
		];
	}

	/**
	 * Returns the product image.
	 *
	 * @since 4.0.13
	 *
	 * @return array The product image.
	 */
	protected function getImage() {
		$imageId = get_post_thumbnail_id();
		return $imageId ? $this->image( $imageId, 'productImage' ) : '';
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
			'url'   => aioseo()->schema->context['url'] . '#offers',
		];

		$dataFunctions = [
			'price'           => 'getPrice',
			'priceCurrency'   => 'getPriceCurrency',
			'priceValidUntil' => 'getPriceValidUntil',
			'areaServed'      => 'getAreaServed',
			'availability'    => 'getAvailability'
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
		return isset( $this->productOptions->price ) ? $this->productOptions->price : '';
	}

	/**
	 * Returns the product currency.
	 *
	 * @since 4.0.13
	 *
	 * @return string The product currency.
	 */
	protected function getPriceCurrency() {
		return ! empty( $this->productOptions->currency ) ? $this->productOptions->currency : '';
	}

	/**
	 * Returns the date the product price is valid until.
	 *
	 * @since 4.0.13
	 *
	 * @return string The date the product price is valid until.
	 */
	protected function getPriceValidUntil() {
		return ! empty( $this->productOptions->priceValidUntil )
			? aioseo()->helpers->dateToIso8601( $this->productOptions->priceValidUntil )
			: '';
	}

	/**
	 * Returns the area the store services.
	 *
	 * @since 4.0.13
	 *
	 * @return string The area the store services.
	 */
	protected function getAreaServed() {
		$options = aioseo()->options->noConflict();
		if ( aioseo()->license->isActive() && $options->has( 'localBusiness' ) ) {
			return $options->localBusiness->locations->business->areaServed;
		}
	}

	/**
	 * Returns the product availability.
	 *
	 * @since 4.0.13
	 *
	 * @return string The product availability.
	 */
	protected function getAvailability() {
		return ! empty( $this->productOptions->availability ) ? $this->productOptions->availability : 'https://schema.org/InStock';
	}

	/**
	 * Returns the review data.
	 *
	 * @since 4.0.13
	 *
	 * @return array The review data.
	 */
	protected function getReviewData() {
		if ( empty( $this->productOptions->reviews ) ) {
			return [];
		}

		$reviews = array_map( function( $review ) {
			return json_decode( $review );
		}, $this->productOptions->reviews );

		if ( empty( $reviews ) ) {
			return [];
		}

		return [
			'aggregateRating' => $this->getAggregateRating( $reviews ),
			'review'          => $this->getReview( $reviews )
		];
	}

	/**
	 * Returns the AggregateRating graph data.
	 *
	 * @since 4.0.13
	 *
	 * @param  array $reviews The reviews.
	 * @return array          The graph data.
	 */
	protected function getAggregateRating( $reviews ) {
		$ratings = array_map( function( $review ) {
			return $review->rating;
		}, $reviews );

		$averageRating = array_sum( $ratings ) / count( $ratings );

		return [
			'@type'       => 'AggregateRating',
			'@id'         => aioseo()->schema->context['url'] . '#aggregrateRating',
			'worstRating' => 1,
			'bestRating'  => 5,
			'ratingValue' => $averageRating,
			'reviewCount' => count( $ratings )
		];
	}

	/**
	 * Returns the Review graph data.
	 *
	 * @since 4.0.13
	 *
	 * @param  array $reviews The reviews.
	 * @return array $graphs  The graph data.
	 */
	protected function getReview( $reviews ) {
		$graphs = [];
		foreach ( $reviews as $review ) {
			if ( empty( $review->author ) || empty( $review->rating ) ) {
				continue;
			}

			$graph = [
				'@type'        => 'Review',
				'reviewRating' => [
					'@type'       => 'Rating',
					'ratingValue' => (int) $review->rating,
					'worstRating' => 1,
					'bestRating'  => 5
				],
				'author'       => [
					'@type' => 'Person',
					'name'  => $review->author
				]
			];

			if ( ! empty( $review->headline ) ) {
				$graph['headline'] = $review->headline;
			}

			if ( ! empty( $review->content ) ) {
				$graph['reviewBody'] = $review->content;
			}
			$graphs[] = $graph;
		}
		return $graphs;
	}
}