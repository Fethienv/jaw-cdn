<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php
//////////////////////////////////////////////////////////////////
// WooCommerce Assets
//////////////////////////////////////////////////////////////////

add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );
add_action('wp_enqueue_scripts', 'rehub_woo_enqueue', 11);
if (!function_exists('rehub_woo_enqueue')){
    function rehub_woo_enqueue() {   

		wp_enqueue_style( 'rehub-woocommerce');

		$disablecartscripts = rehub_option('disable_woo_scripts');

		$ajaxaddtocart = false;
		if ( 'yes' === get_option( 'woocommerce_enable_ajax_add_to_cart' ) && !$disablecartscripts) {
			wp_enqueue_style('rhslidingpanel');
			wp_enqueue_script('rhajaxaddtocart');
			$ajaxaddtocart = true;
		}

		if(rehub_option('woo_quick_view')){
		    wp_enqueue_script ( 'rhquickview'); 
		    wp_localize_script( 'rhquickview', 'quickviewvars', array('templateurl' => get_template_directory_uri(), 'quicknonce' => wp_create_nonce('quickview-nonce') )); 
		}
		if($ajaxaddtocart){
			wp_enqueue_style('rhquantity');
			wp_enqueue_script('rhquantity');			
		}

	    if (defined('wcv_plugin_dir') OR class_exists('WeDevs_Dokan') OR class_exists('WCMp')) {wp_enqueue_style('rhwcvendor');} 
		if(is_singular('product')){
			wp_enqueue_style('rhwoosingle');
		} 
    } 
} 

//////////////////////////////////////////////////////////////////
// Display number products per page.
//////////////////////////////////////////////////////////////////

if(!function_exists('rh_loop_shop_per_page')){
	function rh_loop_shop_per_page( $cols ) {
		if(rehub_option('woo_number') == '16') {
			$cols = 16;
		}
		elseif(rehub_option('woo_number') == '24') {
			$cols = 24;
		}
		elseif(rehub_option('woo_number') == '30') {
			$cols = 30;
		}
		elseif(rehub_option('woo_number') == '36') {
			$cols = 36;
		}	
		else {
			$cols = 12;	
		}	
	  	return $cols;
	}
}
add_filter( 'loop_shop_per_page', 'rh_loop_shop_per_page', 20 );

if(!function_exists('rh_loop_shop_number')){
	function rh_loop_shop_number( ) {
		if(rehub_option('woo_columns') == '4_col'  || rehub_option('woo_columns') == '4_col_side') {
			return 4;
		}
		elseif(rehub_option('woo_columns') == '5_col_side') {
			return 5;
		}	
		else {
			return 3;
		}
	}
}
add_filter( 'loop_shop_columns', 'rh_loop_shop_number', 20 );

//////////////////////////////////////////////////////////////////
// Set 6 related products
//////////////////////////////////////////////////////////////////

add_filter( 'woocommerce_output_related_products_args', 'rh_woo_related_products_args' );
function rh_woo_related_products_args( $args ) {
	$args['posts_per_page'] = 6;
	return $args;
}


//////////////////////////////////////////////////////////////////
// Woo hook customization
/////////////////////////////////////////////////////////////

add_action('woocommerce_before_shop_loop', 'rehub_woocommerce_wrapper_start3', 33);
function rehub_woocommerce_wrapper_start3() {
  echo '<div class="clear"></div>';
}
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
remove_action( 'woocommerce_external_add_to_cart', 'woocommerce_external_add_to_cart', 30 );
remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );

add_action( 'woocommerce_external_add_to_cart', 'rh_woo_external_add_to_cart', 30 );
add_action( 'woocommerce_checkout_before_order_review_heading', 'rehub_woo_order_checkout' );
add_action( 'woocommerce_checkout_after_order_review', 'rehub_woo_after_order_checkout' );
add_action( 'woocommerce_before_add_to_cart_button', 'rehub_woo_countdown' );
add_action( 'woocommerce_product_query', 'rh_change_product_query', 99 ); //Here we change and extend product loop data
add_action( 'woocommerce_before_checkout_form', 'rehub_woo_style_checkout' );
add_action( 'woocommerce_before_cart', 'rehub_woo_style_cart' );
add_action( 'woocommerce_after_cart_table', 'woocommerce_cross_sell_display' );

if(!rehub_option('woo_btn_inner_disable')){
	add_action('rhwoo_template_single_add_to_cart', 'woocommerce_template_single_add_to_cart');
}
if ( ! function_exists( 'rh_woo_external_add_to_cart' ) ) {
	function rh_woo_external_add_to_cart() {
		global $product;
		wc_get_template( 'single-product/add-to-cart/external.php', array(
			'product_url' => $product->add_to_cart_url(),
			'button_text' => $product->single_add_to_cart_text(),
		) );
	}
}

add_filter( 'woocommerce_breadcrumb_defaults', 'rh_change_breadcrumb_delimiter' );
function rh_change_breadcrumb_delimiter( $defaults ) {
	// Change the breadcrumb delimeter from '/' to '>'
	$classicon = (is_rtl()) ? 'rhi-angle-left' : 'rhi-angle-right'; 
	$defaults['delimiter'] = '<span class="delimiter"><i class="rhicon '.$classicon.'"></i></span>';
	return $defaults;
}
add_filter( 'subcategory_archive_thumbnail_size', 'rh_archive_thumbnail_size' );
function rh_archive_thumbnail_size(){
	return 'woocommerce_gallery_thumbnail';
}

//Change position of YITH Buttons
if ( defined( 'YITH_WCWL' )){
	add_filter('yith_wcwl_positions', 'rh_wishlist_change_position');
	function rh_wishlist_change_position($so_array=array()){
        $so_array   =   array(
            "shortcode" => array('hook'=>'shortcode', 'priority'=>0),
            "add-to-cart"=> array('hook'=>'shortcode', 'priority'=>0),
            "thumbnails"=> array('hook'=>'shortcode', 'priority'=>0),
            "summary"=> array('hook'=>'shortcode', 'priority'=>0),
        );
	    return $so_array;
	}	
}

// Display Only 3 Cross Sells instead of default 4
add_filter( 'woocommerce_cross_sells_total', 'rh_change_cross_sells_product_no' );
function rh_change_cross_sells_product_no( $columns ) {
return 3;
}


function rehub_woo_order_checkout() {
	echo '<div class="re_woocheckout_order">';
}
function rehub_woo_after_order_checkout() {
	echo '</div><div class="clearfix"></div>';
}
function rehub_woo_style_checkout() {
	echo rh_generate_incss('woocheckout');
}
function rehub_woo_style_cart() {
	echo rh_generate_incss('woocart');
}
function rehub_woo_wcv_before_dash() {
	echo '<div class="rh_wcv_dashboard_page">';
}
function rehub_woo_wcv_after_dash() {
	echo '</div>';
}

if (!function_exists('rh_woo_code_zone')){
function rh_woo_code_zone($zone='button'){
	if($zone == 'button'){
		global $post;
        $code_incart = get_post_meta($post->ID, 'rh_code_incart', true );
        if ( !empty($code_incart)) {
            echo '<div class="rh_woo_code_zone_button">';
            echo do_shortcode($code_incart);
            echo '</div>';
        }else{
	    	$code_incart = rehub_option('woo_code_zone_button');
	    	if ( !empty($code_incart)) {
	        	echo '<div class="rh_woo_code_zone_button">';
	        	echo do_shortcode($code_incart);
	        	echo '</div>'; 
	        }
        }      		
	}elseif($zone =='content'){	
		global $post;
		$code_in_content = get_post_meta($post->ID, 'rehub_woodeals_short', true );
	    if(!empty ($code_in_content)){
	    	echo '<div class="rh_woo_code_zone_content">';
	    		echo do_shortcode($code_in_content);
	    	echo '</div>';
	    }else{
	    	$code_in_content = rehub_option('woo_code_zone_content');
	    	if ( !empty($code_in_content)) {
		    	echo '<div class="rh_woo_code_zone_content">';
		    		echo do_shortcode($code_in_content);
		    	echo '</div>';
	    	}	    	
	    }    		
	}
	elseif($zone =='bottom'){		
		global $post;
		$code_bottom = get_post_meta($post->ID, 'woo_code_zone_footer', true );
	    if(!empty ($code_bottom)){
	    	echo '<div class="rh_woo_code_zone_bottom">';
	    		echo do_shortcode($code_bottom);
	    	echo '</div>';
	    }else{
	    	$code_bottom = rehub_option('woo_code_zone_footer');
	    	if ( !empty($code_bottom)) {
		    	echo '<div class="rh_woo_code_zone_bottom">';
		    		echo do_shortcode($code_bottom);
		    	echo '</div>';
	    	}		    	
	    } 	
	}	
	elseif($zone =='float'){
    	$code_float = rehub_option('woo_code_zone_float');
    	if ( !empty($code_float)) {
	    	echo '<div class="rh_woo_code_zone_float">';
	    		echo do_shortcode($code_float);
	    	echo '</div>';
    	}		    			
	}	
} 
} 

if(!function_exists('rh_wooattr_code_loop')){
	function rh_wooattr_code_loop($attrelpanel=''){
		if($attrelpanel){
			$attrelpanel = (array) json_decode( urldecode( $attrelpanel ), true );
			echo '<div class="woo_code_zone_loop clearbox">';
				foreach ($attrelpanel as $item) {
					$atts = array();
					if(!empty($item['attrkey'])){
						$atts['type'] = $item['attrtype'];
						$atts['attrfield']=$item['attrkey'];
						$atts['label']=$item['attrlabel'];
						$atts['posttext']=$item['attrposttext'];
						$atts['show_empty']=$item['attrshowempty'];
						$atts['labelclass']='mr5 rtlml5';
				        if(!empty($item['icon']) && is_array($item['icon'])){
				            $atts['icon'] = $item['icon']['value'].' mr5 rtlml5';
				        }						
						echo '<div class="woo_code_loop_item font90">';
						echo wpsm_get_custom_value($atts);
						echo '</div>';
					}
				}
			echo '</div>';
		}else{
			$loop_code_zone = rehub_option('woo_code_zone_loop');        
    		if ($loop_code_zone){
    			echo '<div class="woo_code_zone_loop clearbox">'.do_shortcode($loop_code_zone).'</div>';
    		}
		}
	}
}

//////////////////////////////////////////////////////////////////
// Woo Tab customization
/////////////////////////////////////////////////////////////

if (!function_exists('woo_ce_video_output')){
function woo_ce_video_output(){
	echo do_shortcode('[content-egg module=Youtube template=custom/slider]' );
}}

if (!function_exists('woo_ce_news_output')){
function woo_ce_news_output(){
	echo do_shortcode('[content-egg module=GoogleNews template=custom/grid]' );
}}

if (!function_exists('woo_ce_history_output')){
function woo_ce_history_output(){
	echo do_shortcode('[content-egg-block template=custom/all_pricehistory_full]' );
}}
if (!function_exists('woo_ce_pricelist_output')){
function woo_ce_pricelist_output(){
	echo do_shortcode('[content-egg-block template=custom/all_offers_logo_group]' );
}}

if (!function_exists('woo_photo_booking_out')){
function woo_photo_booking_out(){
	global $product;	
	$attachment_ids = $product->get_gallery_image_ids();
	$galleryids = implode(',', $attachment_ids);
	echo '<div class="rh-woo-section-title"><h2 class="rh-heading-icon">'.__('Photos', 'rehub-theme').': <span class="rh-woo-section-sub">'.get_the_title().'</span></h2></div>';
	echo rh_get_post_thumbnails(array('galleryids' => $galleryids, 'columns' => 4, 'height' => 150));
}}

if (!function_exists('woo_cevideo_booking_out')){
function woo_cevideo_booking_out(){
	echo '<div class="rh-woo-section-title"><h2 class="rh-heading-icon">'.__('Videos', 'rehub-theme').': <span class="rh-woo-section-sub">'.get_the_title().'</span></h2></div>';	
	echo do_shortcode('[content-egg module=Youtube template=custom/slider]' );
}}

add_filter( 'woocommerce_product_tabs', 'woo_custom_video_tab', 98 );
function woo_custom_video_tab( $tabs ) {
	global $post;
	$post_image_videos = get_post_meta( $post->ID, 'rh_product_video', true );
	if(!empty($post_image_videos)){
	    $tabs['woo-custom-videos'] = array(
	        'title' => esc_html__('Videos', 'rehub-theme'),
	        'priority' => '21',
	        'callback' => 'woo_custom_video_output',
	    );		
	}
	return $tabs;
}

if (!function_exists('rehub_woo_countdown')){
function rehub_woo_countdown($label = true){
	global $post;
	$endshedule = get_post_meta($post->ID, '_sale_price_dates_to', true );	
	if($endshedule){
		$endshedule = date_i18n( 'Y-m-d', $endshedule );
		$countdown = explode('-', $endshedule);
		$year = $countdown[0];
		$month = $countdown[1];
		$day  = $countdown[2];
		$startshedule = get_post_meta($post->ID, '_sale_price_dates_from', true );
		if ($startshedule){			
			$startshedule = strtotime(date_i18n( 'Y-m-d', $startshedule )); 
			$current = time();
			if($startshedule > $current){
				return;
			}
		}
		echo '<div class="clearbox">';
		if($label !='no') {echo '<div class="rehub-main-color mb10"><i class="rhicon rhi-bolt mr5 ml5 orangecolor font120 fastShake" aria-hidden="true"></i> '.__('Flash Sale', 'rehub-theme').'</div>';}
		echo wpsm_countdown(array('year'=> $year, 'month'=>$month, 'day'=>$day));
		echo '</div>';
	}
	else {
		$rehub_woo_expiration = get_post_meta( $post->ID, 'rehub_woo_coupon_date', true );
		if ($rehub_woo_expiration){
			$countdown = explode('-', $rehub_woo_expiration);
			$year = $countdown[0];
			$month = $countdown[1];
			$day  = $countdown[2];
			echo '<div class="clearbox">';
			if($label !='no') {echo '<div class="rehub-main-color mb10"><i class="rhicon rhi-bolt mr5 ml5 orangecolor font120 fastShake" aria-hidden="true"></i> '.__('Flash Sale', 'rehub-theme').'</div>';}				
			echo wpsm_countdown(array('year'=> $year, 'month'=>$month, 'day'=>$day));
			echo '</div>';		
		}
	}	
} 
} 

