<?php
namespace AIOSEO\Plugin\Pro\Traits\Helpers;

use AIOSEO\Plugin\Pro\Models;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Contains all Vue related helper methods.
 *
 * @since 4.1.4
 */
trait Vue {
	/**
	 * Gets the data for vue.
	 *
	 * @since 4.0.0
	 *
	 * @param string $page The current page.
	 * @return array        An array of data.
	 */
	public function getVueData( $page = null ) {
		$data = parent::getVueData( $page );

		// Check if user has a custom filename from the V3 migration.
		$sitemapFilename      = aioseo()->sitemap->helpers->filename( 'general' );
		$sitemapFilename      = $sitemapFilename ? $sitemapFilename : 'sitemap';
		$videoSitemapFilename = aioseo()->sitemap->helpers->filename( 'video' );
		$videoSitemapFilename = $videoSitemapFilename ? $videoSitemapFilename : 'video-sitemap';
		$newsIndex            = apply_filters( 'aioseo_news_sitemap_index_name', 'news' );

		$data['urls']['videoSitemapUrl'] = home_url( "/$videoSitemapFilename.xml" );
		$data['urls']['newsSitemapUrl']  = home_url( "/$newsIndex-sitemap.xml" );

		$data['translationsPro'] = $this->getJedLocaleData( 'aioseo-pro' );

		$data['license'] = [
			'isActive'   => aioseo()->license->isActive(),
			'isExpired'  => aioseo()->license->isExpired(),
			'isDisabled' => aioseo()->license->isDisabled(),
			'isInvalid'  => aioseo()->license->isInvalid(),
			'expires'    => aioseo()->internalOptions->internal->license->expires
		];

		$screen = get_current_screen();
		if ( 'term' === $screen->base ) {
			$termId              = isset( $_GET['tag_ID'] ) ? abs( (int) wp_unslash( $_GET['tag_ID'] ) ) : 0; // phpcs:ignore HM.Security.ValidatedSanitizedInput.InputNotSanitized
			$term                = Models\Term::getTerm( $termId );
			$taxonomy            = get_term( $termId );
			$data['currentPost'] = [
				'context'                     => 'term',
				'tags'                        => aioseo()->tags->getDefaultTermTags( $termId ),
				'id'                          => $termId,
				'priority'                    => ! empty( $term->priority ) ? $term->priority : 'default',
				'frequency'                   => ! empty( $term->frequency ) ? $term->frequency : 'default',
				'permalink'                   => get_term_link( $termId ),
				'title'                       => ! empty( $term->title ) ? $term->title : aioseo()->meta->title->getTaxonomyTitle( $taxonomy->taxonomy ),
				'description'                 => ! empty( $term->description ) ? $term->description : aioseo()->meta->description->getTaxonomyDescription( $taxonomy->taxonomy ),
				'keywords'                    => ! empty( $term->keywords ) ? $term->keywords : wp_json_encode( [] ),
				'keyphrases'                  => ( isset( $term->keyphrases ) )
					? json_decode( $term->keyphrases )
					: json_decode( '{}' ),
				'type'                        => get_taxonomy( $screen->taxonomy )->labels->singular_name,
				'termType'                    => 'type' === $screen->taxonomy ? '_aioseo_type' : $screen->taxonomy,
				'seo_score'                   => (int) $term->seo_score,
				'pillar_content'              => ( (int) $term->pillar_content ) === 0 ? false : true,
				'canonicalUrl'                => $term->canonical_url,
				'default'                     => ( (int) $term->robots_default ) === 0 ? false : true,
				'noindex'                     => ( (int) $term->robots_noindex ) === 0 ? false : true,
				'noarchive'                   => ( (int) $term->robots_noarchive ) === 0 ? false : true,
				'nosnippet'                   => ( (int) $term->robots_nosnippet ) === 0 ? false : true,
				'nofollow'                    => ( (int) $term->robots_nofollow ) === 0 ? false : true,
				'noimageindex'                => ( (int) $term->robots_noimageindex ) === 0 ? false : true,
				'noodp'                       => ( (int) $term->robots_noodp ) === 0 ? false : true,
				'notranslate'                 => ( (int) $term->robots_notranslate ) === 0 ? false : true,
				'maxSnippet'                  => (int) $term->robots_max_snippet,
				'maxVideoPreview'             => (int) $term->robots_max_videopreview,
				'maxImagePreview'             => $term->robots_max_imagepreview,
				'modalOpen'                   => false,
				'tabs'                        => ( ! empty( $term->tabs ) )
					? json_decode( $term->tabs )
					: json_decode( Models\Term::getDefaultTabsOptions() ),
				'generalMobilePrev'           => false,
				'socialMobilePreview'         => false,
				'og_object_type'              => ! empty( $term->og_object_type ) ? $term->og_object_type : 'default',
				'og_title'                    => $term->og_title,
				'og_description'              => $term->og_description,
				'og_image_custom_url'         => $term->og_image_custom_url,
				'og_image_custom_fields'      => $term->og_image_custom_fields,
				'og_image_type'               => ! empty( $term->og_image_type ) ? $term->og_image_type : 'default',
				'og_video'                    => ! empty( $term->og_video ) ? $term->og_video : '',
				'og_article_section'          => ! empty( $term->og_article_section ) ? $term->og_article_section : '',
				'og_article_tags'             => ! empty( $term->og_article_tags ) ? $term->og_article_tags : wp_json_encode( [] ),
				'twitter_use_og'              => ( (int) $term->twitter_use_og ) === 0 ? false : true,
				'twitter_card'                => $term->twitter_card,
				'twitter_image_custom_url'    => $term->twitter_image_custom_url,
				'twitter_image_custom_fields' => $term->twitter_image_custom_fields,
				'twitter_image_type'          => $term->twitter_image_type,
				'twitter_title'               => $term->twitter_title,
				'twitter_description'         => $term->twitter_description,
				'schema_type'                 => ( ! empty( $term->schema_type ) ) ? $term->schema_type : 'none',
				'schema_type_options'         => ( ! empty( $term->schema_type_options ) )
					? json_decode( $term->schema_type_options )
					: json_decode( Models\Term::getDefaultSchemaOptions() ),
				'local_seo'                   => ( ! empty( $term->local_seo ) )
					? json_decode( $term->local_seo )
					: json_decode( '{}' )
			];

			if ( ! $term->exists() ) {
				$data['currentPost'] = array_merge( $data['currentPost'], aioseo()->migration->meta->getMigratedTermMeta( $termId ) );
			}
		}

		if ( 'post' === $page ) {
			$post = get_post( get_the_ID() );
			if ( is_object( $post ) ) {
				$dynamicOptions                            = aioseo()->dynamicOptions->noConflict();
				$data['currentPost']['defaultSchemaType']  = '';
				$data['currentPost']['defaultWebPageType'] = '';
				if ( $dynamicOptions->searchAppearance->postTypes->has( $post->post_type ) ) {
					$data['currentPost']['defaultSchemaType']  = $dynamicOptions->searchAppearance->postTypes->{$post->post_type}->schemaType;
					$data['currentPost']['defaultWebPageType'] = $dynamicOptions->searchAppearance->postTypes->{$post->post_type}->webPageType;
				}
			}
		}

		$post = $this->getPost();
		if ( $post && in_array( $post->post_type, [ 'product', 'download' ], true ) ) {
			$isWooCommerceActive = $this->isWooCommerceActive();
			$isEddActive         = $this->isEddActive();
			$data['data']       += [
				'isWooCommerceActive' => $isWooCommerceActive,
				'isEddActive'         => $isEddActive
			];

			if ( $isWooCommerceActive ) {
				$data['data']['wooCommerce'] = [
					'currencySymbol'            => function_exists( 'get_woocommerce_currency_symbol' ) ? get_woocommerce_currency_symbol() : '$',
					'isWooCommerceBrandsActive' => $this->isWooCommerceBrandsActive(),
					'isPerfectBrandsActive'     => $this->isPerfectBrandsActive()
				];
			}

			if ( $isEddActive ) {
				$data['data']['edd']['isEddReviewsActive'] = $this->isEddReviewsActive();
			}
		}

		if ( 'settings' === $page ) {
			// Default breadcrumb templates.
			$data['breadcrumbs']['defaultTemplates'] = [];
			$postTypes = aioseo()->helpers->getPublicPostTypes();
			foreach ( $postTypes as $postType ) {
				$data['breadcrumbs']['defaultTemplates']['postTypes'][ $postType['name'] ] =
					aioseo()->helpers->encodeOutputHtml( aioseo()->breadcrumbs->frontend->getDefaultTemplate( 'single', $postType ) );
			}

			$taxonomies = aioseo()->helpers->getPublicTaxonomies();
			foreach ( $taxonomies as $taxonomy ) {
				$data['breadcrumbs']['defaultTemplates']['taxonomies'][ $taxonomy['name'] ] =
					aioseo()->helpers->encodeOutputHtml( aioseo()->breadcrumbs->frontend->getDefaultTemplate( 'taxonomy', $taxonomy ) );
			}

			$data['breadcrumbs']['defaultTemplates']['archives'] = [
				'blog'     => aioseo()->helpers->encodeOutputHtml( aioseo()->breadcrumbs->frontend->getDefaultTemplate( 'blog' ) ),
				'author'   => aioseo()->helpers->encodeOutputHtml( aioseo()->breadcrumbs->frontend->getDefaultTemplate( 'author' ) ),
				'search'   => aioseo()->helpers->encodeOutputHtml( aioseo()->breadcrumbs->frontend->getDefaultTemplate( 'search' ) ),
				'notFound' => aioseo()->helpers->encodeOutputHtml( aioseo()->breadcrumbs->frontend->getDefaultTemplate( 'notFound' ) ),
				'date'     => [
					'year'  => aioseo()->helpers->encodeOutputHtml( aioseo()->breadcrumbs->frontend->getDefaultTemplate( 'year' ) ),
					'month' => aioseo()->helpers->encodeOutputHtml( aioseo()->breadcrumbs->frontend->getDefaultTemplate( 'month' ) ),
					'day'   => aioseo()->helpers->encodeOutputHtml( aioseo()->breadcrumbs->frontend->getDefaultTemplate( 'day' ) )
				]
			];

			$archives = aioseo()->helpers->getPublicPostTypes( false, true, true );
			foreach ( $archives as $archive ) {
				$data['breadcrumbs']['defaultTemplates']['archives']['postTypes'][ $archive['name'] ] =
					aioseo()->helpers->encodeOutputHtml( aioseo()->breadcrumbs->frontend->getDefaultTemplate( 'postTypeArchive', $archive ) );
			}
		}

		return $data;
	}
}