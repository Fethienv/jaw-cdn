<?php

namespace Keywordrush\AffiliateEgg;

defined('\ABSPATH') || exit;

use Keywordrush\AffiliateEgg\TextHelper;

/**
 * AliexpresscomParser class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2014 keywordrush.com
 */
class AliexpresscomParser extends ShopParser {

    protected $charset = 'utf-8';
    protected $currency = 'USD';
    protected $user_agent = array('Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:73.0) Gecko/20100101 Firefox/73.0');
    protected $headers = array(
        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'Accept-Language' => 'en-us,en;q=0.5',
        'Cache-Control' => 'no-cache',
        'Connection' => 'keep-alive',
    );
    protected $_product = array();

    public function parseCatalog($max)
    {
        // login required for search
        $catalog = $this->xpathArray(".//h3//a[contains(@class, 'product')]/@href");
        if (!$catalog)
            $catalog = array_slice($this->xpathArray(".//*[@id='hs-below-list-items']/li//h3/a[@class]/@href"), 0, $max);
        if (!$catalog)
            $catalog = array_slice($this->xpathArray(".//ul[contains(@class,'items-list')]//a/@href"), 0, $max);
        if (!$catalog)
            $catalog = array_slice($this->xpathArray(".//ul[@id='list-items']/li//h3/a/@href"), 0, $max);
        if (!$catalog)
            $catalog = array_slice($this->xpathArray(".//*[self::ul[@id='hs-list-items'] or self::div[@id='list-items']]/..//h3/a/@href"), 0, $max);
        if (!$catalog)
            $catalog = array_slice($this->xpathArray(".//ul[contains(@class,'switch-box')]/li/a[@class='pro-name']/@href"), 0, $max);
        if (!$catalog)
            $catalog = array_slice($this->xpathArray(".//div[contains(@class,'list-box')]//h4/a/@href"), 0, $max);
        if (!$catalog)
            $catalog = array_slice($this->xpathArray(".//a[@class='item-title']/@href"), 0, $max);
        // group.aliexpress.com
        if (!$catalog)
            $catalog = array_slice($this->xpathArray(".//*[@class='group-pro-main']/a[@class='pro-name']/@href"), 0, $max);
        if (!$catalog && strstr($this->getUrl(), 'flashdeals.aliexpress.com'))
            $catalog = $this->_parseFleshDeals();

        if (!$catalog)
        {
            if (preg_match_all('/"productDetailUrl":"(.+?)"/', $this->dom->saveHTML(), $matches))
                $catalog = $matches[1];
        }

        return $catalog;
    }

    private function _parseFleshDeals()
    {
        $request_url = 'https://gpsfront.aliexpress.com/queryGpsProductAjax.do?widget_id=5547572&platform=pc&limit=12&offset=0&phase=1';
        $response = \wp_remote_get($request_url);
        if (\is_wp_error($response))
            return array();

        $body = \wp_remote_retrieve_body($response);
        if (!$body)
            return array();
        $js_data = json_decode($body, true);

        if (!$js_data || !isset($js_data['gpsProductDetails']))
            return array();

        $urls = array();
        foreach ($js_data['gpsProductDetails'] as $hit)
        {
            $urls[] = $hit['productDetailUrl'];
        }
        return $urls;
    }

    public function parseTitle()
    {
        $this->_product = array();
        $this->_parseProduct();

        if ($this->_product && isset($this->_product['pageModule']['title']))
        {
            $p = explode('-in', $this->_product['pageModule']['title']);
            return $p[0];
        }

        $title = $this->xpathScalar(".//h1[@itemprop='name']");
        if (!$title)
            $title = $this->xpathScalar("div//[@class='product-title']");
        if (!$title)
            $title = $this->xpathScalar(".//h1");
        return $title;
    }

    // short URL page like https://aliexpress.com/item/32934619441.html
    public function _parseProduct()
    {
        if (!preg_match('/window\.runParams = .+?data: ({.+?reqHost.+?"}}),/ims', $this->dom->saveHTML(), $matches))
            return;
        $result = json_decode(html_entity_decode($matches[1]), true);
        if (!$result || !isset($result['priceModule']) || !isset($result['pageModule']))
            return false;

        $this->_product['priceModule'] = $result['priceModule'];
        $this->_product['pageModule'] = $result['pageModule'];
        if (isset($result['specsModule']))
            $this->_product['specsModule'] = $result['specsModule'];
        if (isset($result['titleModule']))
            $this->_product['titleModule'] = $result['titleModule'];
    }

    public function parseDescription()
    {
        return '';
    }

