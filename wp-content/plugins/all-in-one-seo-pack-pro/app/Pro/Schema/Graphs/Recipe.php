<?php
namespace AIOSEO\Plugin\Pro\Schema\Graphs;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use AIOSEO\Plugin\Common\Schema\Graphs as CommonGraphs;

/**
 * Recipe graph class.
 *
 * @since 4.0.13
 */
class Recipe extends CommonGraphs\Graph {
	/**
	 * The recipe options.
	 *
	 * @since 4.0.13
	 *
	 * @var array
	 */
	private $recipeOptions = null;

	/**
	 * Class constructor.
	 *
	 * @since 4.0.13
	 */
	public function __construct() {
		$metaData      = aioseo()->meta->metaData->getMetaData();
		$schemaOptions = json_decode( $metaData->schema_type_options );
		if ( ! empty( $schemaOptions->recipe ) ) {
			$this->recipeOptions = $schemaOptions->recipe;
		}
	}

	/**
	 * Returns the graph data.
	 *
	 * @since 4.0.13
	 *
	 * @return array $data The graph data.
	 */
	public function get() {
		if ( ! is_singular() ) {
			return [];
		}

		$post = aioseo()->helpers->getPost();
		$data = [
			'@type'         => 'Recipe',
			'@id'           => aioseo()->schema->context['url'] . '#recipe',
			'datePublished' => mysql2date( DATE_W3C, $post->post_date_gmt, false ),
		];

		$dataFunctions = [
			'name'               => 'getName',
			'description'        => 'getDescription',
			'author'             => 'getAuthor',
			'image'              => 'getImage',
			'recipeCategory'     => 'getDishType',
			'recipeCuisine'      => 'getCuisineType',
			'recipeYield'        => 'getServings',
			'nutrition'          => 'getCalories',
			'recipeIngredient'   => 'getIngredients',
			'recipeInstructions' => 'getInstructions',
			'aggregateRating'    => 'getAggregateRating',
			'keywords'           => 'getKeywords',
			// 'video'              => 'getVideo'
		];

		$data  = $this->getData( $data, $dataFunctions );
		$data += $this->getTimeRequired();
		return $data;
	}


	/**
	 * Returns the recipe name.
	 *
	 * @since 4.0.13
	 *
	 * @return string The recipe name.
	 */
	protected function getName() {
		return ! empty( $this->recipeOptions->name ) ? $this->recipeOptions->name : get_the_title();
	}

	/**
	 * Returns the recipe description.
	 *
	 * @since 4.0.13
	 *
	 * @return string The recipe description.
	 */
	protected function getDescription() {
		return ! empty( $this->recipeOptions->description ) ? $this->recipeOptions->description : '';
	}

	/**
	 * Returns the recipe author.
	 *
	 * @since 4.0.13
	 *
	 * @return string The recipe author.
	 */
	protected function getAuthor() {
		$author = ! empty( $this->recipeOptions->author ) ? $this->recipeOptions->author : get_the_author();
		if ( empty( $author ) ) {
			return [];
		}

		return [
			'@type' => 'Person',
			'name'  => $author,
			'url'   => aioseo()->schema->context['url'] . '#recipeAuthor',
		];
	}

	/**
	 * Returns the recipe image.
	 *
	 * @since 4.0.13
	 *
	 * @return array The recipe image.
	 */
	protected function getImage() {
		if ( ! empty( $this->recipeOptions->image ) ) {
			return $this->image( $this->recipeOptions->image, 'recipeImage' );
		}
		$imageId = get_post_thumbnail_id();
		return $imageId ? $this->image( $imageId, 'productImage' ) : '';
	}

	/**
	 * Returns the recipe dish type.
	 *
	 * @since 4.0.13
	 *
	 * @return string The recipe dish type.
	 */
	protected function getDishType() {
		return ! empty( $this->recipeOptions->dishType ) ? $this->recipeOptions->dishType : '';
	}

	/**
	 * Returns the recipe description.
	 *
	 * @since 4.0.13
	 *
	 * @return string The recipe description.
	 */
	protected function getCuisineType() {
		return ! empty( $this->recipeOptions->cuisineType ) ? $this->recipeOptions->cuisineType : '';
	}

