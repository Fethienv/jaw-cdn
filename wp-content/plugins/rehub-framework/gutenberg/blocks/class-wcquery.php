<?php

namespace Rehub\Gutenberg\Blocks;

defined( 'ABSPATH' ) OR exit;

class WCQuery extends Basic {
	protected $name = 'wc-query';

	protected function __construct() {
		$this->action();
		parent::__construct();
	}

	static $fonts = array();

	protected $attributes = array(
		'select_type' => array(
			'type' => 'string',
			'default' => 'custom',
		),
		'cat' => array(
			'type' => 'array',
			'default' => null
		),
		'tag' => array(
			'type' => 'array',
			'default' => null
		),
		'tax_name' => array(
			'type' => 'string',
			'default' => '',
		),
		'tax_slug' => array(
			'type' => 'string',
			'default' => '',
		),
		'tax_slug_exclude' => array(
			'type' => 'string',
			'default' => '',
		),
		'user_id' => array(
			'type' => 'array',
			'default' => null
		),
		'type' => array(
			'type' => 'string',
			'default' => 'recent',
		),
		'ids' => array(
			'type' => 'array',
			'default' => null
		),
		'order' => array(
			'type' => 'string',
			'default' => 'desc',
		),
		'orderby' => array(
			'type' => 'string',
			'default' => 'date',
		),
		'meta_key' => array(
			'type' => 'string',
			'default' => '',
		),
		'show' => array(
			'type' => 'number',
			'default' => 12,
		),
		'offset' => array(
			'type' => 'string',
			'default' => '',
		),
		'enable_pagination' => array(
			'type' => 'string',
			'default' => '0',
		),
	);

	protected function action(){
		add_action( 'wp_ajax_wcquery_taxonomies_list', array( $this, 'wcquery_taxonomies_list' ) );
		add_action( 'wp_ajax_wcquery_taxonomy_terms', array( $this, 'wcquery_taxonomy_terms' ) );
		add_action( 'wp_ajax_wcquery_taxonomy_terms_search', array( $this, 'wcquery_taxonomy_terms_search' ) );
		add_action( 'wp_ajax_wcquery_render_preview', array( $this, 'wcquery_render_preview' ) );
	}

	public function wcquery_taxonomies_list() {
		$exclude_list = array_flip([
			'category', 'post_tag', 'nav_menu', 'link_category', 'post_format',
			'elementor_library_type', 'elementor_library_category', 'action-group'
		]);
		$response_data = [
			'results' => []
		];
    	$args = [];
		foreach ( get_taxonomies($args, 'objects') as $taxonomy => $object ) {
			if ( isset( $exclude_list[ $taxonomy ] ) ) {
				continue;
			}

			$taxonomy = esc_html( $taxonomy );
			$response_data['results'][] = [
				'value'    => $taxonomy,
				'label'  => esc_html( $object->label ),
			];
		}
		wp_send_json_success( $response_data );
	}

	public function wcquery_taxonomy_terms() {
		$response_data = [
			'results' => []
		];
	
		if ( empty( $_POST['taxonomy'] ) ) {
			wp_send_json_success( $response_data );
		}
	
		$taxonomy = sanitize_text_field($_POST['taxonomy']);
		$selected = isset($_POST['selected']) ? $_POST['selected'] : '';
		$terms = get_terms([
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
			'number' => 15,
			'exclude' => $selected
		]);
	
		foreach ( $terms as $term ) {
			$response_data['results'][] = [
				'id'    	=> $term->slug,
				'label'  	=> esc_html( $term->name ),
				'value' 	=> $term->term_id
			];
		}
	
		wp_send_json_success( $response_data );
	}

	public function wcquery_taxonomy_terms_search(){
		global $wpdb;
        $taxonomy = isset($_POST['taxonomy']) ? $_POST['taxonomy'] : '';
        $query = [
            "select" => "SELECT SQL_CALC_FOUND_ROWS a.term_id AS id, b.name as name, b.slug AS slug
                        FROM {$wpdb->term_taxonomy} AS a
                        INNER JOIN {$wpdb->terms} AS b ON b.term_id = a.term_id",
            "where"  => "WHERE a.taxonomy = '{$taxonomy}'",
            "like"   => "AND (b.slug LIKE '%s' OR b.name LIKE '%s' )",
            "offset" => "LIMIT %d, %d"
        ];

        $search_term = '%' . $wpdb->esc_like( $_POST['search'] ) . '%';
        $offset = 0;
        $search_limit = 100;

        $final_query = $wpdb->prepare( implode(' ', $query ), $search_term, $search_term, $offset, $search_limit );
        // Return saved values

        $results = $wpdb->get_results( $final_query );

        $total_results = $wpdb->get_row("SELECT FOUND_ROWS() as total_rows;");
        $response_data = [
            'results'       => [],
        ];

        if ( $results ) {
            foreach ( $results as $result ) {
                $response_data['results'][] = [
					'id'    	=> esc_html( $result->slug ),
					'label'  	=> esc_html( $result->name ),
					'value' 	=> (int)$result->id
                ];
            }
        }

        wp_send_json_success( $response_data );
	}

	public function wcquery_render_preview(){
		$settings = $_POST['settings'];
		$this->normalize_arrays( $settings );

		if ( !empty( $settings['attrpanel'] ) ) {
            $settings['attrelpanel'] = rawurlencode( json_encode( $settings['attrpanel'] ) );
        }

		if ( !empty( $settings['filterpanel'] ) ) {
            $settings['filterpanel'] = $this->filter_values( $settings['filterpanel'] );
            $settings['filterpanel'] = rawurlencode( json_encode( $settings['filterpanel'] ) );
        }

		$preview = wpsm_woogrid_shortcode( $settings );

		wp_send_json_success( $preview );
	}

	protected function normalize_arrays( &$settings, $fields = ['cat', 'tag', 'ids', 'tax_slug', 'tax_slug_exclude', 'user_id'] ) {
        foreach( $fields as $field ) {
			
            if ( ! isset( $settings[ $field ] ) || ! is_array( $settings[ $field ] ) || empty( $settings[ $field ] ) ) {
				$settings[ $field ] = null;
                continue;
            }
			$ids = '';
			$last = count( $settings[ $field ] );
			foreach ($settings[ $field ] as $item ){
				$ids .= $item['id'];
				if (0 !== --$last) {
					$ids .= ',';
				}
			}
            $settings[ $field ] = $ids;
        }
		if(isset( $settings['select_type']) && $settings['select_type'] == 'manual'){
			$settings['data_source'] = 'ids';
		}
    }

	protected function filter_values( $haystack ) {
        foreach ( $haystack as $key => $value ) {
            if ( is_array( $value ) ) {
                $haystack[ $key ] = $this->filter_values( $haystack[ $key ]);
            }

            if ( empty( $haystack[ $key ] ) ) {
                unset( $haystack[ $key ] );
            }
        }
        return $haystack;
    }

	protected function render( $settings = array(), $inner_content = '' ) {
		$this->normalize_arrays( $settings );
		if ( !empty( $settings['attrpanel'] ) ) {
            $settings['attrelpanel'] = rawurlencode( json_encode( $settings['attrpanel'] ) );
        }

		if ( !empty( $settings['filterpanel'] ) ) {
            $settings['filterpanel'] = $this->filter_values( $settings['filterpanel'] );
            $settings['filterpanel'] = rawurlencode( json_encode( $settings['filterpanel'] ) );
        }

		$output = str_replace( "{{ content }}", wpsm_woogrid_shortcode( $settings ), $inner_content );
		
		echo $output;
	}
}