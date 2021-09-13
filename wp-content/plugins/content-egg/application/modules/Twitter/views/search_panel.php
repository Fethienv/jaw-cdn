<?php defined('\ABSPATH') || exit; ?>
<select class="col-md-4 input-sm" ng-model="query_params.<?php echo $module_id; ?>.result_type">
    <option value="recent"><?php _e('New', 'content-egg'); ?></option>
    <option value="popular"><?php _e('Popular', 'content-egg'); ?></option>
    <option value="mixed"><?php _e('Mix', 'content-egg'); ?></option>
</select>
