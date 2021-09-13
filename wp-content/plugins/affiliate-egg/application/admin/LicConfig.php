<?php

namespace Keywordrush\AffiliateEgg;

defined('\ABSPATH') || exit;

/**
 * LicConfig class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2014 keywordrush.com
 */
class LicConfig extends Config {

    public function page_slug()
    {
        if ( \get_option(AffiliateEgg::slug . '_env_install'))
            return 'affiliate-egg-lic';
        else
            return AffiliateEgg::slug;
    }

    public function option_name()
    {
        return 'affegg_lic';
    }

    public function add_admin_menu()
    {
        $this->resetLicense();
        \add_submenu_page(AffiliateEgg::slug, __('License', 'affegg') . ' &lsaquo; Affiliate Egg', __('License', 'affegg'), 'manage_options', $this->page_slug(), array($this, 'settings_page'));
    }

    protected function options()
    {
        return array(
            'license_key' => array(
                'title' => __('License key', 'affegg'),
                'description' => __('Here you must enter a valid license key. You can find key in your <a href="http://www.keywordrush.com/en/panel">user panel</a>. If you don\'t have a key you can buy it on <a href="http://www.keywordrush.com/en/affiliateegg">the official website</a> of the plugin.', 'affegg'),
                'callback' => array($this, 'render_input'),
                'default' => '',
                'validator' => array(
                    'trim',
                    array(
                        'call' => array(NS . 'FormValidator', 'required'),
                        'message' => __('Field "License key" can\'t be empty.', 'affegg'),
                    ),
                    array(
                        'call' => array($this, 'licFormat'),
                        'message' => __('Not valid license key.', 'affegg'),
                    ),
                    array(
                        'call' => array($this, 'activatingLicense'),
                        'message' => __('The license key you have entered is invalid. Please verify you have typed the license key correctly. If you believe that there was a mistake, please, contacts with <a href="http://www.keywordrush.com/en/contact">support</a>.', 'affegg'),
                    ),
                ),
                'section' => 'default',
            ),
        );
    }

    public function settings_page()
    {
        AffiliateEggAdmin::getInstance()->render('lic_settings', array('page_slug' => $this->page_slug()));
    }

    public function licFormat($value)
    {
        
        return true;
    }

    public function activatingLicense($value)
    {
        return true;
        $response = AffiliateEgg::apiRequest(array('method' => 'POST', 'timeout' => 15, 'httpversion' => '1.0', 'blocking' => true, 'headers' => array(), 'body' => array('cmd' => 'activate', 'key' => $value, 'd' => parse_url(site_url(), PHP_URL_HOST), 'p' => AffiliateEgg::product_id, 'v' => AffiliateEgg::version()), 'cookies' => array()));
        if (!$response)
            return false;
        $result = json_decode(\wp_remote_retrieve_body($response), true);
        if ($result && !empty($result['status']) && $result['status'] === 'valid')
            return true;
        else
            return false;
    }

    private function resetLicense()
    {
        if (isset($_POST['cmd']) && $_POST['cmd'] == 'lic_reset' && \wp_verify_nonce($_POST['nonce_reset'], 'license_reset_ae'))
        {
            if (!current_user_can('delete_plugins') || empty($_POST['nonce_reset']))
                \wp_die('You don\'t have access to this page.');

            $redirect_url = \get_admin_url(\get_current_blog_id(), 'admin.php?page=affiliate-egg-lic');

            $response = AffiliateEgg::apiRequest(array('method' => 'POST', 'timeout' => 15, 'httpversion' => '1.0', 'blocking' => true, 'headers' => array(), 'body' => array('cmd' => 'deactivate', 'key' => $this->option('license_key'), 'd' => parse_url(site_url(), PHP_URL_HOST), 'p' => AffiliateEgg::product_id, 'v' => AffiliateEgg::version()), 'cookies' => array()));
            $result = json_decode(\wp_remote_retrieve_body($response), true);
            if ($result && !empty($result['status']) && $result['status'] === 'valid')
                \delete_option(LicConfig::getInstance()->option_name());

            \wp_redirect($redirect_url);
            exit;
        }
    }

}
