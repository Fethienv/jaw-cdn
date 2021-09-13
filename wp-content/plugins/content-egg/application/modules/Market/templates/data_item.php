<?php
defined('\ABSPATH') || exit;
/*
  Name: Product card
 */
__('Product card', 'content-egg-tpl');

use ContentEgg\application\helpers\TemplateHelper;
?>
<?php \wp_enqueue_style('egg-bootstrap'); ?>
<?php \wp_enqueue_style('egg-products'); ?>


<div class="egg-container egg-item">
    <?php if ($title): ?>
        <h3><?php echo esc_html($title); ?></h3>
    <?php endif; ?>    

    <div class="products">

        <?php foreach ($items as $item): ?>
            <div class="row">
                <div class="col-md-4 text-center cegg-image-container cegg-mb20">
                    <?php if ($item['img']): ?>
                        <a<?php TemplateHelper::printRel(); ?> target="_blank" href="<?php echo $item['url']; ?>">                    
                            <img src="<?php echo $item['img']; ?>" alt="<?php echo esc_attr($item['title']); ?>" />
                        </a>
                    <?php endif; ?>
                </div>
                <div class="col-md-8">
                    <h2 class="cegg-no-top-margin"><?php echo $item['title']; ?></h2>
                    <?php if ((int) $item['rating'] > 0): ?>
                        <div class="cegg-mb10">
                            <span class="rating" title="<?php _e('Customer reviews:', 'content-egg-tpl'); ?> <?php echo $item['reviewsCount']; ?>">
                                <?php
                                echo str_repeat("<span>&#x2605;</span>", (int) $item['rating']);
                                echo str_repeat("<span>☆</span>", 5 - (int) $item['rating']);
                                ?>
                            </span>
                        </div>
                    <?php endif; ?>
                    <div class="cegg-price-row cegg-mb10">
                        <?php if ($item['price']): ?>
                            <div><span class="text-muted"><?php _e('Average price', 'content-egg-tpl'); ?></span></div>
                            <div class="cegg-mb10"><span class="cegg-price"><?php echo TemplateHelper::formatPriceCurrency($item['price'], $item['currencyCode'], '<span class="cegg-currency">', '</span>'); ?></span></div>
                            <span class="text-muted"><?php echo number_format_i18n($item['extra']['priceMin']) ?> – <?php echo number_format_i18n($item['extra']['priceMax']) ?> <?php echo $item['currency']; ?> </span>
                        <?php endif; ?>
                    </div>                
                    <div class="after-price-row cegg-mb20 cegg-lineh-20">
                        <p><?php echo $item['description']; ?></p>
                        <p class="small"><a target="_blank" rel="nofollow" href="http://market.yandex.ru/<?php if ($item['extra']['offers']) echo $item['unique_id'] . '/offers'; ?>"><?php _e('Data from Yandex.Market', 'content-egg-tpl'); ?></a></p>                
                    </div>
                </div>
            </div> 
            <div class="cegg-line-hr"></div>

            <?php if ($item['extra']['offers']): ?>
                <div class="egg-listcontainer cegg-list-withlogos">
                    <?php foreach ($item['extra']['offers'] as $offer): $offer['img'] = 'https://mdata.yandex.net/i?path=b0605194833_img_id6933159904370791674.jpeg'; ?>
                        <div class="row-products">
                            <div class="col-md-2 col-sm-2 col-xs-12 cegg-image-cell">
                                <?php if ($offer['img']): ?>
                                    <a<?php TemplateHelper::printRel(); ?> target="_blank" href="<?php echo $offer['url']; ?>">
                                        <img src="<?php echo $offer['img']; ?>" alt="<?php echo esc_attr($offer['name']); ?>" />
                                    </a>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12 cegg-desc-cell">
                                <h4><?php echo esc_attr($offer['name']); ?></h4>
                                <span class="text-info"><?php echo esc_attr($offer['shopName']); ?></span>
                                <?php /*
                                  <span class="rating_small" title="Отзывов: <?php echo $offer['shopInfo']['gradeTotal']; ?>">
                                  <?php echo str_repeat("<span>&#x2605;</span>", (int) $offer['shopInfo']['rating']); ?><?php echo str_repeat("<span>☆</span>", 5 - (int) $offer['shopInfo']['rating']); ?>
                                  </span>
                                 * 
                                 */
                                ?>
                            </div>
                            <div class="col-md-3 col-sm-3 col-xs-12 cegg-price-cell text-center">
                                <div class="cegg-price cegg-mb10"><?php echo TemplateHelper::formatPriceCurrency($offer['price'], $offer['currencyCode']); ?></div>
                                <span class="text-muted small">                             

                                    <?php if ((bool) $offer['delivery']['free']): ?>
                                        <?php _e('Shipping:', 'content-egg-tpl'); ?> <?php _e('free', 'content-egg-tpl'); ?>
                                    <?php elseif (isset($offer['delivery']['price'])): ?>
                                        <?php _e('Shipping:', 'content-egg-tpl'); ?> <?php echo number_format_i18n($offer['delivery']['price']['value']); ?> <?php echo $item['currency']; ?>
                                    <?php endif; ?>
                                </span>

                                <?php if ((bool) $offer['delivery']['pickup']): ?>
                                    <br><span class="text-muted small"><?php _e('Pickup', 'content-egg-tpl'); ?></span>
                                <?php endif; ?>

                                <?php if ((bool) $offer['onStock']): ?>
                                    <span class="text-success small"><?php _e('In stock', 'content-egg-tpl'); ?></span>
                                <?php else: ?>
                                    <span class="small"><?php _e('Not available', 'content-egg-tpl'); ?></span>
                                <?php endif; ?>

                            </div>
                            <div class="col-md-3 col-sm-3 col-xs-12 cegg-btn-cell">
                                <a<?php TemplateHelper::printRel(); ?> target="_blank" href="<?php echo $offer['url']; ?>" class="btn btn-success"><?php _e('Visit store', 'content-egg-tpl'); ?></a><br>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($item['extra']['opinions']): ?>
                <h3><?php _e('Customer reviews', 'content-egg-tpl'); ?></h3>        
                <?php foreach ($item['extra']['opinions'] as $opinion): ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="cegg-review-block">
                                <h4>
                                    <span class="rating_default">
                                        <?php
                                        if ((int) $opinion['grade'] > 0):
                                            echo str_repeat("<span>&#x2605;</span>", (int) $opinion['grade']);
                                            echo str_repeat("<span>☆</span>", 5 - (int) $opinion['grade']);
                                        endif;
                                        ?>
                                    </span>

                                    <?php echo TemplateHelper::formatDate($opinion['date']); ?><?php if ($opinion['author']): ?>, <?php echo esc_attr($opinion['author']); ?><?php endif; ?>

                                </h4>
                                <p><span class="text-muted"><b><?php _e('Pros:', 'content-egg-tpl'); ?></b></span> <?php echo esc_attr($opinion['pro']); ?></p>
                                <p><span class="text-muted"><b><?php _e('Cons:', 'content-egg-tpl'); ?></b></span> <?php echo esc_attr($opinion['contra']); ?></p>                
                                <p><span class="text-muted"><b><?php _e('Comment:', 'content-egg-tpl'); ?></b></span> <?php echo esc_attr($opinion['text']); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <br>
                <p class="text-right small"><a target="_blank" rel="nofollow" href="https://market.yandex.ru/product/<?php echo $item['unique_id']; ?>/reviews"><?php _e('All reviews on Yandex.Market', 'content-egg-tpl'); ?></a></p>
                <?php endif; ?>
            <?php endforeach; ?>
    </div>

</div>