<?php defined('\ABSPATH') || exit; ?>
<div id="cegg_waiting_products" style="display:none; text-align: center;"> 
    <h2><?php _e('Scanning... Please wait...', 'content-egg'); ?></h2> 
    <p>
        <img src="<?php echo \ContentEgg\PLUGIN_RES; ?>/img/egg_waiting.gif" />
    </p>
</div>
<script type="text/javascript">
    var $j = jQuery.noConflict();
    $j(document).ready(function () {
        $j('#btn_scan_products').click(function () {
            $j.blockUI({message: $j('#cegg_waiting_products')});
        });
    });
</script>

<?php
$message = '';
?>

<?php if (\ContentEgg\application\Plugin::isFree() || \ContentEgg\application\Plugin::isInactiveEnvato()): ?>
    <div class="cegg-maincol">
    <?php endif; ?>

    <div class="wrap">
        <h1 class="wp-heading-inline">
            <?php _e('Products', 'content-egg'); ?>
        </h1>
        <a id="btn_scan_products" href="<?php echo \get_admin_url(\get_current_blog_id(), 'admin.php?page=content-egg-product&action=scan'); ?>" class="page-title-action"><?php echo __('Scan Products', 'content-egg'); ?></a>        
        &nbsp;<small><?php echo sprintf(__('Last scanned: %s', 'content-egg'), $last_scaned_str); ?></small>

        <?php echo $message; ?>


        <form id="cegg-products-table" method="GET">
            <input type="hidden" name="page" value="<?php echo \esc_attr($_REQUEST['page']); ?>"/>
            <?php if (isset($_REQUEST['stock_status'])): ?>
                <input type="hidden" name="stock_status" value="<?php echo \esc_attr($_REQUEST['stock_status']); ?>"/>
            <?php endif; ?>
            <?php $table->views(); ?>
            <?php $table->search_box(__('Search products', 'content-egg'), 'key'); ?>
            <?php $table->display(); ?>
        </form>
    </div>

    <?php if (\ContentEgg\application\Plugin::isFree() || \ContentEgg\application\Plugin::isInactiveEnvato()): ?>
    </div>    
    <?php include('_promo_box.php'); ?>
<?php endif; ?>        