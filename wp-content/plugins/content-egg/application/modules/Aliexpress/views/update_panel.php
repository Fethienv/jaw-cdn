<?php defined('\ABSPATH') || exit; ?>
<input type="text" class="input-sm col-md-4" ng-model="updateParams.<?php echo $module_id; ?>.original_price_from" placeholder="<?php _e('Min. price', 'content-egg') ?>" title="<?php _e('Min. price for automatic update', 'content-egg') ?>" />
<input type="text" class="input-sm col-md-4" ng-model="updateParams.<?php echo $module_id; ?>.original_price_to" placeholder="<?php _e('Max. price', 'content-egg') ?>" title="<?php _e('Max. price for automatic update', 'content-egg') ?>" />
