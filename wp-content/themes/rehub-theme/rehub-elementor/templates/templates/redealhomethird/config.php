<?php

if ( ! defined('ABSPATH') ) {
    exit('restricted access');
}

return [
    'id'               => 'redealhomethird',
    'title'            => 'Redeal Home As List',
    'thumbnail'        => $local_dir_url . 'thumbnail.jpg',
    'tmpl_created'     => time(),
    'author'           => 'WPSM',
    'url'              => 'https://redeal.lookmetrics.co/homepage-as-list/',
    'type'             => 'page',
    'subtype'          => 'wpsm',
    'tags'             => ['deal'],
    'menu_order'       => 0,
    'popularity_index' => 10,
    'trend_index'      => 1,
    'is_pro'           => 0,
    'has_page_settings'=> 1
];
