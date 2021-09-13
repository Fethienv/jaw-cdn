<?php defined('\ABSPATH') || exit; ?>
<select class="col-md-4 input-sm" ng-model="query_params.<?php echo $module_id; ?>.license">
    <option value=""><?php _e('Any license', 'content-egg'); ?></option>
    <option value="Public"><?php _e('Public', 'content-egg'); ?></option>
    <option value="Share"><?php _e('Share', 'content-egg'); ?></option>
    <option value="ShareCommercially"><?php _e('Share commercially', 'content-egg'); ?></option>
    <option value="Modify"><?php _e('Modify', 'content-egg'); ?></option>
    <option value="ModifyCommercially"><?php _e('Modify commercially', 'content-egg'); ?></option>
</select>