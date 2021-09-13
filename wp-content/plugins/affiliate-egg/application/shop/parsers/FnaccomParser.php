<?php

namespace Keywordrush\AffiliateEgg;

defined('\ABSPATH') || exit;

/**
 * FnaccomParser class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2020 keywordrush.com
 */
class FnaccomParser extends LdShopParser {

    protected $charset = 'utf-8';
    protected $currency = 'EUR';

    public function parseCatalog($max)
    {
        return $this->xpathArray(".//p[@class='Article-desc']/a/@href");
    }

    public function parseOldPrice()
    {
        $p = $this->xpathScalar(array(".//*[@class='f-priceBox']//*[@class='f-priceBox-price f-priceBox-price--old']", ".//span[@class='f-priceBox-price f-priceBox-price--reco f-priceBox-price--alt']", ".//*[contains(@class, 'f-priceBox-price--old')]"), true);
        return str_replace('&euro;', '.', $p);
    }

    public function parseManufacturer()
    {
        if (isset($this->_ld['brand']['name']))
            return $this->_ld['brand']['name'];
    }

    public function parseExtra()
    {
        $extra = parent::parseExtra();
        $extra['features'] = array();
        $names = $this->xpathArray(".//*[@class='f-productDetails-table']//td[1]");
        $values = $this->xpathArray(".//*[@class='f-productDetails-table']//td[2]");
        $feature = array();
        for ($i = 0; $i < count($names); $i++)
        {
            if (!empty($values[$i]))
            {
                $feature['name'] = \sanitize_text_field($names[$i]);
                $feature['value'] = \sanitize_text_field($values[$i]);
                $extra['features'][] = $feature;
            }
        }

        return $extra;
    }

}
