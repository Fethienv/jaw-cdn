<?php

namespace Rehub\Gutenberg\Blocks;

defined('ABSPATH') OR exit;

use Rehub\Gutenberg\Blocks\Basic\Inline_Attributes_Trait;
use WP_REST_Request;
use WP_REST_Server;

abstract class Basic {
	use Inline_Attributes_Trait;

	protected static $index = 0;

	protected $render_index = 1;
	protected $name = 'basic';

	protected $is_rest = false;

	protected $attributes = array();

	protected static $instance = null;

	final public static function instance(){
		static $instance = null;

		if(is_null($instance)) {
			$instance = new static();
		}

		return $instance;
	}

	protected function __construct(){
		add_action('init', array( $this, 'init_handler' ));
		add_filter('rehub/gutenberg/default_attributes', array( $this, 'get_default_attributes' ));

		$this->construct();

	}

	protected function construct(){

	}

	private function __clone(){
	}

	public function __wakeup(){
	}

	public function rest_handler(WP_REST_Request $Request){
		$data = array(
			'rendered' => $this->render_block($Request->get_params(), ''),
		);

		return rest_ensure_response($data);
	}

	public function init_handler(){
		register_block_type('rehub/'.$this->name, array(
			'attributes'      => $this->attributes,
			'render_callback' => array( $this, 'render_block' ),
		));
	}

	public function restHandler(WP_REST_Request $Request){
		$data = array(
			'rendered' => $this->render_block($Request->get_params(), ''),
		);

		return rest_ensure_response($data);
	}


	protected function render($settings, $inner_content){
		return '';
	}

	public function render_block($settings, $inner_content){
		$settings = array_merge(
			$this->array_column_ext($this->attributes, 'default', -1),
			is_array($settings) ? $settings : array()
		);
		ob_start();
		$content = $this->render($settings, $inner_content);

		return strlen($content) ? $content : ob_get_clean();
	}

	protected function array_column_ext($array, $columnkey, $indexkey = null){
		$result = array();
		foreach($array as $subarray => $value) {
			if(array_key_exists($columnkey, $value)) {
				$val = $array[$subarray][$columnkey];
			} else if($columnkey === null) {
				$val = $value;
			} else {
				continue;
			}

			if($indexkey === null) {
				$result[] = $val;
			} else if($indexkey == -1 || array_key_exists($indexkey, $value)) {
				$result[($indexkey == -1) ? $subarray : $array[$subarray][$indexkey]] = $val;
			}
		}

		return $result;
	}

	public function get_default_attributes($attributes) {
		$attributes[$this->name] = $this->attributes;

		return $attributes;
	}
}
