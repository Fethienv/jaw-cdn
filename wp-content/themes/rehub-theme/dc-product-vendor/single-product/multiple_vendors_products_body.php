<?php
/**
 * Single Product Multiple vendors
 *
 * This template can be overridden by copying it to yourtheme/dc-product-vendor/single-product/multiple_vendors_products_body.php.
 *
 * HOWEVER, on occasion WCMp will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * 
 * @author  WC Marketplace
 * @package dc-woocommerce-multi-vendor/Templates
 * @version 2.3.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $WCMp;
if(isset($more_product_array) && is_array($more_product_array) && count($more_product_array) > 0) {
	if(isset($sorting) && !empty($sorting)) {	
		/*function wcmp_sort_by_price($a, $b) {
			return $a['price_val'] - $b['price_val'];
		}*/		
		if($sorting == 'price') {		
			usort($more_product_array, function($a, $b){
				if (empty($a['price_val'])) return 1;
				if (empty($b['price_val'])) return -1;
				return ($a['price_val'] < $b['price_val']) ? -1 : 1;
			});
		}
		elseif($sorting == 'price_high') {		
			usort($more_product_array, function($a, $b){
				if (empty($a['price_val'])) return 1;
				if (empty($b['price_val'])) return -1;
				return ($a['price_val'] < $b['price_val']) ? -1 : 1;
			});
			$more_product_array = array_reverse (  $more_product_array);
		}
		elseif($sorting == 'rating') {
			$more_product_array = wcmp_sort_by_rating_multiple_product($more_product_array);			
		}
		elseif($sorting == 'rating_low') {
			$more_product_array = wcmp_sort_by_rating_multiple_product($more_product_array);
			$more_product_array = array_reverse (  $more_product_array);			
		}
	}
	foreach ($more_product_array as $more_product ) {	
            $_product = wc_get_product($more_product['product_id']);
		?>
		<div class="rh_listofferitem rh_list_mbl_im_left border-grey-bottom">
			<div class="rh-flex-center-align rh-flex-justify-center pt15 pb15 mobileblockdisplay">
	            <div class="rh_listcolumn rh_listcolumn_image text-center">  
					<?php $vendor_id = get_post_field( 'post_author', $more_product['product_id'] );?>
					<a href="<?php echo esc_url($more_product['shop_link']);?>">
						<img src="<?php echo rh_show_vendor_avatar($vendor_id, 90, 90);?>" class="vendor_store_image_single" width="90" height="90" />
						</a>								
	            </div>	
	            <div class="rh_listcolumn rh-flex-grow1 rh_listcolumn_text">
					<a href="<?php echo esc_url($more_product['shop_link']); ?>" class="wcmp_seller_name blackcolor rehub-main-font blockstyle mb10 font90 lineheight15"><?php echo esc_attr($more_product['seller_name']); ?></a> 
					<?php 
						if(isset($more_product['rating_data']) && is_array($more_product['rating_data']) && isset($more_product['rating_data']['avg_rating']) && $more_product['rating_data']['avg_rating']!=0 && $more_product['rating_data']['avg_rating']!=''){ 
							$rating_class = '';
							if($more_product['rating_data']['avg_rating'] > 4.5 ) {
								$rating_class = 'wcmp_superb_rating';
							}
							elseif($more_product['rating_data']['avg_rating'] <= 4.5 && $more_product['rating_data']['avg_rating'] > 4.0) {
								$rating_class = 'wcmp_excellent_rating';
							}
							elseif($more_product['rating_data']['avg_rating'] <= 4.0 && $more_product['rating_data']['avg_rating'] > 3.5) {
								$rating_class = 'wcmp_good_rating';
							}
							elseif($more_product['rating_data']['avg_rating'] <= 3.5 && $more_product['rating_data']['avg_rating'] > 2.5) {
								$rating_class = 'wcmp_above_averege_rating';
							}
							elseif($more_product['rating_data']['avg_rating'] <= 2.5 && $more_product['rating_data']['avg_rating'] >= 2.0) {
								$rating_class = 'wcmp_averege_rating';
							}
							elseif($more_product['rating_data']['avg_rating'] < 2.0 ) {
								$rating_class = 'wcmp_bad_rating';
							}
							echo '<span class="font80 '.$rating_class.'"> '.number_format($more_product['rating_data']['avg_rating'],2).'/5.0 </span>';										
						}else {
							echo "<span class='wcmp_norating'> ".__('no ratings','rehub-theme' )." </span>";
						}
					?>				                              
	            </div>  
	            <div class="rh_listcolumn rh_listcolumn_price text-center"> 
	            	<span class="price_count rehub-main-color rehub-btn-font"><?php echo ''.$_product->get_price_html(); ?></span>    
	            </div> 
	            <div class="text-right-align rh_listcolumn_btn">
	                <div class="priced_block clearfix">
					<?php if($more_product['product_type'] == 'simple') {?>
						<a href="<?php echo '?add-to-cart='.$more_product['product_id']; ?>" class="re_track_btn woo_loop_btn btn_offer_block mb5"><?php echo ''.$_product->add_to_cart_text() ?></a>
					<?php } ?>                
	                </div>
					<a href="<?php echo get_permalink($more_product['product_id']); ?>" class="font80 details-link-list" ><?php echo esc_html__('Details','rehub-theme'); ?></a>                
	            </div> 
            </div>                                							
			<div style="clear:both;"></div>							
		</div>
		
		
	<?php
	}
}
?>