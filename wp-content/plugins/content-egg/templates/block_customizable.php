<?php

/*
 * Name: Customizable (use with "show" parameter)
 * Modules:
 * Module Types: PRODUCT
 * 
 */

__('Customizable (use with "show" parameter)', 'content-egg-tpl');

use ContentEgg\application\helpers\TemplateHelper;
?>

<?php foreach ($data as $module_id => $items): ?>
    <?php foreach ($items as $item): ?>
        <?php

        switch ($params['show'])
        {
            case 'title':
                echo \esc_html($item['title']);
                break;
            case 'img':
                echo '<img src="' . \esc_attr($item['img']) . '" alt="' . \esc_attr($item['title']) . '" />';
                break;
            case 'price':
                if ($item['price'])
                    echo TemplateHelper::formatPriceCurrency($item['price'], $item['currencyCode']);
                break;
            case 'priceold':
                echo TemplateHelper::formatPriceCurrency($item['priceOld'], $item['currencyCode']);
                break;
            case 'currencycode':
                echo \esc_html($item['currencyCode']);
                break;
            case 'button':
                echo '<span class="egg-container"><a' . TemplateHelper::printRel(false) . ' target="_blank" href="' . $item['url'] . '" class="btn btn-danger">' . TemplateHelper::buyNowBtnText(false, $item) . '</a></span>';
                break;
            case 'stock_status':
                echo TemplateHelper::getStockStatusStr($item);
                break;
            case 'url':
                echo $item['url'];
                break;
            default:
                break;
        }
        ?>

    <?php endforeach; ?>  
<?php endforeach; ?>  

