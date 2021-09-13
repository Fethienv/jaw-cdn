<?php

namespace Keywordrush\AffiliateEgg;

defined('\ABSPATH') || exit;

/**
 * EbaycomParser class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2014 keywordrush.com
 */
class EbaycomParser extends ShopParser {

    protected $charset = 'utf-8';
    protected $currency = 'USD';

    public function parseCatalog($max)
    {
        $path = array(
            ".//ul[@id='ListViewInner']//h3/a/@href",
            ".//*[contains(@class, 'grid-gutter')]//h3/a/@href",
            ".//h3/a[@itemprop='url']/@href",
            ".//h3[@class='lvtitle']/a/@href",
            ".//a[@class='s-item__link']/@href",
            ".//h3/a/@href",
            ".//div[@class='dne-itemtile-detail']/a/@href",
            ".//div[contains(@class, 'ebayui-dne-item-featured-card')]//div[@class='dne-itemtile-detail']/a/@href",
        );

        return $this->xpathArray($path);
    }

    public function parseTitle()
    {
        return $this->xpathScalar(".//h1[@itemprop='name']/text()");
    }

    public function parseDescription()
    {
        return $this->xpathScalar(".//div[@class='prodDetailSec']");
    }

    public function parsePrice()
    {
        $paths = array(
            ".//*[@itemprop='price']/@content",
            ".//span[@itemprop='price']",
            ".//span[@id='mm-saleDscPrc']",
        );

        return $this->xpathScalar($paths);
    }

    public function parseOldPrice()
    {
        $paths = array(
            ".//span[@id='mm-saleOrgPrc']",
            ".//*[@id='orgPrc']",
        );

        return $this->xpathScalar($paths);
    }

    public function parseManufacturer()
    {
        return $this->xpathScalar(".//h2[@itemprop='brand']");
    }

    public function parseImg()
    {
        $res = $this->xpathScalar(".//*[@id='icImg']/@src");
        if (!$res)
        {
            $results = $this->xpathScalar(".//script[contains(.,'image.src=')]");
            preg_match("/image\.src=\s+?'(.+?)'/msi", $results, $match);
            if (isset($match[1]))
                $res = $match[1];
        }
        return $res;
    }

    public function parseImgLarge()
    {
        $res = '';
        if ($this->item['orig_img'])
            $res = preg_replace('/\/\$_\d+\./', '/$_57.', $this->item['orig_img']);
        return $res;
    }

    public function parseExtra()
    {
        $extra = array();

        preg_match("/\/(\d{12})/msi", $this->getUrl(), $match);
        $extra['item_id'] = isset($match[1]) ? $match[1] : '';

        $extra['features'] = array();

        $names = $this->xpathArray(".//div[@class='itemAttr']//tr/td[@class='attrLabels']");
        $values = $this->xpathArray(".//div[@class='itemAttr']//tr/td[position() mod 2 = 0]");
        $feature = array();
        for ($i = 0; $i < count($names); $i++)
        {
            if (!empty($values[$i]) && $names[$i] != 'Condition:' && $names[$i] != 'Brand:')
            {
                $feature['name'] = str_replace(":", "", $names[$i]);
                $feature['value'] = $values[$i];
                $extra['features'][] = $feature;
            }
        }

        $extra['images'] = array();
        $results = $this->xpathArray(".//div[@id='vi_main_img_fs_slider']//img/@src");
        foreach ($results as $i => $res)
        {
            if ($i == 0)
                continue;
            if ($res)
            {
                $new_res = preg_replace('/\/\$_\d+\./', '/$_57.', $res);
                if ($new_res !== $res)
                {
                    $extra['images'][] = $new_res;
                }
            }
        }

        $extra['comments'] = array();
        $comments = $this->xpathArray(".//*[@class='reviews']//*[@itemprop='reviewBody']");
        $users = $this->xpathArray(".//*[@class='reviews']//*[@itemprop='author']");
        $dates = $this->xpathArray(".//*[@class='reviews']//*[@itemprop='datePublished']");
        $ratings = $this->xpathArray(".//*[@class='reviews']//*[@class='ebay-star-rating']/@aria-label");
        for ($i = 0; $i < count($comments); $i++)
        {
            $comment['comment'] = sanitize_text_field($comments[$i]);
            if (!empty($users[$i]))
                $comment['name'] = sanitize_text_field($users[$i]);
            if (!empty($ratings[$i]))
                $comment['rating'] = TextHelper::ratingPrepare((float) $ratings[$i]);
            if (!empty($dates[$i]))
            {
                $d = strtotime($dates[$i]);
                if (!$d || $d > time())
                    $d = time() - rand(120, 24 * 3600 * 7);
                else
                    $d += rand(120, 7200);

                $comment['date'] = $d;
            }
            $extra['comments'][] = $comment;
        }
        $extra['rating'] = TextHelper::ratingPrepare($this->xpathScalar(".//*[@itemprop='aggregateRating']//*[@itemprop='ratingValue']/@content"));
        return $extra;
    }

    public function isInStock()
    {
        $res = $this->xpath->evaluate("boolean(.//span[@class='msgTextAlign'][contains(.,'This listing has ended')])");
        if (!$res)
            $res = $this->xpath->evaluate("boolean(.//span[@class='msgTextAlign'][contains(.,'This Buy It Now listing has ended')])");
        return ($res) ? false : true;
    }

    public function getCurrency()
    {
        $currency = $this->xpathScalar(".//span[@itemprop='priceCurrency']/@content");
        if (!$currency)
            $currency = $this->currency;
        return $currency;
    }

}
