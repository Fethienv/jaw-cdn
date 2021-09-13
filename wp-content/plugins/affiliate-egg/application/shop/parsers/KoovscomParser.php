<?php

namespace Keywordrush\AffiliateEgg;

defined('\ABSPATH') || exit;

/**
 * KoovscomParser class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2017 keywordrush.com
 */
class KoovscomParser extends ShopParser {

    protected $charset = 'utf-8';
    protected $currency = 'INR';
    protected $headers = array(
        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'Accept-Language' => 'en-us,en;q=0.5',
        'Cache-Control' => 'no-cache',
        'Connection' => 'keep-alive',
    );

    public function parseCatalog($max)
    {
        $urls = array_slice($this->xpathArray(".//*[@class='prodBox']//a/@href"), 0, $max);
        foreach ($urls as $key => $url)
        {
            if (!preg_match('/^https?:\/\//', $url))
                $urls[$key] = "http://www.koovs.com" . $url;
        }
        return $urls;
    }

    public function parseTitle()
    {
        return $this->xpathScalar(".//*[@class='product-name']");
    }

    public function parseDescription()
    {
        $res = $this->xpathArray(".//*[@class='ddesc']//ul/li");
        return \sanitize_text_field(implode('. ', $res));
    }

    public function parsePrice()
    {
        return $this->xpathScalar(array(".//*[@class='pd-price']", ".//*[@class='pd-discount-price']"));
    }

    public function parseOldPrice()
    {
        
    }

    public function parseManufacturer()
    {
        return $this->xpathScalar(".//*[@class='product-brand-name']");
    }

    public function parseImg()
    {
        return $this->xpathScalar(".//*[@class='slick-list']//img/@src");
    }

    public function parseImgLarge()
    {
        
    }

    public function parseExtra()
    {
        $extra = array();

        $extra['features'] = array();

        $names = $this->xpathArray(".//div[@class='info-care']//*[@class='text']");
        $values = $this->xpathArray(".//div[@class='info-care']//*[contains(@class, 'pd-col')]");
        $feature = array();
        for ($i = 0; $i < count($names); $i++)
        {
            if (!empty($values[$i]))
            {
                $feature['name'] = \sanitize_text_field(str_replace(":", "", $names[$i]));
                $feature['value'] = \sanitize_text_field($values[$i]);
                $extra['features'][] = $feature;
            }
        }

        return $extra;
    }

    public function isInStock()
    {
        return true;
    }

}