if (!function_exists('rh_woo_product_td')){
	function rh_woo_product_td(){
		global $post;
		$model = get_post_meta($post->ID, 'rh_td_model', true);
		$model_usdz = get_post_meta($post->ID, 'rh_td_model_usdz', true);
		if($model){
			$model = trim($model);
			$model_usdz = trim($model_usdz);
			echo '<div id="rh-model-td-trigger" data-popup="triggerModelPopup" class="csspopuptrigger cursorpointer mb25 text-center zind2"><div class="rh-shadow3 roundborder whitebg pr10 pl10 pt5 pb5 inlinestyle"><div class="rh-flex-center-align rh-flex-justify-center"><svg id="rh-model-td-icon" enable-background="new 0 0 512 512" height="30" viewBox="0 0 512 512" width="30" xmlns="http://www.w3.org/2000/svg"><g><g><path d="m331.293 415.258c-1.641-8.115 3.604-16.025 11.733-17.681 82.53-16.714 138.974-55.517 138.974-96.577 0-23.628-19.604-47.183-53.397-66.328-7.207-4.087-9.741-13.242-5.654-20.449 4.072-7.178 13.213-9.697 20.449-5.654 43.593 24.697 68.602 57.524 68.602 92.431 0 56.602-64.604 106.055-163.026 125.991-7.992 1.646-16.011-3.532-17.681-11.733z" fill="#c7cfe1"/></g><g><path d="m194.081 432.104c-115.488-14.854-194.081-67.544-194.081-131.104 0-34.907 25.009-67.734 68.603-92.432 7.251-4.043 16.377-1.523 20.449 5.654 4.087 7.207 1.553 16.362-5.654 20.449-33.794 19.147-53.398 42.701-53.398 66.329 0 46.289 71.195 88.901 167.919 101.338 8.218 1.069 14.019 8.584 12.964 16.802-1.012 7.795-7.998 13.942-16.802 12.964z" fill="#dfe7f4"/></g><g><path d="m382.709 254.418c5.083-2.549 8.291-7.734 8.291-13.418v-150c0-2.842-.802-5.559-2.239-7.885l-57.768-.461-74.993 68.346-30 101.684 30 63.316c2.375 0 4.667-.562 6.709-1.582z" fill="#6aa9ff"/><path d="m123.239 83.116c-1.437 2.325-2.239 5.042-2.239 7.884v150c0 5.685 3.21 10.869 8.291 13.418l120 60c2.124 1.062 4.426 1.582 6.709 1.582v-165l-56.769-59.028z" fill="#80bfff"/></g><path d="m294.32 408.52-38.32-25.547-51.68-34.453c-4.614-3.047-10.547-3.369-15.396-.747-4.877 2.607-7.924 7.69-7.924 13.227v120c0 5.537 3.047 10.62 7.925 13.228 4.81 2.58 10.745 2.353 15.396-.747l51.679-34.453 38.32-25.547c4.175-2.783 6.68-7.471 6.68-12.48s-2.505-9.698-6.68-12.481z" fill="#dfe7f4"/><path d="m301 421c0-5.01-2.505-9.697-6.68-12.48l-38.32-25.547v76.055l38.32-25.547c4.175-2.784 6.68-7.471 6.68-12.481z" fill="#c7cfe1"/><path d="m382.709 77.582-120-60c-2.109-1.054-4.409-1.582-6.709-1.582s-4.6.527-6.709 1.582l-120 60c-2.542 1.274-4.614 3.208-6.052 5.533l132.761 67.885 132.761-67.885c-1.438-2.325-3.511-4.258-6.052-5.533z" fill="#9df"/><path d="m382.709 77.582-120-60c-2.109-1.054-4.409-1.582-6.709-1.582v135l132.761-67.885c-1.438-2.325-3.511-4.258-6.052-5.533z" fill="#80bfff"/></g></svg><span class="ml10 mr10 bluecolor">'.esc_html__("View in your space", "rehub-theme").'</div></div></div>';
			$script = '
			const body = document.body;
			var loadedtd = false;
			const triggerclicktd = document.querySelector("#rh-model-td-trigger");
		
			const onInteraction = () => {
				if (loadedtd === true) {
				return;
				}
				loadedtd = true;

				body.insertAdjacentHTML("beforeend", `<div class="csspopup" id="triggerModelPopup"><div class="csspopupinner"><span class="cpopupclose cursorpointer lightgreybg rh-close-btn rh-flex-center-align rh-flex-justify-center rh-shadow5 roundborder">×</span><div id="divTdForPopup" style="height:500px"><style>#divTdForPopup :not(:defined) > :not(.poster) {display: none;}</style><model-viewer id="modelTdForPopup" src="'.esc_url($model).'" ios-src="'.esc_url($model_usdz).'" environment-image="neutral" ar camera-controls auto-rotate style="width:100%;height:100%;background:#f1f1f1;--poster-color: transparent;--progress-mask:transparent;--progress-bar-color: #00ab1985"><select id="selectTdForPopup" class="rhhidden ml10 mt10 border-grey"></select><button slot="ar-button" class="ar-button pb5 pl15 pr15 pt5 whitebg rh-flex-center-align rh-flex-justify-center" style="position: absolute;left: 50%;transform: translateX(-50%);white-space: nowrap;bottom: 16px;font-size: 14px;border-radius: 18px;border: 1px solid #DADCE0;color:cornflowerblue;">
				<svg height="25" viewBox="0 0 60 54" width="25" class="mr10"><g fill="none" fill-rule="evenodd"><g fill="rgb(0,0,0)" fill-rule="nonzero"><path d="m53 0h-46c-3.86416566.00440864-6.99559136 3.13583434-7 7v40c.00440864 3.8641657 3.13583434 6.9955914 7 7h46c3.8641657-.0044086 6.9955914-3.1358343 7-7v-40c-.0044086-3.86416566-3.1358343-6.99559136-7-7zm5 47c-.0033061 2.7600532-2.2399468 4.9966939-5 5h-46c-2.76005315-.0033061-4.99669388-2.2399468-5-5v-40c.00330612-2.76005315 2.23994685-4.99669388 5-5h46c2.7600532.00330612 4.9966939 2.23994685 5 5z"/><path d="m53 8h-46c-1.65685425 0-3 1.34314575-3 3v36c0 1.6568542 1.34314575 3 3 3h46c1.6568542 0 3-1.3431458 3-3v-36c0-1.65685425-1.3431458-3-3-3zm-23 19.864-10.891-5.864 10.891-5.864 10.891 5.864zm12-4.19v11.726l-11 5.926v-11.726zm-13 5.926v11.726l-11-5.926v-11.726zm-23-18.6c0-.5522847.44771525-1 1-1h22v4.4l-12.474 6.72c-.013.007-.028.01-.041.018-.3023938.1816727-.4866943.5092336-.485.862v8.382l-10 5zm48 36c0 .5522847-.4477153 1-1 1h-46c-.55228475 0-1-.4477153-1-1v-9.382l10-5v3.382c.000193.3677348.2022003.7056937.526.88l13 7c.2959236.1593002.6520764.1593002.948 0l13-7c.3237997-.1743063.525807-.5122652.526-.88v-3.382l10 5zm0-11.618-10-5v-8.382c-.0001367-.3517458-.1850653-.6775544-.487-.858-.013-.008-.028-.011-.041-.018l-12.472-6.724v-4.4h22c.5522847 0 1 .4477153 1 1z"/><circle cx="6" cy="5" r="1"/><circle cx="10" cy="5" r="1"/><circle cx="14" cy="5" r="1"/><path d="m39 6h14c.5522847 0 1-.44771525 1-1s-.4477153-1-1-1h-14c-.5522847 0-1 .44771525-1 1s.4477153 1 1 1z"/></g></g></svg> '.esc_html__("Try in AR", "rehub-theme").'
				</button></model-viewer></div></div></div>`);

				const modelviewerscript = document.createElement("script"); 
				modelviewerscript.type = "module";
				modelviewerscript.src = "'.get_template_directory_uri() . '/js/model-viewer.min.js"; 
				body.appendChild(modelviewerscript);
		
				const focusVisible = document.createElement("script");
				focusVisible.src = "'.get_template_directory_uri() . '/js/focus-visible.js"; 
				body.appendChild(focusVisible);

				const modelViewer = body.querySelector("#modelTdForPopup");
				const select =  modelViewer.querySelector("#selectTdForPopup");

				modelViewer.addEventListener("load", () => {
					const names = modelViewer.availableVariants;
					if(typeof names !=="undefined" && names.length > 0) {
						select.classList.remove("rhhidden");
						for (const name of names) {
							const option = document.createElement("option");
							option.value = name;
							option.textContent = name;
							select.appendChild(option);
						}
					}
				});
				select.addEventListener("input", (event) => {
					modelViewer.variantName = event.target.value;
				});
			};
		
			triggerclicktd.addEventListener("click", onInteraction, {once:true});

	
			';
			wp_add_inline_script('rehub', $script);
		}		
	}
}
add_action('rh_woo_after_single_image','rh_woo_product_td' );

function rh_show_gmw_form_before_wcvendor(){
	if (function_exists('gmw_member_location_form') ) {
		echo rh_add_map_gmw();
		echo '<div class="mb25"></div>';
	}
}


//////////////////////////////////////////////////////////////////
// Woo default thumbnail
//////////////////////////////////////////////////////////////////
add_filter('woocommerce_placeholder_img_src', 'rehub_woocommerce_placeholder_img_src');
function rehub_woocommerce_placeholder_img_src( $src ) {
  	$src = get_template_directory_uri() . '/images/default/wooproductph.png';
	return $src;
}

add_filter( 'woocommerce_gallery_image_size', 'rh_custom_image_gallery_size' );
function rh_custom_image_gallery_size($size){
	return 'full';
}

//////////////////////////////////////////////////////////////////
// Woo update cart in header
//////////////////////////////////////////////////////////////////
if ((rehub_option('rehub_header_style') =='header_seven' || rehub_option('rehub_header_style') =='header_five') && rehub_option('header_seven_cart') != ''){
	add_filter('woocommerce_add_to_cart_fragments', 'woocommerce_header_add_to_cart_fragment');
	if( !function_exists('woocommerce_header_add_to_cart_fragment') ) { 
	function woocommerce_header_add_to_cart_fragment( $fragments ) {
		global $woocommerce;
		ob_start();
		?>		
			<a class="rh-flex-center-align rh-header-icon rh_woocartmenu-link cart-contents cart_count_<?php echo ''.$woocommerce->cart->cart_contents_count; ?>" href="<?php echo wc_get_cart_url(); ?>"><span class="rh_woocartmenu-icon"><span class="rh-icon-notice rehub-main-color-bg"><?php echo ''.$woocommerce->cart->cart_contents_count;?></span></span><span class="rh_woocartmenu-amount"><?php echo ''.$woocommerce->cart->get_total();?></span></a>
		<?php
		$fragments['a.cart-contents'] = ob_get_clean();
		return $fragments;
	}
	}	
}

//////////////////////////////////////////////////////////////////
// Woo quantity
//////////////////////////////////////////////////////////////////

if(rehub_option('woo_wholesale')){
	add_filter( 'woocommerce_loop_add_to_cart_link', 'rh_inputs_for_woocommerce_loop_add_to_cart_link', 10, 2 );
	function rh_inputs_for_woocommerce_loop_add_to_cart_link( $html, $product ) {
		if ( 'yes' === get_option( 'woocommerce_enable_ajax_add_to_cart' ) && $product && $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock() && ! $product->is_sold_individually() ) {
			$html = '<form action="' . esc_url( $product->add_to_cart_url() ) . '" class="rh-loop-quantity wooloopq cart" method="post" enctype="multipart/form-data">';
			$html .= '<div class="mb10 rh-flex-center-align rh-flex-justify-center rh-woo-quantity">'.rehub_cart_quantity_input(array('mb'=> 'mb0'), $product, false).'</div>';
			$html .= sprintf( '<a href="%s" data-product_id="%s" data-product_sku="%s" class="re_track_btn woo_loop_btn btn_offer_block %s %s product_type_%s"%s %s>%s</a>',
			esc_url( $product->add_to_cart_url() ),
			esc_attr( $product->get_id() ),
			esc_attr( $product->get_sku() ),
			$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
			$product->supports( 'ajax_add_to_cart' ) ? 'ajax_add_to_cart' : '',
			esc_attr( $product->get_type() ),
			$product->get_type() =='external' ? ' target="_blank"' : '',
			$product->get_type() =='external' ? ' rel="nofollow sponsored"' : '',
			esc_html( $product->add_to_cart_text() ), $product );
			$html .= '</form>';
		}
		return $html;
	}
}

//////////////////////////////////////////////////////////////////
// Custom Editor Review, User ratings, Pros and Cons fields
//////////////////////////////////////////////////////////////////

if(!function_exists('rh_woo_get_editor_rating')){
	function rh_woo_get_editor_rating(){
		global $post;
		$editor_rating = get_post_meta($post->ID, 'rehub_review_overall_score', true);
		if (!$editor_rating) return;
		if($editor_rating > 0){
			$html = '<div class="rh_woo_star" title="'.sprintf( esc_html__( 'Rated %s out of', 'rehub-theme' ), esc_html( (float)$editor_rating )).' 10">';
			$editor_rating = round($editor_rating/2, 2);
			for ($i = 1; $i <= 5; $i++){
		    	if ($i <= $editor_rating){
		    		$active = ' active';
		    	}else{
		    		$half = $i - 0.5;
		    		if($half <= $editor_rating){
			    		$active = ' halfactive';		    			
		    		}else{
		    			$active ='';
		    		}
		    	}
		        $html .= '<span class="rhwoostar rhwoostar'.$i.$active.'">&#9733;</span>';
			}
			$html .= '</div>';
			return $html;
		}		
	}
}

add_filter( 'woocommerce_product_get_rating_html', 'rh_woo_rating_icons_wrapper', 10, 3 );
add_filter( 'woocommerce_get_star_rating_html', 'rh_woo_rating_icons_html', 10, 3);
function rh_woo_rating_icons_wrapper($html, $rating, $count){
	if ( 0 < $rating ) {
		$html  = '<div class="rh_woo_star" title="'.sprintf( esc_html__( 'Rated %s out of', 'rehub-theme' ), esc_html( (float)$rating )).' 5">';
		$html .= wc_get_star_rating_html( $rating, $count );
		$html .= '</div>';
	} else {
		$html = '';
	}
	return $html;	
}
function rh_woo_rating_icons_html($html, $rating, $count){
	$html = '';
	if($rating > 0){
		$rating = round($rating, 2);
		for ($i = 1; $i <= 5; $i++){
	    	if ($i <= $rating){
	    		$active = ' active';
	    	}else{
	    		$half = $i - 0.5;
	    		if($half <= $rating){
		    		$active = ' halfactive';		    			
	    		}else{
	    			$active ='';
	    		}
	    	}
	        $html .= '<span class="rhwoostar rhwoostar'.$i.$active.'">&#9733;</span>';
		}
	}
	return $html;
}

