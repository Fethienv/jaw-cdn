<?php
/**
 * Code specific to the Popular Products widget.
 */

/**
 * Class MonsterInsights_Popular_Posts_Products
 */
class MonsterInsights_Popular_Posts_Products extends MonsterInsights_Popular_Posts {
	/**
	 * Number of posts to query from the db. Not all queried posts are used for display in the same widget.
	 *
	 * @var int
	 */
	public $posts_count = 15;

	/**
	 * Used to load the setting specific for this class.
	 *
	 * @var string
	 */
	protected $settings_key = 'popular_posts_products';

	/**
	 * Used for registering the shortcode specific to this class.
	 *
	 * @var string
	 */
	protected $shortcode_key = 'monsterinsights_popular_posts_products';

	/**
	 * The instance type. Used for loading specific settings.
	 *
	 * @var string
	 */
	protected $type = 'products';

	/**
	 * Products-specific hooks.
	 */
	public function hooks() {

		if ( ! $this->requirements() ) {
			return;
		}

		parent::hooks();

		add_action( 'wp', array( $this, 'maybe_auto_insert' ) );

		add_action( 'widgets_init', array( $this, 'register_widget' ) );
	}

	/**
	 * Register Popular Products widget.
	 */
	public function register_widget() {
		register_widget( 'MonsterInsights_Popular_Posts_Products_Sidebar' );
	}

