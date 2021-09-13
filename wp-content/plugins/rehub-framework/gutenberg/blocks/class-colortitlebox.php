<?php

namespace Rehub\Gutenberg\Blocks;

defined('ABSPATH') OR exit;

class Colortitlebox{

	final public static function instance(){
		static $instance = null;

		if(is_null($instance)) {
			$instance = new static();
		}

		return $instance;
	}

	protected function __construct(){
		add_action('init', array( $this, 'init_handler' ));
	}

	public function init_handler(){
		register_block_type(__DIR__ . '/colortitlebox');
	}

}