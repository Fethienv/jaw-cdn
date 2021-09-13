<?php defined('\ABSPATH') || exit; ?>
<select class="col-md-4 input-sm" ng-model="query_params.<?php echo $module_id; ?>.license">
    <option value=""><?php _e('Any license', 'content-egg'); ?></option>
    <option value="4,6,3,2,1,5"><?php _e('Any Creative Commons', 'content-egg'); ?></option>
    <option value="4,6,5"><?php _e('With Allow of commercial use', 'content-egg'); ?></option>
    <option value="4,2,1,5"><?php _e('Allowed change', 'content-egg'); ?></option>
    <option value="4,5"><?php _e('Commercial use and change', 'content-egg'); ?></option>
</select>
<select class="col-md-4 input-sm" ng-model="query_params.<?php echo $module_id; ?>.sort">
    <option value="relevance"><?php _e('Relevance', 'content-egg'); ?></option>
    <option value="date-posted-desc"><?php _e('Date of post', 'content-egg'); ?></option>
    <option value="date-taken-desc"><?php _e('Date of shooting', 'content-egg'); ?></option>
    <option value="interestingness-desc"><?php _e('First interesting', 'content-egg'); ?></option>
</select>