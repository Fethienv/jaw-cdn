<?php

defined('\ABSPATH') || exit;

/*
  Name: List
 */
__('List', 'content-egg-tpl');

foreach ($items as $key => $item)
{
    if ($item['img'])
    {
        $items[$key]['img'] = str_replace('/spare_covers/', '/c200/', $item['img']);
    }
}

$this->renderPartial('list', array('items' => $items));
