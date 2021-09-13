<?php
/**
 * Single Product Multiple vendors
 *
 * This template can be overridden by copying it to yourtheme/dc-product-vendor/single-product/multiple_vendors_products.php.
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
if (!defined('ABSPATH')) {
    exit;
}
global $WCMp, $post, $wpdb;
if (!empty($more_product_array) && is_array($more_product_array) && count($more_product_array) > 1) {
    $i = 0;
    ?>

    <div class="rh_listoffers rh_listoffers_price_col">					
            <?php
        $WCMp->template->get_template('single-product/multiple_vendors_products_body.php', array('more_product_array' => $more_product_array, 'sorting' => 'price'));
        ?>		
    </div>        	
    <?php
} else {
    ?>
    <div class="container">
        <div class="row">
    <?php echo esc_html__('Sorry no more offers available', 'rehub-theme'); ?>
        </div>
    </div>	
<?php }
?>

