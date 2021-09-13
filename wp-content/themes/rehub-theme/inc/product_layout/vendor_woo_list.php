<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php global $product; global $post;?>
<?php                             
    if ( post_password_required() ) {
        echo '<div class="rh-container"><div class="rh-content-wrap clearfix"><div class="main-side clearfix full_width" id="content"><div class="post text-center">';
            echo get_the_password_form();
        echo '</div></div></div></div>';
        return;
    }
?>
<!-- CONTENT -->
<div class="rh-container"> 
    <div class="rh-content-wrap clearfix">
        <!-- Main Side -->
        <div class="main-side page clearfix vendor_woo_list full_width" id="content">
            <div class="post">              
                <?php do_action( 'woocommerce_before_main_content' );?>
                <?php if(!rehub_option('rehub_disable_breadcrumbs')){woocommerce_breadcrumb();}?>
                <?php while ( have_posts() ) : the_post(); ?>
                    <?php do_action( 'woocommerce_before_single_product' );?> 
                    <div id="product-<?php the_ID(); ?>" <?php post_class(); ?>>
                        <?php wp_enqueue_script('stickysidebar');?>
                        <div class="rh-stickysidebar-wrapper">
                            <div class="woo-image-part position-relative wpsm-one-half rh-sticky-container modulo-lightbox tabletblockdisplay">
                            <?php  $badge = get_post_meta($post->ID, 'is_editor_choice', true); ?>
                            <?php if ($badge !='' && $badge !='0') :?> 
                                <?php echo re_badge_create('ribbonleft'); ?>
                            <?php else:?>                                        
                                <?php woocommerce_show_product_sale_flash();?>
                            <?php endif;?>
                            <?php $columns_thumbnails = 5?>
                            <?php include(rh_locate_template('woocommerce/single-product/product-image.php')); ?>
                            <?php do_action('rh_woo_after_single_image');?>
                                <div class="re_wooinner_info mb20">
                                    <?php
                                        /**
                                         * woocommerce_single_product_summary hook. was removed in theme and added as functions directly in layout
                                         *
                                         * @dehooked woocommerce_template_single_title - 5
                                         * @dehooked woocommerce_template_single_rating - 10
                                         * @dehooked woocommerce_template_single_price - 10
                                         * @dehooked woocommerce_template_single_excerpt - 20
                                         * @dehooked woocommerce_template_single_add_to_cart - 30
                                         * @dehooked woocommerce_template_single_meta - 40
                                         * @dehooked woocommerce_template_single_sharing - 50
                                         * @hooked WC_Structured_Data::generate_product_data() - 60
                                         */
                                        do_action( 'woocommerce_single_product_summary' );
                                    ?>              
                                    <div class="mb10 font85"><?php woocommerce_template_single_meta();?></div>
                                    <?php woocommerce_template_single_sharing();?>
                                </div>
                            </div>

                            <div class="tabletblockdisplay wpsm-column-last wpsm-one-half summary entry-summary rh-sticky-container">
                                <div class="mb10">
                                    <?php woocommerce_template_single_title();?>
                                    <?php woocommerce_template_single_rating();?>            
                                </div>
                                <div class="woo-button-actions-area mb20">
                                    <?php $wishlistadd = esc_html__('Add to wishlist', 'rehub-theme');?>
                                    <?php $wishlistadded = esc_html__('Added to wishlist', 'rehub-theme');?>
                                    <?php $wishlistremoved = esc_html__('Removed from wishlist', 'rehub-theme');?>
                                    <?php echo RH_get_wishlist($post->ID, $wishlistadd, $wishlistadded, $wishlistremoved);?>  
                                    <?php if(rehub_option('compare_page') || rehub_option('compare_multicats_textarea')) :?>           
                                        <?php 
                                            $cmp_btn_args = array(); 
                                            $cmp_btn_args['class']= 'rhwoosinglecompare';
                                            if(rehub_option('compare_woo_cats') != '') {
                                                $cmp_btn_args['woocats'] = esc_html(rehub_option('compare_woo_cats'));
                                            }
                                        ?>                                                  
                                        <?php echo wpsm_comparison_button($cmp_btn_args); ?> 
                                    <?php endif;?>
                                </div> 
                                <div class="clearfix"></div> 
                                <div class="re_wooinner_info mb20">                                    
                                    <?php rh_woo_code_zone('content');?>
                                    <?php echo wpsm_reviewbox(array('compact'=>1, 'id'=> $post->ID, 'scrollid'=>'tab-title-description'));?>                                     
                                    <?php woocommerce_template_single_excerpt();?>
                                </div>                                                           
                                <div class="woo-ce-list-area">
                                    <div class="border-lightgrey clearbox flowhidden mb25 rh-shadow1 rh-tabletext-block whitebg width-100p">
                                    <div class="rh-tabletext-block-heading fontbold"><span class="cursorpointer floatright lineheight15 ml10 toggle-this-table rtlmr10"></span><h4><?php esc_html_e('Price list', 'rehub-theme');?></h4></div>
                                    <?php if( class_exists('WCMp')):?>
                                            <div class="vendor-list-container rh_listoffers rh_listoffers_price_col">
                                                               
                                            <?php
                                                global $WCMp;
                                                $multivendor_product = $WCMp->product->get_multiple_vendors_array_for_single_product($post->ID);
                                                $more_product_array = $multivendor_product['more_product_array'];

                                                //We add also details of current product and attach to list of all prices (function is from classes/ class-wpsm-product.php)
                                                $current_vendor_array = array();
                                                $vendor_data = get_wcmp_product_vendors($post->ID);
                                                if ($vendor_data) {
                                                    if (isset($vendor_data->page_title)) {
                                                        $current_vendor_array['seller_name'] = $vendor_data->page_title;
                                                        $current_vendor_array['is_vendor'] = 1;

                                                        $current_vendor_array['shop_link'] = $vendor_data->permalink;
                                                        $current_vendor_array['rating_data'] = wcmp_get_vendor_review_info($vendor_data->term_id);
                                                    }
                                                } else {
                                                    $current_vendor_array['seller_name'] = isset($other_user->data->display_name) ? $other_user->data->display_name : '';
                                                    $current_vendor_array['is_vendor'] = 0;
                                                    $current_vendor_array['shop_link'] = get_permalink(wc_get_page_id('shop'));
                                                    $current_vendor_array['rating_data'] = 'admin';
                                                }
                                                $currency_symbol = get_woocommerce_currency_symbol();
                                                $regular_price_val = $product->get_regular_price();
                                                $sale_price_val = $product->get_sale_price();
                                                $price_val = $product->get_price();
                                                $current_vendor_array['product_name'] = $product->get_title();
                                                $current_vendor_array['regular_price_val'] = $regular_price_val;
                                                $current_vendor_array['sale_price_val'] = $sale_price_val;
                                                $current_vendor_array['price_val'] = $price_val;
                                                $current_vendor_array['product_id'] = $post->ID;
                                                $current_vendor_array['product_type'] = $product->get_type();
                                                if ($product->get_type() == 'variable') {
                                                    $current_vendor_array['_min_variation_price'] = get_post_meta($post->ID, '_min_variation_price', true);
                                                    $current_vendor_array['_max_variation_price'] = get_post_meta($post->ID, '_max_variation_price', true);
                                                    $variable_min_sale_price = get_post_meta($post->ID, '_min_variation_sale_price', true);
                                                    $variable_max_sale_price = get_post_meta($post->ID, '_max_variation_sale_price', true);
                                                    $current_vendor_array['_min_variation_sale_price'] = $variable_min_sale_price ? $variable_min_sale_price : $current_vendor_array['_min_variation_price'];
                                                    $current_vendor_array['_max_variation_sale_price'] = $variable_max_sale_price ? $variable_max_sale_price : $current_vendor_array['_max_variation_price'];
                                                    $current_vendor_array['_min_variation_regular_price'] = get_post_meta($post->ID, '_min_variation_regular_price', true);
                                                    $current_vendor_array['_max_variation_regular_price'] = get_post_meta($post->ID, '_max_variation_regular_price', true);
                                                }

                                                $more_product_array[] = $current_vendor_array;                                                    
                                                $WCMp->template->get_template('single-product/multiple_vendors_products_body.php', array('more_product_array' => $more_product_array, 'sorting' => 'price'));
                                            ?>      
                                              
                                        </div>                                             
                                    <?php endif; ?>
                                    <?php
                                        /**
                                         * woocommerce_after_single_product_summary hook.
                                         *
                                         * @hooked woocommerce_output_product_data_tabs - 10
                                         * @hooked woocommerce_upsell_display - 15
                                         * @hooked woocommerce_output_related_products - 20
                                         */
                                        do_action( 'woocommerce_after_single_product_summary' );
                                    ?>
                                    <?php rh_woo_code_zone('button');?>
                                    </div>
                                </div>  
                            </div>
                        </div>
                        <div class="clearfix"></div> 
                        <?php 
                            $tabs = apply_filters( 'woocommerce_product_tabs', array() );                          
                        ?> 
                        <?php if ( ! empty( $tabs ) ) : ?>                        
                            <div id="contents-section-woo-area" class="flowhidden">
                                <div class="clearfix border-lightgrey <?php if ( is_active_sidebar( 'sidebarwooinner' ) ) : ?>tabletblockdisplay rh-300-content-area floatleft<?php else:?>woo_default_no_sidebar<?php endif; ?>">

                                    <div class="woocommerce-tabs wc-tabs-wrapper">
                                        <ul class="tabs wc-tabs wc-tabs-light" role="tablist">
                                            <?php foreach ( $tabs as $key => $tab ) : ?>
                                                <li class="<?php echo esc_attr( $key ); ?>_tab" id="tab-title-<?php echo esc_attr( $key ); ?>" role="tab" aria-controls="tab-<?php echo esc_attr( $key ); ?>">
                                                    <a href="#tab-<?php echo esc_attr( $key ); ?>"><?php echo apply_filters( 'woocommerce_product_' . $key . '_tab_title', esc_html( $tab['title'] ), $key ); ?></a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                        <?php foreach ( $tabs as $key => $tab ) : ?>
                                            <div class="woocommerce-Tabs-panel padd20 woocommerce-Tabs-panel--<?php echo esc_attr( $key ); ?> panel entry-content wc-tab" id="tab-<?php echo esc_attr( $key ); ?>" role="tabpanel" aria-labelledby="tab-title-<?php echo esc_attr( $key ); ?>">
                                                <?php call_user_func( $tab['callback'], $key, $tab ); ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    
                                </div>

                                <?php if ( is_active_sidebar( 'sidebarwooinner' ) ) : ?>
                                    <aside class="rh-300-sidebar tabletblockdisplay floatright">            
                                        <?php dynamic_sidebar( 'sidebarwooinner' ); ?>      
                                    </aside> 
                                <?php endif; ?> 

                            </div>
                        <?php endif; ?>                       

                    </div><!-- #product-<?php the_ID(); ?> -->

                    <?php do_action( 'woocommerce_after_single_product' ); ?>
                <?php endwhile; // end of the loop. ?> 
                <?php do_action( 'woocommerce_after_main_content' ); ?>                                                  
            </div>
        </div>  
        <!-- /Main Side --> 

    </div>
</div>
<!-- /CONTENT --> 
<!-- Related -->
<?php include(rh_locate_template( 'woocommerce/single-product/full-width-related.php' ) ); ?>                      
<!-- /Related -->

<!-- Upsell -->
<?php include(rh_locate_template( 'woocommerce/single-product/full-width-upsell.php' ) ); ?>
<!-- /Upsell --> 

<?php rh_woo_code_zone('bottom');?>