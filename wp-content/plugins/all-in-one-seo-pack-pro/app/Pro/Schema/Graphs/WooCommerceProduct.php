<?php
namespace AIOSEO\Plugin\Pro\Schema\Graphs;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce product graph class.
 *
 * @since 4.0.13
 */
class WooCommerceProduct extends Product {
	/**
	 * The download object.
	 *
	 * @since 4.0.13
	 *
	 * @var WC_Product
	 */
	private $product = null;

	/**
	 * Class constructor.
	 *
	 * @since 4.0.13
	 */
	public function __construct() {
		parent::__construct();

		remove_action( 'wp_footer', [ WC()->structured_data, 'output_structured_data' ], 10 );
		$this->product = wc_get_product( get_the_ID() );
	}

	/**
	 * Returns the graph data.
	 *
	 * @since 4.0.13
	 *
	 * @return array The graph data.
	 */
	public function get() {
		if ( ! is_object( $this->product ) ) {
			return [];
		}

		$data = [
			'@type' => 'Product',
			'@id'   => aioseo()->schema->context['url'] . '#product',
			'url'   => aioseo()->schema->context['url'],
		];

		$dataFunctions = [
			'sku'             => 'getSku',
			'productID'       => 'getSku',
			'name'            => 'getName',
			'description'     => 'getShortDescription',
			'brand'           => 'getBrand',
			'image'           => 'getImage',
			'offers'          => 'getOffers',
			'aggregateRating' => 'getWooCommerceAggregateRating',
			'review'          => 'getWooCommerceReview'
			// @TODO: Look into adding support for shipping details.
		];

		$data = $this->getData( $data, $dataFunctions );

		if (
			! empty( $this->productOptions->identifierType ) &&
			'none' !== lcfirst( $this->productOptions->identifierType ) &&
			! empty( $this->productOptions->identifier )
		) {
			$data[ $this->productOptions->identifierType ] = $this->productOptions->identifier;
		}

		return $data;
	}

	/**
	 * Returns the product SKU.
	 *
	 * @since 4.0.13
	 *
	 * @return string The product SKU.
	 */
	protected function getSku() {
		return method_exists( $this->product, 'get_sku' ) ? $this->product->get_sku() : '';
	}

	/**
	 * Returns the product name.
	 *
	 * @since 4.0.13
	 *
	 * @return string The product name.
	 */
	protected function getName() {
		return method_exists( $this->product, 'get_name' ) ? $this->product->get_name() : get_the_title();
	}

	/**
	 * Returns the product short description.
	 *
	 * @since 4.0.13
	 *
	 * @return string The product short description.
	 */
	protected function getShortDescription() {
		$description = method_exists( $this->product, 'get_short_description' ) ? $this->product->get_short_description() : $this->getDescription();
		return strip_shortcodes( wp_strip_all_tags( $description ) );
	}

	/**
	 * Returns the product description.
	 *
	 * @since 4.0.13
	 *
	 * @return string The product description.
	 */
	protected function getDescription() {
		return method_exists( $this->product, 'get_description' ) ? $this->product->get_description() : '';
	}

	/**
	 * Returns the product brand.
	 *
	 * @since 4.0.13
	 *
	 * @return array The product brand.
	 */
	protected function getBrand() {
		$brand = aioseo()->helpers->getWooCommerceBrand( $this->product->get_id() );
		if ( $brand ) {
			return [
				'@type' => 'Brand',
				'name'  => $brand
			];
		}

		$metaData      = aioseo()->meta->metaData->getMetaData();
		$schemaOptions = json_decode( $metaData->schema_type_options );
		if ( empty( $schemaOptions->product->brand ) ) {
			return [];
		}

		return [
			'@type' => 'Brand',
			'name'  => $schemaOptions->product->brand
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
		$imageId = method_exists( $this->product, 'get_image_id' ) ? $this->product->get_image_id() : get_post_thumbnail_id();
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
			'availability'    => 'getAvailability',
			'category'        => 'getCategory'
		];

		if ( $this->product instanceof \WC_Product_Variable && method_exists( $this->product, 'get_variation_price' ) ) {
			$offer = [
				'@type' => 'AggregateOffer',
				'url'   => aioseo()->schema->context['url'] . '#aggregateOffer'
			];

			$dataFunctions = [
				'lowPrice'      => 'getLowPrice',
				'highPrice'     => 'getHighPrice',
				'offerCount'    => 'getOfferCount',
				'priceCurrency' => 'getPriceCurrency'
			];
		}

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
		return method_exists( $this->product, 'get_price' ) ? $this->product->get_price() : '';
	}