add_filter( 'woocommerce_structured_data_product', 'rh_woo_editor_schema', 10, 2 );
function rh_woo_editor_schema($markup, $product){
	global $post;
	$editor_rating = get_post_meta($product->get_id(), 'rehub_review_overall_score', true);

	if($editor_rating){
		$heading = get_post_meta($product->get_id(), '_review_heading', true);
		$summary = get_post_meta($product->get_id(), '_review_post_summary_text', true);
		$author_data = get_userdata($post->post_author);
		$markup['review'] = array(
			'@type'       => 'Review',
			"reviewRating" => array(
				"@type" => "Rating",
				"worstRating" => "1",
				"bestRating" => "10",
				"ratingValue" => round($editor_rating, 1),
			),
		    "author" => array(
		      "@type" => "Person",
		      "name" => $author_data->display_name,
		    ),								
		);
		if($summary){
			$markup['review']['reviewBody'] = $summary;
		}	
		if($heading){
			$markup['review']['name'] = $heading;
		}
		if ( get_option( 'woocommerce_enable_review_rating' ) === 'yes' && $product->get_average_rating() > 0){
			
		}else{
			$markup['aggregateRating'] = array(
				'@type'       => 'AggregateRating',
				'ratingValue' => (int)$editor_rating/2,
				'reviewCount' => 1,
			);				
		}			
	}
    $term_ids =  wc_get_product_terms($post->ID, 'store', array("fields" => "ids")); 
	if (!empty($term_ids) && ! is_wp_error($term_ids)) {
		$term_id = $term_ids[0];
    	$tagobj = get_term_by('id', $term_id, 'store');
    	$tagname = $tagobj->name;
		$markup['brand'] = array(
			'@type'       => 'Thing',
			'name' => $tagname,
		);    			
	}

	return $markup;
}



function rehub_wc_comment_badges( $comment ) { ?>
	<div class="wc-comment-author vcard floatleft">
		<?php echo get_avatar( $comment, apply_filters( 'woocommerce_review_gravatar_size', '80' ), '' ); ?>
		<div class="text-center font80 lineheight20">
		<?php if (rehub_option('rh_enable_mycred_comment')): ?>
			<?php 
				$author_id = $comment->user_id;
				if(function_exists('mycred_get_users_rank')){
					if(rehub_option('rh_mycred_custom_points')){
						$custompoint = rehub_option('rh_mycred_custom_points');
						$mycredrank = mycred_get_users_rank($author_id, $custompoint );
					}
					else{
						$mycredrank = mycred_get_users_rank($author_id);		
					}
				}
				if(function_exists('mycred_display_users_total_balance') && function_exists('mycred_render_shortcode_my_balance')){
				    if(rehub_option('rh_mycred_custom_points')){
				        $custompoint = rehub_option('rh_mycred_custom_points');
				        $mycredpoint = mycred_render_shortcode_my_balance(array('type'=>$custompoint, 'user_id'=>$author_id, 'wrapper'=>'', 'balance_el' => '') );
				        $mycredlabel = mycred_get_point_type_name($custompoint, false);
				    }
				    else{
				        $mycredpoint = mycred_render_shortcode_my_balance(array('user_id'=>$author_id, 'wrapper'=>'', 'balance_el' => '') );
				        $mycredlabel = mycred_get_point_type_name('', false);           
				    }
				}
			?>
			<?php if (!empty($mycredrank) && is_object( $mycredrank)) :?>
				<span class="rh-user-rank-mc rh-user-rank-<?php echo (int)$mycredrank->post_id; ?>">
					<?php echo esc_html($mycredrank->title) ;?>
				</span>
			<?php endif;?>
			<?php if (!empty($mycredpoint)) :?><div class="rh_mycred_point_bal"><?php echo esc_html($mycredlabel);?>: <?php echo ''.$mycredpoint;?></div><?php endif;?>
			<?php if ( function_exists( 'mycred_get_users_badges' ) ) : ?>
				<div class="comm_meta_cred width-80 mt5 mb5 font80 lineheight20">
					<?php rh_mycred_display_users_badges( $author_id ) ?>
				</div> 
			<?php endif; ?>
		<?php else:?>
			<?php 	
				if (function_exists('bp_get_member_type')){	
					$author_id = $comment->user_id;		
					$membertype = bp_get_member_type($author_id);
					$membertype_object = bp_get_member_type_object($membertype);
					$membertype_label = (!empty($membertype_object) && is_object($membertype_object)) ? $membertype_object->labels['singular_name'] : '';
					if($membertype_label){
						echo '<span class="rh-user-rank-mc rh-user-rank-'.$membertype.'">'.$membertype_label.'</span>';
					}
				}
			?>		
		<?php endif;?>
		</div>
	</div>
<?php 
}
remove_action( 'woocommerce_review_before', 'woocommerce_review_display_gravatar', 10 );
add_action( 'woocommerce_review_before', 'rehub_wc_comment_badges', 10 );

// pros and cons in comment form
add_filter('woocommerce_product_review_comment_form_args', 'rh_add_woo_pros_cons_form_fields');
function rh_add_woo_pros_cons_form_fields($comment_form){
	if ( get_option( 'woocommerce_enable_review_rating' ) === 'yes' ) {
		/*global $wpdb;
		global $product;
		$user = wp_get_current_user();
 		$count = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(comment_ID) FROM $wpdb->comments WHERE comment_post_ID = %d AND comment_author_email = %s", $product->get_id(), $user->user_email ));	*/	
 		global $product;
 		$productid = $product->get_id();
		$userid = get_current_user_id();
		$commented = get_user_meta($userid, '_added_woo_pros_cons', true);
		if(empty($commented) || !is_array($commented)){
			$flagged = false;
		}elseif(in_array($productid, $commented)){
			$flagged = true;
		}else{
			$flagged = false;
		}

		if(!$flagged){
			$comment_form['comment_field'] .= '<div class="woo_pros_cons_form flowhidden"><div class="comment-form-comment wpsm-one-half"><textarea id="pos_comment" name="pos_comment" rows="6" placeholder="'.esc_html__('PROS:', 'rehub-theme').'"></textarea></div><div class="comment-form-comment wpsm-one-half"><textarea id="neg_comment" name="neg_comment" rows="6" placeholder="'.esc_html__('CONS:', 'rehub-theme').'"></textarea></div></div>';
		}
	}
	return $comment_form;
}

// Save Negative, positive
function rh_add_neg_comment( $comment_id ){
	if ( isset($_POST['comment_post_ID']) && (!empty( $_POST['neg_comment']) || !empty($_POST['pos_comment'])) && 'product' === get_post_type( absint( $_POST['comment_post_ID'] ) ) ) {
		if(!empty($_POST['neg_comment'])){
			add_comment_meta( $comment_id, 'neg_comment', sanitize_textarea_field( $_POST['neg_comment'] ), true );
		}
		if(!empty($_POST['pos_comment'])){
			add_comment_meta( $comment_id, 'pos_comment', sanitize_textarea_field( $_POST['pos_comment'] ), true );
		}
		$comment = get_comment( $comment_id );
		$userid = $comment->user_id;
		$postid = $comment->comment_post_ID;
		$commented = get_user_meta($userid, '_added_woo_pros_cons', true);
		if(empty($commented) || !is_array($commented)){
			$commented = array();
		}
		$commented[] = $postid;
		add_user_meta($userid, '_added_woo_pros_cons', $commented, true);	
	}
}
add_action( 'comment_post', 'rh_add_neg_comment' );

// pros and cons in comment text
function rehub_wc_comment_neg_get( $comment ) {
	$out = '';
	$pros_review = get_comment_meta( $comment->comment_ID, 'pos_comment', true );
	$cons_review = get_comment_meta( $comment->comment_ID, 'neg_comment', true );
	if($pros_review || $cons_review){$out .='<div class="flowhidden">';}
	$classcol = (!empty($cons_review) && !empty($pros_review)) ? 'wpsm-one-half ' : '';
	if(isset($pros_review) && $pros_review != '') {
		$pros_reviews = explode(PHP_EOL, $pros_review);
		$proscomment = '';
		foreach ($pros_reviews as $pros) {
			$proscomment .='<span class="pros_comment_item blockstyle mb5">'.$pros.'</span>';
		}
		$out .='<div class="'.$classcol.'lineheight20 padd20 lightgreenbg woo_comment_text_pros mt15 font90 blackcolor"><span class="mb10 blockstyle fontbold">'.__('+ PROS:', 'rehub-theme').' </span><span> '.$proscomment.'</span></div>';
	};
	if(!empty($cons_review)) {
		$cons_reviews = explode(PHP_EOL, $cons_review);
		$conscomment = '';
		foreach ($cons_reviews as $cons) {
			$conscomment .='<span class="cons_comment_item blockstyle mb5">'.$cons.'</span>';
		}			
		$out .= '<div class="'.$classcol.'lineheight20 lightredbg padd20 woo_comment_text_cons mt15 font90 blackcolor"><span class="mb10 blockstyle fontbold">'.__('- CONS:', 'rehub-theme').'</span><span> '.$conscomment.'</span></div>';
	};	
	if($pros_review || $cons_review){$out .= '</div>';}
	return $out;
}
function rehub_wc_comment_neg( $comment ) {
	echo rehub_wc_comment_neg_get($comment);
}
add_action( 'woocommerce_review_comment_text', 'rehub_wc_comment_neg', 12 );

//Render pros, cons in Comment Edit screen
function rh_woo_cm_edit_pros_cons($comment){
	if ( !isset( $comment->comment_ID ) ) return;
 	if ( !isset( $comment->comment_post_ID ) ) return;
	$post_id = $comment->comment_post_ID;
	$post_type = get_post_type( $post_id );
	if($post_type !=='product') return;
	$pos_comment = get_comment_meta( $comment->comment_ID, 'pos_comment', true );
	$neg_comment = get_comment_meta( $comment->comment_ID, 'neg_comment', true );
	$prosconsRow ='';	
	if( !empty($pos_comment) || !empty($neg_comment) ) {
		$prosconsRow .= '<tr><td colspan="2"><label for="pos_comment">';
		$prosconsRow .= esc_html__('+ PROS:', 'rehub-theme');
		$prosconsRow .= '</label><br /><textarea id="pos_comment" name="pos_comment" rows="5" cols="50">';
		$prosconsRow .= esc_attr( $pos_comment );
		$prosconsRow .= '</textarea></td><td colspan="2"><label for="neg_comment">';
		$prosconsRow .= esc_html__('- CONS:', 'rehub-theme');
		$prosconsRow .= '</label><br /><textarea id="neg_comment" name="neg_comment" rows="5" cols="50">';
		$prosconsRow .= esc_attr( $neg_comment );
		$prosconsRow .= '</textarea></td></tr>';
	}	
	if($prosconsRow){
		echo '<fieldset>',
		'<table class="form-table editcomment">',
			'<tbody>',
				$prosconsRow,
			'</tbody></table><br>',
		'</fieldset>';
	}

}

//Save pros cons values from Comment editor
function rehub_wc_neg_comment_save( $data ) {
	if ( ! isset( $_POST['woocommerce_meta_nonce'], $_POST['neg_comment'], $_POST['pos_comment'] ) || ! wp_verify_nonce( wp_unslash( $_POST['woocommerce_meta_nonce'] ), 'woocommerce_save_data' ) )
		return $data;
		
	if(!empty($_POST['neg_comment'])){
		update_comment_meta( $data['comment_ID'], 'neg_comment', sanitize_textarea_field( $_POST['neg_comment'] ) );
	}
	if(!empty($_POST['pos_comment'])){
		update_comment_meta( $data['comment_ID'], 'pos_comment', sanitize_textarea_field( $_POST['pos_comment'] ) );
	}	
	return $data;
}
add_filter( 'wp_update_comment_data', 'rehub_wc_neg_comment_save', 1 );

//Add custom column for Products
function rh_woo_rev_comment_columns( $columns )
{
	$columns['rh_woo_user_review_column'] = esc_html__( 'Product Review', 'rehub-theme' );
	return $columns;
}
add_filter( 'manage_edit-comments_columns', 'rh_woo_rev_comment_columns' );

function rh_woo_rev_comment_column( $column, $comment_ID )
{
	if ( 'rh_woo_user_review_column' == $column ) {
		
	$comment_meta = get_comment_meta($comment_ID);
	//$userCriteria = get_comment_meta($comment_ID, 'user_criteria', true);	
	$pos_comment = get_comment_meta($comment_ID, 'pos_comment', true);
	$neg_comment = get_comment_meta($comment_ID, 'neg_comment', true);
	if(isset($pos_comment) && $pos_comment != '') {
		echo ''.__('+ PROS:', 'rehub-theme').' '.$pos_comment.'<br />';
	};
	if(isset($neg_comment) && $neg_comment != '') {
		echo ''.__('- CONS:', 'rehub-theme').' '.$neg_comment.'<br /><br />';
	};		
	//for($i = 0; $i < count($userCriteria); $i++) {		
		//echo ''.$userCriteria[$i]['name'].': <strong class="rating">'.$userCriteria[$i]['value'].'</strong><br />';
	//};		
	echo '<br /></p>';
	}
}
add_filter( 'manage_comments_custom_column', 'rh_woo_rev_comment_column', 10, 2 );


