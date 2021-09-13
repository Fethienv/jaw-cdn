<?php

defined('\ABSPATH') || exit;

/*
  Name: List
 */

__('List', 'content-egg-tpl');

foreach ($items as $key => $item)
{
    if ($item['img'] && strstr($item['img'], 'images-amazon.com'))
    {
        $items[$key]['img'] = str_replace('/spare_covers/', '/c200/', $item['img']);
    }
}

$this->renderPartial('grid', array('items' => $items));
