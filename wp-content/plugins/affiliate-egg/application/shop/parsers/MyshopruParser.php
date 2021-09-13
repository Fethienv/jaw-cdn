<?php

namespace Keywordrush\AffiliateEgg;

defined('\ABSPATH') || exit;

/**
 * MyshopruParser class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2021 keywordrush.com
 */
class MyshopruParser extends MicrodataShopParser {

    protected $_html;
    
    public function parseCatalog($max)
    {
        if (preg_match_all('~"product_id":(\d+)~', $this->dom->saveHTML(), $matches))
        {
            $urls = array();
            foreach ($matches[1] as $m)
            {
                $urls[] = '/shop/product/' . $m . '.html';
            }
        }
        return $urls;
    }
    
    public function parseTitle()
    {
        $this->_html = $this->dom->saveHTML();
        
        if ($t = $this->xpathScalar(".//title"))
        {
            $parts = explode('|', $t);
            return reset($parts);
        }
                
    }    
    
    public function parsePrice()
    {
        if (preg_match('~"cost":(.+?),~', $this->_html, $matches))
            return $matches[1];
    }    

    public function parseOldPrice()
    {
        if (preg_match('~"old_cost":(.+?),~', $this->_html, $matches))
            return $matches[1];
    }

}
