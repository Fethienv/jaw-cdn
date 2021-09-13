<?php
/**
 * Code specific to the widget Popular Posts widget type.
 */

/**
 * Class MonsterInsights_Popular_Posts_Widget
 */
class MonsterInsights_Popular_Posts_Widget extends MonsterInsights_Popular_Posts {

	/**
	 * Number of posts to query from the db. Not all queried posts are used for display in the same widget.
	 *
	 * @var int
	 */
	public $posts_count = 15;

	/**
	 * Categories used to load posts from.
	 *
	 * @var array
	 */
	public $categories = array();

	/**
	 * The instance type. Used for loading specific settings.
	 *
	 * @var string
	 */
	protected $type = 'widget';

	/**
	 * Used to load the setting specific for this class.
	 *
	 * @var string
	 */
	protected $settings_key = 'popular_posts_widget';

	/**
	 * Used for registering the shortcode specific to this class.
	 *
	 * @var string
	 */
	protected $shortcode_key = 'monsterinsights_popular_posts_widget';

	/**
	 * Widget-specific hooks.
	 */
	public function hooks() {
		parent::hooks();

		add_action( 'wp', array( $this, 'maybe_auto_insert' ) );

		add_action( 'widgets_init', array( $this, 'register_widget' ) );
	}


	/**
	 * Register Popular Posts widget.
	 */
	public function register_widget() {
		register_widget( 'MonsterInsights_Popular_Posts_Widget_Sidebar' );
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

		if ( 'curated' === $this->sort && apply_filters( 'monsterinsights_popular_posts_widget_curated_shuffle', true ) ) {
			// Randomize the order.
			shuffle( $posts );
		}

		$theme_styles = $this->get_theme_props( $theme )->get_theme();

		$label_text = '';
		if ( isset( $theme_styles['styles']['label'] ) ) {
			$label_text = isset( $atts['label_text'] ) ? $atts['label_text'] : $theme_styles['styles']['label']['text'];
		}

		$show_author = false;
		if ( isset( $theme_styles['styles']['meta']['author'] ) ) {
			$show_author = 'on' === $this->theme_meta_author;
			if ( isset( $atts['meta_author'] ) ) {
				$show_author = 'on' === $atts['meta_author'];
			}
		}

		$show_date = false;
		if ( isset( $theme_styles['styles']['meta']['date'] ) ) {
			$show_date = 'on' === $this->theme_meta_date;
			if ( isset( $atts['meta_date'] ) ) {
				$show_date = 'on' === $atts['meta_date'];
			}
		}

		$show_comments = false;
		if ( isset( $theme_styles['styles']['meta']['comments'] ) ) {
			$show_comments = 'on' === $this->theme_meta_comments;
			if ( isset( $atts['meta_comments'] ) ) {
				$show_comments = 'on' === $atts['meta_comments'];
			}
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
			$html .= '<h2 class="monsterinsights-widget-popular-posts-widget-title">' . wp_kses_post( $title_text ) . '</h2>';
		}
		$html .= '<ul class="monsterinsights-widget-popular-posts-list">';

		$display_count = 0;
		foreach ( $posts as $post ) {
			$display_count ++;
			if ( $display_count > $limit ) {
				break;
			}
			$this->set_post_shown( $post['id'] );
			$html .= '<li ' . $this->get_element_style( $theme, 'background', $atts ) . '>';
			$html .= '<a href="' . $post['link'] . '">';
			if ( ! empty( $theme_styles['image'] ) && ! empty( $post['image'] ) ) {
				$html .= '<div class="monsterinsights-widget-popular-posts-image">';
				$html .= '<img src="' . $post['image'] . '" srcset=" ' . $post['srcset'] . ' " alt="' . esc_attr( $post['title'] ) . '" />';
				$html .= '</div>';
			}
			$html .= '<div class="monsterinsights-widget-popular-posts-text">';
			if ( isset( $theme_styles['styles']['label'] ) ) {
				$html .= '<span class="monsterinsights-widget-popular-posts-label" ' . $this->get_element_style( $theme, 'label', $atts ) . '>' . $label_text . '</span>';
			}
			$html .= '<span class="monsterinsights-widget-popular-posts-title" ' . $this->get_element_style( $theme, 'title', $atts ) . '>' . $post['title'] . '</span>';
			$html .= '<div class="monsterinsights-widget-popular-posts-meta" ' . $this->get_element_style( $theme, 'meta', $atts ) . '>';
			if ( $show_author ) {
				// Translators: Placeholder for author name.
				$html .= '<span class="monsterinsights-widget-popular-posts-author">' . sprintf( __( 'by %s', 'google-analytics-for-wordpress' ), $post['author_name'] ) . '</span>';
			}
			if ( ! empty( $theme_styles['styles']['meta']['separator'] ) && $show_author && $show_date ) {
				$html .= "<span>" . $theme_styles['styles']['meta']['separator'] . "</span>";
			}
			if ( $show_date ) {
				$html .= '<span class="monsterinsights-widget-popular-posts-date">' . $post['date'] . '</span>';
			}
			$html .= '</div>';
			if ( $show_comments ) {
				$html .= '<span class="monsterinsights-widget-popular-posts-comments" ' . $this->get_element_style( $theme, 'comments', $atts ) . '><svg width="13" height="12" viewBox="0 0 13 12" fill="#393F4C" xmlns="http://www.w3.org/2000/svg" ' . str_replace( 'color', 'fill', $this->get_element_style( $theme, 'comments', $atts ) ) . '><path d="M7.8251 1.25893C8.70332 2.09821 9.14243 3.10714 9.14243 4.28571C9.14243 5.46429 8.70332 6.47321 7.8251 7.3125C6.94689 8.15179 5.8887 8.57143 4.65056 8.57143C3.78674 8.57143 2.98771 8.34821 2.25346 7.90179C1.63439 8.34821 0.993719 8.57143 0.331456 8.57143C0.302662 8.57143 0.273868 8.5625 0.245074 8.54464C0.216279 8.50893 0.194684 8.47321 0.180287 8.4375C0.151493 8.34821 0.158691 8.26786 0.201882 8.19643C0.50422 7.83929 0.763366 7.35714 0.979321 6.75C0.432235 6.01786 0.158691 5.19643 0.158691 4.28571C0.158691 3.10714 0.5978 2.09821 1.47602 1.25893C2.35424 0.419643 3.41242 0 4.65056 0C5.8887 0 6.94689 0.419643 7.8251 1.25893ZM11.7771 10.1786C11.993 10.7857 12.2522 11.2679 12.5545 11.625C12.5977 11.6964 12.6049 11.7768 12.5761 11.8661C12.5473 11.9554 12.4969 12 12.425 12C11.7627 12 11.122 11.7768 10.5029 11.3304C9.7687 11.7768 8.96967 12 8.10585 12C7.18444 12 6.34941 11.7589 5.60076 11.2768C4.85212 10.7946 4.30503 10.1607 3.9595 9.375C4.21865 9.41071 4.449 9.42857 4.65056 9.42857C6.07587 9.42857 7.29241 8.92857 8.30021 7.92857C9.32239 6.91071 9.83349 5.69643 9.83349 4.28571C9.83349 4.08929 9.82629 3.91071 9.81189 3.75C10.6325 4.07143 11.302 4.59821 11.8203 5.33036C12.3386 6.04464 12.5977 6.83929 12.5977 7.71429C12.5977 8.625 12.3242 9.44643 11.7771 10.1786Z"></path></svg> ' . $post['comments'] . '</span>';
			}
			$html .= '</div>';// monsterinsights-widget-popular-posts-text.
			$html .= '</a>';
			$html .= '</li>';
		}

		$html .= '</ul></div><p></p>';// Main div.

		return $html;

	}

