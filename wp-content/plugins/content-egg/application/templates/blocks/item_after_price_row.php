<?php
defined('\ABSPATH') || exit;

use ContentEgg\application\helpers\TemplateHelper;
?>

<?php if ($item['module_id'] == 'Amazon' && (!empty($item['extra']['totalNew']) && $item['extra']['totalNew'] > 1) || !empty($item['extra']['totalUsed'])): ?>

    <div class="after-price-row cegg-lineh-20 cegg-mb15">
        <small class="text-muted">
            <?php if (!empty($item['extra']['totalNew']) && $item['extra']['totalNew'] > 1): ?>
                <?php echo sprintf(TemplateHelper::__('%d new from %s'), $item['extra']['totalNew'], TemplateHelper::formatPriceCurrency($item['extra']['lowestNewPrice'], $item['currencyCode'])); ?><br />
            <?php endif; ?>
            <?php if (!empty($item['extra']['totalUsed'])): ?>
                <?php echo sprintf(TemplateHelper::__('%d used from %s'), $item['extra']['totalUsed'], TemplateHelper::formatPriceCurrency($item['extra']['lowestUsedPrice'], $item['currencyCode'])); ?><br />
            <?php endif; ?>
            <?php if (!empty($item['extra']['IsEligibleForSuperSaverShipping'])): ?>
                <div class="text-success"><?php TemplateHelper::_e('Free shipping'); ?></div>
            <?php endif; ?>                            
        </small>
    </div>
<?php endif; ?>                            


<?php if ($item['module_id'] == 'Ebay'): ?>
    <?php $time_left = TemplateHelper::getTimeLeft($item['extra']['listingInfo']['endTimeGmt']); ?>

    <div class="after-price-row cegg-lineh-20 cegg-mb15">
        <small class="text-muted">

            <?php if ($item['extra']['sellingStatus']['bidCount'] !== ''): ?>
                <div><?php _e('Bids:', 'content-egg-tpl'); ?> <?php echo $item['extra']['sellingStatus']['bidCount'] ?></div>
            <?php endif; ?>

            <?php if ($item['extra']['conditionDisplayName']): ?>
                <div>
                    <?php _e('Item condition:', 'content-egg-tpl'); ?>
                    <mark><?php echo $item['extra']['conditionDisplayName']; ?></mark>
                </div>
            <?php endif; ?>

            <?php if ($time_left): ?>
                <div>
                    <?php _e('Time left:', 'content-egg-tpl'); ?>
                    <span <?php if (strstr($time_left, __('m', 'content-egg-tpl'))) echo 'class="text-danger"'; ?>><?php echo $time_left; ?></span>
                </div>
            <?php else: ?>
                <div style="color: red;">
                    <?php _e('Ended:', 'content-egg-tpl'); ?>
                    <?php echo date('M j, H:i', strtotime($item['extra']['listingInfo']['endTime'])); ?> <?php echo $item['extra']['listingInfo']['timeZone']; ?>
                </div>
            <?php endif; ?>

            <?php if ($item['extra']['shippingInfo']['shippingType'] == 'Free'): ?>
                <div class="text-success"><?php TemplateHelper::_e('Free shipping'); ?></div>
            <?php endif; ?>

            <?php if ($item['extra']['eekStatus']): ?>
                <div class="muted"><?php _e('EEK:', 'content-egg-tpl'); ?> <?php _p($item['extra']['eekStatus']); ?></div>
            <?php endif; ?>      
        </small>
    </div>        
<?php endif; ?>    