//////////////////////////////////////////////////////////////////
// Product swatches
//////////////////////////////////////////////////////////////////
function init_wc_attribute_swatches(){
	require_once 'class_wc_attribute_swatches.php';
}
add_action( 'admin_init', 'init_wc_attribute_swatches' );
if(!function_exists('rh_wc_dropdown_variation_attribute_options')){
	function rh_wc_dropdown_variation_attribute_options( $html, $args ){
		$product = $args['product'];
		$options = $args['options'];
		$taxonomy = $args['attribute'];
		$att_id = wc_attribute_taxonomy_id_by_name( $taxonomy );
		$attribute = wc_get_attribute( $att_id );

		if(!is_object($attribute)) return $html;

		$swatch_type = $attribute->type;
		
		if( 'select' == $swatch_type )
			return $html;

		wp_enqueue_script('rhswatches');
		
		if ( false === $args['selected'] && $taxonomy && $product instanceof WC_Product ) {
			$selected_key = 'attribute_' . sanitize_title( $taxonomy );
			$args['selected'] = isset( $_REQUEST[ $selected_key ] ) ? wc_clean( wp_unslash( $_REQUEST[ $selected_key ] ) ) : $product->get_variation_default_attribute( $taxonomy );
		}
		
		$name = $args['name'] ? $args['name'] : 'attribute_' . sanitize_title( $taxonomy );
		$output = '';
		
		if ( empty( $options ) && !empty( $product ) && !empty( $taxonomy ) ) {
			$attributes = $product->get_variation_attributes();
			$options = $attributes[$taxonomy];
		}
		if ( !empty( $options ) ){
			
			$terms = wc_get_product_terms( $product->get_id(), $taxonomy, array( 'fields' => 'all' ) );
			$output .= '<div class="rh-var-selector pb10">';
			
			foreach ( $terms as $term ) {
				if ( in_array( $term->slug, $options, true ) ) {
					
					$term_swatch = get_term_meta( $term->term_id, "rh_swatch_{$swatch_type}", true );
					
					switch( $swatch_type ) {
						case 'color':
							$style = 'background-color:'. $term_swatch .';';
							break;
						case 'image':
							$style = 'background-image:url('. esc_url( wp_get_attachment_thumb_url( $term_swatch ) ) .');';
							break;
						default:
						   $style = '';
					}
					
					$id = $taxonomy .'_'. $term->slug;
					if('text' == $swatch_type){
						$label = $term_swatch;
						if(!$label) {
							$label = $term->name;
						}
					}
					else{
						$label = '';
						if(!$term_swatch){
							$style = '';
							$label = $term->name;
							$swatch_type = 'text';
						}
					}
					
					$output .='<input id="'. esc_attr( $id ) .'" type="radio" name="'. esc_attr( $name ) .'" value="'. esc_attr( $term->slug ) .'" '. checked( sanitize_title( $args['selected'] ), $term->slug, false ) .' class="rh-var-input" />';
					$output .='<label for="'. esc_attr( $id ) .'" title="'. $term->name .'" class="rh-var-label '.$swatch_type.'-label-rh" style="'. $style .'" data-value="'. esc_attr( $term->slug ) .'">'. $label .'</label>';
				}
			}
			
			$output .= '</div>';
		    $variationjs = 'jQuery("select[name='. esc_attr( $name ) .']").hide();jQuery("input[name='. esc_attr( $name ) .']").on("click", function(){if(jQuery(this).prop("checked")){var newValue = jQuery(this).val();jQuery("select[name='. esc_attr( $name ) .']").val(newValue).trigger("change");}});';
	        wp_add_inline_script('rehub', $variationjs);
		}
		return $html . $output;
	}	
}
if(!function_exists('rh_show_swatch_in_attr')){
	function rh_show_swatch_in_attr($wpautop, $attribute, $values){
		if(!isset($attribute['id'])) {
			return $wpautop;
		}
		$attribute_id = $attribute['id'];	
		$att = wc_get_attribute( $attribute_id );
		if(!is_object($att)){
			return $wpautop;
		}
		$swatch_type = $att->type;
		if($swatch_type == 'select'){
			return $wpautop;
		}else{
			global $product;
			if(empty($product)) {
				return $wpautop;
			}		
			$currentslug = $att->slug;
			$has_archive = $att->has_archives;

			$terms = wc_get_product_terms( $product->get_id(), $currentslug, array( 'fields' => 'all' ) );
			$result = '';
			foreach ( $terms as $term ) {
				$term_swatch = get_term_meta( $term->term_id, "rh_swatch_{$swatch_type}", true );
				if($term_swatch){
					switch( $swatch_type ) {
						case 'color':
							$style = 'background-color:'. $term_swatch .';';
							break;
						case 'image':
							$style = 'background-image:url('. esc_url( wp_get_attachment_thumb_url( $term_swatch ) ) .');';
							break;
						default:
						   $style = '';
					}
					if('text' == $swatch_type){
						$label = $term_swatch;
						if(!$label) {
							$label = $term->name;
						}
					}
					else{
						$label = '';
					}
					if ( $has_archive ) {
						$result .= '<a href="' . esc_url( get_term_link( $term->term_id, $currentslug ) ) . '" rel="tag">';
					}
					$nonselect = $has_archive ? '' : ' label-non-selectable';	        				
					$result .='<span class="rh-var-label'.$nonselect.' '.$swatch_type.'-label-rh" style="'. $style .'">'. $label .'</span>';
					if ( $has_archive ) {
						$result .='</a>';
					}	        				
					
				}
				else{
					return $wpautop;
				}
			}
			return $result;		
		}
	}	
}
if(!function_exists('rh_show_swatch_in_filters')){
	function rh_show_swatch_in_filters($term_html, $term, $link, $count){

		$attribute_id = wc_attribute_taxonomy_id_by_name( $term->taxonomy );
		if($attribute_id){
			$attribute = wc_get_attribute( $attribute_id );
			if(!empty($attribute)){
				$swatch_type = $attribute->type;
				if($swatch_type != 'select'){
					$term_swatch = get_term_meta( $term->term_id, "rh_swatch_{$swatch_type}", true );
	    			if($term_swatch){
						switch( $swatch_type ) {
							case 'color':
								$style = 'background-color:'. $term_swatch .';';
								break;
							case 'image':
								$style = 'background-image:url('. esc_url( wp_get_attachment_thumb_url( $term_swatch ) ) .');';
								break;
							default:
							   $style = '';
						}
						$attributelabel = 'text' == $swatch_type ? $term_swatch : '';	        				
						$result = '<span class="rh-var-label label-non-selectable '.$swatch_type.'-label-rh" style="'. $style .'">'. $attributelabel .'</span>';
						$termname = esc_html( $term->name ).'</a>';
						$termwithswatch = $result.'<span class="rh_attr_name">'.$termname.'</span></a>';
						$termrel = 'rel="nofollow"';
						$termlinkclass = 'rel="nofollow" class="rh_swatch_filter rh_swatch_'.$swatch_type.'"';
	    				$term_html = str_replace($termname, $termwithswatch, $term_html);
	    				$term_html = str_replace($termrel, $termlinkclass, $term_html);
	    			}								
				}
			}
		}

		return $term_html;
	}	
}
add_filter( 'woocommerce_dropdown_variation_attribute_options_html', 'rh_wc_dropdown_variation_attribute_options', 10, 2 );
add_filter('woocommerce_attribute', 'rh_show_swatch_in_attr', 10,3);
add_filter('woocommerce_layered_nav_term_html', 'rh_show_swatch_in_filters', 10, 4);


if(!function_exists('rh_wc_add_to_cart_params')){
	function rh_wc_add_to_cart_params($params, $handle) {
	    if('wc-add-to-cart' == $handle){
	        $params['i18n_added_to_cart'] = esc_html__( 'Has been added to cart.', 'rehub-theme' );
	    }
	    return $params;
	}
	add_filter('woocommerce_get_script_data', 'rh_wc_add_to_cart_params', 10, 2);
}


//////////////////////////////////////////////////////////////////
//VENDOR FUNCTION
//////////////////////////////////////////////////////////////////

if ( !function_exists('rh_show_vendor_info_single') ) {
	function rh_show_vendor_info_single($wrapperclass='') {
		do_action('rh_woo_single_product_vendor');
		$vendor_verified_label = $vacation_mode = $vacation_msg = '';
		$verified_vendor = $featured_vendor = false;		
		if( class_exists( 'WeDevs_Dokan' ) ) {
			$vendor_id = get_the_author_meta( 'ID' );
			$store_info = dokan_get_store_info( $vendor_id );
			$store_url = dokan_get_store_url( $vendor_id );
			$sold_by_label = apply_filters( 'dokan_sold_by_label', esc_html__( 'Sold by', 'rehub-theme' ) );
			$is_vendor = dokan_is_user_seller( $vendor_id );
			$store_name = esc_html( $store_info['store_name'] );
			$featured_vendor = get_user_meta( $vendor_id, 'dokan_feature_seller', true );
		}elseif (class_exists('WCMp')){
			$vendor_id = get_the_author_meta( 'ID' );
			$is_vendor = is_user_wcmp_vendor( $vendor_id );
			if($is_vendor){
				$vendorobj = get_wcmp_vendor($vendor_id);
				$store_url = $vendorobj->permalink;
				$store_name = $vendorobj->page_title;	
				$verified_vendor = get_user_meta($vendor_id, 'wcmp_vendor_is_verified', true);			
			}
			$wcmp_option = get_option("wcmp_frontend_settings_name");
			$sold_by_label = (!empty($wcmp_option['sold_by_text'])) ? $wcmp_option['sold_by_text'] : esc_html__( 'Sold by', 'rehub-theme' );
		}
		elseif (defined( 'wcv_plugin_dir' )) {
			$vendor_id = get_the_author_meta( 'ID' );
			$store_url = WCV_Vendors::get_vendor_shop_page( $vendor_id );
			$sold_by_label = get_option( 'wcvendors_label_sold_by' );
			$is_vendor = WCV_Vendors::is_vendor( $vendor_id );
			$store_name = WCV_Vendors::get_vendor_sold_by( $vendor_id );
			
			if ( class_exists( 'WCVendors_Pro' ) ) {
				$vendor_meta = array_map( function( $a ){ return $a[0]; }, get_user_meta($vendor_id ) );
				$verified_vendor = ( array_key_exists( '_wcv_verified_vendor', $vendor_meta ) ) ? $vendor_meta[ '_wcv_verified_vendor' ] : false;
				$vacation_mode = get_user_meta( $vendor_id , '_wcv_vacation_mode', true ); 
				$vacation_msg = ( $vacation_mode ) ? get_user_meta( $vendor_id , '_wcv_vacation_mode_msg', true ) : '';		
			}		
		}
		else{
			return false;
		}

		if($is_vendor){
			if ( $verified_vendor || $featured_vendor == 'yes' ) {
				$vendor_verified_label = '';
				if(function_exists('get_wcmp_vendor_verification_badge')){
					$badge_img = get_vendor_verification_settings('badge_img');
					if(!empty($badge_img)){
						$vendor_verified_label = get_wcmp_vendor_verification_badge( $vendor_id, array( 'height' => 20, 'width' => 20, 'class' => 'floatleft mr5 rtlml5' ) );
					}					
				}
				if(!$vendor_verified_label){
					$vendor_verified_label = '<i class="rhicon rhi-shield-check" aria-hidden="true"></i>';
				}
			} 		
			$sold_by = sprintf( '<h5><a href="%s" class="wcvendors_cart_sold_by_meta">%s</a></h5>', $store_url, $store_name );
			
			/* HTML output */
			echo '<div class="vendor_store_details '.esc_attr($wrapperclass).'">';
			echo '<div class="vendor_store_details_image"><a href="'. $store_url  .'"><img src="'. rh_show_vendor_avatar( $vendor_id, 50, 50 ) .'" class="vendor_store_image_single" width=50 height=50 /></a></div>';
			echo '<div class="vendor_store_details_single">';
			echo '<div class="vendor_store_details_nameshop">';
			echo '<span class="vendor_store_details_label">'. $sold_by_label .'</span>';
			echo '<span class="vendor_store_details_title">'. $vendor_verified_label . $sold_by .'</span>';
			echo '</div>';

			if(class_exists( 'WeDevs_Dokan' ) && dokan_get_option( 'contact_seller', 'dokan_general', 'on' ) == 'on'){
				echo '<span class="vendor_store_details_contact mr10">';
				if(class_exists( 'BuddyPress' ) ) {
					echo '<a href="'. bp_core_get_user_domain( $vendor_id ) .'" class="vendor_store_owner_name"><span>'. get_the_author_meta('display_name') .'</span></a> ';
				}else{
					echo '<span class="vendor_store_owner_label">@ <span class="vendor_store_owner_name">'.get_the_author_meta('display_name') .'</span></span>';
				}

				$class = ( !is_user_logged_in() && rehub_option( 'userlogin_enable' ) == '1' ) ? ' act-rehub-login-popup' : '';						
				echo ' <a href="'.$store_url.'#dokan-form-contact-seller" class="vendor_store_owner_contactlink'.$class.'"><i class="rhicon rhi-envelope" aria-hidden="true"></i> <span>'. esc_html__('Ask owner', 'rehub-theme') .'</span></a>';									
				echo '</span>';					
			}
			elseif(is_active_widget( '', '', 'dc-vendor-quick-info')){
				echo '<span class="vendor_store_details_contact mr10">';
				if(class_exists( 'BuddyPress' ) ) {
					echo '<a href="'. bp_core_get_user_domain( $vendor_id ) .'" class="vendor_store_owner_name"><span>'. get_the_author_meta('display_name') .'</span></a> ';
				}else{
					echo '<span class="vendor_store_owner_label">@ <span class="vendor_store_owner_name">'.get_the_author_meta('display_name') .'</span></span>';
				}
				$class = ( !is_user_logged_in() && rehub_option( 'userlogin_enable' ) == '1' ) ? ' act-rehub-login-popup' : '';						
				echo ' <a href="'.$store_url.'#wcmp-vendor-contact-widget-top" class="vendor_store_owner_contactlink'.$class.'"><i class="rhicon rhi-envelope" aria-hidden="true"></i> <span>'. esc_html__('Ask owner', 'rehub-theme') .'</span></a>';									
				echo '</span>';	
			}	
			elseif(class_exists( 'BuddyPress' ) ) {
				echo '<span class="vendor_store_details_contact mr10"><span class="vendor_store_owner_label">@ </span>';
				echo '<a href="'. bp_core_get_user_domain( $vendor_id ) .'" class="vendor_store_owner_name"><span>'. get_the_author_meta('display_name') .'</span></a> ';
				if ( bp_is_active( 'messages' )){
					$link = (is_user_logged_in()) ? wp_nonce_url( bp_loggedin_user_domain() . bp_get_messages_slug() . '/compose/?r=' . bp_core_get_username( $vendor_id) .'&ref='. urlencode(get_permalink())) : '#';
					$class = (!is_user_logged_in() && rehub_option('userlogin_enable') == '1') ? ' act-rehub-login-popup' : '';
						echo ' <a href="'.$link.'" class="vendor_store_owner_contactlink'.$class.'"><i class="rhicon rhi-envelope" aria-hidden="true"></i> <span>'. esc_html__('Ask owner', 'rehub-theme') .'</span></a>';			
				}
				echo '</span>';		
			}
			
			echo '</div></div>';
			if ($vacation_msg) :
				echo '<div class="wpsm_box green_type nonefloat_box"><div>'. $vacation_msg .'</div></div>';
			endif;
		}
	
	}
}

