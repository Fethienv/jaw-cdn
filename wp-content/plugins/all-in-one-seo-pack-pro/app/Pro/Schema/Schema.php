<?php
namespace AIOSEO\Plugin\Pro\Schema;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use AIOSEO\Plugin\Common\Schema as CommonSchema;

/**
 * Builds our schema.
 *
 * @since 4.0.13
 */
class Schema extends CommonSchema\Schema {
	/**
	 * Returns the JSON schema for the requested page.
	 *
	 * @since 4.0.13
	 *
	 * @return string The JSON schema.
	 */
	public function get() {
		// First, let's check if it's disabled.
		if (
			apply_filters( 'aioseo_schema_disable', false ) ||
			( in_array( 'enableSchemaMarkup', aioseo()->internalOptions->deprecatedOptions, true ) && ! aioseo()->options->deprecated->searchAppearance->global->schema->enableSchemaMarkup )
		) {
			return '';
		}

		$this->init();
		if ( ! $this->graphs ) {
			return '';
		}

		$schema = [
			'@context' => 'https://schema.org',
			'@graph'   => []
		];

		$graphs = apply_filters( 'aioseo_schema_graphs', array_unique( array_filter( $this->graphs ) ) );
		foreach ( $graphs as $graph ) {
			$namespace = '';
			if ( class_exists( "\AIOSEO\Plugin\Pro\Schema\Graphs\\$graph" ) ) {
				$namespace = "\AIOSEO\Plugin\Pro\Schema\Graphs\\$graph";
			} elseif ( class_exists( "\AIOSEO\Plugin\Common\Schema\Graphs\\$graph" ) ) {
				$namespace = "\AIOSEO\Plugin\Common\Schema\Graphs\\$graph";
			}

			// Check if the graph exists in any of the active addons.
			if ( ! $namespace ) {
				$graph = $this->getAddonGraphData( $graph );
				if ( ! empty( $graph ) ) {
					$schema['@graph'][] = $graph;
					continue;
				}
			}

			if ( $namespace ) {
				$schema['@graph'][] = array_filter( ( new $namespace )->get() );
			}
		}

		$schema['@graph'] = apply_filters( 'aioseo_schema_output', $schema['@graph'] );
		$schema['@graph'] = array_values( $this->cleanData( $schema['@graph'] ) );

		return isset( $_GET['aioseo-dev'] ) ? wp_json_encode( $schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) : wp_json_encode( $schema );
	}

	/**
	 * Determines the context and graphs for the requested page.
	 *
	 * This can't run in the constructor since the queried object needs to be available first.
	 *
	 * @since 4.0.17
	 *
	 * @return void
	 */
	protected function init() {
		parent::init();
		$this->registerAddonGraphs();
	}

	/**
	 * Checks if our addons need to register graphs.
	 *
	 * @since 4.0.17
	 *
	 * @return void
	*/
	public function registerAddonGraphs( $separatedPerAddon = false ) {
		$loadedAddons = aioseo()->addons->getLoadedAddons();
		if ( empty( $loadedAddons ) ) {
			return;
		}

		foreach ( $loadedAddons as $loadedAddonSlug => $loadedAddon ) {
			if ( ! empty( $loadedAddon->schema ) ) {
				$this->graphs = array_merge( $this->graphs, $loadedAddon->schema->init() );
			}
		}
	}

	/**
	 * Gets the graph data from our addons.
	 *
	 * @since 4.0.17
	 *
	 * @param  string $graphName The graph name.
	 * @return array             The graph data.
	 */
	public function getAddonGraphData( $graphName ) {
		$loadedAddons = aioseo()->addons->getLoadedAddons();
		if ( empty( $loadedAddons ) ) {
			return;
		}

		foreach ( $loadedAddons as $loadedAddonSlug => $loadedAddon ) {
			if ( ! empty( $loadedAddon->schema ) ) {
				$data = $loadedAddon->schema->get( $graphName );
				if ( ! empty( $data ) ) {
					return $data;
				}
			}
		}
	}
}