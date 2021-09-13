<?phpdefined('\ABSPATH') || exit;/*  Name: Product сard for Auto Bloging */__('Product сard for Auto Bloging', 'affegg-tpl');use Keywordrush\AffiliateEgg\TemplateHelper;?><?php $this->enqueueStyle(); ?><div class="egg-container egg-item">    <?php foreach ($items as $key => $item): ?>        <div class="products">            <div class="row">                <div class="col-md-6 text-center cegg-image-container cegg-mb20">                    <?php if ($item['img']): ?>                        <a rel="nofollow" target="_blank" href="<?php echo $item['url']; ?>"<?php echo $item['ga_event'] ?>>                            <img src="<?php echo esc_attr($item['img']) ?>" alt="<?php echo esc_attr($item['title']); ?>" />                        </a>                    <?php endif; ?>                </div>                <div class="col-md-6">                    <?php if ($key > 0): ?>                        <h3 class="cegg-item-title"><?php echo esc_html($item['title']); ?><?php if ($item['manufacturer']): ?>, <?php echo esc_html($item['manufacturer']); ?><?php endif; ?></h3>                    <?php endif; ?>                    <?php if (!empty($item['extra']['rating'])): ?>                        <div class="cegg-mb5">                            <?php echo TemplateHelper::printRating($item, 'default'); ?>                        </div>                    <?php endif; ?>                    <div class="cegg-price-row">                        <?php if ($item['price']): ?>                            <span class="cegg-price cegg-price-color">                                <?php if ($item['old_price']): ?>                                    <span class="text-muted"><strike><?php echo TemplateHelper::formatPriceCurrency($item['old_price_raw'], $item['currency_code'], '<small>', '</small>'); ?></strike></span><br>                                <?php endif; ?>                                                                <?php echo TemplateHelper::formatPriceCurrency($item['price_raw'], $item['currency_code'], '<span class="cegg-currency">', '</span>'); ?>                            </span>                        <?php endif; ?>                    </div>                    <div class="cegg-btn-row cegg-mb20">                        <a rel="nofollow" target="_blank" href="<?php echo $item['url']; ?>" class="btn btn-danger cegg-btn-big cegg-mb5"><?php _e('Buy now', 'affegg-tpl'); ?></a>                        <br/>                        <img src="<?php echo esc_attr(TemplateHelper::getMerhantIconUrl($item, true)); ?>" /> <small><?php echo esc_html($item['domain']); ?></small>                    </div>                </div>            </div>            <div class="row">                <div class="col-md-12">                    <div class="cegg-mb25">                        <?php if (!empty($item['extra']['features'])): ?>                            <div class="cegg-features-box">                                <h4 class="cegg-no-top-margin"><?php _e('Features', 'affegg-tpl'); ?></h4>                                <ul class="cegg-feature-list">                                    <?php foreach ($item['extra']['features'] as $feature): ?>                                        <li><strong><?php echo esc_html($feature['name']) ?></strong>: <?php echo esc_html($feature['value']) ?></li>                                    <?php endforeach; ?>                                </ul>                            </div>                                                <?php endif; ?>                                                <?php if ($item['description']): ?>                            <h4 class="cegg-no-top-margin"><?php _e('Description', 'affegg-tpl'); ?></h4>                            <p><?php echo $item['description']; ?></p>                        <?php endif; ?>                          <?php if (!empty($item['extra']['comments'])): ?>                            <h4><?php _e('User reviews', 'affegg-tpl'); ?></h4>                            <?php foreach ($item['extra']['comments'] as $key => $comment): ?>                                <div class="cegg-review-block">                                    <blockquote>                                        <?php if (!empty($comment['rating'])): ?>                                            <span class="rating_small">                                                <?php echo str_repeat("<span>★</span>", (int) $comment['rating']); ?><?php echo str_repeat("<span>☆</span>", 5 - (int) $comment['rating']); ?>                                            </span>                                        <?php endif; ?>                                        <?php echo esc_html($comment['comment']); ?>                                    </blockquote>                                </div>                            <?php endforeach; ?>                            <p class="text-right">                                <br>                                <a class="btn btn-info" rel="nofollow" target="_blank" href="<?php echo esc_url($item['url']) ?>"><?php _e('View all reviews', 'affegg-tpl'); ?></a>                            </p>                        <?php endif; ?>                                                </div>                </div>            </div>            </div>    <?php endforeach; ?>    <?php if ($see_more_uri): ?>        <div class="row">            <div class="col-md-12 text-center">                 <a class="btn btn-info" rel="nofollow" target="_blank" href="<?php echo $see_more_uri; ?>"><?php _e('See more...', 'affegg-tpl'); ?></a>            </div>        </div>    <?php endif; ?></div>