<?php

namespace ContentEgg\application\admin;

defined('\ABSPATH') || exit;

use ContentEgg\application\components\Config;
use ContentEgg\application\Plugin;

/**
 * EnvatoConfig class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2016 keywordrush.com
 */
class EnvatoConfig extends LicConfig {

    public function page_slug()
    {
        return Plugin::slug . '-lic';
    }

    protected function options()
    {
        return array(
            'email' => array(
                'title' => 'Email <span class="cegg_required">*</span>',
                'description' => __('This Email will be used for license on your plugin. After activation, you will get email with login data for <a target="_blank" href="http://www.keywordrush.com/en/panel">user panel</a>, where you can control your licenses.', 'content-egg'),
                'callback' => array($this, 'render_input'),
                'default' => '',
                'validator' => array(
                    'trim',
                    array(
                        'call' => array('\ContentEgg\application\helpers\FormValidator', 'required'),
                        'message' => sprintf(__('The field  "%s" can not be empty', 'content-egg'), 'Email'),
                    ),
                    array(
                        'call' => array('\ContentEgg\application\helpers\FormValidator', 'valid_email'),
                        'message' => sprintf(__('Field "%s" filled with wrong data.', 'content-egg'), 'Email'),
                    ),
                ),
            ),
            // do not change config order, because validator
            'license_key' => array(
                'title' => 'Purchase code <span class="cegg_required">*</span>',
                'description' => __('Set your purchase code for plugin. If you get plugin in bundle with theme, set Purchase code of theme. <a target="_blank" href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-">Where to find?</a>', 'content-egg'),
                'callback' => array($this, 'render_input'),
                'default' => '',
                'validator' => array(
                    'trim',
                    array(
                        'call' => array('\ContentEgg\application\helpers\FormValidator', 'required'),
                        'message' => sprintf(__('The field  "%s" can not be empty', 'content-egg'), 'Purchase code'),
                    ),
                    array(
                        'call' => array($this, 'licFormat'),
                        'message' => sprintf(__('Field "%s" filled with wrong data.', 'content-egg'), 'Purchase code'),
                    ),
                    array(
                        'call' => array($this, 'activatingLicense'),
                        'message' => __('Plugin cannot be activated!', 'content-egg'), 'Purchase code',
                    ),
                ),
            ),
            'subscribe' => array(
                'title' => '',
                'description' => __('I\'d like to be contacted with the latest project news and offers.', 'content-egg'),
                'callback' => array($this, 'render_checkbox'),
                'default' => false,
                'validator' => array(
                    array(
                        'call' => array($this, 'envatoFlag'),
                        'message' => __('Error.', 'content-egg'), 'Purchase code',
                    ),
                ),
            ),
        );
    }

    public function settings_page()
    {
        PluginAdmin::render('envato_activation', array('page_slug' => $this->page_slug()));
    }

    public function activatingLicense($value)
    {
    	return true;
        // do not try to activate...
        if (\get_settings_errors())
            return false;

        $email = $this->get_submitted_value('email');
        if (!$email)
            return false;
        $subscribe = (bool) $this->get_submitted_value('subscribe');

        $response = Plugin::apiRequest(array('method' => 'POST', 'timeout' => 15, 'httpversion' => '1.0', 'blocking' => true, 'headers' => array(), 'body' => array('cmd' => 'envato_activate', 'key' => $value, 'd' => parse_url(site_url(), PHP_URL_HOST), 'p' => Plugin::product_id, 'v' => Plugin::version(), 'email' => $email, 'subscribe' => $subscribe), 'cookies' => array()));
        if (!$response)
            return false;

        $result = json_decode(\wp_remote_retrieve_body($response), true);
        if ($result && !empty($result['status']) && $result['status'] == 'valid')
        {
            return true;
        } elseif ($result && !empty($result['status']) && $result['status'] == 'error')
        {
            \add_settings_error('license_key', 'license_key', $result['message']);
            return false;
        }
        return false;
    }

    public function envatoFlag()
    {
    	return false;
        if (\get_settings_errors())
            return true;

        \update_option(Plugin::slug . '_env_install', time());
        return true;
    }

}
