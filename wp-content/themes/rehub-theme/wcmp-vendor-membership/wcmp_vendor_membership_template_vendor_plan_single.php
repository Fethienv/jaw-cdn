<?php
/**
 * The template for displaying wcmp-vendor-categorization plugin content.
 *
 * Override this template by copying it to yourtheme/wcmp-vendor-membership/wcmp-vendor-membership_template_vendor_plan_single.php
 *
 * @author 		dualcube
 * @package 	WCMp-Vendor-Catagorization/Templates
 * @version     0.0.1
 */
if (!defined('ABSPATH')) {
    // Exit if accessed directly
    exit;
}
get_header();
global $WCMP_Vendor_Membership, $WCMp;
$current_user = wp_get_current_user();
if (function_exists('is_user_wcmp_vendor')) {
    $is_vendor = is_user_wcmp_vendor($current_user);
} elseif (function_exists('is_user_wcmp_pending_vendor')) {
    $is_pending_vendor = is_user_wcmp_pending_vendor($current_user);
}
$global_settings = $WCMP_Vendor_Membership->get_global_settings();
$current_stylesheet = get_option('stylesheet');
$stylesheet_support = array('rehub-vendor');
$body_class = in_array($current_stylesheet, $stylesheet_support) ? 'post' : '';
?>
<div class="rh-container"> 
    <div class="rh-content-wrap clearfix">
		 <!-- Main Side -->
	    <div class="main-side page clearfix full_width" id="content">
			<article <?php post_class($body_class); ?> id="post-<?php the_ID(); ?>">
			<?php
			// Start the loop.
			while (have_posts()) : the_post();
				// Include the page content template.
				?>
                <div class="rh-cartbox" id="membershipwrapper">
                <div class="wcmp-plan-images">
                    <a href="<?php echo get_the_permalink(); ?>" itemprop="image"><img width="300" height="300" src="<?php echo wp_get_attachment_url(get_post_thumbnail_id($post->ID)); ?>" class="attachment-shop_single size-shop_single wp-post-image" alt="image" title="<?php echo the_title(); ?>"></a>
                </div> 
                <div class="summary entry-summary">
                    <h1 itemprop="name" class="mt0"><?php echo get_the_title(); ?></h1>
                    <div class="rh-line mb15"></div>
                    <?php if (get_post_meta($post->ID, '_is_free_plan', true) != 'Enable') : ?>
                        <p class="wcmp-plan-price rehub_main_font rehub-main-color rehub-main-font fontnormal">
                            <?php
                            $_vendor_billing_field = get_post_meta($post->ID, '_vendor_billing_field', true);
                            if (isset($_vendor_billing_field['_initial_payment']) && !empty($_vendor_billing_field['_initial_payment']) && $_vendor_billing_field['_initial_payment'] > 0) {
                                echo esc_html__(' Initial Payment ', 'rehub-theme');
                                echo get_woocommerce_currency_symbol();
                                echo number_format($_vendor_billing_field['_initial_payment'], 2);
                            }
                            if (isset($_vendor_billing_field['_is_recurring']) && $_vendor_billing_field['_is_recurring'] == 'yes') {
                                if (isset($_vendor_billing_field['_initial_payment']) && $_vendor_billing_field['_initial_payment'] > 0) {
                                    if ($_vendor_billing_field['_vendor_billing_amt_cycle'] == 'Week') {
                                        echo esc_html__(' for First Week', 'rehub-theme');
                                    }
                                    if ($_vendor_billing_field['_vendor_billing_amt_cycle'] == 'SemiMonth') {
                                        echo esc_html__(' for First 15 Days', 'rehub-theme');
                                    }
                                    if ($_vendor_billing_field['_vendor_billing_amt_cycle'] == 'Month') {
                                        echo esc_html__(' for First Month', 'rehub-theme');
                                    } elseif ($_vendor_billing_field['_vendor_billing_amt_cycle'] == 'Day') {
                                        echo esc_html__(' for First Day', 'rehub-theme');
                                    } elseif ($_vendor_billing_field['_vendor_billing_amt_cycle'] == 'Year') {
                                        echo esc_html__(' for First Year', 'rehub-theme');
                                    }

                                    echo esc_html__(' and Next ', 'rehub-theme');
                                }
                                $billing_amt = isset($_vendor_billing_field['_vendor_billing_amt']) && !empty($_vendor_billing_field['_vendor_billing_amt']) ? $_vendor_billing_field['_vendor_billing_amt'] : 0;
                                if (isset($global_settings['display_method']) && !empty($global_settings['display_method']) && $global_settings['display_method'] == 'inclusive') {
                                    if (isset($_vendor_billing_field['_vendor_billing_tax_amt']) && !empty($_vendor_billing_field['_vendor_billing_tax_amt'])) {
                                        $billing_amt += $_vendor_billing_field['_vendor_billing_tax_amt'];
                                    }
                                }
                                echo get_woocommerce_currency_symbol() . number_format($billing_amt, 2) . ' per ' . $_vendor_billing_field['_vendor_billing_amt_cycle'];
                            } else {
                                if (isset($_vendor_billing_field['_initial_payment']) && $_vendor_billing_field['_initial_payment'] > 0) {
                                    echo esc_html__(' One Time', 'rehub-theme');
                                }
                            }
                            ?>
                        </p>
                    <?php endif; ?>
                    <div itemprop="description" class="mb20">
                        <?php echo get_the_content(); ?>
                    </div>
                        <?php $_vender_featurelist = get_post_meta($post->ID, '_vender_featurelist', true); ?>
                        <?php
                        if (is_array($_vender_featurelist)) {
                            echo '<h4 class="">'.__('Features List', 'rehub-theme').'</h4><ul>';
                            foreach ($_vender_featurelist as $flist) {
                                ?>
                                <li><i class="rhicon rhi-check greencolor mr5"></i> <?php echo esc_attr($flist); ?></li>
                                <?php
                            }
                            echo '</ul>';
                        }
                        ?>
                    <?php $payment_page_url = get_wcmp_vendor_settings('vendor_registration', 'vendor', 'general'); ?>
                    <form name="frm_subscribe_vandor_plan" method="post" action="<?php echo get_permalink($payment_page_url); ?>" >
                        <input type="hidden" name="vendor_plan_id" value="<?php echo (int)$post->ID; ?>" />
                        <div class="subscription-container">

                            <?php
                            $button_text = esc_html__('Subscribe Now', 'rehub-theme');
                            if (isset($_vendor_billing_field['_subscribe_button_text']) && $_vendor_billing_field['_subscribe_button_text'] != '') {
                                $button_text = $_vendor_billing_field['_subscribe_button_text'];
                            }
                            if ($current_user->ID != 0) {
                                $button_text = esc_html__('Upgrade Now', 'rehub-theme');
                                if (isset($_vendor_billing_field['_subscribe_button_text_logged_in']) && $_vendor_billing_field['_subscribe_button_text_logged_in'] != '') {
                                    $button_text = $_vendor_billing_field['_subscribe_button_text_logged_in'];
                                }
                            }
                            if (isset($is_vendor) && $is_vendor != 0 && $is_vendor != '' && $is_vendor != false) {

                                $button_text = esc_html__('Upgrade Now', 'rehub-theme');
                                if (isset($_vendor_billing_field['_subscribe_button_text_upgrade']) && $_vendor_billing_field['_subscribe_button_text_upgrade'] != '') {
                                    $button_text = $_vendor_billing_field['_subscribe_button_text_upgrade'];
                                }
                            }

                            if (current_user_can('manage_options')) {
                                ?>
                                <p style="color:red;">
                                    <?php echo esc_html__('Sorry you are logged in as admin please try with another account or logoff', 'rehub-theme'); ?>
                                </p>

                                <?php
                            } else if($post->ID != get_user_meta(get_current_user_id(), 'vendor_group_id', true)) {
                                ?>
                                
                                <input type="submit" value="<?php echo ''.$button_text; ?>" name="vendor_plan_payment" class="button vendor_subscribe_now rehub_main_btn wpsm-button mb15" />
                            <?php } ?>
                        </div>
                    </form>
                </div>
                </div>
            <?php
			// End the loop.
			endwhile;
			?>
			</article>
		</div>
        <!-- /Main Side -->  
    </div>
</div>

<?php
get_footer();
