<?php defined('\ABSPATH') || exit; ?>
<input type="text" class="input-sm col-md-4" ng-model="query_params.<?php echo $module_id; ?>.MinimumPrice" ng-init="query_params.<?php echo $module_id; ?>.MinimumPrice = ''" placeholder="<?php _e('Min. price', 'content-egg'); ?>" />
<input type="text" class="input-sm col-md-4" ng-model="query_params.<?php echo $module_id; ?>.MaximumPrice" ng-init="query_params.<?php echo $module_id; ?>.MaximumPrice = ''" placeholder="<?php _e('Max. price', 'content-egg'); ?>" />

