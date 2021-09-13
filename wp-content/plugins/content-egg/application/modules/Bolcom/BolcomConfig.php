<?php

namespace ContentEgg\application\modules\Bolcom;

defined('\ABSPATH') || exit;

use ContentEgg\application\components\AffiliateParserModuleConfig;

/**
 * BolcomConfig class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2017 keywordrush.com
 */
class BolcomConfig extends AffiliateParserModuleConfig {

    public function options()
    {
        $optiosn = array(
            'apikey' => array(
                'title' => 'API Access Key <span class="cegg_required">*</span>',
                'description' => __('Sign up for a developer account with your website at <a target="_blank" href="http://developers.bol.com">developers.bol.com</a> and <a target="_blank" href="https://partnerblog.bol.com/documentatie/open-api/aanmeldformulier-api-toegang/">request a API-key</a>.', 'content-egg'),
                'callback' => array($this, 'render_input'),
                'default' => '',
                'validator' => array(
                    'trim',
                    array(
                        'call' => array('\ContentEgg\application\helpers\FormValidator', 'required'),
                        'when' => 'is_active',
                        'message' => sprintf(__('The field "%s" can not be empty.', 'content-egg'), 'API Access Key'),
                    ),
                ),
            ),
            'SiteId' => array(
                'title' => 'Site ID <span class="cegg_required">*</span>',
                'description' => __('<a target="_blank" href="http://partnerprogramma.bol.com ">Sign up</a> for a partner program account. You can find your Bol.com site ID via <a target="_blank" href="https://partnerprogramma.bol.com/partner/affiliate/account.do">https://partnerprogramma.bol.com/partner/affiliate/account.do</a>.', 'content-egg'),
                'callback' => array($this, 'render_input'),
                'default' => '',
                'validator' => array(
                    'trim',
                    array(
                        'call' => array('\ContentEgg\application\helpers\FormValidator', 'required'),
                        'when' => 'is_active',
                        'message' => sprintf(__('The field "%s" can not be empty.', 'content-egg'), 'Site ID'),
                    ),
                ),
            ),
            'entries_per_page' => array(
                'title' => __('Results', 'content-egg'),
                'description' => __('Number of results for one search query.', 'content-egg'),
                'callback' => array($this, 'render_input'),
                'default' => 10,
                'validator' => array(
                    'trim',
                    'absint',
                    array(
                        'call' => array('\ContentEgg\application\helpers\FormValidator', 'less_than_equal_to'),
                        'arg' => 100,
                        'message' => sprintf(__('The field "%s" can not be more than %d.', 'content-egg'), 'Results', 100),
                    ),
                ),
            ),
            'entries_per_page_update' => array(
                'title' => __('Results for updates', 'content-egg'),
                'description' => __('Number of results for automatic updates and autoblogging.', 'content-egg'),
                'callback' => array($this, 'render_input'),
                'default' => 6,
                'validator' => array(
                    'trim',
                    'absint',
                    array(
                        'call' => array('\ContentEgg\application\helpers\FormValidator', 'less_than_equal_to'),
                        'arg' => 100,
                        'message' => sprintf(__('The field "%s" can not be more than %d.', 'content-egg'), 'Results', 100),
                    ),
                ),
            ),
            'subId' => array(
                'title' => 'SubID',
                'description' => __('SubID is a parameter that allows for the tracking of sales separately.', 'content-egg'),
                'callback' => array($this, 'render_input'),
                'default' => '',
                'validator' => array(
                    'trim',
                ),
            ),
            'country' => array(
                'title' => __('Country', 'content-egg'),
                'description' => __('Signifies whether the shopping context is Dutch or Belgium. This can influence search ranking, and whether some products and offers are returned.', 'content-egg'),
                'callback' => array($this, 'render_dropdown'),
                'dropdown_options' => array(
                    'NL' => __('Dutch (NL)', 'content-egg'),
                    'BE' => __('Belgium (BE)', 'content-egg'),
                ),
                'default' => 'NL',
            ),
            'ids' => array(
                'title' => __('Category', 'content-egg'),
                'callback' => array($this, 'render_dropdown'),
                'dropdown_options' => array(
                    '' => __('All', 'content-egg'),
                    '8299.' => 'Boeken',
                    '3132.' => 'Muziek',
                    '3133.' => 'Films & Series',
                    '3135.' => 'Games',
                    '7934.' => 'Speelgoed',
                    '11271.' => 'Baby',
                    '16947.' => 'Baby- & Kinderkleding',
                    '12382.' => 'Mooi & Gezond',
                    '11764.' => 'Koken, Tafelen en Huishouden',
                    '3136.' => 'Elektronica',
                    '16737.' => 'Sieraden',
                    '12748.' => 'Dieren',
                    '16784.' => 'Horloges & Accessoires',
                    '13155.' => 'Klussen',
                    '16799.' => 'Tassen & Lederwaren',
                    '3134.' => 'Computer',
                    '25897.' => 'Kantoor & School',
                    '14647.' => 'Sport & Vrije tijd',
                    '12974.' => 'Tuin',
                    '14035.' => 'Wonen',
                    '26147.' => 'Modeaccessoires',
                    '20639.' => 'Cadeaubonnen',
                ),
                'default' => 'all',
            ),
            /*
              'offers' => array(
              'title' => __('Offers', 'content-egg'),
              'callback' => array($this, 'render_dropdown'),
              'dropdown_options' => array(
              'all' => __('All', 'content-egg'),
              'cheapest' => __('Cheapest', 'content-egg'),
              'secondhand' => __('Secondhand', 'content-egg'),
              'newoffers' => __('New offers', 'content-egg'),
              'bolcom' => __('Bol.com', 'content-egg'),
              'bestoffer' => __('Best offer', 'content-egg'),
              ),
              'default' => 'bestoffer',
              ),
             * 
             */
            'sort' => array(
                'title' => __('Sort', 'content-egg'),
                'description' => __('The way the products are sorted', 'content-egg'),
                'callback' => array($this, 'render_dropdown'),
                'dropdown_options' => array(
                    '' => __('Default', 'content-egg'),
                    'rankasc' => __('Sales ranking ascending', 'content-egg'),
                    'rankdesc' => __('Sales ranking descending', 'content-egg'),
                    'priceasc' => __('Price ascending', 'content-egg'),
                    'pricedesc' => __('Price descending', 'content-egg'),
                    'titleasc' => __('Title ascending', 'content-egg'),
                    'titledesc' => __('Title descending', 'content-egg'),
                    'dateasc' => __('Publishing date ascending', 'content-egg'),
                    'datedesc' => __('Publishing date descending', 'content-egg'),
                    'ratingasc' => __('Rating ascending', 'content-egg'),
                    'ratingdesc' => __('Rating descending', 'content-egg'),
                ),
                'default' => '',
            ),
            'description_size' => array(
                'title' => __('Trim description', 'content-egg'),
                'description' => __('Description size in characters (0 - do not cut)', 'content-egg'),
                'callback' => array($this, 'render_input'),
                'default' => '300',
                'validator' => array(
                    'trim',
                    'absint',
                ),
                'section' => 'default',
            ),
            'save_img' => array(
                'title' => __('Save images', 'content-egg'),
                'description' => __('Save images on server', 'content-egg'),
                'callback' => array($this, 'render_checkbox'),
                'default' => false,
                'section' => 'default',
            ),
        );
        return array_merge(parent::options(), $optiosn);
    }

}
