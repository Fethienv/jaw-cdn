<?php

namespace Keywordrush\AffiliateEgg;

defined('\ABSPATH') || exit;

/**
 * AsoscomParser class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2014 keywordrush.com
 */
class AsoscomParser extends ShopParser {

    protected $charset = 'utf-8';
    protected $currency = 'USD';
    protected $user_agent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:77.0) Gecko/20100101 Firefox/77.0';
    protected $_old_price = 0;
    protected $headers = array(
        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'Accept-Language' => 'en-us,en;q=0.5',
        'Cache-Control' => 'no-cache',
        'Connection' => 'keep-alive',
    );

    public function parseCatalog($max)
    {
        return $this->xpathArray(".//article[contains(@id, 'product-')]//a/@href");
    }

    public function parseTitle()
    {
        return $this->xpathScalar(".//h1");
    }

    public function parsePrice()
    {

        $html = $this->dom->saveHTML();
        if (!preg_match('/window\.fetch\("(.+?)",/', $html, $matches))
            return;

        $url = 'https://www.asos.com' . $matches[1];

        $response = \wp_remote_get($url);
        if (\is_wp_error($response))
            return;

        $body = \wp_remote_retrieve_body($response);
        if (!$body)
            return;


        $data = json_decode($body, true);

        if (isset($data[0]['productPrice']['currency']))
            $this->currency = $data[0]['productPrice']['currency'];
        if (isset($data[0]['productPrice']['previous']['value']))
            $this->_old_price = $data[0]['productPrice']['previous']['value'];
        if (isset($data[0]['productPrice']['current']['value']))
            return $data[0]['productPrice']['current']['value'];
    }

    public function parseOldPrice()
    {
        return $this->_old_price;
    }

    public function parseImg()
    {
        return $this->xpathScalar(".//div[@id='product-gallery']//img/@src");
    }

    public function getCurrency()
    {
        return $this->currency;
    }

}