    public function parsePrice()
    {
        if ($this->_product && isset($this->_product['priceModule']['minActivityAmount']['value']))
            return $this->_product['priceModule']['minActivityAmount']['value'];

        if ($this->_product && isset($this->_product['priceModule']['minAmount']['value']))
            return $this->_product['priceModule']['minAmount']['value'];

        $price = $this->xpathScalar(".//*[@id='j-multi-currency-price']//*[@itemprop='lowPrice']");
        if (!$price)
            $price = $this->xpathScalar(".//dl[@class='product-info-current']//span[@itemprop='price' or @itemprop='lowPrice']");
        if (!$price)
            $price = $this->xpathScalar(".//div[@class='cost-box']//b");
        if (!$price)
            $price = $this->xpathScalar(".//*[@id='j-sku-discount-price']");
        if (!$price)
            $price = $this->xpathScalar(".//*[@id='j-sku-price']//*[@itemprop='lowPrice']");
        if (!$price)
            $price = $this->xpathScalar(".//*[@id='j-sku-price']");

        if ($price)
        {
            $parts = explode('-', $price);
            $price = $parts[0];
        }
        return $price;
    }

    public function parseOldPrice()
    {
        if ($this->_product && isset($this->_product['priceModule']['minAmount']['value']))
            return $this->_product['priceModule']['minAmount']['value'];

        $price = $this->xpathScalar(".//dl[@class='product-info-original']//span[@id='sku-price']");
        if (!$price)
            $price = $this->xpathScalar(".//*[@id='j-sku-price']");

        if ($price)
        {
            $parts = explode('-', $price);
            $price = $parts[0];
        }
        $price = TextHelper::parsePriceAmount($price);
        if ($this->item['price'] != $price)
            return $price;
        else
            return '';
    }

    public function parseManufacturer()
    {
        return trim($this->xpathScalar(".//div[contains(@class,'product-params')]//dl/dt/span[normalize-space(text())='Brand Name:']/../parent::dl/dd"));
    }

    public function parseImg()
    {
        if ($this->_product && isset($this->_product['pageModule']['imagePath']))
            return $this->_product['pageModule']['imagePath'];

        $img = $this->xpathScalar(".//div[@id='img']//div[@class='ui-image-viewer-thumb-wrap']/a/img/@src");
        if (!$img)
            $img = $this->xpathScalar(".//*[@id='j-detail-gallery-main']//img/@src");

        $img = str_replace('.jpg_640x640', '.jpg_350x350', $img);

        return $img;
    }

    public function parseImgLarge()
    {
        if ($this->item['orig_img'])
            return str_replace('.jpg_350x350', '', $this->item['orig_img']);
    }

    public function parseExtra()
    {
        $extra = array();

        if (isset($this->_product['titleModule']['feedbackRating']['averageStar']))
            $extra['rating'] = TextHelper::ratingPrepare($this->_product['titleModule']['feedbackRating']['averageStar']);

        if (isset($this->_product['specsModule']['props']))
        {
            $extra['features'] = array();
            foreach ($this->_product['specsModule']['props'] as $prop)
            {
                $feature = array();
                $feature['name'] = sanitize_text_field($prop['attrName']);
                $feature['value'] = sanitize_text_field($prop['attrValue']);
                $extra['features'][] = $feature;
            }
        }

        if (empty($extra['features']))
        {
            $extra['features'] = array();
            $names = $this->xpathArray(".//ul[contains(@class,'product-property-list')]//span[@class='propery-title']");
            $values = $this->xpathArray(".//ul[contains(@class,'product-property-list')]//span[@class='propery-des']");
            if (!$names)
            {
                $names = $this->xpathArray(".//div[@class='ui-box-body']//dt");
                $values = $this->xpathArray(".//div[@class='ui-box-body']//dd");
            }
            $feature = array();
            for ($i = 0; $i < count($names); $i++)
            {
                if (!empty($values[$i]) && trim($names[$i]) != "Brand Name")
                {
                    $feature['name'] = sanitize_text_field(str_replace(':', '', $names[$i]));
                    $feature['value'] = explode(',', sanitize_text_field($values[$i]));
                    $feature['value'] = join(', ', $feature['value']);
                    $extra['features'][] = $feature;
                }
            }
        }

        $extra['images'] = array();
        $results = $this->xpathArray(".//*[@id='j-image-thumb-list']//img/@src");
        foreach ($results as $i => $res)
        {
            if ($i == 0)
                continue;
            $extra['images'][] = str_replace('.jpg_50x50', '.jpg_640x640', $res);
        }

        if (empty($extra['rating']))
            $extra['rating'] = TextHelper::ratingPrepare($this->xpathScalar(".//*[@itemprop='aggregateRating']//*[@itemprop='ratingValue']"));

        return $extra;
    }

    public function isInStock()
    {
        if ($this->parsePrice())
            return true;
        else
            return false;
    }

    public function getCurrency()
    {
        if ($this->_product && isset($this->_product['priceModule']['minAmount']['currency']))
            return $this->_product['priceModule']['minAmount']['currency'];

        $currency = $this->xpathScalar(".//*[@itemprop='priceCurrency']/@content");
        if (!$currency)
            $currency = 'USD';
        return $currency;
    }

}
