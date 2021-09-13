<?php
namespace AIOSEO\Plugin\Pro\Schema\Graphs;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use AIOSEO\Plugin\Common\Schema\Graphs as CommonGraphs;

/**
 * Course graph class.
 *
 * @since 4.0.13
 */
class Course extends CommonGraphs\Graph {
	/**
	 * The course options.
	 *
	 * @since 4.0.13
	 *
	 * @var array
	 */
	private $courseOptions = null;

	/**
	 * Class constructor.
	 *
	 * @since 4.0.13
	 */
	public function __construct() {
		$metaData      = aioseo()->meta->metaData->getMetaData();
		$schemaOptions = json_decode( $metaData->schema_type_options );
		if ( ! empty( $schemaOptions->course ) ) {
			$this->courseOptions = $schemaOptions->course;
		}
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
			'@type' => 'Course',
			'@id'   => aioseo()->schema->context['url'] . '#course',

		];

		$dataFunctions = [
			'name'        => 'getName',
			'description' => 'getDescription',
			'provider'    => 'getProvider',
		];

		return $this->getData( $data, $dataFunctions );
	}

	/**
	 * Returns the course name.
	 *
	 * @since 4.0.13
	 *
	 * @return string The course name.
	 */
	protected function getName() {
		return ! empty( $this->courseOptions->name ) ? $this->courseOptions->name : get_the_title();
	}

	/**
	 * Returns the course description.
	 *
	 * @since 4.0.13
	 *
	 * @return string The course description.
	 */
	protected function getDescription() {
		return ! empty( $this->courseOptions->description ) ? $this->courseOptions->description : '';
	}

	/**
	 * Returns the course provider.
	 *
	 * @since 4.0.13
	 *
	 * @return string The course provider.
	 */
	protected function getProvider() {
		return ! empty( $this->courseOptions->provider ) ? $this->courseOptions->provider : '';
	}
}