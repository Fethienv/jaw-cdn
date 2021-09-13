<?php

namespace Keywordrush\AffiliateEgg;

defined('\ABSPATH') || exit;

/**
 * BolcomParser class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2020 keywordrush.com
 */
class BolcomParser extends LdShopParser {

    protected $charset = 'utf-8';
    protected $currency = 'EUR';

    public function parseCatalog($max)
    {
        $path = array(
            ".//div[@class='product-title--inline']//a/@href",
        );

        return $this->xpathArray($path);
    }

    public function parsePrice()
    {
        if ($price = parent::parsePrice())
            return $price;

        if (isset($this->ld_json['workExample']['potentialAction']['expectsAcceptanceOf']['price']))
            return $this->ld_json['workExample']['potentialAction']['expectsAcceptanceOf']['price'];
    }

    public function parseImg()
    {
        $paths = array(
            ".//meta[@property='og:image']/@content",
            ".//img[@class='js_product_img']/@src"
        );

        if ($img = $this->xpathScalar($paths))
            return $img;
        else
            return parent::parseImg();
    }

    public function parseExtra()
    {
        $extra = parent::parseExtra();

        $extra['features'] = array();
        $names = $this->xpathArray(".//*[@class='specs__list']/dt/text()[normalize-space()]");
        $values = $this->xpathArray(".//*[@class='specs__list']/dd");
        $feature = array();
        for ($i = 0; $i < count($names); $i++)
        {
            if (empty($values[$i]))
                continue;
            $feature['name'] = \sanitize_text_field($names[$i]);
            $feature['value'] = \sanitize_text_field($values[$i]);
            $extra['features'][] = $feature;
        }

        return $extra;
    }

    public function isInStock()
    {
        if ($this->xpathScalar(".//div[@class='buy-block__title']") == 'Niet leverbaar')
            return false;
        else
            return parent::isInStock();
    }

}
