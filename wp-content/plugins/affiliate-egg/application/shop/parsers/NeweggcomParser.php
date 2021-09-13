<?php

namespace Keywordrush\AffiliateEgg;

defined('\ABSPATH') || exit;

/**
 * NeweggcomParser class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2020 keywordrush.com
 */
class NeweggcomParser extends LdShopParser {

    protected $charset = 'utf-8';
    protected $currency = 'USD';

    public function parseCatalog($max)
    {
        return $this->xpathArray(array(".//*[@class='items-view is-grid']//a[@class='item-title']/@href", ".//div[@class='item-info']/a/@href"));
    }

    public function parseOldPrice()
    {
        return $this->xpathScalar(".//ul[@class='price']//span[@class='price-was-data']");
    }

    public function parseImg()
    {
        if ($img = $this->xpathScalar(".//meta[@property='og:image']/@content"))
            return $img;

        $img = $this->xpathScalar(".//a[@id='A2']//img/@src");
        if (!$img)
        {
            $img = $this->xpathScalar(".//*[@id='synopsis']//ul[@class='navThumbs']/li[position() = 1]//img/@src");
            $img = str_replace('/ProductImageCompressAll35/', '/ProductImage/', $img);
        }
        $img = str_replace('//images', 'https://images', $img);
        return $img;
    }

    public function parseExtra()
    {
        $extra = parent::parseExtra();

        $extra['features'] = array();
        $names = $this->xpathArray(".//*[@id='Specs']//dt");
        $values = $this->xpathArray(".//*[@id='Specs']//dd", true);
        $feature = array();
        for ($i = 0; $i < count($names); $i++)
        {
            if (empty($values[$i]))
                continue;
            if ($names[$i] == 'Features') //too long?
                continue;

            $feature['name'] = sanitize_text_field($names[$i]);

            $value = $values[$i];
            $value = preg_replace("~<br*/?>~i", " \n", $value);
            $value = strip_tags($value);
            $feature['value'] = $value;
            $extra['features'][] = $feature;
        }

        return $extra;
    }

    public function isInStock()
    {
        if (!$this->parsePrice())
            return false;
        else
            return true;
    }

}
