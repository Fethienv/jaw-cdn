<?php

namespace Rehub\Gutenberg\Blocks;

defined( 'ABSPATH' ) OR exit;

class Accordion extends Basic {
	protected $name = 'accordion';

	protected $attributes = array(
		'tabs' => array(
			'type'    => 'object',
			'default' => array(
				array(
					'title'   => 'Sample title',
					'content' => 'Sample content'
				)
			),
		),
	);

	protected function render( $settings = array(), $inner_content = '' ) {
		$items_html = '';
		$tabs       = $settings['tabs'];

		if ( empty( $tabs ) ) {
			echo '';
			return null;
		}

		foreach ( $tabs as $tab ) {
			$items_html .= wpsm_accordion_section_shortcode( array( 'title' => $tab['title'] ), $tab['content'] );
		}

		echo wpsm_accordion_main_shortcode( array(), $items_html );
	}
}