if ( !function_exists('rh_show_vendor_ministore') ) {
	function rh_show_vendor_ministore( $vendor_id, $label='' ) { 
		$totaldeals = count_user_posts( $vendor_id, $post_type = 'product' );
		$vendor_verified_label = '';
		$verified_vendor = $featured_vendor = false;
		
		if( class_exists( 'WeDevs_Dokan' ) ){
			$store_url = dokan_get_store_url( $vendor_id );
			$is_vendor = dokan_is_user_seller( $vendor_id );
			$store_info = dokan_get_store_info( $vendor_id );
			$store_name = esc_html( $store_info['store_name'] );
			$featured_vendor = get_user_meta( $vendor_id, 'dokan_feature_seller', true );
		}
		else {
			$store_url = WCV_Vendors::get_vendor_shop_page( $vendor_id );
			$is_vendor = WCV_Vendors::is_vendor( $vendor_id );
			$store_name = WCV_Vendors::get_vendor_sold_by( $vendor_id );
			if ( class_exists( 'WCVendors_Pro' ) ) {
				$vendor_meta = array_map( function( $a ){ return $a[0]; }, get_user_meta($vendor_id ) );
				$verified_vendor = ( array_key_exists( '_wcv_verified_vendor', $vendor_meta ) ) ? $vendor_meta[ '_wcv_verified_vendor' ] : false;
			}
		}
		
		if( $totaldeals > 0 ){
			if ( $verified_vendor || $featured_vendor == 'yes' ) {
				$vendor_verified_label = '<i class="rhicon rhi-check-square" aria-hidden="true"></i>';
			} 
			$sold_by = ( $is_vendor ) ? sprintf( '<h5><a href="%s" class="wcvendors_cart_sold_by_meta">%s</a></h5>', $store_url, $store_name ) : get_bloginfo( 'name' );
			
			/* HTML output */
			echo '<div class="vendor_store_in_bp">';
			echo '<div class="vendor-list-like">'. getShopLikeButton( $vendor_id ) .'</div>';
			echo '<div class="vendor_store_in_bp_image"><a href="'. $store_url .'"><img src="'. rh_show_vendor_avatar( $vendor_id, 80, 80 ) .'" class="vendor_store_image_single" width=80 height=80 /></a></div>';
			echo '<div class="vendor_store_in_bp_single">';
			echo '<span class="vendor_store_in_bp_label"><span class="vendor_store_owner_label">'. $label .'</span></span>';		
			echo '<span class="vendor_store_in_bp_title">'. $vendor_verified_label . $sold_by.'</span>';
			echo '</div>';
			echo '<div class="vendor_store_in_bp_last_products">';
				$totaldeals = $totaldeals - 4;
				$args = array(
					'post_type' => 'product',
					'posts_per_page' => 4,
					'author' => $vendor_id,
					'ignore_sticky_posts'=> true,
					'no_found_rows'=> true
				);
				$looplatest = new WP_Query($args);
				if ( $looplatest->have_posts() ){
					while ( $looplatest->have_posts() ) : $looplatest->the_post();
						echo '<a href="'. get_permalink( $looplatest->ID ) .'">';
							$showimg = new WPSM_image_resizer();
							$showimg->use_thumb = true;
							$showimg->height = 70;
							$showimg->width = 70;
							$showimg->crop = true;
							$showimg->no_thumb = rehub_woocommerce_placeholder_img_src('');
							$img = $showimg->get_resized_url();
							echo '<img src="'. $img .'" width=70 height=70 alt="'. get_the_title( $looplatest->ID ) .'"/>';
						echo '</a>';
					endwhile;
					echo '<a class="vendor_store_in_bp_count_pr" href="'. $store_url .'"><span>+'. $totaldeals .'</span></a>';
				}
				wp_reset_query();
			echo '</div>';
			echo '</div>';		
		}
	}
}

if ( !function_exists('rh_show_vendor_avatar') ) {
	function rh_show_vendor_avatar( $vendor_id, $width=150, $height=150, $crop = true ) {
		if( !$vendor_id ) 
			return;
		$store_icon_url = '';
		if( class_exists( 'WeDevs_Dokan' ) ) {
			$store_info = dokan_get_store_info( $vendor_id );
			$gravatar_id = (!empty($store_info['gravatar_id'])) ? $store_info['gravatar_id'] : ''; 
			$gravatar_id = (!empty( $store_info['gravatar'])) ? $store_info['gravatar'] : $gravatar_id;

			if( !empty($gravatar_id) ) {
				$store_icon_src 	= wp_get_attachment_image_src($gravatar_id, array( 150, 150 ) );
				if ( is_array( $store_icon_src ) ) { 
					$store_icon_url = $store_icon_src[0]; 
				}			
			}
		}
		elseif (class_exists('WCMp')){
			$vendorobj = get_wcmp_vendor($vendor_id);
			if(!empty($vendorobj)){
				$store_icon_url = $vendorobj->get_image();
			}
						
		}		
		elseif(defined( 'wcv_plugin_dir' )) {
			if( class_exists( 'WCVendors_Pro' ) ) {
				$store_icon_src 	= wp_get_attachment_image_src( get_user_meta( $vendor_id, '_wcv_store_icon_id', true ), array( 150, 150 ) );
				if ( is_array( $store_icon_src ) ) { 
					$store_icon_url= $store_icon_src[0]; 
				}
			}
			else{
				$store_icon_src 	= wp_get_attachment_image_src( get_user_meta( $vendor_id, 'rh_vendor_free_logo', true ), array( 150, 150 ) );
				if ( is_array( $store_icon_src ) ) { 
					$store_icon_url= $store_icon_src[0]; 
				}
			}
		}
		elseif(defined( 'WCFMmp_TOKEN' )) {
			$store_user = wcfmmp_get_store( $vendor_id );
			$store_icon_url = $store_user->get_avatar();
		}
		else{
			return;
		}
		if( !$store_icon_url ) {
			$store_icon_url = get_template_directory_uri() . '/images/default/wcvendoravatar.png';	
		}
		$showimg = new WPSM_image_resizer();
		$showimg->src = $store_icon_url;
		$showimg->use_thumb = false;
		$showimg->height = $height;
		$showimg->width = $width;
		$showimg->crop = $crop;           
		$img = $showimg->get_resized_url();
		return $img;	
	}
}

if( !function_exists( 'rh_show_vendor_bg' ) ) {
	function rh_show_vendor_bg( $vendor_id ) {
		$store_bg_styles = '';
		if( !$vendor_id )
			return;
		if( class_exists( 'WeDevs_Dokan' ) ) {
			$store_info = dokan_get_store_info( $vendor_id );
			$banner_id = (!empty($store_info['banner_id'])) ? $store_info['banner_id'] : ''; 
			$banner_id = (!empty( $store_info['banner'])) ? $store_info['banner'] : $banner_id;
			$store_bg = wp_get_attachment_url( $banner_id);

			if( $store_bg ) {
				$store_bg_styles = 'background-image: url('. $store_bg .'); background-repeat: no-repeat;background-size: cover;';
			}
		}
		elseif (class_exists('WCMp')){
			$vendorobj = get_wcmp_vendor($vendor_id);
			$store_bg = $vendorobj->get_image('banner');
			if($store_bg){
				$store_bg_styles = 'background-image: url('. $store_bg .'); background-repeat: no-repeat;background-size: cover;';
			}
		}	
		elseif(defined( 'WCFMmp_TOKEN' )) {
			$store_user = wcfmmp_get_store( $vendor_id );
			$store_bg = $store_user->get_banner();
			if( !$store_bg ) {
				global $WCFMmp;
				$store_bg = isset( $WCFMmp->wcfmmp_marketplace_options['store_default_banner'] ) ? $WCFMmp->wcfmmp_marketplace_options['store_default_banner'] : $WCFMmp->plugin_url . 'assets/images/default_banner.jpg';
				$store_bg = apply_filters( 'wcfmmp_store_default_bannar', $store_bg );
			}
			$store_bg_styles = 'background-image: url('. $store_bg .'); background-repeat: no-repeat;background-size: cover;';
		}	
		elseif(defined( 'wcv_plugin_dir' )) {
			if ( class_exists( 'WCVendors_Pro' ) ) {
				$store_banner_src 	= wp_get_attachment_image_src( get_user_meta( $vendor_id, '_wcv_store_banner_id', true ), 'full'); 
				if ( is_array( $store_banner_src ) ) { 
					$store_bg= $store_banner_src[0]; 
				}
				else { 
					//  Getting default banner 
					$default_banner_src = WCVendors_Pro::get_option( 'default_store_banner_src' ); 
					$store_bg= $default_banner_src; 
				}	
				$store_bg_styles = 'background-image: url('.$store_bg.'); background-repeat: no-repeat;background-size: cover;';	
			}
			else {
				$store_banner_src  = wp_get_attachment_image_src( get_user_meta( $vendor_id, 'rh_vendor_free_header', true ), 'full');
				if ( is_array( $store_banner_src ) ) { 
					$store_bg= $store_banner_src[0]; 
					$store_bg_styles = 'background-image: url('.$store_bg.'); background-repeat: no-repeat;background-size: cover;';
				}
			}
		}
		else{
			return;
		}
		if( !$store_bg_styles ) {
			$store_bg_styles = 'background-image: url('.get_template_directory_uri() . '/images/default/brickwall.png); background-repeat:repeat;';	
		}		
		return $store_bg_styles;	
	}
}

if (!function_exists('rh_change_product_query')){
	function rh_change_product_query($q){
    	if (empty($q->query_vars['wc_query']))
			return;
		
		$search_string = isset($_GET['rh_wcv_search']) ? esc_html($_GET['rh_wcv_search']) : '';
		$cat_string = (isset($_GET['rh_wcv_vendor_cat'])) ? esc_html($_GET['rh_wcv_vendor_cat']) : '';
		
		if($search_string){
			$q->set( 's', $search_string);
		}
		if($cat_string){
			$catarray = array(
				array(
					'taxonomy' => 'product_cat', 
					'terms' => array($cat_string), 
					'field' => 'term_id'				
					)
				);
			$q->set('tax_query', $catarray);
		}
		if (is_tax('store')){ //Here we change number of posts in brand store archives
			$q->set( 'posts_per_page', 30);
		}	
	}
}

if (rehub_option('wooregister_xprofile') == 1){

	//Synchronization with Woocommerce register form and Xprofiles
	add_action('woocommerce_register_form','rh_add_xprofile_to_woocommerce_register');
	add_action('wcvendors_settings_before_paypal','rh_add_xprofile_to_wcvendor');
	add_action('dokan_settings_form_bottom', 'rh_add_xprofile_to_dokan');

	function rh_add_xprofile_to_woocommerce_register() {
	if ( class_exists( 'BuddyPress' ) ) {
		?>
		<?php if ( bp_is_active( 'xprofile' ) ) : ?>
			<div id="xp-woo-profile-details-section">
				<?php if ( bp_has_profile( array( 'profile_group_id' => 1, 'fetch_field_data' => false ) ) ) : while ( bp_profile_groups() ) : bp_the_profile_group(); ?>
					<?php while ( bp_profile_fields() ) : bp_the_profile_field(); ?>
						<div<?php bp_field_css_class( 'editfield form-row' ); ?>>
							<?php
								$field_type = bp_xprofile_create_field_type( bp_get_the_profile_field_type() );
								$field_type->edit_field_html();
							?>
						</div>
					<?php endwhile; ?>
					<input type="hidden" name="signup_profile_field_ids" id="signup_profile_field_ids" value="<?php bp_the_profile_field_ids(); ?>" />
				<?php endwhile; endif; ?>
				<?php do_action( 'bp_signup_profile_fields' ); ?>
			</div><!-- #profile-details-section -->
			<?php do_action( 'bp_after_signup_profile_fields' ); ?>
		<?php endif; ?>
		<?php
	}
	}

	function rh_add_xprofile_to_wcvendor() {
	if ( class_exists( 'BuddyPress' ) ) {
		?>
		<?php if ( bp_is_active( 'xprofile' ) ) : ?>
			<div id="xp-wcvendor-profile">
				<?php $user_id = get_current_user_id();?>
				<?php if ( bp_has_profile( array( 'user_id'=> $user_id, 'profile_group_id' => 1, 'fetch_field_data' => true, 'fetch_fields'=>true ) ) ) : while ( bp_profile_groups() ) : bp_the_profile_group(); ?>
					<?php while ( bp_profile_fields() ) : bp_the_profile_field(); ?>
						<div<?php bp_field_css_class( 'editfield form-row' ); ?>>
							<?php
								$field_type = bp_xprofile_create_field_type( bp_get_the_profile_field_type() );
								$field_type->edit_field_html(array( 'user_id'=> $user_id));
							?>
						</div>
					<?php endwhile; ?>
					<input type="hidden" name="signup_profile_field_ids" id="signup_profile_field_ids" value="<?php bp_the_profile_field_ids(); ?>" />
				<?php endwhile; endif; ?>
				<?php do_action( 'bp_signup_profile_fields' ); ?>
			</div><!-- #profile-details-section -->
			<?php do_action( 'bp_after_signup_profile_fields' ); ?>
		<?php endif; ?>
		<?php
	}
	}	

	function rh_add_xprofile_to_dokan( $user_id ) {
		if ( class_exists( 'BuddyPress' ) ) {
			?>
			<?php if ( bp_is_active( 'xprofile' ) ) : ?>
			<!-- Xprofile fields -->
			<div class="dokan-form-group xprofile-area">
			<h2><?php esc_html_e( 'Extended Profile', 'rehub-theme' ); ?></h2>
				<?php if ( bp_has_profile( array( 'user_id'=> $user_id, 'profile_group_id' => 1, 'hide_empty_fields' => false, 'fetch_field_data' => true, 'fetch_fields'=>true ) ) ) : ?>
					<?php while ( bp_profile_groups() ) : bp_the_profile_group(); ?>
						<?php while ( bp_profile_fields() ) : bp_the_profile_field(); ?>
						<div class="dokan-w6 dokan-text-left">
							<div <?php bp_field_css_class( 'editfield form-row' ); ?>>
								<?php
									$field_type = bp_xprofile_create_field_type( bp_get_the_profile_field_type() );
									$field_type->edit_field_html( array( 'user_id'=> $user_id ) );
								?>
								<p class="description"><?php bp_the_profile_field_description(); ?></p>
							</div>
						</div>
						<?php endwhile; ?>
						<input type="hidden" name="signup_profile_field_ids" id="signup_profile_field_ids" value="<?php bp_the_profile_field_ids(); ?>" />
					<?php endwhile; ?>
				<?php endif; ?>
				<?php do_action( 'bp_signup_profile_fields' ); ?>
			</div>
			<?php do_action( 'bp_after_signup_profile_fields' ); ?>
				<script type="text/javascript">
				jQuery('[aria-required]').each(function() {
					jQuery(this).prop('required',true);
				});
				</script>
			<?php endif; ?>
			<?php
		}
	}	

	//Validating required Xprofile fields
	add_action( 'woocommerce_register_post', 'rh_validate_xprofile_to_woocommerce_register', 10, 3 );
	function rh_validate_xprofile_to_woocommerce_register( $username, $email, $validation_errors ) {
		if ( class_exists( 'BuddyPress' ) ) {
			if (!empty($_POST['signup_profile_field_ids'])){
				$user_error_req_fields = array();
				$signup_profile_field_ids = explode(',', $_POST['signup_profile_field_ids']);
				foreach ((array)$signup_profile_field_ids as $field_id) {
					if ( ! isset( $_POST['field_' . $field_id] ) ) {
						if ( ! empty( $_POST['field_' . $field_id . '_day'] ) && ! empty( $_POST['field_' . $field_id . '_month'] ) && ! empty( $_POST['field_' . $field_id . '_year'] ) ) {
							// Concatenate the values.
							$date_value = $_POST['field_' . $field_id . '_day'] . ' ' . $_POST['field_' . $field_id . '_month'] . ' ' . $_POST['field_' . $field_id . '_year'];

							// Turn the concatenated value into a timestamp.
							$_POST['field_' . $field_id] = date( 'Y-m-d H:i:s', strtotime( $date_value ) );
							
						}
					}
					// Create errors for required fields without values.
					if ( xprofile_check_is_required_field( $field_id ) && empty( $_POST[ 'field_' . $field_id ] ) && ! bp_current_user_can( 'bp_moderate' ) ){
						$field_data = xprofile_get_field($field_id );
						if(is_object($field_data)){
							$user_error_req_fields[]= $field_data->name;
						}		
					}
				}
				if(!empty($user_error_req_fields)){
		        	$validation_errors->add( 'billing_first_name_error', esc_html__( ' Next fields are required: ', 'rehub-theme' ).implode(', ',$user_error_req_fields) );									
				}			
			}
		}	 
	    return $validation_errors;
	} 	

	//Updating use meta after registration successful registration
	add_action('woocommerce_created_customer','rh_save_xprofile_to_woocommerce_register');
	add_action( 'wcvendors_shop_settings_saved', 'rh_save_xprofile_to_woocommerce_register' );
	add_action( 'dokan_store_profile_saved', 'rh_save_xprofile_to_woocommerce_register' );
	function rh_save_xprofile_to_woocommerce_register($user_id) {
		if (!empty($_POST['signup_profile_field_ids'])){
			$signup_profile_field_ids = explode(',', $_POST['signup_profile_field_ids']);
			foreach ((array)$signup_profile_field_ids as $field_id) {
				if ( ! isset( $_POST['field_' . $field_id] ) ) {
					if ( ! empty( $_POST['field_' . $field_id . '_day'] ) && ! empty( $_POST['field_' . $field_id . '_month'] ) && ! empty( $_POST['field_' . $field_id . '_year'] ) ) {
						// Concatenate the values.
						$date_value = $_POST['field_' . $field_id . '_day'] . ' ' . $_POST['field_' . $field_id . '_month'] . ' ' . $_POST['field_' . $field_id . '_year'];

						// Turn the concatenated value into a timestamp.
						$_POST['field_' . $field_id] = date( 'Y-m-d H:i:s', strtotime( $date_value ) );
						
					}
				}
				if(!empty($_POST['field_' . $field_id])){
					$field_val = sanitize_text_field($_POST['field_' . $field_id]);
					xprofile_set_field_data($field_id, $user_id, $field_val);
					$visibility_level = ! empty( $_POST['field_' . $field_id . '_visibility'] ) ? $_POST['field_' . $field_id . '_visibility'] : 'public';
					xprofile_set_field_visibility_level( $field_id, $user_id, $visibility_level );					
				}			
			}
		}
	}	
}