	/**
	 * Add widget-specific styles based on theme settings.
	 */
	public function build_inline_styles() {

		$themes = $this->get_themes_styles_for_output();
		$styles = '';

		foreach ( $themes as $theme_key => $theme_styles ) {

			if ( ! empty( $theme_styles['background'] ) ) {
				$styles .= '.monsterinsights-popular-posts-styled.monsterinsights-widget-popular-posts.monsterinsights-widget-popular-posts-' . $theme_key . ' .monsterinsights-widget-popular-posts-list li {';

				if ( ! empty( $theme_styles['background']['color'] ) ) {
					$styles .= 'background-color:' . $theme_styles['background']['color'] . ';';
				}
				if ( ! empty( $theme_styles['background']['border'] ) ) {
					$styles .= 'border-color:' . $theme_styles['background']['border'] . ';';
				}

				$styles .= '}';
			}

			if ( ! empty( $theme_styles['label'] ) ) {
				$styles .= '.monsterinsights-popular-posts-styled.monsterinsights-widget-popular-posts.monsterinsights-widget-popular-posts-' . $theme_key . ' .monsterinsights-widget-popular-posts-label {';

				if ( ! empty( $theme_styles['label']['color'] ) ) {
					$styles .= 'color:' . $theme_styles['label']['color'] . ';';
				}

				if ( ! empty( $theme_styles['label']['background'] ) ) {
					$styles .= 'background-color:' . $theme_styles['label']['background'] . ';';
				}

				$styles .= '}';
			}

			if ( ! empty( $theme_styles['title'] ) ) {
				$styles .= '.monsterinsights-popular-posts-styled.monsterinsights-widget-popular-posts.monsterinsights-widget-popular-posts-' . $theme_key . ' .monsterinsights-widget-popular-posts-list li .monsterinsights-widget-popular-posts-title {';

				if ( ! empty( $theme_styles['title']['color'] ) ) {
					$styles .= 'color:' . $theme_styles['title']['color'] . ';';
				}
				if ( ! empty( $theme_styles['title']['size'] ) ) {
					$styles .= 'font-size:' . $theme_styles['title']['size'] . 'px;';
				}

				$styles .= '}';
			}

			if ( ! empty( $theme_styles['meta'] ) ) {
				$styles .= '.monsterinsights-popular-posts-styled.monsterinsights-widget-popular-posts.monsterinsights-widget-popular-posts-' . $theme_key . ' .monsterinsights-widget-popular-posts-list .monsterinsights-widget-popular-posts-meta span {';

				if ( ! empty( $theme_styles['meta']['color'] ) ) {
					$styles .= 'color:' . $theme_styles['meta']['color'] . ';';
				}
				if ( ! empty( $theme_styles['meta']['size'] ) ) {
					$styles .= 'font-size:' . $theme_styles['meta']['size'] . 'px;';
				}

				$styles .= '}';
			}

			if ( ! empty( $theme_styles['comments'] ) ) {
				$styles .= '.monsterinsights-widget-popular-posts.monsterinsights-popular-posts-styled.monsterinsights-widget-popular-posts-' . $theme_key . ' .monsterinsights-widget-popular-posts-list .monsterinsights-widget-popular-posts-comments {';

				if ( ! empty( $theme_styles['comments']['color'] ) ) {
					$styles .= 'color:' . $theme_styles['comments']['color'] . ';';
				}

				$styles .= '}';
				$styles .= '.monsterinsights-widget-popular-posts.monsterinsights-popular-posts-styled.monsterinsights-widget-popular-posts-' . $theme_key . ' .monsterinsights-widget-popular-posts-list .monsterinsights-widget-popular-posts-comments svg {';

				if ( ! empty( $theme_styles['comments']['color'] ) ) {
					$styles .= 'fill:' . $theme_styles['comments']['color'] . ';';
				}

				$styles .= '}';
			}

			if ( ! empty( $theme_styles['border'] ) ) {
				$styles .= '.monsterinsights-popular-posts-styled.monsterinsights-widget-popular-posts-' . $theme_key . ' .monsterinsights-inline-popular-posts-border {';

				if ( ! empty( $theme_styles['border']['color'] ) ) {
					$styles .= 'border-color:' . $theme_styles['border']['color'] . ';';
				}

				$styles .= '}';
			}

			if ( ! empty( $theme_styles['border']['color2'] ) ) {
				$styles .= '.monsterinsights-popular-posts-styled.monsterinsights-widget-popular-posts-' . $theme_key . ' .monsterinsights-inline-popular-posts-border-2 {';

				$styles .= 'border-color:' . $theme_styles['border']['color2'] . ';';

				$styles .= '}';
			}
		}

		return $styles;
	}

