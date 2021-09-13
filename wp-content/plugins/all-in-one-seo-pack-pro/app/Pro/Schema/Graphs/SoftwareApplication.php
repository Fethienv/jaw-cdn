<?php
namespace AIOSEO\Plugin\Pro\Schema\Graphs;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use AIOSEO\Plugin\Common\Schema\Graphs as CommonGraphs;

/**
 * Software Application graph class.
 *
 * @since 4.0.13
 */
class SoftwareApplication extends CommonGraphs\Graph {
	/**
	 * The software app options.
	 *
	 * @since 4.0.13
	 *
	 * @var array
	 */
	private $softwareOptions = null;

	/**
	 * Class constructor.
	 *
	 * @since 4.0.13
	 */
	public function __construct() {
		$metaData              = aioseo()->meta->metaData->getMetaData();
		$this->softwareOptions = json_decode( $metaData->schema_type_options )->software;
	}

	/**
	 * Returns the graph data.
	 *
	 * @since 4.0.0
	 *
	 * @return array $data The graph data.
	 */
	public function get() {
		if ( ! is_singular() ) {
			return [];
		}

		$data = [
			'@type' => 'SoftwareApplication',
			'@id'   => aioseo()->schema->context['url'] . '#softwareApp',

		];

		$dataFunctions = [
			'name'                => 'getName',
			'offers'              => 'getOffers',
			'aggregateRating'     => 'getAggregateRating',
			'review'              => 'getReview',
			'operatingSystem'     => 'getOperatingSystem',
			'applicationCategory' => 'getCategory'
		];

		return $this->getData( $data, $dataFunctions );
	}

	/**
	 * Returns the software name.
	 *
	 * @since 4.0.13
	 *
	 * @return string The software name.
	 */
	protected function getName() {
		return ! empty( $this->softwareOptions->name ) ? $this->softwareOptions->name : get_the_title();
	}

	/**
	 * Returns the software category.
	 *
	 * @since 4.0.13
	 *
	 * @return string The software category.
	 */
	protected function getCategory() {
		return ! empty( $this->softwareOptions->category ) ? $this->softwareOptions->category : '';
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
			'price'         => 'getPrice',
			'priceCurrency' => 'getPriceCurrency'
		];

		return $this->getData( $offer, $dataFunctions );
	}

	/**
	 * Returns the AggregateRating graph data.
	 *
	 * @since 4.0.13
	 *
	 * @return array The graph data.
	 */
	protected function getAggregateRating() {
		$review = ! empty( $this->softwareOptions->reviews[0] ) ? json_decode( $this->softwareOptions->reviews[0] ) : '';
		if ( empty( $review->author ) || empty( $review->rating ) ) {
			return [];
		}

		return [
			'@type'       => 'AggregateRating',
			'@id'         => aioseo()->schema->context['url'] . '#aggregrateRating',
			'worstRating' => 1,
			'bestRating'  => 5,
			'ratingValue' => $review->rating,
			'reviewCount' => 1
		];
	}


	/**
	 * Returns the software  price.
	 *
	 * @since 4.0.13
	 *
	 * @return float The price.
	 */
	protected function getPrice() {
		return isset( $this->softwareOptions->price ) ? $this->softwareOptions->price : '';
	}

	/**
	 * Returns the software currency.
	 *
	 * @since 4.0.13
	 *
	 * @return string The software currency.
	 */
	protected function getPriceCurrency() {
		return ! empty( $this->softwareOptions->currency ) ? $this->softwareOptions->currency : '';
	}

	/**
	 * Returns the Review graph data.
	 *
	 * @since 4.0.13
	 *
	 * @return array $review The review data.
	 */
	protected function getReview() {
		$review = ! empty( $this->softwareOptions->reviews[0] ) ? json_decode( $this->softwareOptions->reviews[0] ) : '';
		if ( empty( $review->author ) || empty( $review->rating ) ) {
			return [];
		}

		$data = [
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
			$data['headline'] = $review->headline;
		}

		if ( ! empty( $review->content ) ) {
			$data['reviewBody'] = $review->content;
		}

		return $data;
	}

	/**
	 * Returns the software operating systems.
	 *
	 * @since 4.0.13
	 *
	 * @return string The software operating systems.
	 */
	protected function getOperatingSystem() {
		if ( empty( $this->softwareOptions->operatingSystems ) ) {
			return '';
		}

		$osObjects = json_decode( $this->softwareOptions->operatingSystems );
		if ( empty( $osObjects ) ) {
			return '';
		}

		$operatingSystems = [];
		foreach ( $osObjects as $osObject ) {
			$operatingSystems[] = $osObject->value;
		}
		return implode( ', ', $operatingSystems );
	}
}