//////////////////////////////////////////////////////////////////
//Custom Currency for main product price for Content Egg
//////////////////////////////////////////////////////////////////
if(rehub_option('ce_custom_currency')){
	if(defined('\ContentEgg\PLUGIN_PATH')){

		$currency_code = rehub_option('ce_custom_currency');
		$woocurrency = get_woocommerce_currency();
		if($currency_code != $woocurrency){
			add_filter('woocommerce_get_price_html','rh_ce_multicurrency', 10, 2);
			if(!function_exists('rh_ce_multicurrency')){
				function rh_ce_multicurrency($price, $product){
					//$itemsync = \ContentEgg\application\WooIntegrator::getSyncItem($postid);
					$currency_code = rehub_option('ce_custom_currency');
					$woocurrency = get_woocommerce_currency();					
					$currency_rate = \ContentEgg\application\helpers\CurrencyHelper::getCurrencyRate($woocurrency, $currency_code);
					if (!$currency_rate) $currency_rate = 1;					
					$out = '';
					if ( '' === $product->get_price() ) {
						$out = apply_filters( 'woocommerce_empty_price_html', '', $product);
					}
					elseif($product->is_on_sale()){
						$out = '<del><span class="woocommerce-Price-amount amount">'.ContentEgg\application\helpers\TemplateHelper::formatPriceCurrency($product->get_regular_price()*$currency_rate, $currency_code, '<span class="woocommerce-Price-currencySymbol">', '</span>').'</span></del> <ins><span class="woocommerce-Price-amount amount">'.ContentEgg\application\helpers\TemplateHelper::formatPriceCurrency($product->get_price()*$currency_rate, $currency_code, '<span class="woocommerce-Price-currencySymbol">', '</span>').'</span></ins>';
					}else{
						$out = '<span class="woocommerce-Price-amount amount">'.ContentEgg\application\helpers\TemplateHelper::formatPriceCurrency($product->get_price()*$currency_rate, $currency_code, '<span class="woocommerce-Price-currencySymbol">', '</span>').'</span>';						
					}				
					return $out;
				}
			}			
		}
	}
}

function rh_price_free_zero_empty( $price, $product ) {
	$getprice = get_post_meta($product->get_id(), '_regular_price', true);
	if ( $getprice == 0 && $getprice !=='' ) {
		$price = '<span class="amount">' . esc_html__( 'Free!', 'rehub-theme' ) . '</span>';
	}

	return $price;
}

add_filter( 'woocommerce_get_price_html', 'rh_price_free_zero_empty', 10, 2 );


//////////////////////////////////////////////////////////////////
//GMW to VENDOR PLUGINS
//////////////////////////////////////////////////////////////////
function rh_gmw_vendor_location_synch( $vendor_id, $data = '' ){
	if( !function_exists('gmw_update_user_location') )
		return;

	if( empty( $vendor_id ) ){
	    $vendor_id = get_current_user_id();
	}	

 	$google_server_key = gmw_get_option( 'api_providers', 'google_maps_server_side_api_key', '' );
	
	if( empty( $google_server_key ) ) 
		return;
	
	$data = empty( $data ) ? (array)$_POST : $data;

	if( empty( $data ) )
		return;

	foreach( $data as $key => $value ){
		$key = sanitize_key($key);
		$data[$key] = sanitize_text_field($value);
	}	
	
	$addressArray = array();
	$addressString = '';
	
	// WC Vendors
	if( class_exists('WCVendors_Pro') ){
		If( !empty($data['_wcv_store_country']) AND !empty($data['_wcv_store_city']) ){
			$addressArray['street'] = $data['_wcv_store_address1'];
			$addressArray['apt'] = $data['_wcv_store_address2'];
			$addressArray['city'] = $data['_wcv_store_city'];
			$addressArray['state'] = $data['_wcv_store_state'];
			$addressArray['country'] = $data['_wcv_store_country'];
			$addressArray['zipcode'] = $data['_wcv_store_postcode'];
		}
	}
	// WC Marketplace
	elseif( class_exists('WCMp') ){
		if( !empty($data['_store_location']) AND is_string($data['_store_location']) ) {
			$addressString = $data['_store_location'];
		}
		elseif( !empty($data['vendor_country']) && !empty($data['vendor_city']) ){
			$addressArray['street'] = $data['vendor_address_1'];
			$addressArray['city'] = $data['vendor_city'];
			$addressArray['state'] = $data['vendor_state'];
			$addressArray['country'] = $data['vendor_country'];
			$addressArray['zipcode'] = $data['vendor_postcode'];
		}
	}
	// WC lovers MarketPlace OR Dokan
	elseif( defined('WCFMmp_TOKEN') OR class_exists('WeDevs_Dokan') ){
		$data_address = isset($data['dokan_store_address']) ? $data['dokan_store_address'] : $data['address']; // check if the location was updatet by admin in the Dokan plugin
		if( !empty($data['find_address']) AND is_string($data['find_address']) ){
			$addressString = $data['find_address'];
		}
		elseif( !empty($data_address['country']) AND !empty($data_address['city']) ){
			$addressArray['street'] = $data_address['street_1'];
			$addressArray['apt'] = $data_address['street_2'];
			$addressArray['city'] = $data_address['city'];
			$addressArray['state'] = $data_address['state'];
			$addressArray['country'] = $data_address['country'];
			$addressArray['zipcode'] = $data_address['zip'];
		}
	}
	
	if( empty( $addressArray ) AND empty( $addressString ) )
		return;
		
	$address = empty( $addressString ) ? $addressArray : $addressString;

	gmw_update_user_location( $vendor_id, $address, true );
}
add_action( 'wcv_pro_store_settings_saved', 'rh_gmw_vendor_location_synch' );
add_action( 'before_wcmp_vendor_dashboard', 'rh_gmw_vendor_location_synch' );
add_action( 'wcfm_vendor_settings_update', 'rh_gmw_vendor_location_synch', 10, 2 );
add_action( 'dokan_store_profile_saved', 'rh_gmw_vendor_location_synch', 10, 2 );
add_action( 'edit_user_profile_update', 'rh_gmw_vendor_location_synch' );

//////////////////////////////////////////////////////////////////
//WC Vendor FUNCTIONS
//////////////////////////////////////////////////////////////////

if (defined('wcv_plugin_dir')) {	
	if ( class_exists( 'WCVendors_Pro' ) ) {
		remove_action( 'woocommerce_before_single_product', array($wcvendors_pro->wcvendors_pro_vendor_controller, 'store_single_header'));		
		remove_action( 'woocommerce_after_shop_loop_item', array('WCV_Vendor_Shop', 'template_loop_sold_by'), 9 );
		remove_action( 'woocommerce_product_meta_start', array( 'WCV_Vendor_Cart', 'sold_by_meta' ), 10, 2 );
		add_action( 'rehub_vendor_show_action', 'wcv_vendor_show_vendor_loop', 9);
		add_action( 'wcvendors_settings_before_form', 'rh_show_gmw_form_before_wcvendor');
	}
	else{
		add_action('wcvendors_before_dashboard', 'rehub_woo_wcv_before_dash');
		add_action('wcvendors_after_dashboard', 'rehub_woo_wcv_after_dash');
		remove_action( 'woocommerce_before_single_product', array('WCV_Vendor_Shop', 'vendor_mini_header'));
		remove_action( 'woocommerce_after_shop_loop_item', array('WCV_Vendor_Shop', 'template_loop_sold_by'), 9 );
		remove_action( 'woocommerce_product_meta_start', array( 'WCV_Vendor_Cart', 'sold_by_meta' ), 10, 2 );
		add_action( 'rehub_vendor_show_action', 'wcv_vendor_show_vendor_loop', 9);
		add_filter('wcv_dashboard_nav_items', 'wcv_add_custom_submit_links');
		add_filter('wcv_dashboard_nav_item_classes', 'rhwcv_dashboard_nav_item_classes', 10, 2);
	}
	remove_action( 'woocommerce_before_main_content', array('WCV_Vendor_Shop', 'vendor_main_header'), 20 );
	remove_action( 'woocommerce_before_main_content', array('WCV_Vendor_Shop', 'shop_description'), 30 );
	if( !class_exists('WCVendors_Pro') && class_exists('WC_Vendors') ) {
		require_once ( locate_template( 'inc/wcvendor/wc-vendor-free-brand/class-shop-branding.php' ) );
		
		function wcv_add_custom_submit_links($items){
			if (rehub_option('url_for_add_product') && !empty($items['submit_link'])){
				unset($items['submit_link']);
				$items['submit_link'] = array(
					'url'    => esc_url(rehub_option('url_for_add_product')),
					'label'  => esc_html__('Add New Product', 'rehub-theme'),
					'target' => '_top',
				);
			}
			if (rehub_option('url_for_edit_product') && !empty($items['edit_link'])){
				unset($items['edit_link']);
				$items['edit_link']   = array(
					'url'    => esc_url(rehub_option('url_for_edit_product')),
					'label'  => esc_html__('Edit Products', 'rehub-theme'),
					'target' => '_top',
				);				
			}
			return $items;			
		}
		function rhwcv_dashboard_nav_item_classes($classes, $item_id){
			unset ($classes[0]);
			return $classes;
		}
	}
	function wcv_vendor_show_vendor_loop($product_id){
		if(class_exists('WCV_Vendor_Shop')){
			echo WCV_Vendor_Shop::template_loop_sold_by($product_id);
		}
	}			
} 

//////////////////////////////////////////////////////////////////
//DOKAN FUNCTIONS
//////////////////////////////////////////////////////////////////

if( class_exists( 'WeDevs_Dokan' ) ) {

	add_action('dokan_dashboard_wrap_before', 'rh_dokan_edit_page_before', 9);
	add_action('dokan_dashboard_wrap_after', 'rh_dokan_edit_page_after', 9);
	add_action('dokan_edit_product_wrap_before', 'rh_dokan_edit_page_before');
	add_action('dokan_edit_product_wrap_after', 'rh_dokan_edit_page_after');

	
	function rh_dokan_edit_page_before(){
		echo '<div class="rh-container">';
	}
	function rh_dokan_edit_page_after(){
		echo '</div>';
	}	
	
	/* 
	 * Set defailt theme value for banner sizes
	 */
	 function custom_dokan_set_banner_size() {
		$general_settings = get_option( 'dokan_general' );
		
		if( is_array($general_settings) && empty( $general_settings['store_banner_width'] ) ) {
			$general_settings['store_banner_width'] = 1900;
			$theme_width = true;
		} else {
			$theme_width = false;
		}
			
        if( is_array($general_settings) && empty( $general_settings['store_banner_height'] ) ) {
			$general_settings['store_banner_height'] = 300;
			$theme_height = true;
		} else {
			$theme_height = false;
		}
			
		if( $theme_width AND $theme_height )
			update_option( 'dokan_general', $general_settings );
		return false;
	 }
	 add_action( 'init', 'custom_dokan_set_banner_size' );
	 
	/* 
	 * Change store map description in plugin settings
	 */
	function custom_dokan_admin_settings( $settings_fields ){
		$settings_fields['dokan_general']['store_map']['desc']  = esc_html__( 'Enable showing link to Store location map on store', 'rehub-theme' );
			unset($settings_fields['dokan_general']['enable_theme_store_sidebar']);

		return $settings_fields;
	}
	add_filter( 'dokan_settings_fields', 'custom_dokan_admin_settings' );

	/* 
	 * Remove while Appearance tab in plugin settings
	 */
	function custom_dokan_remove_section($settings_fields){
		if(!empty($settings_fields) && is_array($settings_fields)){
			if(isset($settings_fields['dokan_appearance']['store_header_template'] )){
				unset($settings_fields['dokan_appearance']['store_header_template']);
			}
		}
        return $settings_fields;
	}
	add_filter( 'dokan_settings_fields', 'custom_dokan_remove_section' );

	/* 
	 * Change URL and Title of the About store tab 
	 */
	function custom_dokan_toc_url( $tabs ){
		$tabs['terms_and_conditions'] = array(
			'title' => apply_filters( 'dokan_about_store_title', esc_html__( 'Terms and Conditions', 'rehub-theme' ) ),
			'url'   => '#vendor-about'
		);
		return $tabs;
	}
	add_filter( 'dokan_store_tabs', 'custom_dokan_toc_url' );

	/* 
	 * Output Sold by <store_name> label in loop
	 */
	function dokan_loop_sold_by() {
		$vendor_id = get_the_author_meta( 'ID' );
		$store_info = dokan_get_store_info( $vendor_id );
		$sold_by = dokan_is_user_seller( $vendor_id )
			? sprintf( '<a href="%s">%s</a>', dokan_get_store_url( $vendor_id ), esc_html( $store_info['store_name'] ) )
			: get_bloginfo( 'name' );
		?>
		<small class="wcvendors_sold_by_in_loop"><span><?php echo apply_filters( 'dokan_sold_by_label', esc_html__( 'Sold by', 'rehub-theme' ) ); ?></span> <?php echo ''.$sold_by; ?></small><br />
		<?php
	}
	add_action( 'rehub_vendor_show_action', 'dokan_loop_sold_by' );
}

//////////////////////////////////////////////////////////////////
//WC Marketplace Functions
//////////////////////////////////////////////////////////////////

