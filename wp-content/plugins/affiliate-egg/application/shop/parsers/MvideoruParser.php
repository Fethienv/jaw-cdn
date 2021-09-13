<?php

namespace Keywordrush\AffiliateEgg;

defined('\ABSPATH') || exit;

/**
 * MvideoruParser class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2014 keywordrush.com
 */
class MvideoruParser extends MicrodataShopParser {

    protected $charset = 'utf-8';

    public function parseCatalog($max)
    {
        $urls = array_slice($this->xpathArray(".//a[contains(@class,'product-tile-title-link')]/@href"), 0, $max);
        foreach ($urls as $i => $url)
        {
            if (!preg_match('/^https?:\/\//', $url))
                $urls[$i] = 'https://www.mvideo.ru' . $url;
        }
        return $urls;
    }

    public function parseDescription()
    {
        $d = $this->xpathScalar(".//*[@class='o-about-product']//*[@class='collapse-text-initial']", true);
        return str_replace('<br>', "\r\n", $d);
    }

    public function parseOldPrice()
    {
        return $this->xpathScalar(".//*[@class='c-pdp-price__offers']//div[contains(@class, 'c-pdp-price__old')]");
    }

    public function parseExtra()
    {
        $extra = parent::parseExtra();

        $names = $this->xpathArray(".//table[@class='c-specification__table']//span[@class='c-specification__name-text']");
        $values = $this->xpathArray(".//table[@class='c-specification__table']//span[@class='c-specification__value']");
        $feature = array();
        for ($i = 0; $i < count($names); $i++)
        {
            if (!empty($values[$i]))
            {
                $feature['name'] = \sanitize_text_field(trim($names[$i], "?:"));
                $feature['value'] = \sanitize_text_field(trim($values[$i], "?:"));
                $extra['features'][] = $feature;
            }
        }

        $extra['images'] = array();
        $results = $this->xpathArray(".//div[contains(@class,'list-carousel')]//li[not(@class)]/a/@data-src");
        foreach ($results as $i => $res)
        {
            if ($i == 0)
                continue;
            if ($res && preg_match('/^\/\//', $res))
                $res = 'https:' . $res;
            if (!in_array($res, $extra['images']))
                $extra['images'][] = $res;
        }

        $extra['comments'] = array();
        $users = $this->xpathArray(".//div[@class='product-review-area']//strong[@class='product-review-author-name']");
        $dates = $this->xpathArray(".//div[@class='product-review-area']//span[@class='product-review-date']");
        $comments = $this->xpathArray(".//div[@class='product-review-area']//div[@class='product-review-description']/p");
        for ($i = 0; $i < count($comments); $i++)
        {
            if (!empty($comments[$i]))
            {

                $comment['name'] = (isset($users[$i])) ? trim($users[$i], ', ') : '';
                $comment['date'] = '';
                if (isset($dates[$i]))
                {
                    $date = explode('.', $dates[$i]);
                    if (count($date) == 3)
                        $comment['date'] = strtotime(trim($date[1]) . '/' . trim($date[0]) . '/' . $date[2]);
                }

                $comment['comment'] = sanitize_text_field($comments[$i]);
                $extra['comments'][] = $comment;
            }
        }
        return $extra;
    }

}
