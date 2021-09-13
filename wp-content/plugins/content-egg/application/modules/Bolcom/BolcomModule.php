<?php

namespace ContentEgg\application\modules\Bolcom;

defined('\ABSPATH') || exit;

use ContentEgg\application\components\AffiliateParserModule;
use ContentEgg\application\components\ContentProduct;
use ContentEgg\application\admin\PluginAdmin;
use ContentEgg\application\helpers\TextHelper;
use ContentEgg\application\libs\bolcom\BolcomApi;
use ContentEgg\application\components\LinkHandler;

/**
 * BolcomModule class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2021 keywordrush.com
 */
class BolcomModule extends AffiliateParserModule {

    private $api_client = null;

    public function info()
    {
        return array(
            'name' => 'Bolcom',
            'description' => sprintf(__('Adds products from %s.', 'content-egg'), 'Bol.com'),
        );
    }

    public function releaseVersion()
    {
        return '4.1.0';
    }

    public function getParserType()
    {
        return self::PARSER_TYPE_PRODUCT;
    }

    public function defaultTemplateName()
    {
        return 'grid';
    }

    public function isItemsUpdateAvailable()
    {
        return true;
    }

    public function doRequest($keyword, $query_params = array(), $is_autoupdate = false)
    {
        $options = array();

        if ($is_autoupdate)
            $limit = $this->config('entries_per_page_update');
        else
            $limit = $this->config('entries_per_page');

        $options['limit'] = $limit;

        $params = array(
            'country',
            //'offers',
            'sort',
        );

        foreach ($params as $param)
        {
            $value = $this->config($param);
            if ($value)
                $options[$param] = $value;
        }
        if ($this->config('ids'))
            $options['ids'] = (int) $this->config('ids');

        $options['dataoutput'] = 'products';
        $options['includeattributes'] = 'true';
        //$options['offers'] = 'bestoffer'; //default

        $results = $this->getApiClient()->search($keyword, $options);

        if (!isset($results['products']) || !is_array($results['products']))
            return array();

        return $this->prepareResults($results['products']);
    }

    private function prepareResults($results)
    {
        $data = array();
        foreach ($results as $key => $r)
        {
            $content = new ContentProduct;

            $content->unique_id = $r['id'];
            $content->title = $r['title'];
            if (!empty($r['rating']))
                $content->rating = TextHelper::ratingPrepare($r['rating'] / 10);
            
            if (!empty($r['summary']))
                $content->description = $r['summary'];
            elseif (!empty($r['longDescription']))
                $content->description = $r['longDescription'];
            $content->description = strip_tags($content->description);
            if ($max_size = $this->config('description_size'))
                $content->description = TextHelper::truncateHtml($content->description, $max_size);
            if (!empty($r['upc']))
                $content->upc = $r['upc'];
            if (!empty($r['ean']))
                $content->ean = $r['ean'];
            if (!empty($r['isbn']))
                $content->isbn = $r['isbn'];

            if (!empty($r['specsTag']))
                $content->manufacturer = $r['specsTag'];

            $content->domain = 'bol.com';

            if (isset($r['offerData']['offers']))
            {
                $offer = $r['offerData']['offers'][0]; //only first offer...
                if (!empty($offer['price']))
                    $content->price = $offer['price'];
                if (!empty($offer['listPrice']))
                    $content->priceOld = $offer['listPrice'];

                $content->availability = $offer['availabilityDescription'];
                if (isset($r['offerData']['offers'][0]['id']))
                    $content->stock_status = ContentProduct::STOCK_STATUS_IN_STOCK;
                else
                    $content->stock_status = ContentProduct::STOCK_STATUS_OUT_OF_STOCK;
            }

            if (isset($r['images']))
            {
                $content->img = $r['images'][4]['url']; // XL size
            }

            if (isset($r['parentCategoryPaths']))
            {
                $column_name = 'name';
                $content->categoryPath = array_map(function($element) use($column_name) {
                    return $element[$column_name];
                }, $r['parentCategoryPaths'][0]['parentCategories']);
                $content->category = end($content->categoryPath);
            }

            $content->currencyCode = 'EUR';
            $content->orig_url = $r['urls'][0]['value'];
            $content->url = $this->createAffUrl($content->orig_url, (array) $content);

            $content->extra = new ExtraDataBolcom();
            ExtraDataBolcom::fillAttributes($content->extra, $r);

            if (isset($r['attributeGroups']))
            {
                $content->features = array();
                if (!isset($r['attributeGroups']))
                    continue;
                
                foreach ($r['attributeGroups'] as $attributeGroup)
                {
                    if (!isset($attributeGroup['attributes']))
                        continue;
                    foreach ($attributeGroup['attributes'] as $attribute)
                    {
                        $value = preg_replace("~<br*/?>~i", " \n", $attribute['value']);
                        $value = preg_replace("~<li*/?>~i", " \n ", $value);
                        $value = trim(strip_tags($value));

                        $feature = array(
                            'group' => sanitize_text_field($attributeGroup['title']),
                            'name' => sanitize_text_field($attribute['label']),
                            'value' => strip_tags($attribute['value']),
                        );
                        $content->features[] = $feature;
                    }
                }
            }
            $data[] = $content;
        }
        return $data;
    }