if( class_exists('WCMp')) {

	add_action('init', 'wcmp_remove_rh_hook_vendor', 11);
	add_action('rehub_vendor_show_action', 'wcmprh_loop_sold_by');
	add_filter('settings_vendor_dashboard_tab_options', 'wcmp_remove_rh_vendor_dashboard_template_settings');
	add_filter('is_vendor_add_external_url_field', 'rh_wcmp_add_external_url_field', 10, 2); //filter adds an external URL of vendor store
	add_action('widget_wcmp_quick_info_top', 'rh_anchor_vendor_contact_widget');
	add_action( 'wcmp_frontend_enqueue_scripts', 'rh_add_theme_style_wcmp' );
	add_filter('wcmp_frontend_dash_upload_script_params', 'rh_change_crop_for_wcmp');

	function rh_change_crop_for_wcmp($image_script_params){
		$image_script_params['default_logo_ratio'] = array(150,150);
		return $image_script_params;
	}

	function rh_add_theme_style_wcmp() {
		$theme_css = "#logo_mobile_wrapper, #rhmobpnlcustom{display:none}";
		wp_add_inline_style( 'vandor-dashboard-style', $theme_css );
	}

	function rh_anchor_vendor_contact_widget(){
		echo '<div id="wcmp-vendor-contact-widget-top"></div>';
	}
	function wcmp_remove_rh_hook_vendor(){
   		global $WCMp;
   		remove_action( 'woocommerce_product_meta_start', array( $WCMp->vendor_caps, 'wcmp_after_add_to_cart_form' ), 25);
   		remove_action( 'woocommerce_after_shop_loop_item_title', array( $WCMp->vendor_caps, 'wcmp_after_add_to_cart_form' ), 30);
   		//remove_action( 'woocommerce_after_shop_loop', array( $WCMp->review_rating, 'wcmp_seller_review_rating_form' ), 30);
	}
	function wcmp_remove_rh_vendor_dashboard_template_settings($settings_tab_options){
		if(isset($settings_tab_options['sections']['wcmp_vendor_shop_template']['fields']['wcmp_vendor_shop_template'])){
			unset($settings_tab_options['sections']['wcmp_vendor_shop_template']['fields']['wcmp_vendor_shop_template']);
		}
		return $settings_tab_options;
	}
	function rh_wcmp_add_external_url_field($status, $user_id = ''){
		return true;
	}
	function wcmprh_loop_sold_by(){
		global $WCMp, $post;
		if ('Enable' === get_wcmp_vendor_settings('sold_by_catalog', 'general') && apply_filters('wcmp_sold_by_text_after_products_shop_page', true, $post->ID)){
			$multivendor_product = $WCMp->product->get_multiple_vendors_array_for_single_product($post->ID);
			$vendor_id = get_the_author_meta( 'ID' );
			$is_vendor = is_user_wcmp_vendor( $vendor_id );
			$vendor_verified_label='';
			if($is_vendor && empty($multivendor_product['more_product_array'])){
				$vendor = get_wcmp_vendor($vendor_id);
				$store_url = $vendor->permalink;
				$store_name = $vendor->page_title; 
				$sold_by = sprintf('<a href="%s">%s</a>', $store_url, esc_html($store_name));
				$verified_vendor = get_user_meta($vendor_id, 'wcmp_vendor_is_verified', true);	
				if($verified_vendor){
					$vendor_verified_label = '<i class="rhicon rhi-shield-check greencolor"></i> ';
				}
			}elseif(!empty($multivendor_product['more_product_array'])){
				$sold_by = apply_filters('rh_sold_by_multivendor', esc_html__('Multiple vendor', 'rehub-theme'), $multivendor_product['more_product_array']);
			}else{
				$sold_by = apply_filters('rh_sold_by_site', get_bloginfo('name'));
			}
			$sold_by_text = apply_filters('wcmp_sold_by_text', esc_html__('Sold By', 'rehub-theme'));
			?><small class="wcvendors_sold_by_in_loop"><span><?php echo ''.$sold_by_text ?></span> <?php echo ''.$vendor_verified_label; echo ''.$sold_by; ?></small><br /><?php
		}
	}
	add_filter('wcmp_vendor_dashboard_nav', 'rh_wcmp_vendor_dashboard_nav' );
	if(!function_exists('rh_wcmp_vendor_dashboard_nav')) {
		function rh_wcmp_vendor_dashboard_nav($vendor_nav) {
			if(class_exists('WCFM'))
				return $vendor_nav;		
			if(current_user_can('edit_products')) {
				$userlogin_submit_page = rehub_option('url_for_add_product');
				$userlogin_edit_page = rehub_option('url_for_edit_product');
				if(!empty($userlogin_submit_page)) {
					$vendor_nav['vendor-products']['submenu']['add-new-product'] = array(
						'label' => esc_html__('Add Product', 'rehub-theme'),
						'url' => esc_url($userlogin_submit_page),
						'capability' => apply_filters('wcmp_vendor_dashboard_menu_add_new_product_capability', 'edit_products'), 
						'position' => 10, 
						'link_target' => '_self'
					);
					unset($vendor_nav['vendor-products']['submenu']['add-product']);
				}
				if(!empty($userlogin_edit_page)) {
					$vendor_nav['vendor-products']['submenu']['edit-products'] = array(
						'label' => esc_html__('Products', 'rehub-theme'),
						'url' => esc_url($userlogin_edit_page),
						'capability' => apply_filters('wcmp_vendor_dashboard_menu_vendor_products_capability', 'edit_products'), 
						'position' => 20, 
						'link_target' => '_self'
					);
					unset($vendor_nav['vendor-products']['submenu']['products']);
				}
			} else {
				unset($vendor_nav['vendor-products']);
			}
		return $vendor_nav;
		}
	}
	add_filter( 'wcmp_vendor_dashboard_header_nav', 'rh_wcmp_vendor_dashboard_header_nav', 10, 1 );
	function rh_wcmp_vendor_dashboard_header_nav( $header_nav ) {
		$userlogin_submit_page = rehub_option('url_for_add_product');
		if($userlogin_submit_page){
			unset($header_nav['add-product']); //remove Add Product Link
		}
		return $header_nav;
	}	
	if(class_exists('WCMP_Vendor_Vacation')){
		add_action('rhwoo_template_single_add_to_cart', 'rh_wcmp_vacation_addon_fix', 9);
	    function rh_wcmp_vacation_addon_fix() {
	        global $product;
	        $vendor_product = get_wcmp_product_vendors($product->get_id());
	        if ($vendor_product) {
				remove_action( 'rhwoo_template_single_add_to_cart', 'woocommerce_template_single_add_to_cart' );
	        }
	    }		
	}	
}

//////////////////////////////////////////////////////////////////
//WCFM vendor functions
//////////////////////////////////////////////////////////////////

if(defined( 'WCFMmp_TOKEN' )){
	add_filter('wcfmmp_store_sidebar_args', 'rh_wcfm_sidebar_args');
	function rh_wcfm_sidebar_args(){
		return array(
			'id'            => 'sidebar-wcfmmp-store',
			'name'          => esc_html__( 'Vendor store page sidebar', 'rehub-theme' ),
			'before_widget' => '<div class="rh-cartbox widget"><div>',
			'after_widget'  => '</div></div>',
			'before_title'  => '<div class="widget-inner-title rehub-main-font">',
			'after_title'   => '</div>',
		);
	}
	add_filter( 'wcfm_marketplace_settings_fields_store', 'rh_wcfm_store_settings', 11);
	function rh_wcfm_store_settings($args){
		if(isset($args['vendor_sold_by_position'])){
			unset($args['vendor_sold_by_position']);
		}
		return $args;
	}

	add_filter( 'wcfm_marketplace_settings_fields_visibility', 'rh_wcfm_vendor_settings', 11);
	function rh_wcfm_vendor_settings($args){
		if(isset($args['store_name_position'])){
			unset($args['store_name_position']);
		}
		return $args;
	}

	add_filter('wcfmvm_membership_color_setting_options', 'rh_wcfm_member_color_settings', 11);
	function rh_wcfm_member_color_settings($args){
		$args['wcfmvm_field_table_head_bg_color']['default'] = '#ffffff';
		$args['wcfmvm_field_table_head_price_color']['default'] = '#000000';
		$args['wcfmvm_field_table_border_color']['default'] = '#e0e0e0';
		$args['wcfmvm_field_table_bg_heighlighter_color']['default'] = '#fb7203';
		$args['wcfmvm_field_table_bg_heighlighter_color']['element'] = '#wcfm-main-contentainer .wcfm_featured_membership_box .wcfm_membership_box_head .wcfm_membership_title, #wcfm-main-contentainer .wcfm_membership_box_wrraper .wcfm_membership_box_head .wcfm_membership_featured_top';
		$args['wcfmvm_field_table_bg_heighlighter_color']['style2'] = 'color';
		$args['wcfmvm_field_table_bg_heighlighter_color']['element2'] = '#wcfm-main-contentainer .wcfm_featured_membership_box .wcfm_membership_box_head .wcfm_membership_price .amount';
		$args['wcfmvm_field_table_bg_heighlighter_color']['default2'] = '#fb7203';
		$args['wcfmvm_field_table_head_title_color']['default'] = '#ffffff';
		$args['wcfmvm_field_table_head_title_color']['element'] = '#wcfm-main-contentainer .wcfm_membership_box_wrraper .wcfm_membership_box_head .wcfm_membership_featured_top, #wcfm-main-contentainer .wcfm_membership_box_head .wcfm_membership_title';
		$args['wcfmvm_field_table_head_description_color']['default'] = '#444444';
		$args['wcfmvm_field_table_head_price_desc_color']['default'] = '#888888';
		$args['wcfmvm_field_button_color']['default'] = rehub_option('rehub_btnoffer_color');
		$args['wcfmvm_field_base_highlight_color']['default'] = '#009f0d';
		$args['wcfmvm_field_preview_plan_bg_color']['default'] = '#ffffff';
		unset($args['wcfmvm_field_preview_plan_text_color']['element3']);
		return $args;
	}
	add_filter('wcfm_color_setting_options', 'rh_wcfm_dash_color_settings', 11);
	function rh_wcfm_dash_color_settings($args){
		$activebg = rehub_option('rehub_custom_color') ? rehub_option('rehub_custom_color') : '#fb7203';
		$btncolor = rehub_option('rehub_btnoffer_color') ? rehub_option('rehub_btnoffer_color') : '#00b90f';
		$btncolortext = rehub_option('rehub_btnoffer_color_text') ? rehub_option('rehub_btnoffer_color_text') : '#ffffff';
		$args['wcfm_field_menu_icon_active_bg_color']['default'] = $activebg;
		$args['wcfm_field_menu_icon_active_bg_color']['element2'] = '#wcfm_menu .wcfm_menu_items:hover a span.wcfmfas';
		$args['wcfm_field_button_color']['default'] = $btncolor;
		$args['wcfm_field_base_highlight_color']['default'] = $activebg;
		$args['wcfm_field_button_text_color']['default'] = $btncolortext;
		$args['wcfm_field_secondary_font_color']['default'] = '#6ccedd';
		return $args;
	}
	add_filter('wcfmmp_store_color_setting_options', 'rh_wcfm_store_color_settings', 11);
	function rh_wcfm_store_color_settings($args){
		$activebg = rehub_option('rehub_custom_color') ? rehub_option('rehub_custom_color') : '#fb7203';
		$btncolor = rehub_option('rehub_btnoffer_color') ? rehub_option('rehub_btnoffer_color') : '#7000f4';
		$linkcolor = rehub_option('rehub_color_link') ? rehub_option('rehub_color_link') : '#0099cc';
		$btncolortext = rehub_option('rehub_btnoffer_color_text') ? rehub_option('rehub_btnoffer_color_text') : '#ffffff';
		$btnhovercolortext = rehub_option('rehub_btnofferhover_color_text') ? rehub_option('rehub_btnofferhover_color_text') : '#ffffff';
		$btnhovercolor = rehub_option('rehub_btnoffer_color_hover') ? rehub_option('rehub_btnoffer_color_hover') : '#7000f4';
		$args['wcfmmp_store_name_color']['default'] = '#ffffff';
		unset($args['wcfmmp_header_social_background_color']);
		unset($args['wcfmmp_star_rating_color']);
		$args['wcfmmp_sidebar_background_color']['element'] = '.wcvcontent .rh-cartbox .widget-inner-title';
		$args['wcfmmp_sidebar_background_color']['default'] = '#f7f7f7';
		$args['wcfmmp_sidebar_heading_color']['element'] = '.wcvcontent .rh-cartbox .widget-inner-title';
		$args['wcfmmp_sidebar_heading_color']['default'] = '#555555';
		$args['wcfmmp_sidebar_text_color']['default'] = '#111111';	
		$args['wcfmmp_tabs_text_color']['element'] = '#wcfmmp-store ul.rh-big-tabs-ul .rh-big-tabs-li a';
		$args['wcfmmp_tabs_active_text_color']['element'] = '#wcfmmp-store .rh-hov-bor-line > a:after';
		$args['wcfmmp_tabs_active_text_color']['default'] = $activebg;
		$args['wcfmmp_tabs_text_color']['default'] = '#111111';
		$args['wcfmmp_header_text_color']['default'] = '#111111';
		$args['wcfmmp_header_background_color']['default'] = '#ffffff';
		$args['wcfmmp_tabs_active_text_color']['style'] = 'background';
		$args['wcfmmp_button_bg_color']['default'] = $btncolor;
		$args['wcfmmp_button_text_color']['default'] = $btncolortext;
		$args['wcfmmp_button_active_text_color']['default'] = $btnhovercolortext;
		$args['wcfmmp_button_bg_color']['element'] = '#wcfmmp-store .add_review button, #wcfmmp-store .user_rated, #wcfmmp-stores-wrap a.wcfmmp-visit-store, #wcfmmp-store .bd_icon_box .wcfm_store_enquiry';
		$args['wcfmmp_header_text_color']['element'] = '#wcfmmp-store .address span, #wcfmmp-store .address a, #wcfmmp-store .address h1, #wcfmmp-store .address h2, #wcfmmp-store .social_area ul li:hover i, .wcvendor_profile_cell .profile-stats';
		$args['wcfmmp_button_bg_color']['element2'] = '#wcfmmp-store .reviews_heading a, #wcfmmp-store .reviews_count a, .wcfmmp_store_hours .wcfmmp-store-hours-day';
		$args['wcfmmp_button_text_color']['element'] = '#wcfmmp-store .add_review button, #wcfmmp-store .user_rated, #wcfmmp-stores-wrap a.wcfmmp-visit-store, #wcfmmp-store .bd_icon_box .wcfm_store_enquiry, #wcfmmp-store .bd_icon_box .wcfm_store_enquiry span';

		$args['wcfmmp_button_active_bg_color']['default'] = $btnhovercolor;
		$args['wcfmmp_button_active_bg_color']['element'] = '#wcfmmp-store .add_review button:hover, #wcfmmp-stores-wrap a.wcfmmp-visit-store:hover, #wcfmmp-store .bd_icon_box .wcfm_store_enquiry:hover';
		$args['wcfmmp_button_active_text_color']['element'] = '#wcfmmp-store .add_review button:hover, #wcfmmp-stores-wrap a.wcfmmp-visit-store:hover, #wcfmmp-store .bd_icon_box .wcfm_store_enquiry:hover, #wcfmmp-store .bd_icon_box .wcfm_store_enquiry:hover span';

		return $args;
	}
	add_action( 'rehub_vendor_show_action', array('WCFMmp_Frontend', 'wcfmmp_sold_by_product'), 50);

	function rh_wcfm_menus( $menus ) {
		$custom_menus = array();
		if(rehub_option('url_for_add_one') && rehub_option('label_for_add_one')){
			$custom_menus['wcfm-link-one'] = array(
				'label'  => rehub_option('label_for_add_one'),
				'url' => esc_url(rehub_option('url_for_add_one')),
				'icon' => 'cubes',
				'priority'  => 5.1,
			);
		}
		if(rehub_option('url_for_add_two') && rehub_option('label_for_add_two')){
			$custom_menus['wcfm-link-two'] = array(
				'label'  => rehub_option('label_for_add_two'),
				'url' => esc_url(rehub_option('url_for_add_two')),
				'icon' => 'cubes',
				'priority'  => 5.2,
			);
		}
		if(rehub_option('url_for_add_three') && rehub_option('label_for_add_three')){
			$custom_menus['wcfm-link-three'] = array(
				'label'  => rehub_option('label_for_add_three'),
				'url' => esc_url(rehub_option('url_for_add_three')),
				'icon' => 'cubes',
				'priority'  => 5.3,
			);
		}
		if(rehub_option('url_for_add_four') && rehub_option('label_for_add_four')){
			$custom_menus['wcfm-link-four'] = array(
				'label'  => rehub_option('label_for_add_four'),
				'url' => esc_url(rehub_option('url_for_add_four')),
				'icon' => 'cubes',
				'priority'  => 5.4,
			);
		}
		$menus = array_merge( $menus, $custom_menus );
			
		return $menus;
	}
	add_filter( 'wcfm_menus', 'rh_wcfm_menus', 20 );

}

