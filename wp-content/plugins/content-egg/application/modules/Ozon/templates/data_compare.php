<?php
defined('\ABSPATH') || exit;
/*
  Name: Compare
 */

__('Compare', 'content-egg-tpl');

use ContentEgg\application\helpers\TemplateHelper;
?>

<?php
\wp_enqueue_style('egg-bootstrap');
\wp_enqueue_style('egg-products');
?>

<div class="egg-container egg-compare">
    <?php if ($title): ?>
        <h3><?php echo esc_html($title); ?></h3>
    <?php endif; ?>

    <?php $length = 2; ?>
    <?php for ($offset = 0; $offset < count($items); $offset += $length): ?>

        <?php $current_items = array_slice($items, $offset, $length); ?>
        <?php $first = reset($current_items); ?>
        <div class="row">
            <div class="col-sm-12 col-md-2 text-info">
                <?php _e('Compare', 'content-egg-tpl'); ?>
            </div>
            <?php foreach ($current_items as $item): ?>
                <div class="col-sm-6 col-md-5">
                    <?php if ($item['img']): ?>
                        <a<?php TemplateHelper::printRel(); ?> target="_blank" href="<?php echo $item['url']; ?>">                           
                            <img style="max-height: 310px;" class="img-responsive" src="<?php echo esc_attr($item['img']) ?>" alt="<?php echo esc_attr($item['title']); ?>" />
                        </a>                            
                        <br>
                    <?php endif; ?>
                    <h3><?php echo esc_html(TemplateHelper::truncate($item['title'], 120)); ?></h3>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="row">
            <div class="col-sm-12 col-md-2 text-info">
                <?php _e('User Rating', 'content-egg-tpl'); ?>
            </div>
            <?php foreach ($current_items as $item): ?>
                <div class="col-sm-6 col-md-5 products">
                    <?php if ((int) $item['rating'] > 0): ?>
                        <span class="rating"><?php
                            echo str_repeat("<span>&#x2605;</span>", (int) $item['rating']);
                            echo str_repeat("<span>☆</span>", 5 - (int) $item['rating']);
                            ?></span><br>
                    <?php endif; ?>
                    <?php if (!empty($item['extra']['ClientRatingCount'])): ?>
                        <?php _e('Reviews:', 'content-egg-tpl'); ?> <?php echo $item['extra']['ClientRatingCount']; ?> 
                    <?php endif; ?>
                    <br>
                    <a<?php TemplateHelper::printRel(); ?> target="_blank" href="<?php echo $item['url']; ?>"><?php _e('See all reviews', 'content-egg-tpl'); ?></a>

                </div>
            <?php endforeach; ?>
        </div>         

        <div class="row">
            <div class="col-sm-12 col-md-2 text-info">
                <?php _e('Price', 'content-egg-tpl'); ?>
            </div>
            <?php foreach ($current_items as $item): ?>
                <div class="col-sm-6 col-md-5 text-center products">
                    <?php if ($item['price']): ?>
                        <span class="cegg-price"><?php echo TemplateHelper::price_format_i18n($item['price']); ?> <small><?php echo $item['currency']; ?></small></span>
                        <?php if ($item['priceOld']): ?><br><strike class="text-muted"><?php echo TemplateHelper::price_format_i18n($item['priceOld']); ?> <?php echo $item['currency']; ?></strike><?php endif; ?>
                    <?php endif; ?>

                    <span class="text-muted">
                        <br /><?php echo sprintf(TemplateHelper::__('as of %s'), TemplateHelper::getLastUpdateFormatted('Ozon', $post_id)); ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>     

        <div class="row">
            <div class="col-sm-12 col-md-2 text-info">
                <?php _e('Shop Now', 'content-egg-tpl'); ?>
            </div>
            <?php foreach ($current_items as $item): ?>
                <div class="col-sm-6 col-md-5 text-center">
                    <a<?php TemplateHelper::printRel(); ?> target="_blank" href="<?php echo $item['url']; ?>" class="btn btn-success"><?php TemplateHelper::buyNowBtnText(); ?></a>
                    <br>
                    <small>OZON.ru</small>                    
                </div>
            <?php endforeach; ?>
        </div>

        <div class="row">
            <div class="col-sm-12 col-md-2 text-info">
                <?php _e('Description', 'content-egg-tpl'); ?>
            </div>
            <?php foreach ($current_items as $item): ?>
                <div class="col-sm-6 col-md-5">
                    <?php echo $item['description']; ?>
                </div>
            <?php endforeach; ?>
        </div>         

        <?php
        $lines = array();
        $i = 0;
        foreach ($current_items as $item)
        {
            foreach ($item['extra']['Detail'] as $attribute => $value)
            {
                if (!isset($lines[$attribute]))
                    $lines[$attribute] = array();
                $lines[$attribute][$i] = $value;
            }
            $i++;
        }
        ?>
        <?php foreach ($lines as $attribute => $line): ?>
            <div class="row">
                <div class="col-sm-12 col-md-2 text-info">
                    <?php _e($attribute, 'content-egg-tpl'); ?>
                </div>
                <?php for ($i = 0; $i < count($current_items); $i++): ?>
                    <div class="col-sm-6 col-md-5">
                        <?php if (isset($line[$i])): ?>
                            <?php echo esc_html($line[$i]); ?>
                        <?php endif; ?>
                    </div>
                <?php endfor; ?>
            </div>         
        <?php endforeach; ?>    

        <?php if ($first['extra']['Reviews']): ?>
            <div class="row">
                <div class="col-sm-12 col-md-2 text-info">
                    <?php _e('User Reviews', 'content-egg-tpl'); ?>
                </div>
                <?php foreach ($current_items as $item): ?>
                    <div class="col-sm-6 col-md-5 products">

                        <?php foreach ($item['extra']['Reviews'] as $review): ?>
                            <div>
                                <h5><?php echo esc_html($review['Title']); ?></h5>
                                <em>
                                    <small>
                                        <?php echo esc_html($review['FIO']); ?>,
                                        <?php echo TemplateHelper::formatDate($review['Date']); ?>
                                    </small>
                                </em>
                                <span class="rating_small">
                                    <?php echo str_repeat("<span>&#x2605;</span>", (int) $review['Rate']); ?><?php echo str_repeat("<span>☆</span>", 5 - (int) $review['Rate']); ?>
                                </span>
                            </div>
                            <blockquote><?php echo esc_html($review['Comment']); ?></blockquote>
                        <?php endforeach; ?>                                
                    </div>
                <?php endforeach; ?>
            </div>         
        <?php endif; ?>

        <div class="row">
            <div class="col-sm-12 col-md-2 text-info">
                <?php _e('Shop Now', 'content-egg-tpl'); ?>
            </div>
            <?php foreach ($current_items as $item): ?>
                <div class="col-sm-6 col-md-5 text-center">
                    <a<?php TemplateHelper::printRel(); ?> target="_blank" href="<?php echo $item['url']; ?>" class="btn btn-success"><?php TemplateHelper::buyNowBtnText(true, $item, $btn_text); ?></a>
                    <br><br>
                </div>
            <?php endforeach; ?>
        </div>  

        <?php if (!empty($first['extra']['Gallery'])): ?>
            <div class="row">
                <div class="col-sm-12 col-md-2 text-info">
                    <?php _e('Gallery', 'content-egg-tpl'); ?>
                </div>
                <?php foreach ($current_items as $item): ?>
                    <div class="col-sm-6 col-md-5">
                        <?php if (!empty($item['extra']['Gallery'])): ?>
                            <img class="img-responsive" src="<?php echo esc_attr($item['extra']['Gallery'][0]) ?>" alt="<?php echo esc_attr($item['title']); ?>" />                    
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>   
        <?php endif; ?>
    <?php endfor; ?>
</div>