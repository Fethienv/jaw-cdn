<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit('Restricted Access');
} // Exit if accessed directly

/**
 * Info box Widget class.
 *
 * 'wpsm_box' shortcode
 *
 * @since 1.0.0
 */
class Widget_Woo_EL_dealday extends WPSM_Widget_Base {

    /* Widget Name */
    public function get_name() {
        return 'wpsm_woofeatured';
    }

    /* Widget Title */
    public function get_title() {
        return esc_html__('Woo Deal Block', 'rehub-theme');
    }
    public function get_icon() {
        return 'eicon-favorite';
    }    
    protected function get_sections() {
        return [
            'general'   => esc_html__('General', 'rehub-theme'),
        ];
    }
    public function get_script_depends() {
        return [ 'owlcarousel', 'owlinit' ];
    }
    public function get_style_depends() {
        return [ 'rhcarousel' ];
    }
    protected function general_fields() {
        $this->add_control( 'ids', [
            'type'        => 'select2ajax',
            'label'       => esc_html__( 'Product names', 'rehub-theme' ),
            'description' => esc_html__( 'Enter the Name of Products', 'rehub-theme' ),
            'options'     => [],
            'label_block'  => true,
            'multiple'     => true,
            'callback'    => 'get_wc_products_posts_list'
        ]);
        $this->add_control( 'title', [
            'type'        => \Elementor\Controls_Manager::TEXT,
            'label'       => esc_html__( 'Add title for block', 'rehub-theme' ),
            'default'     => 'Deal of Day',
            'label_block' => true,
        ]);      
        $this->add_control( 'faketimer', [
            'type'        => \Elementor\Controls_Manager::SWITCHER,
            'label'       => esc_html__( 'Set fake timer', 'rehub-theme' ),
            'description' => esc_html__('By default, widget shows countdown base on Sale price dates of product. You can enable fake timer (always shows 12 hours)', 'rehub-theme'),
            'label_on'    => esc_html__('Yes', 'rehub-theme'),
            'label_off'   => esc_html__('No', 'rehub-theme'),
            'return_value' => '1',
        ]);
        $this->add_control( 'markettext', [
            'type'        => \Elementor\Controls_Manager::TEXT,
            'label'       => esc_html__( 'Add marketing text', 'rehub-theme' ),
            'default'     => esc_html__( 'Hurry Up! Offer ends soon.', 'rehub-theme' ),
            'label_block' => true,
        ]);  
        $this->add_control( 'fakebar', [
            'type'        => \Elementor\Controls_Manager::SWITCHER,
            'label'       => esc_html__( 'Set fake sold bar:', 'rehub-theme' ),
            'description' => esc_html__('By default, widget shows real progress bar based on stock status, you can enable fake bar', 'rehub-theme'),
            'label_on'    => esc_html__('Yes', 'rehub-theme'),
            'label_off'   => esc_html__('No', 'rehub-theme'),
            'return_value' => '1',
        ]); 
        $this->add_control(
            'fakebar_sold',
            [
                'label' => esc_html__( 'Sold', 'rehub-theme' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 12,
                'condition'  => [ 'fakebar' => '1' ],
                'min' => 1,
                'step' => 1,                
            ]
        );  
        $this->add_control(
            'fakebar_stock',
            [
                'label' => esc_html__( 'In Stock', 'rehub-theme' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 16,
                'condition'  => [ 'fakebar' => '1' ],
                'min' => 1,
                'step' => 1,                
            ]
        );
        $this->add_control( 'autorotate', [
            'type'        => \Elementor\Controls_Manager::SWITCHER,
            'label'       => esc_html__( 'Auto rotation for carousel', 'rehub-theme' ),
            'label_on'    => esc_html__('Yes', 'rehub-theme'),
            'label_off'   => esc_html__('No', 'rehub-theme'),
            'return_value' => '1',
        ]);                                             
    }

    /* Widget output Rendering */
    protected function render() {
        $settings = $this->get_settings_for_display();

        /* Our variables from the widget settings. */
        $title = isset($settings['title']) ? $settings['title'] : '';
        $ids = (!empty($settings['ids'])) ? $settings['ids'] : '';
        $faketimer = (!empty($settings['faketimer'])) ? $settings['faketimer'] : '';
        $fakebar = (!empty($settings['fakebar'])) ? $settings['fakebar'] : '';
        $fakebar_sold = (!empty($settings['fakebar_sold'])) ? $settings['fakebar_sold'] : 12;
        $fakebar_stock = (!empty($settings['fakebar_stock'])) ? $settings['fakebar_stock'] : 16;
        $markettext = ( !empty( $settings['markettext'] ) ) ? $settings['markettext'] : '';
        $autorotate = ( !empty( $settings['autorotate'] ) ) ? $settings['autorotate'] : '';
        $index = 0;
        $carousel = $carouselnumber = false;

        if(!empty($ids)){
            $carouselnumber = count($ids);
            if ($carouselnumber > 1){
                $carousel = true;
            }
        }        
        if ( !empty($ids) ) {
            $query = array( 
                'post_status' => 'publish', 
                'ignore_sticky_posts' => 1, 
                'post_type' => 'product', 
                'no_found_rows'=>1,
                'post__in' => $ids,
            );
        } else {
            $query = array(
                'posts_per_page'=>'1',
                'post_type' => 'product',            
            );
            $query['posts_per_page'] = $carouselnumber = 3;
            
            $meta_query = $tax_query = array();
            $product_ids_on_sale = wc_get_product_ids_on_sale();
            $tax_query[] = WC()->query->get_tax_query();
            $query['tax_query'] = $tax_query;
            $query['post__in'] = array_merge( array( 0 ), $product_ids_on_sale );
            $query['no_found_rows'] = 1;   
            $carousel = true;     
        }
        $autodata = ($autorotate) ? 'data-auto="1"' : 'data-auto="0"' ;
        $loop = new \WP_Query( $query );
        echo '<div class="deal_daywoo woocommerce position-relative custom-nav-car flowhidden">';
            echo '<style scoped>
                @media (min-width: 600px){
                    .elementor-widget:not(.elementor-widget-sidebar) .deal_daywoo figure, .elementor-widget:not(.elementor-widget-sidebar) .deal_daywoo .dealdaycont{width: 50%; float: left;}
                    .elementor-widget:not(.elementor-widget-sidebar) .deal_daywoo .dealdaycont{padding: 0 16px}
                    .elementor-widget:not(.elementor-widget-sidebar) .deal_daywoo figure img{max-height: 350px;}
                    .elementor-widget:not(.elementor-widget-sidebar) .deal_daywoo figure a{max-height: 350px}
                }
                .deal_daywoo .price{ color: #489c08; font-weight: bold;font-size: 22px; line-height: 18px }
                .deal_daywoo figure a{min-height: 250px}
                .deal_daywoo figure img{width: auto;}
                .sidebar .deal_daywoo figure img{max-height: 250px;}
                .sidebar .deal_daywoo figure a{max-height: 250px}
                body .deal_daywoo .title:after{display: none;}
                .deal_daywoo h3, .woo_feat_slider h3{font-size: 18px}
                .deal_daywoo .wpsm-bar-bar, .deal_daywoo .wpsm-bar, .deal_daywoo .wpsm-bar-percent{height: 20px; line-height: 20px}
                </style>
            ';
            if ( $loop->have_posts() ) :
                if ( $title ) : ?><div class="title"><?php echo ''.$title; ?><?php if ($carousel) :?><span class="greycolor font120 cursorpointer mr5 ml5 cus-car-next floatright"><span class="rhicon rhi-arrow-square-right"></span></span><span class="greycolor font120 cursorpointer mr5 ml5 cus-car-prev floatright"><span class="rhicon rhi-arrow-square-left"></span></span><?php endif; ?></div><?php endif; ?>
                <?php if ($carousel) :?>
                    <?php wp_enqueue_style('rhcarousel');wp_enqueue_script('owlcarousel'); wp_enqueue_script('owlinit'); ?>
                    <div class="loading woo_carousel_block mb0"><div class="woodealcarousel re_carousel" data-showrow="1" <?php echo ''.$autodata;?> data-laizy="0" data-fullrow="3" data-navdisable="1">
                <?php endif; ?>
                <?php while( $loop->have_posts() ) : $loop->the_post(); $index++; ?>
                    <div class="woo_spec_widget product">
                        <?php $post_id = $loop->post->ID; ?>             
                        <?php $_product = wc_get_product( $post_id ); if(empty($_product)) continue; ?>
                        <?php 
                            $target_blank = ( $_product->get_type() == 'external' ) ? ' target="_blank" rel="nofollow"' : '' ;
                            $product_link = ( $_product->get_type() == 'external' ) ? $_product->add_to_cart_url() : get_the_permalink($post_id);
                            $offer_coupon_date = get_post_meta( $post_id, 'rehub_woo_coupon_date', true );
                        ?>
                        <figure class="position-relative">
                            <?php
                                if ( $_product->is_featured() ) :
                                    echo apply_filters( 'woocommerce_featured_flash', '<span class="onfeatured">' . esc_html__( 'Featured!', 'rehub-theme' ) . '</span>', $loop->post, $_product );
                                endif; 
                                if ( $_product->is_on_sale() ) : 
                                    $percentage = 0;
                                    $featured = ( $_product->is_featured() ) ? ' onsalefeatured' : '';
                                    if ( $_product->get_regular_price() ) {
                                        $percentage = round( ( ( $_product->get_regular_price() - $_product->get_price() ) / $_product->get_regular_price() ) * 100 );
                                    }
                                    if ( $percentage && $percentage > 0 && !$_product->is_type( 'variable' ) ) {
                                        $sales_html = apply_filters( 'woocommerce_sale_flash', '<span class="onsale'. $featured .'"><span>- ' . $percentage . '%</span></span>', $loop->post, $_product );
                                    } else {
                                        $sales_html = apply_filters( 'woocommerce_sale_flash', '<span class="onsale'. $featured .'">' . esc_html__( 'Sale!', 'rehub-theme' ) . '</span>', $loop->post, $_product );
                                    }
                                    echo ''.$sales_html;
                                endif; 
                            ?>
                            <a class="img-centered-flex rh-flex-center-align rh-flex-justify-center" href="<?php echo esc_url($product_link) ; ?>"<?php echo ''.$target_blank;?>>
                                <?php echo \WPSM_image_resizer::show_wp_image('woocommerce_single'); ?>
                            </a>          
                        </figure>
                        <div class="dealdaycont">
                            <div class="woo_loop_desc"> 
                                <h3>      
                                    <a class="<?php echo getHotIconclass($post_id); ?>" href="<?php echo esc_url($product_link) ;?>"<?php echo ''.$target_blank; ?>>
                                            <?php echo rh_expired_or_not($post_id, 'span');?>     
                                            <?php the_title();?>
                                    </a>
                                </h3>                
                                <?php do_action( 'rehub_vendor_show_action' ); ?>            
                            </div>  
                            <div class="woo_spec_price">
                                <?php wc_get_template( 'loop/price.php' ); ?>
                            </div>
                            <?php // stock wpsm_bar
                            if ($fakebar) {
                                $stock_sold = $fakebar_sold; 
                                $stock_available = $fakebar_stock;
                                if($index > 1){
                                    $stock_sold = $stock_sold + ($index * 3);
                                    $stock_available = $stock_available + ($index * 5);
                                }
                            } else {
                                $stock_sold = ( $total_sales = get_post_meta( $post_id, 'total_sales', true ) ) ? round( $total_sales ) : 0;
                                $stock_available = ( $stock = get_post_meta( $post_id, '_stock', true ) ) ? round( $stock ) : 0;            
                            } 
                            $percentage = ( $stock_available > 0 ) ? round( $stock_sold / $stock_available * 100 ) : '';    
                            ?>
                            
                            <?php if ( !empty($percentage) ) : ?>        
                                <div class="woo_spec_bar mt30 mb20">
                                    <div class="deal-stock mb10">
                                    <span class="stock-sold floatleft">
                                        <?php esc_html_e( 'Already Sold:', 'rehub-theme' );?> <strong><?php echo esc_html( $stock_sold ); ?></strong>
                                    </span>
                                    <span class="stock-available floatright">
                                        <?php esc_html_e( 'Available:', 'rehub-theme' );?> <strong><?php echo esc_html( $stock_available ); ?></strong>
                                    </span>
                                    </div>
                                    <?php if ( $percentage == 0 ) { $percentage = 10; }?>
                                    <?php echo wpsm_bar_shortcode(array('percentage'=>$percentage));?>
                                </div>   
                            <?php endif;?>
                            <div class="marketing-text mt15 mb15"><?php echo esc_attr($markettext); ?></div>    
                            <?php if( $faketimer ) : ?>
                                <?php 
                                    $currenttime = current_time('mysql',0);
                                    $now = new \DateTime($currenttime);

                                    $now->modify( '+'.$index.' day' );
                                    $month = $now->format( 'm' );
                                    $year = $now->format( 'Y' );
                                    $day = $now->format( 'd' );
                                ?>
                            <?php else:?>
                                <?php 
                                    $sale_price_dates_to = ( $date = get_post_meta( $post_id, '_sale_price_dates_to', true ) ) ? date_i18n( 'Y-m-d', $date ) : $offer_coupon_date;
                                    if ( $sale_price_dates_to ) {
                                        $expireddate = explode('-', $sale_price_dates_to);
                                        $year = $expireddate[0];
                                        $month = $expireddate[1];
                                        $day  = $expireddate[2];
                                    } else {
                                        $year = '';
                                    }
                                ?>              
                            <?php endif;?>
                            <?php if( $year ) : ?>
                                <div class="woo_spec_timer">
                                    <?php echo wpsm_countdown(array('year'=> $year, 'month'=>$month, 'day'=>$day));?>
                                </div>
                            <?php endif;?>
                            <div class="mt20 mb15">
                            <?php echo  apply_filters( 'woocommerce_loop_add_to_cart_link',
                                sprintf( '<a href="%s" data-product_id="%s" data-product_sku="%s" class="re_track_btn rehub_main_btn rehub-main-smooth wpsm-button %s product_type_%s"%s %s>%s</a>',
                                esc_url( $_product->add_to_cart_url() ),
                                esc_attr( $_product->get_id() ),
                                esc_attr( $_product->get_sku() ),
                                $_product->supports( 'ajax_add_to_cart' ) ? 'ajax_add_to_cart' : '',
                                esc_attr( $_product->get_type() ),
                                $_product->get_type() =='external' ? ' target="_blank"' : '',
                                $_product->get_type() =='external' ? ' rel="nofollow"' : '',
                                esc_html( $_product->add_to_cart_text() )
                                ),
                            $_product ); 
                            ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
                <?php if ($carousel) :?></div></div><?php endif; ?>
            <?php else: ?>
                <?php esc_html_e( 'No products for this criteria.', 'rehub-theme' );  ?>
            <?php endif; ?>
            <?php wp_reset_postdata();
        echo '</div>';        

    }
}

Plugin::instance()->widgets_manager->register_widget_type( new Widget_Woo_EL_dealday );