	/**
	 * Check if requirements are met. We need WooCommerce to run the sidebar widget correctly.
	 *
	 * @return bool
	 */
	public function requirements() {

		if ( ! class_exists( 'WooCommerce' ) ) {
			return false;
		}

		// License type has to be pro or higher.
		if ( ! MonsterInsights()->license->license_can( 'pro' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Get the rendered HTML for output.
	 *
	 * @param array $atts These are attributes used to build the specific instance, they can be either shortcode
	 * attributes or Gutenberg block props.
	 *
	 * @return string
	 */
	public function get_rendered_html( $atts ) {

		if ( ! $this->requirements() ) {
			return false;
		}

		$theme = $this->theme;
		if ( ! empty( $atts['theme'] ) ) {
			$theme = $atts['theme'];
		}

		if ( ! empty( $atts['post_count'] ) ) {
			$limit = intval( $atts['post_count'] );
		} else {
			$limit = $this->count;
		}

		if ( ! empty( $atts['categories'] ) ) {
			$this->categories = $atts['categories'];
		}

		$posts = $this->get_posts_to_display();

		if ( empty( $posts ) ) {
			return '';
		}

		$theme_styles = $this->get_theme_props( $theme )->get_theme();

		$show_price = false;
		if ( isset( $theme_styles['styles']['meta']['price'] ) ) {
			$show_price = 'on' === $this->theme_meta_price;
			if ( isset( $atts['meta_price'] ) ) {
				$show_price = 'on' === $atts['meta_price'];
			}
		}

		$show_rating = false;
		if ( isset( $theme_styles['styles']['meta']['rating'] ) ) {
			$show_rating = 'on' === $this->theme_meta_rating;
			if ( isset( $atts['meta_rating'] ) ) {
				$show_rating = 'on' === $atts['meta_rating'];
			}
		}

		$show_image = ! empty( $theme_styles['image'] );
		if ( ! $show_image && ! empty( $theme_styles['styles']['meta']['image'] ) ) {
			$show_image = isset( $atts['meta_image'] ) ? 'on' === $atts['meta_image'] : 'on' === $this->theme_meta_image;
		}

		if ( isset( $atts['widget_title'] ) ) {
			$show_title = (bool) $atts['widget_title'];
			$title_text = empty( $atts['widget_title_text'] ) ? '' : $atts['widget_title_text'];
		} else {
			$show_title = $this->title;
			$title_text = $this->title_text;
		}

		$html = '<div class="' . $this->get_wrapper_class( $atts ) . '">';
		if ( $show_title ) {
			$html .= '<h2 class="monsterinsights-products-popular-posts-title">' . wp_kses_post( $title_text ) . '</h2>';
		}

		$html .= '<ul class="monsterinsights-products-popular-posts-list">';

		$display_count = 0;
		foreach ( $posts as $post ) {
			$display_count ++;
			if ( $display_count > $limit ) {
				break;
			}
			$this->set_post_shown( $post['id'] );
			$html .= '<li ' . $this->get_element_style( $theme, 'background', $atts ) . '>';
			$html .= '<a href="' . $post['link'] . '">';
			if ( $show_image && ! empty( $post['image'] ) ) {
				$html .= '<div class="monsterinsights-products-popular-posts-image">';
				$html .= '<img src="' . $post['image'] . '" srcset=" ' . $post['srcset'] . ' " alt="' . esc_attr( $post['title'] ) . '" />';
				$html .= '</div>';
			}
			$html .= '<div class="monsterinsights-products-popular-posts-text">';

			$html .= '<span class="monsterinsights-products-popular-posts-title" ' . $this->get_element_style( $theme, 'title', $atts ) . '>' . $post['title'] . '</span>';
			if ( $show_price ) {
				$html .= '<span class="monsterinsights-products-popular-posts-price" ' . $this->get_element_style( $theme, 'price', $atts ) . '>' . $post['price'] . '</span>';
			}
			if ( $show_rating ) {
				$rating_color = ! empty( $atts['rating_color'] ) ? $atts['rating_color'] : $theme_styles['styles']['rating']['color'];
				$html         .= '<span class="monsterinsights-products-popular-posts-rating">' . $this->get_rating_stars( $rating_color, $post['rating'] ) . '</span>';
			}
			$html .= '</div>';// monsterinsights-products-popular-posts-text.
			$html .= '</a>';
			$html .= '</li>';
		}

		$html .= '</ul></div><p></p>';// Main div.

		return $html;

	}

	/**
	 * Get SVGs used to display star ratings.
	 *
	 * @param string $color The color used to render the star.
	 * @param float  $rating The rating value that is rounded to grab stars.
	 *
	 * @return string
	 */
	public function get_rating_stars( $color, $rating ) {

		$rating = intval( $rating );

		$stars = array(
			$this->get_rating_star_svg( $color ),
			$this->get_rating_star_svg( $color ),
			$this->get_rating_star_svg( $color ),
			$this->get_rating_star_svg( $color ),
			$this->get_rating_star_svg( $color ),
		);

		foreach ( $stars as $i => $star ) {
			if ( $i < $rating ) {
				$stars[ $i ] = $this->get_rating_star_svg( $color, true );
			}
		}

		$stars_html = implode( '', $stars );

		return $stars_html;

	}

	/**
	 * Returns a SVG full or just outline in the specified color.
	 *
	 * @param string $color The color used to fill the SVG.
	 * @param bool   $full Whether to use a full star or just an outline.
	 *
	 * @return mixed|void
	 */
	public function get_rating_star_svg( $color, $full = false ) {
		$star = '<svg width="14" height="13" viewBox="0 0 14 13" fill="' . $color . '" xmlns="http://www.w3.org/2000/svg"><path d="M12.375 4.53125C12.6875 4.5625 12.8906 4.72656 12.9844 5.02344C13.0781 5.32031 13.0156 5.57812 12.7969 5.79688L10.3125 8.21094L10.8984 11.6328C10.9453 11.9297 10.8438 12.1641 10.5938 12.3359C10.3438 12.5234 10.0859 12.5469 9.82031 12.4062L6.75 10.8125L3.67969 12.4062C3.41406 12.5625 3.15625 12.5469 2.90625 12.3594C2.65625 12.1719 2.55469 11.9297 2.60156 11.6328L3.1875 8.21094L0.703125 5.79688C0.484375 5.57812 0.421875 5.32031 0.515625 5.02344C0.609375 4.72656 0.8125 4.5625 1.125 4.53125L4.54688 4.01562L6.07031 0.921875C6.21094 0.640625 6.4375 0.5 6.75 0.5C7.0625 0.5 7.28906 0.640625 7.42969 0.921875L8.95312 4.01562L12.375 4.53125ZM9.11719 7.8125L11.4609 5.51562L8.20312 5.04688L6.75 2.09375L5.29688 5.04688L2.03906 5.51562L4.38281 7.8125L3.84375 11.0703L6.75 9.52344L9.65625 11.0703L9.11719 7.8125Z"></path></svg>';
		if ( $full ) {
			$star = '<svg width="14" height="13" viewBox="0 0 14 13" fill="' . $color . '" xmlns="http://www.w3.org/2000/svg"><path d="M6.07031 0.921875C6.21094 0.640625 6.4375 0.5 6.75 0.5C7.0625 0.5 7.28906 0.640625 7.42969 0.921875L8.95312 4.01562L12.375 4.53125C12.6875 4.5625 12.8906 4.72656 12.9844 5.02344C13.0781 5.32031 13.0156 5.57812 12.7969 5.79688L10.3125 8.21094L10.8984 11.6328C10.9453 11.9297 10.8438 12.1641 10.5938 12.3359C10.3438 12.5234 10.0859 12.5469 9.82031 12.4062L6.75 10.8125L3.67969 12.4062C3.41406 12.5625 3.15625 12.5469 2.90625 12.3594C2.65625 12.1719 2.55469 11.9297 2.60156 11.6328L3.1875 8.21094L0.703125 5.79688C0.484375 5.57812 0.421875 5.32031 0.515625 5.02344C0.609375 4.72656 0.8125 4.5625 1.125 4.53125L4.54688 4.01562L6.07031 0.921875Z"></path></svg>';
		}

		return apply_filters( 'monsterinsights_popular_posts_product_rating_star', $star );
	}

	/**
	 * Add product-specific properties for the output.
	 *
	 * @return array|mixed|void
	 */
	public function get_posts() {

		$posts = parent::get_posts();

		if ( function_exists( 'wc_get_product' ) ) { // Just being extra careful not to trigger a fatal error.
			foreach ( $posts as $key => $post ) {
				$product                 = wc_get_product( $post['id'] );
				$posts[ $key ]['price']  = $product->get_price_html();
				$posts[ $key ]['rating'] = $product->get_average_rating();
			}
		}

		return apply_filters( 'monsterinsights_popular_posts_products', $posts );

	}

	/**
	 * Inline styles specific to the products instance.
	 *
	 * @return string
	 */
	public function build_inline_styles() {

		$themes = $this->get_themes_styles_for_output();
		$styles = '';

		foreach ( $themes as $theme_key => $theme_styles ) {

			if ( ! empty( $theme_styles['background'] ) ) {
				$styles .= '.monsterinsights-popular-posts-styled.monsterinsights-products-popular-posts.monsterinsights-products-popular-posts-' . $theme_key . ' .monsterinsights-products-popular-posts-list li {';

				if ( ! empty( $theme_styles['background']['color'] ) ) {
					$styles .= 'background-color:' . $theme_styles['background']['color'] . ';';
				}
				if ( ! empty( $theme_styles['background']['border'] ) ) {
					$styles .= 'border-color:' . $theme_styles['background']['border'] . ';';
				}

				$styles .= '}';
			}

			if ( ! empty( $theme_styles['title'] ) ) {
				$styles .= '.monsterinsights-popular-posts-styled.monsterinsights-products-popular-posts.monsterinsights-products-popular-posts-' . $theme_key . ' .monsterinsights-products-popular-posts-list li .monsterinsights-products-popular-posts-title {';

				if ( ! empty( $theme_styles['title']['color'] ) ) {
					$styles .= 'color:' . $theme_styles['title']['color'] . ';';
				}
				if ( ! empty( $theme_styles['title']['size'] ) ) {
					$styles .= 'font-size:' . $theme_styles['title']['size'] . 'px;';
				}

				$styles .= '}';
			}

			if ( ! empty( $theme_styles['price'] ) ) {
				$styles .= '.monsterinsights-popular-posts-styled.monsterinsights-products-popular-posts.monsterinsights-products-popular-posts-' . $theme_key . ' .monsterinsights-products-popular-posts-list li .monsterinsights-products-popular-posts-price {';

				if ( ! empty( $theme_styles['price']['color'] ) ) {
					$styles .= 'color:' . $theme_styles['price']['color'] . ';';
				}
				if ( ! empty( $theme_styles['price']['size'] ) ) {
					$styles .= 'font-size:' . $theme_styles['price']['size'] . 'px;';
				}

				$styles .= '}';
			}
		}

		return $styles;
	}

	/**
	 * Check if we should attempt to automatically insert the inline widget.
	 */
	public function maybe_auto_insert() {

		if ( $this->automatic ) {
			add_action( 'woocommerce_after_single_product', array( $this, 'add_inline_posts_to_content' ) );
		}

	}

	/**
	 * Display the Popular Products widget after the single product.
	 *
	 * @return string
	 */
	public function add_inline_posts_to_content() {
		echo $this->shortcode_output( array() );
	}

	/**
	 * Get the posts to exclude from the query as an array of ids.
	 *
	 * @return array
	 */
	private function get_post_not_in() {
		$posts_to_exclude = $this->exclude_posts;
		$post_ids         = array();
		if ( ! empty( $posts_to_exclude ) ) {
			foreach ( $posts_to_exclude as $exclude_post ) {
				if ( ! empty( $exclude_post['id'] ) ) {
					$post_ids[] = intval( $exclude_post['id'] );
				}
			}
		}

		return $post_ids;
	}

	/**
	 * Use child classes to extend/override the default query args.
	 *
	 * @return array
	 */
	protected function query_args() {
		$args = array(
			'numberposts' => $this->posts_count,
			'post_type'   => 'product',
			'meta_key'    => 'total_sales',
			'orderby'     => 'meta_value_num',
			'order'       => 'DESC',
		);

		if ( ! empty( $this->categories ) ) {
			$args['tax_query'] = array(
				array(
					'terms'    => $this->categories,
					'taxonomy' => 'product_cat',
				),
			);
		}

		$exclude = $this->get_post_not_in();
		if ( ! empty( $exclude ) ) {
			$args['post__not_in'] = $exclude;
		}

		return $args;
	}

}

/**
 * Get the current class in a function.
 *
 * @return MonsterInsights_Popular_Posts_Products Instance of the current class.
 */
function MonsterInsights_Popular_Posts_Products() {
	return MonsterInsights_Popular_Posts_Products::get_instance();
}

MonsterInsights_Popular_Posts_Products();
