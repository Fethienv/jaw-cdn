<?php

namespace Keywordrush\AffiliateEgg;

defined('\ABSPATH') || exit;

/**
 * EtsyParser class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2016 keywordrush.com
 */
class EtsyParser extends LdShopParser {

    protected $charset = 'utf-8';
    protected $currency = 'USD';

    public function parseCatalog($max)
    {
        $urls = $this->xpathArray(".//a[contains(@class, 'listing-link')]/@href");
        return $urls;
    }

    public function parsePrice()
    {
        // local currency
        $price = $this->xpathScalar(".//*[@class='text-largest strong override-listing-price']");
        if ($price)
            return $price;
        else
            return parent::parsePrice();
    }

    /**
     * @return String
     */
    public function parseImgLarge()
    {
        return str_replace('/il_570xN.', '/il_fullxfull.', $this->parseImg());
    }

    public function getCurrency()
    {
        if ($this->xpathScalar(".//*[@class='text-largest strong override-listing-price']"))
        {
            if (preg_match('/"locale_currency_code":"([A-Z]+?)"/', $this->dom->saveHTML(), $matches))
                return $matches[1];
        }
        return parent::getCurrency();
    }

}
