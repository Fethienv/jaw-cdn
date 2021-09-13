<?php defined('\ABSPATH') || exit; ?>
<select class="col-md-4 input-sm" ng-model="query_params.<?php echo $module_id; ?>.itemsType">
    <option value="voucher"><?php _e('Vouchers', 'content-egg'); ?></option>
    <option value="offer"><?php _e('Offers', 'content-egg'); ?></option>
    <option value="text"><?php _e('Text Links', 'content-egg'); ?></option>
</select>