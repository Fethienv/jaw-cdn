<?php
namespace AIOSEO\Plugin\Pro\Schema\Graphs;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use AIOSEO\Plugin\Common\Schema\Graphs as CommonGraphs;

/**
 * FAQPage graph class.
 *
 * @since 4.0.13
 */
class FAQPage extends CommonGraphs\WebPage {
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

		$metaData      = aioseo()->meta->metaData->getMetaData();
		$schemaOptions = json_decode( $metaData->schema_type_options );
		if ( empty( $schemaOptions->faq->pages ) ) {
			return [];
		}

		$faqPages = $schemaOptions->faq->pages;
		$data     = [
			'@type'      => 'FAQPage',
			'@id'        => aioseo()->schema->context['url'] . '#faq',
			'url'        => aioseo()->schema->context['url'],
			'inLanguage' => get_bloginfo( 'language' ),
			'isPartOf'   => [ '@id' => trailingslashit( home_url() ) . '#website' ],
			'breadcrumb' => [ '@id' => aioseo()->schema->context['url'] . '#breadcrumblist' ]
		];

		$graphs = [];
		foreach ( $faqPages as $faqPage ) {
			$faqPage = json_decode( $faqPage );
			if ( empty( $faqPage->question ) || empty( $faqPage->answer ) ) {
				continue;
			}

			$graphs[] = [
				'@type'          => 'Question',
				'name'           => $faqPage->question,
				'acceptedAnswer' => [
					'@type' => 'Answer',
					'text'  => $faqPage->answer
				]
			];
		}

		if ( empty( $graphs ) ) {
			return [];
		}

		$data['mainEntity'] = $graphs;
		return $data;
	}
}