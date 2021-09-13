<?php


namespace Rehub\Gutenberg\Blocks;
defined('ABSPATH') OR exit;


class Toc extends Basic{

	protected $name = 'toc';

	protected $attributes = array(
		'textColor'      => array(
			'type'    => 'string',
			'default' => '',
		),
		'numberColor'      => array(
			'type'    => 'string',
			'default' => '',
		),
		'fontSize'      => array(
			'type'    => 'number',
			'default' => 14,
		),
		'lineHeight'      => array(
			'type'    => 'number',
			'default' => 20,
		),
		'margin'      => array(
			'type'    => 'number',
			'default' => 10,
		),
		'blockId'      => array(
			'type'    => 'string',
			'default' => '',
		),
		'items' => array(
			'type'    => 'array',
			'default' => [],
		),
	);

	protected function render($settings = array(), $inner_content = ''){
		extract($settings);
		$id = 'rh-gut-'.mt_rand();
		$out = '';
		$out .= '<div id="'.$id.'">
		<style scoped>
		#'.$id.' .autocontents li{margin: 0 0 '.$margin.'px 0; font-size: '.$fontSize.'px; line-height:'.$lineHeight.'px}
		#'.$id.' .autocontents li a{
			'.(($textColor) ? "color:".$textColor.";" : "").'
		}
		#'.$id.' .autocontents li:before{
			'.(($numberColor) ? "color:".$numberColor.";" : "").'
		}
		</style>
		'.wpsm_contents_shortcode(array("headers"=>"h2")).'
		</div>';

		return $out;
	}
}