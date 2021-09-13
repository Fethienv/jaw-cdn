<?php
defined('\ABSPATH') || exit;
$locales = \ContentEgg\application\modules\AmazonNoApi\AmazonNoApiConfig::getActiveLocalesList();
$default_locale = \ContentEgg\application\modules\AmazonNoApi\AmazonNoApiConfig::getInstance()->option('locale');

?>

<?php if (count($locales) > 1): ?>
    <select class="input-sm col-md-4" ng-model="query_params.AmazonNoApi.locale" ng-init="query_params.AmazonNoApi.locale = '<?php echo $default_locale; ?>'">
        <?php foreach ($locales as $value => $name): ?>
            <option value="<?php echo $value; ?>"><?php echo $name; ?></option>
        <?php endforeach; ?>
    </select>
<?php endif; ?>