//////////////////////////////////////////////////////////////////
//Woo REVIEWS
//////////////////////////////////////////////////////////////////

if(get_option( 'woocommerce_enable_reviews' ) === 'yes'){
	add_action( 'woocommerce_review_after_comment_text', 'getCommentLike_woo' );
}
function getCommentLike_woo( $comment ){
	echo getCommentLike_re('');
}


if (!function_exists('RH_get_quick_view')){
	function RH_get_quick_view( $product_id, $type='icon', $class=''){
		if( rehub_option('woo_quick_view') == '')
			return '';
		if($type=='icon'){
			return '<div class="quick_view_wrap '.esc_attr($class).'"><span class="flowhidden cell_quick_view"><span class="cursorpointer quick_view_button" data-product_id="'.$product_id.'"><i class="rhicon rhi-search-plus"></i></span></div>';
		}
		return '';
	}
}

//////////////////////////////////////////////////////////////////
//Quick View
//////////////////////////////////////////////////////////////////

if(rehub_option('woo_quick_view')){

	if( !function_exists('ajax_action_product_quick_view') ) {
		function ajax_action_product_quick_view() {

			$nonce = sanitize_text_field($_GET['nonce']);
			
	 		if ( ! wp_verify_nonce( $nonce, 'quickview-nonce' ) )
				wp_die ( 'Nope!' ); 
			
			$product_id = intval($_GET['product_id']);
			$query = new WP_Query( [ 'p' => $product_id, 'post_type' => 'product' ] );

	 		ob_start();
	        
	        while ( $query->have_posts() ) { $query->the_post();
				do_action( 'rehub_woo_quick_view', $product_id );
				include(rh_locate_template('inc/product_layout/popup_no_sidebar.php'));
			}
			 wp_reset_postdata();
			echo ''. ob_get_clean();
			exit;
		}
	}
	add_action( 'wp_ajax_product_quick_view', 'ajax_action_product_quick_view' );
	add_action( 'wp_ajax_nopriv_product_quick_view', 'ajax_action_product_quick_view' );


	if( !function_exists('rehub_woo_quick_view_action') ){
		function rehub_woo_quick_view_action($product_id){
			$product = wc_get_product( $product_id );
			$has_coupon = get_post_meta($product_id, 'rehub_woo_coupon_code', true);
			$rtl = is_rtl() ? 'true' : 'false';
			$stylesingle = get_template_directory_uri() . "/css/woosingle.css";
			$stylemodulo = get_template_directory_uri() . "/css/modulobox.min.css";
			?>
			<script type="text/javascript">
				var stylelen = jQuery('link[href="<?php echo esc_url($stylesingle) ?>"]').length;
				if(!stylelen) {jQuery('<link/>', {rel: 'stylesheet',type: 'text/css',href: '<?php echo esc_url($stylesingle) ?>'}).appendTo('head');}
				var modulocss = jQuery('link[href="<?php echo esc_url($stylemodulo) ?>"]').length;
				if(!modulocss) {jQuery('<link/>', {rel: 'stylesheet',type: 'text/css',href: '<?php echo esc_url($stylemodulo) ?>'}).appendTo('head');}
				var wc_single_product_params = {"flexslider":{"rtl":<?php echo ''.$rtl; ?>,"animation":"slide","smoothHeight":true,"directionNav":false,"controlNav":"thumbnails","slideshow":false,"animationSpeed":500,"animationLoop":false,"allowOneSlide":false},"flexslider_enabled":"1"};
				jQuery.getScript("<?php echo get_template_directory_uri() . '/js/jquery.flexslider-min.js' ?>");
				jQuery.getScript("<?php echo get_template_directory_uri() . '/js/modulobox.min.js' ?>");
				jQuery.getScript("<?php echo plugins_url( 'assets/js/frontend/single-product.min.js', WC_PLUGIN_FILE ); ?>");
			</script>
			<?php if( $product->get_type() == 'variable' ): ?>
			<script type="text/javascript">
				var wc_add_to_cart_variation_params = {"wc_ajax_url":"\/?wc-ajax=%%endpoint%%"};
				jQuery.getScript("<?php echo includes_url('js/underscore.min.js'); ?>");
				jQuery.getScript("<?php echo includes_url('js/wp-util.min.js'); ?>");
				jQuery.getScript("<?php echo plugins_url( 'assets/js/frontend/add-to-cart-variation.min.js', WC_PLUGIN_FILE ); ?>");
				if(jQuery(".rh-var-selector").length > 0){
					jQuery("input.rh-var-input").on("click", function(){
						if(jQuery(this).prop("checked")){
							var newValue = jQuery(this).val();
							var namevar = jQuery(this).attr("name");
							jQuery("select[name="+namevar+"]").val(newValue).trigger("change");
						}
					});
					jQuery(".rh-var-selector").each(function(){
						jQuery(this).prev("select").hide();
					});
				}
			</script>
			<script type="text/template" id="tmpl-variation-template">
				<div class="woocommerce-variation-description">{{{ data.variation.variation_description }}}</div>
				<div class="woocommerce-variation-price">{{{ data.variation.price_html }}}</div>
				<div class="woocommerce-variation-availability">{{{ data.variation.availability_html }}}</div>
			</script>
			<?php endif; ?>
			<?php if($has_coupon): ?>
			<script type="text/javascript">
				jQuery(window).ready(function($) {
				   'use strict';
					/* Coupons script & copy function */
				   $.getScript("<?php echo get_template_directory_uri() . '/js/clipboard.min.js' ?>", function(){
				   });
				});  
			</script>
			<?php endif; ?>
			<?php
		}
	}
	add_action('rehub_woo_quick_view', 'rehub_woo_quick_view_action');
}


//////////////////////////////////////////////////////////////////
//Fake Sold Counter
//////////////////////////////////////////////////////////////////
if(!function_exists('rh_soldout_bar')){
	function rh_soldout_bar( $post_id, $color = '#e33333' ){
		if(!$post_id){
			$post_id = get_the_ID();
		}
	    $manage_stock = get_post_meta( $post_id, '_manage_stock', true );
	    $soldout = '';
	    if($manage_stock == 'yes'):
	        $stock_available = ( $stock = get_post_meta( $post_id, '_stock', true ) ) ? round( $stock ) : 0;
	        $stock_sold = ( $total_sales = get_post_meta( $post_id, 'total_sales', true ) ) ? round( $total_sales ) : 0;
	        $soldout = ( $stock_available > 0 ) ? round( $stock_sold / $stock_available * 100 ) : '';
	    else:
	        $soldout = get_transient('rh-soldout-'. $post_id);
	        if(!$soldout):
	            $soldout = rand(10,100);
	            set_transient( 'rh-soldout-'. $post_id, $soldout, DAY_IN_SECONDS );
	        endif;
	    endif;
	    if ($soldout > 100){ $soldout = 95;}
	    ?>
		    <?php if($soldout):?>
			    <div class="soldoutbar mb10">
			        <div class="wpsm-bar minibar wpsm-clearfix mb5" data-percent="<?php echo (float)$soldout;?>%">
			            <div class="wpsm-bar-bar" style="background: <?php echo ''.$color; ?>"></div>
			        </div>
			        <div class="soldoutpercent greycolor font70 lineheight15"><?php esc_html_e( 'Already Sold:', 'rehub-theme' );?> <?php echo (float)$soldout;?>%</div>
			    </div>
			<?php endif;?>
	    <?php
	}
}

//////////////////////////////////////////////////////////////////
//Custom plus minus
//////////////////////////////////////////////////////////////////
if ( ! function_exists( 'rehub_cart_quantity_input' ) ) {
	function rehub_cart_quantity_input( $args = array(), $product = null, $echo = false ) {
		if ( is_null( $product ) ) {
			$product = $GLOBALS['product'];
		}

		$defaults = array(
			'input_id'     => uniqid( 'quantity_' ),
			'input_name'   => 'quantity',
			'input_value'  => '1',
			'classes'      => apply_filters( 'woocommerce_quantity_input_classes', array( 'input-text', 'qty', 'text' ), $product ),
			'max_value'    => apply_filters( 'woocommerce_quantity_input_max', -1, $product ),
			'min_value'    => apply_filters( 'woocommerce_quantity_input_min', 1, $product ),
			'step'         => apply_filters( 'woocommerce_quantity_input_step', 1, $product ),
			'pattern'      => apply_filters( 'woocommerce_quantity_input_pattern', has_filter( 'woocommerce_stock_amount', 'intval' ) ? '[0-9]*' : '' ),
			'inputmode'    => apply_filters( 'woocommerce_quantity_input_inputmode', has_filter( 'woocommerce_stock_amount', 'intval' ) ? 'numeric' : '' ),
			'product_name' => $product ? $product->get_title() : '',
			'placeholder'  => apply_filters( 'woocommerce_quantity_input_placeholder', '', $product ),
		);

		$args = apply_filters( 'woocommerce_quantity_input_args', wp_parse_args( $args, $defaults ), $product );

		// Apply sanity to min/max args - min cannot be lower than 0.
		$args['min_value'] = max( $args['min_value'], 0 );
		$args['max_value'] = 0 < $args['max_value'] ? $args['max_value'] : '';

		// Max cannot be lower than min if defined.
		if ( '' !== $args['max_value'] && $args['max_value'] < $args['min_value'] ) {
			$args['max_value'] = $args['min_value'];
		}

		ob_start();

		wc_get_template( 'global/rh-quantity-input.php', $args );

		if ( $echo ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo ob_get_clean();
		} else {
			return ob_get_clean();
		}
	}
}

//////////////////////////////////////////////////////////////////
//Cart panel
//////////////////////////////////////////////////////////////////
if ( ! function_exists( 'rh_update_sidebar_cart_item' ) ) {
	function rh_update_sidebar_cart_item() {
		if ( ( isset( $_GET['item_id'] ) && $_GET['item_id'] ) && ( isset( $_GET['qty'] ) ) ) {
			global $woocommerce;
			if ( $_GET['qty'] ) {
				$woocommerce->cart->set_quantity( $_GET['item_id'], $_GET['qty'] );
			} else {
				$woocommerce->cart->remove_cart_item( $_GET['item_id'] );
			}
		}

		WC_AJAX::get_refreshed_fragments();
	}
	
	add_action( 'wp_ajax_rh_update_sidebar_cart_item', 'rh_update_sidebar_cart_item' );
	add_action( 'wp_ajax_nopriv_rh_update_sidebar_cart_item', 'rh_update_sidebar_cart_item' );
}

add_filter('woocommerce_widget_cart_item_quantity', 'rh_widget_cart_quantity', 10, 3);
function rh_widget_cart_quantity($output, $cart_item, $cart_item_key){
	$quantity = '';
	$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
	if ( ! $_product->is_sold_individually() && $_product->is_purchasable() ) {
		$quantity .= rehub_cart_quantity_input(
			array(
				'input_value' => $cart_item['quantity'],
				'min_value' => 0,
				'max_value' => $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(),
			),
			$_product
		);
	}
	return $quantity.$output;
}
add_filter( 'woocommerce_mini_cart_item_class', 'rh_widget_cart_quantity_class', 10, 3 );
function rh_widget_cart_quantity_class( $string, $cart_item, $cart_item_key ){
	return $string.' cartkey-'.$cart_item_key;
}

if ( ! function_exists( 'rehub_woo_cart_panel' ) ) {
	function rehub_woo_cart_panel( $echo = true, $toolbar = false ) {
		?>
			<div id="rh-woo-cart-panel" class="from-right rh-sslide-panel">
				<div id="rh-woo-cart-panel-wrap" class="rh-sslide-panel-wrap">
					<div id="rh-woo-cart-panel-heading" class="rh-sslide-panel-heading">
						<h5 class="pt15 pb15 pr15 pl20 upper-text-trans mt0 mb0 font130"><?php esc_html_e( 'Shopping cart', 'rehub-theme' ); ?><i class="blackcolor closecomparepanel rh-sslide-close-btn cursorpointer floatright font130 rhi-times-circle rhicon" aria-hidden="true"></i></h5>
					</div>
					<div id="rh-woo-cart-panel-tabs" class="rh-sslide-panel-tabs abdfullwidth mt30 pb30 pt30 width-100p">
						<div class="rh-sslide-panel-inner font120 mt10 woocommerce widget_shopping_cart" id="rh-woo-cart-panel-content">
							<?php //the_widget( 'WC_Widget_Cart', 'title=' ); ?>
						</div>
					</div>
				</div>
			</div>
		<?php
	}
	$disablecartscripts = rehub_option('disable_woo_scripts');
	if ( 'yes' === get_option( 'woocommerce_enable_ajax_add_to_cart' ) && !$disablecartscripts){
		add_action( 'wp_footer', 'rehub_woo_cart_panel', 200 );
	}
}


?>