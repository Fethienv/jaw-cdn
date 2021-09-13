<?php

namespace Rehub\Gutenberg;

defined('ABSPATH') OR exit;

final class Assets {
	private static $instance = null;

	/** @return Assets */
	public static function instance(){
		if(is_null(static::$instance)) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	protected $is_rest = false;

	/** @var \stdClass $assets */
	protected $assets = null;

	private function __construct(){
		add_action('enqueue_block_editor_assets', array( $this, 'editor_gutenberg' ));
		//add_action('enqueue_block_assets', array( $this, 'common_assets' ));
		add_action('init', array( $this, 'init' ));
		add_filter('render_block', array( $this, 'guten_render_assets' ), 10, 2); //conditional assets loading

		$this->assets             = new \stdClass();
		$this->assets->path       = __DIR__.'/';
		$this->assets->path_css   = $this->assets->path.'assets/css/';
		$this->assets->path_js    = $this->assets->path.'assets/js/';
		$this->assets->path_image = $this->assets->path.'assets/images/';
		$this->assets->url        = plugins_url('/', __FILE__);
		$this->assets->url_css    = $this->assets->url.'assets/css/';
		$this->assets->url_js     = $this->assets->url.'assets/js/';
		$this->assets->url_image  = $this->assets->url.'assets/images/';

	}

	public function init(){
		$this->is_rest = defined('REST_REQUEST');
		wp_register_style('rh-gutenberg-admin',$this->assets->url_css.'editor.css', array(), '12.6');
		wp_register_style('rhgutslider', $this->assets->url_css . 'slider.css', array(), '1.0');
		wp_register_style('rhgutreviewheading', $this->assets->url_css . 'review-heading.css', array(), '1.0');
		wp_register_style('rhgutcomparison', $this->assets->url_css . 'comparison-table.css', array(), '1.3');
		wp_register_style('rhgutswiper', $this->assets->url_css . 'swiper-bundle.min.css', array(), '1.1');
		wp_register_style( 'rhpb-video',  $this->assets->url_css . 'rhpb-video.css', array(), '1.1' );
		wp_register_style( 'rhpb-lightbox',  $this->assets->url_css . 'simpleLightbox.min.css', array(), '1.0' );
		wp_register_style( 'rhpb-howto',  $this->assets->url_css . 'howto.css', array(), '1.0' );
		wp_register_style( 'rhofferlistingfull',  $this->assets->url_css . 'offerlistingfull.css', array(), '1.1' );
		wp_register_style( 'rhcountdownblock',  $this->assets->url_css . 'countdown.css', array(), '1.0' );
		wp_register_style( 'rhcolortitlebox',  $this->assets->url_css . 'colortitlebox.css', array(), '1.0' );

		wp_register_script('rhgutslider', $this->assets->url_js . 'slider.js', array('jquery'), '1.1');
		wp_register_script('rhgutswiper', $this->assets->url_js . 'swiper-bundle.min.js', array(), true, '1.1');
		wp_register_script('rhgutequalizer', $this->assets->url_js . 'equalizer.js', array(), true, '1.1');	
		wp_register_script( 'rhpb-video',  $this->assets->url_js . 'rhpb-video.js', array(), true, '1.0' );
		wp_register_script( 'rhpb-lightbox',  $this->assets->url_js . 'simpleLightbox.min.js', array(), '1.0' );
		wp_register_script('lazysizes', $this->assets->url_js . 'lazysizes.js', array('jquery'), '5.2');
		wp_register_script( 'gctoggler',  $this->assets->url_js.'toggle.js', array(), '1.1', true );
		wp_register_script( 'rhcountdownblock',  $this->assets->url_js.'countdown.js', array(), '1.0', true );


		wp_register_script(
			'rehub-block-format',
			$this->assets->url_js . 'format.js',
			array('wp-rich-text', 'wp-element', 'wp-editor'),
			null,
			true
		);

		add_action( 'wp_ajax_check_youtube_url', array( $this, 'check_youtube_url') );

        //Add style to blocks
        $inline_css = '.is-style-halfbackground::before {content: "";position: absolute;left: 0;bottom: 0;height: 50%;background-color: white;width:100vw;margin-left: calc(-100vw / 2 + 100% / 2);margin-right: calc(-100vw / 2 + 100% / 2);}.is-style-halfbackground, .is-style-halfbackground img{position:relative; margin-top:0; margin-bottom:0}';
        register_block_style('core/post-featured-image', [
            'name' => 'halfbackground',
            'label' => __('Half white background under image', 'rehub-framework'),
            'inline_style' => $inline_css
        ]);

        //Add style to blocks
        $inline_css = '.is-style-height150{height:150px; overflow:hidden} .is-style-height150 img{object-fit: cover;flex-grow: 0;height: 100% !important;width: 100%;}';
        register_block_style('core/post-featured-image', [
            'name' => 'height150',
            'label' => __('Height 150px', 'rehub-framework'),
            'inline_style' => $inline_css
        ]);

        //Add style to blocks
        $inline_css = '.is-style-height180{height:180px; overflow:hidden} .is-style-height180 img{object-fit: cover;flex-grow: 0;height: 100% !important;width: 100%;}';
        register_block_style('core/post-featured-image', [
            'name' => 'height180',
            'label' => __('Height 180px', 'rehub-framework'),
            'inline_style' => $inline_css
        ]);

        //Add style to blocks
        $inline_css = '.is-style-height230{height:230px; overflow:hidden} .is-style-height230 img{object-fit: cover;flex-grow: 0;height: 100% !important;width: 100%;}';
        register_block_style('core/post-featured-image', [
            'name' => 'height230',
            'label' => __('Height 230px', 'rehub-framework'),
            'inline_style' => $inline_css
        ]);

        //Add style to blocks
        $inline_css = '.is-style-height350{height:350px; overflow:hidden} .is-style-height350 img{object-fit: cover;flex-grow: 0;height: 100% !important;width: 100%;}';
        register_block_style('core/post-featured-image', [
            'name' => 'height350',
            'label' => __('Height 350px', 'rehub-framework'),
            'inline_style' => $inline_css
        ]);

		//Add style to blocks
		$inline_css = '.is-style-rhborderquery > ul > li{border:1px solid #eee; padding:15px;box-sizing: border-box; margin-bottom:1.25em}.is-style-rhborderquery figure{margin-top:0}';
		register_block_style('core/query', [
			'name' => 'rhborderquery',
			'label' => __('Bordered block', 'gutencon'),
			'inline_style' => $inline_css
		]);

		//Add style to blocks
		$inline_css = '.is-style-rhbordernopaddquery > ul > li{border:1px solid #eee; padding:15px;box-sizing: border-box;margin-bottom:1.25em}.editor-styles-wrapper .is-style-rhbordernopaddquery figure.wp-block-post-featured-image, .is-style-rhbordernopaddquery figure.wp-block-post-featured-image{margin:-15px -15px 12px -15px !important}';
		register_block_style('core/query', [
			'name' => 'rhbordernopaddquery',
			'label' => __('No padding for image', 'gutencon'),
			'inline_style' => $inline_css
		]);

		//Add style to blocks
		$inline_css = '.is-style-brdnpaddradius > ul > li{border-radius:8px; padding:15px;box-sizing: border-box;box-shadow:-2px 3px 10px 1px rgb(202 202 202 / 26%);margin-bottom:1.25em}.editor-styles-wrapper .is-style-brdnpaddradius figure.wp-block-post-featured-image, .is-style-brdnpaddradius figure.wp-block-post-featured-image{margin:-15px -15px 12px -15px !important}.is-style-brdnpaddradius figure.wp-block-post-featured-image img{border-radius:8px 8px 0 0}';
		register_block_style('core/query', [
			'name' => 'brdnpaddradius',
			'label' => __('Rounded border box', 'gutencon'),
			'inline_style' => $inline_css
		]);

		//Add style to blocks
		$inline_css = '.is-style-smartscrollposts{overflow-x: auto !important;overflow-y: hidden;white-space: nowrap; -webkit-overflow-scrolling: touch;scroll-behavior: smooth;scroll-snap-type: x mandatory;}.is-style-smartscrollposts > ul{flex-wrap: nowrap !important;}.is-style-smartscrollposts > ul > li{border-radius:8px; padding:15px;box-sizing: border-box;border:1px solid #eee;margin-bottom:1.25em; min-width:230px;display: inline-block;margin: 0 13px 0px 0 !important;white-space: normal !important;scroll-snap-align: start;}.editor-styles-wrapper .is-style-smartscrollposts figure.wp-block-post-featured-image, .is-style-smartscrollposts figure.wp-block-post-featured-image{margin:-15px -15px 12px -15px !important}.is-style-smartscrollposts figure.wp-block-post-featured-image img{border-radius:8px 8px 0 0}.is-style-smartscrollposts::-webkit-scrollbar-track{background-color:transparent;border-radius:20px}.is-style-smartscrollposts::-webkit-scrollbar-thumb{background-color:transparent;border-radius:20px;border:1px solid transparent}.is-style-smartscrollposts:hover::-webkit-scrollbar-thumb{background-color:#ddd;}.is-style-smartscrollposts:hover{scrollbar-color: #ddd #fff;}';
		register_block_style('core/query', [
			'name' => 'smartscrollposts',
			'label' => __('Smart scroll carousel', 'gutencon'),
			'inline_style' => $inline_css
		]);

		//Add style to blocks
		$inline_css = '.is-style-rhelshadow1{box-shadow: 0px 5px 20px 0 rgb(0 0 0 / 3%);}';
		register_block_style('core/group', [
			'name' => 'rhelshadow1',
			'label' => __('Light shadow', 'gutencon'),
			'inline_style' => $inline_css
		]);

		$inline_css = '.is-style-rhelshadow2{box-shadow: 0 5px 21px 0 rgb(0 0 0 / 7%);}';
		register_block_style('core/group', [
			'name' => 'rhelshadow2',
			'label' => __('Middle shadow', 'gutencon'),
			'inline_style' => $inline_css
		]);

		$inline_css = '.is-style-rhelshadow3{box-shadow: 0 5px 23px rgb(188 207 219 / 35%);border-top: 1px solid #f8f8f8;}';
		register_block_style('core/group', [
			'name' => 'rhelshadow3',
			'label' => __('Smooth shadow', 'gutencon'),
			'inline_style' => $inline_css
		]);

	}

	public function check_youtube_url(){
		$url = $_POST['url'];
		$max = wp_safe_remote_head($url);
		wp_send_json_success( wp_remote_retrieve_response_code($max) );
	}

	/**
	 * Enqueue Gutenberg block assets for backend editor.
	 */
	function editor_gutenberg(){
		static $loaded = false;
		if($loaded) {
			return;
		}
		$loaded = true;

		global $pagenow;

		if ( 'widgets.php' !== $pagenow &&  'customize.php' !== $pagenow) {
			//add common editor js
			wp_enqueue_script(
				'rehub-blocks-editor',
				$this->assets->url_js.'editor.js',
				array('wp-api'),
				filemtime($this->assets->path_js.'editor.js'),
				true
			);
			wp_localize_script('rehub-blocks-editor','RehubGutenberg', array(
				'pluginDirUrl' => trailingslashit(plugin_dir_url( __DIR__ )),
				'isRtl' => is_rtl(),
			));

			//initialiation of editor styles are in blocks/video/block.json 
			wp_style_add_data( 'rh-gutenberg-admin', 'rtl', true );

			//add formatting
			wp_enqueue_script( 'rehub-block-format' );

			//add editor block scripts
			wp_enqueue_script(
				'rehub-block-script',
				$this->assets->url_js . 'backend.js',
				array('wp-api'),
				null,
				true
			);
			wp_enqueue_script('lazysizes');
		}

	}

    public function guten_render_assets($html, $block){
        static $renderedrh_styles = [];
		if(isset( $block['blockName'] )){
			if ( $block['blockName'] === 'rehub/comparison-table' ) {
				wp_enqueue_script('rhgutequalizer');
				if(isset( $block['attrs']['responsiveView']) && $block['attrs']['responsiveView'] == 'slide'){
					wp_enqueue_style('rhgutswiper');
					wp_enqueue_script('rhgutswiper');
				}
			}
			if ( $block['blockName'] === 'rehub/slider' ) {
				wp_enqueue_script('rhgutslider');
			}
			if ( $block['blockName'] === 'rehub/offerlistingfull' ) {
				wp_enqueue_script('gctoggler');
			}
			if ( $block['blockName'] === 'rehub/countdown' ) {
				wp_enqueue_script('rhcountdownblock');
			}
			if( $block['blockName'] === 'rehub/video' ){				
				wp_enqueue_script( 'rhpb-video');
				if( $block['attrs']['provider'] === "vimeo" ){
					wp_enqueue_script( 'vimeo-player', 'https://player.vimeo.com/api/player.js', array(), true, '1.0' );
				}				
				if( isset($block['attrs']['overlayLightbox']) && $block['attrs']['overlayLightbox'] ){
					wp_enqueue_style( 'rhpb-lightbox');
					wp_enqueue_script( 'rhpb-lightbox' );
				}
				$width = isset($block['attrs']['width']) ? $block['attrs']['width'] : '';
				$height = isset($block['attrs']['height']) ? $block['attrs']['height'] : '';
				$block_style = "#rhpb-video-" . $block['attrs']['blockId']. "{";
					if(!empty($width) && $width['desktop']['size'] > 0){
						$block_style .= "width: " . $width['desktop']['size'] . $width['desktop']['unit'] .";";
					}
					if(!empty($height) && $height['desktop']['size'] > 0){
						$block_style .= "height: " . $height['desktop']['size'] . $height['desktop']['unit'] .";";
					}
				$block_style .= "} @media (min-width: 1024px) and (max-width: 1140px) {";
				$block_style .= "#rhpb-video-" . $block['attrs']['blockId']. "{";
					if(!empty($width) && $width['landscape']['size'] > 0){
						$block_style .= "width: " . $width['landscape']['size'] . $width['landscape']['unit'] .";";
					}
					if(!empty($height) && $height['landscape']['size'] > 0){
						$block_style .= "height: " . $height['landscape']['size'] . $height['landscape']['unit'] .";";
					}
				$block_style .= "}";
				$block_style .= "} @media (min-width: 768px) and (max-width: 1023px) {";
				$block_style .= "#rhpb-video-" . $block['attrs']['blockId']. "{";
					if(!empty($width) && $width['tablet']['size'] > 0){
						$block_style .= "width: " . $width['tablet']['size'] . $width['tablet']['unit'] .";";
					}
					if(!empty($height) && $height['tablet']['size'] > 0){
						$block_style .= "height: " . $height['tablet']['size'] . $height['tablet']['unit'] .";";
					}
				$block_style .= "}";
				$block_style .= "} @media (max-width: 767px) {";
				$block_style .= "#rhpb-video-" . $block['attrs']['blockId']. "{";
					if(!empty($width) && $width['mobile']['size'] > 0){
						$block_style .= "width: " . $width['mobile']['size'] . $width['mobile']['unit'] .";";
					}
					if(!empty($height) && $height['mobile']['size'] > 0){
						$block_style .= "height: " . $height['mobile']['size'] . $height['mobile']['unit'] .";";
					}
				$block_style .= "} }";
				$html = '<style scoped>'.$block_style.'</style>'.$html;
				//wp_add_inline_style( 'rhpb-video', $block_style );
			}                       
		}

        return $html;
    }
	
	public function common_assets() {
		// conditional scripts
		if(!is_admin()){
			global $post;
			$wp_post = get_post( $post );
			if ( $wp_post ) {
				$content = $wp_post->post_content;
			}else{
				return false;
			}
			$blocks = parse_blocks( $content );
			$this->check_block_array($blocks); //check blocks to inject conditional scripts		
		}
	}

	public function check_block_array($blocks=array()){
		if ( empty( $blocks ) ) return;

		foreach ( $blocks as $block ) {
			if ( $block['blockName'] === 'rehub/comparison-table' ) {
				wp_enqueue_style('rhgutcomparison');
				wp_enqueue_script('rhgutequalizer');
				if(isset( $block['attrs']['responsiveView']) && $block['attrs']['responsiveView'] == 'slide'){
					wp_enqueue_style('rhgutswiper');
					wp_enqueue_script('rhgutswiper');
				}
			}
			if ( $block['blockName'] === 'rehub/slider' ) {
				wp_enqueue_style('rhgutslider');
				wp_enqueue_script('rhgutslider');
			}
			if ( $block['blockName'] === 'rehub/review-heading' ) {
				wp_enqueue_style('rhgutreviewheading');
			}
			if ( $block['blockName'] === 'rehub/howto' ) {
				wp_enqueue_style('rhpb-howto');
			}
			if ( $block['blockName'] === 'rehub/offerlistingfull' ) {
				wp_enqueue_style('rhofferlistingfull');
				wp_enqueue_script('gctoggler');
			}
			if( $block['blockName'] === 'rehub/video' ){
				if( $block['attrs']['provider'] === "vimeo" ){
					wp_enqueue_script( 'vimeo-player', 'https://player.vimeo.com/api/player.js', array(), true, '1.0' );
				}
				wp_enqueue_style( 'rhpb-video' );
				wp_enqueue_script( 'rhpb-video');
				
				if( isset($block['attrs']['overlayLightbox']) && $block['attrs']['overlayLightbox'] ){
					wp_enqueue_style( 'rhpb-lightbox');
					wp_enqueue_script( 'rhpb-lightbox' );
				}
				$width = isset($block['attrs']['width']) ? $block['attrs']['width'] : '';
				$height = isset($block['attrs']['height']) ? $block['attrs']['height'] : '';
				$block_style = "#rhpb-video-" . $block['attrs']['blockId']. "{";
					if(!empty($width) && $width['desktop']['size'] > 0){
						$block_style .= "width: " . $width['desktop']['size'] . $width['desktop']['unit'] .";";
					}
					if(!empty($height) && $height['desktop']['size'] > 0){
						$block_style .= "height: " . $height['desktop']['size'] . $height['desktop']['unit'] .";";
					}
				$block_style .= "} @media (min-width: 1024px) and (max-width: 1140px) {";
				$block_style .= "#rhpb-video-" . $block['attrs']['blockId']. "{";
					if(!empty($width) && $width['landscape']['size'] > 0){
						$block_style .= "width: " . $width['landscape']['size'] . $width['landscape']['unit'] .";";
					}
					if(!empty($height) && $height['landscape']['size'] > 0){
						$block_style .= "height: " . $height['landscape']['size'] . $height['landscape']['unit'] .";";
					}
				$block_style .= "}";
				$block_style .= "} @media (min-width: 768px) and (max-width: 1023px) {";
				$block_style .= "#rhpb-video-" . $block['attrs']['blockId']. "{";
					if(!empty($width) && $width['tablet']['size'] > 0){
						$block_style .= "width: " . $width['tablet']['size'] . $width['tablet']['unit'] .";";
					}
					if(!empty($height) && $height['tablet']['size'] > 0){
						$block_style .= "height: " . $height['tablet']['size'] . $height['tablet']['unit'] .";";
					}
				$block_style .= "}";
				$block_style .= "} @media (max-width: 767px) {";
				$block_style .= "#rhpb-video-" . $block['attrs']['blockId']. "{";
					if(!empty($width) && $width['mobile']['size'] > 0){
						$block_style .= "width: " . $width['mobile']['size'] . $width['mobile']['unit'] .";";
					}
					if(!empty($height) && $height['mobile']['size'] > 0){
						$block_style .= "height: " . $height['mobile']['size'] . $height['mobile']['unit'] .";";
					}
				$block_style .= "} }";
				wp_add_inline_style( 'rhpb-video', $block_style );
			}
			//We check here reusable and inner blocks
			if ( $block['blockName'] === 'core/block' && ! empty( $block['attrs']['ref'] ) ) {
				$post_id = $block['attrs']['ref'];
				$content = get_post_field( 'post_content', $post_id );
				$blocks = parse_blocks( $content );
				$this->check_block_array($blocks);
			}
			if ( !empty($block['innerBlocks'])) {
				$blocks = $block['innerBlocks'];
				$this->check_block_array($blocks);
			}
		}
	}

}