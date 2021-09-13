<?php
/**
 * Vendor Review Comments Template
 *
 * Closing li is left out on purpose!.
 *
 * This template can be overridden by copying it to yourtheme/dc-product-vendor/review/rating.php.
 *
 * HOWEVER, on occasion WC Marketplace will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * 
 * @author  WC Marketplace
 * @package dc-woocommerce-multi-vendor/Templates
 * @version 3.3.5
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $WCMp;
$rating = round($rating_val_array['avg_rating'], 1);
$count = intval($rating_val_array['total_rating']);
$review_text = $count > 1 ? esc_html__('Reviews', 'rehub-theme') : esc_html__('Review', 'rehub-theme');
?> 
<div style="clear:both; width:100%;"></div> 

<a href="#reviews">
<?php if ($count > 0) { ?>	
    <span itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="wcmp_star_rating rh_woo_star" title="<?php echo sprintf(esc_html__('Rated %s out of', 'rehub-theme'), $rating) ?> 5">
    <?php    
    if($rating > 0){
        for ($i = 1; $i <= 5; $i++){
            if ($i <= $rating){
                $active = ' active';
            }else{
                $active ='';
            }
            echo '<span class="rhwoostar rhwoostar'.$i.$active.'">&#9733;</span>';
        }
    } 
    ?> 
    <strong itemprop="ratingValue" class="rhhidden"><?php echo ''.$rating; ?></strong> 
    </span>
    <span class=""><?php echo esc_html__(sprintf(' %s %s', $count, $review_text)); ?></span>  
    <?php
} else {
    ?>
        <?php echo esc_html__(' No Review Yet ', 'rehub-theme'); ?>
    <?php } ?>
</a>
