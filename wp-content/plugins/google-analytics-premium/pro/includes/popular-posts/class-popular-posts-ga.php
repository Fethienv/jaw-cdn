<?php
/**
 * Order posts by pageviews from GA.
 *
 * @package MonsterInsights
 */

/**
 * Class MonsterInsights_Popular_Posts_GA
 */
class MonsterInsights_Popular_Posts_GA {

	/**
	 * Popular posts as extracted from GA data.
	 *
	 * @var array
	 */
	public $popular_posts;

	/**
	 * Number of posts to grab data for.
	 *
	 * @var int
	 */
	protected $limit = 5;

	/**
	 * Instance of current class.
	 *
	 * @var MonsterInsights_Popular_Posts_GA
	 */
	public static $instance;

	/**
	 * Grab the current instance.
	 *
	 * @return MonsterInsights_Popular_Posts_GA
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;

	}

	/**
	 * MonsterInsights_Popular_Posts_GA constructor.
	 */
	public function __construct() {

		add_action( 'monsterinsights_popular_posts_grab_ga_data', array( $this, 'try_popular_posts_processing' ) );
	}

	/**
	 * This function attempts to get the report data from GA (or cache) and associate it with posts on the site for ordering.
	 */
	public function try_popular_posts_processing() {

		$report_data   = $this->get_popular_posts_report();
		$popular_posts = array();
		$count         = 0;

		if ( isset( $report_data['error'] ) && false === $report_data['error'] && isset( $report_data['data']['popularposts'] ) ) {
			$ga_popular_posts = $report_data['data']['popularposts'];

			foreach ( $ga_popular_posts as $popular_post ) {

				if ( empty( $popular_post['page'] ) ) {
					// Missing page path data.
					continue;
				}

				$slug       = untrailingslashit( $popular_post['page'] );
				$slug_parts = explode( '/', $slug );
				$post_slug  = $slug_parts[ count( $slug_parts ) - 1 ];
				$post_data  = get_page_by_path( $post_slug, OBJECT, array( 'post' ) );// Use array for post type so attachments are excluded.

				if ( is_null( $post_data ) ) {
					// No post found for that path, probably a GA misconfiguration.
					continue;
				}
				$popular_posts[] = array(
					'id'        => $post_data->ID,
					// We're only caching the id here, we can cache others at the popular posts widget level.
					'pageviews' => $popular_post['pageviews'], // Store pageviews count for reference/sorting.
				);
				$count ++;
				if ( $count >= $this->limit ) {
					break;
				}
			}

			if ( ! empty( $popular_posts ) ) {
				$this->save_popular_posts_order( $popular_posts );
			}
		}

		return $popular_posts;

	}

	/**
	 * Save the popular posts as retrieved from GA and parsed to match current site posts.
	 *
	 * @param array $posts Posts to save in the cache.
	 */
	public function save_popular_posts_order( $posts ) {

		$posts      = apply_filters( 'monsterinsights_popular_posts_ga_processed_before_cache', $posts );
		$expiration = strtotime( ' Tomorrow 1am ' ) - ( get_option( 'gmt_offset', 0 ) * HOUR_IN_SECONDS ) - time();
		set_transient( 'monsterinsights_popular_posts_ga_data', $posts, $expiration );

	}

	/**
	 * Grab the popular posts either from cache already processed or from fresh GA data.
	 *
	 * @return array|mixed
	 */
	public function get_popular_posts() {

		if ( ! isset( $this->popular_posts ) ) {
			$popular_posts = $this->get_popular_posts_from_cache();
			if ( false === $popular_posts ) {
				$this->popular_posts = $this->try_popular_posts_processing();
			} else {
				$this->popular_posts = $popular_posts;
			}
		}

		return $this->popular_posts;

	}

	/**
	 * Load the current popular posts from GA data from the cache.
	 *
	 * @return array|mixed
	 */
	public function get_popular_posts_from_cache() {

		return get_transient( 'monsterinsights_popular_posts_ga_data' );

	}

	/**
	 * Grab the popular posts report data using a regular report call.
	 *
	 * @return array|bool
	 */
	public function get_popular_posts_report() {

		if ( is_null( MonsterInsights()->reporting ) ) {
			// We don't have access to the reports, probably frontend. Dispatch a one-time event to update the transient.
			$this->dispatch_report_fetching();

			return false;
		}

		// We do not have a current auth.
		$site_auth = MonsterInsights()->auth->get_viewname();
		$ms_auth   = is_multisite() && MonsterInsights()->auth->get_network_viewname();
		if ( ! $site_auth && ! $ms_auth ) {
			return array(
				'error'   => true,
				'message' => __( 'You must authenticate with MonsterInsights before you can view reports.', 'google-analytics-for-wordpress' ),
			);
		}

		$report_name = 'popularposts';

		/**
		 * @var $report MonsterInsights_Report_PopularPosts The popular posts report object.
		 */
		$report = MonsterInsights()->reporting->get_report( $report_name );

		$start = $report->default_start_date();
		$end   = $report->default_end_date();

		$args = array(
			'start' => $start,
			'end'   => $end,
		);

		if ( monsterinsights_is_pro_version() && ! MonsterInsights()->license->license_can( $report->level ) ) {
			$data = array(
				'success' => false,
				'error'   => 'license_level',
			);
		} else {
			$data = $report->get_data( $args );
		}

		if ( ! empty( $data['success'] ) && ! empty( $data['data'] ) ) {
			return array(
				'error' => false,
				'data'  => $data['data'],
			);
		} else if ( isset( $data['success'] ) && false === $data['success'] && ! empty( $data['error'] ) ) {
			return array(
				'error'   => true,
				'message' => $data['error'],
			);
		}

		return array(
			'error'   => true,
			'message' => __( 'We encountered an error when fetching the report data.', 'google-analytics-for-wordpress' ),
		);
	}

	/**
	 * Schedule a single event to load the GA data async.
	 */
	public function dispatch_report_fetching() {

		if ( false === wp_next_scheduled( 'monsterinsights_popular_posts_grab_ga_data' ) ) {
			wp_schedule_single_event( time(), 'monsterinsights_popular_posts_grab_ga_data' );
		}

	}
}

new MonsterInsights_Popular_Posts_GA();