	/**
	 * Check if we should attempt to automatically insert the inline widget.
	 */
	public function maybe_auto_insert() {

		$post_types = $this->post_types;
		if ( ! empty( $post_types ) && is_singular( $post_types ) && $this->automatic ) {
			add_filter( 'the_content', array( $this, 'add_inline_posts_to_content' ) );
		}

	}

	/**
	 * Insert the widget in the content.
	 *
	 * @param string $content The post content.
	 *
	 * @return string
	 */
	public function add_inline_posts_to_content( $content ) {

		if ( $this->is_post_excluded() ) {
			return $content;
		}

		$content .= $this->shortcode_output( array() );

		return $content;
	}

	/**
	 * Add the categories option to the query.
	 *
	 * @return array
	 */
	protected function query_args() {
		$args = parent::query_args();

		if ( ! empty( $this->categories ) ) {
			$args['cat'] = implode( ',', $this->categories );
		}

		return $args;
	}

	/**
	 * Build the query args for the curated option from the settings in the panel.
	 * Override to use the GA option if available & enabled.
	 *
	 * @return array
	 */
	protected function get_query_args_curated() {

		$posts   = $this->curated;
		$post_in = array();

		if ( ! empty( $posts ) && is_array( $posts ) ) {
			foreach ( $posts as $post ) {
				if ( ! empty( $post['id'] ) ) {
					$post_in[] = intval( $post['id'] );
				}
			}
		}

		if ( class_exists( 'MonsterInsights_Popular_Posts_GA' ) && $this->ga ) {
			$popular_posts_ga = MonsterInsights_Popular_Posts_GA::get_instance()->get_popular_posts();
			if ( ! empty( $popular_posts_ga ) ) {
				foreach ( $popular_posts_ga as $popular_post ) {
					if ( ! empty( $popular_post['id'] ) ) {
						$post_in[] = intval( $popular_post['id'] );
					}
				}
			}
		}

		$query_args = array(
			'post__in' => $post_in,
		);

		return $query_args;
	}
}

/**
 * Get the current class in a function.
 *
 * @return MonsterInsights_Popular_Posts_Widget Instance of the current class.
 */
function MonsterInsights_Popular_Posts_Widget() {
	return MonsterInsights_Popular_Posts_Widget::get_instance();
}

MonsterInsights_Popular_Posts_Widget();