	/**
	 * Returns the lowest price of the product.
	 *
	 * @since 4.1.1
	 *
	 * @return float The lowest price.
	 */
	protected function getLowPrice() {
		return method_exists( $this->product, 'get_variation_price' ) ? $this->product->get_variation_price( 'min' ) : '';
	}

	/**
	 * Returns the highest price of the product.
	 *
	 * @since 4.1.1
	 *
	 * @return float The highest price.
	 */
	protected function getHighPrice() {
		return method_exists( $this->product, 'get_variation_price' ) ? $this->product->get_variation_price( 'max' ) : '';
	}

	/**
	 * Returns the product currency.
	 *
	 * @since 4.0.13
	 *
	 * @return string The product currency.
	 */
	protected function getPriceCurrency() {
		return function_exists( 'get_woocommerce_currency' ) ? get_woocommerce_currency() : 'USD';
	}

	/**
	 * Returns the offer count.
	 *
	 * @since 4.1.1
	 *
	 * @return string The offer count.
	 */
	protected function getOfferCount() {
		return count( $this->product->get_available_variations( 'objects' ) );
	}

	/**
	 * Returns the date the product price is valid until.
	 *
	 * @since 4.0.13
	 *
	 * @return string The date the product price is valid until.
	 */
	protected function getPriceValidUntil() {
		if ( ! method_exists( $this->product, 'get_date_on_sale_to' ) || ! $this->product->get_date_on_sale_to() ) {
			return '';
		}

		$date = $this->product->get_date_on_sale_to();
		return is_object( $date ) && method_exists( $date, 'date_i18n' ) ? $date->date_i18n() : '';
	}

	/**
	 * Returns the product availability.
	 *
	 * @since 4.0.13
	 *
	 * @return string The product availability.
	 */
	protected function getAvailability() {
		if ( ! method_exists( $this->product, 'get_stock_status' ) || ! $this->product->get_stock_status() ) {
			return 'https://schema.org/InStock';
		}

		switch ( $this->product->get_stock_status() ) {
			case 'outofstock':
				return 'https://schema.org/OutOfStock';
			case 'onbackorder':
				return 'https://schema.org/PreOrder';
			case 'instock':
			default:
				return 'https://schema.org/InStock';
		}
	}

	/**
	 * Returns the product category.
	 *
	 * @since 4.0.13
	 *
	 * @return string The product category.
	 */
	protected function getCategory() {
		$categories = wp_get_post_terms( $this->product->get_id(), 'product_cat', [ 'fields' => 'names' ] );
		return ! empty( $categories ) && __( 'Uncategorized', '' ) !== $categories[0] ? $categories[0] : ''; // phpcs:ignore WordPress.WP.I18n.TextDomainMismatch
	}

	/**
	 * Returns the AggregateRating graph data.
	 *
	 * @since 4.0.13
	 *
	 * @return array The graph data.
	 */
	protected function getWooCommerceAggregateRating() {
		if ( ! method_exists( $this->product, 'get_average_rating' ) || ! $this->product->get_average_rating() ) {
			return [];
		}

		if (
			apply_filters( 'aioseo_schema_product_check_reviews_allowed', false ) &&
			method_exists( $this->product, 'get_reviews_allowed' ) &&
			! $this->product->get_reviews_allowed()
		) {
			return [];
		}

		return [
			'@type'       => 'AggregateRating',
			'@id'         => aioseo()->schema->context['url'] . '#aggregrateRating',
			'worstRating' => 1,
			'bestRating'  => 5,
			'ratingValue' => $this->product->get_average_rating(),
			'reviewCount' => $this->product->get_review_count()
		];
	}

	/**
	 * Returns the Review graph data.
	 *
	 * @since 4.0.13
	 *
	 * @return array The graph data.
	 */
	protected function getWooCommerceReview() {
		$comments = get_comments( [
			'post_id' => $this->product->get_id(),
			'type'    => 'review',
			'status'  => 'approve',
			'number'  => 25
		] );

		if ( empty( $comments ) ) {
			return [];
		}

		$reviews = [];
		foreach ( $comments as $comment ) {
			$review = [
				'@type'         => 'Review',
				'reviewRating'  => [
					'@type'       => 'Rating',
					'ratingValue' => get_comment_meta( $comment->comment_ID, 'rating', true ),
					'worstRating' => 1,
					'bestRating'  => 5
				],
				'author'        => [
					'@type' => 'Person',
					'name'  => $comment->comment_author
				],
				'datePublished' => mysql2date( DATE_W3C, $comment->comment_date_gmt, false )
			];

			if ( ! empty( $comment->comment_content ) ) {
				$review['reviewBody'] = $comment->comment_content;
			}

			$reviews[] = $review;
		}
		return $reviews;
	}
}