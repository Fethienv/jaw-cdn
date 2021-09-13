<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php global $product; global $post;?>
<?php if (empty( $product ) ) {return;}?>
<?php $attrelpanel = (isset($attrelpanel)) ? $attrelpanel : '';?>
<?php $woolinktype = (isset($woolinktype)) ? $woolinktype : '';?>
<?php $woo_aff_btn = rehub_option('woo_aff_btn');?>
<?php $affiliatetype = ($product->get_type() =='external') ? true : false;?>
<?php if($affiliatetype && ($woolinktype == 'aff' || $woo_aff_btn)) :?>
    <?php $woolink = $product->add_to_cart_url(); $wootarget = ' target="_blank" rel="nofollow sponsored"';?>
<?php else:?>
    <?php $woolink = get_post_permalink($post->ID); $wootarget = '';?>
<?php endif;?>

<div class="woocommerce type-product woocompactlist rh-flex-columns width-100p mb15 border-grey-bottom pb15 mobilesblockdisplay">    
    <?php do_action('woocommerce_before_shop_loop_item');?> 
    <div class="rh-flex-columns rh-flex-grow1 mobmb10">
        <div class="deal_img_wrap position-relative text-center width-80 height-80 img-width-auto"> 
            <?php 
                if($product->is_on_sale() && $product->get_regular_price() && $product->get_price() > 0 && !$product->is_type( 'variable' )){
                    $offer_price_calc = (float) $product->get_price();
                    $offer_price_old_calc = (float) $product->get_regular_price();
                    $sale_proc = 0 -(100 - ($offer_price_calc / $offer_price_old_calc) * 100); 
                    $sale_proc = round($sale_proc);
                    echo '<span class="rh-label-string abdposright greenbg mr0 mb5">'.$sale_proc.'%</span>';
                }

            ?>       
            <a href="<?php echo esc_url($woolink) ;?>"<?php echo ''.$wootarget ;?>>           
                <?php echo WPSM_image_resizer::show_wp_image('woocommerce_thumbnail'); ?>
            </a>
            <?php do_action( 'rh_woo_thumbnail_loop' ); ?>
        </div>
        <div class="woo_list_desc rh-flex-grow1 pr15 pl15">                            
            <h3 class="font90 mb15 mt0 fontnormal lineheight15"><a href="<?php echo esc_url($woolink) ;?>"<?php echo ''.$wootarget ;?>><?php the_title();?></a></h3>
            <?php rh_wooattr_code_loop($attrelpanel);?> 
            <?php if ($product->get_price() !='') : ?>
            <?php echo '<div class="pricefont110 rehub-main-color mobpricefont90 fontbold mb10 mr10 lineheight15"><span class="price">'.$product->get_price_html().'</span></div>';?>
            <?php endif ;?> 
            <?php if ( $product->managing_stock() ):?>
                <?php if(! $product->is_in_stock()):?>
                    <div class="stock out-of-stock mt5 redbrightcolor mb5"><?php esc_html_e('Out of Stock', 'rehub-theme');?></div>
                <?php else:?>
                    <div class="stock in-stock mt5 font80"><span class="greycolor">SKU: <?php echo ''.$product->get_sku();?></span>  <span class="greencolor"><i class="rhicon rhi-database mr5 ml10"></i><?php echo ''.$product->get_stock_quantity( );?> <?php esc_html_e('in stock', 'rehub-theme');?></span></div>
                <?php endif;?> 
            <?php endif;?>                                          
            <div class="clearfix"></div>
            <span class="woolist_vendor">
                <?php do_action( 'rehub_vendor_show_action' ); ?>                            
            </span>  
        </div> 
    </div>          
    
    <?php if ( 'yes' === get_option( 'woocommerce_enable_ajax_add_to_cart' ) && $product && $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock() && ! $product->is_sold_individually() ):?>
        <form action="<?php esc_url( $product->add_to_cart_url() );?>" class="rh-flex-columns mb10 rh-loop-quantity cart" method="post" enctype="multipart/form-data">
        <div class="rh-woo-quantity">
            <?php rehub_cart_quantity_input(array('mb'=> 'mb0'), $product, true);?>
        </div>
    <?php else:?>
        <div class="rh-flex-columns mb10 rh-loop-quantity">
    <?php endif;?>
        <div class="width-50 ml15">
            <?php  echo apply_filters( 'wholesale_loop_add_to_cart_link',
                sprintf( '<a href="%s" data-product_id="%s" data-product_sku="%s" class="re_track_btn woo_loop_btn rh-flex-center-align rh-flex-justify-center rh-shadow-sceu %s %s product_type_%s"%s %s><svg height="24px" version="1.1" viewBox="0 0 64 64" width="24px" xmlns="http://www.w3.org/2000/svg"><g><path d="M56.262,17.837H26.748c-0.961,0-1.508,0.743-1.223,1.661l4.669,13.677c0.23,0.738,1.044,1.336,1.817,1.336h19.35   c0.773,0,1.586-0.598,1.815-1.336l4.069-14C57.476,18.437,57.036,17.837,56.262,17.837z"/><circle cx="29.417" cy="50.267" r="4.415"/><circle cx="48.099" cy="50.323" r="4.415"/><path d="M53.4,39.004H27.579L17.242,9.261H9.193c-1.381,0-2.5,1.119-2.5,2.5s1.119,2.5,2.5,2.5h4.493l10.337,29.743H53.4   c1.381,0,2.5-1.119,2.5-2.5S54.781,39.004,53.4,39.004z"/></g></svg></a>',
                esc_url( $product->add_to_cart_url() ),
                esc_attr( $product->get_id() ),
                esc_attr( $product->get_sku() ),
                $product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
                $product->supports( 'ajax_add_to_cart' ) ? 'ajax_add_to_cart' : '',
                esc_attr( $product->get_type() ),
                $product->get_type() =='external' ? ' target="_blank"' : '',
                $product->get_type() =='external' ? ' rel="nofollow sponsored"' : ''
                ),
            $product );?>           
            <?php do_action( 'rh_woo_button_loop' ); ?>
        </div>
    <?php if ( 'yes' === get_option( 'woocommerce_enable_ajax_add_to_cart' ) && $product && $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock() && ! $product->is_sold_individually() ):?>
        </form>
    <?php else:?>
        </div>
    <?php endif;?>           

    <?php do_action( 'woocommerce_after_shop_loop_item' );?>
</div>