    public function doRequestItems(array $items)
    {
        $options = array();
        $options['country'] = $this->config('country');

        $item_ids = array_map(function($element) {
            return $element['unique_id'];
        }, $items);

        $results = $this->getApiClient()->products($item_ids, $options);
        if (!$results || !isset($results['products']))
            throw new \Exception('doRequestItems request error.');

        // assign new data
        foreach ($results['products'] as $r)
        {
            $unique_id = $r['id'];
            if (!isset($items[$unique_id]))
                continue;

            if (isset($r['offerData']['offers']))
            {
                $offer = $r['offerData']['offers'][0]; //only first offer...
                if (!empty($offer['price']))
                    $items[$unique_id]['price'] = $offer['price'];
                if (!empty($offer['listPrice']))
                    $items[$unique_id]['priceOld'] = $offer['listPrice'];

                $items[$unique_id]['availability'] = $offer['availabilityDescription'];
            }

            if (isset($r['offerData']['offers'][0]['id']))
                $items[$unique_id]['stock_status'] = ContentProduct::STOCK_STATUS_IN_STOCK;
            else
                $items[$unique_id]['stock_status'] = ContentProduct::STOCK_STATUS_OUT_OF_STOCK;

            $items[$unique_id]['orig_url'] = $r['urls'][0]['value'];
            $items[$unique_id]['url'] = $this->createAffUrl($items[$unique_id]['orig_url'], $items[$unique_id]);
        }
        return $items;
    }

    private function getApiClient()
    {
        if ($this->api_client === null)
        {
            $this->api_client = new BolcomApi($this->config('apikey'));
        }
        return $this->api_client;
    }

    public function viewDataPrepare($data)
    {
        $deeplink = $this->config('deeplink');
        foreach ($data as $key => $d)
        {
            $data[$key]['url'] = $this->createAffUrl($d['orig_url'], $d);
        }

        return parent::viewDataPrepare($data);
    }

    private function createAffUrl($url, $item = array())
    {
        $deeplink = 'https://partnerprogramma.bol.com/click/click?p=1&t=url&s=' . urlencode($this->config('SiteId')) . '&f=TXL&url={{url_encoded}}&name=cegg';
        if ($this->config('subId'))
            $deeplink .= '&subid=' . $this->config('subId');

        return LinkHandler::createAffUrl($url, $deeplink, $item);
    }

    public function renderResults()
    {
        PluginAdmin::render('_metabox_results', array('module_id' => $this->getId()));
    }

    public function renderSearchResults()
    {
        PluginAdmin::render('_metabox_search_results', array('module_id' => $this->getId()));
    }

}
