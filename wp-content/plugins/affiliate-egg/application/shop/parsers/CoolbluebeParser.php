<?php

namespace Keywordrush\AffiliateEgg;

defined('\ABSPATH') || exit;

/**
 * CoolbluebeParser class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2020 keywordrush.com
 */
require_once dirname(__FILE__) . '/CoolbluenlParser.php';

class CoolbluebeParser extends CoolbluenlParser {

    protected $canonical_domain = 'https://www.coolblue.be';

    public function parseOldPrice()
    {
        return $this->xpathScalar(".//div[@class='grid-section-xs--gap-4']//*[@class='sales-price__former-price']");
    }

}
