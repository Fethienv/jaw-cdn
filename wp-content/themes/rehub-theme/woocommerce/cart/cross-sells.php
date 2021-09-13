<?php
/**
 * Cross-sells
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cross-sells.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     4.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$classes = array('products', 'col_wrap_three', 'rh-flex-eq-height', 'woorelatedgrid', 'compact_rel_grid');
if ( $cross_sells ) : ?>
	<div class="cross-sells">
		<?php $heading = apply_filters( 'woocommerce_product_cross_sells_products_heading', esc_html__( 'You may be interested in&hellip;', 'rehub-theme' ) );
		if ( $heading ) :
			?>
			<h2 class="font120"><?php echo ''.$heading; ?></h2>
		<?php endif; ?>
		<div class="<?php echo implode(' ',$classes);?>">
		<?php foreach ($cross_sells as $cross_sell): ?>
                    <?php 
						$item = $cross_sell->get_id();
						$post_object = get_post( $item );
							setup_postdata( $GLOBALS['post'] =& $post_object ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found
                        $title = get_the_title($item);
                        $url = get_the_permalink($item);
						$image_id = '';
                        if ( has_post_thumbnail($item) ){
                            $image_id = get_post_thumbnail_id($item);  
                            $image_url = wp_get_attachment_image_src($image_id, 'full'); 
                            $image_url = $image_url[0];
                            $image_url = apply_filters('rh_thumb_url', $image_url );
                        }

                    ?>
                    <div class="col_item border-lightgrey pb10 pl10 pr10 pt10">
                        <div class="medianews-img width-80 floatleft mr20 rtlml20">
                            <a href="<?php echo esc_url($url);?>">
                            <?php echo wp_get_attachment_image($image_id, 'minithumb');?> 
                            </a>                    
                        </div>
                        <div class="medianews-body floatright width-100-calc">
                            <h5 class="font90 lineheight20 mb10 mt0 fontnormal">
                                <a href="<?php echo esc_url($url);?>"><?php echo strip_tags($title);?></a>
                            </h5>
                            <div class="font80 lineheight15 greencolor">
                                <?php  
                                    $the_price = get_post_meta( $item, '_price', true);  
                                    if ( '' != $the_price ) {
                                        if(rehub_option('ce_custom_currency')){
                                            $currency_code = rehub_option('ce_custom_currency');
                                            $woocurrency = get_woocommerce_currency(); 
                                            if($currency_code != $woocurrency && defined('\ContentEgg\PLUGIN_PATH')){
                                                $currency_rate = \ContentEgg\application\helpers\CurrencyHelper::getCurrencyRate($woocurrency, $currency_code);
                                                if (!$currency_rate) $currency_rate = 1;
                                                $the_price = \ContentEgg\application\helpers\TemplateHelper::formatPriceCurrency($the_price*$currency_rate, $currency_code, '<span class="woocommerce-Price-currencySymbol">', '</span>');
                                            }
                                            else{
                                                $the_price = wc_price( $the_price ) ;
                                            }                                               
                                        }else{
                                            $the_price = wc_price( $the_price );
                                        }
                                        echo strip_tags($the_price);
                                    }  
                                ?>
                            </div>
                            <?php if(rehub_option('compare_page') || rehub_option('compare_multicats_textarea')) :?>  
                                <div class="woo-btn-actions-notext mt10">         
                                <?php 
                                    $cmp_btn_args = array(); 
                                    $cmp_btn_args['class']= 'rhwoosinglecompare';
                                    $cmp_btn_args['id'] = $item;
                                    if(rehub_option('compare_woo_cats') != '') {
                                        $cmp_btn_args['woocats'] = esc_html(rehub_option('compare_woo_cats'));
                                    }
                                ?>                                                  
                                <?php echo wpsm_comparison_button($cmp_btn_args); ?>
                                </div> 
                            <?php endif;?>                                                        
                        </div>
                    </div>
                <?php endforeach; ?>

		</div>
	</div>
<?php endif;
wp_reset_postdata();