	/**
	 * Returns the recipe description.
	 *
	 * @since 4.0.13
	 *
	 * @return string The recipe description.
	 */
	protected function getTimeRequired() {
		if ( empty( $this->recipeOptions->preparationTime ) && empty( $this->recipeOptions->cookingTime ) ) {
			return [];
		}

		return [
			'prepTime'  => aioseo()->helpers->minutesToIso8601( $this->recipeOptions->preparationTime ),
			'cookTime'  => aioseo()->helpers->minutesToIso8601( $this->recipeOptions->cookingTime ),
			'totalTime' => aioseo()->helpers->minutesToIso8601( (int) $this->recipeOptions->preparationTime + (int) $this->recipeOptions->cookingTime )
		];
	}

	/**
	 * Returns the recipe amount of servings.
	 *
	 * @since 4.0.13
	 *
	 * @return int The recipe amount of servings.
	 */
	protected function getServings() {
		return ! empty( $this->recipeOptions->servings ) ? (int) $this->recipeOptions->servings : '';
	}

	/**
	 * Returns the recipe calories.
	 *
	 * @since 4.0.13
	 *
	 * @return array The recipe calories.
	 */
	protected function getCalories() {
		if ( empty( $this->recipeOptions->calories ) ) {
			return [];
		}

		$calories = $this->recipeOptions->calories;
		return [
			'@type'    => 'NutritionInformation',
			'calories' => "$calories calories"
		];
	}

	/**
	 * Returns the recipe ingredients.
	 *
	 * @since 4.0.13
	 *
	 * @return array $ingredients The recipe ingredients.
	 */
	protected function getIngredients() {
		if ( empty( $this->recipeOptions->ingredients ) ) {
			return [];
		}

		$ingredientObjects = json_decode( $this->recipeOptions->ingredients );
		if ( empty( $ingredientObjects ) ) {
			return [];
		}

		$ingredients = [];
		foreach ( $ingredientObjects as $ingredientObject ) {
			$ingredients[] = $ingredientObject->value;
		}
		return $ingredients;
	}

	/**
	 * Returns the recipe instructions.
	 *
	 * @since 4.0.13
	 *
	 * @return array $steps The recipe instructions.
	 */
	protected function getInstructions() {
		if ( empty( $this->recipeOptions->instructions ) ) {
			return [];
		}

		$steps     = [];
		$stepCount = 0;
		foreach ( $this->recipeOptions->instructions as $instruction ) {
			$instruction = json_decode( $instruction );
			if ( empty( $instruction->content ) ) {
				continue;
			}

			$stepCount++;

			$steps[] = [
				'@type' => 'HowToStep',
				'text'  => $instruction->content,
				'url'   => aioseo()->schema->context['url'] . "#step$stepCount"
			];
		}
		return $steps;
	}

	/**
	 * Returns the AggregateRating graph data.
	 *
	 * @since 4.0.13
	 *
	 * @return array The graph data.
	 */
	protected function getAggregateRating() {
		if ( empty( $this->recipeOptions->averageRating ) || empty( $this->recipeOptions->ratingCount ) ) {
			return [];
		}

		return [
			'@type'       => 'AggregateRating',
			'@id'         => aioseo()->schema->context['url'] . '#aggregrateRating',
			'worstRating' => 1,
			'bestRating'  => 5,
			'ratingValue' => $this->recipeOptions->averageRating,
			'reviewCount' => $this->recipeOptions->ratingCount
		];
	}

	/**
	 * Returns the recipe keywords.
	 *
	 * @since 4.0.13
	 *
	 * @return array $keywords The recipe keywords.
	 */
	protected function getKeywords() {
		if ( empty( $this->recipeOptions->keywords ) ) {
			return [];
		}

		$keywordObjects = json_decode( $this->recipeOptions->keywords );
		if ( empty( $keywordObjects ) ) {
			return [];
		}

		$keywords = [];
		foreach ( $keywordObjects as $keywordObject ) {
			$keywords[] = $keywordObject->value;
		}
		return $keywords;
	}

	/**
	 * Returns the recipe video.
	 *
	 * @since 4.0.13
	 *
	 * @return array The recipe video.
	 */
	protected function getVideo() {
		if ( empty( $this->recipeOptions->videoUrl ) ) {
			return [];
		}

		return [
			'@type'      => 'VideoObject',
			'contentUrl' => $this->recipeOptions->videoUrl
		];
	}
}